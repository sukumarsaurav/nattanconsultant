-- Rename user_id to consultant_id in appointments table
ALTER TABLE appointments CHANGE COLUMN user_id consultant_id INT NOT NULL;
-- Add foreign key constraint
ALTER TABLE appointments ADD CONSTRAINT fk_appointment_consultant FOREIGN KEY (consultant_id) REFERENCES consultants(id) ON DELETE CASCADE ON UPDATE CASCADE;
-- Add indexes
CREATE INDEX idx_appointment_status ON appointments(status);
CREATE INDEX idx_appointment_datetime ON appointments(appointment_datetime);
CREATE INDEX idx_consultant_appointments ON appointments(consultant_id, status);
-- Add additional columns
ALTER TABLE appointments ADD COLUMN IF NOT EXISTS consultation_type VARCHAR(50) NOT NULL DEFAULT 'general' AFTER consultant_id, ADD COLUMN IF NOT EXISTS duration INT NOT NULL DEFAULT 60 AFTER consultation_type, ADD COLUMN IF NOT EXISTS notes TEXT AFTER duration, ADD COLUMN IF NOT EXISTS cancellation_reason TEXT AFTER notes, ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
-- Create appointment_history table
CREATE TABLE IF NOT EXISTS appointment_history (id INT PRIMARY KEY AUTO_INCREMENT, appointment_id INT NOT NULL, user_id INT NOT NULL, user_type ENUM('consultant', 'client') NOT NULL, action VARCHAR(50) NOT NULL, details TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE);
-- Create appointment_notes table
CREATE TABLE IF NOT EXISTS appointment_notes (id INT PRIMARY KEY AUTO_INCREMENT, appointment_id INT NOT NULL, user_id INT NOT NULL, user_type ENUM('consultant', 'client') NOT NULL, note TEXT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE);
