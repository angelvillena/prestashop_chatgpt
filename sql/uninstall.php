<?php
$db = Db::getInstance();
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'chatgpt_log`';

foreach ($sql as $query) {
    if (!$db->execute($query)) return false;
}

return true;
