<?php

class LoginForgotPasswordMetadata
{
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

    public function Login($arrayReturn)
	{
        $infoPage = [];
        $infoPage = $this->GetInfo();
        $infoPage['fields'] = $this->LoadForm($arrayReturn);
        $infoPage['buttons'] = $this->Buttons();
        return $infoPage;
	}

    private function GetInfo()
    {
        return [
            'service' => 'ON',
            'pageTitle' => 'Login',
            'nameForm' => 'ForgotPassword',
            'iconForm' => $this->_icon->Key,
            'titleForm' => $this->_langResource->Login->TitleFormResetPwd,
            'msgForm' => $this->_langResource->Login->DescFormResetPwd,
            'appName' => $this->_config->app->name,
            'appLang' => $this->_config->app->lang,
            'recaptchaEnabled' => $this->_config->security->recaptchaEnabled,
            'recaptchaPublicKey' => $this->_config->security->recaptchaPublicKey,
            'recaptchaText' => $this->_langResource->Login->RecaptchaText
        ];
    }

    private function LoadForm($arrayReturn)
    {
        return [
            'visible' => [
                0 => [
                    'options' => $arrayReturn,
                    "disabled" => "",
                    'value' => '',
                    "fieldName" => "LoginIdeType"
                    // 'metadata' => $this->_dictionary->LoginIdeType
                ],
                1 => [
                    'options' => '',
                    "disabled" => "",
                    'value' => '',
                    "fieldName" => "LoginIdeNumber"
                    // 'metadata' => $this->_dictionary->LoginIdeNumber
                ],
                2 => [
                    'options' => '',
                    "disabled" => "",
                    'value' => '',
                    "fieldName" => "LoginMail"
                    // 'metadata' => $this->_dictionary->LoginMail
                ]
            ],
            'hidden' => [
                ["id" => "urlFront", "value" => '/ForgotPassword/LoadForm/'],
                ["id" => "urlRedirect", "value" => '/'],
                ["id" => "urlChangePwd", "value" => '/ChangePassword/Form/'],
                ["id" => "urlActionForm", "value" => '/ForgetPassword/Validate/']
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
                'classname' => 'btn btn-primary btn-block',
                'event' => [],
                'icon' => $this->_icon->Check,
                'text' => $this->_langResource->Login->BtnVerify
            ],
            1 => [
                'mode' => 'RedirectLogin',
                'type' => 'button',
                'name' => 'btn-cancel',
                'classname' => 'btn btn-link btn-block mt-2',
                'url' => '/',
                'icon' => $this->_icon->Return,
                'text' => $this->_langResource->Login->BtnResetPwdCancel
            ],
            2 => [
                'mode' => 'ResetLogin',
                'type' => 'button',
                'name' => 'btn-reset',
                'classname' => 'btn btn-link text-info',
                'url' => "",
                'icon' => "fas fa-eraser",
                'text' => "Limpiar formulario"
            ]
        ];
    }
}