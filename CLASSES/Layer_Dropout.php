<?PHP
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight as np; // simulating numpy from python 
use NameSpaceRandomGenerator\RandomGenerator;

class Layer_Dropout {
    
    public $rate;
    public $inputs;
    public $binary_mask;
    public $output;
    public $dinputs;
    // for deprecation wanring
    public $prev;
    public $next;

    public function __construct($rate) {
        // Store rate, we invert it as for example for dropout
        // of 0.1 we need a success rate of 0.9
        $this->rate = 1 - $rate;
    }

    public function forward($inputs,$training = true) {
        // Save input values
        $this->inputs = $inputs;
        
        // If not in the training mode, return values
        if (!$training) {
            $this->output = $inputs; // Assuming $inputs is an array, no need to copy
            return;
        }

        // Generate and save scaled mask
        $inputsShape = np::shape($inputs);
        $this->binary_mask = np::divide(np::random()->binomial(1, $this->rate, $inputsShape), $this->rate);
        
        // Apply mask to output values
        $this->output = np::multiply($inputs, $this->binary_mask);
    }

    public function backward($dvalues) {
        // Gradient on values
        $this->dinputs = np::multiply($dvalues, $this->binary_mask);
    }
}

?>
