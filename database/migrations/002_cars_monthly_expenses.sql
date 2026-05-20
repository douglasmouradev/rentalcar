-- Gastos mensais estimados por veículo (bases já criadas antes desta alteração)
USE titanium_rental_car;

ALTER TABLE cars
  ADD COLUMN monthly_fuel DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER mileage,
  ADD COLUMN monthly_toll DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER monthly_fuel,
  ADD COLUMN monthly_wash DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER monthly_toll,
  ADD COLUMN monthly_maintenance DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER monthly_wash,
  ADD COLUMN monthly_extra DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER monthly_maintenance;
