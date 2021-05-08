<?php
  if(!\VDS\UserManager::is_logged_in()){
  	 die("Access denied.");
  }

  if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['subtitle_files'])){
  	 $this->uploadSubtitles($_FILES['subtitle_files']);
  }

  $videos_list = $this->getVideosList();
?>

<?php $page_title = "Subtitles upload form"; ?>
<?php $show_nav = true; ?>
<?php $css_to_include = array('global'); ?>
<?php include_once("data/languages.data.php"); ?>
<?php require_once("header.phtml"); ?>

<h4 class="centered-heading">От тук имате възможност да добавяте субтитри, на различни езици, към качените видеа.</h4>
<p class="upload-subline"><strong>Имайте предвид, че субтитрите, които качвате трябва да бъдат във <ins>.vtt</ins> формат.</strong></p>
<form class="upload-form" method="post" enctype="multipart/form-data">
	<div class="form-row">
		<label for="subtitle_file_field">Моля изберете файл със субтитри*</label>
		<input type="file" name="subtitle_file" id="subtitle_file_field" required>
	</div>
	<div class="form-row">
        <label for="subtitles_lang_select">Език на субтитрите*</label>
		<select id="subtitles_lang_select" name="subtitles_lang" required>
            <option value="none">--- Изберете език ---</option>
            <?php foreach($languages as $lang_code => $lang_name): ?>
                <option value="<?= $lang_code ?>"><?= $lang_name ?></option>
            <?php endforeach; ?>
        </select>
	</div>
	<div class="form-row">
		<label for="video_select">За кое видео са субтитрите?*</label>
        <select id="video_select" name="subtitles_video" required>
            <option value="none">--- Изберете видео ---</option>
            <?php foreach($videos_list as $vid => $vname): ?>
                <option value="<?= $vid ?>"><?= $vname ?></option>
            <?php endforeach; ?>
        </select>
	</div>
	<div class="form-row">
		<input type="submit" name="submit_video_files" value="Качи файловете">
	</div>
</form>

<?php require_once("footer.phtml"); ?>
