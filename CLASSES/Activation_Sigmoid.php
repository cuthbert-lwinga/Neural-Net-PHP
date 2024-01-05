<?PHP
// namespace NameSpaceActivationSigmoid;

include_once("Headers.php");

use NameSpaceNumpyLight\NumpyLight as np; // simulating numpy from python 
use NameSpaceRandomGenerator\RandomGenerator;

class Activation_Sigmoid {
    
    public $inputs;
    public $output;
    public $dinputs;
    // for deprecation wanring
    public $prev;
    public $next;
    public function forward($inputs) {
        // Save input and calculate/save output of the sigmoid function
        $this->inputs = $inputs;
        $this->output = np::divide(1,
                            np::add(1,
                                np::exp(
                                    np::multiply($inputs,-1)
                                )
                            )
                        );


    }

    public function backward($dvalues) {
        // Derivative - calculates from the output of the sigmoid function
        //                 var_dump($dvalues);
        // die();
        $this->dinputs = np::multiply(
                            $dvalues, 
                            np::multiply(
                                    np::subtract(
                                        1,
                                        $this->output
                                    ),
                                    $this->output
                                )
                        );

    }


public function predictions($outputs) {
    $result = array();

    // Check if the first element is also an array (indicating a matrix)
    if (is_array($outputs[0])) {
        foreach ($outputs as $row) {
            $resultRow = array();
            foreach ($row as $output) {
                $resultRow[] = ($output > 0.5) ? 1 : 0;
            }
            $result[] = $resultRow;
        }
    } else {
        // Handle as a single array
        foreach ($outputs as $output) {
            $result[] = ($output > 0.5) ? 1 : 0;
        }
    }

    return $result;
}




}

?>
