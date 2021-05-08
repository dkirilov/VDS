<?php

namespace VDS;

require_once(APP_BASE . '/classes/interfaces/Handler.iface.php');
require_once(APP_BASE . '/classes/clients/DropboxClientExtended.class.php');
require_once('TokensHandler.class.php');

class DropboxHandler implements iHandler{

    public static function getInstance(){
      $db_instance = new \VDS\DropboxClientExtended(array('app_key' => DROPBOX_APP_KEY, 
                                                    'app_secret' => DROPBOX_APP_SECRET,
                                                    'app_full_access' => false,
                                                    'en'));
      return $db_instance;
    }

    public static function authorize($db_instance){
        self::isDropboxInstance($db_instance);

        $redir_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . "?auth_redirect=1";

        // Dropbox authorization
        $my_token = TokensHandler::loadToken('dropbox', 'zqpalnik-vds');
        if($my_token){
            $db_instance->SetBearerToken($my_token);              
        }else if(!empty($_GET['auth_redirect'])){
            $token = $db_instance->GetBearerToken(null, $redir_url);
            TokensHandler::saveToken('dropbox', 'zqpalnik-vds', $token);               
        }else{
            $auth_url = $db_instance->BuildAuthorizeUrl( $redir_url );
            die( "Authentication required. <a href='$auth_url'>Continue.</a>" );            
        }
    }

    private static function isDropboxInstance($instance){
        if( !is_object($instance) ){
           throw new \Exception('Fatal error: You should create a DropboxClient\'instance!');
        }
    }

}

?>
