<?php
class AdminChatgptConfigurationController extends ModuleAdminController
{
    public $tabClassName;
    public function __construct()
    {
        $this->bootstrap = true;
        $this->tabClassName = 'AdminChatgptConfiguration';
        parent::__construct();
    }

    public function initContent()
    {

        if (((bool)Tools::isSubmit('submitform')) == true) {
            $this->formProcess();
        }
       
        $this->content .= $this->renderForm();

        parent::initContent();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();
    }

    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $this->module;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->currentIndex = self::$currentIndex;
        $helper->submit_action = 'submitform';
        $helper->token = Tools::getAdminTokenLite($this->tabClassName);

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,

        );

        return $helper->generateForm($this->getConfigForm());
    }

    public function getConfigForm()
    {     
        $url_cron = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/chatgpt/cron/cron.php?token='.md5(Configuration::get('CHATGPT_TOKEN', ''));
        $cron_settings = '<div class="alert alert-info">
            <p>Para configurar el cron, crea una tarea programada en tu servidor con este link:</p>
            <p><code>'.$url_cron.'</code></p>
            <p><a href="'.$url_cron.'" target="_blank" class="btn btn-primary">Abrir en una nueva pestaña</a></p>
        </div>';      
        $form[] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Ajustes del chatGPT', [], 'Modules.Chatgpt.ConfigurationForm'),
                    'icon' => 'icon-cogs',
                ),
                'tabs' => array(                    
                    'general' => $this->trans("General", [], "Modules.Chatgpt.ConfigurationForm"),
                    'cron' => $this->trans("Cron", [], "Modules.Chatgpt.ConfigurationForm"),
				),
                'input' => array(  
                    array(
                        'type' => 'text',
                        'label' => $this->trans('API Key', [], 'Modules.Chatgpt.ConfigurationForm'),
                        'name' => 'CHATGPT_API_KEY',
                        'tab' => 'general',
                        'col' => 4,
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->trans('Debug', [], 'Modules.Chatgpt.ConfigurationForm'),
                        'name' => 'CHATGPT_DEBUG',
                        'tab' => 'general',
                        'desc' => $this->trans('Muestra las consultas realizadas a chatGPT y la respuesta obtenida en el proceso de búsqueda.', [], 'Modules.Chatgpt.ConfigurationForm'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                            ),
                        ),
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'html_data',
                        'tab' => 'cron',
                        'html_content' => $cron_settings,
                    ),
                ),                
                'submit' => array(
                    'title' => $this->trans('Guardar', [], "Modules.Chatgpt.SaveButton")
                ),
            ),
        );       

        return $form;
    }

    /**
     * Set values for the inputs.
     */
    public function getConfigFormValues()
    {
        $variables = array(
            'CHATGPT_API_KEY' => Configuration::get('CHATGPT_API_KEY', ''),
            'CHATGPT_DEBUG' => Configuration::get('CHATGPT_DEBUG', ''), 
            //'CHATGPT_ARRAY[]' => json_decode(Configuration::get('CHATGPT_ARRAY', '')),
        );
        return $variables;
    }
    public function formProcess()
    {
        if (Tools::getValue('submitform')) {
            foreach ($this->getConfigFormValues() as $key => $value) {
                if ((version_compare(_PS_VERSION_, '1.6', '>=') ? Tools::strpos($key, '[]') > 0 : strpos($key, '[]') > 0)) {
                    $key = Tools::str_replace_once('[]', '', $key);
                    // json encode the array
                    Configuration::updateValue($key, json_encode(Tools::getValue($key)));
                } else {
                    Configuration::updateValue($key, Tools::getValue($key), true);
                }
            }           
            
            //Con esto se saca el mensaje de confirmación
            if (!$this->errors) {
                $this->confirmations[] = $this->trans("Guardado correctamente", [], "Modules.Chatgpt.ConfigurationForm");
            }
        }
    }
    
}
