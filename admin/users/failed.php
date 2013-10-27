<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Administrator"); ?>
<?php
//Check for failed logins
	$loginsCheck = mysql_query("SELECT * FROM `failedlogins`", $connDBA);
	
	if (exist("failedlogins")) {
		$logins = "exist";
	} else {
		$logins = "empty";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Failed Logins"); ?>
<?php headers(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
<h2>Failed Logins</h2>
<p>Below is a list of details for failed logins on this site.</p>
<p>&nbsp;</p>
<div class="toolBar"><a class="toolBarItem back" href="index.php">Back to Users</a></div>
<br />
<?php
	if (isset($_GET['limit'])) {
		$limit = $_GET['limit'];
		
		if ($limit == "all") {
			$showAll = "true";
		}
		
		if ($limit == "1") {
			header("Location: failed.php");
			exit;
		}
	} else {
		$limit = "25";
	}
	
	if (isset($_GET['sort']) && isset($_GET['order'])) {
		$sortArray = explode(".", $_GET['sort']);
		$sortArrayValues = count($sortArray) - 1;
		
		$sort = " ORDER BY ";
		for ($count = 0; $count <= $sortArrayValues; $count++) {
			if ($_GET['order']) {
				$orderArray = explode(".", $_GET['order']);
				$orderArrayValues = count($orderArray) - 1;
				
				switch($orderArray[$count]) {
					case "ascending" : $order = " ASC"; break;
					case "descending" : $order = " DESC"; break;
				}
			} else {
				$order = " ASC";
			}
			
			if ($orderArrayValues != $sortArrayValues) {
				header("Location: failed.php");
				exit;
			}
			
			$sort .= $sortArray[$count];
			
			if ($count != $sortArrayValues) {
				$sort .= $order . ", ";
			} else {
				$sort .= $order . " ";
			}
		}
	} else {
		$sort = " ORDER BY timeStamp DESC, IPAddress ";
		$order = "ASC ";
	}
	
	if (!isset($showAll)) {
		$objectNumberGrabber = mysql_query("SELECT * FROM failedlogins", $connDBA);
		$objectNumber = mysql_num_rows($objectNumberGrabber);
		$searchPages = ceil($objectNumber/$limit);
		
		if (!isset($_GET['page'])) {
			$failedGrabber = mysql_query("SELECT * FROM failedlogins{$sort}LIMIT 0, {$limit}", $connDBA);
		} else {
			$searchPage = $_GET['page'];
			
			if ($searchPage > $searchPages) {
				header("Location: failed.php");
				exit;
			}
			
			if ($searchPage == "1") {
				$lowerLimit = ($searchPage*$limit)-$limit;
			
				$failedGrabber = mysql_query("SELECT * FROM failedlogins{$sort}LIMIT 0, {$limit}", $connDBA);
			} else {
				$lowerLimit = ($searchPage*$limit)-$limit;
				
				$failedGrabber = mysql_query("SELECT * FROM failedlogins{$sort}LIMIT {$lowerLimit}, {$limit}", $connDBA);
			}
		}
		
		if (!isset($searchPages) || $searchPages != "1") {
			if (!isset($_GET['page'])) {
				$navigationPage = "1";
			} else {
				$navigationPage = $_GET['page'];
			}
			
			if (isset($_GET['sort']) && isset($_GET['order'])) {
				$additionalParameters = "sort=" . $_GET['sort'] . "&order=" . $_GET['order'] . "&";
			} else {
				$additionalParameters = "";
			}
			
			$navigation = "<div class=\"pagesBox\">";
			if (isset($_GET['page'])) {
				if ($_GET['page'] != "1") {
					$previousPage = $navigationPage - 1;
					
					$navigation .= "<a href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $previousPage . "\">(Previous)</a>";
				}
			}
			
			for ($count = 1; $count <= $searchPages; $count++) {
			//If there are less than or equal to 15 pages, then display them all
				if ($searchPages - 15 <= 1) {
					if ($navigationPage != $count) {
						$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
					} else {
						$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
					}
				}
				
			//If there are more than or equal to 15 pages
				if ($searchPages - 15 > 1) {
				//If the pages are in the lower set, then only break the upper set
					if ($navigationPage < 8) {
						$orginalUpper = $navigationPage - 7;
						switch ($searchPages - $navigationPage) {
							case "0" : $additionalLower = 6; break;
							case "1" : $additionalLower = 5; break;
							case "2" : $additionalLower = 4; break;
							case "3" : $additionalLower = 3; break;
							case "4" : $additionalLower = 2; break;
							case "5" : $additionalLower = 1; break;
							case "6" : $additionalLower = 0; break;
						}
						
						if ($count <= 14) {
							if ($navigationPage != $count) {
								$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
							} else {
								$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
							}
							
							if  ($count == 14) {
								$navigation .= "...<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $searchPages . "\">" . $searchPages . "</a>";
							}
						}
					}
					
				//If the pages are somewhere in the middle
					if ($navigationPage >= 8) {	
					//If this page is not one page after the first page, break the list (e.g.: NOT 1 3, BUT 1 ... 3)
						$additionalLower = 0;
						
						switch ($searchPages - $navigationPage) {
							case "0" : $additionalLower = 6; break;
							case "1" : $additionalLower = 5; break;
							case "2" : $additionalLower = 4; break;
							case "3" : $additionalLower = 3; break;
							case "4" : $additionalLower = 2; break;
							case "5" : $additionalLower = 1; break;
							case "6" : $additionalLower = 0; break;
						}
						
						if ($count == $navigationPage - 6 - $additionalLower && $count != 2) {
							$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=1\">1</a>...";
						} elseif ($count == $navigationPage - 6 - $additionalLower && $count == 2) {
							$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=1\">1</a>";
						}
					
					//Do not break the upper set of pages, if the user is approaching the end, and display a constant number of suggestions				
						if ($navigationPage + 7 > $searchPages) {
							$orginalLower = $navigationPage - 7;
							
							if ($orginalLower - $additionalLower < $count && $count < $navigationPage + 7) {
								if ($navigationPage != $count) {
									$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
								} else {
									$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
								}
							}
					//Display all pages in the center with a value of +- 6, with the upper and lower extremes
						} else {
						//For all pages in the center of the list
							if ($navigationPage - 7 < $count && $count < $navigationPage + 7) {
							//For the one page before the last page, do not break the list (e.g.: NOT 18 ... 19, BUT 18 19)
								if ($count + 1 == $searchPages) {									
									if ($navigationPage != $count) {
										$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
									} else {
										$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
									}
									
									$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $searchPages . "\">" . $searchPages . "</a>";
									break;
								}
								
							//If this page is not one page before the last page, break the list (e.g.: NOT 17 19, BUT 17 ... 19)	
								if ($count + 1 != $searchPages) {
									if ($navigationPage != $count) {
										$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
									} else {
										$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
									}
								}
								
								if ($count == $navigationPage + 6) {
									$navigation .= "...<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $searchPages . "\">" . $searchPages . "</a>";
								}
							}
						}
					}
				}
			}
			
			if (isset($_GET['page'])) {
				if ($_GET['page'] != $searchPages) {
					$nextPage = $navigationPage + 1;
					
					$navigation .= "<a href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $nextPage . "\">(Next)</a>";
				}
			} else {
				$navigation .= "<a href=\"?" . $additionalParameters . "limit=" . $limit . "&page=2\">(Next)</a>";
			}
			$navigation .= "</div><br />";
		}
	} else {
		$failedGrabber = mysql_query("SELECT * FROM failedlogins{$sort}", $connDBA);
	}
	
	if (isset($navigation) && $logins != "empty") {
		echo $navigation;
	}
	
	$failedNumberGrabber = mysql_query("SELECT * FROM failedlogins ORDER BY timeStamp DESC, IPAddress ASC", $connDBA);
	$failedNumber = mysql_num_rows($failedNumberGrabber);
	
	if (!$failedGrabber && $failedNumber) {
		header("Location: failed.php");
		exit;
	}
?>

<?php
//If no logins exist	
	if (isset ($logins) && $logins == "empty") {
		echo "<div class=\"noResults\">No failed logins have taken place</div>";
//If logins exist
	} else {
		$count = 1;
		
		echo "<table class=\"dataTable\">
		<tbody>
			<tr>";		
			
				if (isset($_GET['limit'])) {
					$additionalParameters = "&limit=" . $_GET['limit'] . "&page=1";
				} else {
					$additionalParameters = "&page=1";
				}
							
				echo "<th width=\"200\" class=\"tableHeader\"><a href=\"failed.php";
				if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "timeStamp.IPAddress" && $_GET['order'] == "descending.ascending") {
					echo "?sort=timeStamp.IPAddress&order=ascending.ascending" . $additionalParameters . "\" class=\"ascending\">Date</a>";
				} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "timeStamp.IPAddress" && $_GET['order'] == "ascending.ascending") {
					echo "?sort=timeStamp.IPAddress&order=descending.ascending" . $additionalParameters . "\" class=\"descending\">Date</a>";
				} elseif (!isset ($_GET['sort'])) {
					echo "?sort=timeStamp.IPAddress&order=ascending.ascending" . $additionalParameters . "\" class=\"ascending\">Date</a>";
				} else {
					echo "?sort=timeStamp.IPAddress&order=descending.ascending" . $additionalParameters . "\" class=\"hover\">Date</a>";
				}
				echo "</th>
				<th width=\"150\" class=\"tableHeader\"><a href=\"failed.php";
				if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "IPAddress.timeStamp" && $_GET['order'] == "ascending.ascending") {
					echo "?sort=IPAddress.timeStamp&order=descending.ascending" . $additionalParameters . "\" class=\"descending\">IP Address</a>";
				} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "IPAddress.timeStamp" && $_GET['order'] == "descending.ascending") {
					echo "?sort=IPAddress.timeStamp&order=ascending.ascending" . $additionalParameters . "\" class=\"ascending\">IP Address</a>";
				} else {
					echo "?sort=IPAddress.timeStamp&order=ascending.ascending" . $additionalParameters . "\" class=\"sortHover\">IP Address</a>";
				}
				echo "</th>
				<th width=\"175\" class=\"tableHeader\"><a href=\"failed.php";
				if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "userName.timeStamp" && $_GET['order'] == "ascending.ascending") {
					echo "?sort=userName.timeStamp&order=descending.ascending" . $additionalParameters . "\" class=\"descending\">User name</a>";
				} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "userName.timeStamp" && $_GET['order'] == "descending.ascending") {
					echo "?sort=userName.timeStamp&order=ascending.ascending" . $additionalParameters . "\" class=\"ascending\">User name</a>";
				} else {
					echo "?sort=userName.timeStamp&order=ascending.ascending" . $additionalParameters . "\" class=\"sortHover\">User name</a>";
				}
				echo "</th>
			</tr>";
		
		
		while($logins = mysql_fetch_array($failedGrabber)) {
			echo "<tr";
			//Alternate the color of each row.
			if ($count++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			
			echo "<td width=\"400\">" . date("l, M j, Y \\a\\t h:i:s A", $logins['timeStamp']) . "</td>" . 
			"<td width=\"200\">" . $logins['IPAddress'] . "</td>" .
			"<td>" . prepare($logins['userName']) . "</td>" . 
			"</tr>";
		}
		
		echo "</tbody>
		</table>";
	}
?>
<?php
	if (isset($navigation) && $logins != "empty") {
		echo "<br />" . $navigation;
	}
?>
<?php footer(); ?>
</body>
</html>