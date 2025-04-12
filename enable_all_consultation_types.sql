-- Enable all consultation types by default for all days
UPDATE day_consultation_availability 
SET video_available = 1, phone_available = 1, in_person_available = 1;

-- For any consultant/day combinations that don't have records yet, create them with all types enabled
INSERT IGNORE INTO day_consultation_availability (consultant_id, day_of_week, video_available, phone_available, in_person_available)
SELECT 
    c.id as consultant_id,
    dow.day_name as day_of_week,
    1 as video_available,
    1 as phone_available,
    1 as in_person_available
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