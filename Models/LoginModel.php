<?php

class LoginModel
{
    private HttpDatabase $_headersDB;
    private object $_config;

    public function __construct(object $config)
    {
        $this->_config = $config;
    }

    // Obtener datos usuario
	public function getDataUserLogged(string $username, string $password):mixed
	{	
		$params = [
			':idUser' => $username,
			':passwordHash'=> $password
		];
		$this->_headersDB = new HttpDatabase();
		$this->_headersDB->SetModel('UsersModel')
						->SetResoruceModel('GetDataUser')
						->SetParams($params);	
		return $this->_headersDB->Get($this->_config);
	}

    // verifica existencia de usuario login
	public function validateIfUserExists(string $username):mixed
	{
		$params = [
			':idUser' => $username
		];
		$this->_headersDB = new HttpDatabase();
		$this->_headersDB->SetModel('UsersModel')
						->SetResoruceModel('GetExistsUser')
						->SetParams($params);
		return $this->_headersDB->Get($this->_config);
	}

    // Cambio forzado de password por vencimiento retorna fecha
	public function GetLastDateChangePass(string $username)
	{		
		$params = [
			':idUser' => $username
		];
        $this->_headersDB = new HttpDatabase();
        $this->_headersDB->SetModel('ControlPasswordsModel')
                        ->SetResoruceModel('GetLastDatePwCreate')
                        ->SetParams($params);

        return $this->_headersDB->Get($this->_config);
	}

    // Cambio forzado de password para acceso de primera vez, retorna numero
    public function GetUserChangePwRequired(string $username)
    {
		$params = [
			':idUser' => $username
		];
		$this->_headersDB = new HttpDatabase();
		$this->_headersDB->SetModel('ControlPasswordsModel')
						->SetResoruceModel('GetChangePwRequired')
						->SetParams($params);

		return $this->_headersDB->Get($this->_config);
    }

    public function GetFailedAttempts(string $username)
	{
		$params = [
			':idUser'=> $username
		];
		$this->_headersDB = new HttpDatabase();
		$this->_headersDB->SetModel('ControlPasswordsModel')
						->SetResoruceModel('GetFailedAttempts')
						->SetParams($params);
		
	    return $this->_headersDB->Get($this->_config);
	}

	public function GetSessionUserHash(string $hashUser)
	{
		$params = [
			':hashUser'=> $hashUser
		];
        $this->_headersDB = new HttpDatabase();
        $this->_headersDB->SetModel('UsersModel')
                        ->SetResoruceModel('GetUser')
                        ->SetParams($params);
        return $this->_headersDB->Get($this->_config);
	}

	public function GetTokenRegisteredDB(string $user)
    {
		$params = [
			':UseId' => $user
		];
        $this->_headersDB = new HttpDatabase();
		$this->_headersDB->SetModel('UsersModel')
						->SetResoruceModel('GetTokenRegisteredDB')
						->SetParams($params);

	    return $this->_headersDB->Get($this->_config);
    }


    public function RegisterSessionLogout(string $idUser, string $lastAccess)
	{
		$body = [
			':IdUser' => $idUser, 
			':IdProblem' => 'EA',
			':lastAccess' => $lastAccess
		];

        $this->_headersDB = new HttpDatabase();
        $this->_headersDB->SetModel('LoginModel')
                        ->SetResoruceModel('RegisterLogout');

        $res = $this->_headersDB->Post($body, $this->_config);

		return $res->result->InsertedRecords;
	}

	public function RegisterLoginLog(string $username, string $problem, int $failedAttempts, string $ip = ''):void
	{
		$body = [
			':user' => $username, 
			':problem' => $problem, 
			':attemp' => is_null($failedAttempts) ? 0 : $failedAttempts,
			':lastIp' => !empty($ip) ? $ip : Util::GetIP()
		];

		$this->_headersDB = new HttpDatabase();
		$this->_headersDB
			->SetModel('LoginModel')
			->SetResoruceModel('RegisterAccessLogin');	

		$this->_headersDB->Post($body, $this->_config);
	}

    public function accessLogger(string $username):mixed
	{
		$body = [
			':attemp' => 0,
			':idUser' => $username
		];

		$this->_headersDB = new HttpDatabase();
		$this->_headersDB->SetModel('ControlPasswordsModel')
						->SetResoruceModel('RegisterAccess');

		return $this->_headersDB->Post($body, $this->_config);
	}

	public function VerifyInactivity(string $username)
	{	
		$body = [
			':userstatus' => 'I', 
			':idUser' => $username
		];
        $this->_headersDB = new HttpDatabase();
        $this->_headersDB->SetModel('UsersModel')
                        ->SetResoruceModel('SetBlockingUser')
                        ->SetParams([':idUser' => $username]);
        $this->_headersDB->Put($body, $this->_config);
	}

	// public function RegisterToken(string $username, string $token):mixed
    // {
	// 	$body = [
	// 		':userId' => $username,
	// 		':token' => $token
	// 	];
    //     // registrar token
	// 	$this->_headersDB = new HttpDatabase();
	// 	$this->_headersDB->SetModel('UsersModel')
	// 					->SetResoruceModel('RegisterUserToken');
		
	// 	return $this->_headersDB->Put($body, $this->_config);
    // }

    public function SetFailedAttempts(int $failedAttempts, string $username)
	{
		$body = [
			':failedAttempts' => $failedAttempts, 
			':idUser' => $username
		];

		$this->_headersDB = new HttpDatabase();
		$this->_headersDB->SetModel('ControlPasswordsModel')
						->SetResoruceModel('UpdateFailedAttempts');

		$this->_headersDB->Put($body, $this->_config);
	}

	public function LookingUserByFailedAttempts(string $username)
	{	
		$body = [
			':idUser' => $username,
			':userstatus' => 'B'
		];

        $this->_headersDB = new HttpDatabase();
        $this->_headersDB->SetModel('UsersModel')
                        ->SetResoruceModel('SetBlockingUser');        

		return $this->_headersDB->Put($body, $this->_config);	
	}

	public function GetLang()
    {
        $this->_headersDB = new HttpDatabase();
		$this->_headersDB->SetModel('LangModel')
						->SetResoruceModel('GetLang');
        return $this->_headersDB->Get($this->_config);
    }
}