<?php
defined('BASEPATH') OR exit('No direct script access allowed');
global $data;
class Home extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library("Watson");
    }

	public function index() {
		$data['chat'] = $this->load->view('asistant/chat', NULL, TRUE);
		$this->load->view('asistant/index', $data);
	}
    public function asistente(){
        $natural_text = $this->input->post('user_message');
        $watson = new Watson();
        $watson->set_credentials('apikey', 'xtE1cj7lKJiWSJWfCSB9XAwXEL5vl3iZCoTXo3IZXv9P');
        $workspace_id = "74e19152-ad16-46bb-a8d2-8b9439b55f91";
        $data_array = $watson->send_watson_conv_request($natural_text, $workspace_id);
        header('Content-Type: application/json');
        $watson->set_context(json_encode($data_array["context"]));
        $this->output->set_output(json_encode($data_array));
    }
	public function asistente_v2(){
        //verificar si existe la session del usuario
        if(!isset($_SESSION['session_watson'])){
            $watson = new Watson();
            $watson->set_credentials('apikey','C2Z5xMoG23wN1acYD9TBl86cmDNI_mDHzFKuAVavy97j');
            $url = "https://gateway.watsonplatform.net/assistant/api/v2/assistants/c912fa5b-f0ae-4513-971e-2de74ef71b91/sessions?version=2019-02-28";
            $data_array = $watson->send_watson_conv_request_v2("", $url);
            //print_r($data_array);
            $_SESSION['session_watson']=$data_array["session_id"];
        }
        $natural_text = $this->input->post('user_message');
        $watson = new Watson();
        $watson->set_credentials('apikey','C2Z5xMoG23wN1acYD9TBl86cmDNI_mDHzFKuAVavy97j');
        $url = "https://gateway.watsonplatform.net/assistant/api/v2/assistants/c912fa5b-f0ae-4513-971e-2de74ef71b91/sessions/".$_SESSION['session_watson']."/message?version=2019-02-28";
        $data_array = $watson->send_watson_conv_request_v2($natural_text, $url);
        header('Content-Type: application/json');
        $this->output->set_output(json_encode($data_array));
    }
    
   
    
    
}