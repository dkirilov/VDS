<?php

if(\VDS\UserManager::is_logged_in()){
   echo "<strong>You already logged in :-)</strong><a href='". \VDS\UserManager::$dashboard_link ."'> &lt;&lt; Dashboard &gt;&gt;</a><br>";
   exit();
}else{
   \VDS\UserManager::login();
}

?>

<?php $page_title = "Authorization form"; ?>
<?php require_once("header.phtml"); ?>

<form id="auth_form" method="post">
  <p>
    <label for="uname_field">Username: </label>
    <input type="text" id="uname_field" name="uname" size="20">

    <br>

    <label for="pwd_field">Password: </label>
    <input type="password" id="pwd_field" name="pwd" size="20">

    <br>

    <button type="submit" name="auth_btn">Authorize</button>
  </p>
</form>

<?php require_once("footer.phtml"); ?>

