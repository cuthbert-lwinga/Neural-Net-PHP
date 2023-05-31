<?PHP
include_once("../CLASSES/headers.php");
use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 


class Activation_Softmax {
    public $output;
    public $inputs;
function __construct($inputs){
	$this->inputs = $inputs;
}

    public function forward() {
    	$inputs = $this->inputs;
        $maxInputs = array_map('max', $inputs);
        $maxInputs = array_map(function ($value) {
            return [$value];
        }, $maxInputs);
        
        $expValues = array_map(function ($input, $maxInput) {
            $exp = array_map(function ($value) use ($maxInput) {
                return exp($value - $maxInput[0]);
            }, $input);
            return $exp;
        }, $inputs, $maxInputs);
        
        $sumExpValues = array_map(function ($exp) {
            return array_sum($exp);
        }, $expValues);
        
        $probabilities = array_map(function ($exp, $sumExp) {
            return array_map(function ($value) use ($sumExp) {
                return $value / $sumExp;
            }, $exp);
        }, $expValues, $sumExpValues);
        
        $this->output = $probabilities;
    }
}


?>