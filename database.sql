-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `mydb` ;

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`Channel`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Channel` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Channel` (
  `id` VARCHAR(20) NOT NULL,
  `guild_id` VARCHAR(20) NOT NULL,
  `name` VARCHAR(40) NOT NULL,
  `type` VARCHAR(5) NOT NULL,
  `position` INT(11) NOT NULL,
  `is_private` TINYINT(1) NOT NULL,
  `permission_overwrites` VARCHAR(45) NULL DEFAULT NULL,
  `topic` TEXT NOT NULL,
  `bitrate` VARCHAR(45) NULL DEFAULT NULL,
  `user_limit` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`Command`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Command` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Command` (
  `namespace` VARCHAR(145) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `activated` TINYINT(1) NOT NULL,
  PRIMARY KEY (`namespace`),
  UNIQUE INDEX `namespace_UNIQUE` (`namespace` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`CommandAlias`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`CommandAlias` ;

CREATE TABLE IF NOT EXISTS `mydb`.`CommandAlias` (
  `title` VARCHAR(20) NOT NULL,
  `Command_namespace` VARCHAR(145) NOT NULL,
  PRIMARY KEY (`title`, `Command_namespace`),
  UNIQUE INDEX `title_UNIQUE` (`title` ASC),
  INDEX `fk_CommandAlias_Command1_idx` (`Command_namespace` ASC),
  CONSTRAINT `fk_CommandAlias_Command1`
    FOREIGN KEY (`Command_namespace`)
    REFERENCES `mydb`.`Command` (`namespace`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`Command_chatlog_loggable_channels`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Command_chatlog_loggable_channels` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Command_chatlog_loggable_channels` (
  `Channel_id` VARCHAR(20) NOT NULL,
  `loggable` TINYINT(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Channel_id`),
  UNIQUE INDEX `Channel_id_UNIQUE` (`Channel_id` ASC),
  INDEX `fk_Command_chatlog_loggable_channels_Channel1_idx` (`Channel_id` ASC),
  CONSTRAINT `fk_Command_chatlog_loggable_channels_Channel1`
    FOREIGN KEY (`Channel_id`)
    REFERENCES `mydb`.`Channel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`Role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Role` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Role` (
  `id` VARCHAR(20) NOT NULL,
  `name` VARCHAR(32) NOT NULL,
  `color` VARCHAR(8) NOT NULL,
  `hoist` TINYINT(1) NOT NULL,
  `position` INT(11) NOT NULL,
  `managed` TINYINT(1) NOT NULL,
  `mentionable` TINYINT(1) NOT NULL,
  `permissions` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`Command_has_Role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Command_has_Role` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Command_has_Role` (
  `Command_namespace` VARCHAR(145) NOT NULL,
  `Role_id` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`Command_namespace`, `Role_id`),
  INDEX `fk_Command_has_Role_Role1_idx` (`Role_id` ASC),
  INDEX `fk_Command_has_Role_Command1_idx` (`Command_namespace` ASC),
  CONSTRAINT `fk_Command_has_Role_Command1`
    FOREIGN KEY (`Command_namespace`)
    REFERENCES `mydb`.`Command` (`namespace`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Command_has_Role_Role1`
    FOREIGN KEY (`Role_id`)
    REFERENCES `mydb`.`Role` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`Guild`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Guild` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Guild` (
  `guild_id` VARCHAR(64) NOT NULL COMMENT 'should only have one entry',
  PRIMARY KEY (`guild_id`),
  UNIQUE INDEX `guild_id_UNIQUE` (`guild_id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`Level`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Level` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Level` (
  `title` VARCHAR(9) NOT NULL,
  `description` TEXT NOT NULL,
  `value` INT(11) NOT NULL,
  PRIMARY KEY (`title`),
  UNIQUE INDEX `title_UNIQUE` (`title` ASC),
  UNIQUE INDEX `value_UNIQUE` (`value` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`Logger`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Logger` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Logger` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `namespace` VARCHAR(100) NOT NULL,
  `classname` VARCHAR(35) NOT NULL,
  `message` VARCHAR(256) NOT NULL,
  `Level_title` VARCHAR(9) NOT NULL,
  PRIMARY KEY (`id`, `Level_title`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_Logs_Level1_idx` (`Level_title` ASC),
  CONSTRAINT `fk_Logs_Level1`
    FOREIGN KEY (`Level_title`)
    REFERENCES `mydb`.`Level` (`title`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`User`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`User` ;

CREATE TABLE IF NOT EXISTS `mydb`.`User` (
  `discord_id` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`discord_id`),
  UNIQUE INDEX `user_discord_id_UNIQUE` (`discord_id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`Message`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Message` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Message` (
  `id` VARCHAR(20) NOT NULL,
  `deleted` TINYINT(1) NOT NULL,
  `Channel_id` VARCHAR(20) NOT NULL,
  `User_discord_id` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id`, `Channel_id`, `User_discord_id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_Message_User1_idx` (`User_discord_id` ASC),
  INDEX `fk_Message_Channel1_idx` (`Channel_id` ASC),
  CONSTRAINT `fk_Message_Channel1`
    FOREIGN KEY (`Channel_id`)
    REFERENCES `mydb`.`Channel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Message_User1`
    FOREIGN KEY (`User_discord_id`)
    REFERENCES `mydb`.`User` (`discord_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`MessageContent`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`MessageContent` ;

CREATE TABLE IF NOT EXISTS `mydb`.`MessageContent` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `content` TEXT NOT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Message_id` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id`, `Message_id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_MessageContent_Message1` (`Message_id` ASC),
  CONSTRAINT `fk_MessageContent_Message1`
    FOREIGN KEY (`Message_id`)
    REFERENCES `mydb`.`Message` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 637
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`Service`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`Service` ;

CREATE TABLE IF NOT EXISTS `mydb`.`Service` (
  `title` VARCHAR(45) NOT NULL,
  `error` VARCHAR(256) NULL DEFAULT NULL,
  PRIMARY KEY (`title`),
  UNIQUE INDEX `title_UNIQUE` (`title` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`UserName`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`UserName` ;

CREATE TABLE IF NOT EXISTS `mydb`.`UserName` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `User_discord_id` VARCHAR(20) NOT NULL,
  `username` VARCHAR(45) NOT NULL,
  `discriminator` INT(4) NOT NULL,
  `avatar` VARCHAR(145) NOT NULL,
  `bot` TINYINT(1) NOT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`, `User_discord_id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_UserName_User1_idx` (`User_discord_id` ASC),
  CONSTRAINT `fk_UserName_User1`
    FOREIGN KEY (`User_discord_id`)
    REFERENCES `mydb`.`User` (`discord_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 160
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`UserName_has_Role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`UserName_has_Role` ;

CREATE TABLE IF NOT EXISTS `mydb`.`UserName_has_Role` (
  `UserName_id` INT(11) NOT NULL,
  `UserName_User_discord_id` VARCHAR(20) NOT NULL,
  `Role_id` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`UserName_id`, `UserName_User_discord_id`, `Role_id`),
  INDEX `fk_UserName_has_Role_Role1_idx` (`Role_id` ASC),
  INDEX `fk_UserName_has_Role_UserName1_idx` (`UserName_id` ASC, `UserName_User_discord_id` ASC),
  CONSTRAINT `fk_UserName_has_Role_Role1`
    FOREIGN KEY (`Role_id`)
    REFERENCES `mydb`.`Role` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_UserName_has_Role_UserName1`
    FOREIGN KEY (`UserName_id` , `UserName_User_discord_id`)
    REFERENCES `mydb`.`UserName` (`id` , `User_discord_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`nootropics`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`nootropics` ;

CREATE TABLE IF NOT EXISTS `mydb`.`nootropics` (
  `id` INT(11) NOT NULL,
  `name` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `mydb`.`user_stacks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`user_stacks` ;

CREATE TABLE IF NOT EXISTS `mydb`.`user_stacks` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `stack` TINYTEXT NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
