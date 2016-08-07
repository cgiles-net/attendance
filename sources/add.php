<?php
  $type = (isset($_REQUEST["type"]))? $_REQUEST["type"]: "";
  if ($type=="student"){
    $title = "Add Student";
?>
      <header data-role="header" data-position="fixed">
        <h1><?php echo $title; ?></h1>
        <?php 
          echo TPL::button(array(
            "position"=>"left",
            "icon"=>"carat-l",
            "target"=>(! isset($id))?"#":"?view=portals",
            "rel"=>'back" data-transition="slidedown',
            "notext"=>true
          ));
        ?>
      </header>
      <div data-role="main" data-theme="a">
        <form>
          <ul data-inset="true" data-role="listview">
            <!--<li data-role="list-divider"><h3>Add Student</h3></li>-->
            <li>
              <table>
                <tr>
                  <td><input name="first_name" type="text" placeholder="First Name" /></td>
                  <td><input name="middle" type="text" placeholder="MI" maxlength="1" size="3" /></td>
                  <td><input name="last_name" type="text" placeholder="Last Name" /></td>
                </tr>
                <tr>
                  <td><label for="birthdate">Birthdate</label></td>
                  <td colspan="2"><input name="birthdate" type="date" placeholder="mm/dd/yyyy" /></td>
                </tr>
              </table>
            </li>
            <li data-role="list-divider">Contact</li>
            <li>
              <table>
                <tr>
                  <td><input name="home_phone" type="tel" placeholder="Home Phone" /></td>
                  <td><input name="cell_phone" type="tel" placeholder="Cell Phone" /></td>
                </tr>
              </table>
            </li>
            <li>
              <input name="email" type="email" placeholder="email" />
            <li data-role="list-divider">Address</li>
            <li>
              <label for="address">Street</label>
              <input name="address" type="text" placeholder="123 main st." />
            </li>
            <li>
              <table>
                <tr>
                  <td class="input-row">
                    <label for="city">City</label>
                    <input name="city" type="text" placeholder="Somewhere" />
                  </td>
                  <td class="input-row">
                    <label for="state">State</label>
                    <input name="state" type="text" placeholder="XX" size="3" />
                  </td>
                  <td class="input-row">
                    <label for="zip">Zip</label>
                    <input name="zip" type="number" placeholder="55555" size="7" />
                  </td>
                </tr>
              </table>
            </li>
          </ul>
        </form>
      </div>
  <?php } ?>