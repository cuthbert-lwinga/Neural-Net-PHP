<?PHP
include_once("../CLASSES/headers.php");

use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 

function TEST_ADAM_DECAY(){
    
    list($X, $y) =  np::spiral_data(100, 3);
    $yValues = [];
    $yLossValues = [];
    $yAccValues = [];
    $dense1 = new Layer_Dense(2,64);
    $activation1 = new Activation_Relu();
    $dense2 = new Layer_Dense(64,3);
    $loss_activation = new Activation_Softmax_Loss_CategoricalCrossentropy();
    $learning_rate = 1;
    $decay = 1e-4;
    $momentum = 0;//0.5;
    $optimizer = new optimizer_Adagrad($learning_rate,$decay);
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

    $Matrix_has_NAN = MathOperations::findNaN($loss_activation->output);

    if ($Matrix_has_NAN !== null) {
        $rowIndex = $Matrix_has_NAN['row'];
        $colIndex = $Matrix_has_NAN['col'];
        echo "Error: NaN found in loss_activation->output $rowIndex, col $colIndex\n";
        die();
    } 

        $dense2->backward($loss_activation->dinputs);
        $activation1->backward($dense2->dinputs);
        $dense1->backward($activation1->dinput);

        $optimizer->pre_update_params();
        $optimizer->update_params($dense1);
        $optimizer->update_params($dense2);
        $optimizer->post_update_params();

        // np::printMatrix($activation1->output,5);

        // echo "------------------------\n\n";

    }

   // die();
    $grapher = new Grapher();
$grapher->addColor([255, 0, 0]);    // Red color for class 0
$grapher->addColor([0, 255, 0]);    // Green color for class 1
$grapher->addColor([0, 0, 255]);    // Blue color for class 2
$grapher->addColor([255, 255, 0]);  // Yellow color
$grapher->addColor([128, 0, 128]);  // Purple color
$grapher->addColor([255, 165, 0]);  // Orange color
$grapher->addColor([0, 255, 255]);  // Cyan color
$grapher->addColor([255, 0, 255]);  // Magenta color
$grapher->addColor([0, 255, 0]);    // Lime color
$grapher->addColor([0, 128, 128]);  // Teal color
$grapher->addColor([255, 192, 203]); // Pink color
$grapher->addColor([165, 42, 42]);  // Brown color
$grapher->addColor([255, 215, 0]);  // Gold color


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


//softmax();
TEST_ADAM_DECAY();
?>