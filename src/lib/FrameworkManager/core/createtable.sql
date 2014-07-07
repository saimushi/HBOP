-- MySQL --
-- user --
CREATE TABLE IF NOT EXISTS `user` (`id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'pkey', `name` VARCHAR(1024) NOT NULL COMMENT '名前', `mail` VARCHAR(1024) NOT NULL COMMENT 'メールアドレス', `pass` VARCHAR(64) NOT NULL COMMENT 'パスワード(SHA256)', `rolu` CHAR(1) NOT NULL DEFAULT '1' COMMENT 'アクセス権限', PRIMARY KEY(`id`));
-- session --
CREATE TABLE IF NOT EXISTS `session` (`token` VARCHAR(255) NOT NULL COMMENT 'ワンタイムトークン', `created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'トークン作成日時', PRIMARY KEY(`token`)) ENGINE = MYISAM;
CREATE TABLE IF NOT EXISTS `sessiondata` (`uid` CHAR(32) NOT NULL COMMENT 'user_idから算出したUID', `data` TEXT DEFAULT '' COMMENT 'jsonシリアライズされたセッションデータ', `modified` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '変更日時', PRIMARY KEY(`uid`)) ENGINE = MYISAM;
