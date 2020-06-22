ALTER TABLE `cimetieres` CHANGE `slugname` `slug` VARCHAR(70) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `defunts` CHANGE `slugname` `slug` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `page` CHANGE `slugname` `slug` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `sepultures` CHANGE `slugname` `slug` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `sihl` CHANGE `slugname` `slug` VARCHAR(70) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;