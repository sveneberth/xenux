<?php
if(isset($_GET['timestamp']) and preg_match("/[0-9]/", $_GET['timestamp'])) {
	$date = $_GET['timestamp'];
} else {
	$date = time();
}

function monthBack($timestamp) {
	return mktime(0,0,0, date("m",$timestamp)-1,date("d",$timestamp),date("Y",$timestamp));
}
function yearBack($timestamp) {
	return mktime(0,0,0, date("m",$timestamp),	date("d",$timestamp),date("Y",$timestamp)-1);
}
function monthForward($timestamp) {
	return mktime(0,0,0, date("m",$timestamp)+1,date("d",$timestamp),date("Y",$timestamp));
}
function yearForward($timestamp) {
	return mktime(0,0,0, date("m",$timestamp),	date("d",$timestamp),date("Y",$timestamp)+1);
}
?>
<div class="calender">
	<menu>
		<a class="btn" style="float:left;" href="?<?php echo "site=$site->site"; ?>" class="now">Heute</a>
		<a class="btn" href="?<?php echo "site=$site->site&timestamp=".yearBack($date); ?>" class="last">&laquo;</a>
		<a class="btn" href="?<?php echo "site=$site->site&timestamp=".monthBack($date); ?>" class="last">&lt;</a>
		<span><?php echo $month_DE[date('n',$date)];?> <?php echo date('Y',$date); ?></span>
		<a class="btn" href="?<?php echo "site=$site->site&timestamp=".monthForward($date); ?>" class="next">&gt;</a>
		<a class="btn" href="?<?php echo "site=$site->site&timestamp=".yearForward($date); ?>" class="next">&raquo;</a>  
	</menu>
	<div class="head">
		<div class="day headline">Mon</div>
		<div class="day headline">Die</div>
		<div class="day headline">Mit</div>
		<div class="day headline">Don</div>
		<div class="day headline">Fri</div>
		<div class="day headline">Sam</div>
		<div class="day headline">Son</div>
		<div class="clear"></div>
	</div>
	<?php
	$line = 0;
	$sum_days = date('t',$date);
	$LastMonthSum = date('t',mktime(0,0,0,(date('m',$date)-1),1,date('Y',$date)));
	
	for($i = 1; $i <= $sum_days; $i++) {
		if($line == 7) {
			echo "<div class=\"clear line\"></div>";
			$line = 0;
		}
		$day_name = date('D',mktime(0,0,0,date('m',$date),$i,date('Y',$date)));
		$day_number = date('w',mktime(0,0,0,date('m',$date),$i,date('Y',$date)));
		
		if($i == 1) {
			$s = array_search($day_name,array('Mon','Tue','Wed','Thu','Fri','Sat','Sun'));
			for($b = $s; $b > 0; $b--) {
				$x = $LastMonthSum-$b;
				echo "<div class=\"day before\">".sprintf("%02d",$x+1)."</div>";
				$line++;
			}
		} 
		
		if($i == date('d',$date) and date("m",$date) == date("m",time()) and date("Y",$date) == date("Y",time())) {
			echo "<div class=\"day current\">".sprintf("%02d",$i);
			$result = $db->query("	SELECT *, 
									DATE_FORMAT(date,'%d') as d, DATE_FORMAT(date,'%m') as m, DATE_FORMAT(date,'%Y') as Y, DATE_FORMAT(date,'%H') as H, DATE_FORMAT(date,'%i') as i
									FROM XENUX_dates;");
			while($row = $result->fetch_object()) {
				if($row->d == $i and $row->m == date("m",$date) and $row->Y == date("Y",$date)) {
					echo "<a href=\"?site=event_view&event_id=$row->id\" class=\"date\"><span>$row->H:$row->i</span>
					$row->text</a>";
				}
			}
			echo "</div>";
			$line++;
		} else {
			echo "<div class=\"day normal\">".sprintf("%02d",$i);
			$result = $db->query("	SELECT *, 
									DATE_FORMAT(date,'%d') as d, DATE_FORMAT(date,'%m') as m, DATE_FORMAT(date,'%Y') as Y, DATE_FORMAT(date,'%H') as H, DATE_FORMAT(date,'%i') as i
									FROM XENUX_dates;");
			while($row = $result->fetch_object()) {
				if($row->d == $i and $row->m == date("m",$date) and $row->Y == date("Y",$date)) {
					echo "<a href=\"?site=event_view&event_id=$row->id\" class=\"date\"><span>$row->H:$row->i</span>
					$row->text</a>";
				}
			}
			echo "</div>";
			$line++;
		}
		
		if($i == $sum_days) {
			$next_sum = (6 - array_search($day_name,array('Mon','Tue','Wed','Thu','Fri','Sat','Sun')));
			for($c = 1; $c <= $next_sum; $c++) {
				echo "<div class=\"day after\"> ".sprintf("%02d",$c)."</div>";
				$line++; 
			}
		}
	}
	?>
	<div class="clear"></div>
</div>