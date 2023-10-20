<?php
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;
use NameSpaceOptimizerSGD\Optimizer_SGD;
use NameSpaceOptimizerAdagrad\Optimizer_Adagrad;
use NameSpaceOptimizerRMSprop\Optimizer_RMSprop;

$dataAnalyzed = "mandelbrot_spiral_data";
list($X, $y) = NumpyLight::mandelbrotData(500, 500);; // Call your Mandelbrotized spiral data function
$filename = pathinfo(basename($_SERVER['SCRIPT_NAME']), PATHINFO_FILENAME);

$plotterTemp = new LinePlotter(500, 500);
$plotterTemp->plotPoints($X, $y);
$plotterTemp->save("images/".$filename."_$dataAnalyzed.png");
?>