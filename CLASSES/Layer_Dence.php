<?PHP
include_once("../CLASSES/headers.php");
use MathOperations as np;


class Layer_Dence{
	public $weights;
	public $biases;
	public $output;
	function __construct($n_inputs,$n_neurons){
		$this->weights = np::rand($n_inputs,$n_neurons,-0.1,0.1);
		$zerosMatrix = np::zeros($n_neurons,1); //. createing a spae of (1,n_neurons)
		$this->biases  = $zerosMatrix;//np::transform($zerosMatrix);
	}

	public function foward($input){
		$dot = np::dot($input,$this->weights);
		$this->output = np::m_operator($dot, '+', $this->biases);
	}

}


?>