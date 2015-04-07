<?php

/* CREATES A PDO */
	function newPDO() {
		$dbhost = "localhost"; $dbname = "ticketsys"; $dbuser = "ticketsys"; $dbpass = "derp99";
										
		$pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, 
						array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
						
		return $pdo;
	}

/* PULLS TICKETS FROM DATABASE */
	function get_tickets($fltobject, $fltvalue, $pType) {

		$pdo = newPDO();
		
		/* IF FILTERED */				
		if ($pType=="newFilter") {	
			$query = $pdo->prepare("SELECT * FROM tickets WHERE {$fltobject} LIKE :fltvalue");						
			$query->bindParam(':fltvalue', $fltvalue, PDO::PARAM_STR);
		} else {
			$query = $pdo->prepare("SELECT * FROM tickets");
		}	

		$query->execute();
		
		return $query;
	}

/* PULLS SPECIFIC TICKET FROM DATABASE */
	function get_record($caseid) {

		$pdo = newPDO();
		
		$query = $pdo->prepare("SELECT * FROM tickets WHERE id = :caseid");						
		$query->bindParam(':caseid', $caseid, PDO::PARAM_INT);
						
		$query->execute();
		
		return $query;
	}

/* INSERTS A NEW TICKET IN TO THE DATABASE */
	function new_ticket($pdo,$name,$created,$updated,$department,$severity,$description,$status,$assigned) {
		$query = $pdo->prepare("INSERT INTO tickets
									(name,created,updated,department,severity,description,status,assigned) 
								VALUES 		
									(:name,:created,:updated,:department,:severity,:description,:status,:assigned)");
		
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
	}
	
/* CLOSE A TICKET */
	function close_ticket($pdo,$closeid) {
		$query = $pdo->prepare("UPDATE tickets SET status = 'Closed' WHERE id = {$closeid}");			
		$query->execute();
	}
	
/* EDIT A TICKET */
	function edit_ticket($pdo,$editStatus,$editAssigned,$editDept,$editSev,$editId,$editUpdated,$editDesc,$editNotes) {

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
	}
?>