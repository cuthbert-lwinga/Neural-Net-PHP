<?PHP
include_once("../CLASSES/headers.php");

use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 
class Optimizer_SGD
{
    public $learning_rate;
    public $current_learning_rate;
    public $decay;
    public $iterations;
    public $momentum;
    public function __construct($learning_rate = 1.0,$decay=0,$momentum = 0)
    {
        $this->learning_rate = $learning_rate;
        $this->current_learning_rate = $learning_rate;
        $this->decay = $decay;
        $this->iterations = 0;
        $this->momentum = $momentum;
    }

    public function post_update_params() {
        $this->iterations += 1;
    }


    public function pre_update_params() {
        if ($this->decay!=0) {
            $this->current_learning_rate = $this->learning_rate * (1.0 / (1.0 + $this->decay * $this->iterations));
        }
    }


    // public function update_params(&$layer){
    //     $temp_dweights = np::m_operator($layer->dweights,"x",(-1*$this->current_learning_rate));
    //     $layer->weights = np::m_operator($layer->weights,"+",$temp_dweights);
    //     $temp_dbiases = np::m_operator($layer->dbiases,"x",(-1*$this->current_learning_rate));
    //     $layer->biases = np::m_operator($layer->biases,"+",$temp_dbiases);
    // }


    public function update_params(&$layer){


        if ($this->momentum!=0){
            if (!isset($layer->weight_momentums) && !isset($layer->bias_momentums)) {
                $layer->weight_momentums = np::zeros_like($layer->weights);
                $layer->bias_momentums = np::zeros_like($layer->biases);
            }

            $temp_1 = $this->scalarMultiply($layer->weight_momentums,$this->momentum);
            $temp_2 = $this->scalarMultiply($layer->dweights,($this->current_learning_rate));
            $weight_updates = np::m_operator($temp_1,"-",$temp_2);
            $layer->weight_momentums = $weight_updates;

            $temp_1 = $this->scalarMultiply($layer->bias_momentums,$this->momentum);
            $temp_2 = $this->scalarMultiply($layer->dbiases,($this->current_learning_rate));
            $bias_updates = np::m_operator($temp_1,"-",$temp_2);
            $layer->bias_momentums = $bias_updates;


        }else{
            $weight_updates = $this->scalarMultiply($layer->dweights,-1*$this->current_learning_rate);
            $bias_updates = $this->scalarMultiply($layer->dbiases,-1*$this->current_learning_rate);
        }

            $layer->weights = np::m_operator($layer->weights,"+",$weight_updates);//$weight_updates;
            $layer->biases = np::m_operator($layer->biases,"+",$bias_updates);//$bias_updates;
        }

        public function scalarMultiply($matrix, $scalar) {
    $rows = count($matrix);
    $cols = count($matrix[0]);

    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            $matrix[$i][$j] *= $scalar;
        }
    }

    return $matrix;
}


    }




?>