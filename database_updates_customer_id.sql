-- Check if user_id exists in appointments table and needs to be renamed to consultant_id
SET @user_id_exists := (SELECT COUNT(*)
                        FROM INFORMATION_SCHEMA.COLUMNS
                        WHERE TABLE_NAME = 'appointments'
                        AND COLUMN_NAME = 'user_id'
                        AND TABLE_SCHEMA = DATABASE());

SET @consultant_id_exists := (SELECT COUNT(*)
                              FROM INFORMATION_SCHEMA.COLUMNS
                              WHERE TABLE_NAME = 'appointments'
                              AND COLUMN_NAME = 'consultant_id'
                              AND TABLE_SCHEMA = DATABASE());

SET @rename_query = IF(@user_id_exists > 0 AND @consultant_id_exists = 0, 
                       'ALTER TABLE appointments CHANGE COLUMN user_id consultant_id INT NOT NULL', 
                       'SELECT "No need to rename user_id" AS message');

PREPARE stmt FROM @rename_query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- If neither user_id nor consultant_id exists, add consultant_id column
SET @add_consultant_id = IF(@user_id_exists = 0 AND @consultant_id_exists = 0,
                           'ALTER TABLE appointments ADD COLUMN consultant_id INT NOT NULL',
                           'SELECT "consultant_id already exists or was just renamed" AS message');

PREPARE stmt FROM @add_consultant_id;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add customer_id column to appointments table (check if column exists first)
SET @exist := (SELECT COUNT(*)
               FROM INFORMATION_SCHEMA.COLUMNS
               WHERE TABLE_NAME = 'appointments'
               AND COLUMN_NAME = 'customer_id'
               AND TABLE_SCHEMA = DATABASE());

SET @query = IF(@exist = 0, 'ALTER TABLE appointments ADD COLUMN customer_id INT AFTER consultant_id', 'SELECT "Column customer_id already exists" AS message');

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create index on customer_id for faster queries (only if it doesn't exist)
SET @exist_index := (SELECT COUNT(*)
                     FROM INFORMATION_SCHEMA.STATISTICS
                     WHERE TABLE_NAME = 'appointments'
                     AND INDEX_NAME = 'idx_appointment_customer'
                     AND TABLE_SCHEMA = DATABASE());

SET @query_index = IF(@exist_index = 0, 'CREATE INDEX idx_appointment_customer ON appointments(customer_id)', 'SELECT "Index idx_appointment_customer already exists" AS message');

PREPARE stmt FROM @query_index;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key constraint to link appointments to customers (only if it doesn't exist)
SET @exist_fk := (SELECT COUNT(*)
                  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                  WHERE TABLE_NAME = 'appointments'
                  AND CONSTRAINT_NAME = 'fk_appointment_customer'
                  AND TABLE_SCHEMA = DATABASE());

SET @query_fk = IF(@exist_fk = 0, 'ALTER TABLE appointments ADD CONSTRAINT fk_appointment_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL ON UPDATE CASCADE', 'SELECT "Foreign key fk_appointment_customer already exists" AS message');

PREPARE stmt FROM @query_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Migration to populate customer_id from existing appointment data
-- This assumes that customers with the same email in appointments table match to customers table
UPDATE appointments a
JOIN customers c ON a.email = c.email
SET a.customer_id = c.id
WHERE a.customer_id IS NULL;

-- Create customer_notes table if it doesn't exist yet
CREATE TABLE IF NOT EXISTS customer_notes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  customer_id INT NOT NULL,
  consultant_id INT NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
  FOREIGN KEY (consultant_id) REFERENCES consultants(id) ON DELETE CASCADE
); 