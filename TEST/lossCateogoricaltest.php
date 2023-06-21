<?PHP
include_once("../CLASSES/headers.php");

use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 
function softMaxtest(){

    
    $softmax = new Loss_CategoricalCrossentropy();
    $temp = $softmax->forward([[1,2,3]]);
    
    echo "\nOutput \n";
    np::printMatrix($softmax->output);
    echo "\nExpected \n";
    np::printMatrix([[0.09003057,0.24472847,0.66524096]]);
}
softMaxtest();
?>