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
    public $momentum;

    function __construct($learning_rate=1.0,$decay = 0.0,$momentum = 0){
        $this->learning_rate = $learning_rate;
        $this->current_learning_rate = $learning_rate;
        $this->decay = $decay;
        $this->iterations = 0;
        $this->momentum = $momentum;
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

        if ($this->momentum) {

            if (!$layer->weight_momentums) {
                
                $layer->weight_momentums = NumpyLight::zeros_like($layer->weights);
                $layer->bias_momentums = NumpyLight::zeros_like($layer->biases);
            }

            $weight_updates = 
            NumpyLight::add(
                NumpyLight::multiply($layer->weight_momentums,$this->momentum),
                NumpyLight::multiply($layer->dweights,-1*$this->current_learning_rate)
            );

            $layer->weight_momentums = $weight_updates;

            $bias_updates = 
            NumpyLight::add(
                NumpyLight::multiply($layer->bias_momentums,$this->momentum),
                NumpyLight::multiply($layer->dbiases,-1*$this->current_learning_rate)
            );

            $layer->bias_momentums = $bias_updates;

        }else{
        // Compute the updates for weights and biases
            $weight_updates = NumpyLight::multiply($layer->dweights, -1*$this->current_learning_rate);
            $bias_updates = NumpyLight::multiply($layer->dbiases, -1*$this->current_learning_rate);

        // Update the weights and biases
        }


        $layer->weights = NumpyLight::add($layer->weights, $weight_updates);

        $layer->biases = NumpyLight::add($layer->biases, $bias_updates);

    }



}

?>