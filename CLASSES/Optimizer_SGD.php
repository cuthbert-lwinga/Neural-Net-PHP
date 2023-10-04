<?PHP
namespace NameSpaceOptimizerSGD;
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;

class Optimizer_SGD {
    public $learning_rate;
    public $current_learning_rate;
    public $decay;
    public $iterations;

    function __construct($learning_rate=1.0,$decay = 0.0){
        $this->learning_rate = $learning_rate;
        $this->current_learning_rate = $learning_rate;
        $this->decay = $decay;
        $this->iterations = 0;
    }

    public function pre_update_params() {
        if ($this->decay) {
            $this->current_learning_rate = $this->learning_rate * (1.0 / (1.0 + $this->decay * $this->iterations));
        }
    }

    public function post_update_params() {
        $this->iterations++;
    }

    public function update_params(&$layer){
    // Gradient Clipping Threshold

    // Compute the updates for weights and biases
    $negateDweights = NumpyLight::multiply($layer->dweights, -1*$this->current_learning_rate);
    $negateDbiases = NumpyLight::multiply($layer->dbiases, -1*$this->current_learning_rate);

    // Update the weights and biases
    $layer->weights = NumpyLight::add($layer->weights, $negateDweights);
    $layer->biases = NumpyLight::add($layer->biases, $negateDbiases);
}




}

?>