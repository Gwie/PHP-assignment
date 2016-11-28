<?php
	function myerrorhandler($type, $message, $file, $line) {
			
		if( error_reporting() !== 0 ) {
			
			echo "<!--<div class='overlay'>
				</div>-->
				<div class='modal'><h2>An error occured</h2>
				$message<br>
				<div class='details'><h3>error details</h3>
				<p>
				number: $type<br>
				file: $file<br>
				line: $line</div>
				</p>
				<div class='errorimg'>
				<img src='images/error.png' width=100 height=100>
				</div>
				</div>";
			
				exit();
		}
			
	}
?>