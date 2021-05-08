<?php $page_title = "Dashboard"; ?>
<?php $css_to_include = array('global'); ?>
<?php require_once("header.phtml"); ?>

<?php if(\VDS\UserManager::is_logged_in()): ?>
    <div id="dashboard">
      <ul>
        <li><a href="<?= APP_BASE_URL ?>">Индекс</a></li>
        <li><a href="<?= APP_BASE_URL ?>/upload">Качи видео</a></li>
        <li><a href="<?= APP_BASE_URL ?>/upload-subtitles">Качи субтитри</a></li>
        <li><a href="<?= APP_BASE_URL ?>/logout">Излез</a></li>
      </ul>
    </div>
<?php else: ?>
    <strong>Access denied.</strong>
<?php endif; ?>
<?php require_once("footer.phtml"); ?>

