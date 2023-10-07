<?php
namespace NameSpaceOptimizerRMSprop;
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight;

class Optimizer_RMSprop {
    public $learning_rate;
    public $current_learning_rate;
    public $decay;
    public $iterations;
    public $epsilon;
    public $rho;

    function __construct($learning_rate=0.001, $decay=0.0, $epsilon=1e-7, $rho=0.9) {
        $this->learning_rate = $learning_rate;
        $this->current_learning_rate = $learning_rate;
        $this->decay = $decay;
        $this->iterations = 0;
        $this->epsilon = $epsilon;
        $this->rho = $rho;
    }

    public function pre_update_params() {
        if ($this->decay) {
            $this->current_learning_rate = $this->learning_rate / (1.0 + $this->decay * $this->iterations);
        }
    }

    public function update_params(&$layer) {
        if (!isset($layer->weight_cache)) {
            $layer->weight_cache = NumpyLight::zeros_like($layer->weights);
            $layer->bias_cache = NumpyLight::zeros_like($layer->biases);
        }

        // Update cache with squared current gradients
        $layer->weight_cache = NumpyLight::add(
            NumpyLight::multiply($layer->weight_cache,$this->rho),
            NumpyLight::multiply(NumpyLight::pow($layer->dweights, 2),(1 - $this->rho))
        );

        $layer->bias_cache = NumpyLight::add(
            NumpyLight::multiply($layer->bias_cache,$this->rho),
            NumpyLight::multiply(NumpyLight::pow($layer->dbiases, 2),(1 - $this->rho))
        );

        // Vanilla SGD parameter update + normalization with square rooted cache
        $weight_updates = NumpyLight::divide(
            NumpyLight::multiply($layer->dweights, -1 * $this->current_learning_rate),
            NumpyLight::add(NumpyLight::sqrt($layer->weight_cache), $this->epsilon)
        );
        $layer->weights = NumpyLight::add($layer->weights, $weight_updates);

        $bias_updates = NumpyLight::divide(
            NumpyLight::multiply($layer->dbiases, -1 * $this->current_learning_rate),
            NumpyLight::add(NumpyLight::sqrt($layer->bias_cache), $this->epsilon)
        );
        $layer->biases = NumpyLight::add($layer->biases, $bias_updates);
    }

    public function post_update_params() {
        $this->iterations++;
    }
}

?>
