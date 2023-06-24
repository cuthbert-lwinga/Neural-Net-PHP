<?PHP
include_once("../CLASSES/headers.php");
use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 


class Activation_Softmax {
    public $output;
    public $inputs;
    public $dinputs;
    
    function __construct($inputs=array()){
    	$this->inputs = $inputs;
    }
    
public function fwd($inputs = null) {
    $maxInputs = max($inputs[0]);

    $expValues = array_map(function ($input) use ($maxInputs) {
        $exp = array_map(function ($value) use ($maxInputs) {
            return exp($value - $maxInputs);
        }, $input);
        return $exp;
    }, $inputs);

    $sumExpValues = array_map(function ($exp) {
        return array_sum($exp);
    }, $expValues);

    $probabilities = array_map(function ($exp, $sumExp) {
        $softmax = array_map(function ($value) use ($sumExp) {
            return $value / $sumExp;
        }, $exp);
        return $softmax;
    }, $expValues, $sumExpValues);

    $this->output = $probabilities;
}

public function forward($inputs  = NULL){

    $expValues = np::exp(np::deductMaxValueByRow($inputs));

    $probabilities = np::normalizeRows($expValues);

    $this->output = $probabilities;
}

    public function backward($dinput){
        $this->dinputs = np::empty_like($dinput);
        for ($i=0; $i < count($this->output); $i++) { 
            $jb = (np::JacobianMatrix($this->output[$i])); 
            $temp = np::flattenArray(np::dot($jb,np::reshape($dinput[$i],[1, count($dinput[$i])])));
            $this->dinputs[$i] = $temp;
        }
    }


}


?>