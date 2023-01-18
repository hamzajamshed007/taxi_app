create table extra_driver_info(
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    user_id int UNSIGNED ,
    driver_license_number varchar(255) NULL,
    driver_license_front varchar(255) NULL,
    driver_license_back varchar(255) NULL,
    date_of_expiration varchar(255) NULL,
    cnic_number varchar(255) NULL,
    cnic_front varchar(255) NULL,
    cnic_back varchar(255) NULL,
    car_name varchar(255) NULL,
    number_plate varchar(255) NULL,
    model_year varchar(255) NULL,
    car_photo varchar(255) NULL,
    car_certificate_front varchar(255) NULL,
    car_certificate_back varchar(255) NULL,
      PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
)