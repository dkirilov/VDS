<?php 

namespace VDS;

defined('PASSWD') or die();

require_once(APP_BASE . "/classes/UserManager.class.php");
require_once(APP_BASE . "/classes/Validator.class.php");
require_once(APP_BASE . '/classes/Tools.class.php');

class VDS{
    private $available_clients = array('dropbox');
    private $clients = array();
    private $file_path = null;
    private $password = null;

    public function __construct($fp, $pwd){
       $this->setFilePath($fp);
       $this->setPassowrd($pwd);

       $this->initClients();

       if(empty($fp)){
          $this->printFilesIndex();
       }else{
          $sliced_fp = explode('/', $fp);

          $page_path = APP_BASE . "/pages/" . $sliced_fp[0] . ".php";
          if(file_exists($page_path)){
             include_once($page_path); 
          }else{
             $this->getStream();
          }
       }
    }
    
    public function setFilePath($fp){
         $this->file_path = $fp;
    }
    
    public function setPassowrd($pwd){
       $this->password = $pwd;
    }

    private function initClients(){
       foreach($this->available_clients as $cl){
           $this->initClient($cl);
       }
    }
 
    private function initClient($client_name){
       $cn = ucfirst($client_name);
       $hn = "\\VDS\\{$cn}Handler";

       require_once("handlers/{$cn}Handler.class.php");       
       
       // make new client's instance
       $this->clients[$cn] =  $hn::getInstance();

       // authorize the client
       $hn::authorize($this->clients[$cn]);  
    }

    private static function matchStrings($value){
        preg_match_all('/[A-z0-9]+/miu', Tools::getReqVar('search'), $search, PREG_SET_ORDER, 0);

        $min_matches = 3;
        $matches_count =  0;
        foreach ($search as $word) {
           if(stripos($value['name'], $word[0]) !== false){
              $matches_count++;
           }
        }

        return $matches_count >= $min_matches;
    }
    
    private function printFilesIndex(){
        if(!\VDS\UserManager::is_logged_in()){
           die("Access denied.");
        }

        $page_title = "Index";
        $show_nav = true;
        $css_to_include = array('global');
        require_once(APP_BASE . "/pages/header.phtml");

        foreach($this->clients as $client_name => $client_instance){
           $files_list = $client_instance->getFilesList($this->file_path);

           if(Tools::reqVarExists('search')){
              $files_list = array_filter($files_list, "self::matchStrings");
           }

           sort($files_list);
    
           $paginated_flist = array_chunk($files_list, ITEMS_PER_PAGE);
           $current_page = $this->getCurrentPage($client_name, count($paginated_flist));

           // Outputs the search form
           ?>
            <form method="get" class="search_form" id="flist_search_form">
                <label>
                   <input type="text" name="search" size="35" placeholder="Search for files...">
                   <button type="submit">Search</button>
                </label>
            </form>
           <?php         
 
           echo "<h1>$client_name</h1>" . PHP_EOL;
           foreach($paginated_flist[$current_page-1] as $file){
               $link = APP_BASE_URL . '/' . $file["path"];
               $size_mb = round( $file["size"] / (1024*1024) );

               echo "<p>";
               echo "<a href='$link'><strong>".$file["name"]."</strong></a> - <em>$size_mb MB</em>";
               echo "</p>";
           }
           $this->printPagination($client_name, $current_page, count($paginated_flist));
        }

        require_once(APP_BASE . "/pages/footer.phtml");
    }

    private function printPagination(string $client_name, int $current_page, int $pages_total){
        $range = array(
            'min' => $current_page-PAGINATION_DISPLAY_NEAREST,
            'max' => $current_page+PAGINATION_DISPLAY_NEAREST
        );

        if($range['min'] < 1){
            $range['min'] = 1;
        }

        if($range['max'] > $pages_total){
            $range['max'] = $pages_total;
        }
        ?>
        <div class="pagination" id="<?= $client_name ?>_pages">
            <ul>
                <?php if($range['min'] > 1): ?>
                    <li title="Към първата страница" class="special"><a href="<?= Tools::addQueryParam($client_name."_page", 1); ?>"><<</a></li>
                <?php endif; ?>

                <?php if($current_page > 1): ?>                    
                    <li title="Предишна страница" class="special"><a href="<?= Tools::addQueryParam($client_name."_page", $current_page-1); ?>"><</a></li>
                <?php endif; ?>

                <?php if($range['min'] > 1): ?>
                    <li> ... </li>
                <?php endif; ?>
       
                <?php for($i = $range['min']; $i <= $range['max']; $i++): ?>
                    <li <?= $i==$current_page?"class=\"active\"":""; ?> ><a href="<?= Tools::addQueryParam($client_name."_page", $i); ?>">[<?= $i ?>]</a></li>
                <?php endfor; ?>

                <?php if($range['max'] < $pages_total): ?>
                    <li> ... </li>
                <?php endif; ?>

                <?php if($current_page < $pages_total): ?>
                    <li title="Следваща страница" class="special"><a href="<?= Tools::addQueryParam($client_name."_page", $current_page+1); ?>">></a></li>                    
                <?php endif; ?>

                <?php if($range['max'] < $pages_total): ?>
                    <li title="Към последната страница" class="special"><a href="<?= Tools::addQueryParam($client_name."_page", $pages_total); ?>">>></a></li>
                <?php endif; ?>
            </ul>
        </div>
        <?php
    }

    private function getCurrentPage(string $client_name, int $pages_total){
        $current_page = Tools::getReqVar($client_name.'_page');
        if(!$current_page || $current_page < 1 || $current_page > $pages_total){
            $current_page = 1;
        }
        
        return $current_page;
    }
    
    public function getStream(){    
        foreach($this->clients as $client){
           $client->getStream($this->file_path);
        }      
    }

    public function uploadVideo(array $video_file){
        if(empty($video_file) || !isset($video_file['size'])){
            throw new \Exception("Error occured while uploading videos! Error reason: missing file size or video_data array is empty!");
        }

        Validator::validateVideoExtension($video_file['name']);
        Validator::validateVideoType($video_file['type']);

        $client_name = $this->chooseClientForUpload($video_file['size']);
 
        if(empty($client_name)){
            throw new \Exception("I could not find a server with enough free space to upload this video file!");
        }

        $source_file = '/tmp/'.$video_file['name'];
        rename($video_file['tmp_name'], $source_file);

        if(isset($_POST['separate_folder']) && !empty($_POST['separate_folder_name'])){
          $destination_file = str_replace("//", "/", $_POST['separate_folder_name'].'/') . $video_file['name'];
        }else{
          $destination_file = '/' . $video_file['name'];
        }

        $upload_succeeded = $this->clients[$client_name]->upload($destination_file, $source_file);

        if($upload_succeeded){
            echo "<p class=\"msg success\"><strong>Файлът <em>{$video_file['name']}</em> е качен успешно.</strong></p>";
            return true;
        }else{
            echo "<p class=\"msg error\"><strong>Файлът <em>{$video_file['name']}</em> не е качен, понеже възникна грешка.</strong></p>";
            return false;
        }
    }

    public function uploadVideos(array $uploaded_videos){
        $error_count = 0;

        $files_count = count($uploaded_videos['name']);
        for($c = 0; $c<$files_count; $c++){
            $current_video = array(
                'name' => $uploaded_videos['name'][$c],
                'type' => $uploaded_videos['type'][$c],
                'tmp_name' => $uploaded_videos['tmp_name'][$c],
                'size' => $uploaded_videos['size'][$c]
            );

            if(!$this->uploadVideo($current_video)){
                $error_count++;
            }
        }

        if($error_count == 0){
          echo "<p class=\"msg success\"><strong>Поздравления! Всички файлове бяха качени безпроблемно. :-)</strong></p>";
          return true;
        }else{
          echo "<p class=\"msg error\"><strong>Лоши новини! Някои файлове не бяха качени, поради възникнали грешки по време на качването. :-(</strong></p>";
          return false;          
        }
    }

    public function getClientFreeSpace(string $client_name){
        return $this->clients[$client_name]->getFreeSpace();
    }

    public function getClientFilesList(string $client_name = null){
        $files_list = array();
        
        if(!empty($client_name)){
            $files_list = $this->clients[$client_name]->getFilesList();
        }else{
            foreach($this->clients as $cname => $cinstance){
                $files_list = array_merge($files_list, $cinstance->getFilesList());
            }
        }

        return $files_list;
    }

    public function getVideosList(){
        $all_files = $this->getClientFilesList();
        $videos = array();
        
        foreach($all_files as $file){
            if(Validator::isValidVideoFile($file['name'])){
                $vid = md5($file['name'] . $file['size']);
                $videos[$vid] = $file['name'];
            }
        }

        return $videos;
    }

    private function chooseClientForUpload(int $video_file_size){
        $choosen_client_name = null;
        foreach($this->clients as $client_name => $client_instance){
            if($client_instance->getFreeSpace() > $video_file_size){
                $choosen_client_name = $client_name;
                break;
            }
        }

        return $choosen_client_name;
    }
}

?>
