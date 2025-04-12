-- Add consultation_type column to availability_schedule table
ALTER TABLE `availability_schedule` 
ADD COLUMN `consultation_type` ENUM('Video Consultation', 'Phone Consultation', 'In-Person Consultation') 
AFTER `end_time`;

-- Update existing records to set a default consultation type (optional)
-- You might want to run this only if you want to preserve existing data
UPDATE `availability_schedule` 
SET `consultation_type` = 'Video Consultation' 
WHERE `consultation_type` IS NULL; 