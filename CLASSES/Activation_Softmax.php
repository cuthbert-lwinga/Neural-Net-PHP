<?PHP
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight as np; // simulating numpy from python 
use NameSpaceRandomGenerator\RandomGenerator;

class Activation_Softmax {
    public $output;
    public $inputs;
    public $dinputs;
    
    function __construct($inputs=array()){
    	$this->inputs = $inputs;
    }
    
public function forward($inputs  = NULL){
    $temp =   np::subtract($inputs,np::max($inputs,1,True)); // deduct by row
    $exp_values = np::exp($temp);
    $this->output = np::divide($exp_values,np::sum($exp_values,1,True));
}

public function backward($dvalues){
    $this->dinputs = np::empty_like($dvalues);
    foreach ($this->output as $index => $single_output) {
        $single_dvalues = $dvalues[$index];
        $this->dinputs[$index] = np::jacobian_matrix($single_output, $single_dvalues);
    }
}


}





?>