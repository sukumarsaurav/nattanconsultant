-- Add foreign key constraint if not exists
ALTER TABLE appointments ADD CONSTRAINT IF NOT EXISTS fk_appointment_consultant FOREIGN KEY (consultant_id) REFERENCES consultants(id) ON DELETE CASCADE ON UPDATE CASCADE;
-- Add indexes if not exist
CREATE INDEX IF NOT EXISTS idx_appointment_status ON appointments(status);
CREATE INDEX IF NOT EXISTS idx_appointment_datetime ON appointments(appointment_datetime);
CREATE INDEX IF NOT EXISTS idx_consultant_appointments ON appointments(consultant_id, status);
-- Add additional columns if they don't exist
ALTER TABLE appointments ADD COLUMN IF NOT EXISTS consultation_type VARCHAR(50) NOT NULL DEFAULT 'general' AFTER consultant_id;
ALTER TABLE appointments ADD COLUMN IF NOT EXISTS duration INT NOT NULL DEFAULT 60 AFTER consultation_type;
ALTER TABLE appointments ADD COLUMN IF NOT EXISTS notes TEXT AFTER duration;
ALTER TABLE appointments ADD COLUMN IF NOT EXISTS cancellation_reason TEXT AFTER notes;
ALTER TABLE appointments ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE appointments ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
-- Create appointment_history table
CREATE TABLE IF NOT EXISTS appointment_history (id INT PRIMARY KEY AUTO_INCREMENT, appointment_id INT NOT NULL, user_id INT NOT NULL, user_type ENUM('consultant', 'client') NOT NULL, action VARCHAR(50) NOT NULL, details TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE);
-- Create appointment_notes table
CREATE TABLE IF NOT EXISTS appointment_notes (id INT PRIMARY KEY AUTO_INCREMENT, appointment_id INT NOT NULL, user_id INT NOT NULL, user_type ENUM('consultant', 'client') NOT NULL, note TEXT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE);
