<?php
  if(!\VDS\UserManager::is_logged_in()){
  	 die("Access denied.");
  }

  $sprogress_prefix = "zqpalnik_vds_";
  $sprogress_name = "video_upload_progress";

  ini_set('session.upload_progress.enabled', '1');
  ini_set('session.upload_progress.prefix', $sprogress_prefix);
  ini_set('session.upload_progress.name', $sprogress_name);

  if(isset($_GET['ajax'])){
  	 $key = ini_get('session.upload_progress.prefix') . ini_get('session.upload_progress.name');

  	 // $data = array();
  	 // foreach ($_SESSION[$key]['files'] as $file) {
  	 // 	$data[] = array(
  	 // 		'fname' => $file['name'],
  	 // 		'fsize' => Tools::format_bytes(Tools::getUploadingFileInfo($file['name'], 'size')),
  	 // 		'progress' => Tools::getUploadingFileProgress($key, $file['name'])
  	 // 	);
  	 // }

  	 die(json_encode($_SESSION));
  }

  if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['video_files'])){
  	 $this->uploadVideos($_FILES['video_files']);
  }
?>

<?php $page_title = "Videos upload form"; ?>
<?php $show_nav = true; ?>
<?php $css_to_include = array('global'); ?>
<?php require_once("header.phtml"); ?>

<h4 class="centered-heading">С помощта на тази форма, имате възможност лесно да качвате видео файлове в нашата система за съхранение и предоставяне на видео файлове.</h4>
<p class="upload-subline"><strong>Формата приема файлове със следните разширения: <em>.webm</em>, <em>.mp4</em>.</strong></p>
<p class="upload-subline"><strong>Формата приема файлове от следните типове: <em>video/webm</em>, <em>video/mp4</em>.</strong></p>
<form class="upload-form" method="post" enctype="multipart/form-data">
	<div class="form-row">
		<label for="video_files_field">Моля изберете видео файлове за качване</label>
		<input type="file" name="video_files[]" id="video_files_field" multiple>
	</div>
	<div class="form-row">
		<label for="separate_folder_field">В отделна директория</label>
		<input type="checkbox" name="separate_folder" id="separate_folder_field">
	</div>
	<div class="form-row">
		<label for="separate_folder_name_field">Име на директорията</label>
		<input type="text" name="separate_folder_name" id="separate_folder_name_field">
	</div>
	<div class="form-row">
		<input type="submit" name="submit_video_files" value="Качи файловете" onclick="updateProgress();">
	</div>
</form>

<table id="upload_progress">
	<caption>
		Прогрес на качването
	</caption>
	<thead>
		<tr>
			<th>Файл</th>
			<th>Размер</th>
			<th>Прогрес</th>
		</tr>
	</thead>
	<tbody>
		
	</tbody>
</table>

<script type="text/javascript" defer>
	function updateProgress(){
		setInterval(function(){
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function(){
				console.log(this);
			};
			xhttp.open('GET', 'ajax=1', false);
			xhttp.send();
			//console.log(xhttp);
		}, 1000);
	}
</script>

<?php require_once("footer.phtml"); ?>
