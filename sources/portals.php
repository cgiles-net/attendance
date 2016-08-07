<?php

  $title=$viewsArray[$view][1];
  
  $build_page = "";
  
  $portals_list = new list_obj(array(
        "type"    =>"ul",
        "inset"   =>true,
        "spacer"  =>"        "));
  $portals_list->add_item("<a href='?view=rooms'>Classrooms</a>");
  $portals_list->add_item("<a href='?view=students'>Students</a>");
  $portals_list->add_item("<a href='?view=routes'>Routes</a>");
  $build_page .= $portals_list->list_close();
  require_once("includes/user_panel.php");
?>
      <header data-role="header" data-position="fixed">
        <h1><?php echo $title; ?></h1>
        <?php echo TPL::old_button("left","gear",$_SESSION['user']['ss_staff.first_name'],"#user_panel"); ?>
      </header>
      <div data-role="main" class="ui-content">
        <?php echo $build_page; ?>
      </div><!-- close ui-content -->
      <?php require_once("includes/footer.php");?>
