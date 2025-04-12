-- Add check constraints to day_consultation_availability table to ensure values are always 0 or 1
ALTER TABLE day_consultation_availability
ADD CONSTRAINT video_available_check CHECK (video_available IN (0, 1));

ALTER TABLE day_consultation_availability
ADD CONSTRAINT phone_available_check CHECK (phone_available IN (0, 1));

ALTER TABLE day_consultation_availability
ADD CONSTRAINT in_person_available_check CHECK (in_person_available IN (0, 1));

-- Make sure foreign key constraint exists (in case it's missing)
ALTER TABLE day_consultation_availability
ADD CONSTRAINT day_consultation_availability_consultant_fk 
FOREIGN KEY (consultant_id) REFERENCES consultants (id) ON DELETE CASCADE; 