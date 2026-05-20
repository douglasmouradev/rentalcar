-- Registo de acesso após aceitação da política no login (LGPD — rastreabilidade)
CREATE TABLE IF NOT EXISTS privacy_login_consent (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id          INT UNSIGNED     NOT NULL,
  ip_hash          CHAR(64)         NOT NULL,
  user_agent_hash  CHAR(64)         NOT NULL,
  created_at       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_privacy_login_user (user_id),
  INDEX idx_privacy_login_created (created_at)
);
