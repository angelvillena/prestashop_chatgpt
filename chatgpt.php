<?php

class chatgpt extends Module
{

    public $tabs = [
        [
            'name' => 'ChatGPT',
            'class_name' => 'AdminChatgptConfiguration',
            'parent_class_name' => 'CONFIGURE',
            'wording' => 'Configuration',
            'wording_domain' => 'Modules.Chatgpt.tab',
            'icon' => 'settings',
        ],
        [
            'name' => 'Buscar',
            'class_name' => 'AdminChatgptSearch',
            'parent_class_name' => 'AdminChatgptConfiguration',
            'wording' => 'Log',
            'wording_domain' => 'Modules.Chatgpt.tab',
        ],
        [
            'name' => 'Log',
            'class_name' => 'AdminChatgptLog',
            'parent_class_name' => 'AdminChatgptConfiguration',
            'wording' => 'Log',
            'wording_domain' => 'Modules.Chatgpt.tab',
        ],
    ];

    public function __construct()
    {
        $this->name = 'chatgpt';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Ángel Villena Fernández';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('ChatGPT en PrestaShop', array(), 'Modules.Chatgpt.Admin');
        $this->description = $this->trans('Facilita tareas mediante ChatGPT y realiza consultas e informes sobre tu tienda.', array(), 'Modules.Chatgpt.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.7.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');      
        // guardar configuración CHATGPT_TOKEN
        Configuration::updateValue('CHATGPT_TOKEN', uniqid(rand(), true));
        return parent::install() &&
            $this->registerHook('displayBackofficeHeader');  
    }

    public function uninstall()
    {
        //include(dirname(__FILE__).'/sql/uninstall.php');
        return parent::uninstall();
    }

    public function getContent()
    {    	
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminChatgptConfiguration'));
    }
   

    public function isUsingNewTranslationSystem()
    {
        return true;
    } 
    
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/chatgpt.js');
        $this->context->controller->addCSS($this->_path.'views/css/chatgpt.css');
    }

}