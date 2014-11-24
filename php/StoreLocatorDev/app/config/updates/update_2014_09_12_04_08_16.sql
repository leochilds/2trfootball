
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_label_address_not_found', 'frontend', 'Label / Address could not be found.', 'script', '2014-09-12 04:04:43');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '1', 'title', 'Address could not be found.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '2', 'title', 'Address could not be found.', 'script');
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '3', 'title', 'Address could not be found.', 'script');

COMMIT;