ALTER TABLE leads
  ADD COLUMN contact_name VARCHAR(120) NULL AFTER return_location_text,
  ADD COLUMN contact_email VARCHAR(180) NULL AFTER contact_name,
  ADD COLUMN contact_phone VARCHAR(30) NULL AFTER contact_email;
