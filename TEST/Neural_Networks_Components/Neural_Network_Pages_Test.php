<?php
use PHPUnit\Framework\TestCase;
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;
use NameSpaceOptimizerSGD\Optimizer_SGD;
use NameSpaceOptimizerRMSprop\Optimizer_RMSprop;

//ini_set('memory_limit', '256M');
ini_set("precision", "32");

// must have bcmath for extra precision


class Neural_Network_Pages_Test extends TestCase
{
    private $data;
    private $path;
    private $tolerance = 0.0000001;

 protected function setUp(): void
    {
        // Load the JSON data once for all tests
        $filePath = __DIR__ . '/neural_network_data.json';
        $filePath2 = __DIR__ . '/recorded_data.json';
        $json_data = file_get_contents($filePath2);
        $this->data = json_decode($json_data, true);
        $this->path = $filePath;
    }

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

    $this->assertEqualsWithDelta($dvalues1_expected_output, $dvalues1, $this->tolerance, "dvalues1 didn't match");

    $this->assertEqualsWithDelta($dvalues2_expected_output, $dvalues2, $this->tolerance, "dvalues2 didn't match");
}


public function testLayerDenseOutput() {
        // Load JSON data
        $json_data = file_get_contents($this->path);
        $data = json_decode($json_data, true);

        // Extract spiral_data
        list($X, $y) = $data['spiral_data'];

        $dense1 = new Layer_Dense(2, 64);

        // Set weights and biases for the dense layer from the JSON data
        $dense1->weights = $data['weights'];
        $dense1->biases = $data['biases'];

        $dense1->forward($X);

        // Assert that the output from your Layer_Dense matches the output saved in the JSON file
      // $this->assertEquals($data['output'], $dense1->output, "Output of Layer_Dense does not match expected output.");
      $this->assertEqualsWithDelta($data['output'], $dense1->output, $this->tolerance, "Output of Layer_Dense does not match expected output.");
    }


public function testActivationReLU() {
     echo "\n TESTING....[Activation_ReLU ] \n";
    // Load JSON data
    $json_data = file_get_contents($this->path);
    $data = json_decode($json_data, true);
// var_dump($data['activation1']);
    // Extract spiral_data and setup layers
    list($X, $y) = $data['spiral_data'];
    $dense1 = new Layer_Dense(2, 64);
    $dense1->weights = $data['weights'];
    $dense1->biases = $data['biases'];
    $dense1->forward($X);

    $activation1 = new Activation_ReLU();
    $activation1->forward($dense1->output);

    $this->assertEqualsWithDelta($data['activation1'], $activation1->output, $this->tolerance, "Output of Activation_ReLU does not match expected output.");

}


public function testDense2Foward() {
     echo "\n TESTING....[Dense layer 2 output] \n";
    // Load JSON data
    $json_data = file_get_contents($this->path);
    $data = json_decode($json_data, true);

    // Extract spiral_data and setup layers
    list($X, $y) = $data['spiral_data'];
    $dense1 = new Layer_Dense(2, 64);
    $dense1->weights = $data['weights'];
    $dense1->biases = $data['biases'];

    $dense2 = new Layer_Dense(64, 3);
    $dense2->weights = $data['dense2_weights'];
    $dense2->biases = $data['dense2_biases'];

    $dense1->forward($X);

    $activation1 = new Activation_ReLU();
    $activation1->forward($dense1->output);

    $dense2->forward($activation1->output);
    
    $this->assertEqualsWithDelta($data['dense2_output'], $dense2->output, $this->tolerance, "Output of Dense 2 does not match expected output.");

}


public function testLossFoward() {
     echo "\n TESTING....[Loss output] \n";
    // Load JSON data
    $json_data = file_get_contents($this->path);
    $data = json_decode($json_data, true);

    // Extract spiral_data and setup layers
    list($X, $y) = $data['spiral_data'];
    $dense1 = new Layer_Dense(2, 64);
    $dense1->weights = $data['weights'];
    $dense1->biases = $data['biases'];

    $dense2 = new Layer_Dense(64, 3);
    $dense2->weights = $data['dense2_weights'];
    $dense2->biases = $data['dense2_biases'];

    $dense1->forward($X);

    $activation1 = new Activation_ReLU();
    $activation1->forward($dense1->output);

    $dense2->forward($activation1->output);
    
    $loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();


    $loss = $loss_activation->forward($dense2->output, $y,false);
    $predictions = NumpyLight::accuracy($loss_activation->output, $y);

    $this->assertEqualsWithDelta($data['loss'], $loss, $this->tolerance, "Output of Dense 2 does not match expected output.");

}


public function testLossDInputsFoward() {
     echo "\n TESTING....[loss_dinputs output,dense2_dweights, dense2_dbiases, dense2_dinputs ] \n";
    // Load JSON data
    $json_data = file_get_contents($this->path);
    $data = json_decode($json_data, true);

    // Extract spiral_data and setup layers
    list($X, $y) = $data['spiral_data'];
    $dense1 = new Layer_Dense(2, 64);
    $dense1->weights = $data['weights'];
    $dense1->biases = $data['biases'];

    $dense2 = new Layer_Dense(64, 3);
    $dense2->weights = $data['dense2_weights'];
    $dense2->biases = $data['dense2_biases'];

    $dense1->forward($X);

    $activation1 = new Activation_ReLU();
    $activation1->forward($dense1->output);

    $dense2->forward($activation1->output);
    
    $loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();

    
    $loss = $loss_activation->forward($dense2->output, $y);
    $predictions = NumpyLight::accuracy($loss_activation->output, $y);

    $loss_activation->backward($loss_activation->output, $y);
    $dense2->backward($loss_activation->dinputs);
    

    $this->assertEqualsWithDelta($data['loss_dinputs'], $loss_activation->dinputs, $this->tolerance, "Output of loss_dinputs not match expected output.");

    $this->assertEqualsWithDelta($data['dense2_dweights'], $dense2->dweights, $this->tolerance, "Output of dense2_dweights does not match expected output.");

    $this->assertEqualsWithDelta($data['dense2_dbiases'], $dense2->dbiases, $this->tolerance, "Output of dense2_dbiases does not match expected output.");

    $this->assertEqualsWithDelta($data['dense2_dinputs'], $dense2->dinputs, $this->tolerance, "Output of dense2_dinputs does not match expected output.");


}



public function testMull() {
     echo "\n TESTING....[testMull] \n";
    // Load JSON data
    $json_data = file_get_contents($this->path);
    $data = json_decode($json_data, true);

    // Extract spiral_data and setup layers
    list($X, $y) = $data['spiral_data'];

    $temp = NumpyLight::dot(NumpyLight::transpose($X), $data['activation1_dinputs']);
   
    $this->assertEqualsWithDelta($data['dense1_dweights'], $temp, $this->tolerance, "Output of dot dont match does not match expected output.");

    // $this->assertEqualsWithDelta($data['dense1_dinputs'], $dense1->dinputs, 0.0001, "Output of dense1_dinputs does not match expected output.");


}


public function testComprehensiveNeuralNetworkFlow()
    {
        $X = $this->data['X'];
        $y = $this->data['y'];

        // Dense Layer 1
        $dense1 = new Layer_Dense(2, 64);
        $dense1->weights = $this->data['dense1_weights'];
        $dense1->biases = $this->data['dense1_biases'];
        $dense1->forward($X);
        $this->assertEqualsWithDelta($this->data['dense1_output'], $dense1->output, $this->tolerance, "Output of Layer_Dense1 does not match expected output.");

        // Activation ReLU
        $activation1 = new Activation_ReLU();
        $activation1->forward($dense1->output);
        $this->assertEqualsWithDelta($this->data['activation1_output'], $activation1->output, $this->tolerance, "Output of Activation_ReLU does not match expected output.");

        // Dense Layer 2
        $dense2 = new Layer_Dense(64, 3);
        $dense2->weights = $this->data['dense2_weights'];
        $dense2->biases = $this->data['dense2_biases'];
        $dense2->forward($activation1->output);
        $this->assertEqualsWithDelta($this->data['dense2_output'], $dense2->output, $this->tolerance, "Output of Dense2 does not match expected output.");

        // Loss Activation
        $loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();
        $loss = $loss_activation->forward($dense2->output, $y,false);
        $this->assertEqualsWithDelta($this->data['loss'], $loss, $this->tolerance, "Output of Loss Activation does not match expected output.");

        // Loss Activation Backward
        $loss_activation->backward($loss_activation->output, $y);
        $this->assertEqualsWithDelta($this->data['loss_dinputs'], $loss_activation->dinputs, $this->tolerance, "Output of Loss Activation Backward does not match expected output.");

        // Dense2 Backward
        $dense2->backward($loss_activation->dinputs);
        $this->assertEqualsWithDelta($this->data['dense2_dweights'], $dense2->dweights, $this->tolerance, "Output of Dense2 Backward (dweights) does not match expected output.");
        $this->assertEqualsWithDelta($this->data['dense2_dbiases'], $dense2->dbiases, $this->tolerance, "Output of Dense2 Backward (dbiases) does not match expected output.");
        $this->assertEqualsWithDelta($this->data['dense2_dinputs'], $dense2->dinputs, $this->tolerance, "Output of Dense2 Backward (dinputs) does not match expected output.");

        // Activation ReLU Backward
        $activation1->backward($dense2->dinputs);
        $this->assertEqualsWithDelta($this->data['activation1_dinputs'], $activation1->dinputs, $this->tolerance, "Output of Activation ReLU Backward does not match expected output.");

        // Dense1 Backward

        NumpyLight::addKeyValueToJson('check_these.json', 'dense1_dweights', $this->data['dense1_dweights']);
        NumpyLight::addKeyValueToJson('check_these.json', 'activation1_dinputs', $activation1->dinputs);
        NumpyLight::addKeyValueToJson('check_these.json', 'dense1_inputs', $dense1->inputs);
        NumpyLight::addKeyValueToJson('check_these.json', 'dense1_dweights_php', $dense1->dweights);


        $this->assertEqualsWithDelta($this->data['dense1_inputs'], $dense1->inputs, $this->tolerance, "Output of Denseasdasdasda1 Backward (dweights) does not match expected output.");
        $this->assertEqualsWithDelta($this->data['activation1_dinputs'], $activation1->dinputs, $this->tolerance, "Output of asdasdasd Backward (dweights) does not match expected output.");
        
        $this->assertEqualsWithDelta($this->data['dense1_dweights'], NumpyLight::dot(NumpyLight::transpose($this->data['dense1_inputs']), $this->data['activation1_dinputs']), $this->tolerance, "Output of Dense1 inputs does not match expected output.");
        
        $this->assertEqualsWithDelta($this->data['dense1_dweights'], NumpyLight::dot(NumpyLight::transpose($dense1->inputs), $activation1->dinputs), $this->tolerance, "Output of Dense1 inputs does not match expected output.");

        ///


        $dense1->backward($activation1->dinputs);
        $this->assertEqualsWithDelta($this->data['dense1_dweights'], $dense1->dweights, 0.0001, "Output of Dense1 Backward (dweights) does not match expected output.");
        $this->assertEqualsWithDelta($this->data['dense1_dbiases'], $dense1->dbiases, 0.0001, "Output of Dense1 Backward (dbiases) does not match expected output.");
        $this->assertEqualsWithDelta($this->data['dense1_dinputs'], $dense1->dinputs, 0.0001, "Output of Dense1 Backward (dinputs) does not match expected output.");

        // Optimizer
        // $optimizer = new Optimizer_RMSprop();
        // // Update weights and biases
        // $optimizer->pre_update_params();
        // $optimizer->update_params($dense1);
        // $optimizer->update_params($dense2);
        // $optimizer->post_update_params();
        // // Assuming you have the final weights and biases after optimization in your JSON
        // $this->assertEqualsWithDelta($this->data['optimizer_dense1_weights'], $dense1->weights, 0.0001, "Weights after optimizer for Dense1 do not match expected weights.");
        // $this->assertEqualsWithDelta($this->data['optimizer_dense2_weights'], $dense2->weights, 0.0001, "Weights after optimizer for Dense2 do not match expected weights.");
    }

public function testLossBinaryCrossentropyWithLargeDataset()
{   
         echo "\n TESTING....[testLossBinaryCrossentropyWithLargeDataset] \n";

    $filePath = __DIR__ . '/binary_crossentropy_large_dataset.json';
    $json_data = file_get_contents($filePath);
    $data = json_decode($json_data, true);

    $loss_binary_crossentropy = new Loss_BinaryCrossentropy();

    // Forward pass test
    $forward_output = $loss_binary_crossentropy->forward($data['y_pred'], $data['y_true']);
    //$this->assertEquals($data['forward_output'], $forward_output, "Forward pass output does not match the expected result.", 0.0001);

    $this->assertEqualsWithDelta($data['forward_output'], $forward_output, $this->tolerance, "Forward pass output does not match the expected result.");

    // Prepare a dummy gradient array with ones (assuming the shape is similar to y_pred)
    $dvalues = array_fill(0, count($data['y_pred']), [1]);

    // Backward pass test
    $loss_binary_crossentropy->backward($dvalues, $data['y_true']);
    $backward_output = $loss_binary_crossentropy->dinputs;
    //$this->assertEquals($data['backward_output'], $backward_output, "Backward pass output does not match the expected result.", 0.0001);


    $this->assertEqualsWithDelta($data['backward_output'], $backward_output, $this->tolerance, "Backward pass output does not match the expected result.");

}

public function testActivationSigmoidWithLargeDataset()
{
    echo "\nTESTING....[testActivationSigmoidWithLargeDataset = Activation_Sigmoid Forward and Backward Pass]\n";
    $filePath = __DIR__ . '/activation_sigmoid_large_dataset.json';
    $json_data = file_get_contents($filePath);
    $data = json_decode($json_data, true);

    $activation_sigmoid = new Activation_Sigmoid();

    // Forward pass test
    $activation_sigmoid->forward($data['inputs']);
    $expected_forward_output = $data['forward_output'];
    $this->assertEqualsWithDelta($expected_forward_output, $activation_sigmoid->output, $this->tolerance, "Forward pass does not match expected output");

    // Assuming a dummy gradient array for backward pass
    $dvalues = array_fill(0, count($data['inputs']), [1]);

    // Backward pass test
    $activation_sigmoid->backward($dvalues);
    $backward_output = $activation_sigmoid->dinputs;
    $expected_backward_output = $data['backward_output'];
    $this->assertEqualsWithDelta($expected_backward_output, $backward_output, $this->tolerance, "Backward pass does not match expected output");
}

public function testLossBinaryCrossentropyCalculateWithLargeDataset()
{
    echo "\nTESTING....[testLossBinaryCrossentropyCalculateWithLargeDataset = Activation_Sigmoid Forward and Backward Pass]\n";
    $filePath = __DIR__ . '/activation_sigmoid_large_dataset.json';
    $json_data = file_get_contents($filePath);
    $data = json_decode($json_data, true);

    $loss_binary_crossentropy = new Loss_BinaryCrossentropy();

    // Extract y_pred and y_true from the data
    $y_pred = $data['forward_output'];
    $y_true = $data['y_true'];

    // Expected loss from Python calculation
    $expected_loss = 0.8059; // Replace with the actual loss from Python if needed

    // Calculate the loss using your PHP Loss_BinaryCrossentropy class
    $calculated_loss = $loss_binary_crossentropy->calculate($y_pred, $y_true,false);

    // Assert if the calculated loss is close to the expected loss
    $this->assertEqualsWithDelta($expected_loss, $calculated_loss, 0.0001, "Calculated loss does not match expected output");
}


public function testMeanAbsoluteErrorLoss() {
        echo "\nTESTING....[testMeanAbsoluteErrorLoss]\n";
        $filePath = __DIR__ . '/mse_loss_data.json'; // Update with the actual file path
        $json_data = file_get_contents($filePath);
        $data = json_decode($json_data, true);

        $loss_mean_absolute_error = new Loss_MeanAbsoluteError();

        // Extract y_pred and y_true from the data
        $y_pred = $data['y_pred'];
        $y_true = $data['y_true'];

        // Extract expected forward and backward outputs from the data
        $expected_forward_output = $data['forward_output'];
        $expected_backward_output = $data['backward_output'];

        // Perform forward pass
        $forward_output = $loss_mean_absolute_error->forward($y_pred, $y_true);
        // Perform backward pass
        $loss_mean_absolute_error->backward($y_pred, $y_true);
        $backward_output = $loss_mean_absolute_error->dinputs;

        // Assert if the forward and backward outputs are close to the expected values
        
        $this->assertEqualsWithDelta($expected_forward_output, $forward_output, 0.0001, "Forward pass does not match expected output");
        
        $this->assertEqualsWithDelta($expected_backward_output, $backward_output, 0.0001, "Backward pass does not match expected output");
        
    }

    public function testStd() {
        echo "\nTESTING....[testStd]\n";
        $filePath = __DIR__ . '/mse_loss_data.json'; // Update with the actual file path
        $json_data = file_get_contents($filePath);
        $data = json_decode($json_data, true);

        $loss_mean_absolute_error = new Loss_MeanAbsoluteError();

        // Extract y_pred and y_true from the data
        $y_pred = $data['y_pred'];
        $y_true = $data['y_true'];

        $std = NumpyLight::std($y_pred);
        
        $this->assertEqualsWithDelta(0.2895911034398418, $std, 0.0001, "Forward pass does not match expected output");
        
        
    }

    public function testBinaryCrossentropyLoss() {
    echo "\nTESTING....[testBinaryCrossentropyLoss]\n";
    $filePath = __DIR__ . '/Loss_BinaryCrossentropy_data.json'; // Update with the actual file path
    $json_data = file_get_contents($filePath);
    $data = json_decode($json_data, true);

    $loss_binary_crossentropy = new Loss_BinaryCrossentropy();

    // Extract y_pred and y_true from the data
    $y_pred = $data['y_pred'];
    $y_true = $data['y_true'];

    // Forward pass
    $forward_output = $loss_binary_crossentropy->forward($y_pred, $y_true);
    $this->assertEqualsWithDelta($data['forward_output'], $forward_output, 0.00001, "Forward pass does not match expected output");

    // Backward pass
    $loss_binary_crossentropy->backward($y_pred, $y_true);
    $backward_output = $loss_binary_crossentropy->dinputs;
    $this->assertEqualsWithDelta($data['backward_output'], $backward_output, 0.00001, "Backward pass does not match expected output");
}



}
?>