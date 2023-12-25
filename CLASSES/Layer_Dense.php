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
    public $weight_regularizer_l1;
    public $weight_regularizer_l2;
    public $bias_regularizer_l1;
    public $bias_regularizer_l2;

    public function __construct($n_inputs, $n_neurons, 
                                $weight_regularizer_l1 = 0, $weight_regularizer_l2 = 0,
                                $bias_regularizer_l1 = 0, $bias_regularizer_l2 = 0) {
        $this->weights = np::multiply(np::random()->rand($n_inputs, $n_neurons), 0.1);
        $this->biases = np::zeros(1, $n_neurons);

        $this->weight_regularizer_l1 = $weight_regularizer_l1;
        $this->weight_regularizer_l2 = $weight_regularizer_l2;
        $this->bias_regularizer_l1 = $bias_regularizer_l1;
        $this->bias_regularizer_l2 = $bias_regularizer_l2;
    }

    public function forward($inputs){
        $this->inputs = $inputs;
        $dot = np::dot($inputs,$this->weights);
        $this->output = np::add($dot,$this->biases);
// self.inputs = inputs
//         self.output = np.dot(inputs, self.weights) + self.biases
        
    }

    // public function backward($dvalues) {
    //     $this->dweights = np::dot(np::transpose($this->inputs), $dvalues);
    //     $this->dbiases = np::sum($dvalues,0,true); 
    //     $this->dinputs = np::dot($dvalues,np::transpose($this->weights));
    // }

    public function backward($dvalues) {
    // Gradients on parameters
    $this->dweights = np::dot(np::transpose($this->inputs), $dvalues);
    $this->dbiases = np::sum($dvalues, 0, true);

    // Gradients on regularization
    // L1 on weights
    if ($this->weight_regularizer_l1 > 0) {
        $dL1 = np::ones_like($this->weights,1);
        $dL1 = np::apply_relu_backwards($this->weights,$dL1,0,-1,$strict = false);
        $this->dweights = np::add($this->dweights, np::multiply($dL1,$this->weight_regularizer_l1));
    }

    // L2 on weights
    if ($this->weight_regularizer_l2 > 0) {
        $this->dweights = np::add($this->dweights, np::multiply($this->weights,(2 * $this->weight_regularizer_l2)));
    }

    // L1 on biases
    if ($this->bias_regularizer_l1 > 0) {
        $dL1 = np::ones_like($this->biases);
        $dL1 = np::apply_relu_backwards($this->biases,$dL1,0,-1,$strict = false);
        $this->dbiases = np::add($this->dbiases, np::multiply($dL1,$this->bias_regularizer_l1));
    }

    // L2 on biases
    if ($this->bias_regularizer_l2 > 0) {
        $this->dbiases = np::add($this->dbiases, np::multiply($this->biases,(2 * $this->bias_regularizer_l2)));
    }

    // Gradient on values
    $this->dinputs = np::dot($dvalues, np::transpose($this->weights));
}



}

?>