<?php
$db = Db::getInstance();
$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'chatgpt_log` (
    `id_chatgpt_log` int(11) NOT NULL AUTO_INCREMENT,
    `prompt` text NOT NULL,
    `prompt_original` text NOT NULL,
    `response` text NOT NULL,
    `date_add` datetime NOT NULL,
    PRIMARY KEY (`id_chatgpt_log`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

foreach ($sql as $query) {
    if (!$db->execute($query)) return false;
}