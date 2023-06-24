<?PHP
include_once("../CLASSES/headers.php");
use MathOperations as np;


class Layer_Dense{
	public $inputs;
	public $weights;
	public $biases;
	public $output;
	public $dweights;
	public $dbiases;
	public $doutput;
	public $weight_momentums = NULL;
	public $bias_momentums = NULL;
	public $weight_cache = NULL;
	public $bias_cache = NULL;
	function __construct($n_inputs,$n_neurons){
		$this->weights = np::m_operator(np::rand($n_inputs,$n_neurons),"x",0.01);
		$zerosMatrix = np::zeros(1,$n_neurons);
		$this->biases  = $zerosMatrix;
	}

	public function forward($input){
		$this->inputs = $input;
		$dot = np::dot($input,$this->weights);
		$this->output = np::m_operator($dot, '+', $this->biases);
	}

	public function backward($dvalues) {
	  // Gradients on parameters

		$this->dweights = np::dot(np::transform($this->inputs), $dvalues);
		$this->dbiases = array(np::sum($dvalues,0));
		$transformedWeights = np::transform($this->weights); 
		$this->dinputs = np::dot($dvalues,$transformedWeights);
		
	}

}
function calculate_sum_shape($array, $axis) {
	$sum = array_sum($array, $axis);
	$shape = array_map('count', $sum);
	return implode(',', $shape);
}

?>