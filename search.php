<?php
    require "res/header.php";
	function sql($sql_s){
		global $conn;
		return mysqli_query($conn, $sql_s);
	}
	$name=$_REQUEST["name"];
	$cols=sql("show columns from list");
	for($lid=0;$row_c=mysqli_fetch_row($cols);$lid++){
		$exam_s[]=$row_c[0];
		$exam_struct[$row_c[0]]=$lid;
	}
	$cols=sql("show columns from students");
	for($lid=0;$row_c=mysqli_fetch_row($cols);$lid++){
		$col_s[]=$row_c[0];
		$student_struct[$row_c[0]]=$lid;
	}
	$sql="SELECT * FROM `list` ORDER BY time";
	$examlist=sql($sql);
	for($lid=0;$row=mysqli_fetch_row($examlist);$lid++){
		foreach($row as $key=>$data){
			$exam_list[$row[$exam_struct['grade']]][$row[$exam_struct['kind']]][$lid][$exam_s[$key]]=$data;
		}
	}
	function getDetail($stu_name,$stu_grade,$id,$c){
		global $exam_struct,$exam_list;
		$t="exam.php?name=".$stu_name."&class=".$c."&grade=".$stu_grade;
        $r='
                <tr id="hidden-1">
                	<td colspan="4" style="padding:0px">
                		<div class="collapse" id="'.$id.'" aria-expanded="true" style="">
                			<font style="font-size:xx-large">
                				'.$stu_name.'
                			</font>
                			<font style="font-size:large">
                				,现在在高三 '.$c.' 班 
                			</font>
                			<a href="'.$t.'">查看历次考试统计</a>
                			<table class="table">
                				<thead>
                					<tr>
                						<th>
                							时间
                						</th>
                						<th>
                							考试
                						</th>
                						<th>
                							分数
                						</th>
                						<th>
                							排名
                						</th>
                					</tr>
                				</thead>
                				<tbody>';
		if($c[0]=='L') $tid=1;
		else if($c[0]=='W') $tid=2;
		else $tid=0;
		foreach($exam_list[$stu_grade][$tid] as $row){
			$sql="SELECT count(*) FROM ".$stu_grade."_".$row['name']." WHERE 姓名='{$stu_name}'";
			$re_count=sql($sql);
			if(mysqli_fetch_row($re_count)[0]==0) continue;
			$sql="SELECT * FROM `".$stu_grade."_".$row['name']."` WHERE 姓名 like '{$stu_name}' and 班级 like '{$c}'";
			$testdetail=sql($sql);
			$col_s=[];
			$sql="show columns from ".$stu_grade."_".$row['name'];
			$cols=sql($sql);
			while($row_c=mysqli_fetch_row($cols)){
				$col_s[]=$row_c[0];
			}
			while($row_r=mysqli_fetch_row($testdetail)){
				foreach($row_r as $rol_r => $data_r){
					if($col_s[$rol_r] == "总分"){
						$score=$data_r;
					}
					if($col_s[$rol_r] == "校次"){
						$rank=$data_r;
					}
				}
			}
				$r=$r.'                        					
						<tr>
							<th scope="row">
								'.$row['time'].'
							</th>
							<td>
								'.$row['name'].'
							</td>
							<td>
								'.$score.'
							</td>
							<td>
								'.$rank.'
							</td>
						</tr>
											';
		}
        $r=$r.'</tbody>
                        			</table>
                        		</div>
                        	</td>
                        </tr>';
	    return [$r,$rank];
	}
	function getBrief($stu_name_brief){
		global $student_struct;
		$skeywords=explode(" ",$stu_name_brief);
		$sql="SELECT * FROM `students` WHERE";
		foreach($skeywords as $key=>$skeyword){
			$sql = $sql."(name like '%{$skeyword}%' or class like '%{$skeyword}%' or grade like '%{$skeyword}%')";
			if($key+1 != count($skeywords)) $sql=$sql."and";
		}
		//$sql = $sql."LIMIT 200"; //性能不足时开启，限制搜索上限
	    $namelist=sql($sql);
		$s="";
	    while($row=mysqli_fetch_row($namelist)){
	        $r = getDetail($row[$student_struct['name']],$row[$student_struct['grade']],$row[$student_struct['id']],$row[$student_struct['class']]);
	        $s=$s.'                        <tr role="button" id="row-1" data-toggle="collapse" data-target="#'.$row[$student_struct['id']].'" aria-expanded="false" aria-controls="'.$row[$student_struct['id']].'">
                        	<th scope="row">
                        		'.$row[$student_struct['grade']].'
                        	</th>
                        	<td>
                        		'.$row[$student_struct['class']].'
                        	</td>
                        	<td>
                        		'.$row[$student_struct['name']].'
                        	</td>
                        	<td>
                        		'.$r[1].'
                        	</td>
                        </tr>'.$r[0];
	    }
	    return $s;
	}
	$sql="INSERT INTO `log` (`id`, `time`, `ip`, `keyword`) VALUES (NULL, '".date("Y-m-d H:i:s")."', '".$_SERVER["REMOTE_ADDR"]."', '".$name."')
";
    sql($sql);
	echo getBrief($name);
?>