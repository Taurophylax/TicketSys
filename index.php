<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<title>TicketSys - By James Carr</title>
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" href="modal.css">
		<script type="text/javascript" src="jquery-2.1.0.js"></script> 
		<script type="text/javascript" src="jquery.tablesorter.js"></script> 
		<script type="text/javascript">
			$(function() { $("#ticketlist").tablesorter({widgets: ['zebra']}); });
		</script>
	</head>

	<body>

		<div id="head">
			You are currently logged in as: <b>Demo User</b>
			<a href="#" title="Account Settings"><img src="images/cog.png" /></a>
			<a href="#" title="Create Reports"><img src="images/doc.png" /></a>
			<a href="#" title="Manage Users"><img src="images/user.png" /></a>
		</div>
		
				<?php
						$name = $severity = $department = $description = $error = $fltobject = $fltvalue = $closeid = "";

/* CHECK FOR POSTS AND SANITIZE */
					
						function check_form($X) {
						  $X = trim(stripslashes(htmlspecialchars($X)));
						  return $X;
						}
						
						if ($_SERVER["REQUEST_METHOD"] == "POST")
						{
							if (empty($_POST["pType"])) { $error = "Missing pType"; }
							
							else {
								$pType = $_POST["pType"];
								
								if ($pType=="newTicket") {
									if (empty($_POST["name"])) { 
										$error = "Missing name"; 
									} else { $name = check_form($_POST["name"]); }
									
									if (empty($_POST["department"])) {
										$error = "Missing department";
									} else { $department = check_form($_POST["department"]); }
									
									if (empty($_POST["description"])) {
										$error = "Missing description";
									} else { $description = check_form($_POST["description"]); }
									
									if (empty($_POST["severity"])) {
										$error = "Missing severity";
									} else { $severity = check_form($_POST["severity"]); }
								}
								
								if ($pType=="newFilter") {
									if (empty($_POST["fltobject"])) {
										$error = "Missing Filter Type";
									} else { $fltobject = check_form($_POST["fltobject"]); }
									
									if (empty($_POST["fltvalue"])) {
										$error = "Missing Filter Value";
									} else { $fltvalue = check_form($_POST["fltvalue"]); }
								}
								
								if ($pType=="newClose"){
									if (empty($_POST["closeid"])) {
										$error = "Missing Ticket ID";										
									} else { $closeid = check_form($_POST["closeid"]); }
								}
								
								if ($pType=="editTicket"){
									if (empty($_POST["editStatus"])) {
										$error = "Missing value for Status";
									} else { $editstatus = check_form($_POST["editStatus"]); }
									if (empty($_POST["editAssigned"])) {
										$error = "Missing value for Assigned";
									} else { $editassigned = check_form($_POST["editAssigned"]); }
									if (empty($_POST["editDept"])) {
										$error = "Missing value for Department";
									} else { $editdept = check_form($_POST["editDept"]); }
									if (empty($_POST["editSev"])) {
										$error = "Missing value for Severity";
									} else { $editsev = check_form($_POST["editSev"]); }
									if (empty($_POST["editId"])) {
										$error = "Missing value for ID";
									} else { $editid = check_form($_POST["editId"]); }
									if (empty($_POST["editUpdated"])) {
										$error = "Missing value for Updated";
									} else { $editupdated = check_form($_POST["editUpdated"]); }
									if (empty($_POST["editDesc"])) {
										$error = "Missing value for Description";
									} else { $editdesc = check_form($_POST["editDesc"]); }
									if (empty($_POST["editNotes"])) {
										$error = "Missing value for Notes";
									} else { $editnotes = check_form($_POST["editNotes"]); }
								}								
							}
						}						

						
						if (!empty($error)) 
						{ 
							printf('<div class="command E">FORM ERROR: %s</div>',$error); 
						} else {
							
/* IF NO ISSUES - CREATE A PDO */

							if (!empty($_POST)) 
							{ 
								$dbhost = "localhost";
								$dbname = "ticketsys";
								$dbuser = "ticketsys";
								$dbpass = "derp99";
									
								$pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, 
											array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

/* NEW TICKET QUERY */										
								if ($pType=="newTicket") {
									$created = date('Y-m-d');
									$updated = date('Y-m-d H:i:s');
									$status = "Open";
									$assigned = "Unassigned";
									
									try {
									
										$query = $pdo->prepare("INSERT INTO tickets (name,created,updated,department,severity,description,status,assigned) VALUES 		(:name,:created,:updated,:department,:severity,:description,:status,:assigned)");
										
										$data = array(
										  ':name'=>$name,
										  ':created'=>$created,
										  ':updated'=>$updated,
										  ':department'=>$department,
										  ':severity'=>$severity,
										  ':description'=>$description,
										  ':status'=>$status,
										  ':assigned'=>$assigned
										  );

										
										$query->execute($data);
										
										printf('<div class="command S">Ticket created successfully.</div>');
									}

									catch(PDOException $e) {
										printf('<div class="command E">Error - Could not create ticket... %s</div>', $e->getMessage());
									}
								}

/* FILTER SEARCH QUERY */								
								// This happens in the display query, scroll down!

/* CLOSE TICKET QUERY */								
								if ($pType=="newClose") {
								
									try {
									
										$query = $pdo->prepare("UPDATE tickets SET status = 'Closed' WHERE id = {$closeid}");			
										$query->execute();
										
										printf('<div class="command S">Ticket closed successfully.</div>');
									}

									catch(PDOException $e) {
										printf('<div class="command E">Error - Could not close ticket... %s</div>', $e->getMessage());
									}
								
								}
								
/* EDIT TICKET QUERY */
								if ($pType=="editTicket") {
								
									try {
									
										$query = $pdo->prepare("UPDATE tickets 
																SET 
																	updated = :editupdated,
																	department = :editdept,
																	severity = :editsev,
																	description = :editdesc,
																	status = :editstatus,
																	assigned = :editassigned,
																	notes = :editnotes
																WHERE id = {$editid}");

									    $query->bindParam(':editupdated',$editupdated, PDO::PARAM_STR);
									    $query->bindParam(':editdept',$editdept, PDO::PARAM_STR);										
									    $query->bindParam(':editsev',$editsev, PDO::PARAM_STR);
									    $query->bindParam(':editdesc',$editdesc, PDO::PARAM_STR);
									    $query->bindParam(':editstatus',$editstatus, PDO::PARAM_STR);
									    $query->bindParam(':editassigned',$editassigned, PDO::PARAM_STR);
										$query->bindParam(':editnotes',$editnotes, PDO::PARAM_STR);
										  
										$query->execute();
										
										printf('<div class="command S">Ticket edited successfully.</div>');
									}

									catch(PDOException $e) {
										printf('<div class="command E">Error - Could not edit ticket... %s</div>', $e->getMessage());
									}									
									
								}
						    } else {
								printf('<div class="command N">No messages to report.</div>');
						    }

						}

/* DISPLAY CURRENT CASE IF APPLICABLE */
						
			if (!empty($_GET["caseopen"])) {
			
				$updated = date('Y-m-d H:i:s');
				$formception = htmlspecialchars($_SERVER["PHP_SELF"]);
				
				printf('<div id="casewrapper">');
				
				$caseid = check_form($_GET["caseopen"]);
				
				$dbhost = "localhost";
				$dbname = "ticketsys";
				$dbuser = "ticketsys";
				$dbpass = "derp99";
				
				$pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, 
							array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
				
				$query = $pdo->prepare("SELECT * FROM tickets WHERE id = :caseid");						
				$query->bindParam(':caseid', $caseid, PDO::PARAM_INT);
								
				$query->execute();
				
				while ($row = $query->fetch()) {
					$idfound = 1;
					printf('<div id="caseedit">
					
						<form name="editticket" action="%s" method="post">
							
							<table id="editticketinfo"><tr><td>Case ID: %s</td><td>Name: %s</td><td>Severity: %s</td><td>Created: %s</td><td>Updated: %s</td></tr></table>
							
							Status: <input type="text" name="editStatus" value="%s" /> 
							Assigned: <input type="text" name="editAssigned" value="%s" />
							Department: <input type="text" name="editDept" value="%s" />
							Severity: 	<select id="editSev" name="editSev">
											<option value="1">--</option>
											<option value="1">1 - High</option>
											<option value="2">2 - Medium</option>
											<option value="3">3 - Low</option>
										</select><br />
							
							Description: <input type="text" name="editDesc" value="%s" class="longdescbox" /><br /><br />
							
							<textarea name="editNotes" rows="5" cols="80" placeholder="Notes">%s</textarea><br />
							<input type="hidden" name="editId" value="%s" />
							<input type="hidden" name="editUpdated" value="%s" />
							<input type="hidden" name="pType" value="editTicket" />
							<input type="submit" value="Submit" />	
						</form>
							</div>',
							$formception, $row["id"], $row["name"], $row["severity"], $row["created"], $updated, $row["status"], $row["assigned"], $row["department"], $row["description"], $row["notes"], $row["id"], $updated
					);
				}
				
				if (empty($idfound)) { printf("No cases found with that ID."); }
				
				printf('</div>');
			}
		?>
		
		
		<div id="tickets">
			<div id="ticketman">

					<a href="#newticket" class="call-modal" title="Create new ticket.">New</a>	
						<section class="semantic-content" id="newticket" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
							<div class="modal-inner">
								<div class="modal-content">
									<form name="newticket" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
										<select id="severity" name="severity">
											<option value="1">1 - High</option>
											<option value="2" selected>2 - Medium</option>
											<option value="3">3 - Low</option>
										</select><br />
										<input type="text" name="name" placeholder="Name" /><br />
										<input type="text" name="department" placeholder="Department" /><br />
										<textarea name="description" rows="10" cols="40" placeholder="Description"></textarea><br />
										<input type="hidden" name="pType" value="newTicket" />
										<input type="submit" value="Submit" />		 
									</form>	
								</div>
							</div>
							<a href="#!" class="modal-close" title="Close this modal" data-dismiss="modal">&times;</a>
						</section>
						
					<a href="#filterview" class="call-modal" title="Filter ticket view.">Filter</a>	
						<section class="semantic-content" id="filterview" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
							<div class="modal-inner">
								<div class="modal-content">
									<form name="filterview" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
										<select id="fltobject" name="fltobject">
											<option value="name">Name</option>
											<option value="department" selected>Department</option>
											<option value="severity">Severity</option>
											<option value="description">Description</option>
											<option value="status">Status</option>
											<option value="assigned">Assigned</option>
										</select><br />
										<input type="text" name="fltvalue" placeholder="Value" /><br />
										<input type="hidden" name="pType" value="newFilter" />	
										<input type="submit" value="Submit" />		 
									</form>	
								</div>
							</div>
							<a href="#!" class="modal-close" title="Close this modal" data-dismiss="modal">&times;</a>
						</section>
				
					<a href="#closecase" class="call-modal" title="Close a ticket.">Close</a>	
						<section class="semantic-content" id="closecase" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
							<div class="modal-inner">
								<div class="modal-content">
									<form name="closeticket" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
										<input type="text" name="closeid" placeholder="Case ID" /><br />									
										<input type="hidden" name="pType" value="newClose" />
										<input type="submit" value="Submit" />		 
									</form>	
								</div>
							</div>
							<a href="#!" class="modal-close" title="Close this modal" data-dismiss="modal">&times;</a>
						</section>
						<script src="js/modal.js"></script>
				

			</div>
			<table id="ticketlist" class="tablesorter">
				<thead>
					<tr id="tickhead" class="tick"><th>Ticket</th><th>Name</th><th>Created</th><th>Updated</th>
					<th>Department</th><th class="colsev">Severity</th><th>Description</th><th>Status</th><th>Assigned</th></tr>
				</thead>
				<tbody>
				<?php

/* DISPLAY TICKETS */
				
					$dbhost = "localhost";
					$dbname = "ticketsys";
					$dbuser = "ticketsys";
					$dbpass = "derp99";
									
					$pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, 
								array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
/* IF FILTERED */				
					if ($pType=="newFilter") {	
						$query = $pdo->prepare("SELECT * FROM tickets WHERE {$fltobject} LIKE :fltvalue");						
						$query->bindParam(':fltvalue', $fltvalue, PDO::PARAM_STR);
					} else {
						$query = $pdo->prepare("SELECT * FROM tickets");
					}	

					$query->execute();					
					
					while ($row = $query->fetch()) {
						if ($row[status] == "Closed") {
							printf(
							 "<tr class='tick'>
								<td><a href='?caseopen=%s'>%s</a></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td class='tick_closed'>%s</td><td>%s</td>
							</tr>",
							$row["id"], $row["id"], $row["name"], $row["created"], $row["updated"], $row["department"],
							$row["severity"], $row["description"], $row["status"], $row["assigned"]
							);
						} else {
							printf(
							 "<tr class='tick'>
								<td><a href='?caseopen=%s'>%s</a></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td class='tick_open'>%s</td><td>%s</td>
							</tr>",
							$row["id"], $row["id"], $row["name"], $row["created"], $row["updated"], $row["department"],
							$row["severity"], $row["description"], $row["status"], $row["assigned"]
							);
						}
					}

				?>
				</tbody>
			</table>
		</div>
		<div id="footer">
			TicketSys - Copyright 2015 - James Carr
		</div>

	</body>
</html>