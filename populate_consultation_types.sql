-- This script populates the time_slot_consultation_types table with default values
-- for existing availability_schedule entries that don't have consultation types assigned

-- First, make sure all consultants have the proper day_consultation_availability settings
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

-- For each existing time slot in availability_schedule, add default consultation types
-- based on the consultant's consultation availability settings for that day

-- First, add Video Consultation type where it's available
INSERT INTO time_slot_consultation_types (availability_schedule_id, consultation_type)
SELECT 
    a.id as availability_schedule_id,
    'Video Consultation' as consultation_type
FROM 
    availability_schedule a
JOIN 
    day_consultation_availability d ON a.consultant_id = d.consultant_id AND a.day_of_week = d.day_of_week
WHERE 
    d.video_available = 1
    AND NOT EXISTS (
        SELECT 1 FROM time_slot_consultation_types t 
        WHERE t.availability_schedule_id = a.id 
        AND t.consultation_type = 'Video Consultation'
    );

-- Next, add Phone Consultation type where it's available
INSERT INTO time_slot_consultation_types (availability_schedule_id, consultation_type)
SELECT 
    a.id as availability_schedule_id,
    'Phone Consultation' as consultation_type
FROM 
    availability_schedule a
JOIN 
    day_consultation_availability d ON a.consultant_id = d.consultant_id AND a.day_of_week = d.day_of_week
WHERE 
    d.phone_available = 1
    AND NOT EXISTS (
        SELECT 1 FROM time_slot_consultation_types t 
        WHERE t.availability_schedule_id = a.id 
        AND t.consultation_type = 'Phone Consultation'
    );

-- Finally, add In-Person Consultation type where it's available
INSERT INTO time_slot_consultation_types (availability_schedule_id, consultation_type)
SELECT 
    a.id as availability_schedule_id,
    'In-Person Consultation' as consultation_type
FROM 
    availability_schedule a
JOIN 
    day_consultation_availability d ON a.consultant_id = d.consultant_id AND a.day_of_week = d.day_of_week
WHERE 
    d.in_person_available = 1
    AND NOT EXISTS (
        SELECT 1 FROM time_slot_consultation_types t 
        WHERE t.availability_schedule_id = a.id 
        AND t.consultation_type = 'In-Person Consultation'
    );

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