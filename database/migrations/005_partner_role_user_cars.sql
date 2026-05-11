-- Perfil cotista (partner): atribuição de veículos por utilizador
ALTER TABLE users
  MODIFY COLUMN role ENUM('owner', 'operator', 'partner') NOT NULL DEFAULT 'operator';

CREATE TABLE IF NOT EXISTS user_cars (
  user_id INT UNSIGNED NOT NULL,
  car_id  INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, car_id),
  CONSTRAINT fk_user_cars_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_user_cars_car FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
  INDEX idx_user_cars_car (car_id)
);
