<?PHP
include_once("UTILITY/MatrixOperations.php");
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


$input = array(
  array(1, 2, 3,2.5),
  array(2.0,5.0, -1.0, 2.0),
  array(-1.5,2.7,3.3,-0.8)
);


// Layer done OOP
$Layer1 = new Layer_Dence(4,5);
$Layer2 = new Layer_Dence(5,2);
$Layer1->foward($input);
$Layer2->foward($Layer1->output);

var_dump($Layer2->output);
//$Layer2->foward($input);
?>