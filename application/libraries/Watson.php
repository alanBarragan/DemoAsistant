<?php

class Watson {

	private $Watson_Credentials_User = "";
	private $Watson_Credentials_Password = "";
	private $Current_Context = "";

	function __construct() {	
    	if (isset($_SESSION['context'])) {
    		$this->Current_Context = $_SESSION['context'];
    	}
    	else
    		$this->Current_Context = 'undefied';
    }

	public function set_credentials($usr,$pass) {
		$this->Watson_Credentials_User = $usr;
		$this->Watson_Credentials_Password = $pass;
	}

	public function set_context($context) {
		$this->Current_Context = $context;
		$_SESSION['context'] = $context;
	}

	public function send_watson_conv_request($text, $workspace) {
		$curl = curl_init();
		$context_data = json_decode($this->Current_Context);
		$post_args = array(
					'input' => array(
						'text' => $text
					),
					'context' => $context_data
		);
		$dataa = json_encode($post_args);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $dataa);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',                                                                               
			'Content-Length: ' . strlen($dataa))                                                                       
		);  
	    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($curl, CURLOPT_USERPWD, $this->Watson_Credentials_User.":".$this->Watson_Credentials_Password);
	    curl_setopt($curl, CURLOPT_URL, "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace."/message?version=2016-09-20");
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    $result = curl_exec($curl);
	    if (curl_errno($curl)) {
		    echo 'Error:' . curl_error($curl);
		}
	    curl_close($curl);
	    $decoded = json_decode($result, true);
     return $decoded;
	  }
	  public function send_watson_conv_request_v2($text,$url) {
		$curl = curl_init();
		$context_data = json_decode($this->Current_Context);
		$post_args = array(
				'input' => array(
						'text' => $text,
						'options' => array(
							'return_context' => true
						)
					),
				'context' => $context_data
		);
		$dataa = json_encode($post_args);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $dataa);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',                                                                               
		   'Content-Length: ' . strlen($dataa))                                                                       
	   );  
	   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	   curl_setopt($curl, CURLOPT_USERPWD, $this->Watson_Credentials_User.":".$this->Watson_Credentials_Password);
	   curl_setopt($curl, CURLOPT_URL, $url);
	   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	   $result = curl_exec($curl);
	   if (curl_errno($curl)) {
		   echo 'Error:' . curl_error($curl);
	   }
	   curl_close($curl);
	   $decoded = json_decode($result, true);
	return $decoded;
	}

}