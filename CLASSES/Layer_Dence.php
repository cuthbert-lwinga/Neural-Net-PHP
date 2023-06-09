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
	function __construct($n_inputs,$n_neurons){
		$this->weights = np::rand($n_inputs,$n_neurons,-0.1,0.1);
		$zerosMatrix = np::zeros($n_neurons,1); //. createing a spae of (1,n_neurons)
		$this->biases  = $zerosMatrix;//np::transform($zerosMatrix);
	}

	public function forward($input){
		$this->inputs = $input;
		$dot = np::dot($input,$this->weights);
		$this->output = np::m_operator($dot, '+', $this->biases);
	}

	public function backward($dvalues) {
	  // Gradients on parameters
	  $this->dweights = np::dot(np::transform($this->inputs), $dvalues);
	  $this->dbiases = np::sum($dvalues,0);
	  $this->dinputs = np::dot($dvalues, np::transform($this->weights));
	}

}


?>