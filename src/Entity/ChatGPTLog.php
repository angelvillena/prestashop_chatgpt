<?php

class ChatGPTLog extends ObjectModel
{
    public $id_chatgpt_log;
    public $prompt;
    public $prompt_original;
    public $response;
    public $date_add;

    public static $definition = array(
        'table' => 'chatgpt_log',
        'primary' => 'id_chatgpt_log',
        'fields' => array(
            'prompt' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'prompt_original' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'response' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
        ),
    );

    public static function existsPrompt($prompt)
    {
        $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'chatgpt_log` WHERE `prompt` = "' . pSQL($prompt) . '" and `date_add` > DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        return (bool)Db::getInstance()->getValue($sql);
    }

    public static function getResponse($prompt)
    {
        $sql = 'SELECT `response` FROM `' . _DB_PREFIX_ . 'chatgpt_log` WHERE `prompt` = "' . pSQL($prompt) . '" ORDER BY `date_add`';
        return Db::getInstance()->getValue($sql);
    }
} 
