<?php
include('../../../config/config.inc.php');

$get_token = Tools::getValue('token');

 if (md5(Configuration::get('CHATGPT_TOKEN', '')) == $get_token) {
    // Tareas a realizar
    die('Proceso finalizado');
} else {
    die('Token incorrecto');
}