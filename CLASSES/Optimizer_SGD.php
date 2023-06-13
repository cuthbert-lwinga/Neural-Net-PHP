<?PHP
include_once("../CLASSES/headers.php");

use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 
class Optimizer_SGD
{
    private $learning_rate;

    public function __construct($learning_rate = 1.0)
    {
        $this->learning_rate = $learning_rate;
    }

    public function update_params(&$layer){
        $temp_dweights = np::m_operator($layer->dweights,"x",(-1*$this->learning_rate));
        $layer->weights = np::m_operator($layer->dweights,"+",$temp_dweights);
        $layer->biases = (addOrSubtractValueToArray($layer->dbiases,(-1*$this->learning_rate),"add"));
        }
}


function addOrSubtractValueToArray($array, $value, $operation)
{
    $result = [];

    if (is_array($array[0])) {
        foreach ($array as $subArray) {
            $result[] = addOrSubtractValueToArray($subArray, $value, $operation);
        }
    } else {
        foreach ($array as $element) {
            if ($operation === 'add') {
                $result[] = $element + $value;
            } elseif ($operation === 'subtract') {
                $result[] = $element - $value;
            }
        }
    }

    return $result;
}


?>