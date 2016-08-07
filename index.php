<?php
  require_once( "classes/" . "meekrodb.2.3.class.php" );
  require_once( "classes/" . "tpl.class.php" );
  
  date_default_timezone_set('America/Chicago');
  
  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  
  session_start();
  /* view=something&id=number */
  $view = ( isset( $_SESSION['user'] ) )? ( isset( $_REQUEST["view"] ) )? $_REQUEST["view"] : "portals" : "login";
  
  $iPod   = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
  $iPhone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
  $iPad   = strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
  $dajax = ($iPod||$iPhone||$iPad)? "": ' data-ajax="false"';
  
  $viewsArray = array (
    "portals"  => array( "portals.php", "TAS.KOM Portal" ) ,
    "portal"   => array( "portals.php", "TAS.KOM Portal" ) ,
    "students" => array( "students.php", "TAS.KOM Students" ) ,
    "student"  => array( "students.php", "" ) ,
    "form"  => array( "add.php", "" ) ,
    "teacher"  => array( "teacher.php", "TAS.KOM Teachers" ) ,
    "rooms"    => array( "rooms.php",   "TAS.KOM Classrooms" ) ,
    "room"     => array( "rooms.php",   "" ) ,
    "routes"   => array( "routes.php",  "TAS.KOM Routes" ) ,
    "buswork"  => array( "buswork.php", "TAS.KOM Bus Workers" ) ,
    "reports"  => array( "reports.php", "TAS.KOM Reports" ) ,
    "profile"  => array( "profile.php", "TAS.KOM Profile" ) ,
    "login"    => array( "login.php",   "TAS.KOM Login" )
  );
  
  require_once("includes/header.php");
  
  require_once( "sources/" . $viewsArray[$view][0] );

  
?>
    </div>
  </body>
</html>