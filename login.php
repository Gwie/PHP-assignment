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
					<h1 class="center">Inloggen</h1>
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
						$message = "";
						//for when someone who is already logged in goes back to this page
						if( isset( $_SESSION['uniqid'] ) ) {
							
							header( "Location: index.php" );
						} else {
							
							if( isset( $_POST['login'] ) ) {
									
								$password_hash = md5 ( $_POST['password'] );
								$username = $_POST['username'];
								
								$stmt = mysqli_prepare( $link, "select uniqid_gebruiker, wachtwoord from gebruikers where emailadres = ? ;" );
								mysqli_stmt_bind_param( $stmt, "s", $username );
								mysqli_stmt_execute( $stmt ) or die( mysqli_stmt_error( $stmt ) );
								mysqli_stmt_store_result( $stmt );
								mysqli_stmt_bind_result( $stmt, $uniqid, $password );
								mysqli_stmt_fetch( $stmt );
								mysqli_stmt_close( $stmt );

								// check if the user credentials are valid
								if( $password == $password_hash ) {
								
									$_SESSION['uniqid'] = $uniqid;
									header( "Location: index.php" );
								} else {
											
									$message = "<br><br>De ingevoerde gebruikersnaam en/of wachtwoord is niet juist.<br><br>";
								}
							}
						}
						?>
						<form action="login.php" method="post" class="container">
			  				<br>
			  				<div style="color:red;" class="center">
							<?php print $message;?>
							</div>
							<br>
							Vul hieronder uw inloggegevens in<br><br>
			   				<label><b>Gebuikersnaam (uw email adres)</b></label>
			    			<input type="text" placeholder="Vul hier uw gebruikersnaam (uw email adres) in" name="username" value="<?php if( isset( $_POST['username'] ) ) { echo $_POST['username']; } ?>" required>
			    			<label><b>Wachtwoord</b></label>
			    			<input type="password" placeholder="Vul hier uw wachtwoord in" name="password" value="<?php if( isset( $_POST['password'] ) ) { echo $_POST['password']; } ?>" required>
			    			<input class="submitbtn" type="submit" name="login" value="Login"> <a href="index.php" style='color: white;' class="cancelbtn">Cancel</a>
			   			</form>
					</div>
				</div>
			</div>
		</body>
	</html>


