-- Create day_consultation_availability table for day-specific consultation settings
CREATE TABLE IF NOT EXISTS day_consultation_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consultant_id INT NOT NULL,
    day_of_week VARCHAR(20) NOT NULL,
    video_available TINYINT(1) DEFAULT 0,
    phone_available TINYINT(1) DEFAULT 0,
    in_person_available TINYINT(1) DEFAULT 0,
    UNIQUE KEY consultant_day (consultant_id, day_of_week),
    FOREIGN KEY (consultant_id) REFERENCES consultants(id) ON DELETE CASCADE
); 