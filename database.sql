-- MySQL database export

START TRANSACTION;

CREATE TABLE IF NOT EXISTS `cars_models` (
    `id` BIGINT NOT NULL,
    `car_manufacturer_id` BIGINT,
    `name` VARCHAR(255),
    `is_active` BIGINT,
    `model_year` DATE,
    `service_level_id` BIGINT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `trip_types` (
    `id` BIGINT NOT NULL,
    `name` BIGINT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `payment_methods` (
    `id` BIGINT NOT NULL,
    `name` VARCHAR(255),
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `car_service_level` (
    `id` BIGINT NOT NULL,
    `name` VARCHAR(255),
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `driver_car` (
    `id` BIGINT NOT NULL,
    `driver_id` BIGINT,
    `car_id` BIGINT,
    `front_photo` VARCHAR(255),
    `back_photo` BIGINT,
    `left_photo` BIGINT,
    `right_photo` BIGINT,
    `inside_photo` BIGINT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `riders` (
    `id` BIGINT NOT NULL,
    `first_name` VARCHAR(255),
    `last_name` VARCHAR(255),
    `birth_date` DATE,
    `phone_number` VARCHAR(255),
    `email` VARCHAR(255),
    `profile_photo` VARCHAR(255),
    `defaul_payment_id` BIGINT,
    `avg_rating` DECIMAL,
    `account_status` TINYINT(1),
    `w` BIGINT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `rider_folders` (
    `id` BIGINT NOT NULL,
    `rider_id` BIGINT,
    `name` VARCHAR(255),
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `pricing` (
    `id` BIGINT,
    `service_level_id` BIGINT,
    `price_per_km` DECIMAL,
    `base_fare` DECIMAL,
    `valid_from` DATETIME,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `trip_statuses` (
    `id` BIGINT NOT NULL,
    `name` BIGINT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `cars` (
    `id` BIGINT NOT NULL,
    `car_manufacturer_id` BIGINT,
    `number_of_seats` INT,
    `is_active` TINYINT(1),
    `car_model_id` BIGINT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `drivers` (
    `id` BIGINT NOT NULL,
    `wallet_id` BIGINT,
    `first_name` VARCHAR(255),
    `last_name` VARCHAR(255),
    `birth_date` DATE,
    `phone_number` VARCHAR(255),
    `email` VARCHAR(255),
    `profile_photo` VARCHAR(255),
    `avg_rating` DECIMAL,
    `account_status` TINYINT(1),
    `status_id` BIGINT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `rider_saved_locations` (
    `id` BIGINT NOT NULL,
    `rider_id` BIGINT,
    `location` point,
    `folder_id` BIGINT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `rider_coupons` (
    `id` BIGINT NOT NULL,
    `rider_id` BIGINT,
    `coupon_id` BIGINT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `countries` (
    `id` BIGINT NOT NULL,
    `name` VARCHAR(255),
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `trip_locations` (
    `id` BIGINT NOT NULL,
    `trip_id` BIGINT,
    `location_type` VARCHAR(50),
    `location_order` SMALLINT,
    `coordinates` point,
    `estimated_arrival` DATETIME,
    `actual_arrival` DATETIME NOT NULL,
    `is_completed` TINYINT(1),
    PRIMARY KEY (`id`, `is_completed`)
);


CREATE TABLE IF NOT EXISTS `transactions` (
    `id` BIGINT,
    `wallet_id` BIGINT,
    -- DECIMAL(15,2),
    `amount ` DOUBLE COMMENT 'DECIMAL(15,2),',
    `type` VARCHAR(50),
    `code` VARCHAR(255),
    `trip_id` BIGINT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `car_manufacturers` (
    `id` BIGINT NOT NULL,
    `country_id` BIGINT,
    `name` VARCHAR(255),
    `is_active` TINYINT(1),
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `wallets` (
    `id` BIGINT,
    `user_id` BIGINT,
    `user_type` VARCHAR(50),
    `balance` DECIMAL,
    `created_at` DATETIME,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `driver_statuses` (
    `id` BIGINT NOT NULL,
    `status` VARCHAR(255),
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `coupons` (
    `id` BIGINT NOT NULL,
    `code` VARCHAR(255),
    `perecent` DOUBLE,
    `end_time` DATE,
    `max_amount` BIGINT,
    `used_count ` BIGINT,
    PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS `trips` (
    `id` BIGINT NOT NULL,
    `rider_id` BIGINT,
    `driver_id` BIGINT,
    `trip_status_id` BIGINT,
    `trip_type_id` BIGINT,
    `cupon_id` BIGINT,
    `payment_type_id` BIGINT,
    `pickup_location` point,
    `dropoff_location` point,
    `stops` BIGINT,
    `start_time` DATETIME,
    `end_time` DATETIME,
    `fare` INT,
    `distance` BIGINT,
    PRIMARY KEY (`id`)
);


-- Foreign key constraints

ALTER TABLE `car_service_level`
ADD CONSTRAINT `fk_car_service_level_id` FOREIGN KEY(`id`) REFERENCES `pricing`(`service_level_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `car_service_level`
ADD CONSTRAINT `fk_car_service_level_id` FOREIGN KEY(`id`) REFERENCES `cars_models`(`service_level_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `cars`
ADD CONSTRAINT `fk_cars_id` FOREIGN KEY(`id`) REFERENCES `driver_car`(`car_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `car_manufacturers`
ADD CONSTRAINT `fk_car_manufacturers_id` FOREIGN KEY(`id`) REFERENCES `cars_models`(`car_manufacturer_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `cars_models`
ADD CONSTRAINT `fk_cars_models_id` FOREIGN KEY(`id`) REFERENCES `cars`(`car_model_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `countries`
ADD CONSTRAINT `fk_countries_id` FOREIGN KEY(`id`) REFERENCES `car_manufacturers`(`country_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `coupons`
ADD CONSTRAINT `fk_coupons_id` FOREIGN KEY(`id`) REFERENCES `trips`(`cupon_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `coupons`
ADD CONSTRAINT `fk_coupons_id` FOREIGN KEY(`id`) REFERENCES `rider_coupons`(`coupon_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `driver_statuses`
ADD CONSTRAINT `fk_driver_statuses_id` FOREIGN KEY(`id`) REFERENCES `drivers`(`status_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `drivers`
ADD CONSTRAINT `fk_drivers_id` FOREIGN KEY(`id`) REFERENCES `trips`(`driver_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `drivers`
ADD CONSTRAINT `fk_drivers_id` FOREIGN KEY(`id`) REFERENCES `driver_car`(`driver_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `drivers`
ADD CONSTRAINT `fk_drivers_wallet_id` FOREIGN KEY(`wallet_id`) REFERENCES `wallets`(`id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `payment_methods`
ADD CONSTRAINT `fk_payment_methods_id` FOREIGN KEY(`id`) REFERENCES `riders`(`defaul_payment_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `payment_methods`
ADD CONSTRAINT `fk_payment_methods_id` FOREIGN KEY(`id`) REFERENCES `trips`(`trip_type_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `rider_folders`
ADD CONSTRAINT `fk_rider_folders_id` FOREIGN KEY(`id`) REFERENCES `rider_saved_locations`(`folder_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `riders`
ADD CONSTRAINT `fk_riders_id` FOREIGN KEY(`id`) REFERENCES `rider_coupons`(`rider_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `riders`
ADD CONSTRAINT `fk_riders_id` FOREIGN KEY(`id`) REFERENCES `rider_folders`(`rider_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `riders`
ADD CONSTRAINT `fk_riders_id` FOREIGN KEY(`id`) REFERENCES `wallets`(`id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `riders`
ADD CONSTRAINT `fk_riders_id` FOREIGN KEY(`id`) REFERENCES `trips`(`rider_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `riders`
ADD CONSTRAINT `fk_riders_id` FOREIGN KEY(`id`) REFERENCES `rider_saved_locations`(`rider_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `trip_statuses`
ADD CONSTRAINT `fk_trip_statuses_id` FOREIGN KEY(`id`) REFERENCES `trips`(`trip_status_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `trip_types`
ADD CONSTRAINT `fk_trip_types_id` FOREIGN KEY(`id`) REFERENCES `trips`(`trip_type_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `trips`
ADD CONSTRAINT `fk_trips_id` FOREIGN KEY(`id`) REFERENCES `trip_locations`(`trip_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `wallets`
ADD CONSTRAINT `fk_wallets_id` FOREIGN KEY(`id`) REFERENCES `transactions`(`wallet_id`)
ON UPDATE CASCADE ON DELETE RESTRICT;

COMMIT;
