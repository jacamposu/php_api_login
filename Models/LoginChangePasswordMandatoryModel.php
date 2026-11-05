<?php

class LoginChangePasswordMandatoryModel
{
    private HttpDatabase $_headersDB;
    private object $_config;
    private array $_session;

    public function __construct(object $config)
    {
        $this->_config = $config;
    }

    public function GetUserIdChangePw(string $UserId, string $passwordOld)
    {
		$params = [
			':idUser' => $UserId,
			':passwordHash'=> $passwordOld
		];
		$this->_headersDB = new HttpDatabase();
		$this->_headersDB->SetModel('UsersModel')
						->SetResoruceModel('GetUserIdChangePw')
						->SetParams($params);
		return $this->_headersDB->Get($this->_config);
    }

    public function GetConPssId(string $UserId, string $passwordNew)
    {
        $params = [
            ':idUser'=> $UserId, 
            ':CpPassword' => $passwordNew
        ];
        $this->_headersDB = new HttpDatabase();
		$this->_headersDB->SetModel('ControlPasswordsModel')
				->SetResoruceModel('GetConPssId')
				->SetParams($params);
        return $this->_headersDB->Get($this->_config);
    }

    public function ChangePassword(string $UserId, string $passwordNew, int  $pwHistoryCount)
	{
		/* Llamado del SP Usp_ControlPasswords_SetPassword */
		$this->_headersDB = new HttpDatabase();

		$this->_headersDB->SetModel('Usp_ControlPasswords_SetPassword')
						->SetResoruceModel('SP')
						->SetParams([
							':userId' => $UserId,
							':passwordNew' => $passwordNew,
							':pwHistoryCount' => $pwHistoryCount
						]);
		return $this->_headersDB->SP($this->_config);
	}

    public function GetUserLastAccess($id):mixed
	{
        $params = [
            ':idUser'=> $id
        ];
		$this->_headersDB = new HttpDatabase();
		
		$this->_headersDB->SetModel('UsersModel')
						->SetResoruceModel('GetUserLastAccess')
						->SetParams($params);
		$response = $this->_headersDB->Get($this->_config);

		return $response->result->data;
	}

    public function GetNowDB():mixed
	{
		$this->_headersDB = new HttpDatabase();
		
		$this->_headersDB->SetModel('UsersModel')
						->SetResoruceModel('GetNowDB')
						->SetParams([]);
		$response = $this->_headersDB->Get($this->_config);

		return $response->result;
	}
}