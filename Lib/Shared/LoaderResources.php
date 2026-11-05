<?php

class LoaderResources
{
    private string $_dir;

    public function __construct()
    {
        $this->_dir = 'Config/';
    }
    
    public function GetConfig(bool $asArray=false)
    {
        $urlConfig = $this->_dir."Config.json";
        if(file_exists($urlConfig))
            return json_decode(file_get_contents($urlConfig), $asArray);
        
        throw new Exception('CONFIG FILE NOT FOUND',404);
    }

    public function GetUrlFile(bool $asArray=false)
    {
        $urlFile = $this->_dir."Url.json";
        if(file_exists($urlFile))
            return json_decode(file_get_contents($urlFile), $asArray);
        
        throw new Exception('URL FILE NOT FOUND',404);
    }

    public function GetMimeType(bool $asArray=false)
    {     
        $urlMime = $this->_dir."Mime.json";   
        if(file_exists($urlMime))
            return json_decode(file_get_contents($urlMime), $asArray);
        
        throw new Exception('MIME FILE NOT FOUND',404);
    }

    public function GetIcons(bool $asArray=false)
    {
        $urlIcons = $this->_dir."Icons.json";
        if(file_exists($urlIcons))
            return json_decode(file_get_contents($urlIcons), $asArray);
        
        throw new Exception('ICONS FILE NOT FOUND',404);
    }

    public function GetProfile(string $profile, bool $asArray=false)
    {
        $urlProfile = $this->_dir."Profiles/{$profile}.json";
        if(is_null($profile)) 
            throw new Exception('PROFILE USER UNDEFINED',404);

        if(file_exists($urlProfile)) 
            return json_decode(file_get_contents($urlProfile), $asArray);
               
        throw new Exception('PROFILE FILE NOT FOUND',404);
    }

    public function GetLang(string $lang, bool $asArray=false)
    {
        $urlLang = $this->_dir."Lang/{$lang}/Lang.json";
        if(is_null($lang) || $lang=='') 
            throw new Exception('LANG USER UNDEFINED',404);

        if(file_exists($urlLang))       
            return json_decode(file_get_contents($urlLang), $asArray);

        throw new Exception('LANG FILE NOT FOUND',404);
    }

    public function GetHttpCodes(string $lang, bool $asArray=false)
    {
        $urlCodes = $this->_dir."{$lang}/HttpCodes.json";
        if(is_null($lang)) 
            throw new Exception('LANG USER UNDEFINED',404);

        if(file_exists($urlCodes))
            return json_decode(file_get_contents($urlCodes), $asArray);
        
        throw new Exception('HTTPCODES FILE NOT FOUND',404);
    }

    public function GetDictionary(bool $asArray=false)
    {
        $urlDictionary = $this->_dir."Dictionary.json";
        if(file_exists($urlDictionary))
            return json_decode(file_get_contents($urlDictionary), $asArray);
        
        throw new Exception('DICTIONARY FILE NOT FOUND',404);
    }

    public function GetUserOptions(bool $asArray=false)
    {
        $urlOptions = $this->_dir."UserOptions.json";
        if(file_exists($urlOptions))
            return json_decode(file_get_contents($urlOptions), $asArray);
        
        throw new Exception('USER OPTIONS FILE NOT FOUND',404);
    }

    public function GetMenu(string $lang, bool $asArray=false)
    {
        $urlMenu1 = $this->_dir."Lang/{$lang}/MenuLevel1.json";
        $urlMenu2 = $this->_dir."Lang/{$lang}/MenuLevel2.json";
        $urlMenu3 = $this->_dir."Lang/{$lang}/MenuLevel3.json";

        if(is_null($lang)) 
            throw new Exception('LANG USER UNDEFINED',404);

        if(!file_exists($urlMenu1))
            throw new Exception('MENU LEVEL 1 FILE NOT FOUND',404);

        if(!file_exists($urlMenu2))
            throw new Exception('MENU LEVEL 2 FILE NOT FOUND',404);

        if(!file_exists($urlMenu3))
            throw new Exception('MENU LEVEL 3 FILE NOT FOUND',404);
        
        return [
            'MenuLevel1' => json_decode(file_get_contents($urlMenu1), $asArray),
            'MenuLevel2' => json_decode(file_get_contents($urlMenu2), $asArray),
            'MenuLevel3' => json_decode(file_get_contents($urlMenu3), $asArray)
        ];
    }
}