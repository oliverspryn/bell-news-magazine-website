<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Administrator"); ?>
<?php
//Search for users
	if (isset($_GET['keywords']) && isset($_GET['searchMethod'])) {
		if ($_GET['searchMethod'] == "organization") {
			$organizationName = $_GET['keywords'];
			$organizationInfoGrabber = mysql_query("SELECT * FROM `organizations` WHERE `organization` LIKE '%{$organizationName}%'", $connDBA);
			
			if ($organizationInfo = mysql_fetch_array($organizationInfoGrabber)) {
				$keywords = $organizationInfo['id'];
				$searchMethod = $_GET['searchMethod'];
			} else {
				header("Location: search.php?suggestions=display");
				exit;
			}
		} else {
			$keywords = $_GET['keywords'];
			$searchMethod = $_GET['searchMethod'];
		}
		
		if (empty($keywords)) {
			header("Location: search.php");
			exit;
		}
		
		if (isset($_GET['limit'])) {
			$limit = $_GET['limit'];
			
			if ($limit == "all") {
				$showAll = "true";
			}
			
			if ($limit == "1") {
				header("Location: search.php");
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
					header("Location: search.php");
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
			$sort = " ORDER BY lastName ASC, role ";
			$order = "ASC ";
		}
		
		if (!isset($showAll)) {
			$objectNumberGrabber = mysql_query("SELECT * FROM users WHERE `{$searchMethod}` LIKE '%{$keywords}%'", $connDBA);
			$objectNumber = mysql_num_rows($objectNumberGrabber);
			if ($objectNumber == 1) {
				$searchPages = 1;
			} else {
				$searchPages = ceil($objectNumber/$limit);
			}
			
			if (!isset($_GET['page'])) {
				$userGrabber = mysql_query("SELECT * FROM users WHERE `{$searchMethod}` LIKE '%{$keywords}%'{$sort}LIMIT 0, {$limit}", $connDBA);
			} else {
				$searchPage = $_GET['page'];
				
				if ($searchPage > $searchPages) {
					header("Location: search.php");
					exit;
				}
				
				if ($searchPage == "1") {
					$lowerLimit = ($searchPage*$limit)-$limit;
				
					$userGrabber = mysql_query("SELECT * FROM users WHERE `{$searchMethod}` LIKE '%{$keywords}%'{$sort}LIMIT 0, {$limit}", $connDBA);
				} else {
					$lowerLimit = ($searchPage*$limit)-$limit;
					
					$userGrabber = mysql_query("SELECT * FROM users WHERE `{$searchMethod}` LIKE '%{$keywords}%'{$sort}LIMIT {$lowerLimit}, {$limit}", $connDBA);
				}
			}
			
			if (!isset($searchPages) || $searchPages != "1") {
				if (!isset($_GET['page'])) {
					$navigationPage = "1";
				} else {
					$navigationPage = $_GET['page'];
				}
				
				if (isset($_GET['sort']) && isset($_GET['order'])) {
					$additionalParameters = "keywords=" . $_GET['keywords'] . "&searchMethod=" . $_GET['searchMethod'] . "&sort=" . $_GET['sort'] . "&order=" . $_GET['order'] . "&";
				} else {
					$additionalParameters = "keywords=" . $_GET['keywords'] . "&searchMethod=" . $_GET['searchMethod'] . "&";
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
			$userGrabber = mysql_query("SELECT * FROM users WHERE `{$searchMethod}` LIKE '%{$keywords}%'{$sort}", $connDBA);
		}
		
		$userNumberGrabber = mysql_query("SELECT * FROM users WHERE `{$searchMethod}` LIKE '%{$keywords}%' ORDER BY lastName ASC, role ASC", $connDBA);
		$userNumber = mysql_num_rows($userNumberGrabber);
		
		if (!$userNumber) {
			header("Location: search.php?suggestions=display");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Search for Users"); ?>
<?php headers(); ?>
<?php validate(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
<h2>Search for Users</h2>
<?php
	if (!isset($_GET['keywords']) && !isset($_GET['searchMethod']) && !isset($_GET['suggestions'])) {
		echo "<form id=\"search\" name=\"search\" method=\"get\" action=\"search.php\">
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" class=\"\">
			  <tr>
				<td width=\"30%\"><div align=\"right\">Keywords:</div></td>
				<td width=\"70%\"><div align=\"left\">
				  <input name=\"keywords\" id=\"keywords\" size=\"50\" autocomplete=\"off\" type=\"text\" />
				</div></td>
			  </tr>
			  <tr>
				<td width=\"30%\"><div align=\"right\">Search by:</div></td>
				<td width=\"70%\"><div align=\"left\">
				  <select name=\"searchMethod\" id=\"searchMethod\">
					<option value=\"firstName\" selected=\"selected\">First Name</option>
					<option value=\"lastName\">Last Name</option>
					<option value=\"emailAddress\">Email Address</option>
					<option value=\"role\">Role</option>
				  </select>
				</div></td>
			  </tr>
			  <tr>
				<td width=\"30%\"><div align=\"right\"></div></td>
				<td width=\"70%\"><div align=\"left\">
					<input name=\"submit\" id=\"submit\" value=\"Submit\" type=\"submit\" />
				</div></td>
			  </tr>
		  </table>
		</form>";
		formErrors();
	} else {
		if (isset($_GET['suggestions'])) {
			$failedMessage = "<p>Your search keywords did not return any results. Try checking your spelling, and ensuring that you are searching under the correct category.";
			errorMessage($failedMessage);
			
			echo "<form id=\"search\" name=\"search\" method=\"get\" action=\"search.php\">
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" class=\"\">
			  <tr>
				<td width=\"30%\"><div align=\"right\">Keywords:</div></td>
				<td width=\"70%\"><div align=\"left\">
				  <input name=\"keywords\" id=\"keywords\" size=\"50\" autocomplete=\"off\" type=\"text\" />
				</div></td>
			  </tr>
			  <tr>
				<td width=\"30%\"><div align=\"right\">Search by:</div></td>
				<td width=\"70%\"><div align=\"left\">
				  <select name=\"searchMethod\" id=\"searchMethod\">
					<option value=\"firstName\" selected=\"selected\">First Name</option>
					<option value=\"lastName\">Last Name</option>
					<option value=\"emailAddress1\">Email Address</option>
					<option value=\"role\">Role</option>
				  </select>
				</div></td>
			  </tr>
			  <tr>
				<td width=\"30%\"><div align=\"right\"></div></td>
				<td width=\"70%\"><div align=\"left\">
					<input name=\"submit\" id=\"submit\" value=\"Submit\" type=\"submit\" />
				</div></td>
			  </tr>
		  </table>
		</form>";
		formErrors();
		} else {
			echo "<p>&nbsp;</p><div class=\"toolBar\"><a class=\"toolBarItem search\" href=\"search.php\">Perform another Search</a><a class=\"toolBarItem back\" href=\"index.php\">Back to Users</a></div><br />";
			
			if (isset($navigation)) {
				echo $navigation;
			}
			
			echo "<table class=\"dataTable\">
			<tbody>
				<tr>";
				
					if (isset($_GET['limit'])) {
						$additionalParameters = "keywords=" . $_GET['keywords'] . "&searchMethod=" . $_GET['searchMethod'] . "&limit=" . $_GET['limit'] . "&page=1&";
					} else {
						$additionalParameters =  "keywords=" . $_GET['keywords'] . "&searchMethod=" . $_GET['searchMethod'] . "&page=1&";
					}
								
					echo "<th width=\"200\" class=\"tableHeader\"><a href=\"search.php";
					if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "lastName.role" && $_GET['order'] == "descending.ascending") {
						echo "?" . $additionalParameters . "sort=lastName.role&order=ascending.ascending\" class=\"ascending\">Name</a>";
					} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "lastName.role" && $_GET['order'] == "ascending.ascending") {
						echo "?" . $additionalParameters . "sort=lastName.role&order=descending.ascending\" class=\"descending\">Name</a>";
					} elseif (!isset ($_GET['sort'])) {
						echo "?" . $additionalParameters . "sort=lastName.role&order=descending.ascending\" class=\"descending\">Name</a>";
					} else {
						echo "?" . $additionalParameters . "sort=lastName.role&order=ascending.ascending\" class=\"sortHover\">Name</a>";
					}
					echo "</th>
					<th width=\"150\" class=\"tableHeader\"><a href=\"search.php";
					if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "emailAddress1.lastName" && $_GET['order'] == "ascending.ascending") {
						echo "?" . $additionalParameters . "sort=emailAddress1.lastName&order=descending.ascending\" class=\"descending\">Email Address</a>";
					} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "emailAddress1.lastName" && $_GET['order'] == "descending.ascending") {
						echo "?" . $additionalParameters . "sort=emailAddress1.lastName&order=ascending.ascending\" class=\"ascending\">Email Address</a>";
					} else {
						echo "?" . $additionalParameters . "sort=emailAddress1.lastName&order=ascending.ascending\" class=\"sortHover\">Email Address</a>";
					}
					echo "</th>
					<th width=\"175\" class=\"tableHeader\"><a href=\"search.php";
					if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "role.lastName" && $_GET['order'] == "ascending.ascending") {
						echo "?" . $additionalParameters . "sort=role.lastName&order=descending.ascending\" class=\"descending\">Role</a>";
					} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "role.lastName" && $_GET['order'] == "descending.ascending") {
						echo "?" . $additionalParameters . "sort=role.lastName&order=ascending.ascending\" class=\"ascending\">Role</a>";
					} else {
						echo "?" . $additionalParameters . "sort=role.lastName&order=ascending.ascending\" class=\"sortHover\">Role</a>";
					}
					echo "</th>
					<th class=\"tableHeader\"><a href=\"search.php";
					if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "active.lastName" && $_GET['order'] == "descending.ascending") {
						echo "?" . $additionalParameters . "sort=active.lastName&order=ascending.ascending\" class=\"descending\">Last Access</a>";
					} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "active.lastName" && $_GET['order'] == "ascending.ascending") {
						echo "?" . $additionalParameters . "sort=active.lastName&order=descending.ascending\" class=\"ascending\">Last Access</a>";
					} else {
						echo "?" . $additionalParameters . "sort=active.lastName&order=descending.ascending\" class=\"sortHover\">Last Access</a>";
					}
					echo "</th>
					<th width=\"50\" class=\"tableHeader\">Edit</th>
					<th width=\"50\" class=\"tableHeader\">Delete</th>
					
				</tr>";
				
			$number = 1;
			
			while(($userData = mysql_fetch_array($userGrabber)) && ($number <= $userNumber)) {
				echo "<tr";
				//Alternate the color of each row.
				if ($number++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				echo "<td width=\"200\"><a href=\"profile.php?id=" . $userData['id'] . "\">";
				
				if ($_GET['searchMethod'] == "lastName") {
					echo str_ireplace($_GET['keywords'], "<span class=\"searchKeywords\">" . strtolower($_GET['keywords']) . "</span>", $userData['lastName']);	
				} else {
					echo $userData['lastName'];
				}
				
				echo ", ";
				
				if ($_GET['searchMethod'] == "firstName") {
					echo str_ireplace($_GET['keywords'], "<span class=\"searchKeywords\">" . strtolower($_GET['keywords']) . "</span>", $userData['firstName']);	
				} else {
					echo $userData['firstName'];
				}
				
				echo "</a></td>" . 
				"<td width=\"150\"><a href=\"../collaboration/send_email.php?type=user&id=" . $userData['id'] . "\">";
				
				if ($_GET['searchMethod'] == "emailAddress1") {
					echo str_ireplace($_GET['keywords'], "<span class=\"searchKeywords\">" . strtolower($_GET['keywords']) . "</span>", $userData['emailAddress1']);	
				} else {
					echo $userData['emailAddress1'];
				}
				
				echo "</a></td>" . 
				"<td width=\"175\">";
				
				if ($_GET['searchMethod'] == "role") {
					echo str_ireplace($_GET['keywords'], "<span class=\"searchKeywords\">" . strtolower($_GET['keywords']) . "</span>", $userData['role']);	
				} else {
					echo $userData['role'];
				}
				
				echo "</td>";
				
				if ($userData['active'] == "") {
					$access = "<span class=\"alertNotAssigned\">Never</span>";
				} else {
					$lastActive = time() - $userData['active'];
					$access = "";
					
					if ($lastActive >= 31556926) {
						$access .= floor($lastActive/31556926);
						
						if (floor($lastActive/31556926) == "1") {
							$access .= " Year ";
						} else {
							$access .= " Years ";
						}
						
						$lastActive = ($lastActive%31556926);
					}
					
					if ($lastActive >= 2629744) {
						$access .= floor($lastActive/2629744);
						
						if (floor($lastActive/2629744) == "1") {
							$access .= " Month ";
						} else {
							$access .= " Months ";
						}
						
						$lastActive = ($lastActive%2629744);
					}
					
					if ($lastActive >= 86400) {
						$access .= floor($lastActive/86400);
						
						if (floor($lastActive/86400) == "1") {
							$access .= " Day ";
						} else {
							$access .= " Days ";
						}
						
						$lastActive = ($lastActive%86400);
					}
					
					if ($lastActive >= 3600) {
						$access .= floor($lastActive/3600);
						
						if (floor($lastActive/3600) == "1") {
							$access .= " Hour ";
						} else {
							$access .= " Hours ";
						}
						
						$lastActive = ($lastActive%3600);
					}
					
					if ($lastActive >= 60) {
						$access .= floor($lastActive/60);
						
						if (floor($lastActive/60) == "1") {
							$access .= " Min ";
						} else {
							$access .= " Mins ";
						}
						
						$lastActive = ($lastActive%60);
					}
					
					$access .= $lastActive;
					
					if ($lastActive == "1") {
						$access .= " Sec ";
					} else {
						$access .= " Secs ";
					}
					
					if ($lastActive == "0") {
						$access = "Now";
					}
				}
							
				echo "<td>" . $access . "</td>" .
				"<td width=\"50\"><a class=\"action edit\" href=\"manage_user.php?id=" . $userData['id'] . "\" onmouseover=\"Tip('Edit <strong>" .  $userData['firstName'] . " " . $userData['lastName'] . "</strong>')\" onmouseout=\"UnTip()\"></a>
				</td>" . "<td width=\"50\">";
				
				if ($userData['userName'] !== $_SESSION['MM_Username']) {
					echo "<a class=\"action delete\" href=\"javascript:void\" onclick=\"warningDelete('index.php?action=delete&id=" . $userData['id'] . "', 'user')\" onmouseover=\"Tip('Delete <strong>" . $userData['firstName'] . " " .  $userData['lastName'] . "</strong>')\" onmouseout=\"UnTip()\"></a>";
				} else {
					echo "<span class=\"action noDelete\" onmouseover=\"Tip('You may not delete yourself')\" onmouseout=\"UnTip()\"></span>";
				}
				
				echo "</td></tr>";
			}
			echo "</tbody>
			</table>";
			
			if (isset($navigation)) {
				echo "<br />" . $navigation;
			}
		}
	}
?>
<?php footer(); ?>
</body>
</html>