<?php
#Header
header('Content-Type: text/html; charset=utf-8');

require 'vendor/autoload.php';

#If file name is given
if (isset($_GET['file'])) {
	#Get file contents
	$content = file_get_contents('./md/' . $_GET['file'] . '.md');

	$Parsedown = new Parsedown();
	
	#Return parsed content
	echo $Parsedown->text($content);
} else {
	#Get error file's content
	$content = file_get_contents('./md/noParameter.md');

	$Parsedown = new Parsedown();
	
	#Return parsed content
	echo $Parsedown->text($content);
}
?>
