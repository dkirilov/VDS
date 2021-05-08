<?php $page_title = "Logout"; ?>
<?php require_once("header.phtml"); ?>
  <?php
     \VDS\UserManager::logout();
     if(!\VDS\UserManager::is_logged_in()){
        echo "Congratulations! You logged out successfully.";
     }else{
     	echo "Log out failed!";
     }
  ?>
<?php require_once("footer.phtml"); ?>
