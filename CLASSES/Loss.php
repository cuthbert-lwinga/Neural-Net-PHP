<?PHP
include_once("../CLASSES/headers.php");
use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 

abstract class Loss {
	public $dinputs;
    public abstract function calculate($output, $y);
    protected abstract function forward($y_pred, $y_true);
}


?>