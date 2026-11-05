<?php

class LoginMetadata
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
            'pageTitle' => 'Login',
            'nameForm' => 'FormLogin',
            'iconForm' => $this->_icon->Login,
            'titleForm' => $this->_langResource->Login->TitleFormLogin,
            'msgForm' => $this->_langResource->Login->DescFormLogin,
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
                0 => [
                    'fieldName' => 'username',
                    'disabled' => '',
                    'options' => '',
                    'value' => ''
                    // 'metadata' => $this->_dictionary->username
                ],
                1 => [
                    'fieldName' => 'password',
                    'disabled' => '',
                    'options' => '',
                    'value' => ''
                    // 'metadata' => $this->_dictionary->password
                ]
            ],
            'hidden' => [
                ["id" => "urlProject", "value" => $this->_url->project],
                ["id" => "urlFront", "value" => $this->_url->project.'/Login/LoadForm/'],
                ["id" => "urlRedirect", "value" => $this->_url->project.'/Login/ChangePasswordMandatory/'],
                ["id" => "urlChangePwd", "value" => $this->_url->project.'/Login/ChangePassword/'],
                ["id" => "urlActionForm", "value" => $this->_url->apiLogin.'/Login/Validate/']
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
                'mode' => 'CancelLogin',
                'type' => 'button',
                'name' => 'btn-cancel',
                'classname' => 'btn btn-link',
                'url' => $this->_url->project.'/Login/ForgetPassword/',
                'icon' => $this->_icon->Padlock,
                'text' => $this->_langResource->Login->BtnLoginCancel
            ],
            2 => [
                'mode' => 'Reset',
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