<?php
  
  /*page title and container for the string that contains the content of the page*/
  $title = $viewsArray[$view][1];
  $build_page = "";
  
  /* allow for logout */
  $logout = ( isset( $_REQUEST["act"] ) )? (intval($_REQUEST["act"])==2)? 2: 1: 0;
  if ($logout == 2) unset($_SESSION['user']);
  
  /* Error container */
  $error = "";
  
  /*check if we've submitted data*/
  if ( isset($_POST['submit']) ) {
    $error = ( empty($_POST['username']) )? "Username or Password is invalid.":"";
    $username=$_POST['username'];
    $password=$_POST['password'];
    $account = DB::queryFullColumns('SELECT * FROM ss_staff WHERE username=%s and password=%s',$username, md5($password));
    /* if returned rows is greater than 0 */
    if (count($account)<1) {
      $error = "Username or Password is invalid. line 22.";
    } else {
      $_SESSION['user'] = $account[0];
      $view = ( isset( $_REQUEST["view"] ) )? ( $_REQUEST["view"] != 'login' )? $_REQUEST['view'] : 'portals' : "portals" ;
    }
  }
  if ($view != "login") {
    require_once( "sources/" . $viewsArray[$view][0] );
  } else {
  
    $build_page .= <<<END
<form action="?view=portals" method="post">
          <label>UserName :</label>
          <input id="name" name="username" placeholder="username" type="text">
          <label>Password :</label>
          <input id="password" name="password" placeholder="password" type="password">
          <input name="submit" type="submit" value=" Login ">
          <span>$error</span>
        </form>
END;
  
  echo <<<EOD
      <header data-role="header">
        <h1>$title</h1>
      </header>
      <div data-role="main" class="ui-content">
        $build_page
      </div><!-- close ui-content -->\n
EOD;
  }
?>