<?php

  $title=$viewsArray[$view][1];
  $portalsArray;
  $this_sunday = date('Y-m-d', strtotime('sunday this week'));
  
  $build_page = "";
  
  if ( ! isset( $_REQUEST["id"] ) ) {
  $rooms   = DB::queryFirstColumn("SELECT DISTINCT room_id FROM ss_rooms");
  if ( count($rooms)>=1 ) {
    $rooms_list = new list_obj(array(
        "type"    =>"ul",
        "inset"   =>true));
    $rooms_list->add_item("Classrooms",true);
    foreach ($rooms as $room_id){
      $room_name = DB::queryFirstField("SELECT `room_name` FROM ss_rooms WHERE `room_id` = $room_id");
      $rooms_list->add_item("<a class=\"navigate-right\" href=\"?view=room&id=$room_id\"$dajax>$room_name</a>\n");
    }
    $build_page .= $rooms_list->list_close();
  }
  if (intval($_SESSION["user"]["ss_staff.approved"])==1)
    $build_page .= "<a href=\"?view=room#createModal\" class=\"ui-btn ui-corner-all ui-btn-icon-right ui-icon-plus\">Add a Room?</a>";
  } else {
    $id=$_REQUEST["id"];
    $room_name = DB::queryFirstField("SELECT `room_name` FROM ss_rooms WHERE `room_id` = %i", $id);
    $title.=$room_name;
    /* create the occupant list */
    $occupant_list = new list_obj(array(
        "type"    =>"ul",
        "inset"   =>true,
        "spacer"  =>"        "));
    
    /* get teachers in room */
    $occupant_list->add_item("Teachers",true);
    $teachers   = DB::queryFirstColumn("SELECT DISTINCT teacher_id FROM ss_staff WHERE room_id = %i ORDER BY `ss_staff`.`last_name` ASC", $id);
    if ( count($teachers)>0 ) {
      foreach ($teachers as $teacher_id){
        $teacher_name = DB::queryFirstField("SELECT last_name FROM ss_staff WHERE teacher_id = $teacher_id").", ".DB::queryFirstField("SELECT first_name FROM ss_staff WHERE teacher_id = $teacher_id");
        $occupant_list->add_item("<a href=\"?view=teacher&id=$teacher_id&prev_view=room\">$teacher_name</a>");
      }
    } else /* if no teachers */ 
      $occupant_list->add_item("<a href='?view=room&id=$id#editModal'><span class='ui-btn-icon-left ui-icon-alert'></span>This class has no teachers.</a>");
    $occupant_list->add_item("Students",true);
    $students   = DB::queryFirstColumn("SELECT DISTINCT student_id FROM ss_students WHERE `room_id` = %i",$id);
    if ( count($students)>0 ) {
      foreach ($students as $student_id){
        $student_name = DB::queryFirstField("SELECT `last_name` FROM ss_students WHERE `student_id` = %i",$student_id).", ".DB::queryFirstField("SELECT `first_name` FROM ss_students WHERE `student_id` = %i",$student_id);
        $inroute = DB::queryFirstField("SELECT inroute FROM ss_attend WHERE `student_id` = %i AND attend_date = '$this_sunday'",$student_id);
        $inclass = DB::queryFirstField("SELECT inclass FROM ss_attend WHERE `student_id` = %i AND attend_date = '$this_sunday'",$student_id);
        $present = ($inroute)==1? ' class=" ui-icon-tag"' : "";
        $present = ($inroute)==2? ' class=" ui-icon-navigation"' : $present;
        $present = ($inclass)==1? ' class=" ui-icon-check"' : $present;
        $occupant_list->add_item("<a href=\"?view=student&id=$student_id&prev_view=room\"$present>$student_name</a>");
      }
    } else
      $occupant_list->add_item("<a href='?view=student'><span class='ui-btn-icon-left ui-icon-alert'></span>This class has no students.</a>");
    
    $build_page .= $occupant_list->list_close();
    
    $hasAuth = ( $_SESSION["user"]["ss_staff.teacher_id"] == DB::queryFirstField("SELECT room_captain FROM ss_rooms WHERE room_id = %i",$id))?true:false;
  }
  
  require_once("includes/user_panel.php");
?>
      <header data-role="header" data-position="fixed">
        <h1><?php echo $title; ?></h1>
        <?php echo TPL::button(array(
          "position"=>"left",
          "icon"=>"carat-l",
          "target"=>"#",
          "rel"=>"back",
          "notext"=>true
        )); ?>
        <?php if (isset($id)) if($hasAuth||intval($_SESSION["user"]["ss_staff.approved"])==1) echo TPL::old_button("right","gear","Edit","?view=room&id=$id#editModal"); ?>
      </header>
      <div data-role="main" id="main" class="ui-content">
        <?php echo $build_page; ?>
      </div><!-- close ui-content -->
      <?php require_once("includes/footer.php");?>
      <!-- create modal -->
      <div data-role="panel" data-position="right" data-display="overlay" id="createModal" data-cache="never">
        <form class="input-group" method="post" action="sources/save.php?type=room"><div class="card">
          <div class='table-view-cell table-view-divider'>Room Name</div>
          <input name="room_name" type="text">
          <div class='table-view-cell table-view-divider'>Room Captain</div>
<?php
  $teachers   = DB::queryFirstColumn("SELECT DISTINCT teacher_id FROM ss_staff WHERE room_id IS NOT NULL");
  if ( count($teachers)<1 ) echo "            <a href=\"teachers.php\">\n          <span class='btn btn-block'>There are no teachers, add a teacher first.</span>\n            </a>\n";
  else {
    echo "          <select name='room_captain' class='table-view-cell'>\n";
    foreach ($teachers as $teacher_id){
      $teacher_name = DB::queryFirstField("SELECT last_name FROM ss_staff WHERE teacher_id = $teacher_id").", ".DB::queryFirstField("SELECT first_name FROM ss_staff WHERE teacher_id = $teacher_id");
      echo "            <option value=".$teacher_id."\">$teacher_name</option>\n";
    }
    echo "          </select>\n";
  }
?>
          <!--<input name="passphrase" type="password" placeholder="password">--></div>
          <div class="content-padded"><button class="btn btn-positive btn-block" type="submit" value="submit">Add Room</button></div>
        </form>
      </div>
      
      <!-- Compose modal -->
      <div data-role="panel" data-position="right" data-display="overlay" id="editModal" data-cache="never">
          <form class="input-group" method="post" action="sources/save.php?type=edit_room"><div class="card">
            <input type="hidden" name="room_id" value="<?php echo $id; ?>" />
            <div class='table-view-cell table-view-divider'>Room Name</div>
            <input name="room_name" type="text" value="<?php echo $room_name; ?>">
            <div class='table-view-cell table-view-divider'>Room Captain</div>
  <?php
    $current_captain = DB::queryFirstColumn("SELECT DISTINCT room_captain FROM ss_rooms WHERE room_id = %i",$id);
    if (isset($current_captain))
      if (count($current_captain)>0)
          $current_captain=$current_captain[0];
    $teachers   = DB::queryFirstColumn("SELECT DISTINCT teacher_id FROM ss_staff WHERE room_id IS NOT NULL");
    if ( count($teachers)<1 ) echo "            <a href=\"teachers.php\">\n          <span class='btn btn-block'>There are no teachers, add a teacher first.</span>\n            </a>\n";
    else {
      echo "          <select name='room_captain' class='table-view-cell'>\n";
      foreach ($teachers as $teacher_id){
          $teacher_name = DB::queryFirstField("SELECT last_name FROM ss_staff WHERE teacher_id = $teacher_id").", ".DB::queryFirstField("SELECT first_name FROM ss_staff WHERE teacher_id = $teacher_id");
          if ($teacher_id == $current_captain)
              echo "            <option value=\"$teacher_id\" selected='selected'>$teacher_name</option>\n";
          else
              echo "            <option value=\"$teacher_id\">$teacher_name</option>\n";
              
      }
      echo "          </select>\n";
    }
  ?>
            <!--<input name="passphrase" type="password" placeholder="password">--></div>
            <div class="content-padded"><button class="btn btn-positive btn-block" type="submit" value="submit">Update Room</button></div>
          </form>
      </div><!-- /.modal -->