<?php

  $title=$viewsArray[$view][1];
  
  $build_page = "";
  
  $portals_list = new list_obj(array(
        "type"    =>"ul",
        "inset"   =>true,
        "spacer"  =>"        "));
  $portals_list->add_item("<a href='?view=rooms'>Classrooms</a>");
  $portals_list->add_item("<a href='?view=routes'>Routes</a>");
  $portals_list->add_item("<a href='?view=students'>Students</a>");
  $portals_list->add_item("<a href='?view=Staff' class='ui-state-disabled'>Staff</a>");
  $build_page .= $portals_list->close();
  require_once("includes/user_panel.php");
  
  $room_id = $_SESSION["user"]["ss_staff.room_id"];
  $route_id = $_SESSION["user"]["ss_staff.route_id"];
  $this_sunday = date('Y-m-d', strtotime('sunday this week'));
  
  if (isset($room_id)&&$room_id>0) {
    $room_students = new list_obj(array(
        "type"    =>"ul",
        "inset"   =>false,
        "spacer"  =>"          "
      ));
    $students = DB::queryFullColumns("SELECT * FROM ss_students WHERE `room_id` = %i ORDER BY `last_name` ASC",$room_id);
    if ( count($students)>0 ) {
      foreach ($students as $student){
        $student_name = $student["ss_students.last_name"].", ".$student["ss_students.first_name"];
        $attend = DB::queryFirstRow("SELECT `inroute`, `inclass` FROM ss_attend WHERE `student_id` = %i AND attend_date = '$this_sunday'", $student["ss_students.student_id"]);
        $inroute = (isset($attend["inroute"]))?$attend["inroute"]:0;
        $inclass = (isset($attend["inclass"]))?$attend["inclass"]:0;
        $present = ($inroute==1)? ' class=" ui-icon-tag"' : "";
        $present = ($inroute==2)? ' class=" ui-icon-navigation"' : $present;
        $present = ($inclass==1)? ' class=" ui-icon-check"' : $present;
        $room_students->add_item("<a href=\"?view=student&id=".$student["ss_students.student_id"]."\"$present>$student_name</a>");
      }
    } else
      $room_students->add_item("<a href='?view=student'><span class='ui-btn-icon-left ui-icon-alert'></span>This class has no students.</a>");
    $build_page .= "        <div data-role=\"collapsible\" data-collapsed=\"false\">\n";
    $build_page .= "          <h4>My Class</h4>\n";
    $build_page .= "          ".$room_students->close();
    $build_page .= "        </div>\n";
  }
  if (isset($route_id)&&$route_id>0) {
    $route_students = new list_obj(array(
        "type"    =>"ul",
        "spacer"  =>"          "
      ));
    $students = DB::queryFullColumns("SELECT * FROM ss_students WHERE `route_id` = %i ORDER BY `last_name` ASC",$route_id);
    if ( count($students)>0 ) {
      foreach ($students as $student){
        $student_name = $student["ss_students.last_name"].", ".$student["ss_students.first_name"];
        $attend = DB::queryFirstRow("SELECT `inroute`, `inclass` FROM ss_attend WHERE `student_id` = %i AND attend_date = '$this_sunday'", $student["ss_students.student_id"]);
        $inroute = (isset($attend["inroute"]))?$attend["inroute"]:0;
        $inclass = (isset($attend["inclass"]))?$attend["inclass"]:0;
        $present = ($inroute==1)? ' class=" ui-icon-tag"' : "";
        $present = ($inroute==2)? ' class=" ui-icon-navigation"' : $present;
        $present = ($inclass==1)? ' class=" ui-icon-check"' : $present;
        $route_students->add_item("<a href=\"?view=student&id=".$student["ss_students.student_id"]."\"$present>$student_name</a>");
      }
    } else
      $route_students->add_item("<a href='?view=student'><span class='ui-btn-icon-left ui-icon-alert'></span>This class has no students.</a>");
    $build_page .= "        <div data-role=\"collapsible\" data-collapsed=\"false\">\n";
    $build_page .= "          <h4>My Route</h4>\n";
    $build_page .= "          ".$route_students->close();
    $build_page .= "        </div>\n";
  }
?>
      <header data-role="header" data-position="fixed">
        <h1><?php echo $title; ?></h1>
        <?php echo TPL::old_button("left","gear",$_SESSION['user']['ss_staff.first_name'],"#user_panel"); ?>
      </header>
      <div data-role="main" class="ui-content">
        <?php echo $build_page; ?>
      </div><!-- close ui-content -->
      <?php require_once("includes/footer.php");?>
