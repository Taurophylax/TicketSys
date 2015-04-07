<?php

/* Sanitize */
	function check_form($X) {
		$X = trim(stripslashes(htmlspecialchars($X)));
		return $X;
	}

?>