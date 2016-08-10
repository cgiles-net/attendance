<?php
    require_once '../classes/meekrodb.2.3.class.php';
    if ( isset($_GET['type']) &&
         isset($_POST["submit"]) &&
         isset($_SESSION["user"]) ) {
    
  switch($_REQUEST['type']){
    case "student":
      add_student();
      break;
    case "edit_student":
      edit_student();
      break;
    case "guardian":
      break;
    case "note":
      add_note();
      break;
    case "relationship":
      break;
    case "attendance":
      update_attend();
      break;
    case "teacher":
      add_teacher();
      break;
    case "room":
      room();
    case "edit_room":
      edit_room();
      break;
    case "route";
      route();
      break;
    default:
      header ("Location: http://".$_SERVER["SERVER_NAME"].':'.$_SERVER['SERVER_PORT']"/Attendance/");
  }

    
  } else header ("Location: http://".$_SERVER["SERVER_NAME"].':'.$_SERVER['SERVER_PORT']."/Attendance/");
    function add_teacher() {
        DB::insert('ss_staff', array(
            'first_name' => $_REQUEST['first_name'],
            'last_name'  => $_REQUEST['last_name'],
            'email'      => $_REQUEST['email'],
            'room_id'    => $_REQUEST['room_id']
        ));
        header ("Location: http://".$_SERVER["SERVER_NAME"].":81/Attendance/?view=teachers");
    }
    function route() {
      $infoArray = array(
        'route_id' => (isset($_REQUEST["route_id"]))?$_REQUEST["route_id"]:null,
        'route_name'=>$_REQUEST["route_name"],
        'route_captain'=>$_REQUEST["route_captain"],
        'display_order'=>(isset($_REQUEST["display_order"]))?$_REQUEST["display_order"]:0
      );
      DB::insertUpdate("ss_routes",$infoArray);  
      $route_id = DB::insertId();    
      DB::query("UPDATE `ss_staff` SET `route_id` = '%i' WHERE `ss_staff`.`staff_id` = %i;",$route_id,$_REQUEST['route_captain']);
      echo "Saved";
    }
    function room() {
      $infoArray = array(
        'room_id' => (isset($_REQUEST["room_id"]))?$_REQUEST["room_id"]:null,
        'room_name'=>$_REQUEST["room_name"],
        'room_captain'=>$_REQUEST["room_captain"],
        'display_order'=>(isset($_REQUEST["display_order"]))?$_REQUEST["display_order"]:0
      );
      DB::insertUpdate("ss_rooms",$infoArray);
      $room_id = DB::insertId();
      DB::query("UPDATE `ss_staff` SET `room_id` = '%i' WHERE `ss_staff`.`staff_id` = %i;",$room_id,$_REQUEST['room_captain']);
      echo "Saved";
    }
    /*function add_room() {
        DB::insert('ss_rooms', array(
            'room_name'    => $_REQUEST['room_name'],
            'room_captain' => $_REQUEST['room_captain']
        ));
    $room_id = DB::insertId();
    DB::query("UPDATE `ss_staff` SET `room_id` = '%i' WHERE `ss_staff`.`staff_id` = %i;",$room_id,$_REQUEST['room_captain']);
        header ("Location: http://".$_SERVER["SERVER_NAME"].":81/Attendance/?view=rooms");
    }
    function edit_room() {
        DB::insertUpdate('ss_rooms', array(
      'room_id'      => $_REQUEST['room_id'],
            'room_name'    => $_REQUEST['room_name'],
            'room_captain' => $_REQUEST['room_captain']
        ));
    DB::query("UPDATE `ss_staff` SET `room_id` = '%i' WHERE `ss_staff`.`staff_id` = %i;",$_REQUEST['room_id'],$_REQUEST['room_captain']);
        header ("Location: http://".$_SERVER["SERVER_NAME"].":81/Attendance/view=room&id=".$_REQUEST['room_id']);
    }*/
  function add_student() {
        DB::insert('ss_students', array(
      'last_name'  => $_REQUEST['last_name'],
      'first_name' => $_REQUEST['first_name'],
      'middle'     => $_REQUEST['middle'],
      'birthdate'  => $_REQUEST['birthdate'],
      'phone'      => $_REQUEST['phone'],
      'email'     => $_REQUEST['email'],
      'address'    => $_REQUEST['address'],
      'city'     => $_REQUEST['city'],
      'state'     => $_REQUEST['state'],
      'zip'     => $_REQUEST['zip'],
      'room_id'   => $_REQUEST['room_id'],
      'route_id'   => $_REQUEST['route_id']
    ));
        header ("Location: http://".$_SERVER["SERVER_NAME"].":81/Attendance/?view=students");
  }
  function edit_student() {
        DB::insertUpdate('ss_students', array(
      'student_id' => $_REQUEST['student_id'],
      'last_name'  => $_REQUEST['last_name'],
      'first_name' => $_REQUEST['first_name'],
      'middle'     => $_REQUEST['middle'],
      'birthdate'  => $_REQUEST['birthdate'],
      'phone'      => $_REQUEST['phone'],
      'email'     => $_REQUEST['email'],
      'address'    => $_REQUEST['address'],
      'city'     => $_REQUEST['city'],
      'state'     => $_REQUEST['state'],
      'zip'     => $_REQUEST['zip'],
      'room_id'   => $_REQUEST['room_id'],
      'route_id'   => $_REQUEST['route_id']
    ));
        header ("Location: http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/attendance/?view=students");
  }
  function update_student() {
    $updateArray =  array(
      'student_id' => $_REQUEST['student_id'],
    );
    if (isset($_REQUEST['last_name']))  $updateArray['last_name']= $_REQUEST['last_name'];
    if (isset($_REQUEST['first_name'])) $updateArray['first_name'] = $_REQUEST['first_name'];
    if (isset($_REQUEST['middle']))     $updateArray['middle']   = $_REQUEST['middle'];
    if (isset($_REQUEST['birthdate']))  $updateArray['birthdate']= $_REQUEST['birthdate'];
    if (isset($_REQUEST['phone']))      $updateArray['phone']    = $_REQUEST['phone'];
    if (isset($_REQUEST['email']))      $updateArray['email']    = $_REQUEST['email'];
    if (isset($_REQUEST['address']))    $updateArray['address']  = $_REQUEST['address'];
    if (isset($_REQUEST['city']))       $updateArray['city']     = $_REQUEST['city'];
    if (isset($_REQUEST['state']))      $updateArray['state']    = $_REQUEST['state'];
    if (isset($_REQUEST['zip']))        $updateArray['zip']      = $_REQUEST['zip'];
    if (isset($_REQUEST['room_id']))    $updateArray['room_id']  = $_REQUEST['room_id'];
    if (isset($_REQUEST['route_id']))   $updateArray['route_id'] = $_REQUEST['route_id'];
    
    DB::insertUpdate('ss_students', $updateArray);
    
  }
  function update_attend() {
    /* 
      attend_array: we want to insert (create) an entry for the student if we don't have one for the current sunday. if we do have one, then we want to update the entry instead. But since we want to allow updates to happen individually we use the structure below to add the (key => value) pairing to the array only if submitted.
    */
    
    $attendArray = array();
    /* only add key to array if value exists from ajax */
    /* check to see if attendance already exists for student this week */
    $attend_id = DB::queryFirstField("SELECT `attend_id` FROM ss_attend WHERE `student_id` = %i AND attend_date = %s",$_REQUEST["student_id"],$_REQUEST["attend_date"]);
    $attendArray["attend_id"] = ($attend_id!="")?$attend_id:null;
    $attendArray["student_id"] = $_REQUEST["student_id"];
    $attendArray["attend_date"] = $_REQUEST["attend_date"];
    if (isset($_REQUEST["inroute"]))
      $attendArray["inroute"] = $_REQUEST["inroute"];
    if (isset($_REQUEST["inclass"]))
      $attendArray["inclass"] = $_REQUEST["inclass"];
    if (isset($_REQUEST["memory_verse"]))
      $attendArray["memory_verse"] = $_REQUEST["memory_verse"];
    if (isset($_REQUEST["offering"]))
      $attendArray["offering"] = $_REQUEST["offering"];
    if (isset($_REQUEST["visitor"]))
      $attendArray["visitor"] = $_REQUEST["visitor"];
    if (isset($_REQUEST["bonus_1"]))
      $attendArray["bonus_1"] = $_REQUEST["bonus_1"];
    if (isset($_REQUEST["bonus_2"]))
      $attendArray["bonus_2"] = $_REQUEST["bonus_2"];
    DB::insertUpdate(
      'ss_attend',
      $attendArray
    );
    foreach($attendArray as $key => $value) {
      echo "$key => $value\n";
    }
    echo $attend_id;
  }
  function add_note () {
    DB::insert("ss_student_notes",array(
      "student_id"=>$_REQUEST["student_id"],
      "staff_id"=>$_REQUEST["staff_id"],
      "note"=>$_REQUEST["note"]
    ));
  }
?>