<?php  

class ChangePasswordController extends MainController 
{
    private LoginChangePasswordMandatoryModel $_model;
	private object $_config;
	private string $_captcha;
	private object $_lang;
	private array $_session;
	private $_passwordOld;
	private $_passwordNew;
	private $_passwordConfirm;
	private $_headers;

	public function __construct($config)
	{		
		parent::__construct($config, []);
		$this->_config = $config;
		$this->_captcha = '';
		$loader = new LoaderResources(['user_lang' => $this->_config->app->lang, 'user_type' => $this->_config->app->ProfileDefault]);
		$this->_lang = $loader->GetLang($config->app->lang);
		$this->_session = [
			'app' => $this->_config->app->name, 
			'lang' => $this->_config->app->lang, 
			'type' => $this->_config->app->ProfileDefault
		];
		$this->_model = new LoginChangePasswordMandatoryModel($config);
	}

	#region public methods
	
    // Cambio de password forzado, para ingresos de primera vez
	public function Form()
	{
		$this->response->VerifyMethod("GET");
		
		$meta = new LoginChangePasswordMandatoryMetadata();

		return $meta->Login();
	}

	// Formulario para cambio de contraseña, recuperación por olvido de clave.
	public function UrlToForm()
	{
		$this->response->VerifyMethod("GET");

		$response = $this->_model->GetUserLastAccess(Util::VarGET('token'));

		if(!isset($response->UsrId) || empty($response->UsrId))
			throw new Exception($this->_lang->Login->invalidUrl, 404);

		// Validaciones: Tiempo transcurrido
		$lastAccess = new DateTime($response->UsrLastAccess);
		$lastAccess->format('Y-m-d H:i:s');
		$lastAccess->modify('+30 minute');
		if(is_object($lastAccess)) $lastAccess = (array) $lastAccess;
		
		$nowDB = $this->_model->GetNowDB();

		if($nowDB > $lastAccess['date'])
			throw new Exception($this->_lang->Login->expiredTime, 404);

		
		$meta = new ChangePasswordMetadata();

		return $meta->Login();
	}

    public function ValidateAndUpdatePassword()
	{
		$body = json_decode(file_get_contents("php://input"));
		$this->_headers = getallheaders();

		$this->_passwordOld = isset($body->passwordOld) ? ScapeStr::Clean($body->passwordOld) : null;
		$this->_passwordNew = ScapeStr::Clean($body->passwordNew);
		$this->_passwordConfirm = ScapeStr::Clean($body->passwordConfirm);

		$UserId = $this->_headers['idUser'];

		$flag = is_null($this->_passwordOld) ? false : true;

		$this->ValidateInput($flag);
		$this->EncryptPasswords($flag);
		$this->ValidateModel($UserId, $flag);

		$response = $this->_model->ChangePassword($UserId, $this->_passwordNew, $this->_config->security->pwHistory);

		if(!$response) throw new Exception($this->_lang->Validate->DatabaseError, 500);

		$this->SendMail($UserId, $body->passwordNew);
			
		return [
			'statusCode' => 300,
			'statusText' => 'success',
			'text' => $this->_lang->Login->changePwSuccess,
			'title' => $this->_lang->Login->titlePopup,
			'buttonName' => $this->_lang->Button->BackToLogin
		];
	}

	#endregion

	#region private methods

	private function ValidateInput(bool $validatePwOld): void
	{
		if(empty($this->_passwordNew)) 
			throw new Exception($this->_lang->Validate->EmptyNewPw, 400);

		if(empty($this->_passwordConfirm)) 
			throw new Exception($this->_lang->Validate->EmptyConfirmPw, 400);

		if($validatePwOld && empty($this->_passwordOld)) 
			throw new Exception($this->_lang->Validate->EmptyOldPw, 400);

		if($this->_passwordNew != $this->_passwordConfirm) 
			throw new Exception($this->_lang->Validate->PasswordNotMatch, 400);

		if($validatePwOld && ($this->_passwordNew == $this->_passwordOld))
			throw new Exception($this->_lang->Validate->PwSame, 400);
		
		if(!$this->IsValidPass($this->_passwordNew)) 
			throw new Exception($this->_lang->Validate->PasswordBad, 400);
	}

	private function IsValidPass(string $password):bool
	{
		$min = 6;
		$max = 16;

		if(strlen($password) < $min || 
			strlen($password) > $max || 
			!preg_match('`[a-z]`',$password) || 
			!preg_match('`[A-Z]`',$password) || 
			!preg_match('`[0-9]`',$password)) return false;
		
		return true;
	}

	private function SendMail(string $user, string $confirmPass):void
	{
		//Variables correo
		$subject = $this->_lang->Login->emailSubject;
		$typeContent = "text/html";
		$mailFrom = $this->_config->mail->from;
		$app = $this->_config->app->name;
		//$url = $this->config->url->app.'/Login/FormForgotPassword/token='.$id;

		//Mensaje
		$message = "\$message = <<< HTML\n";
		$message .= file_get_contents("MailTemplates/Es/PasswordChanged.html");
		$message .= "\nHTML;\n";
		eval($message);

		// Envío de mensaje
		// $mail = new MailSendgrid($this->config);
		// $mail->SetMailTo($dataUser->UseEmail)
		// 	->SetSubject($subject)
		// 	->SetMessage($message)
		// 	->SetTypeContent($typeContent)
		// 	->Send();

		file_put_contents("C:\\xampp\\htdocs\\Desarrollo\\mailChanged.html", $message);
	}

	private function EncryptPasswords(bool $encryptPwOld):void
	{
		$this->_passwordNew = Util::Encrypt($this->_config->security->tokenPw.$this->_passwordNew);
		$this->_passwordConfirm = Util::Encrypt($this->_passwordConfirm);
		if($encryptPwOld)
			$this->_passwordOld = Util::Encrypt($this->_config->security->tokenPw.$this->_passwordOld);
	}

	private function ValidateModel(string $UserId, bool $validatePwOld):void
	{
		// Contrasenia actual incorrecta
		if($validatePwOld)
		{
			$UseId = $this->_model->GetUserIdChangePw($UserId, $this->_passwordOld);
			if($UseId->result != $UserId) throw new Exception($this->_lang->Validate->PasswordIncorrect, 400);
		}
		
		// Password ya existe en el historico
		$passExists = $this->_model->GetConPssId($UserId, $this->_passwordNew);
		if(!empty($passExists->result) && $passExists->result > 0) throw new Exception($this->_lang->Validate->PasswordExists, 400);
	}

	#endregion
}