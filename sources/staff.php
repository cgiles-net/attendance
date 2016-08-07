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
        $staff_list->add_item("<a href=\"?view=busworker&id=$staff_id\">$busworker_name</a>");
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
  
  /*profile*/
  
  
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