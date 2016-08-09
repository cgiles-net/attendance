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
      $rooms_list->add_item("<a href=\"?view=room&id=$room_id\"$dajax>$room_name</a>\n");
    }
    $build_page .= $rooms_list->close();
  }
  if (intval($_SESSION["user"]["ss_staff.approved"])==1)
    $build_page .= "<a href=\"?view=room#roomModal\" class=\"ui-btn ui-corner-all ui-btn-icon-right ui-icon-plus\">Add a Room?</a>";
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
    $staff   = DB::queryFullColumns("SELECT * FROM ss_staff WHERE room_id = %i ORDER BY `ss_staff`.`last_name` ASC", $id);
    if ( count($staff)>0 ) {
      foreach ($staff as $teacher){
        $teacher_name = $teacher["ss_staff.last_name"].", ".$teacher["ss_staff.first_name"];
		$staff_id = $teacher["ss_staff.staff_id"];
        $occupant_list->add_item("<a href=\"?view=teacher&id=$staff_id\" class='ui-state-disabled'>$teacher_name</a>");
      }
    } else /* if no teachers */ 
      $occupant_list->add_item("<a href='?view=room&id=$id#roomModal'><span class='ui-btn-icon-left ui-icon-alert'></span>This class has no teachers.</a>");
    $occupant_list->add_item("Students",true);
    $students = DB::queryFullColumns("SELECT * FROM ss_students WHERE `room_id` = %i",$id);
    if ( count($students)>0 ) {
      foreach ($students as $student){
        $student_name = $student["ss_students.first_name"]." ".$student["ss_students.last_name"];
        $attend = DB::queryFirstRow("SELECT `inroute`, `inclass` FROM ss_attend WHERE `student_id` = %i AND attend_date = '$this_sunday'", $student["ss_students.student_id"]);
        $inroute = (isset($attend["inroute"]))?$attend["inroute"]:0;
        $inclass = (isset($attend["inclass"]))?$attend["inclass"]:0;
        $present = ($inroute==1)? ' class=" ui-icon-tag"' : "";
        $present = ($inroute==2)? ' class=" ui-icon-navigation"' : $present;
        $present = ($inclass==1)? ' class=" ui-icon-check"' : $present;
        $occupant_list->add_item("<a href=\"?view=student&id=".$student["ss_students.student_id"]."\"$present>$student_name</a>");
      }
    } else
      $occupant_list->add_item("<a href='?view=student'><span class='ui-btn-icon-left ui-icon-alert'></span>This class has no students.</a>");
    
    $build_page .= $occupant_list->close();
    
    $hasAuth = ( $_SESSION["user"]["ss_staff.staff_id"] == DB::queryFirstField("SELECT room_captain FROM ss_rooms WHERE room_id = %i",$id))?true:false;
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
        <?php if (isset($id)) if($hasAuth||intval($_SESSION["user"]["ss_staff.approved"])==1) echo TPL::old_button("right","gear","Edit","?view=room&id=$id#roomModal"); ?>
      </header>
      <div data-role="main" id="main" class="ui-content">
        <?php echo $build_page; ?>
      </div><!-- close ui-content -->
      <?php require_once("includes/footer.php");?>
      
<?php

  $room_staff = DB::queryFullColumns("SELECT DISTINCT * FROM ss_staff WHERE room_id > -1");
  $current_captain = (isset($id))?DB::queryFirstField("SELECT DISTINCT room_captain FROM ss_rooms WHERE room_id = %i",$id):"-1";
  $room_name = (isset($id))?DB::queryFirstField("SELECT DISTINCT room_name FROM ss_rooms WHERE room_id = %i",$id):"";
  $buildform = "";
  $formtitle = (isset($id))?"Edit Room":"Add Room";
  $savetext  = (isset($id))?"Edit Room":"Add Room";
  
  $room_staff_select= "There are no Teachers,<br /> nominate staff to teaching first.";
  if (count($room_staff)>0) {
    $room_staff_select   = "\n            <select name='room_captain'>\n";
    $room_staff_select  .= "              <option value='-1'>No Captain</option>\n";
    foreach ($room_staff as $staff_member){
      $selected = (intval($staff_member["ss_staff.staff_id"])==$current_captain)? " selected='selected'":"";
      $room_staff_select .= "              <option value='".$staff_member["ss_staff.staff_id"]."'$selected>".$staff_member["ss_staff.last_name"].", ".$staff_member["ss_staff.first_name"]."</option>\n";
    }
    $room_staff_select .= "            </select>";
  }
  $formlist = new list_obj(array(
    "type"     => "ul",
    "spacer"   => "        ",
  ));
  if (isset($id)) $buildform.="<input name='room_id' type='hidden' value='$id'>";
  $formlist->add_item("<h3>$formtitle</h3>",true);
  $formlist->add_item("Classoom Name",true);
  $formlist->add_item("<input name=\"room_name\" type=\"text\" value=\"$room_name\">");
  $formlist->add_item("Room Captain",true);
  $formlist->add_item($room_staff_select);
  $formlist->add_item("<button onclick=\"validation_check()\" value=\"submit\">$savetext</button>");
  $buildform .= $formlist->close();
  
?>
      <!-- room modal -->
        
      <div data-role="panel" data-position="right" id="roomModal" data-display="overlay" data-cache="never">
        <?php echo $buildform; ?>
      </div>
      <script>
        function validation_check() {
          failed=0;
          if (jQ("input[name=room_name]").val()=="") {
            failed=1;
            jQ("input[name=room_name]").parent().after("<span class='red' style='color: red;'>Cannot be blank</span>");
            setTimeout(function(){jQ('.red').fadeOut(function(){$(this).remove()});},1000);
          }
          if (failed==0) {
            save_form();
          }
          return false;
        }
        function save_form() {
          jQ.post("sources/save.php?type=room",
            jQ("#roomModal select,#roomModal input").serialize(),
            function (data) {
              <?php if (!isset($id)) echo '$("#roomModal input,#roomModal select").val("");'; ?>
              console.log(data);
              if (data.match("Saved")) window.location.reload();
            }
          );
          return false;
        }
      </script>