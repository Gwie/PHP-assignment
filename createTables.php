<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8">
			<title>Inzendoopdracht 051R7</title>
		</head>
		<body>
			<?php
				include_once 'db_connect.php';
				
				//create users table
				$stmt = mysqli_prepare($link, "create table gebruikers(ID int not null auto_increment primary key, uniqid_gebruiker varchar(13) unique not null, naam varchar(20) not null, emailadres varchar(50) not null, wachtwoord varchar(32) not null);" );
				mysqli_stmt_execute( $stmt ) or die (mysqli_stmt_error( $stmt ) );
				mysqli_stmt_close( $stmt );
				
				echo "Table 'gebruikers' created<br>";
				//create posts table
				$stmt = mysqli_prepare($link, 'create table blogs(ID int not null auto_increment primary key, uniqid_blog varchar(13) unique not null, onderwerp varchar(150) not null, datum datetime not null, blogtekst text not null, GebruikersID int not null, foreign key (GebruikersID) references gebruikers(ID));' );
				mysqli_stmt_execute( $stmt )or die( mysqli_stmt_error( $stmt ) );
				mysqli_stmt_close( $stmt );
				
				echo "Table 'blogs' created<br>";
				header("Location: index.php")
			?>
		</body>
	</html>
