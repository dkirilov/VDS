<?php

namespace VDS;

require_once(APP_BASE . '/classes/interfaces/Client.iface.php');
require_once(APP_BASE . '/classes/clients/DropboxClient.class.php');
require_once(APP_BASE . "/classes/CacheManager.class.php");

class DropboxClientExtended extends \DropboxClient implements iClient{
   public function getStream($file_path){
        $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);

        // Get direct link to the file
        $file_path = '/' . $file_path;
        $link = $this->GetLink($file_path, false);
 
        // Redirect the client to get the stream
        header("Location: $link");
        exit();   
   }
 
   public function getFilesList($path = ''){
      if(CacheManager::cacheExists('DropboxFilesList')){
          $files_list = CacheManager::getCache('DropboxFilesList');
      }else{
          $files_list = $this->GetFiles($path , true);
          CacheManager::cache('DropboxFilesList', $files_list, time()+(24*3600));       
      }

      $files_info_array = array();

      foreach($files_list as $fp=>$fm){
        if(!$fm->is_dir){
           $files_info_array[] = array(
               'path' => $fp,
               'name' => $fm->name,
               'size' => $fm->size
           );
        }
      }

      return $files_info_array;
   }

   /**
    * @param string $target_path Absolute path to the final destination of uploaded source file. This path must be ending with the file name and its extension.
    * @param string $source_file_path Absolute path to a source file
    *
    * @return bool Returns true on success or false on faliure
    */
   public function upload(string $target_file_path, string $source_file_path){
        return !empty($this->UploadFile($source_file_path, $target_file_path));
   }

   /**
    * @return int|false Returns remaining free space in bytes or false if someting went wrong.
    */
   public function getFreeSpace(){
      $resp = $this->apiCall('2/users/get_space_usage');
      if(!empty($resp)){
          return $resp->allocation->allocated - $resp->used;
      }

      return false;
   }
}

?>
