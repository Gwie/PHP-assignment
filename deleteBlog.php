<?php
	include_once 'errorhandler.php';
	set_error_handler('myerrorhandler', E_ALL);
	include_once 'db_connect.php';
	include_once 'checktables.php';
	session_start();
				
	if( !isset( $_SESSION['uniqid'] ) || !isset( $_GET['bid'] ) ) {
				
		header( "Location: index.php" );
	}
	
	//get user id and name
	$stmt = mysqli_prepare( $link, "select ID, naam from gebruikers where uniqid_gebruiker = ? ;" );
	mysqli_stmt_bind_param( $stmt, "s", $_SESSION['uniqid'] );
	mysqli_stmt_execute( $stmt ) or die( mysqli_stmt_error( $stmt ) );
	mysqli_stmt_store_result( $stmt );
	mysqli_stmt_bind_result( $stmt, $ID, $username );
	mysqli_stmt_fetch( $stmt );
	
	if( mysqli_stmt_num_rows( $stmt ) != 1 ) {
		
		mysqli_stmt_close( $stmt );
		trigger_error("Failed to get user ID");
	}	
	mysqli_stmt_close( $stmt );
	
	//get ownerID
	$stmt = mysqli_prepare( $link, "select GebruikersID from blogs where uniqid_blog = ?;" );
	mysqli_stmt_bind_param( $stmt, "s", $_GET['bid'] );
	mysqli_stmt_execute( $stmt ) or die( mysqli_stmt_error( $stmt ) );
	mysqli_stmt_store_result( $stmt );
	mysqli_stmt_bind_result( $stmt, $ownerID );
	mysqli_stmt_fetch( $stmt );
	mysqli_stmt_close( $stmt );
	
				
	//check if the blog is owned by the user that is currently logged in
	if( $ownerID != $ID && $username !== 'admin' ) {
		
		header("Location: index.php");
	} else {
		
		$stmt = mysqli_prepare( $link, "delete from blogs where uniqid_blog = ? ;");
		mysqli_stmt_bind_param( $stmt, "s", $_GET['bid']);
		mysqli_stmt_execute( $stmt );
		
		if( mysqli_stmt_affected_rows( $stmt ) == 1 ) {
			
			mysqli_stmt_close( $stmt );
			header( "Location: index.php?usr={$_SESSION['uniqid']}&edit=true" );
		} else {
			
			mysqli_stmt_close( $stmt );
			trigger_error( "Something went wrong with the query" );
		}
	}
?>