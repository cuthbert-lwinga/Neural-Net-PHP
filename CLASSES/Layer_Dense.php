<?PHP
include_once("Headers.php");

use NameSpaceNumpyLight\NumpyLight as np; // simulating numpy from python 
use NameSpaceRandomGenerator\RandomGenerator;

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
        $this->weights = np::multiply(np::random()->rand($n_inputs,$n_neurons),0.010);
        $zerosMatrix = np::zeros(1,$n_neurons);
        $this->biases  = $zerosMatrix;
    }

    public function forward($input){
        $this->inputs = $input;
        $dot = np::dot($input,$this->weights);
        $this->output = np::add($dot,$this->biases);
    }

    public function backward($dvalues) {
        $this->dweights = np::dot(np::transpose($this->inputs), $dvalues);
        $this->dbiases = np::sum($dvalues,0,true);
        $transformedWeights = np::transpose($this->weights); 

       // echo json_encode(np::shape($dvalues))." = ".json_encode(np::shape($dvalues))." \n";
        $this->dinputs = np::dot($dvalues,$transformedWeights);
        
    }

}

?>