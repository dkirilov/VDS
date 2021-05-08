<?php

namespace VDS;

class TokensHandler{

public static function loadToken($server_name, $token_name){
   $tok_fp = APP_BASE . "/tokens/$server_name/$token_name.tok";
   
   if(file_exists($tok_fp)){
     $token = file_get_contents($tok_fp);
     return unserialize($token);
   }
   
   return false;
}

public static function saveToken($server_name, $token_name, $token){
    $tok_fp = APP_BASE . "/tokens/$server_name/$token_name.tok";
    
    $file = fopen($tok_fp, "w");
    $written_bytes = fwrite($file, serialize($token));
    fclose($file);
    
    return !empty($written_bytes);
}

}

?>
