<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8">
			<title>Inzendopdracht 051R7</title>
			<link rel="stylesheet" href="styles/styles.css">
		</head>
		<body>
			<div class="home">
				<?php include_once 'errorhandler.php'; set_error_handler('myerrorhandler', E_ALL); include_once 'db_connect.php'; include_once 'checktables.php'; session_start();?>
				<br>
				<hr>
				<div class="title">
					<img class="imgleft" alt="404 image not found" src="images\avatar.png">
					<img class="imgright" alt="404 image not found" src="images\avatar.png">
					<h1 class="center">Blog aanmaken</h1>
				</div>
				<hr>
				<div class="nav">
					<ul>
						<li class='left nav'><a href=index.php>Home</a></li>
						<?php
							if( !isset( $_SESSION['uniqid'] ) ) {
							
								echo "<li class='right'><a href=login.php>Login</a></li>\n
									<li class='right'><a href=inschrijven.php>Inschrijven</a></li>";
							
							} else {
								
								echo"<li class='right'><a href=logout.php>Uitloggen</a></li>\n
								<li><a href='index.php?usr={$_SESSION['uniqid']}&edit=true'>Bewerken</a></li>\n";
								
								$stmt = mysqli_prepare( $link, "select naam from gebruikers where uniqid_gebruiker = ? ;" );
								mysqli_stmt_bind_param( $stmt, "s", $_SESSION['uniqid'] );
								mysqli_stmt_execute( $stmt ) or die( mysqli_stmt_error( $stmt ) );
								mysqli_stmt_store_result( $stmt );
								mysqli_stmt_bind_result( $stmt, $username );
								mysqli_stmt_fetch( $stmt );
								mysqli_stmt_close( $stmt );
								
								if( $username != "admin" ) {
									
									echo"<li><a href='createBlog.php'>Blog aanmaken</a></li>\n";
								}
							}
						?>
					</ul>
				</div>
				<div class="bodybox">
					<div class="wrap">
						<div class="userlist">
							<br>
							<ul>
								<?php
								
									$stmt = mysqli_prepare( $link, "select uniqid_gebruiker, naam from gebruikers where naam != 'admin' ;" );
									mysqli_stmt_execute( $stmt ) or die( mysqli_stmt_error( $stmt ) );
									mysqli_stmt_store_result($stmt);
									mysqli_stmt_bind_result( $stmt, $uniqid, $gebruikersnaam );
									
									if( mysqli_stmt_num_rows( $stmt ) < 1 ) {
										
										mysqli_stmt_close( $stmt );
										echo "<li>Er hebben zich nog geen gebruikers geregistreerd</li>";
									} else {
										
										while( mysqli_stmt_fetch( $stmt ) ) {
											
											echo "<a href='index.php?usr=$uniqid'><li>$gebruikersnaam</li></a>";
										}
										mysqli_stmt_close( $stmt );
									}
								?>
							</ul>
						</div>
					</div>
					<div class="blogwindow">
						<?php
							if( !isset( $_SESSION['uniqid'] ) ) {
							
								header( "Location: index.php" );
							}
							
							//get username and ID
							$stmt = mysqli_prepare( $link, "select naam, ID from gebruikers where uniqid_gebruiker = ? ;" );
							mysqli_stmt_bind_param( $stmt, "s", $_SESSION['uniqid'] );
							mysqli_stmt_execute( $stmt ) or die( mysqli_stmt_error( $stmt ) );
							mysqli_stmt_store_result($stmt);
							mysqli_stmt_bind_result( $stmt, $naam, $ID);
							mysqli_stmt_fetch( $stmt );
							
							if( mysqli_stmt_num_rows( $stmt ) != 1 ) {
								
								mysqli_stmt_close( $stmt );
								trigger_error("Failed to get user ID");
							}
							
							mysqli_stmt_close( $stmt );
							
							if( $naam == 'admin' ) {
								
								header( "Location: index.php" );
							}
							
							if( isset( $_POST['submit'] ) ) {
								
								//create uniqid for blog
								$uniqid_blog = uniqid();
								
								$subject = $_POST['subject'];
								$date = date('Y-m-d H:i:s');
								$text = $_POST['text'];
								
								$stmt = mysqli_prepare( $link, "insert into blogs(uniqid_blog, onderwerp, datum, blogtekst, GebruikersID) values( ?, ?, ?, ?, ?);" );
								mysqli_stmt_bind_param( $stmt, "ssssi", $uniqid_blog, $subject, $date, $text, $ID );
								mysqli_stmt_execute( $stmt ) or die( mysqli_stmt_error( $stmt ) );
								
								if( mysqli_stmt_affected_rows( $stmt ) == 1 ) {
									
									mysqli_stmt_close( $stmt );
									header( "Location: index.php?usr={$_SESSION['uniqid']}&edit=true" );
								} else {
									
									mysqli_stmt_close( $stmt );
									trigger_error( "Something went wrong with the query" );
								}
							}
						?>
						<form action="createBlog.php" method="post">
			  				<div class="container">
				   				<label>Onderwerp</label>
								<input type="text" name="subject" size="50" placeholder="Vul hier het onderwerp in" required>
								<label>Blogtekst</label>
								<textarea rows="25" cols="50" name="text" required placeholder="Vul hier de blogtekst in"></textarea>
								<input type="submit" name="submit" value="Blog aanmaken" class="submitbtn"><input type="reset" class="cancelbtn" value="Reset">
			  				</div>
			   			</form>
					</div>
				</div>
			</div>
		</body>
	</html>