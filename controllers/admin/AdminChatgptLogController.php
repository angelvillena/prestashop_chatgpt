<?php
class AdminChatgptLogController extends ModuleAdminController
{
    public $tabClassName;

    public function __construct()
    {
        $this->table = 'chatgpt_log';
        $this->tabClassName = 'AdminChatgptLog';
        parent::__construct();
        $this->bootstrap = true;
        $this->identifier = 'id_chatgpt_log';
        $this->className = 'ChatGPTLog';

    }

    public function init()
    {
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Eliminar', [], 'Modules.Chatgpt.Admin'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('¿Está seguro de eliminar el log?', [], 'Modules.Chatgpt.Admin')
            ]
        ];
        parent::init();
        $this->initForm();
        $this->initList();
    }   

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();       
    }

    public function initForm()
    {         
        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('ChatGPT LOG', [], 'Modules.Chatgpt.Admin'),
                'icon' => 'icon-picture'
            ],
            'input' => [
                [
                    'label' => $this->trans('prompt', [], 'Modules.Chatgpt.Admin'),
                    'type' => 'textarea',
                    'name' => 'prompt',
                    'readonly' => true,
                ],
                [
                    'label' => $this->trans('prompt_original', [], 'Modules.Chatgpt.Admin'),
                    'type' => 'textarea',
                    'name' => 'prompt_original',
                    'readonly' => true,
                ],
                [
                    'label' => $this->trans('Respuesta', [], 'Modules.Chatgpt.Admin'),
                    'type' => 'textarea',
                    'name' => 'response',
                    'desc' =>  $this->trans('SQL final para obtener los datos.', [], 'Modules.Chatgpt.Admin'),
                ], 
            ],
            'submit' => [
                'title' => $this->trans('Guardar y salir', [], 'Modules.Chatgpt.Admin')
            ],
            'buttons' => [
                'save-and-stay' => array(
                    'title' => $this->trans('Guardar y quedarse'),
                    'name' => 'submitAdd'.$this->table.'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                )
            ]
        ];  
                   
    }    

    public function initList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->fields_list = [
            'id_chatgpt_log' => [
                'title' => $this->trans('ID', [], 'Modules.Chatgpt.Admin'),
                'align' => 'center',
                'width' => 'auto'
            ],
            'prompt_original' => [
                'title' => $this->trans('Prompt', [], 'Modules.Chatgpt.Admin'),
                'align' => 'center',
                'width' => 'auto'
            ], 
            'response' => [
                'title' => $this->trans('Respuesta', [], 'Modules.Chatgpt.Admin'),
                'align' => 'center',
                'width' => 'auto'
            ],
            'date_add' => [
                'title' => $this->trans('Fecha', [], 'Modules.Chatgpt.Admin'),
                'align' => 'center',
            ]
        ];
        $this->_defaultOrderBy = 'date_add';
        $this->_defaultOrderWay = 'desc';
    }

}
