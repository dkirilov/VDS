<?php

namespace VDS;

class UserManager{

 public static $dashboard_link =  APP_BASE_URL . "/dashboard";

 public static function login(){
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
         if($_POST['uname'] == USER && $_POST['pwd'] == PASSWD){
            $_SESSION['uname'] = $_POST['uname'];
            $_SESSION['pwd'] = $_POST['pwd'];
            echo "<strong>Congratulations! You're logged in now. :-)</strong><a href='". self::$dashboard_link ."'> &lt;&lt; Dashboard &gt;&gt;</a><br>";
            exit();
         }else if(!empty($_POST['uname']) || !empty($_POST['pwd'])){
              echo "<em style='color:red;'>Invalid login details!</em>";
         }
    }
 }

 public static function logout(){
    session_unset();
    session_destroy();
 }

 public static function is_logged_in(){
   if(!empty($_SESSION['uname']) && !empty($_SESSION['pwd']) && $_SESSION['uname'] == USER && $_SESSION['pwd'] == PASSWD){ 
      return true;
   }else{
      return false;
   }
 }

}

?>
