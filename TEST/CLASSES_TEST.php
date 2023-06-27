<?PHP
include_once("../CLASSES/headers.php");

use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 

// Testing the classes and functions
function test_Layer_Dense() {
    $layer = new Layer_Dense(2, 3);
    echo "\n[TESTING] test_Layer_Dense()\n";
    Test::compare(np::shape($layer->weights),"(2, 3)");
}



function test_Activation_ReLU() {
    $activation = new Activation_ReLU();
    $inputs = [[1, -2, 3]];
    $activation->forward($inputs);
    echo "\n[TESTING] test_Activation_ReLU()\n";
    Test::compare(($activation->output),[[1, 0, 3]]);
}

function test_Activation_Softmax() {
    $activation = new Activation_Softmax();
    $inputs = [[1, 2, 3]];
    $activation->forward($inputs);
    assert(abs(array_sum($activation->output[0]) - 1.0) < 1e-7);
	echo "\n[TESTING] test_Activation_Softmax()\n";
    echo "OUTPUT: \n";
    np::printMatrix($activation->output);
    echo "\n";
    Test::compare(abs(array_sum($activation->output[0]) - 1.0),1e-7,"<");
}

function test_Loss_CategoricalCrossentropy() {
    $loss = new Loss_CategoricalCrossentropy();
    $y_pred = [[0.1, 0.2, 0.7], [0.3, 0.5, 0.2]];
    $y_true = [2, 1];
    $loss_value = $loss->calculate($y_pred, $y_true);
    echo "\n[TESTING] test_Loss_CategoricalCrossentropy()\n";
    echo "LOSS: $loss_value\n";
    //assert(abs($loss_value - 0.571599) < 1e-7);
    Test::compare(abs($loss_value - 0.571599),1e-7,"<");
}

function test_Activation_Softmax_Loss_CategoricalCrossentropy() {
    $activation_loss = new Activation_Softmax_Loss_CategoricalCrossentropy();
    $inputs = [[1, 2, 3], [4, 5, 6]];
    $y_true = [2, 1];
    $loss_value = $activation_loss->forward($inputs, $y_true);
    echo "\n[TESTING] Activation_Softmax_Loss_CategoricalCrossentropy()\n";
    echo "LOSS: $loss_value\n";
    //assert(abs($loss_value - 2.407607) < 1e-7);
    Test::compare(abs($loss_value - 2.407607),1e-7,"<");
}

function test_Optimizer_SGD() {
    $layer = new Layer_Dense(2, 3);
     $layer->weights = [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6]];
    $layer->dweights = [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6]];
    $layer->dbiases = [[0.01, 0.02, 0.03]];
    $optimizer = new Optimizer_SGD(0.1);
    $optimizer->update_params($layer);
    //assert($layer->weights === [[0.01, 0.02, 0.03], [0.04, 0.05, 0.06]]);
    //assert($layer->biases === [[-0.001, -0.002, -0.003]]);

    echo "\n[TESTING] test_Optimizer_SGD()\n";
    echo "\n WEIGHTS \n";
    np::printMatrix($layer->weights);
    echo "\n BIASES \n";
    np::printMatrix($layer->biases);

    Test::compare($layer->weights,[[0.01, 0.02, 0.03], [0.04, 0.05, 0.06]]);
    Test::compare($layer->biases,[[-0.001, -0.002, -0.003]]);
}


function test_Layer_Dense_backward() {
    $layer = new Layer_Dense(2, 3);
    $layer->inputs = [[1, 2, 3], [4, 5, 6]];
    $layer->weights = [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6]];
    $layer->biases = [[0.01, 0.02, 0.03]];
    $dvalues = [[1, 2, 3], [4, 5, 6]];

    // $layer->forward(); // Uncomment if forward method needs to be called

    $layer->backward($dvalues);

    // Expected results
    $expected_dweights = np::dot(np::transform($layer->inputs), $dvalues);
    $expected_dbiases = np::sum($dvalues, 0, true);
    $expected_dinputs = np::dot($dvalues, np::transform($layer->weights));

    echo "\n[TESTING] test_Layer_Dense_backward()\n";

    echo "\nExpected dweights:\n";
    np::printMatrix($expected_dweights);
    echo "\nExpected dbiases:\n";
    np::printMatrix($expected_dbiases);
    echo "\nExpected dinputs:\n";
    np::printMatrix($expected_dinputs);
    echo "\n";
}

function test_Loss_CategoricalCrossentropy_backward() {
    $loss = new Loss_CategoricalCrossentropy();
    $y_pred = [[0.2, 0.8, 0.1], [0.6, 0.1, 0.3]];
    $y_true = [1, 0];
    $dvalues = [[0.3, -0.5, 0.2], [0.1, -0.2, 0.4]];

    $loss->backward($dvalues, $y_true);

    // Expected results
    $expected_dinputs = $loss->dinputs;
	echo "\n[TESTING] test_Loss_CategoricalCrossentropy_backward()\n";
    echo "\nExpected dinputs:\n";
    np::printMatrix($expected_dinputs);
    echo "\n";
}

function test_Activation_Softmax_Loss_CategoricalCrossentropy_backward() {
    $activation_loss = new Activation_Softmax_Loss_CategoricalCrossentropy();
    $inputs = [[1, 2, 3], [4, 5, 6]];
    $y_true = [[0, 1, 0], [1, 0, 0]];
    $dvalues = [[-0.3, 0.5, -0.2], [0.1, -0.1, 0.2]];

    $activation_loss->forward($inputs, $y_true);
    $activation_loss->backward($dvalues, $y_true);

    // Expected results
// Expected results
    $expected_dinputs = $activation_loss->dinputs;
	echo "\n[TESTING] test_Activation_Softmax_Loss_CategoricalCrossentropy_backward()\n";
    echo "\nExpected dinputs:\n";
    np::printMatrix($expected_dinputs);
    echo "\n";
}

function flatTest(){
    // Jacobian Test
    $array = [1, 2, 3, 4, 5, 6];
    $out = np::npreshape($array, -1, 1);
    $out2 = np::transform($out);
    $dot = np::dot($out,$out2);

    $duplicated_col_by_n_rows = np::duplicate($array,count($out));
    $jacobian_matrix = np::m_operator($duplicated_col_by_n_rows,"-",$dot);
    echo "\n";
np::printMatrix($jacobian_matrix);
}
flatTest();
// Run the test suite
// test_Layer_Dense();
// test_Activation_ReLU();
// test_Activation_Softmax();
// test_Loss_CategoricalCrossentropy();
// test_Activation_Softmax_Loss_CategoricalCrossentropy();
// test_Optimizer_SGD();
//test_Layer_Dense_backward();
//test_Loss_CategoricalCrossentropy_backward();
//test_Activation_Softmax_Loss_CategoricalCrossentropy_backward();

?>