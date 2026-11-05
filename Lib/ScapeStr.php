<?php 
/**
* ----------------------------------------------------------
* Clase AppScapeStr
* @return métodos de inicio y cierre de sesión
* @version 1
* @package Libraries
* @author Jose Antonio Campos Urquijo
* @copyright  Copyright (c)
* ----------------------------------------------------------
*/

// require_once 'InterfacesLib.php';
// implements InterfaceScapeStr

class ScapeStr{

	/**
    * @return string, remplazo de caracteres provenientes de url.
    */
    public static function Url($string) {

        $chars = ['%'  ,'^'  ,'$'  , '/' ,':'  ,'?'  ,'@'  , '=' , '}' , '{' , '&' ,   '<',   '>',  '"',  '\'',  '#',   ' '];
        $urlCodes = ['%25','%5E','%24','%2F','%3A','%3F','%40','%3D','%7D','%7B','%26', '%3C', '%3E', '%22', '%27','%23', '%20'];
        $string = str_replace($urlCodes, $chars, $string);
        return $string;

    } // End method Url


    /**
    * @return string, cadena limpia de posibles inyecciones de código.
    */
  	public static function Clean ($startString) 
  	{
	    $stringClean = preg_replace(['/\s+/','/^\s|\s$/'], [' ', ''], $startString);
	    $stringClean = trim($stringClean);
	    $stringClean = self::EscSlashes($stringClean);
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
	    $stringClean = str_ireplace('OR ´1´=´1´', "", $stringClean);
	    $stringClean = str_ireplace("is NULL; --", "", $stringClean);
	    $stringClean = str_ireplace("IS NULL; --", "", $stringClean);
	    $stringClean = str_ireplace("LIKE '", "", $stringClean);
	    $stringClean = str_ireplace('LIKE "', "", $stringClean);
	    $stringClean = str_ireplace("LIKE ´", "", $stringClean);
	    $stringClean = str_ireplace("OR 'a'='a'", "", $stringClean);
	    $stringClean = str_ireplace('OR "a"="a"', "", $stringClean);
	    $stringClean = str_ireplace('OR ´a´=´a´', "", $stringClean);
	    $stringClean = str_ireplace("OR ´a´=´a´", "", $stringClean);
	    $stringClean = str_ireplace("--", "", $stringClean);
	    $stringClean = str_ireplace("^", "", $stringClean);
	    $stringClean = str_ireplace("[", "", $stringClean);
	    $stringClean = str_ireplace("]", "", $stringClean);
	    $stringClean = str_ireplace("==", "", $stringClean);

	    return $stringClean;

	} // End method Clean


	/**
    * @return string, Escapa caracteres especiales tipo slash.
    */
	public static function EscSlashes($string, $mode=''){
	    
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


	public static function ChartsToHTML($texto){
		$UrlLibreriasExternas = APP_URL_EXTERNAL_LIBRARIES;

		$texto = str_replace("[b]", "<strong>", $texto);
		$texto = str_replace("[/b]", "</strong>", $texto);

		$texto = str_replace("[i]", "<i>", $texto);
		$texto = str_replace("[/i]", "</i>", $texto);

		$texto = str_replace("[u]", "<u>", $texto);
		$texto = str_replace("[/u]", "</u>", $texto);

		$texto = str_replace("[s]", "<s>", $texto);
		$texto = str_replace("[/s]", "</s>", $texto);

		$texto = str_replace("[sub]", "<sub>", $texto);
		$texto = str_replace("[/sub]", "</sub>", $texto);

		$texto = str_replace("[sup]", "<sup>", $texto);
		$texto = str_replace("[/sup]", "</sup>", $texto);

		$texto = str_replace("[left]", "<left>", $texto);
		$texto = str_replace("[/left]", "</left>", $texto);

		$texto = str_replace("[center]", "<center>", $texto);
		$texto = str_replace("[/center]", "</center>", $texto);

		$texto = str_replace("[right]", "<right>", $texto);
		$texto = str_replace("[/right]", "</right>", $texto);

		$texto = str_replace("[justify]", "<justify>", $texto);
		$texto = str_replace("[/justify]", "</justify>", $texto);

		$texto = str_replace("[ul]", "<ul>", $texto);
		$texto = str_replace("[/ul]", "</ul>", $texto);

		$texto = str_replace("[li]", "<li>", $texto);
		$texto = str_replace("[/li]", "</li>", $texto);

		$texto = str_replace("[ol]", "<ol>", $texto);
		$texto = str_replace("[/ol]", "</ol>", $texto);

		$texto = str_replace("[hr]", "<hr>", $texto);
		$texto = str_replace("[/hr]", "</hr>", $texto);

		$texto = str_replace("[table]", "<table border=\"1\" cellspacing=\"0\" cellpadding=\"0\">", $texto);
		$texto = str_replace("[/table]", "</table>", $texto);

		$texto = str_replace("[tr]", "<tr>", $texto);
		$texto = str_replace("[/tr]", "</tr>", $texto);

		$texto = str_replace("[td]", "<td>", $texto);
		$texto = str_replace("[/td]", "</td>", $texto);

		$texto = str_replace("[quote]", "<blockquote>", $texto);
		$texto = str_replace("[/quote]", "</blockquote>", $texto);

		$texto = str_replace("[code]", "<code><pre>", $texto);
		$texto = str_replace("[/code]", "</pre></code>", $texto);

		$texto = str_replace("[rtl]", "<p style=\"direction: rtl;\">", $texto);
		$texto = str_replace("[/rtl]", "</p>", $texto);

		// ejm: 
		// $texto = "[font=Arial Black][color=#000000][size=3][color=#000000][size=3]arial black [/size][/color][/size][/color][/font][font=Comic Sans MS][color=#000000][size=3][color=#000000][size=3]comic [/size][/color][/size][/color][/font][font=Impact][color=#000000][size=3][color=#000000][size=3]impact[/size][/color][/size][/color][/font]";

		// Tipo de letra
		if(preg_match('[font=.*]', $texto)){
			// Convierte el texto en array
			$textDelimited = str_replace("[/font]", "[/font]««", $texto);
			$textArray = explode("««", $textDelimited);
			for ($i=0; $i < count($textArray); $i++) {
				$regex = '/\[font=.*\[\/font]/';
				preg_match($regex, $textArray[$i], $fonts, PREG_OFFSET_CAPTURE);
				if(!empty($fonts[0][0])){
					$regexFont = "/(\[font=)+([A-z ])+(\])/";
					preg_match($regexFont, $fonts[0][0], $fontText, PREG_OFFSET_CAPTURE);
					$fontText = str_replace("]", "", $fontText[0][0]);
					list($tag, $font) = explode("=", $fontText);
					$texto = str_replace("[font=$font]", "<font face=\"$font\">", $texto);
					$texto = str_replace("[/font]", "</font>", $texto);
				}
			}	
		}

		// Tamaño de letra
		if(preg_match('[size=.*]', $texto)){
			// Convierte el texto en array
			$textDelimited = str_replace("[/size]", "[/size]««", $texto);
			$textArray = explode("««", $textDelimited);
			for ($i=0; $i < count($textArray); $i++) {
				$regex = '/\[size=.*\[\/size]/';
				preg_match($regex, $textArray[$i], $size, PREG_OFFSET_CAPTURE);
				if(!empty($size[0][0])){
					$regexSize = "/(\[size=)+([0-9]{0,1})+(\])/";
					preg_match($regexSize, $size[0][0], $sizeText, PREG_OFFSET_CAPTURE);
					$sizeText = str_replace("]", "", $sizeText[0][0]);
					list($tag, $size) = explode("=", $sizeText);
					$texto = str_replace("[size=$size]", "<font size=\"$size\">", $texto);
					$texto = str_replace("[/size]", "</font>", $texto);
				}
			}
		}

		// Color de letra
		if(preg_match('[color=#.*]', $texto)){
			// Convierte el texto en array
			$textDelimited = str_replace("[/color]", "[/color]««", $texto);
			$textArray = explode("««", $textDelimited);
			for ($i=0; $i < count($textArray); $i++) {
				$regex = '/\[color=#.*\[\/color]/';
				preg_match($regex, $textArray[$i], $color, PREG_OFFSET_CAPTURE);
				if(!empty($color[0][0])){
					$regexColor = "/(\[color=)+([#0-9{6,} ])+(\])/";
					preg_match($regexColor, $color[0][0], $colorText, PREG_OFFSET_CAPTURE);
					$colorText = str_replace("]", "", $colorText[0][0]);
					list($tag, $color) = explode("=", $colorText);
					$texto = str_replace("[color=$color]", "<font color=\"$color\">", $texto);
					$texto = str_replace("[/color]", "</font>", $texto);
				}
			}
		}


		// ejm: 
		// $texto = "[img]https://d500.epimg.net/cincodias/imagenes/2018/11/13/lifestyle/1542113135_776401_1542116070_noticia_normal.jpg[/img] esto es un texto alterno a la imagen [img]https://i.blogs.es/594843/chrome/450_1000.jpg[/img] otro texto [img=50x50]https://www.40defiebre.com/wp-content/uploads/2015/10/imagenes.png[/img] esto es otro texto despues de las imágenes con ancho y alto [img=350x150]https://d500.epimg.net/cincodias/imagenes/2018/11/13/lifestyle/1542113135_776401_1542116070_noticia_normal.jpg[/img]";	

		// Búsqueda de imágenes sin ancho ni alto
		$count = substr_count($texto,"[img]");
		if($count > 0){
			for ($i=0; $i<$count; $i++) { 
				$regex = "/(\[img\])+(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?+(\[\/img\])/";
				preg_match($regex, $texto, $coincidencias, PREG_OFFSET_CAPTURE);
				if(!empty($coincidencias[0][0])){
					$match = $coincidencias[0][0];
					// Remplazo del [/img] por nulo
					$regexReplace = "/(\[\/img\])/";
					$matchRelpace = preg_replace($regexReplace, "", $match);
					// Obtiene sólo la url
					$regexUrl = "/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/";
					preg_match($regexUrl, $matchRelpace, $url, PREG_OFFSET_CAPTURE);
					$url = $url[0][0];
					// Remplaza el valor
					$texto = str_replace($match, "<br><img src=\"".$url."\" /><br>", $texto);
				}
			}
		}


		// Búsqueda de imágenes con ancho y alto
		$count = substr_count($texto,"[img=");
		if($count > 0){
			for ($i=0; $i<$count; $i++) {
				$regex = "/(\[img\=)+([0-9]{1,4})+(x)+([0-9]{1,4})(\])+(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?+(\[\/img\])/";
				preg_match($regex, $texto, $encontrado, PREG_OFFSET_CAPTURE);
				if(!empty($encontrado[0][0])){
					$match = $encontrado[0][0];
					// Remplazo del [/img] por nulo
					$regexReplace = "/(\[\/img\])/";
					$matchRelpace = preg_replace($regexReplace, "", $match);
					// Obtiene sólo la url
					$regexUrl = "/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/";
					preg_match($regexUrl, $matchRelpace, $url, PREG_OFFSET_CAPTURE);
					$url = $url[0][0];
					// Búsqueda de valores ancho y alto
					$regexSize = "/([0-9]{1,4})+(x)+([0-9]{1,4})/";
					preg_match($regexSize, $matchRelpace, $size, PREG_OFFSET_CAPTURE);
					list($width, $heigth) = explode("x", $size[0][0]);
					// Remplaza el valor
					$texto = str_replace($match, "<br><img src=\"".$url."\" width=\"".$width."\" heigth=\"".$heigth."\" /><br>", $texto);
				}
			}
		}
		
		// Ejm:
		// [email=joseacamposu@gmail.com]joseantoniocampos[/email]

		// Email
		$count = substr_count($texto,"[email=");
		if($count > 0){
			// Convierte el texto en array
			$textDelimited = str_replace("[/email]", "[/email]««", $texto);
			$textArray = explode("««", $textDelimited);
			for ($i=0; $i < count($textArray); $i++) {
				$regex = '/\[email=.*\[\/email]/';
				preg_match($regex, $textArray[$i], $email, PREG_OFFSET_CAPTURE);
				if(!empty($email[0][0])){
					$regexEmail = "/\b[\w.!#$%&*+\/=?{|}]+@[\w-]+(?:\.[\w]+)+\b(][a-z]*)/";
					preg_match($regexEmail, $email[0][0], $emailText, PREG_OFFSET_CAPTURE);
					list($emailTag, $text) = explode("]", $emailText[0][0]);
					list($tag, $email) = explode("=", $emailTag);
					$texto = str_replace("[email=$email]", "<a href=\"mailto:$email\">", $texto);
					$texto = str_replace("[/email]", "</a>", $texto);
				}
			}
		}

		// Url's
		if(preg_match('[url=.*]', $texto)){
			$ereg = '/\[url=.*\[\/url]/';
			$texto1 = str_replace("[/url]", "[/url]««", $texto);
			$texto2 = explode("««", $texto1);
			for ($i=0; $i < count($texto2); $i++) {
				$text = preg_match($ereg,$texto2[$i],$coincidencias,PREG_OFFSET_CAPTURE);
				if(!empty($coincidencias)){
					$text3 = ($coincidencias[0][0]);
					$ntext = explode("]", $text3);
					$ntext2 = explode("=", $ntext[0]);
					$fuente = $ntext2[1];
					// Recorre los parámetros
					if(count($ntext2)>2){
						for ($i=2; $i < count($ntext2); $i++) { 
							$fuente .= '='.$ntext2[$i];
						}
					}
					$texto = str_replace("[url=$fuente]", "<a href=\"$fuente\" target=\"_blank\">", $texto);
					$texto = str_replace("[/url]", "</a>", $texto);
				}
			}
		}

		if(preg_match('[youtube]', $texto)){
			$ereg = '/\[youtube.*\[\/youtube]/';
			$texto1 = str_replace("[/youtube]", "[/youtube]««", $texto);
			$texto2 = explode("««", $texto1);
			for ($i=0; $i < count($texto2); $i++) {
				$text = preg_match($ereg,$texto2[$i],$coincidencias,PREG_OFFSET_CAPTURE);
				if(!empty($coincidencias)){
					$text3 = ($coincidencias[0][0]);
					$ntext = explode("]", $text3);
					$ntext2 = explode("[", $ntext[1]);
					$fuente = $ntext2[0];
					$texto = str_replace("[youtube]", "<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/$fuente\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen>", $texto);
					$texto = str_replace("[/youtube]", "</iframe>", $texto);
				}
			}
		}

		// emoticons replace
		$texto = str_replace(":)", "<img src=\"$UrlLibreriasExternas/SCEditor/$UrlLibreriasExternas/SCEditor/emoticons/smile.png\">", $texto);
		$texto = str_replace(":angel:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/angel.png\">", $texto);
		$texto = str_replace(":angry:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/angry.png\">", $texto);
		$texto = str_replace("8-)", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/cool.png\">", $texto);
		$texto = str_replace(":'(", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/cwy.png\">", $texto);
		$texto = str_replace(":ermm:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/ermm.png\">", $texto);
		$texto = str_replace(":D", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/grin.png\">", $texto);
		$texto = str_replace("<3", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/heart.png\">", $texto);
		$texto = str_replace(":(", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/sad.png\">", $texto);
		$texto = str_replace(":O", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/shocked.png\">", $texto);
		$texto = str_replace(":P", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/tongue.png\">", $texto);
		$texto = str_replace(";)", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/wink.png\">", $texto);
		$texto = str_replace(":alien:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/alien.png\">", $texto);
		$texto = str_replace(":blink:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/blink.png\">", $texto);
		$texto = str_replace(":blush:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/blush.png\">", $texto);
		$texto = str_replace(":cheerful:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/cheerful.png\">", $texto);
		$texto = str_replace(":devil:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/devil.png\">", $texto);
		$texto = str_replace(":dizzy:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/dizzy.png\">", $texto);
		$texto = str_replace(":getlost:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/getlost.png\">", $texto);
		$texto = str_replace(":happy:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/happy.png\">", $texto);
		$texto = str_replace(":kissing:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/kissing.png\">", $texto);
		$texto = str_replace(":ninja:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/ninja.png\">", $texto);
		$texto = str_replace(":pinch:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/pinch.png\">", $texto);
		$texto = str_replace(":pouty:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/pouty.png\">", $texto);
		$texto = str_replace(":sick:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/sick.png\">", $texto);
		$texto = str_replace(":sideways:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/sideways.png\">", $texto);
		$texto = str_replace(":silly:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/silly.png\">", $texto);
		$texto = str_replace(":sleeping:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/sleeping.png\">", $texto);
		$texto = str_replace(":unsure:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/unsure.png\">", $texto);
		$texto = str_replace(":woot:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/w00t.png\">", $texto);
		$texto = str_replace(":wassat:", "<img src=\"$UrlLibreriasExternas/SCEditor/emoticons/wassat.png\">", $texto);

		return $texto;
	}


} // End class AppScapeStr


 ?>