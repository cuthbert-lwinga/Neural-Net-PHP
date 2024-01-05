<?php
ini_set('memory_limit', '1024M'); // Increase the memory limit to 1024MB
include_once("../../CLASSES/Headers.php");

//echo "\nhello\n";

$ImageProcessor = new ImageProcessor("0000.png");

var_dump($ImageProcessor->getImageGrayscaleArray(["rows"=>28,"cols"=>28],$grayScale = true));


?>