<?php require_once("../../../Connections/connDBA.php"); ?>
<?php
	if (privileges("viewStatistics") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Export as XML file
	header("Content-type: application/xml");
	
	if (isset($_GET['type'])) {
		switch ($_GET['type']) {
			case "daily" : 
				$statisticsNumber = query("SELECT * FROM `dailyhits`", "num");
				$statisticsCheck = mysql_query("SELECT * FROM `dailyhits` LIMIT 50");
				
				if (mysql_fetch_array($statisticsCheck)) {
					$statisticsGrabber = mysql_query("SELECT * FROM `dailyhits` ORDER BY `id` DESC LIMIT 50");
					
					echo "<graph caption=\"Daily Hits\" subcaption=\"";
					
					if ($statisticsNumber > 50) {
						echo "Last 50 Days - ";
					}
					
					$values = array();
					
					while($statistics = mysql_fetch_array($statisticsGrabber)) {
						array_push($values, array("name" => $statistics['date'], "value" => $statistics['hits'], "hoverText" => $statistics['date']));
					}
					
					$correctedValues = array_reverse($values);
					
					echo "From " . $correctedValues['0']['name'] . " to " . $correctedValues[sizeof($correctedValues) - 1]['name'] ."\" xAxisName=\"\" yAxisMinValue=\"0\" yAxisName=\"Hits\" decimalPrecision=\"0\" formatNumberScale=\"0\" showNames=\"0\" showValues=\"0\" showAlternateHGridColor=\"1\" AlternateHGridColor=\"ff5904\" divLineColor=\"ff5904\" divLineAlpha=\"20\" alternateHGridAlpha=\"5\" bgAlpha=\"0\" rotateNames=\"1\">";
					
					foreach($correctedValues as $value) {
						echo "<set name=\"" . $value['name'] . "\" value=\"" . $value['value'] . "\" hoverText=\"" . $value['hoverText'] . "\"/>";
					}
					
					echo "</graph>";
				} else {
					echo "<graph caption=\"Daily Hits\" subcaption=\"No Data\" xAxisName=\"\" yAxisMinValue=\"10\" yAxisName=\"hits\" decimalPrecision=\"0\" formatNumberScale=\"0\" numberPrefix=\"\" showNames=\"0\" showValues=\"0\"  showAlternateHGridColor=\"1\" AlternateHGridColor=\"ff5904\" divLineColor=\"ff5904\" divLineAlpha=\"20\" alternateHGridAlpha=\"5\"></graph>";
				}
				
				break;
			
			case "page" : 
				$statisticsCheck = mysql_query("SELECT * FROM `pagehits`");
				
				if (mysql_fetch_array($statisticsCheck)) {
					$statisticsGrabber = mysql_query("SELECT * FROM `pagehits` ORDER BY `id` ASC");
					
					echo "<graph caption=\"Page Hits\" xAxisName=\"\" yAxisMinValue=\"0\" yAxisName=\"Hits\" decimalPrecision=\"0\" formatNumberScale=\"0\" showNames=\"0\" showValues=\"0\" alternateHGridAlpha=\"5\" bgAlpha=\"0\" rotateNames=\"1\">";
					
					while($statistics = mysql_fetch_array($statisticsGrabber)) {
						$id = $statistics['page'];
						$pageGrabber = mysql_query("SELECT * FROM `pages` WHERE `id` = '{$id}'", $connDBA);
						$pagePrep = mysql_fetch_array($pageGrabber);
						$page = unserialize($pagePrep['content' . $pagePrep['display']]);
						
						echo "<set name=\"" . prepare($page['title'], true, true) . "\" value=\"" . $statistics['hits'] . "\" hoverText=\"" . prepare($page['title'], true, true) . " (" . $statistics['hits'] . " Hits)\"/>";
					}
					
					echo "</graph>";
				} else {
					echo "<graph caption=\"Page Hits\" subcaption=\"No Data\" xAxisName=\"\" yAxisMinValue=\"0\" yAxisName=\"Hits\" decimalPrecision=\"0\" formatNumberScale=\"0\" showNames=\"0\" showValues=\"0\" alternateHGridAlpha=\"5\" bgAlpha=\"0\" rotateNames=\"1\"></graph>";
				}
				
				break;
		}
	}
?>