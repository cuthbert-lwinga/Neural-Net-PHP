<?PHP
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;

class Optimizer_Adam {
    public $learning_rate;
    public $current_learning_rate;
    public $decay;
    public $iterations;
    public $epsilon;
    public $beta_1;
    public $beta_2;

    public function __construct($learning_rate = 0.001, $decay = 0.0, $epsilon = 1e-7, $beta_1 = 0.9, $beta_2 = 0.999) {
        $this->learning_rate = $learning_rate;
        $this->current_learning_rate = $learning_rate;
        $this->decay = $decay;
        $this->iterations = 0;
        $this->epsilon = $epsilon;
        $this->beta_1 = $beta_1;
        $this->beta_2 = $beta_2;
    }

    public function pre_update_params() {
        if ($this->decay) {
            $this->current_learning_rate = $this->learning_rate * (1. / (1. + $this->decay * $this->iterations));
        }
    }

// public function update_params(&$layer) {
//     if (!$layer->weight_cache) {
//         $layer->initialize_caches();
//     }

//     // Update weight momentums
//     $layer->weight_momentums = NumpyLight::update_weight_momentums($layer, $this->beta_1);
//     if (NumpyLight::hasNAN($layer->weight_momentums)) {
//         echo "\nNaN found in weight momentums\n";
//         die();
//     }

//     // Update bias momentums
//     $layer->bias_momentums = NumpyLight::update_bias_momentums($layer, $this->beta_1);
//     if (NumpyLight::hasNAN($layer->bias_momentums)) {
//         echo "\nNaN found in bias momentums\n";
//         die();
//     }

//     // Calculate corrected momentums
//     $weight_momentums_corrected = NumpyLight::correct_momentums($layer->weight_momentums, $this->beta_1, $this->iterations);
//     $bias_momentums_corrected = NumpyLight::correct_momentums($layer->bias_momentums, $this->beta_1, $this->iterations);
//     if (NumpyLight::hasNAN($weight_momentums_corrected) || NumpyLight::hasNAN($bias_momentums_corrected)) {
//         echo "\nNaN found in corrected momentums\n";
//         die();
//     }

//     // Update weight and bias caches
//     $layer->update_caches($this->beta_2);
//     if (NumpyLight::hasNAN($layer->weight_cache) || NumpyLight::hasNAN($layer->bias_cache)) {
//         echo "\nNaN found in weight/bias caches\n";
//         die();
//     }

//     // Calculate corrected caches
//     $weight_cache_corrected = NumpyLight::correct_caches($layer->weight_cache, $this->beta_2, $this->iterations);
//     $bias_cache_corrected = NumpyLight::correct_caches($layer->bias_cache, $this->beta_2, $this->iterations);
//     if (NumpyLight::hasNAN($weight_cache_corrected) || NumpyLight::hasNAN($bias_cache_corrected)) {
//         echo "\nNaN found in corrected caches\n";
//         die();
//     }

//     // Update weights and biases
//     $layer->update_weights_and_biases($weight_momentums_corrected, $weight_cache_corrected, $bias_momentums_corrected, $bias_cache_corrected, $this->current_learning_rate, $this->epsilon);
//     if (NumpyLight::hasNAN($layer->weights) || NumpyLight::hasNAN($layer->biases)) {
//         echo "\nNaN found in updated weights/biases\n";
//         die();
//     }
// }


public function update_params(&$layer) {
    // print_r("Entering update_params\n");
    if (!$layer->weight_cache) {
        // print_r("Initializing weight and bias caches\n");
        $layer->weight_momentums = NumpyLight::zeros_like($layer->weights);
        $layer->weight_cache = NumpyLight::zeros_like($layer->weights);
        $layer->bias_momentums = NumpyLight::zeros_like($layer->biases);
        $layer->bias_cache = NumpyLight::zeros_like($layer->biases);
    }
    
    // print_r("Calculating weight momentums\n");
    $layer->weight_momentums = NumpyLight::add(
        NumpyLight::multiply($layer->weight_momentums, $this->beta_1),
        NumpyLight::multiply($layer->dweights, 1 - $this->beta_1)
    );


    $layer->bias_momentums = NumpyLight::add(
        NumpyLight::multiply($layer->bias_momentums, $this->beta_1),
        NumpyLight::multiply($layer->dbiases, 1 - $this->beta_1)
    );

   
    $weight_momentums_corrected = NumpyLight::divide(
        $layer->weight_momentums,
        (1 - pow($this->beta_1, $this->iterations + 1))
    );
    // print_r($weight_momentums_corrected);
    
    // print_r("Calculating corrected bias momentums\n");
    $bias_momentums_corrected = NumpyLight::divide(
        $layer->bias_momentums,
        (1 - pow($this->beta_1, $this->iterations + 1))
    );
    // print_r($bias_momentums_corrected);
    
    // print_r("Calculating weight cache\n");
    $layer->weight_cache = NumpyLight::add(
        NumpyLight::multiply($layer->weight_cache, $this->beta_2),
        NumpyLight::multiply(NumpyLight::pow($layer->dweights, 2), (1 - $this->beta_2))
    );
    // print_r($layer->weight_cache);
    
    // print_r("Calculating bias cache\n");
    $layer->bias_cache = NumpyLight::add(
        NumpyLight::multiply($layer->bias_cache, $this->beta_2),
        NumpyLight::multiply(NumpyLight::pow($layer->dbiases, 2), 1 - $this->beta_2)
    );
    // print_r($layer->bias_cache);
    
    // print_r("Calculating corrected weight cache\n");
    $weight_cache_corrected = NumpyLight::divide(
        $layer->weight_cache,
        (1 - pow($this->beta_2, $this->iterations + 1))
    );
    // print_r($weight_cache_corrected);
    
    // print_r("Calculating corrected bias cache\n");
    $bias_cache_corrected = NumpyLight::divide(
        $layer->bias_cache,
        (1 - pow($this->beta_2, $this->iterations + 1))
    );
    // print_r($bias_cache_corrected);
    
    // print_r("Updating weights\n");
    $layer->weights = NumpyLight::add(
        $layer->weights,
        NumpyLight::divide(
            NumpyLight::multiply($weight_momentums_corrected, -1 * $this->current_learning_rate),
            NumpyLight::add(NumpyLight::sqrt($weight_cache_corrected), $this->epsilon)
        )
    );
    // print_r($layer->weights);
    
    // print_r("Updating biases\n");
    $layer->biases = NumpyLight::add(
        $layer->biases,
        NumpyLight::divide(
            NumpyLight::multiply($bias_momentums_corrected, -1 * $this->current_learning_rate),
            NumpyLight::add(NumpyLight::sqrt($bias_cache_corrected), $this->epsilon)
        )
    );
    // print_r($layer->biases);
}


    public function post_update_params() {
        $this->iterations++;
    }
}

?>