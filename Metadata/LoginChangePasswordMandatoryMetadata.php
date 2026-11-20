<?php

class LoginChangePasswordMandatoryMetadata
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
        $infoPage['fields'] = $this->LoadForm();
        $infoPage['buttons'] = $this->Buttons();
        return $infoPage;
	}

    private function GetInfo()
    {
        return [
            'service' => 'ON',
            'time' => time(),
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
                    'fieldName' => 'passwordOld',
                    'disabled' => '',
                    'options' => '',
                    'value' => ''
                ],
                [
                    'fieldName' => 'passwordNew',
                    'disabled' => '',
                    'options' => '',
                    'value' => ''
                ],
                [
                    'fieldName' => 'passwordConfirm',
                    'disabled' => '',
                    'options' => '',
                    'value' => ''
                ]
            ],
            'hidden' => [
                ["id" => "urlFront", "value" => '/Login/LoadForm/'],
                ["id" => "urlRedirect", "value" => '/'],
                ["id" => "urlChangePwd", "value" => '/Login/ChangePassword/'],
                ["id" => "urlActionForm", "value" => '/ChangePassword/ValidateAndUpdatePassword/']
            ]
        ];      
    }

    private function Buttons()
    {
        return [
            0 => [
                'mode' => 'SubmitLogin',
                'type' => 'button',
                'name' => 'btn-submit',
                'classname' => 'btn btn-primary',
                'event' => [],
                'icon' => $this->_icon->Login,
                'text' => $this->_langResource->Login->BtnLoginSubmit
            ],
            1 => [
                'mode' => 'RedirectLogin',
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