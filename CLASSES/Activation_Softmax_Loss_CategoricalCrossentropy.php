<?PHP
include_once("../CLASSES/headers.php");
use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 
class Activation_Softmax_Loss_CategoricalCrossentropy extends Loss{
	public $activation;
	public $loss;
	public $output;
	public $dinputs;
	function __construct(){
		$this->activation = new Activation_Softmax();
		$this->loss = new Loss_CategoricalCrossentropy();
	}

	public function forward($inputs, $y_true) {
		//$this->activation->inputs = $inputs;
		$this->activation->forward($inputs);
		$this->output = $this->activation->output;
		$return = $this->loss->calculate($this->output,$y_true);
		return $return;
	}

function backward($dvalues, $y_true) {
    // Number of samples
    $samples = count($dvalues);
   
    if (np::checkArrayShape($y_true)==2) {
        $y_true = np::argmax($y_true); // axis = 1
    }
	$dinputs = $dvalues;
    // Calculate gradient
    $this->dinputs = np::subtractFromDInputs($dinputs, $y_true,1);//self.dinputs[samples, y_true] -= 1, check MatrixOperation for this function equivalent;
    $this->dinputs = np::m_operator($this->dinputs,"/",$samples);
}

}

?>