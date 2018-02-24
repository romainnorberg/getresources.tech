-- Doctrine Migration File Generated on 2018-02-24 16:04:43

-- Version 20180224160419
ALTER TABLE user ADD roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)';
INSERT INTO migration_versions (version) VALUES ('20180224160419');
UPDATE `user` SET `roles` = 'a:0:{}'

