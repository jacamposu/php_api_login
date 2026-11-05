<?php
/**
* ----------------------------------------------------------
* Clase AppUtil
* @return M�todos generales para usar en la aplicaci�n
* @version 1
* @package Lib
* @author Jose Antonio Campos Urquijo
* @copyright  Copyright (c)
* ----------------------------------------------------------
*/


class Util
{
    /**
    * @return string, encriptaci�n de cadenas, (contrase�as).
    */
    public static function Encrypt($tokenString)
    {
        return hash("sha512", $tokenString);
    }



    

    /**
    * @return void, verifica si el perfil puede acceder al recurso.
    */
    public static function VerifyAccessProfile($profiles = ['Ad'])
    {
        $recordEdit = self::VarGet('recordEdit');
        $idPage = Token::Get($_SESSION['appSessionUseId'], null);
        if(in_array($recordEdit, $profiles)  && $recordEdit != $_SESSION['appSessionUseUserType'])
        {
            die(appAlert(41, 'AppProfilingUser.php?idPage='.$idPage, 'No puede acceder a la perfilaci�n de este usuario.'.__FILE__.__LINE__));
        }
    }


    /**
    * @return string, cadena aleatoria con length determinado.
    */
    public static function RandomString($length = 1) 
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
    }


    /**
    * @return string, convierte en variable un indice de $_GET[].
    */
    public static function VarGET($VarName) 
    {
        global $_GET;
        
        if (isset($_GET[$VarName]) && !empty($_GET[$VarName])) 
        {
            $VarName = self::Url($_GET[$VarName]);
        }
        else
        {
            $VarName = null;
        }
        return $VarName;
    }


    public static function Url($string) {

        $chars = ['%'  ,'^'  ,'$'  , '/' ,':'  ,'?'  ,'@'  , '=' , '}' , '{' , '&' ,   '<',   '>',  '"',  '\'',  '#',   ' '];
        $urlCodes = ['%25','%5E','%24','%2F','%3A','%3F','%40','%3D','%7D','%7B','%26', '%3C', '%3E', '%22', '%27','%23', '%20'];
        $string = str_replace($urlCodes, $chars, $string);
        return $string;

    }


    /**
    * @return string, convierte en variable un indice de $_POST[].
    */
    public static function VarPOST($VarName) 
    {    
        global $_POST;
        if (isset($_POST[$VarName])) {
            $VarName = ScapeStr::Url($_POST[$VarName]);
        }else{
            $VarName = null;
        }
        return $VarName;
    }


    /**
    * @return int, diferencia en dias entre dos fechas.
    */
    public static function DateDiff($startDate, $endDate) 
    {
        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        $diff = $startDate->diff($endDate);
        return $diff->days;
    }


    


    /**
    * @return string, Redireccionamiento ($url).
    */
    public static function Redirect($url) 
    {
        // global $config;
        // $dirname = dirname($_SERVER['PHP_SELF']);
        // die(header("Location: {$config->url->protocolHost}{$dirname}{$url}"));
        die(header("Location: ".$url));
    }

    public static function RedirectJs($url)
    {
        die('<script>window.location.href="'.$url.'"</script>');
    }

    public static function RedirectJsAlert($url, $msg)
    {
        die('<script>
            alert("'.$msg.'");
            window.location.href="'.$url.'";
            </script>');
    }


    /**
    * @return string, Convierte a formato moneda un string.
    */
    public static function CurrencyFormat($format, $number)
    {
        $decSeparator = null;
        $intSeparator = null;
        $lenDecimals = 0;

        // Obtiene el simbolo
        if(count(explode(' ', $format)) > 1){
            list($symbol, $value) = explode(' ', $format);
            $symbol = $symbol.' '; 
        }else{
            $value = $format;
            $symbol = '';
        }

        if(stristr($value, "#,###.##")){
            $decSeparator = '.';
            $intSeparator = ',';
            list($integer, $decimals) = explode('.', $value);
            $lenDecimals = strlen($decimals);
        }elseif(stristr($value, "#.###,##")){
            $decSeparator = ',';
            $intSeparator = '.';
            list($integer, $decimals) = explode(',', $value);
            $lenDecimals = strlen($decimals);
        }

        if($lenDecimals==0){
            if(stristr($value, ",")){
                $intSeparator = ',';
            }else{
                $intSeparator = '.';
            }
        }

        if(!empty($number)){
            return $symbol.number_format($number, $lenDecimals, $decSeparator, $intSeparator);
        }
    }


    /**
    * @return string, B�squeda recursiva de directorios.
    */
    public static function FindDirectories($recordEdit)
    {
        $directories = AppQuery::Column("SELECT DirId from directories where DirParentDirectory = $recordEdit", 'DirId');
        $subDirectories = [];
        if(count($directories)>0){
            foreach ($directories as $key => $value) {
                $subDirectories = self::FindDirectories($value);
                if(count($subDirectories)>0){
                    foreach ($subDirectories as $value) {
                        $directories[] = $value;
                    }
                }
            }
        }
        rsort($directories);
        return $directories;
    }


    /**
    * @return string, B�squeda recursiva de archivos.
    */
    public static function FindFiles($recordEdit, $directories)
    {
        $files = AppQuery::Column("SELECT FilId from files where FilDirectory = $recordEdit", 'FilId');
        foreach ($directories as $value) {
            $subFiles = AppQuery::Column("SELECT FilId from files where FilDirectory = $value", 'FilId');
            foreach ($subFiles as $value) {
                $files[] = $value;
            }
        }
        rsort($files);
        return $files;
    }



    // Funcion para obtener la ip del cliente
    public static function GetIP()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }



    // Retorna el browser usado por el cliente
    public static function GetBrowser( $useragent )
    {
        if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)) {
            $browser_version=$matched[1];
            $browser = 'Internet Explorer';
        } elseif (preg_match( '|Trident/([0-9].[0-9]{1,2})|',$useragent,$matched)) {
            $browser_version=$matched[1];
            $browser = 'Internet Explorer';
        } elseif (strpos($useragent, 'Edg')) {
            $browser_version='';
            $browser = 'Microsoft Edge';
        } elseif (strpos($useragent, 'Opera') || strpos($useragent, 'OPR')){
                $browser_version='';
                $browser = 'Opera';
        } elseif (preg_match( '|Opera Mini/([0-9].[0-9]{1,2})|',$useragent,$matched)) {
            $browser_version=$matched[1];
            $browser = 'Opera Mini';
        } elseif(preg_match('|Firefox/([0-9\.]+)|',$useragent,$matched)) {
            $browser_version=$matched[1];
            $browser = 'Mozilla Firefox';
        } elseif(preg_match('|Chrome/([0-9\.]+)|',$useragent,$matched)) {
            $browser_version=$matched[1];
            $browser = 'Google Chrome';
        } elseif(preg_match('|Safari/([0-9\.]+)|',$useragent,$matched)) {
            $browser_version=$matched[1];
            $browser = 'Safari';
        } else {
            // browser not recognized!
            $browser_version = 0;
            $browser= 'Desconocido';
        }
        return $browser." - ".$browser_version;
    }



    // Retorna el navegador del cliente
    public static function SetBrowser($user_agent)
    {
        if(strpos($user_agent, 'MSIE') !== FALSE)
            return 'Internet explorer';
        elseif(strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
            return 'Microsoft Edge';
        elseif(strpos($user_agent, 'Trident') !== FALSE) //IE 11
            return 'Internet explorer';
        elseif(strpos($user_agent, 'Opera Mini') !== FALSE)
            return "Opera Mini";
        elseif(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
            return "Opera";
        elseif(strpos($user_agent, 'Firefox') !== FALSE)
            return 'Mozilla Firefox';
        elseif(strpos($user_agent, 'Chrome') !== FALSE)
            return 'Google Chrome';
        elseif(strpos($user_agent, 'Safari') !== FALSE)
            return "Safari";
        else
            return 'No detectado';
    }


    public static function DeleteCookies() 
    {
        global $config;
        if ((
            !empty($_SERVER['HTTPS']) && 
            $_SERVER['HTTPS'] != 'off') || 
            $config->url->protocol === 'https'
        ){
            setcookie($config->app->name, "", time()-1, '/', null, true, true);
        } else {
            setcookie($config->app->name, "", time()-1, '/', null, false, true);
        }
    }


    public static function DrawTemplate($content, $infoPage, $hideMenu=false)
    {
        if(is_array($infoPage)){
            $controller = new ControllerOff('body', $infoPage);
            return $controller->showPageWhite($content, $hideMenu);
        }else{
            appPrint('El par�metro $infoPage debe ser un array', 1);
        }
    }

    public static function Server()
    {
        if(isset($_SERVER['SCRIPT_URL'])){
            return $_SERVER['SCRIPT_URL'];
        }elseif(isset($_SERVER['HTTP_REFERER'])){
            return $_SERVER['HTTP_REFERER'];
        }else{
            return null;
        }
    }

    /** 
     * @return Retorna un array dentro de etiquetas <pre> para mejor visualizaci�n y corta el flujo de ejecuci�n del programa.
     */
    public static function Debug($data, bool $killProcess=false)
    {
        if(!is_array($data))
        {
            $data = [$data];
        }
        
        echo "<pre>"; print_r($data); echo "</pre>";

        if($killProcess)
        {
            die();
        }
    }
}


    










    
















    















    

    
    




// Retorna link
function appGetLink($field, $url, $keyField, $target=null, $eventJS=null, $desc=null){
    return "<a href=\"$url\" id=\"$field\" name=\"$keyField\" target=\"$target\" $eventJS />$desc</a>";
} 


// Retorna un template html dentro de un php
function appReturnTemplate($PathTemplate){
    return '<br><div class="text-center">'.file_get_contents($PathTemplate).'</div>';
}


// Retorna popup tipo alert para mostrar informacion o cortar una ejecuci�n del programa en un momento espec�fico.
function appAlert($code, $redirect, $text, $context='app', $time=null){
    require_once 'Views/Func/Popups.php';

    global $icon;
    global $alertCodes;
    global $langResource;
    global $config;

    $arrText = null;
    $count = 1;
    $time = $time ? $time : $config->login->waitLoginFailed;
    $typeMsg = 'text';

    $redirect = !is_null($redirect) ? "window.location = '$redirect'" : "window.history.back();";
    $redirect = $context==='login' ? "setTimeout(function(){alert('{$langResource['Login']['TimeOutLogin']}');$redirect;}, {$time});" : $redirect;    

    $type = $alertCodes[$code]['color'];
    $title = $alertCodes[$code]['title'];
    $msgBackToLogin = $langResource['Login']['MsgBackToLogin'];
   
    if($context=='login'){
        die($msgBackToLogin);
    }

    if(is_array($text)){
        $liError = '';
        foreach ($text as $key => $error) {
            $liError .= "<li>{$error}</li>";
        }
        $text = $liError;
        $typeMsg = 'html';
    }

    die($typeMsg);
}


// Retorna popup tipo confirm para mostrar informacion y pedir al usuario que tome una desici�n.
function appConfirm($msgTitle, $msgContent, $msgColor, $msgIcon, $actionYes, $actionNot, $redirect='', $post=''){
    require_once '../View/Templates/Popups.php';

    global $icon;
    
    if($redirect){
        if(empty($post)){
            $redirect = "window.location = '$redirect'";
        }else{
            $redirect = "window.location = '$redirect&datos=$post'";
        }
    }else{
        $redirect = "window.history.back();";
    }

    if(!function_exists("appHeaderPage")){
        require_once '../Lib/Funciones_Contenido.php';
    }

    $msgIcon = $icon[$msgIcon].' fa-2x';

    die($msgContent);
}




function appDrawDetailsSummary($result){
    global $icon;
    $drawTable = null;
    $idPage = AppToken::Get($_SESSION['appSessionUseId'], null);
    for($f=0; $f<count($result['data']); $f++){
        if($result['data'][$f]['WikId'] == $result['data'][$f]['WikParentTitle']){
            $title = $result['data'][$f]['WikTitle'];

            $drawTable .= '<details>';
            $drawTable .= '<summary class="appSummary">'.$title.'</summary>';
            $drawTable .= '<div class="container">';
        }
        for($i=0; $i<count($result['data']); $i++){
            if($result['data'][$f]['WikId'] == $result['data'][$i]['WikParentTitle']){
                $id = $result['data'][$i]['WikId'];
                $subtitle = $result['data'][$i]['WikSubtitle'];
                $idPage = APP_ENABLED_CSRF_TOKEN ? '&idPage='.$idPage : null;
                $ruta = "../App/AppWiki.php?action=showFormEdit&recordEdit={$id}{$idPage}";
                $edit = "<small><a href=\"{$ruta}\"><em class=\"{$icon['RecordEdit']}\"></em></a></small>";
                $content = ScapeStr::ChartsToHTML($result['data'][$i]['WikContent']);
                $drawTable .= '<details>';
                $drawTable .= "<summary class=\"appSummarySubItem\">$subtitle $edit</summary>";
                $drawTable .= '<div class="container">';
                $drawTable .= "<p class=\"app-content-summary\">$content</p>";
                $drawTable .= '</div>';
                $drawTable .= '</details>';
            }
        }
        $drawTable .= '</details>';
    }
    return $drawTable;
}


function getLocation($url){
    global $config;
    return $config->url->app.'/'.$url;
}


// Retorna un array en forma debug
function debug($array, $killProcess=false){
    echo "<pre><hr>";
    print_r($array);
    echo "<hr></pre>";
    if($killProcess){
        die();
    }
}

function ffl($function, $file, $line){
    /* 
ffl(__FUNCTION__, __FILE__, __LINE__)
    */
    return 'function: <strong>'.$function.'()</strong><br>file: <strong>'.$file.'</strong><br>line: <strong>'.$line.'</strong>';
}


// Retorna la ip del cliente
function getIP(){

    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;

}



// Retorna el browser usado por el cliente
function getBrowser( $useragent ){
    if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)) {
        $browser_version=$matched[1];
        $browser = 'Internet Explorer';
    } elseif (preg_match( '|Trident/([0-9].[0-9]{1,2})|',$useragent,$matched)) {
        $browser_version=$matched[1];
        $browser = 'Internet Explorer';
    } elseif (strpos($useragent, 'Edg')) {
        $browser_version='';
        $browser = 'Microsoft Edge';
    } elseif (strpos($useragent, 'Opera') || strpos($useragent, 'OPR')){
            $browser_version='';
            $browser = 'Opera';
    } elseif (preg_match( '|Opera Mini/([0-9].[0-9]{1,2})|',$useragent,$matched)) {
        $browser_version=$matched[1];
        $browser = 'Opera Mini';
    } elseif(preg_match('|Firefox/([0-9\.]+)|',$useragent,$matched)) {
        $browser_version=$matched[1];
        $browser = 'Mozilla Firefox';
    } elseif(preg_match('|Chrome/([0-9\.]+)|',$useragent,$matched)) {
        $browser_version=$matched[1];
        $browser = 'Google Chrome';
    } elseif(preg_match('|Safari/([0-9\.]+)|',$useragent,$matched)) {
        $browser_version=$matched[1];
        $browser = 'Safari';
    } else {
        // browser not recognized!
        $browser_version = 0;
        $browser= 'Desconocido';
    }
    return $browser." - ".$browser_version;

}


// Limpia un string para evitar inyecci�n SQL, quitar espacios en blanco, evitar javascript
function CleanString ($startString) {

    $stringClean = preg_replace(['/\s+/','/^\s|\s$/'], [' ', ''], $startString);
    $stringClean = trim($stringClean);
    $stringClean = EscSlashes($stringClean);
    $stringClean = htmlspecialchars($stringClean);
    $stringClean = str_ireplace("<script>", "", $stringClean);
    $stringClean = str_ireplace("</script>", "", $stringClean);
    $stringClean = str_ireplace("<script src>", "", $stringClean);
    $stringClean = str_ireplace("</script type=>", "", $stringClean);
    $stringClean = str_ireplace("SELECT * FROM", "", $stringClean);
    $stringClean = str_ireplace("DELETE FROM", "", $stringClean);
    $stringClean = str_ireplace("INSERT INTO", "", $stringClean);
    $stringClean = str_ireplace("SELECT COUNT(*) FROM", "", $stringClean);
    $stringClean = str_ireplace("DROP TABLE", "", $stringClean);
    $stringClean = str_ireplace("DROP DATABASE", "", $stringClean);
    $stringClean = str_ireplace("OR '1'='1'", "", $stringClean);
    $stringClean = str_ireplace("OR 1=1", "", $stringClean);
    $stringClean = str_ireplace("OR 1 = 1", "", $stringClean);
    $stringClean = str_ireplace('OR "1"="1"', "", $stringClean);
    $stringClean = str_ireplace('OR �1�=�1�', "", $stringClean);
    $stringClean = str_ireplace("is NULL; --", "", $stringClean);
    $stringClean = str_ireplace("IS NULL; --", "", $stringClean);
    $stringClean = str_ireplace("LIKE '", "", $stringClean);
    $stringClean = str_ireplace('LIKE "', "", $stringClean);
    $stringClean = str_ireplace("LIKE �", "", $stringClean);
    $stringClean = str_ireplace("OR 'a'='a'", "", $stringClean);
    $stringClean = str_ireplace('OR "a"="a"', "", $stringClean);
    $stringClean = str_ireplace('OR �a�=�a�', "", $stringClean);
    $stringClean = str_ireplace("OR �a�=�a�", "", $stringClean);
    $stringClean = str_ireplace("--", "", $stringClean);
    $stringClean = str_ireplace("^", "", $stringClean);
    $stringClean = str_ireplace("[", "", $stringClean);
    $stringClean = str_ireplace("]", "", $stringClean);
    $stringClean = str_ireplace("==", "", $stringClean);

    return $stringClean;

} // End method Clean


function EscSlashes($string, $mode=''){
    
    $chars = ['\\\\', "\\'", '\\', "'"];
    $scapeChars = ['\\', "'", '\\\\', "\\'"];
    $mode = trim(strtolower($mode));
    if ($mode == 'like') {
        array_push($chars, '%');
        array_push($scapeChars, '\\%');
        array_push($chars, '_');
        array_push($scapeChars, '\\_');
    }
    return str_replace($chars, $scapeChars, $string);

} // End method EscSlashes



function formatMoney($value){
    $value = number_format($value, 2, SPD, SPM);
    return SMONEY.$value;
}

?>