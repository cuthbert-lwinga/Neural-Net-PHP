<?PHP
include_once("../CLASSES/headers.php");

use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 

class Optimizer_Adagrad
{
    public $learning_rate;
    public $current_learning_rate;
    public $decay;
    public $iterations;
    public $epsilon;
    public function __construct($learning_rate = 1.0,$decay=0,$epsilon = 1e-7)
    {
        $this->learning_rate = $learning_rate;
        $this->current_learning_rate = $learning_rate;
        $this->decay = $decay;
        $this->iterations = 0;
        $this->epsilon = $epsilon;
    }

    public function post_update_params() {
        $this->iterations += 1;
    }


    public function pre_update_params() {
        if ($this->decay!=0) {
            $this->current_learning_rate = $this->learning_rate * (1.0 / (1.0 + $this->decay * $this->iterations));
        }
    }


    public function update_params(&$layer){

        if (!isset($layer->weight_cache) && !isset($layer->bias_cache)) {
            $layer->weight_cache = np::zeros_like($layer->weights);
            $layer->bias_cache = np::zeros_like($layer->biases);
        }

        $layer->weight_cache = np::m_operator($layer->weight_cache,"+",np::sqr($layer->dweights));
        $layer->bias_cache = np::m_operator($layer->bias_cache,"+",np::sqr($layer->dbiases));
        
        $temp_w = np::m_operator($this->scalarMultiply($layer->dweights,-1*$this->current_learning_rate),"/",np::m_operator(np::sqrt($layer->weight_cache),"+",$this->epsilon));
    
        $temp_b = np::m_operator($this->scalarMultiply($layer->dbiases,-1*$this->current_learning_rate),"/",np::m_operator(np::sqrt($layer->bias_cache),"+",$this->epsilon));

        $layer->weights = np::m_operator($layer->weights,"+",$temp_w);//$weight_updates;
        $layer->biases = np::m_operator($layer->biases,"+",$temp_b);//$bias_updates;
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