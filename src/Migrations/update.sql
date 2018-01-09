-- Doctrine Migration File Generated on 2018-01-08 08:32:24

-- Version 20180108073215
ALTER TABLE user ADD last_login DATETIME DEFAULT NULL;
INSERT INTO migration_versions (version) VALUES ('20180108073215');
