<?php
  /* Staff page */
  $title=$viewsArray[$view][1];
  $build_page="";
  
  /* list staff */
  if (!isset($_REQUEST["id"])&&$view!="profile") {
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
  } else
    $id = (isset($_REQUEST["id"]))? $_REQUEST["id"] : $_SESSION["user"]["ss_staff.staff_id"];
  
  
  $hasAuth = (!!$_SESSION["user"]["ss_staff.approved"]);
  /*profile*/
  if (isset($id)&&!isset($_REQUEST["action"])) {
    $profile = DB::queryFirstRow("select * from ss_staff where staff_id = %i",$id);
    $hasAuth = ($_SESSION["user"]["ss_staff.staff_id"]==$id)?true:$hasAuth;
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
    if ($room_id>0) {
      $room_name = DB::queryFirstField("SELECT `room_name` FROM ss_rooms WHERE `room_id` = %i", $room_id);
      $profile_list->add_item("Classroom",true);
      $profile_list->add_item("<a href=\"?view=room&id=$room_id\">$room_name</a>");
    }
    $route_id = $profile["route_id"];
    if ($route_id>0) {
      $route_name = DB::queryFirstField("SELECT `route_name` FROM ss_routes WHERE `route_id` = %i", $route_id);
      $profile_list->add_item("Route",true);
      $profile_list->add_item("<a href=\"?view=route&id=$route_id\">$route_name</a>");
    }
    $build_page .= $profile_list->close();
  }
  
  if (isset($_REQUEST["action"])) {
    if ($_REQUEST["action"]=="edit") {
      
    }
    if ($_REQUEST["action"]=="register") {
      
    }
  }
  
  require_once("includes/user_panel.php");
?>
      <header data-role="header" data-position="fixed">
        <h1><?php echo $title; ?></h1>
        <?php 
          echo TPL::button(array(
            "position"=>"left",
            "icon"=>"carat-l",
            "target"=>(isset($id))?"#":"?view=portals",
            "rel"=>(isset($id))?"back":null,
            "reverse"=>true,
            "notext"=>true
          ));
        ?>
        <?php if (isset($id)) if($hasAuth||intval($_SESSION["user"]["ss_staff.approved"])==1) echo TPL::old_button("right","gear","Edit","?view=staff&action=edit&id=$id"); else echo TPL::old_button("right","gear","Add","?view=staff&action=register&id=$id"); ?>
      </header>
      <div data-role="main" id="main" class="ui-content">
        <?php echo $build_page; ?>
      </div><!-- close ui-content -->
      <?php require_once("includes/footer.php");?>