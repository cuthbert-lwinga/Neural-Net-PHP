<?PHP
include_once("Headers.php");

use NameSpaceNumpyLight\NumpyLight; // simulating numpy from python 
use NameSpaceRandomGenerator\RandomGenerator;

class Activation_Softmax_Loss_CategoricalCrossentropy {
    private $activation;
    private $loss;
    public $output;
    public $dinputs;

    public function __construct() {
        $this->activation = new Activation_Softmax();
        $this->loss = new Loss_CategoricalCrossentropy();
    }

    public function forward($inputs, $y_true) {
        $this->activation->forward($inputs);
        $this->output = $this->activation->output;
        return $this->loss->calculate($this->output, $y_true);
    }

    public function backward($dvalues, $y_true) {
        $samples = count($dvalues);

        if (count(NumpyLight::shape($y_true)) == 2) {
            $y_true = NumpyLight::argmax($y_true);
        }

        $this->dinputs = $dvalues;
        
        $this->dinputs = NumpyLight::modifyOneHotEncoded($this->dinputs, $y_true,-1);

        $this->dinputs = NumpyLight::divide($this->dinputs, $samples);
    }
}

?>