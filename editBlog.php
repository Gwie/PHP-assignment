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
					<h1 class="center">Blog Bewerken</h1>
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
								
								$stmt = mysqli_prepare( $link, "select ID, naam from gebruikers where uniqid_gebruiker = ? ;" );
								mysqli_stmt_bind_param( $stmt, "s", $_SESSION['uniqid'] );
								mysqli_stmt_execute( $stmt ) or die( mysqli_stmt_error( $stmt ) );
								mysqli_stmt_store_result( $stmt );
								mysqli_stmt_bind_result( $stmt, $ID, $username );
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
							if( !isset( $_SESSION['uniqid'] ) || !isset( $_GET['bid'] ) ) {
								
								header( "Location: index.php" );
							}
							
							$stmt = mysqli_prepare( $link, "select blogs.onderwerp, blogs.blogtekst, blogs.GebruikersID from blogs where blogs.uniqid_blog = ? ;" );
							mysqli_stmt_bind_param( $stmt, "s", $_GET['bid'] );
							mysqli_stmt_execute( $stmt ) or die( mysqli_stmt_error( $stmt ) );
							mysqli_stmt_bind_result( $stmt, $onderwerp, $blogtekst, $ownerID );
							mysqli_stmt_store_result( $stmt );
							mysqli_stmt_fetch( $stmt );
							mysqli_stmt_close( $stmt );
							
							if( $ownerID != $ID && $username !== 'admin' ) {
								
								header("Location: index.php");
							}
							
							if( isset( $_POST['submit'] ) ) {
								
								if ( $_POST['subject'] == $onderwerp && $_POST['text'] == $blogtekst ) {
									
									$message = "U heeft geen wijzigingen aangebracht in deze blog";
								} else {
									
									$subject = $_POST['subject'];
									$text = $_POST['text'];
									
									
									$stmt = mysqli_prepare( $link, "update blogs set onderwerp = ? , blogtekst = ? where uniqid_blog = ? ;" );
									mysqli_stmt_bind_param( $stmt, "sss", $subject, $text, $_GET['bid'] );
									mysqli_stmt_execute( $stmt );
									
									if( mysqli_stmt_affected_rows( $stmt ) == 1 ) {
										
										mysqli_stmt_close( $stmt );
										header( "Location: index.php?usr={$_SESSION['uniqid']}&edit=true" );
										exit();
										
									} else {
										
											mysqli_stmt_close( $stmt );
											trigger_error( "Something went wrong with the query" );
									}
								}
							}
						?>
						<form method="post" action="editBlog.php?bid=<?php echo $_GET['bid']?>" class="blogform">
							<div style="color:red;" class="center">
							<?php if( isset( $message ) ) { echo $message . "<br>"; unset( $message ); }?>
							</div>
							<label>Onderwerp</label>
							<input type="text" name="subject" size="50" required value="<?php echo htmlspecialchars( $onderwerp ); ?>">
							<textarea rows="25" cols="50" name="text" required><?php echo htmlspecialchars( $blogtekst ); ?></textarea>
			  				<input type="submit" name="submit" value="Wijzigingen toepassen" class="submitbtn">
			   				<a href="index.php?usr=<?php echo $_SESSION['uniqid']; ?>&edit=true" class="cancelbtn">Annuleren</a>
						</form>
					</div>
				</div>
			</div>
		</body>
	</html>