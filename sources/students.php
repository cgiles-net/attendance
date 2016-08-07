<?php #students!
  $title=$viewsArray[$view][1];
  $build_page = "";
  $this_sunday = date('Y-m-d', strtotime('sunday this week'));
  
  if (isset($_REQUEST['id'])) {
  
    $id=(isset($_REQUEST["id"]))?$_REQUEST["id"]:0;
    $student = DB::queryFullColumns("select * from ss_students where student_id=%i",$_REQUEST["id"])[0];
    $title=$student["ss_students.last_name"].", ".$student["ss_students.first_name"];
    
    if (isset($_REQUEST["prev_view"])) {
      if ($_REQUEST["prev_view"]=="room") {}
    }
    /* get the date of sunday this working week */
    $this_student = DB::queryFullColumns("SELECT * FROM ss_students WHERE `student_id` = $id")[0];
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
      $attend_form->add_item("<label for=\"inroute\">Contact: </label><select name=\"inroute\" id=\"inroute\" data-mini=\"true\"><option value='0'>No response</option><option value=\"1\"$rsvp>RSVP'd</option><option value=\"2\"$tran>Picked up</option></select>",false,"ui-field-contain");
      
    $attend_form->add_item("<label for=\"inclass\">Present:<span style='float:right'><select name=\"inclass\" id=\"inclass\" data-role=\"slider\" data-mini='true'><option value=\"0\">No</option><option value=\"1\"$here>Yes</option></select></span></label>",false,"ui-field-contain");
    $attend_form->add_item("<label for=\"offering\">Offering:<span style='float:right'><input name=\"number\" min='0' pattern=\"\" step=\"0.01\" id=\"offering\" value=\"$offer\" placeholder=\"0.00\" onchange=\"update_offering(); return false;\" type=\"number\" style=\"width: 5em;\"></span></label>",false,"ui-field-contain");
    $attend_form->add_item("<label for=\"memory_verse\">Verse:<span style='float:right'><select name=\"memory_verse\" id=\"memory_verse\" data-role=\"slider\" data-mini='true'><option value=\"0\">No</option><option value=\"1\"$verse>Yes</option></select></span></label>",false,"ui-field-contain");
    $attend_form->add_item("<label for=\"visitor\">Visitor:<span style='float:right'><select name=\"visitor\" id=\"visitor\" data-role=\"slider\" data-mini='true'><option value=\"0\">No</option><option value=\"1\"$visit>Yes</option></select></span></label>",false,"ui-field-contain");
    $attend_form->add_item("<label for=\"bonus_1\">Bonus 1:<span style='float:right'><select name=\"bonus_1\" id=\"bonus_1\" data-role=\"slider\" data-mini='true'><option value=\"0\">No</option><option value=\"1\"$bonus_1>Yes</option></select></span></label>",false,"ui-field-contain");
    $attend_form->add_item("<label for=\"bonus_2\">Bonus 2:<span style='float:right'><select name=\"bonus_2\" id=\"bonus_2\" data-role=\"slider\" data-mini='true'><option value=\"0\">No</option><option value=\"1\"$bonus_2>Yes</option></select></span></label>",false,"ui-field-contain");
    $build_page.=$attend_form->list_close();
        
    /* Attendance view */
    
    $note_card = new card_obj(Array(
      "spacer"=>"        ",
      "id"=>"notes",
      "role"=>"collapsible",
      "title"=>"Notes"
    ));
    $dbnotes = DB::queryFullColumns("select * from ss_student_notes where student_id = %i AND visible = 1 ORDER BY `created` DESC LIMIT 5", $id);
    $notes=new list_obj(array("type"=>"ul","spacer"=>"          "));
    if (count($dbnotes)>0) {
      foreach ($dbnotes as $note)
        $notes->add_item($note["ss_student_notes.note"]."<br />"."Added by: ".DB::queryFirstField("select first_name from ss_staff where teacher_id = %i",intval($note["ss_student_notes.teacher_id"]))." on: ".$note["ss_student_notes.created"]);
    } else
      $notes->add_item("There are no notes currently.");
    $notes->add_item("Add note",true);
    $notes->add_item("<form name='notes' method='post'><input id='student_id' name='student_id' type='hidden' value='$id' /><input id='teacher_id' name='teacher_id' type='hidden' value='".$_SESSION["user"]["ss_staff.teacher_id"]."' /><textarea id='note' name='note'></textarea></form><button class=\"ui-btn\" onclick='javascript:save_note();'>Save</button>");
    $note_card->add_content($notes->list_close());
    $note_card->add_content();
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
      $build_page .= $students_list->list_close();
    }
  }/*end all student list*/
  
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
          formData[name] = $("#"+name).val();
          console.log(name);
          console.log(formData);
          if (name!="inroute")
            $(this).parent().prepend('<span id="badge-'+name+'" class="badge badge-primary" style="float: left;margin: 7px;">Updating</span>');
          else
            $(this).parent().parent().prepend('<span id="badge-'+name+'" class="badge badge-primary" style="float: left;margin: 7px;">Updating</span>');
          $.post(
            "sources/save.php?type=attendance",
            formData,
            function (data){
              console.log(data);
              $("#badge-"+name).attr("class","badge badge-positive").text("saved");
              setTimeout(function(){$("#badge-"+name).remove()},1000);
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
          var ishere   = $("#inclass").val(),
              formData = {
<?php
echo <<<END
                "student_id"  : $id,
                "attend_date" : "$this_sunday",
END;
?>
                "offering" : $("#offering").val()
              };
          console.log("updating offering");
          if (ishere||$("#offering").val()==0) {
            $("#offering").parent().parent().prepend('<span id="offer-badge" class="badge badge-primary" style="float: left;margin: 7px;">Updating</span>');
            $.post(
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
          $.post("sources/save.php?type=note",
            $("form[name=notes]").serialize(),
            function () {
              $("form[name=notes]").val("").html("");
              window.location.reload();
            }
          );
          return false;
        }
      </script><!-- --><?php } ?>
