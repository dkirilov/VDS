<?php 

  if(!\VDS\UserManager::is_logged_in()){
  	 die("Access denied.");
  }

  $sprogress_prefix = "zqpalnik_vds_";
  $sprogress_name = "video_upload_progress";

  ini_set('session.upload_progress.enabled', '1');
  ini_set('session.upload_progress.prefix', $sprogress_prefix);
  ini_set('session.upload_progress.name', $sprogress_name);


   $key = ini_get('session.upload_progress.prefix') . ini_get('session.upload_progress.name');

   // $data = array();
   // foreach ($_SESSION[$key]['files'] as $file) {
   //   $data[] = array(
   //     'fname' => $file['name'],
   //     'fsize' => Tools::format_bytes(Tools::getUploadingFileInfo($file['name'], 'size')),
   //     'progress' => Tools::getUploadingFileProgress($key, $file['name'])
   //   );
   // }

   die(json_encode($_SESSION));


 ?>