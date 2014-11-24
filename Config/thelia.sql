
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- keyword_group
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `keyword_group`;

CREATE TABLE `keyword_group`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `visible` TINYINT,
    `position` INTEGER,
    `code` VARCHAR(255),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- keyword
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `keyword`;

CREATE TABLE `keyword`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `keyword_group_id` INTEGER NOT NULL,
    `visible` TINYINT,
    `position` INTEGER,
    `code` VARCHAR(255),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `idx_keyword_group_id` (`keyword_group_id`),
    CONSTRAINT `fk_keyword_group_id`
        FOREIGN KEY (`keyword_group_id`)
        REFERENCES `keyword_group` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- content_associated_keyword
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `content_associated_keyword`;

CREATE TABLE `content_associated_keyword`
(
    `content_id` INTEGER NOT NULL,
    `keyword_id` INTEGER NOT NULL,
    `position` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`content_id`,`keyword_id`),
    INDEX `idx_content_associated_keyword_content_id` (`content_id`),
    INDEX `idx_content_associated_keyword_keyword_id` (`keyword_id`),
    CONSTRAINT `fk_content_associated_keyword_content_id`
        FOREIGN KEY (`content_id`)
        REFERENCES `content` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `fk_content_associated_keyword_keyword_id`
        FOREIGN KEY (`keyword_id`)
        REFERENCES `keyword` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- folder_associated_keyword
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `folder_associated_keyword`;

CREATE TABLE `folder_associated_keyword`
(
    `folder_id` INTEGER NOT NULL,
    `keyword_id` INTEGER NOT NULL,
    `position` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`folder_id`,`keyword_id`),
    INDEX `idx_folder_associated_keyword_folder_id` (`folder_id`),
    INDEX `idx_folder_associated_keyword_keyword_id` (`keyword_id`),
    CONSTRAINT `fk_folder_associated_keyword_folder_id`
        FOREIGN KEY (`folder_id`)
        REFERENCES `folder` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `fk_folder_associated_keyword_keyword_id`
        FOREIGN KEY (`keyword_id`)
        REFERENCES `keyword` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- category_associated_keyword
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `category_associated_keyword`;

CREATE TABLE `category_associated_keyword`
(
    `category_id` INTEGER NOT NULL,
    `keyword_id` INTEGER NOT NULL,
    `position` INTEGER NOT NULL,
    PRIMARY KEY (`category_id`,`keyword_id`),
    INDEX `idx_category_associated_keyword_category_id` (`category_id`),
    INDEX `idx_category_associated_keyword_keyword_id` (`keyword_id`),
    CONSTRAINT `fk_category_associated_keyword_category_id`
        FOREIGN KEY (`category_id`)
        REFERENCES `category` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `fk_category_associated_keyword_keyword_id`
        FOREIGN KEY (`keyword_id`)
        REFERENCES `keyword` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- product_associated_keyword
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `product_associated_keyword`;

CREATE TABLE `product_associated_keyword`
(
    `product_id` INTEGER NOT NULL,
    `keyword_id` INTEGER NOT NULL,
    `position` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`product_id`,`keyword_id`),
    INDEX `idx_product_associated_keyword_product_id` (`product_id`),
    INDEX `idx_product_associated_keyword_keyword_id` (`keyword_id`),
    CONSTRAINT `fk_product_associated_keyword_product_id`
        FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE,
    CONSTRAINT `fk_product_associated_keyword_keyword_id`
        FOREIGN KEY (`keyword_id`)
        REFERENCES `keyword` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- keyword_group_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `keyword_group_i18n`;

CREATE TABLE `keyword_group_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    `chapo` TEXT,
    `postscriptum` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `keyword_group_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `keyword_group` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- keyword_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `keyword_i18n`;

CREATE TABLE `keyword_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    `chapo` TEXT,
    `postscriptum` TEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `keyword_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `keyword` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
