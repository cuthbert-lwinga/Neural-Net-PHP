<?php
use PHPUnit\Framework\TestCase;
use NameSpaceNumpyLight\NumpyLight;
//ini_set('memory_limit', '256M');
ini_set("precision", "16");

// must have bcmath for extra precision


class Neural_Network_Pages_Test extends TestCase
{
public function testP65(){
	echo "\n TESTING....[Page 65 of  Chapter 9 - Backpropagation - Neural Networks from Scratch in Python ] (Tolerance: 0.0001)  \n";

$matrix = [
    [1, 2, 3, 4, 5, 6],
    [7, 8, 9, 10, 11, 12],
    [13, 14, 15, 16, 17, 18]
];

$softmax_outputs = [[0.7, 0.1, 0.2], [0.1, 0.5, 0.4], [0.02, 0.9, 0.08]];
$class_targets = [0, 1, 1];

$softmax_loss = new Activation_Softmax_Loss_CategoricalCrossentropy();
$softmax_loss->backward($softmax_outputs, $class_targets);
$dvalues1 = $softmax_loss->dinputs;
$activation = new Activation_Softmax();
$activation->output = $softmax_outputs;
$loss = new Loss_CategoricalCrossentropy();
$loss->backward($softmax_outputs, $class_targets);
$activation->backward($loss->dinputs);
$dvalues2 = $activation->dinputs;



$dvalues1_expected_output = [
    [-0.1, 0.03333333, 0.06666667],
    [0.03333333, -0.16666667, 0.13333333],
    [0.00666667, -0.03333333, 0.02666667]
];

$dvalues2_expected_output = [
    [-0.09999999, 0.03333334, 0.06666667],
    [0.03333334, -0.16666667, 0.13333334],
    [0.00666667, -0.03333333, 0.02666667]
];

$this->assertEqualsWithDelta($dvalues1_expected_output, $dvalues1, 0.0001, "dvalues1 didn't match");

$this->assertEqualsWithDelta($dvalues2_expected_output, $dvalues2, 0.0001, "dvalues2 didn't match");
}

}
