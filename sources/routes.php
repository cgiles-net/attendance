<?php

  $title=$viewsArray[$view][1];
  
  $this_sunday = date('Y-m-d', strtotime('sunday this week'));
  
  $build_page = "";
  
  if ( empty( $_GET["id"] ) ) {
  $routes   = DB::queryFirstColumn("SELECT DISTINCT route_id FROM ss_routes");
  if ( count($routes)>=1 ) {
    $routes_list = new list_obj(array(
        "type"    =>"ul",
        "inset"   =>true));
    $routes_list->add_item("Routes",true);
    foreach ($routes as $route_id){
      $route_name = DB::queryFirstField("SELECT `route_name` FROM ss_routes WHERE `route_id` = $route_id");
      $routes_list->add_item("<a class=\"navigate-right\" href=\"?view=route&id=$route_id\"$dajax>$route_name</a>\n");
    }
    $build_page .= $routes_list->close();
  }
  if (intval($_SESSION["user"]["ss_staff.approved"])==1)
    $build_page .= "<a href=\"?view=route#routeModal\" class=\"ui-btn ui-corner-all ui-btn-icon-right ui-icon-plus\">Add a route?</a>";
  } else {
    $id=intval($_GET["id"]);
    $route_name = DB::queryFirstField("SELECT `route_name` FROM ss_routes WHERE `route_id` = %i", $id);
    $title.=$route_name;
    /* create the occupant list */
    $occupant_list = new list_obj(array(
        "type"    =>"ul",
        "inset"   =>true,
        "spacer"  =>"        "));
    
    /* get teachers in route */
    $occupant_list->add_item("Workers",true);
    $teachers   = DB::queryFirstColumn("SELECT DISTINCT staff_id FROM ss_staff WHERE route_id = %i ORDER BY `ss_staff`.`last_name` ASC", $id);
    if ( count($teachers)>0 ) {
      foreach ($teachers as $staff_id){
        $staff_name = DB::queryFirstField("SELECT last_name FROM ss_staff WHERE staff_id = $staff_id").", ".DB::queryFirstField("SELECT first_name FROM ss_staff WHERE staff_id = $staff_id");
        $occupant_list->add_item("<a href=\"?view=teacher&id=$staff_id&prev_view=route\">$staff_name</a>");
      }
    } else /* if no teachers */ 
      $occupant_list->add_item("<a href='?view=route&id=$id#routeModal'><span class='ui-btn-icon-left ui-icon-alert'></span>This route has no staff.</a>");
    $occupant_list->add_item("Students",true);
    $students   = DB::queryFirstColumn("SELECT DISTINCT student_id FROM ss_students WHERE `route_id` = %i",$id);
    if ( count($students)>0 ) {
      foreach ($students as $student_id){
        $student_name = DB::queryFirstField("SELECT `last_name` FROM ss_students WHERE `student_id` = %i",$student_id).", ".DB::queryFirstField("SELECT `first_name` FROM ss_students WHERE `student_id` = %i",$student_id);
        $inroute = DB::queryFirstField("SELECT inroute FROM ss_attend WHERE `student_id` = %i AND attend_date = '$this_sunday'",$student_id);
        $inclass = DB::queryFirstField("SELECT inclass FROM ss_attend WHERE `student_id` = %i AND attend_date = '$this_sunday'",$student_id);
        $present = ($inroute)==1? ' class=" ui-icon-tag"' : "";
        $present = ($inroute)==2? ' class=" ui-icon-navigation"' : $present;
        $present = ($inclass)==1? ' class=" ui-icon-check"' : $present;
        $occupant_list->add_item("<a href=\"?view=student&id=$student_id&prev_view=route\"$present>$student_name</a>");
      }
    } else
      $occupant_list->add_item("<a href='?view=student'><span class='ui-btn-icon-left ui-icon-alert'></span>This route has no students.</a>");
    
    $build_page .= $occupant_list->close();
    
    $hasAuth = ( $_SESSION["user"]["ss_staff.staff_id"] == DB::queryFirstField("SELECT route_captain FROM ss_routes WHERE route_id = %i",$id))?true:false;
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
        <?php if (isset($id)) if($hasAuth||intval($_SESSION["user"]["ss_staff.approved"])==1) echo TPL::old_button("right","gear","Edit","?view=route&id=$id#routeModal"); ?>
      </header>
      <div data-role="main" id="main" class="ui-content">
        <?php echo $build_page; ?>
      </div><!-- close ui-content -->
<?php

  $bus_staff = DB::queryFullColumns("SELECT DISTINCT * FROM ss_staff WHERE route_id > -1");
  $current_captain = (isset($id))?DB::queryFirstField("SELECT DISTINCT route_captain FROM ss_routes WHERE route_id = %i",$id):"-1";
  $route_name = (isset($id))?DB::queryFirstField("SELECT DISTINCT route_name FROM ss_routes WHERE route_id = %i",$id):"";
  $buildform = "";
  $formtitle = (isset($id))?"Edit Route":"Add Route";
  $savetext  = (isset($id))?"Edit Route":"Add Route";
  
  $bus_staff_select= "There are no busworkers,<br /> nominate staff to buswork first.";
  if (count($bus_staff)>0) {
    $bus_staff_select   = "\n            <select name='route_captain'>\n";
    $bus_staff_select  .= "              <option value='-1'>No Captain</option>\n";
    foreach ($bus_staff as $staff_member){
      $selected = (intval($staff_member["ss_staff.staff_id"])==$current_captain)? " selected='selected'":"";
      $bus_staff_select .= "              <option value='".$staff_member["ss_staff.staff_id"]."'$selected>".$staff_member["ss_staff.last_name"].", ".$staff_member["ss_staff.first_name"]."</option>\n";
    }
    $bus_staff_select .= "            </select>";
  }
  $formlist = new list_obj(array(
    "type"     => "ul",
    "spacer"   => "        ",
  ));
  if (isset($id)) $buildform.="<input name='route_id' type='hidden' value='$id'>";
  $formlist->add_item("<h3>$formtitle</h3>",true);
  $formlist->add_item("Route Name",true);
  $formlist->add_item("<input name=\"route_name\" type=\"text\" value=\"$route_name\">");
  $formlist->add_item("Bus Captain",true);
  $formlist->add_item($bus_staff_select);
  $formlist->add_item("<button onclick=\"validation_check()\" value=\"submit\">$savetext</button>");
  $buildform .= $formlist->close();
  
?>
      <!-- route modal -->
        
      <div data-role="panel" data-position="right" id="routeModal" data-display="overlay" data-cache="never">
        <?php echo $buildform; ?>
      </div>
      <script>
        function validation_check() {
          failed=0;
          if (jQ("input[name=route_name]").val()=="") {
            failed=1;
            jQ("input[name=route_name]").parent().after("<span class='red' style='color: red;'>Cannot be blank</span>");
            setTimeout(function(){jQ('.red').fadeOut(function(){$(this).remove()});},1000);
          }
          if (failed==0) {
            save_route();
          }
          return false;
        }
        function save_route() {
          jQ.post("sources/save.php?type=route",
            jQ("#routeModal select,#routeModal input").serialize(),
            function (data) {
              <?php if (!isset($id)) echo '$("#routeModal input,#routeModal select").val("");'; ?>
              console.log(data);
              if (data.match("Saved")) window.location.reload();
            }
          );
          return false;
        }
      </script>
      
      <?php require_once("includes/footer.php");?>
