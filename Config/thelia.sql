
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- keyword
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `keyword`;

CREATE TABLE `keyword`
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
