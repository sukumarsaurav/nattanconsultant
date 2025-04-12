-- Comprehensive Migration Script for Time Slots
-- This script handles the complete migration of the time slot system to support multiple consultation types

-- Step 1: Make sure day_consultation_availability table has the proper constraints
ALTER TABLE day_consultation_availability
ADD CONSTRAINT IF NOT EXISTS video_available_check CHECK (video_available IN (0, 1));

ALTER TABLE day_consultation_availability
ADD CONSTRAINT IF NOT EXISTS phone_available_check CHECK (phone_available IN (0, 1));

ALTER TABLE day_consultation_availability
ADD CONSTRAINT IF NOT EXISTS in_person_available_check CHECK (in_person_available IN (0, 1));

-- Make sure foreign key constraint exists (in case it's missing)
-- First check if the constraint already exists to avoid errors
SET @constraint_exists = (
    SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'day_consultation_availability' 
    AND CONSTRAINT_NAME = 'day_consultation_availability_consultant_fk'
);

SET @sql = IF(@constraint_exists > 0, 
    'SELECT "Foreign key constraint already exists"', 
    'ALTER TABLE day_consultation_availability 
     ADD CONSTRAINT day_consultation_availability_consultant_fk 
     FOREIGN KEY (consultant_id) REFERENCES consultants (id) ON DELETE CASCADE');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Create the new time_slot_consultation_types table if it doesn't exist
CREATE TABLE IF NOT EXISTS `time_slot_consultation_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `availability_schedule_id` int(11) NOT NULL,
  `consultation_type` ENUM('Video Consultation', 'Phone Consultation', 'In-Person Consultation') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_time_slot_id` (`availability_schedule_id`),
  CONSTRAINT `fk_time_slot_id` FOREIGN KEY (`availability_schedule_id`) REFERENCES `availability_schedule` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 3: Make sure all consultants have the proper day_consultation_availability settings
INSERT IGNORE INTO day_consultation_availability (consultant_id, day_of_week, video_available, phone_available, in_person_available)
SELECT 
    c.id as consultant_id,
    dow.day_name as day_of_week,
    c.video_consultation_available as video_available,
    c.phone_consultation_available as phone_available,
    c.in_person_consultation_available as in_person_available
FROM 
    consultants c
CROSS JOIN 
    (SELECT 'monday' as day_name
     UNION SELECT 'tuesday'
     UNION SELECT 'wednesday'
     UNION SELECT 'thursday'
     UNION SELECT 'friday'
     UNION SELECT 'saturday'
     UNION SELECT 'sunday') as dow
WHERE 
    NOT EXISTS (
        SELECT 1 FROM day_consultation_availability dca 
        WHERE dca.consultant_id = c.id AND dca.day_of_week = dow.day_name
    );

-- Step 4: Fix duplicate time slots (same consultant, day, start_time, end_time)
-- First, we'll identify duplicate time slots
CREATE TEMPORARY TABLE temp_duplicate_slots AS
SELECT consultant_id, day_of_week, start_time, end_time, COUNT(*) as count
FROM availability_schedule
GROUP BY consultant_id, day_of_week, start_time, end_time
HAVING COUNT(*) > 1;

-- Create a temporary table to store the IDs to keep
CREATE TEMPORARY TABLE temp_slots_to_keep AS
SELECT MIN(a.id) as id_to_keep
FROM availability_schedule a
JOIN temp_duplicate_slots d ON 
    a.consultant_id = d.consultant_id AND
    a.day_of_week = d.day_of_week AND
    a.start_time = d.start_time AND
    a.end_time = d.end_time
GROUP BY a.consultant_id, a.day_of_week, a.start_time, a.end_time;

-- Delete duplicate slots (keeping only one slot per time period)
DELETE a FROM availability_schedule a
JOIN temp_duplicate_slots d ON 
    a.consultant_id = d.consultant_id AND
    a.day_of_week = d.day_of_week AND
    a.start_time = d.start_time AND
    a.end_time = d.end_time
LEFT JOIN temp_slots_to_keep k ON a.id = k.id_to_keep
WHERE k.id_to_keep IS NULL;

-- Drop temporary tables
DROP TEMPORARY TABLE IF EXISTS temp_duplicate_slots;
DROP TEMPORARY TABLE IF EXISTS temp_slots_to_keep;

-- Step 5: For each existing time slot in availability_schedule, add consultation types
-- based on the consultant's consultation availability settings for that day

-- First, add Video Consultation type where it's available
INSERT IGNORE INTO time_slot_consultation_types (availability_schedule_id, consultation_type)
SELECT 
    a.id as availability_schedule_id,
    'Video Consultation' as consultation_type
FROM 
    availability_schedule a
JOIN 
    day_consultation_availability d ON a.consultant_id = d.consultant_id AND a.day_of_week = d.day_of_week
WHERE 
    d.video_available = 1;

-- Next, add Phone Consultation type where it's available
INSERT IGNORE INTO time_slot_consultation_types (availability_schedule_id, consultation_type)
SELECT 
    a.id as availability_schedule_id,
    'Phone Consultation' as consultation_type
FROM 
    availability_schedule a
JOIN 
    day_consultation_availability d ON a.consultant_id = d.consultant_id AND a.day_of_week = d.day_of_week
WHERE 
    d.phone_available = 1;

-- Finally, add In-Person Consultation type where it's available
INSERT IGNORE INTO time_slot_consultation_types (availability_schedule_id, consultation_type)
SELECT 
    a.id as availability_schedule_id,
    'In-Person Consultation' as consultation_type
FROM 
    availability_schedule a
JOIN 
    day_consultation_availability d ON a.consultant_id = d.consultant_id AND a.day_of_week = d.day_of_week
WHERE 
    d.in_person_available = 1;

-- If there are any time slots that still don't have any consultation types,
-- add a default of Video Consultation (you can modify this default as needed)
INSERT INTO time_slot_consultation_types (availability_schedule_id, consultation_type)
SELECT 
    a.id as availability_schedule_id,
    'Video Consultation' as consultation_type
FROM 
    availability_schedule a
WHERE 
    NOT EXISTS (
        SELECT 1 FROM time_slot_consultation_types t 
        WHERE t.availability_schedule_id = a.id
    );

-- Step 6: Add indexes to improve query performance
-- Check if index exists first to avoid errors
SET @index_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'availability_schedule'
    AND INDEX_NAME = 'idx_consultant_day'
);

SET @sql = IF(@index_exists > 0, 
    'SELECT "Index idx_consultant_day already exists"', 
    'ALTER TABLE `availability_schedule` ADD INDEX `idx_consultant_day` (`consultant_id`, `day_of_week`, `start_time`, `end_time`)');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check if index exists first to avoid errors
SET @index_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'time_slot_consultation_types'
    AND INDEX_NAME = 'idx_consultation_type'
);

SET @sql = IF(@index_exists > 0, 
    'SELECT "Index idx_consultation_type already exists"', 
    'ALTER TABLE `time_slot_consultation_types` ADD INDEX `idx_consultation_type` (`consultation_type`)');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt; 