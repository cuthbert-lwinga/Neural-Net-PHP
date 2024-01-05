<?PHP
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight as np; // simulating numpy from python 
use NameSpaceRandomGenerator\RandomGenerator;
class Activation_Linear {

    public $inputs;
    public $output;
    public $dinputs;
    // for deprecation wanring
    public $prev;
    public $next;
    // Forward pass
    public function forward($inputs) {
        // Just remember values
        $this->inputs = $inputs;
        $this->output = $inputs;
    }

    // Backward pass
    public function backward($dvalues) {
        // Derivative is 1, 1 * dvalues = dvalues - the chain rule
        $this->dinputs = $dvalues;
    }


    public function predictions($output){
        return $output;
    }

}

?>