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
    public $dinputs;
    public $dbiases;
    public $doutput;
    public $weight_momentums = NULL;
    public $bias_momentums = NULL;
    public $weight_cache = NULL;
    public $bias_cache = NULL;
    function __construct($n_inputs,$n_neurons){
        $this->weights = np::multiply(np::random()->rand($n_inputs,$n_neurons), 1e-4); // book is 0.01, but this performs way better. I suspsect it's somthing to do with my random 
        $zerosMatrix = np::zeros(1,$n_neurons);
        $this->biases  = $zerosMatrix;
    }

    public function forward($input){
        $this->inputs = $input;
        // issue is here weight and inputs are massive
        
        $dot = np::dot($input,$this->weights);


        $this->output = np::add($dot,$this->biases);
    }

    public function backward($dvalues) {
    //         np::displayMatrix($dvalues,1);
    // echo "\n\n\n\n";
        $this->dweights = np::dot(np::transpose($this->inputs), $dvalues);
        $this->dbiases = np::sum($dvalues,0,true);
        $transformedWeights = np::transpose($this->weights); 

       // echo json_encode(np::shape($dvalues))." = ".json_encode(np::shape($dvalues))." \n";
        $this->dinputs = np::dot($dvalues,$transformedWeights);
        
    }


}

?>