-- Step 1: Create the new time_slot_consultation_types table
CREATE TABLE IF NOT EXISTS `time_slot_consultation_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `availability_schedule_id` int(11) NOT NULL,
  `consultation_type` ENUM('Video Consultation', 'Phone Consultation', 'In-Person Consultation') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_time_slot_id` (`availability_schedule_id`),
  CONSTRAINT `fk_time_slot_id` FOREIGN KEY (`availability_schedule_id`) REFERENCES `availability_schedule` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: SKIP data migration since consultation_type column doesn't exist
-- Original code commented out:
-- INSERT INTO `time_slot_consultation_types` (`availability_schedule_id`, `consultation_type`)
-- SELECT id, consultation_type 
-- FROM `availability_schedule` 
-- WHERE consultation_type IS NOT NULL;

-- Step 3: Fix scheduling for duplicate time slots (same time, different consultation types)
-- First, we'll identify duplicate time slots (same consultant, day, start_time, end_time)
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

-- Migrate additional consultation types for duplicate slots
-- Commenting out since this also depends on consultation_type column
-- INSERT INTO time_slot_consultation_types (availability_schedule_id, consultation_type)
-- SELECT k.id_to_keep, a.consultation_type
-- FROM availability_schedule a
-- JOIN temp_duplicate_slots d ON 
--     a.consultant_id = d.consultant_id AND
--     a.day_of_week = d.day_of_week AND
--     a.start_time = d.start_time AND
--     a.end_time = d.end_time
-- JOIN temp_slots_to_keep k ON 
--     a.consultant_id = d.consultant_id AND
--     a.day_of_week = d.day_of_week AND
--     a.start_time = d.start_time AND
--     a.end_time = d.end_time
-- WHERE a.id != k.id_to_keep
-- AND NOT EXISTS (
--     SELECT 1 FROM time_slot_consultation_types t 
--     WHERE t.availability_schedule_id = k.id_to_keep 
--     AND t.consultation_type = a.consultation_type
-- );

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

-- Step 4: Skip removing consultation_type column since it doesn't exist
-- ALTER TABLE `availability_schedule` DROP COLUMN `consultation_type`;

-- Step 5: Add indexes to improve query performance
ALTER TABLE `availability_schedule` ADD INDEX `idx_consultant_day` (`consultant_id`, `day_of_week`, `start_time`, `end_time`);
ALTER TABLE `time_slot_consultation_types` ADD INDEX `idx_consultation_type` (`consultation_type`); 