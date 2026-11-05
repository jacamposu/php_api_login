<?php

class LoginForgotPasswordModel
{
    private HttpDatabase $_headersDB;
    private object $_config;
    private array $_session;

    public function __construct(object $config)
    {
        $this->_config = $config;
        $this->_session = [
			'app' => $config->app->name, 
			'lang' => $config->app->lang, 
			'type' => $config->app->ProfileDefault
		];
    }

    public function GetIDTypes()
    {
        $this->_headersDB = new HttpDatabase($this->_config);
		$this->_headersDB->SetModel('IdentificationTypesModel')
						->SetResoruceModel('GetIDTypes');
        return $this->_headersDB->Get($this->_config);
    }

    public function CheckUserExists($body)
    {
        $params = [
            ':idtype'=> $body->LoginIdeType, 
            ':idnumber' => $body->LoginIdeNumber, 
            ':mail' => $body->LoginMail
        ];

        $this->_headersDB = new HttpDatabase($this->_config);
        $this->_headersDB->SetModel('UsersModel')
                        ->SetResoruceModel('CheckUserExists')
                        ->SetParams($params);;
        return $this->_headersDB->Get($this->_config);
    }

    public function GetDataUser(string $idUser):mixed
	{							
		$this->_headersDB = new HttpDatabase();

		$this->_headersDB->SetModel('UsersModel')
						->SetResoruceModel('GetDataUserFromHash')
						->SetParams([
							':idUser' => $idUser
						]);

		$response = $this->_headersDB->Get($this->_config);

		return $response->result->data;
	}

    public function UpdateLastAccess(string $idUser):void
	{	
        $body = [
            ':idUser' => $idUser
        ];

		$this->_headersDB = new HttpDatabase();
		$this->_headersDB->SetModel('UsersModel')
						->SetResoruceModel('UpdateLastAccess');
        $this->_headersDB->Put($body, $this->_config);
	}
}