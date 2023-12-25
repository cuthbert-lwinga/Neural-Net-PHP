<?PHP
use NameSpaceArrayFileHandler\ArrayFileHandler;

include_once("ProcessManager.php");
include_once("NumpyLight.php");
include_once("RandomGenerator.php");
include_once("Activation_Relu.php");
include_once("Activation_Softmax.php");
include_once("Layer_Dense.php");
include_once("Optimizer_Adam.php");
include_once("Loss_CategoricalCrossentropy.php");
include_once("Activation_Softmax_Loss_CategoricalCrossentropy.php");
include_once("Optimizer_SGD.php");
include_once("Optimizer_Adagrad.php");
include_once("Optimizer_RMSprop.php");
include_once("LinePlotter.php");
include_once("Layer_Dropout.php");
include_once("Loss_BinaryCrossentropy.php");
include_once("Activation_Sigmoid.php");
include_once("Activation_Linear.php");
include_once("PlotChart.php");
include_once("Accuracy.php");
include_once("Layer_Input.php");
include_once("Model.php");
include_once("ArrayFileHandler.php");
include_once("Queue.php");
include_once("TaskRegistry.php");

ArrayFileHandler::initialize();
?>