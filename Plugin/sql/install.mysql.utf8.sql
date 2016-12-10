CREATE TABLE IF NOT EXISTS `#__trangel_hikashop_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(15,7) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__trangel_hikashop_variant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL,
  `variant_id` int(10) NOT NULL,
  `variant_name` varchar(255) NOT NULL,
  `variant_price` decimal(15,7) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
