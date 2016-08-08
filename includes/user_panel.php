<?php 
  $ucp_nav = new list_obj(array(
        "type"    =>"ul",
        "spacer"  =>"        "));
  $ucp_nav->add_item("Welcome back: ".$_SESSION['user']["ss_staff.first_name"]." ".$_SESSION['user']["ss_staff.last_name"], true);
  $ucp_nav->add_item("<a href='?view=portals' class='ui-icon-home ui-nodisc-icon ui-alt-icon' style='padding-left: 1.5em;'></span>Home</a>");
  $ucp_nav->add_item("<a href='?view=profile&action=edit'>Edit My Profile</a>");
  $ucp_nav->add_item("<a href='javascript:jQ.get(\"?view=login&act=2\",function(){window.location.replace(\"http://\"+window.location.host+window.location.pathname)});'>Log Out</a>");
?>
      <div data-role="panel" id="user_panel" data-display="overlay">
        <?php echo $ucp_nav->close(); ?>
      </div><!-- /panel -->
