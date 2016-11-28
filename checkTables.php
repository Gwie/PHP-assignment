<?php
	
	include_once 'errorhandler.php'; set_error_handler('myerrorhandler', E_ALL);
	
	$stmt = mysqli_prepare($link, "show tables where tables_in_dbloi in('gebruikers', 'blogs');" );
	mysqli_stmt_execute( $stmt ) or die( trigger_error( mysqli_stmt_error( $stmt ) ) );
	mysqli_stmt_store_result( $stmt );
		
	if( mysqli_stmt_num_rows( $stmt ) < 2 ) {
			
		 trigger_error( "Could not find all required database tables." . mysqli_stmt_num_rows($stmt) );
	}
		
	mysqli_stmt_close( $stmt );
?>
