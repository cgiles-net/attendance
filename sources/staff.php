<?php
  /* Staff page */
  $title=$viewsArray[$view][1];
  $build_page="";
  
  /* list staff */
  if (!isset($_GET["id"])&&$view!="profile"&&!isset($_GET["action"])) {
    $staff_list = new list_obj(array(
        "type"    =>"ul",
        "inset"   =>true,
        "spacer"  =>"        "
      ));
    
    /* get teachers in room */
    $staff_list->add_item("Teachers",true);
    $teachers   = DB::queryFullColumns("SELECT * FROM ss_staff WHERE room_id IS NOT NULL ORDER BY `ss_staff`.`last_name` ASC");
    if ( count($teachers)>0 ) {
      foreach ($teachers as $teacher){
        $teacher_name = $teacher["ss_staff.last_name"].", ".$teacher["ss_staff.first_name"];
        $staff_id = $teacher["ss_staff.staff_id"];
        $staff_list->add_item("<a href=\"?view=teacher&id=$staff_id\">$teacher_name</a>");
      }
    }
    $staff_list->add_item("Bus Workers",true);
    $busworkers   = DB::queryFullColumns("SELECT * FROM ss_staff WHERE route_id IS NOT NULL ORDER BY `ss_staff`.`last_name` ASC");
    if ( count($busworkers)>0 ) {
      foreach ($busworkers as $busworker){
        $busworker_name = $busworker["ss_staff.last_name"].", ".$busworker["ss_staff.first_name"];
        $staff_id = $busworker["ss_staff.staff_id"];
        $staff_list->add_item("<a href=\"?view=buswrker&id=$staff_id\">$busworker_name</a>");
      }
    }
    $staff_list->add_item("Unassigned",true);
    $gen_staff   = DB::queryFullColumns("SELECT * FROM ss_staff WHERE route_id IS NULL AND room_id IS NULL ORDER BY `ss_staff`.`last_name` ASC");
    if ( count($gen_staff)>0 ) {
      foreach ($gen_staff as $staff){
        $staff_name = $staff["ss_staff.last_name"].", ".$staff["ss_staff.first_name"];
        $staff_id = $staff["ss_staff.staff_id"];
        $staff_list->add_item("<a href=\"?view=staff&id=$staff_id\">$staff_name</a>");
      }
    }
    $build_page.=$staff_list->close();
  } else if ($view=="profile"||isset($_GET["id"]))
    $id = (isset($_GET["id"]))? intval($_GET["id"]) : $_SESSION["user"]["ss_staff.staff_id"];
  
  $hasAuth = (isset($id))? ($_SESSION["user"]["ss_staff.staff_id"]==$id||$_SESSION["user"]["ss_staff.approved"]) : ($_SESSION["user"]["ss_staff.approved"]);
  /*profile*/
  if (isset($id)&&!isset($_GET["action"])) {
    $profile = DB::queryFirstRow("select * from ss_staff where staff_id = %i",$id);
    #$hasAuth = ($_SESSION["user"]["ss_staff.staff_id"]==$id)?true:$hasAuth;
    $room_id = (isset($profile["room_id"]))? $profile["room_id"] : -1;
    $route_id = (isset($profile["route_id"]))? $profile["route_id"] : -1;
    $teacher = ($room_id>=0)? " checked='checked'" : "";
    $busworker = ($route_id>=0)? " checked='checked'" : "";
    $admin="";
    $profile_list = new list_obj(array(
      "type"    =>"ul",
      "inset"   =>true,
      "spacer"  =>"        "
    ));
    $profile_list->add_item("Image",true);
    if ($hasAuth) {
      $profile_list->add_item("username",true);
      $profile_list->add_item($profile["username"]);
    }
    $profile_list->add_item("Contact",true);
    $profile_list->add_item("Phone: ".$profile["phone"]);
    $profile_list->add_item("Email: ".$profile["email"]);
    
    $room_id = $profile["room_id"];
    $route_id = $profile["route_id"];
    if ($room_id>0||$route_id>0)
      $profile_list->add_item("Role > Assignment",true);
    if ($room_id>0) {
      $room_name = DB::queryFirstField("SELECT `room_name` FROM ss_rooms WHERE `room_id` = %i", $room_id);
      $profile_list->add_item("<fieldset data-role='controlgroup' data-type='horizontal'><input class='ui-disabled' disabled='disabled' name='teacher' id='teacher' type='checkbox'$teacher><label for='teacher'>Teacher</label><a href=\"?view=room&id=$room_id\" class='ui-btn ui-corner-all ui-icon-carat-r ui-btn-icon-right'>$room_name</a></fieldset>");
    }
    if ($route_id>0) {
      $route_name = DB::queryFirstField("SELECT `route_name` FROM ss_routes WHERE `route_id` = %i", $route_id);
      $profile_list->add_item("<fieldset data-role='controlgroup' data-type='horizontal'><input class='ui-disabled' disabled='disabled' name='busroute' id='busroute' type='checkbox'$busworker><label for='busroute'>Bus Worker</label><a href=\"?view=route&id=$route_id\" class='ui-btn ui-corner-all ui-icon-carat-r ui-btn-icon-right'>$route_name</a></feildset>");
    }
    $build_page .= $profile_list->close();
  }
  
  if (isset($_GET["action"])) {
    $act= $_GET["action"];
    if (!isset($id)) $id=0;
    $build_page .= build_form($act, $hasAuth, $id);
    $build_page .= "<script src='includes/staff.$act.js' type='text/css'></script>";
    #password_hash($password, PASSWORD_BCRYPT);
  }
  function build_form($act,$hasAuth,$id) {
    if (!$hasAuth)
      return "invalid action";
    $username = ""; $first_name = ""; $last_name = ""; $email = ""; $phone = ""; $admin = ""; $busworker = ""; $teacher = ""; $room_id = ""; $route_id = "";
    
    switch ($act) {
      case "register":
        break;
      case "edit":
        if ($id==0)
          return "invalid action";
        $profile = DB::queryFirstRow("SELECT * FROM ss_staff WHERE staff_id = %i",$id);
        $username = $profile["username"];
        $first_name = $profile["first_name"]; $last_name = $profile["last_name"];
        $email = $profile["email"]; $phone = $profile["phone"];
        $room_id = (isset($profile["room_id"]))? $profile["room_id"] : -1;
        $route_id = (isset($profile["route_id"]))? $profile["route_id"] : -1;
        $teacher = ($room_id>=0)? " checked='checked'" : "";
        $busworker = ($route_id>=0)? " checked='checked'" : "";
        break;
      default:
        return "invalid action";
        break;
    }
    
    $routes   = DB::queryFullColumns("SELECT DISTINCT * FROM ss_routes");
    if ( count($routes)>=1 ) {
      $route_input =new Select_input(array("name"=>"routes","disabled"=>($busworker=="")?" disabled='disabled'":"","spacer"=>"                  "));
      $route_input->add_option("Not assigned",0);
      foreach ($routes as $route){
        $route_name = $route["ss_routes.route_name"];
        if ($route_id==$route["ss_routes.route_id"])
          $route_input->add_option($route_name,$route["ss_routes.route_id"],true);
        else
          $route_input->add_option($route_name,$route["ss_routes.route_id"]);
      }
      $routes  =$route_input->close()."";
    } else
      $routes  ="<a href=\"?view=routes#routeModal\" class=\"ui-btn ui-corner-all ui-btn-icon-right ui-icon-plus\">Add a Route?</a>";
    
    $rooms   = DB::queryFullColumns("SELECT DISTINCT * FROM ss_rooms");
    if ( count($rooms)>=1 ) {
      $rooms_input =new Select_input(array("name"=>"rooms","disabled"=>($teacher=="")?" disabled='disabled'":"","spacer"=>"                  "));
      $rooms_input->add_option("Not assigned",0);
      foreach ($rooms as $room){
        $room_name = $room["ss_rooms.room_name"];
        if ($room_id==$room["ss_rooms.room_id"])
          $rooms_input->add_option($room_name,$room["ss_rooms.room_id"],true);
        else
          $rooms_input->add_option($room_name,$room["ss_rooms.room_id"]);
      }
      $rooms  =$rooms_input->close()."";
    } else
      $rooms  ="<a href=\"?view=room#roomModal\" class=\"ui-btn ui-corner-all ui-btn-icon-right ui-icon-plus\">Add a Room?</a>";
    
    $fields = new Card(array(
      "tag"=>"form",
      "spacer"  =>"          ",
      "id" => "staff",
    ));
    $fields->add_list(array("type"=>"ul","inset"=>"true"));
    $fields->add_item("Account",true);
    $fields->add_item("<table width='100%'><tr><td width='25em'><label for='username'>Username:</label></td><td><input type='text' value='$username' name='username' /></td></tr><tr><td><label for='password'>Password:</label></td><td><input type='password' value='' name='password' /></td></tr><tr><td><label for='password2'>Repeat:</label></td><td><input type='password' placeholder='if changing password' value='' name='password2' /></td></tr></table>");
    $fields->add_item("Personal Info",true);
    $fields->add_item("<table width='100%'><tr><td width='25em'><label for='first_name'>First Name:</label></td><td><input type='text' value='$first_name' name='first_name' /></td></tr><tr><td><label for='last_name'>Last Name:</label></td><td><input type='text' value='$last_name' name='last_name' /></td></tr></table>");
    $fields->add_item("Contact",true);
    $fields->add_item("<table width='100%'><tr><td width='25em'><label for='email'>Email:</label></td><td><input type='email' value='$email' name='email' /></td></tr><tr><td><label for='phone'>Phone:</label></td><td><input type='text' value='$phone' name='phone' /></td></tr></table>");
    $fields->add_item("Assignment / Role",true);
    $fields->add_item("
                <fieldset data-role='controlgroup' data-type='horizontal'>
                  <input name='busroute' id='busroute' type='checkbox'$busworker>
                  <label for='busroute'>Bus Worker</label>
                  $routes
                </fieldset>
              ");
    $fields->add_item("
                <fieldset data-role='controlgroup' data-type='horizontal'>
                  <input name='teacher' id='teacher' type='checkbox'$teacher>
                  <label for='teacher'>Teacher</label>
                  $rooms
                </fieldset>
              ");
    return $fields->close();
  }
  require_once("includes/user_panel.php");
?>
      <header data-role="header" data-position="fixed">
        <h1><?php echo $title; ?></h1>
        <?php 
          echo TPL::button(array(
            "position"=>"left",
            "icon"=>"carat-l",
            "target"=>(isset($id)||isset($_GET["action"]))?"#":"?view=portals",
            "rel"=>(isset($id)||isset($_GET["action"]))?"back":null,
            "reverse"=>true,
            "notext"=>true
          ));
          if (!isset($_GET["action"]) && isset($id) && $hasAuth)
            echo "        ".TPL::button(array(
                "position"=>"right",
                "text"=>"Edit",
                "data-icon"=>"gear",
                "icon"=>"gear",
                "target"=>"?view=staff&action=edit&id=$id"
              ));
          else if ($hasAuth&&!isset($_GET["action"]))
            echo "        ".TPL::button(array(
                "position"=>"right",
                "text"=>"Add",
                "icon"=>"plus",
                "target"=>"?view=staff&action=register"
              )); ?>
      </header>
      <div data-role="main" id="main" class="ui-content">
        <?php echo $build_page; ?>
      </div><!-- close ui-content -->
      <?php require_once("includes/footer.php");?>