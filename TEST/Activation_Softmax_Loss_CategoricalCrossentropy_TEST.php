<?PHP
include_once("../CLASSES/headers.php");

use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 


function TEST_1(){
    list($X, $y) =  np::spiral_data(100, 3);

    $softmax_outputs = [
        [0.7, 0.1, 0.2],
        [0.1, 0.5, 0.4],
        [0.02, 0.9, 0.08]
    ];

    $class_targets = [0, 1, 1];

    $softmax_loss = new Activation_Softmax_Loss_CategoricalCrossentropy();
    $softmax_loss->backward($softmax_outputs,$class_targets);
    $dvalues1 = $softmax_loss->dinputs;

    $activation = new Activation_Softmax();
    $activation->output = $softmax_outputs;
    $loss = new Loss_CategoricalCrossentropy();

    $loss->backward($softmax_outputs,$class_targets);
    $activation->backward($loss->dinputs);
    $dvalue2 = $activation->dinputs;

    echo "Gradients: combined loss and activation: \n";
    np::printMatrix($dvalues1);
    echo "Gradients: separate loss and activation: \n";
    np::printMatrix($dvalue2);

}

function TEST_2(){

    $dinputs = [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6], [0.7, 0.8, 0.9]];
    $y_true = [0, 1, 2];
    $value = 1;

    $result = np::subtractFromDInputs($dinputs, $y_true, $value);
    var_dump($result);
}

function TEST_3(){
    list($X, $y) =  np::spiral_data(100, 3);

    $dense1 = new Layer_Dense(2,3);

    $activation1 = new Activation_Relu();

    $dense2 = new Layer_Dense(3,3);

    $loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();

    $dense1->forward($X);


    $activation1->forward($dense1->output);

    $dense2->forward($activation1->output);

    $loss = $loss_activation->forward($dense2->output,$y);

    np::printMatrix($loss_activation->output,5);

    $acc = np::accuracy($loss_activation->output,$y);

    echo "acc: $acc , loss: $loss\n";


    $loss_activation->backward($loss_activation->output,$y);
    $dense2->backward($loss_activation->dinputs);
    $activation1->backward($dense2->dinputs);
    $dense1->backward($activation1->dinput);

    echo "\n";
    np::printMatrix($dense1->dweights);
    echo "\n";
    np::printMatrix($dense1->dbiases);
    echo "\n";
    np::printMatrix($dense2->dweights);
    echo "\n";
    np::printMatrix($dense2->dbiases);


}



function TEST_4(){
    list($X, $y) =  np::spiral_data(100, 3);
    $yValues = [];

    $yLossValues = [];

    $yAccValues = [];

    $dense1 = new Layer_Dense(2,64);

    $activation1 = new Activation_Relu();

    $dense2 = new Layer_Dense(64,3);

    $loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();


    $learning_rate = 0.85;
    $decay = 1e-3;
    $momentum = 0;//0.5;

    $optimizer = new Optimizer_SGD($learning_rate,$decay,$momentum);

    $accold = -1;

    for($i = 0; $i < 10000; $i++){

        $dense1->forward($X);

        $activation1->forward($dense1->output);

        $dense2->forward($activation1->output);

        $loss = $loss_activation->forward($dense2->output,$y);

        $acc = np::accuracy($loss_activation->output,$y);


        if (($i%100==0)) {

            $yValues[] = $optimizer->current_learning_rate;

            $yLossValues[] = $loss;

            $yAccValues[] = $acc;


            echo "epoc: $i,\tacc: $acc\t,loss: $loss,\tlr: ".$optimizer->current_learning_rate."\n";
        }


        $loss_activation->backward($loss_activation->output,$y);
        $dense2->backward($loss_activation->dinputs);
        $activation1->backward($dense2->dinputs);
        $dense1->backward($activation1->dinput);

        $optimizer->pre_update_params();
        $optimizer->update_params($dense1);
        $optimizer->update_params($dense2);
        $optimizer->post_update_params();

    }


    $grapher = new Grapher();
    $grapher->addColor([255, 0, 0]); // Red color for class 0
    $grapher->addColor([0, 255, 0]); // Green color for class 1
    $grapher->addColor([0, 0, 255]); // Blue color for class 2

    $filename = 'spiral-data.png';
    $grapher->createImage($X, $y, $filename);

    //Learning rate
    $imageWidth = 400;
    $imageHeight = 300;
    $outputPath = "2.png";
    $label = 'Learning rate';

    $grapher->drawLineGraph(null, $yValues, $imageWidth, $imageHeight, $outputPath, $label);


    $outputPath = "3.png";
    $label = 'Loss';

    $grapher->drawLineGraph(null, $yLossValues, $imageWidth, $imageHeight, $outputPath, $label);


    $outputPath = "4.png";
    $label = 'Accuracy';

    $grapher->drawLineGraph(null, $yAccValues, $imageWidth, $imageHeight, $outputPath, $label);



    $graph1 = imagecreatefrompng($filename);
    $graph2 = imagecreatefrompng($outputPath);
    $graph3 = imagecreatefrompng("3.png");
    $graph4 = imagecreatefrompng("2.png");

    $graphs = [$graph1, $graph2,$graph3,$graph4];
    $outputPath = "learning_rate = $learning_rate ,decay = $decay,monument = $momentum.png";

    $grapher->combineGraphs($graphs, $outputPath);



}


function TEST_SOFTMAX(){

    list($X, $y) =  np::spiral_data(100, 3);

    $dense1 = new Layer_Dense(2,3);

    $activation1 = new Activation_Relu();

    $dense2 = new Layer_Dense(3,3);

    $loss_activation = new Activation_Softmax();

    $dense1->forward($X);

    $activation1->forward($dense1->output);

    $dense2->forward($activation1->output);

    $loss = $loss_activation->forward($dense2->output,$y);


    $Loss_CategoricalCrossentropy= new Loss_CategoricalCrossentropy();
    $los = $Loss_CategoricalCrossentropy->calculate($loss_activation->output,$y);
    np::printMatrix($loss_activation->output,5);

    $acc = np::accuracy($loss_activation->output,$y);


    echo "loss $los $acc";

    
}


function test_grapher(){
    list($X, $y) =  np::spiral_data(100, 3);

    $grapher = new Grapher();
    $grapher->addColor([255, 0, 0]); // Red color for class 0
    $grapher->addColor([0, 255, 0]); // Green color for class 1
    $grapher->addColor([0, 0, 255]); // Blue color for class 2

    //     $filename = '1.png';

    //     $grapher->createImage($X, $y, $filename);



    // $xValues = [1, 2, 3, 4, 5];
    // $yValues = [10, 15, 8, 12, 6];
    // $imageWidth = 400;
    // $imageHeight = 300;
    // $outputPath = "2.png";
    // $label = 'Line Graph Example';

    // $grapher->drawLineGraph(null, $yValues, $imageWidth, $imageHeight, $outputPath, $label);

    $graph1 = imagecreatefrompng('1.png');
    $graph2 = imagecreatefrompng('2.png');
    $graph3 = imagecreatefrompng('3.png');
    $graph4 = imagecreatefrompng('4.png');

    $graphs = [$graph1, $graph2, $graph3];
    $outputPath = 'combined.png';

    $grapher->combineGraphs($graphs, $outputPath);



}

function TEST_6(){
    list($X, $y) =  np::spiral_data(100, 3);

    $dense1 = new Layer_Dense(2,3);

    $activation1 = new Activation_Relu();

    $dense2 = new Layer_Dense(3,3);

    $activation2 = new Activation_Softmax();

    $loss = new Loss_CategoricalCrossentropy();

    $dense1->forward($X);

    $activation1->forward($dense1->output);

    $dense2->forward($activation1->output);

    $activation2->forward($dense2->output);

    echo "\n Loss: ".$loss->calculate($activation2->output,$y)."\n";

    np::printMatrix($activation2->output,5);

    

// [0.33333333333333, 0.33333333333333, 0.33333333333333]
// [0.3333328805805, 0.3333332388716, 0.33333388054789]
// [0.33333241448518, 0.33333312843774, 0.33333445707708]
// [0.33333197538193, 0.3333328974254, 0.33333512719266]
// [0.33333155836908, 0.33333273616165, 0.33333570546927]

// Loss: 1.0986142824252

}

function testLoss(){
    $output = [ [0.7,0.1,0.2],
                [0.5,0.1,0.4],
                [0.02,0.9,0.08]];
    $target = [ [1,0,0],
                [0,1,0],
                [0,1,0]];
    $target = [0,1,1];


    // $loss_activation = new Loss_CategoricalCrossentropy();
    // // $loss = $loss_function->calculate($output,$target);
    // // var_dump($loss);

    // $loss = $loss_activation->forward($dense2->output,$y);

    //np::printMatrix($loss_activation->output,5);

    $acc = np::accuracy($output,$target);

    echo "acc: $acc , loss: \n";

}


//TEST_4();
TEST_3();
?>