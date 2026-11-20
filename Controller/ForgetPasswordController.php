<?php 

class ForgetPasswordController extends MainController 
{
	private LoginForgotPasswordModel $_model;
	private object $_config;
	private string $_captcha;
	private object $_lang;
	private array $_session;

	public function __construct($config)
	{
		parent::__construct($config, []);
		$this->_config = $config;
		$this->_captcha = '';
		$loader = new LoaderResources();
		$this->_lang = $loader->GetLang($config->app->lang);
		$this->_session = [
			'app' => $config->app->name, 
			'lang' => $config->app->lang, 
			'type' => $config->app->ProfileDefault
		];
		$this->_model = new LoginForgotPasswordModel($config);
	}

	#region public methods

	// Formulario para recuperacion de contraseña (1er paso)
	public function Form()
	{
		$this->response->VerifyMethod("GET");

		$apiResult = $this->_model->GetIDTypes();
		
		$arrayReturn = [];
								
		if($apiResult->statusCode == 200)
		{
			array_push($arrayReturn, ['value' => '', 'text' => $this->_lang->Login->SelectIdeTypField]);
			
			foreach($apiResult->result->data as $key => $value)
			{
				array_push($arrayReturn, ['value' => $value->ItId, 'text' => $value->ItDescription]);
			}
		}

		$meta = new LoginForgotPasswordMetadata();
		
		return $meta->Login($arrayReturn);
	}

	// Verificacion de datos provenientes del formulario de recuperacion de password
	public function Validate()
	{
		$this->response->VerifyMethod("POST");

		$body = json_decode(file_get_contents("php://input"));
		
		$time = $body->time ?? null;
		if(time() - $time < 3){
			throw new Exception("Demasiado rápido para ser un humano, tómate tu tiempo!", 400);
		}

		$this->ValidateMandatoryData($body);

		// Validación recaptcha
		//$this->validateRecaptcha();

		$userExists = $this->_model->CheckUserExists($body);

		$this->ValidateQueryResult($userExists);
		$idUser = $userExists->result->data->UsrEncryptedId;
		
		$dataUser = $this->_model->GetDataUser($idUser);

		$this->SendMailToUser($dataUser, $idUser);

		$this->_model->UpdateLastAccess($dataUser->UsrId);

		return [
			'statusCode' => 300,
			'statusText' => 'success',
			'text' => str_replace('{{UsrEmail}}', $dataUser->UsrEmail, $this->_lang->Login->textSendMail),
			'title' => $this->_lang->Login->titlePopup,
			'buttonName' => $this->_lang->Button->BackToLogin
		];
	}

	#endregion

	#region private methods

	private function ValidateMandatoryData($body)
	{
		if(!isset($body->LoginIdeType) || empty($body->LoginIdeType))
			throw new Exception($this->_lang->Login->EmptyData, 400);

		if(!isset($body->LoginIdeNumber) || empty($body->LoginIdeNumber))
			throw new Exception($this->_lang->Login->EmptyData, 400);

		if(!isset($body->LoginMail) || empty($body->LoginMail))
			throw new Exception($this->_lang->Login->EmptyData, 400);
	}

	private function ValidateQueryResult($data)
	{
		if(!isset($data->result->data) || !$data->result->data->userExists || empty($data->result->data->userExists))
			throw new Exception($this->_lang->Login->WrongData, 400);
	}

	private function SendMailToUser($dataUser, $id):void
	{
		//Variables correo
		$subject = $this->_lang->Login->emailSubject;
		$typeContent = "text/html";
		$mailFrom = $this->_config->mail->from;
		$app = $this->_config->app->name;		
		$url = $this->_config->url->app_front.'Login/ChangePassword/token='.$id;

		//Mensaje
		$message = "\$message = <<< HTML\n";
		$message .= file_get_contents("MailTemplates/Es/RecoverPassMail.html");
		$message .= "\nHTML;\n";
		eval($message);

		// Envío de mensaje
		// $mail = new MailSendgrid($this->config);
		// $mail->SetMailTo($dataUser->UseEmail)
		// 	->SetSubject($subject)
		// 	->SetMessage($message)
		// 	->SetTypeContent($typeContent)
		// 	->Send();

		file_put_contents($this->_config->path->pathApp."mailRecoveryPass.html", $message);
	}

	#endregion
}
