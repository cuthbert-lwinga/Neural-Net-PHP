<?PHP
include_once("../CLASSES/headers.php");
use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 


class Activation_Relu{
	public $inputs;
	public $output;
    public $input;
    public $dinput;
	function __construct($input){
		$this->inputs = $input;
	}

    public function forward() {
        $output = [];
        foreach ($this->inputs as $input) {
            $output[] = $this->reluActivation($input);
        }
        $this->output = $output;
    }

    private function reluActivation($input) {
        $output = [];
        foreach ($input as $value) {
            $maxFound = max(0,$value);;
            $output[] = $maxFound;
        }

        return $output;
    }

    public function backward($dvalues) {
        $this->dinput = $dvalues;
        $this->dinput = np::applyThreshold($this->dinput,0); #Equivalent to self.dinput[self.dinput<=0] = 0
    }


}




?>