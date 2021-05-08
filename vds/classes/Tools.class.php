<?php

namespace VDS;

class Tools{
	/**
	 * @param int $size Size in bytes
	 * @return array Returns array that contains formatted size and unit.
	 */
	public static function format_bytes(int $size){
		 $one_kilobyte = 1024;
		 $one_megabyte = $one_kilobyte * 1024;
		 $one_gigabyte = $one_megabyte * 1024;
		 $one_terabyte = $one_gigabyte * 1024;

		 $formatted_size = array('size' => 0, 'unit' => 'Bytes');

		 if($size < $one_kilobyte){
		 	$formatted_size['size'] = $size;
		 }else if($size < $one_megabyte){
		 	$formatted_size['size'] = $size / $one_kilobyte;
		 	$formatted_size['unit'] = 'Kilobytes';
		 }else if($size < $one_gigabyte){
		 	$formatted_size['size'] = $size / $one_megabyte;
		 	$formatted_size['unit'] = 'Megabytes';
		 }else if($size < $one_terabyte){
		 	$formatted_size['size'] = $size / $one_gigaabyte;
		 	$formatted_size['unit'] = 'Gigabytes';
		 }else{
		 	$formatted_size['size'] = $size / $one_terabyte;
		 	$formatted_size['unit'] = 'Terabytes';
		 }

		 $formatted_size['size'] = number_format($formatted_size['size'], 2);

		 return $formatted_size;
	}

	public static function inclCss(string $css_name){
		$css_file_url = CSS_DIR_URL . $css_name . '.css';
?>
<link rel="stylesheet" type="text/css" href="<?= $css_file_url ?>">
<?php
	}

	public static function inclCssMulti(array $css_names){
		foreach ($css_names as $css_name) {
			self::inclCss($css_name);
		}
	}

    public static function getReqVar(string $var_name){
        if(isset($_REQUEST[$var_name]) && !empty($_REQUEST[$var_name])){
            return $_REQUEST[$var_name];
        }else{
            return false;
        }
    }

    public static function reqVarExists(string $var_name){
        return isset($_REQUEST[$var_name]) && !empty($_REQUEST[$var_name]);
    }

    public static function addQueryParam(string $param_name, $param_value){
        $query_string = HTTP_PROTO . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $query_base = strstr($query_string, "?", true);
        $query_params = $_GET;
        $query_params[$param_name] = $param_value;
 
        $query_string = $query_base . "?";
        foreach($query_params as $param_name => $param_value){
            $query_string .= ($param_name."=".$param_value."&");
        }

        return substr($query_string, 0, strlen($query_string)-1);
    }

    public static function getUploadingFileInfo(string $file_name, string $retkey = 'all'){
    	foreach ($_FILES as $file) {
    		if($file['name'] === $file_name){
    			if($retkey === 'all'){
    				return $file;
    			}else{
    				if(isset($file[$retkey])){
	    				return $file[$retkey];
    				}
    			}
    		}
    	}

    	return false;
    }

    public static function getUploadingFileProgress(string $sess_key, string $file_name){
    	if(isset($_SESSION[$sess_key])){
    		return ($_SESSION[$sess_key]['bytes_processed'] / self::getUploadingFileInfo($file_name, 'size')) * 100;
    	}

    	return false;
    }
}
