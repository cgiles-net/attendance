<?php
/*
load attendance record where verified is not ture.
loop through unverified records
	load student where student_id matches unverified record.
	update points total.
	update verifed = true.
end loop.
*/

  require_once 'meekrodb.2.3.class.php';
	
	$attend_records = DB::queryFullColumns("SELECT * FROM ss_attend WHERE verified IS NOT TRUE");
	
	foreach ( $attend_records as $record ) {
    $new_points; $student; $current; $lifetime;
    $new_points  = ($record["ss_attend.inclass"])? 1: 0;
    $new_points += ($record["ss_attend.memory_verse"])? 1: 0;
    $new_points += (ceil(floatval($record["ss_attend.offering"]))>0)? 1: 0;
    $new_points += ($record["ss_attend.visitor"])? 1: 0;
    $new_points += ($record["ss_attend.bonus_1"])? 1: 0;
    $new_points += ($record["ss_attend.bonus_2"])? 1: 0;
    $student 	 = DB::queryFirstRow("SELECT * FROM ss_students WHERE student_id = %i",$record["ss_attend.student_id"]);
		
		$current = intval($student["current_points"]) + $new_points;
		$lifetime = intval($student["lifetime_points"]) + $new_points;
		
    echo $current."<br>\n";
    echo $lifetime."<br>\n";
    
		DB::insertUpdate("ss_attend",array(
      "attend_id" => $record["ss_attend.attend_id"],
			"verified" => TRUE
		));
		
		DB::insertUpdate("ss_students",array(
			"student_id" => $record["ss_attend.student_id"],
			"current_points" => $current,
			"lifetime_points" => $lifetime
		));
	}
	
?>