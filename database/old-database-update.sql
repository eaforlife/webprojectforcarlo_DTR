ALTER TABLE `emp_accounts` ADD `photo` VARCHAR(100) NOT NULL DEFAULT 'generic_avatar.png' AFTER `email`;
ALTER TABLE `emp_accounts` CHANGE `acctID` `acctID` INT(11) NULL AUTO_INCREMENT;
ALTER TABLE `emp_accounts` AUTO_INCREMENT=1000000001;