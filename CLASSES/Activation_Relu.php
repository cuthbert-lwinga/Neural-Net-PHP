<?PHP
namespace NameSpaceActivationRelu;

include_once("Headers.php");

use NameSpaceNumpyLight\NumpyLight as np; // simulating numpy from python 
use NameSpaceRandomGenerator\RandomGenerator;


class Activation_Relu{
	public $inputs;
	public $output;
    public $dinputs;
	function __construct($input=array()){
		$this->inputs = $input;
	}

    public function forward($input=array()) {
        $this->inputs = $input;
        $this->output = np::ReLU($this->inputs); // equal to maxium(0,matrix)
    }

    public function backward($dvalues) {
        $this->dinputs = $dvalues;
        $this->dinputs = np::apply_relu_backwards($this->inputs, $this->dinputs);
    }


}




?>