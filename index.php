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
					<h1 class="center">Bloggers Website</h1>
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
									mysqli_stmt_store_result( $stmt );
									$userlist = mysqli_stmt_store_result( $stmt );
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
						//check if edit mode is set and if not set declare edit as false
						if ( !isset( $_GET['edit'] ) ) { $_GET['edit'] = false; }
						
							if( isset( $_SESSION['uniqid'] ) && $username == 'admin' && ( !isset( $_GET['usr'] ) || $_GET['usr'] == $_SESSION['uniqid'] ) && $_GET['edit'] == true ) {
								
								echo "<h2>Administrator paneel</h2><hr><br><br>";
								echo "Klik op de naam van het persoon om een lijst van dienst blogs weer te geven om ze vervolgends te bewerken / verwijderen<br><br>";
								
								$stmt = mysqli_prepare( $link, "select uniqid_gebruiker, naam from gebruikers where naam != 'admin' ;" );
								mysqli_stmt_execute( $stmt ) or die( mysqli_stmt_error( $stmt ) );
								mysqli_stmt_store_result( $stmt );
								mysqli_stmt_bind_result( $stmt, $uniqid, $gebruikersnaam );
									
								if( mysqli_stmt_num_rows( $stmt ) < 1 ) {
								
									mysqli_stmt_close( $stmt );
									echo"Er hebben zich nog geen gebruikers aangemeld";
								} else {
									
									echo "<table class='bloglist'>";
									while( mysqli_stmt_fetch( $stmt ) ) {
											
										echo "<tr><td><a href='index.php?usr=$uniqid&edit=true'>$gebruikersnaam</a></td></tr>";
									}
									echo "</table>";
									mysqli_stmt_close( $stmt );
								}
							} elseif( isset( $_GET['usr'] ) ) {
								
								//get user ID and name
								$stmt = mysqli_prepare( $link, "select ID, naam from gebruikers where uniqid_gebruiker = ? ;" );
								mysqli_stmt_bind_param( $stmt, "s", $_GET['usr'] );
								mysqli_stmt_execute( $stmt ) or die( trigger_error( mysqli_stmt_error( $stmt ) ) );
								mysqli_stmt_store_result( $stmt );
								mysqli_stmt_bind_result( $stmt, $ID, $naam );
								
								if( mysqli_stmt_num_rows( $stmt ) != 1 ) {
									
									mysqli_stmt_close( $stmt );
									trigger_error("Failed to get user ID");
								} else {
									
									mysqli_stmt_fetch( $stmt );
									mysqli_stmt_close( $stmt );
								}
								
								echo "<h2>$naam's blogs</h2><hr><br><br>";

								
								
								
								//check if the correct user is logged in and is in edit mode or if the administrator is logged in
								if( isset( $_SESSION['uniqid'] ) && $_GET['edit'] == true && ( $_SESSION['uniqid'] == $_GET['usr'] || $username == 'admin' ) ) {
									
									$stmt = mysqli_prepare( $link, "select onderwerp, datum, uniqid_blog from blogs where GebruikersID = ? order by datum DESC;" );
									mysqli_stmt_bind_param( $stmt, "s", $ID );
									mysqli_stmt_execute( $stmt ) or die( trigger_error( mysqli_stmt_error( $stmt ) ) );
									mysqli_stmt_store_result( $stmt );
									mysqli_stmt_bind_result( $stmt, $subject, $date, $bid );
									
									if( mysqli_stmt_num_rows( $stmt ) < 1 ) {
										
										echo "U heeft nog geen blogs op deze site aangemaakt.<br>";
									} else {
										
										echo "<table class='bloglist'>";
										while( mysqli_stmt_fetch( $stmt ) ) {
												
											echo "<tr><td>$subject</td><td>Aangemaakt op $date</td><td><a href='editBlog.php?bid=$bid'>Bewerken</a> / <a href='deleteBlog.php?bid=$bid'>Verwijderen</a></td></tr>";
										}
										echo "</table>";
									}
								} else {
								
								//get blogs of user
								
									$stmt = mysqli_prepare( $link, "select onderwerp, datum, blogtekst from blogs where GebruikersID = ? order by datum DESC;" );
									mysqli_stmt_bind_param( $stmt, "s", $ID );
									mysqli_stmt_execute( $stmt ) or die( trigger_error( mysqli_stmt_error( $stmt ) ) );
									mysqli_stmt_store_result( $stmt );
									mysqli_stmt_bind_result( $stmt, $subject, $date, $text );
										
									if( mysqli_stmt_num_rows( $stmt ) < 1 ) {
										
										echo "$naam heeft nog geen blogs op deze site aangemaakt.";
									} else {
										
										while( mysqli_stmt_fetch( $stmt ) ) {
											
											$text = nl2br( htmlspecialchars( $text ) );
											echo "<table width='80%' class='blog'>";
											echo "<tr style='background-color: #CCC;'><th>$subject</th></tr>
												<tr style='background-color: #E6E6E6;'><td><br>Aangemaakt op $date<br><br></td></tr>
												<tr style='background-color: #F2F2F2;'><td><br>$text<br><br><hr></td></tr>";
											echo "</table><br>";
										}
									}
								}
											
							} else {
								
								echo"<h2>Welkom op onze website voor bloggers.</h2>
									<p>Op deze website kunt u uw eigen blog bijhouden en bewerken. U kunt ook de blogs van andere leden bekijken.</p>
									<h2>Om te beginnen</h2>
									<p>Om uw blog aan te maken op onze website moet u zich eerst <a href='inschrijven.php' style='text-decoration: none;'>inschrijven</a>.<br>
									Nadat u zich heeft ingeschreven krijgt u een E-mail op het door u opgegeven E-mail adres met daarin uw automatisch gegenereerde wachtwoord.</p>
									<p>Dit wachtwoord kunt u gebruiken samen met uw E-mail adres om op onze site <a href='login.php' style='text-decoration: none;'>in te loggen</a>.<br>
									Wanneer u bent ingelogd ziet u in de navigatiebalk de optie <cite>Bewerken</cite>. Wanneer u daarop klikt verschijnt er een lijst met al de door u aangemaakte blogs (als die aanwezig zijn).<br>
									Naast de optie <cite>Bewerken</cite> in de navigatiebalk ziet u de optie <cite>Blog aanmaken</cite>. Wanneer u kiest voor deze optie verschijnt er een venster waar u een onderwerp voor uw blog en de blogtekst kunt invoeren.<br>
									Als u uw gewenste onderwerp en blogtekst heeft ingevoerd kunt u uw blog opslaan door te klikken op de knop <cite>Blog aanmaken</cite>.</p>
									<p>In de lijst met de door u aangemaakte blogs staan het onderwerp van de blog en de aanmaak- datum en tijd.<br>
									Daarnaast staan nog twee opties: <cite>Bewerken</cite> en <cite>Verwijderen</cite>.<br>
									Als u kiest voor de optie <cite>Bewerken</cite> krijgt u een venster te zien vergelijkbaar met venster voor het aanmaken van een blog met als enig verschil dat het onderwerp en de blogtekst al zijn ingevuld.<br>
									U kunt het onderwerp en de tekst aanpassen. Als u het bewerken wilt annuleren klikt u op de knop <cite>Annuleren</cite>. Als uw bewerkingen wilt opslaan klikt u op de knop <cite>Wijzigingen toepassen</cite>.</p>
									<p>Om uw blogs te bekijken kiest u voor uw naam in de lijst met aangemelde gebruikers aan de linkerkant van de webpagina.<br>
									Om de blogs van een ander lid te bekijken kiest u voor diens naam in de lijst met aangemelde gebruikers</p>
									<p>Wij wensen u veel plezier toe op onze website!</p>";
							}
						?>
					</div>
				</div>
			</div>
		</body>
	</html>