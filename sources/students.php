<?php #students!
  $title=$viewsArray[$view][1];
  $build_page = "";
  $this_sunday = date('Y-m-d', strtotime('sunday this week'));
  /* get the date of sunday this working week */
  
  if (isset($_GET['id'])) {
  
    $id=(isset($_GET["id"]))?intval($_GET["id"]):0;
    /* Get current student from database */
    $student = DB::queryFullColumns("select * from ss_students where student_id=%i",$id);
    /* die if no student */
    if (count($student)==0) { echo "no such student"; die; }
    $student = $student[0]; /* reassign array */
    
    /* get name */
    $name=$student["ss_students.first_name"]." ".$student["ss_students.last_name"];
    $title.=$name;
    
    /* demographics */
    $demographics = new Card(array(
      "title" => $name,
      "role" => "collapsible",
      "spacer" => "        ",
      "collapse" => 'true'
    ));
    $demographics->add_list(array(
        "type"    =>"ul"
    ));
    $demographics->add_item("Address:");
    $demographics->close_list();
    $build_page .= $demographics->close();
        
    /* Attendance view */
    $attendance = DB::queryFullColumns("SELECT * FROM ss_attend WHERE `student_id` = %i AND attend_date = '$this_sunday'",$id);
    if (count($attendance)<1) {
        $attend_id=0;
        $rsvp="";
        $tran="";
        $here="";
        $verse="";
        $offer="0.00";
        $visit="";
        $bonus_1="";
        $bonus_2="";
    } else {
        $rsvp=($attendance[0]["ss_attend.inroute"]==1)? " selected": "";
        $tran=($attendance[0]["ss_attend.inroute"]==2)? " selected": "";
        $attend_id=$attendance[0]["ss_attend.attend_id"];
        $here=($attendance[0]["ss_attend.inclass"])?" selected":"";
        $verse=($attendance[0]["ss_attend.memory_verse"])?" selected":"";
        $offer=(floatval($attendance[0]["ss_attend.offering"])>0)? $attendance[0]["ss_attend.offering"]:"" ;
        $visit=($attendance[0]["ss_attend.visitor"])?" selected":"";
        $bonus_1=($attendance[0]["ss_attend.bonus_1"])?" selected":"";
        $bonus_2=($attendance[0]["ss_attend.bonus_2"])?" selected":"";
    }
    
    $attend_form = new list_obj(array("type"=>"ul","inset"=>true,"spacer"=>"        "));
    if (intval($_SESSION["user"]["ss_staff.route_id"])>0||intval($_SESSION["user"]["ss_staff.route_id"])==intval($student["ss_students.route_id"]))
      $attend_form->add_item("<label for=\"inroute\">Contact:</label><select name=\"inroute\" id=\"inroute\" data-mini=\"true\"><option value='0'>No response</option><option value=\"1\"$rsvp>RSVP'd</option><option value=\"2\"$tran>Picked up</option></select>",false,"ui-field-contain");
      
    $attend_form->add_item("<label for=\"inclass\">Present:</label><span style='float:right'><select name=\"inclass\" id=\"inclass\" data-role=\"slider\" data-mini='true'><option value=\"0\">No</option><option value=\"1\"$here>Yes</option></select></span>",false,"ui-field-contain");
    $attend_form->add_item("<label for=\"offering\">Offering:</label><span style='float:right'><input name=\"number\" min='0' pattern=\"\" step=\"0.01\" id=\"offering\" value=\"$offer\" placeholder=\"0.00\" onchange=\"update_offering(); return false;\" type=\"number\" style=\"width: 5em;\"></span>",false,"ui-field-contain");
    $attend_form->add_item("<label for=\"memory_verse\">Verse:</label><span style='float:right'><select name=\"memory_verse\" id=\"memory_verse\" data-role=\"slider\" data-mini='true'><option value=\"0\">No</option><option value=\"1\"$verse>Yes</option></select></span>",false,"ui-field-contain");
    $attend_form->add_item("<label for=\"visitor\">Visitor:</label><span style='float:right'><select name=\"visitor\" id=\"visitor\" data-role=\"slider\" data-mini='true'><option value=\"0\">No</option><option value=\"1\"$visit>Yes</option></select></span>",false,"ui-field-contain");
    $attend_form->add_item("<label for=\"bonus_1\">Bonus 1:</label><span style='float:right'><select name=\"bonus_1\" id=\"bonus_1\" data-role=\"slider\" data-mini='true'><option value=\"0\">No</option><option value=\"1\"$bonus_1>Yes</option></select></span>",false,"ui-field-contain");
    $attend_form->add_item("<label for=\"bonus_2\">Bonus 2:</label><span style='float:right'><select name=\"bonus_2\" id=\"bonus_2\" data-role=\"slider\" data-mini='true'><option value=\"0\">No</option><option value=\"1\"$bonus_2>Yes</option></select></span>",false,"ui-field-contain");
    $build_page.="        ".$attend_form->close();
    /* End Attendance view */
    
    /*Notes*/
    $note_card = new card_obj(Array(
      "spacer"=>"        ",
      "id"=>"notes",
      "role"=>"collapsible",
      "title"=>"Notes"
    ));
    $dbnotes = DB::queryFullColumns("select * from ss_student_notes where student_id = %i AND visible = 1 ORDER BY `created` DESC LIMIT 5", $id);
    $notes=new list_obj(array("type"=>"ul","spacer"=>"          "));
    $flashcard="<!-- flash cards -->";
    if (count($dbnotes)>0) {
      foreach ($dbnotes as $note) {
        $staff_name = DB::queryFirstField("select first_name from ss_staff where staff_id = %i",intval($note["ss_student_notes.staff_id"]));
        $note_id = $note["ss_student_notes.note_id"];
        $notes->add_item("<a href='#note_$note_id' data-transition='slidedown' data-rel='popup'><h2>$staff_name</h2><p>".$note["ss_student_notes.note"]."</p><p class='ui-li-aside'>".$note["ss_student_notes.created"]."</p></a>");
        $flashcard.="<div data-role='popup' id='note_$note_id'><header data-role='header'><a href='#' data-rel='back' class='ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right'>Close</a><h1>Notecard</h1></header><div role='main' class='ui-content'><p>".$note["ss_student_notes.note"]."</p><p>Added by: $staff_name, at ".date('g:ia \o\n l, F jS, Y',strtotime($note["ss_student_notes.created"]))."</p></div></div>";
      }
    } else
      $notes->add_item("There are no notes currently.");
    $notes->add_item("Add note",true);
    $notes->add_item("<form name='notes' method='post'><input id='student_id' name='student_id' type='hidden' value='$id' /><input id='staff_id' name='staff_id' type='hidden' value='".$_SESSION["user"]["ss_staff.staff_id"]."' /><textarea id='note' name='note'></textarea></form><button class=\"ui-btn\" onclick='javascript:save_note();'>Save</button>");
    $note_card->add_content($notes->close());
    $note_card->add_content($flashcard);
    $build_page.=$note_card->close();
    
    
  } else { /* list students */
    $students   = DB::queryFirstColumn("SELECT DISTINCT student_id FROM ss_students ORDER BY `last_name` ASC");
    if ( count($students)>=1 ) {
      $students_list = new list_obj(array(
        "type"    =>"ul",
        "dividers"=>true,
        "inset"   =>true,
        "spacer"  =>"        ",
        "filter"  =>true,
        "search"  =>"students-search"
      ));
      foreach ($students as $student_id){
        $student_name = DB::queryFirstField("SELECT last_name FROM ss_students WHERE student_id = $student_id").", ".DB::queryFirstField("SELECT first_name FROM ss_students WHERE student_id = $student_id");
        $inroute = intval(DB::queryFirstField("SELECT inroute FROM ss_attend WHERE `student_id` = %i AND attend_date = '$this_sunday'",$student_id));
        #echo "<!-- $inroute -->";
        $inclass = intval(DB::queryFirstField("SELECT inclass FROM ss_attend WHERE `student_id` = %i AND attend_date = '$this_sunday'",$student_id));
        #echo "<!-- $inclass -->";
        $present = ($inroute)==1? ' class=" ui-icon-tag"' : "";
        $present = ($inroute)==2? ' class=" ui-icon-navigation"' : $present;
        $present = ($inclass)==1? ' class=" ui-icon-check"' : $present;
        $students_list->add_item("<a href='?view=student&id=$student_id'$present>$student_name</a>");
      }
      $build_page .= "<form class=\"ui-filterable\">\n";
      $build_page .= "          <input id=\"students-search\" data-type=\"search\" placeholder=\"Search for a first or last name...\">\n";
      $build_page .= "        </form>\n        ";
      $build_page .= $students_list->close();
    }
  }/*end all student list*/
  
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
      </header>
      
      <div data-role="main" class="ui-content">
        <?php echo $build_page; ?>
      </div><!-- close ui-content -->
      
      <!--<span class="badge badge-primary" style="float: left;margin: 7px;">Updating</span>-->
<?php require_once("includes/footer.php"); if (isset($id)){ ?>
      <script>
        $("select").bind("change",function(){
          var name     = $(this).attr("id"),
              formData = {
                "student_id"  : <?php echo $id; ?>,
                "attend_date" : "<?php echo $this_sunday ?>"
              };
          formData[name] = jQ("#"+name).val();
          console.log(name);
          console.log(formData);
          if (name!="inroute")
            jQ(this).parent().prepend('<span id="badge-'+name+'" class="badge badge-primary" style="float: left;margin: 7px;">Updating</span>');
          else
            jQ(this).parent().parent().prepend('<span id="badge-'+name+'" class="badge badge-primary" style="float: left;margin: 7px;">Updating</span>');
          jQ.post(
            "sources/save.php?type=attendance",
            formData,
            function (data){
              console.log(data);
              jQ("#badge-"+name).attr("class","badge badge-positive").text("saved");
              setTimeout(function(){jQ("#badge-"+name).remove()},1000);
<?php
if ($attend_id==0) echo <<<END
              if ($("input[name=attend_id]").val()==0)
                $("input[name=attend_id]").val(data);
END;
?>
            }
          );
        });
        function update_offering(){
          var ishere   = jQ("#inclass").val(),
              formData = {
<?php
echo <<<END
                "student_id"  : $id,
                "attend_date" : "$this_sunday",
END;
?>
                "offering" : jQ("#offering").val()
              };
          console.log("updating offering");
          if (ishere||jQ("#offering").val()==0) {
            jQ("#offering").parent().parent().prepend('<span id="offer-badge" class="badge badge-primary" style="float: left;margin: 7px;">Updating</span>');
            jQ.post(
                "sources/save.php?type=attendance",
                formData,
                function (data){
                    console.log(data);
                    $("#offer-badge").attr("class","badge badge-positive").text("saved");
                    setTimeout(function(){$("#offer-badge").remove()},1000);
                }
            );
          }
        }
        function save_note() {
          console.log($("form[name=notes]").serialize());
          jQ.post("sources/save.php?type=note",
            jQ("form[name=notes]").serialize(),
            function () {
              jQ("form[name=notes]").val("").html("");
              window.location.reload();
            }
          );
          return false;
        }
      </script><!-- --><?php } ?>
