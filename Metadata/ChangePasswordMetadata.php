<?php

class ChangePasswordMetadata
{
    private array $_headers;
    private $_config;
    private $_url;
    private $_icon;
    private $_langResource;
    private $_langFunct;
    private $_dictionary;

    public function __construct()
    {
        $loader = new LoaderResources();
        $this->_config = $loader->GetConfig();
		$this->_url = $loader->GetUrlFile();
        $this->_icon = $loader->GetIcons();
        $this->_langResource = $loader->GetLang($this->_config->app->lang);
        $this->_dictionary = $loader->GetDictionary();
    }

    public function Login()
	{
        $infoPage = [];
        $infoPage = $this->GetInfo();
        $infoPage['Fields'] = $this->LoadForm();
        $infoPage['Buttons'] = $this->Buttons();
        return $infoPage;
	}

    private function GetInfo()
    {
        return [
            'service' => 'ON',
            'pageTitle' => 'ChangePassword',
            'nameForm' => 'FormLogin',
            'iconForm' => $this->_icon->Key,
            'titleForm' => $this->_langResource->Login->TitleFormChangePass,
            'msgForm' => $this->_langResource->Login->DescFormChangePass,
            'appName' => $this->_config->app->name,
            'appLang' => $this->_config->app->lang,
            'recaptchaEnabled' => $this->_config->security->recaptchaEnabled,
            'recaptchaPublicKey' => $this->_config->security->recaptchaPublicKey,
            'recaptchaText' => $this->_langResource->Login->RecaptchaText
        ];
    }

    private function LoadForm()
    {
        return [
            'visible' => [
                [
                    'fieldDictionary' => 'passwordNew',
                    'disabled' => '',
                    'options' => '',
                    'value' => '',
                    'metadata' => $this->_dictionary->passwordNew
                ],
                [
                    'fieldDictionary' => 'passwordNewConfirm',
                    'disabled' => '',
                    'options' => '',
                    'value' => '',
                    'metadata' => $this->_dictionary->passwordNewConfirm
                ]
            ],
            'hidden' => [
                ["id" => "urlProject", "value" => $this->_url->project],
                ["id" => "urlFront", "value" => $this->_url->project.'/Login/LoadForm/'],
                ["id" => "urlRedirect", "value" => $this->_url->project.'/'],
                ["id" => "urlChangePwd", "value" => $this->_url->project.'/Login/ChangePassword/'],
                ["id" => "urlActionForm", "value" => $this->_url->apiLogin.'/ChangePassword/ValidateAndUpdatePassword/']
            ]
        ];      
    }

    private function Buttons()
    {
        return [
            'Submit' => [
                'type' => 'button',
                'name' => 'btn-submit',
                'classname' => 'btn btn-primary',
                'event' => [],
                'icon' => $this->_icon->Login,
                'text' => $this->_langResource->Login->BtnLoginSubmit
            ],
            'Cancel' => [
                'type' => 'button',
                'name' => 'btn-cancel',
                'classname' => 'btn btn-link',
                'url' => $this->_url->project.'/',
                'icon' => $this->_icon->Padlock,
                'text' => $this->_langResource->Login->BtnResetPwdCancel
            ]
        ];
    }
}