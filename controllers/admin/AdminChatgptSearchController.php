<?php
class AdminChatgptSearchController extends ModuleAdminController
{
    public $tabClassName;
    public function __construct()
    {
        $this->bootstrap = true;
        $this->tabClassName = 'AdminChatgptSearch';
        parent::__construct();
    }

    public function initContent()
    {

        if (((bool)Tools::isSubmit('submitform')) == true) {
            $this->formProcess();
        }
       
        // render search.tpl        
        $this->content = $this->context->smarty->fetch($this->module->getLocalPath().'views/templates/admin/search.tpl');

        parent::initContent();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        // load css and js
        $this->addCSS($this->module->getLocalPath().'views/css/admin/chatgpt_search.css');
        $this->addJS($this->module->getLocalPath().'views/js/admin/chatgpt_search.js');
        $url_ajax = $this->context->link->getAdminLink('AdminChatgptSearch');
        Media::addJsDef(['chatgpt_search_url_ajax' => $url_ajax]);
    }    

    //ajax search
    public function ajaxProcessChatgptSearch()
    {
        $prompt = Tools::getValue('prompt');
        $simple_data = Tools::getValue('simple_data');
        $success = false;
        $output = '';  

        if ($prompt) {

            $prompt_final = "Devuelve únicamente el texto de una consulta MySQL a realizar en una base de datos de PrestaShop para obtener la siguiente información: ".$prompt;
            $prompt_final .= " . No incluyas ```sql al principio ni al final de la consulta. ";
            if($simple_data) {
                $prompt_final .= " Devuelve únicamente los datos más relevantes.";
            }

            $answer = '';

            if(ChatGPTLog::existsPrompt($prompt_final)) {                
                $answer = ChatGPTLog::getResponse($prompt_final);
                if( Configuration::get('CHATGPT_DEBUG', '') == 1) {
                    $output .= "<p>Ya se ha realizado una consulta similar recientemente. Se recupera la del Log para no hacer otra petición a chatGPT.</p>";
                }
            }else{
                
                // Conexión a API de chatgpt
                $curl = curl_init();
        
                $key = Configuration::get('CHATGPT_API_KEY');
                $data = array(
                    "model" => "gpt-4o",
                    "messages" => array(
                        array(
                            "role" => "user",
                            "content" => $prompt_final
                        )
                    )
                );

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Bearer $key",
                        "Content-Type: application/json"
                    ),
                ));
        
                $response = curl_exec($curl);
                $err = curl_error($curl);
        
                curl_close($curl);
        
                if ($err) {
                    $output .= "cURL Error #:" . $err;
                } else {
                    $response_data = json_decode($response, true);
                    $answer = $response_data['choices'][0]['message']['content'];                   

                    $chatgpt_log = new ChatGPTLog();
                    $chatgpt_log->prompt = $prompt_final;
                    $chatgpt_log->prompt_original = $prompt;
                    $chatgpt_log->response = $answer;
                    $chatgpt_log->date_add = date('Y-m-d H:i:s');
                    $chatgpt_log->add();
                }
            }
                
            if($answer) {

                if( Configuration::get('CHATGPT_DEBUG', '') == 1) {                   
                    $output .= "<h2>Pregunta realizada a ChatGPT:</h2>";
                    $output .= "<textarea readonly>".$prompt_final."</textarea>";
                    $output .= "<h2>Respuesta de ChatGPT:</h2>";
                    $output .= "<textarea readonly>$answer</textarea>";
                }

                $result = Db::getInstance()->executeS($answer);

                if ($result) {
                    $success = true;
                    $output .= "<div class='response'>";
                    $output .= "<h2>Resultados de la base de datos:</h2>";
                    $output .= "<div class='table-container'>";
                    $output .= "<table><thead>";
                    $output .= "<tr>";
                    // Obtener y mostrar los nombres de las columnas
                    $headers = array_keys($result[0]);
                    foreach ($headers as $header) {
                        $output .= "<th scope='col'>" . htmlspecialchars($header) . "</th>";
                    }
                    $output .= "</tr>";
                    $output .= "</thead>";
                    $output .= "<tbody>";
                    // Mostrar los datos de las filas
                    foreach ($result as $row) {
                        $output .= "<tr>";
                        $i = 0;
                        foreach ($row as $key => $column) {
                            $output .= "<td ";
                            if($i == 0) {
                                $output .= "scope='row'";
                            }
                            $output .= " data-label='".$key."'>" . htmlspecialchars($column) . "</td>";
                            $i++;
                        }
                        $output .= "</tr>";
                    }                    
                    $output .= "</tbody>";
                    $output .= "</table>";
                    $output .= "</div>";
                    $output .= "</div>";
                } else {
                    $output .= "<div class='response'>";
                    $output .= "<h2>No se encontraron resultados en la base de datos.</h2>";
                    $output .= "</div>";
                }
            }
        }

        die(json_encode(array('success' => $success, 'output' => $output)));
    }
    
}
