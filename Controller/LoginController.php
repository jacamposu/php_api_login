<?php 

class LoginController extends MainController 
{
	private LoginModel $_model;
	private object $_lang;
	protected object $config;
	private int $failedAttempts;
	private string $_username;
	private string $_password;
	private string $_ip;
	private string $_captcha;
	private $headers;
	
	public function __construct($config)
	{
		parent::__construct($config, []);
		$this->config = $config;
		$this->failedAttempts = 0;
		$this->_captcha = '';
		$loader = new LoaderResources();
		$this->_lang = $loader->GetLang($config->app->lang);
		$this->_model = new LoginModel($config);
	}
 
	#region public methods

	/**
	 * @method GET
	 */
	public function Form()
	{
		$this->response->VerifyMethod("GET");

		$meta = new LoginMetadata();

		return $meta->Login();
	}

	/**
	 * @method POST
	 */
	public function Validate()
    {
		$this->response->VerifyMethod("POST");
	
		$validated = $this->ValidateAccess();

		return (isset($validated['statusCode']) && $validated['statusCode'] != 200) ? $validated : ['token' => $validated];
    }

	/**
	 * @method POST
	 */
	public function GetTokenRegistered()
	{
		$this->response->VerifyMethod("POST");
		
		$body = json_decode(file_get_contents("php://input"));
		
		$token = $this->_model->GetTokenRegisteredDB($body->user);

		if(!isset($token->result)) throw new Exception($this->_lang->Login->TokenDoesNotExist, 400);

		return ['token' => $token->result];
	}
	
	/**
	 * @method POST
	 */
	public function Logout()
    {
		$this->response->VerifyMethod("POST");

		$body = json_decode(file_get_contents("php://input"));
	
		$user = $this->ValidateUserSession($body->user_id);

		$logout = $this->_model->RegisterSessionLogout($user->data->UsrId, $user->data->CpLastAccess);

		if(!$logout) throw new Exception($this->_lang->Login->FailedInsert, 400);
		
		return true;
    }

	#endregion

	#region private methods
	
	private function ValidateAccess()
	{
		$this->SetUserAndPassword();
		
		if($this->config->security->recaptchaEnabled) $this->validateRecaptcha();

		$response = $this->_model->SP_AccessControl($this->_username, $this->_password);

		if($response->statusCode == 200){

			if($response->result[0]->Success == 1){
				$data = $response->result[0];

				$data = [
					'UserIdEncrypt' => Util::Encrypt($data->UsrId),
					'UserSurname' => $data->UsrSurname,
					'UserFirtsName' => $data->UsrFirtsName,
					'UserTypeDecription' => $data->UtDescription,
					'UserLang' => $data->IdLang,
					'UserType' => $data->IdUserTypeUser,
					'AppStyle' => $data->IdAppStyle,
					'UseDatCellphone' => $data->UsrCellphone,
					'UseDatAddress' => $data->UsrAddress,
					'Localization' => $data->Localization,
					'CreationDate' => $data->CreationDate,
					'UseLastAccess' => $data->CpLastAccess,
					'UseEmail' => $data->UsrEmail,
					'UseId' => $this->_username
				];

				return $this->TokenSession($data);
			}else{
				$this->InterpretResponseSP($response->result[0]->ErrorMessage);
			}
		}
	}

	private function ResponseRedirect(string $message): array
	{
		return [
			'statusCode' => 300,
			'statusText' => 'warning',
			'text' => $this->_lang->Login->{$message},
			'title' => $this->_lang->Login->TitleFormChangePass,
			'buttonName' => $this->_lang->Button->ChangePassword,
			'user' => $this->_username
		];
	}

	private function BlockUserByFailedAttempts():void
	{
		if($this->failedAttempts > $this->config->login->numberAttempts)
		{
			$this->_model->LookingUserByFailedAttempts($this->_username);

			if($this->config->Logs->createLoginLogDB) 
				$this->_model->RegisterLoginLog($this->_username, 'UAB', $this->failedAttempts);
			
			throw new Exception($this->_lang->Login->AttemptsBlocking, 401);
		}
	}

	private function RegisterAccessFailed():mixed
	{
		$failedAttempts = $this->_model->GetFailedAttempts($this->_username);
		$failedAttempts = isset($failedAttempts->result) ? $failedAttempts->result : 0;
		$this->failedAttempts = is_null($failedAttempts) ? 1 : $failedAttempts + 1;
		
		$this->_model->SetFailedAttempts($this->failedAttempts, $this->_username);

		if($this->config->Logs->createLoginLogDB) 
				$this->_model->RegisterLoginLog($this->_username, 'WU', $this->failedAttempts);

		$this->BlockUserByFailedAttempts();

		throw new Exception($this->_lang->Login->WrongUser, 401);
	}

	private function GetDataUser():mixed
	{
		$user = $this->_model->getDataUserLogged($this->_username, $this->_password);
	
		if($user->statusCode != 200){
			$this->RegisterAccessFailed();
			throw new Exception($this->_lang->Login->WrongUser, 400);
		}
		
		if(!isset($user->result->data) || empty($user->result->data)){
			$this->RegisterAccessFailed();
			throw new Exception($this->_lang->Login->WrongUser, 400);
		}
			
		return $user->result->data;
	}

	private function VerifyInactivityUser($usrLastAccess):void
	{
		$daysDiff = Util::DateDiff(date("Y-m-d H:i:s"), $usrLastAccess);
		if($daysDiff > $this->config->login->daysInactivity)
		{
			$this->_model->VerifyInactivity($this->_username);
			if($this->config->Logs->createLoginLogDB) $this->_model->RegisterLoginLog($this->_username, 'UAB', $this->failedAttempts);
			throw new Exception($this->_lang->Login->InactivityBlocking, 401);
		}
	}

	private function RegisterAccess()
	{
		$registeredUser = $this->_model->accessLogger($this->_username);
		if($registeredUser->statusCode != 200) 
			throw new Exception($registeredUser->description, 400);

		$this->RegisterLog();
	}

	private function RegisterLog()
	{
		if($this->config->Logs->createLoginLogDB) 
			$this->_model->RegisterLoginLog($this->_username, 'EA', $this->failedAttempts, $this->_ip);
	}

	private function validateIfUserExists()
	{
		$result = $this->_model->validateIfUserExists($this->_username);

		if(isset($result->statusCode) && $result->statusCode != 200)
			throw new Exception("Error Database: ".$result->result->error,404);

		if(!isset($result->result)) 
			throw new Exception($this->_lang->Login->WrongUser,400);
	}

	private function ForceChangePassword()
	{
		if($this->config->login->forceChangePw)
		{	
			$datePass = $this->_model->GetLastDateChangePass($this->_username);
			$daysDiff = Util::DateDiff(date("Y-m-d H:i:s"), $datePass->result);
			if($daysDiff >= $this->config->login->daysForceChangePw) 
				return true;
		}
		return false;
	}

	private function ChangePwRequired()
	{
		if($this->config->login->forceChangePw)
		{
			$required = $this->_model->GetUserChangePwRequired($this->_username);
			if($required->result)
				return true;
		}
		return false;
	}

	private function TokenSession($data):string
	{
		$jwt = new JsonWebToken($this->config);
		$token = $jwt->GetToken($data);

		// $registeredToken = $this->_model->RegisterToken($this->_username, $token);
		// if($registeredToken->statusCode != 200) 
		// 	throw new Exception($registeredToken->description, $registeredToken->statusCode);

		return $token;
	}
	
	private function SetUserAndPassword()
	{
		$body = json_decode(file_get_contents("php://input"));

		if(!isset($body->username) || !isset($body->password) || empty($body->username) || empty($body->password))
			throw new Exception($this->_lang->Login->EmptyUserPass, 400);

		if(isset($body->captcha)) 
			$this->_captcha = $body->captcha;

		$this->_username = ScapeStr::Clean(($body->username));
		$this->_password = Util::Encrypt(ScapeStr::Clean(($this->config->security->tokenPw.$body->password)));
		$this->_ip = "123";//ScapeStr::Clean(($body->ip)) ?? Util::GetIP();
	}
	
	private function validateRecaptcha()
	{
		if(empty($this->_captcha)) throw new Exception($this->_lang->Login->CaptchaVerify, 400);
		
		$apiUrl = $this->config->url->captchaGoogle."?secret={$this->config->security->recaptchaSecretKey}&response={$this->_captcha}";
		$response = json_decode(file_get_contents($apiUrl), true);
		
		if(!$response['success']) throw new Exception($this->_lang->Login->CaptchaErrorMessage, 400);
	}

	// El usuario ha sido bloqueado por el sistema
	private function BlockUser()
	{
		if($this->config->Logs->createLoginLogDB) 
			$this->_model->RegisterLoginLog($this->_username, 'UAB', $this->failedAttempts);

		throw new Exception($this->_lang->Login->AttemptsBlocking, 401);
	}

	// El usuario ha superado el tiempo de inactividad maximo establecido.
	private function InactivateUser()
	{
		if($this->config->Logs->createLoginLogDB) 
			$this->_model->RegisterLoginLog($this->_username, 'UIB', $this->failedAttempts);

		throw new Exception($this->_lang->Login->InactivityBlocking, 401);
	}


	private function ValidateUserSession(string $userIdEncrypted)
	{
		if(!$this->config->Logs->createLoginLogDB) return null;

        $userId = $this->_model->GetSessionUserHash($userIdEncrypted);

        if(empty($userId->result)) throw new Exception($this->_lang->Login->UserNotExists, 403);

		return $userId->result;
	}

	private function InterpretResponseSP($response){
		// echo $response;
		switch ($response) {
			case 'ERROR_WU':
				throw new Exception($this->_lang->Login->WrongUser,400);

			case 'ERROR_CPR':
				return $this->ResponseRedirect('ChangePwdRequired');

			case 'ERROR_FCP':
				return $this->ResponseRedirect('ForceChangePass');

			case 'ERROR_I':
				throw new Exception($this->_lang->Login->InactivityBlocking, 401);

			case 'ERROR_UAB':
				throw new Exception($this->_lang->Login->AttemptsBlocking, 401);

			case 'ERROR_UIB':
				throw new Exception($this->_lang->Login->InactivityBlocking, 401);
			
			default:
				throw new Exception("ERROR NO IDENTIFIFCADO: ".$response, 401);
				break;
		}
	}

	#endregion
}