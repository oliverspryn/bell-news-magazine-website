<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Administrator"); ?>
<?php
//Check for users
	$usersCheck = mysql_query("SELECT * FROM `users`", $connDBA);
	
	if (mysql_fetch_array($usersCheck)) {
		$users = "exist";
	} else {
		$users = "empty";
	}
?>
<?php
//Delete a user
	if (isset ($_GET['action']) && isset ($_GET['id']) && $_GET['action'] == "delete") {
		$id = $_GET['id'];
		$userCheck = mysql_query("SELECT * FROM `users` WHERE `id` = '{$id}'", $connDBA);
		if ($user = mysql_fetch_array($userCheck)) {
			if ($user['userName'] !== $_SESSION['MM_Username']) {
				mysql_query("DELETE FROM `users` WHERE `id` = '{$id}'", $connDBA);
				
				header ("Location: index.php");
				exit;
			} else {
				header ("Location: index.php");
				exit;
			}
		} else {
			header ("Location: index.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Users"); ?>
<?php headers(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
<h2>Users</h2>
<p>Below is a list of all users registered within this system. Users may be sorted according to a certain criteria by clicking on the text in the header row of the desired column.</p>
<p>&nbsp;</p>
<div class="toolBar"><a class="toolBarItem new" href="manage_user.php">Add New User</a><a class="toolBarItem user" href="privileges.php">Assign User Privileges</a><a class="toolBarItem alert" href="failed.php">Failed Logins</a><a class="toolBarItem search" href="search.php">Search for Users</a></div>
<?php
//A user was created
	if (isset($_GET['message']) && $_GET['message'] == "userCreated") {
		successMessage("The user was created");	
//A user was edited
	} elseif (isset($_GET['message']) && $_GET['message'] == "userEdited") {
		successMessage("The user was modified");	
//If a site administrator tries to change their role, and there aren't any other site administrators
	} elseif (isset($_GET['message']) && $_GET['message'] == "noAdmin") {
		errorMessage("Your profile was not updated, since you tried to change your role, and thus would have left this site without a site administrator.");
//If the privileges are updated
	} elseif (isset($_GET['updated']) && $_GET['updated'] == "privileges") {
		successMessage("The privileges were modified");
	} else {
		echo "<br />";
	}
?>
<?php
	if (isset($_GET['limit'])) {
		$limit = $_GET['limit'];
		
		if ($limit == "all") {
			$showAll = "true";
		}
		
		if ($limit == "1") {
			header("Location: index.php");
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
				header("Location: index.php");
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
		$objectNumberGrabber = mysql_query("SELECT * FROM users", $connDBA);
		$objectNumber = mysql_num_rows($objectNumberGrabber);
		$searchPages = ceil($objectNumber/$limit);
		
		if (!isset($_GET['page'])) {
			$userGrabber = mysql_query("SELECT * FROM users{$sort}LIMIT 0, {$limit}", $connDBA);
		} else {
			$searchPage = $_GET['page'];
			
			if ($searchPage > $searchPages) {
				header("Location: index.php");
				exit;
			}
			
			if ($searchPage == "1") {
				$lowerLimit = ($searchPage*$limit)-$limit;
			
				$userGrabber = mysql_query("SELECT * FROM users{$sort}LIMIT 0, {$limit}", $connDBA);
			} else {
				$lowerLimit = ($searchPage*$limit)-$limit;
				
				$userGrabber = mysql_query("SELECT * FROM users{$sort}LIMIT {$lowerLimit}, {$limit}", $connDBA);
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
		$userGrabber = mysql_query("SELECT * FROM users{$sort}", $connDBA);
	}
	
	if (isset($navigation) && $users != "empty") {
		echo $navigation;
	}
	
	$userNumberGrabber = mysql_query("SELECT * FROM users ORDER BY lastName ASC, role ASC", $connDBA);
	$userNumber = mysql_num_rows($userNumberGrabber);
	
	if (!$userGrabber && $userNumber) {
		header("Location: index.php");
		exit;
	}
?>
<?php
//If no users exist	
	if (isset ($users) && $users == "empty") {
		echo "<div class=\"noResults\">No users exist on this system</div>";
	} else {
//If users exist
		echo "<table class=\"dataTable\">
		<tbody>
			<tr>";
			
				if (isset($_GET['limit'])) {
					$additionalParameters = "&limit=" . $_GET['limit'] . "&page=1";
				} else {
					$additionalParameters = "&page=1";
				}
							
				echo "<th width=\"200\" class=\"tableHeader\"><a href=\"index.php";
				if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "lastName.role" && $_GET['order'] == "descending.ascending") {
					echo "?sort=lastName.role&order=ascending.ascending" . $additionalParameters . "\" class=\"ascending\">Name</a>";
				} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "lastName.role" && $_GET['order'] == "ascending.ascending") {
					echo "?sort=lastName.role&order=descending.ascending" . $additionalParameters . "\" class=\"descending\">Name</a>";
				} elseif (!isset ($_GET['sort'])) {
					echo "?sort=lastName.role&order=descending.ascending" . $additionalParameters . "\" class=\"descending\">Name</a>";
				} else {
					echo "?sort=lastName.role&order=ascending.ascending" . $additionalParameters . "\" class=\"sortHover\">Name</a>";
				}
				echo "</th>
				<th width=\"150\" class=\"tableHeader\"><a href=\"index.php";
				if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "emailAddress1.lastName" && $_GET['order'] == "ascending.ascending") {
					echo "?sort=emailAddress1.lastName&order=descending.ascending" . $additionalParameters . "\" class=\"descending\">Email Address</a>";
				} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "emailAddress1.lastName" && $_GET['order'] == "descending.ascending") {
					echo "?sort=emailAddress1.lastName&order=ascending.ascending" . $additionalParameters . "\" class=\"ascending\">Email Address</a>";
				} else {
					echo "?sort=emailAddress1.lastName&order=ascending.ascending" . $additionalParameters . "\" class=\"sortHover\">Email Address</a>";
				}
				echo "</th>
				<th width=\"175\" class=\"tableHeader\"><a href=\"index.php";
				if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "role.lastName" && $_GET['order'] == "ascending.ascending") {
					echo "?sort=role.lastName&order=descending.ascending" . $additionalParameters . "\" class=\"descending\">Role</a>";
				} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "role.lastName" && $_GET['order'] == "descending.ascending") {
					echo "?sort=role.lastName&order=ascending.ascending" . $additionalParameters . "\" class=\"ascending\">Role</a>";
				} else {
					echo "?sort=role.lastName&order=ascending.ascending" . $additionalParameters . "\" class=\"sortHover\">Role</a>";
				}
				echo "</th>
				<th class=\"tableHeader\"><a href=\"index.php";
				if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "active.lastName" && $_GET['order'] == "descending.ascending") {
					echo "?sort=active.lastName&order=ascending.ascending" . $additionalParameters . "\" class=\"descending\">Last Access</a>";
				} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "active.lastName" && $_GET['order'] == "ascending.ascending") {
					echo "?sort=active.lastName&order=descending.ascending" . $additionalParameters . "\" class=\"ascending\">Last Access</a>";
				} else {
					echo "?sort=active.lastName&order=descending.ascending" . $additionalParameters . "\" class=\"sortHover\">Last Access</a>";
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
			echo "<td width=\"200\"><a href=\"profile.php?id=" . $userData['id'] . "\">" . $userData['lastName'] . ", " . $userData['firstName'] . "</a></td>" . 
			"<td width=\"150\"><a href=\"../collaboration/send_email.php?type=user&id=" . $userData['id'] . "\">" . $userData['emailAddress1'] . "</a></td>" . 
			"<td width=\"175\">" . $userData['role'] . "</td>";
			
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
				
				if (time() - $userData['active'] == "0") {
					$access = "Now";
				}
			}
						
			echo "<td>" . $access . "</td>" .
			"<td width=\"50\"><a class=\"action edit\" href=\"manage_user.php?id=" . $userData['id'] . "\" onmouseover=\"Tip('Edit <strong>" .  $userData['firstName'] . " " . $userData['lastName'] . "</strong>')\" onmouseout=\"UnTip()\"></a>
			</td>" . "<td width=\"50\">";
			if ($userData['userName'] !== $_SESSION['MM_Username']) {
				echo "<a class=\"action delete\" href=\"index.php?action=delete&id=" . $userData['id'] . "\" onclick=\"return confirm('This action cannot be undone. Continue?')\" onmouseover=\"Tip('Delete <strong>" . $userData['firstName'] . " " .  $userData['lastName'] . "</strong>')\" onmouseout=\"UnTip()\"></a>";
			} else {
				echo "<span class=\"action noDelete\" onmouseover=\"Tip('You may not delete yourself')\" onmouseout=\"UnTip()\"></span>";
			}
			echo "</td></tr>";
		}
		echo "</tbody>
		</table>";
	}
?>
<?php
	if (isset($navigation) && $users != "empty") {
		echo "<br />" . $navigation;
	}
?>
<?php footer(); ?>
</body>
</html>