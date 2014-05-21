<?php
if(isset($_GET['timestamp']) and preg_match("/[0-9]/", $_GET['timestamp'])) {
	$date = $_GET['timestamp'];
} else {
	$date = time();
}

$arrMonth = array(
	"January" => "Januar",
	"February" => "Februar",
	"March" => "März",
	"April" => "April",
	"May" => "Mai",
	"June" => "Juni",
	"July" => "Juli",
	"August" => "August",
	"September" => "September",
	"October" => "Oktober",
	"November" => "November",
	"December" => "Dezember"
);
function monthBack($timestamp) {
	return mktime(0,0,0, date("m",$timestamp)-1,date("d",$timestamp),date("Y",$timestamp));
}
function yearBack($timestamp) {
	return mktime(0,0,0, date("m",$timestamp),date("d",$timestamp),date("Y",$timestamp)-1);
}
function monthForward($timestamp) {
	return mktime(0,0,0, date("m",$timestamp)+1,date("d",$timestamp),date("Y",$timestamp));
}
function yearForward($timestamp) {
	return mktime(0,0,0, date("m",$timestamp),date("d",$timestamp),date("Y",$timestamp)+1);
}
?>
<div class="calender">
	<a href="?<?php echo "site=$filename"; ?>" class="now">Heute</a>
	<div class="pagination">
		<a href="?<?php echo "site=$filename&timestamp=".yearBack($date); ?>" class="last">&laquo;</a>
		<a href="?<?php echo "site=$filename&timestamp=".monthBack($date); ?>" class="last">&lt;</a>
		<span><?php echo $arrMonth[date('F',$date)];?> <?php echo date('Y',$date); ?></span>
		<a href="?<?php echo "site=$filename&timestamp=".monthForward($date); ?>" class="next">&gt;</a>
		<a href="?<?php echo "site=$filename&timestamp=".yearForward($date); ?>" class="next">&raquo;</a>  
	</div>
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
	$row = 0;
	$sum_days = date('t',$date);
	$LastMonthSum = date('t',mktime(0,0,0,(date('m',$date)-1),1,date('Y',$date)));
	
	for($i = 1; $i <= $sum_days; $i++) {
		if($row == 7) {
			echo "<div class=\"clear line\"></div>";
			$row = 0;
		}
		$day_name = date('D',mktime(0,0,0,date('m',$date),$i,date('Y',$date)));
		$day_number = date('w',mktime(0,0,0,date('m',$date),$i,date('Y',$date)));
		
		if($i == 1) {
			$s = array_search($day_name,array('Mon','Tue','Wed','Thu','Fri','Sat','Sun'));
			for($b = $s; $b > 0; $b--) {
				$x = $LastMonthSum-$b;
				echo "<div class=\"day before\">".sprintf("%02d",$x+1)."</div>";
				$row++;
			}
		} 
		
		if($i == date('d',$date) and date("m",$date) == date("m",time()) and date("Y",$date) == date("Y",time())) {
			echo "<div class=\"day current\">".sprintf("%02d",$i);
			$sql = "SELECT *, DATE_FORMAT(date,'%d') as d, DATE_FORMAT(date,'%m') as m, DATE_FORMAT(date,'%Y') as Y, DATE_FORMAT(date,'%H') as H, DATE_FORMAT(date,'%i') as i FROM XENUX_dates";
			$erg = mysql_query($sql);
			while($row1 = mysql_fetch_array($erg)) {
				if($row1['d'] == $i and $row1['m'] == date("m",$date) and $row1['Y'] == date("Y",$date)) {
					echo "<a href=\"?site=terminview&id=".$row1['id']."\" class=\"date\"><span>".$row1['H'].":".$row1['i']."</span>".substr($row1['text'],0,50)."</a>";
				}
			}
			echo "</div>";
			$row++;
		} else {
			echo "<div class=\"day normal\">".sprintf("%02d",$i);
			$sql = "SELECT *, DATE_FORMAT(date,'%d') as d, DATE_FORMAT(date,'%m') as m, DATE_FORMAT(date,'%Y') as Y, DATE_FORMAT(date,'%H') as H, DATE_FORMAT(date,'%i') as i FROM XENUX_dates";
			$erg = mysql_query($sql);
			while($row1 = mysql_fetch_array($erg)) {
				if($row1['d'] == $i and $row1['m'] == date("m",$date) and $row1['Y'] == date("Y",$date)) {
					echo "<a href=\"?site=terminview&id=".$row1['id']."\" class=\"date\"><span>".$row1['H'].":".$row1['i']."</span>".substr($row1['text'],0,50)."</a>";
				}
			}
			echo "</div>";
			$row++;
		}
		
		if($i == $sum_days) {
			$next_sum = (6 - array_search($day_name,array('Mon','Tue','Wed','Thu','Fri','Sat','Sun')));
			for($c = 1; $c <=$next_sum; $c++) {
				echo "<div class=\"day after\"> ".sprintf("%02d",$c)."</div>";
				$row++; 
			}
		}
	}
	?>
	<div class="clear"></div>
</div>