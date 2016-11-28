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
					<h1 class="center">Inschrijven</h1>
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
							
							if( isset( $_SESSION['uniqid'] ) ) {
							
								header( "Location: index.php" );
							} else {
								
								if ( isset( $_POST['submit'] ) ) {
									
									//validate username
									if( strlen( $_POST['username'] ) < 3 ) {
									
										$invalid_name = true;
										$message .= "Vul een username in bestaande uit minimaal drie karakters<br>";
									}
							
									//validate email
									if( preg_match( '#[a-zA-Z]{2,}@[a-zA-Z]{2,}(?:\.nl|\.com)#', $_POST['email'] ) === 0 ) {
									
										$message .= "Vul een correct email adres in eindigend op .nl<br>";
										$invalid_email = true;
									} 
								
									// if all are correct: register the user
									if( !isset ( $invalid_email ) && !isset ( $invalid_name ) ) {
										
										
										$stmt = mysqli_prepare( $link, "select ID from gebruikers where naam = 'admin';" );
										mysqli_stmt_execute( $stmt ) or die( mysqli_stmt_error( $stmt ) );
										mysqli_stmt_store_result( $stmt );
										
										if( mysqli_stmt_num_rows( $stmt ) >= 1 && $_POST['username'] == 'admin') {
											
											mysqli_stmt_close( $stmt );
											$message = 'Er is al een administrator account aangemaakt.';
										} else {
											
											mysqli_stmt_close( $stmt );
											
											//check if email is already in use
											$email = $_POST['email'];
											
											$stmt = mysqli_prepare( $link, "select ID from gebruikers where emailadres = ? " );
											mysqli_stmt_bind_param( $stmt, "s", $email );
											mysqli_stmt_execute( $stmt ) or die ( "Failed to query the database, " . mysqli_stmt_error( $stmt ) );
											mysqli_stmt_store_result( $stmt );
											if( mysqli_stmt_num_rows( $stmt ) >= 1 ) {
												
												mysqli_stmt_close( $stmt );
												$message = 'Dit email adres is al in gebruik. Vul alstublieft een ander email adres in.';
											} else {
												
												mysqli_stmt_close( $stmt );
												
												//generate password
												$pass_charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*(){}[]";
												$special_chars = "!@#$%^&*(){}[]";
												$numbers = "0123456789";
												$capitals = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
												$alphabet = "abcdefghijklmnopqrstuvwxyz";
												$pass_base = $special_chars[rand(0, strlen($special_chars -1))].$numbers[rand(0, strlen($numbers -1))].$capitals[rand(0, strlen($capitals -1))].$alphabet[rand(0, strlen($alphabet -1))];
												$pass = substr( str_shuffle($pass_charset), 0, 9 - 4);
												$password = str_shuffle($pass_base.$pass);
												
												// register the user in database
												$uniqid = uniqid();
												$username = $_POST['username'];
												$password_hash = md5 ( $password );
												
												$stmt = mysqli_prepare( $link, "insert into gebruikers(uniqid_gebruiker, naam, emailadres, wachtwoord) values( ?, ?, ?, ?);" );
												mysqli_stmt_bind_param( $stmt, "ssss", $uniqid, $username, $email, $password_hash );
												mysqli_stmt_execute( $stmt ) or die ( "Failed to query the database, " . mysqli_stmt_error( $stmt ) );
												
												if ( mysqli_stmt_affected_rows( $stmt ) == 1) {
													
													mysqli_stmt_close( $stmt );
													
													//mail user
													//using the post value because some characters in the username might be escaped with slashes in $escaped_uername
													$to = $_POST['email'];
													$subject = "Bevestiging inschrijving bloggers website";
													$header  = 'From: phpbot@phpmail.com';
													$msg  = "Beste {$_POST['username']},\nU heeft zich ingeschreven bij onze website voor bloggers.\nUw gebruikersnaam is: $escaped_email\nUw automatich gegenereerd wachtwoord is: " . $password;
													
													//mail user
													if( @mail( $to, $subject, $msg, $header) ) {
														
														header("Location: index.php");
														exit();
													} else {
														$mail = htmlentities($_POST['email']);
														$message = "Uw account is aangemaakt maar het versturen van een bevestigings email naar $mail is mislukt.";
													}
												} else {
													
													mysqli_stmt_close( $stmt );
													$message = 'Het toevoegen van de gebruiker is mislukt.';
												}
											}
										}
									}
								}
							}	
						?>
						<form method="post" action="inschrijven.php" class="shedule container">
							<br>
							<div style="color:red;" class="center">
								<?php print $message;?>
							</div>
							<br>
							Vul hieronder je naam en emailadres (als gebruikersnaam) in.<br><br>
							<label>Naam</label>
							<input type="text" size="20" placeholder="Vul hier uw naam in" required <?php if( isset( $invalid_name ) ) echo 'style="border-color:red; border-width: 3px"'; ?> name="username" value="<?php if( isset( $_POST['username'] ) ) echo $_POST['username']; ?>"><br>
							<label>Email</label>
							<input type="text" size="50" name="email" placeholder="Vul hier uw emailadres in." required <?php if( isset( $invalid_email ) ) echo 'style="border-color:red; border-width: 3px"'; ?> value="<?php if ( isset( $_POST['email'] ) ) echo $_POST['email']; ?>"><br>
							<input class="submitbtn" type="submit" name="submit" value="Registreer"> <a href="index.php" style='color: white;' class="cancelbtn">Cancel</a>
			   			</form>	
					</div>
				</div>
			</div>
		</body>
	</html>