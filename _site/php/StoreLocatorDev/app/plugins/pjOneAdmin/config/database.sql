DROP TABLE IF EXISTS `plugin_one_admin`;
CREATE TABLE IF NOT EXISTS `plugin_one_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
(NULL, 'plugin_one_admin_menu_index', 'backend', 'One Admin plugin / List', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES
(NULL, @id, 'pjField', 1, 'title', 'List', 'plugin'),
(NULL, @id, 'pjField', 2, 'title', 'List', 'plugin'),
(NULL, @id, 'pjField', 3, 'title', 'List', 'plugin');

INSERT INTO `fields` (`id`, `key`, `type`, `label`, `source`, `modified`) VALUES
(NULL, 'plugin_one_admin_btn_add', 'backend', 'One Admin plugin / Add button', 'plugin', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES
(NULL, @id, 'pjField', 1, 'title', '+ Add', 'plugin'),
(NULL, @id, 'pjField', 2, 'title', '+ Add', 'plugin'),
(NULL, @id, 'pjField', 3, 'title', '+ Add', 'plugin');