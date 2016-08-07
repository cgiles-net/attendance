
      <div data-role="footer" style="overflow:hidden;" data-position="fixed">
        <div data-role="navbar" data-iconpos="left">
          <ul>
            <li>
              <?php echo TPL::button(array(
                "data-icon"=>"gear",
                "target"=>"#user_panel",
                "corners"=>false,
                "text"=>$_SESSION['user']['ss_staff.first_name']
              )); ?>
            </li>
            <li><a href="#" class="ui-state-disabled">Search</a></li>
            <li><a href="?view=form&type=student" class="ui-btn ui-btn-icon-left ui-icon-plus" data-transition="slideup">Add Student</a></li>
          </ul>
        </div><!-- /navbar -->
      </div><!-- /footer -->
