<?php
	
	include_once 'errorhandler.php';
	
	// establish connection with the database
	if( !$link = @mysqli_connect( 'localhost', 'root', '', 'dbloi' ) ) {
		
		trigger_error( 'Could not connect to the database.');
	}
?>