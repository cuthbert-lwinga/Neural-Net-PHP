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
    
public function forward($inputs  = NULL){

    $expValues = np::exp(np::deductMaxValueByRow($inputs));

    $probabilities = np::normalizeRows($expValues);

    $this->output = $probabilities;
}

    // public function backward($dinput){
    //     $this->dinputss = np::empty_like($dinput);
    //     for ($i=0; $i < count($this->output); $i++) { 
    //         $jb = (np::JacobianMatrix($this->output[$i])); 
    //         $temp = np::flattenArray(np::dot($jb,np::reshape($dinput[$i],[1, count($dinput[$i])])));
    //         $this->dinputs[$i] = $temp;
    //     }
    // }
    public function backward($dinput){
        np::printMatrix($dinput,5);
        die();
        $this->dinputs = np::empty_like($dinput);
        for ($i=0; $i < count($this->output); $i++) { 
            $jb = (np::JacobianMatrix($this->output[$i])); 
            $temp = np::flattenArray(np::dot($jb,np::reshape($dinput[$i],[1, count($dinput[$i])])));
            $this->dinputs[$i] = $temp;
        }
    }

}


?>