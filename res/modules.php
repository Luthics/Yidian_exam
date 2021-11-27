<?php
    require "header.php";
	function sql($sql_s){
		global $conn;
		return mysqli_query($conn, $sql_s);
	}
	function is_student_exist($stu_name,$stu_class="0",$stu_grade="0"){
		$sql="SELECT count(*) FROM students WHERE name like '%{$stu_name}%'";
		if($stu_grade != "0") $sql=$sql." AND grade='{$stu_grade}'";
		if($stu_class != "0") $sql=$sql." AND class='{$stu_class}'";
		$sql_stu = sql($sql);
		//echo $sql;
		$re_num=mysqli_fetch_row($sql_stu)[0];
		if($re_num==0){
			$re['status']=404;
			$re['body']="404 NOT FOUND";
			return json_encode($re);
		}
		else{
			$re['status']=200;
			$re['body']=$re_num;
			return json_encode($re);
		}
	}
?>