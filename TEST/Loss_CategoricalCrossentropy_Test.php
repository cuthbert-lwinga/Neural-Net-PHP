<?PHP
include_once("../CLASSES/headers.php");

use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 


$input = array(
  array(1, 2, 3,2.5),
  array(2.0,5.0, -1.0, 2.0),
  array(-1.5,2.7,3.3,-0.8)
);



function TEST_1(){
$Layer1 = new Layer_Dense(2,5);
$Layer2 = new Layer_Dense(5,2);
$Layer1->forward($input);
$Layer2->forward($Layer1->output);

var_dump($Layer2->output);

}


function TEST_2(){
list($X, $y) =  np::spiral_data(100, 4);
$Layer1 = new Layer_Dense(2,5);
$Layer2 = new Layer_Dense(5,2);
$Layer1->forward($X);
$activation1 = new Activation_Relu($Layer1->output);
$activation1->forward();
$testOutput = Test::activation1Check($activation1->output);
var_dump($testOutput);

}

function TEST_3(){
list($X, $y) =  np::spiral_data(100, 3);
$Layer1 = new Layer_Dense(2,3);
$Layer1->forward($X);
$activation1 = new Activation_Relu($Layer1->output);
$activation1->forward();


$Layer2 = new Layer_Dense(3,3);
$Layer2->forward($activation1->output);
$activation2 = new Activation_softMax($Layer2->output);
$activation2->forward();


//var_dump($activation2->output);


$loss_function = new Loss_CategoricalCrossentropy();
$loss = $loss_function->calculate($activation2->output, $y);


var_dump($loss);


}


function TEST_4(){
list($X, $y) =  np::spiral_data(100, 3);
  

$lowes_loss = 9999999;
$best_dense1_weights = [];
$best_dense1_biases = [];
$best_dense2_weights = [];
$best_dense3_biases = [];

  $Layer1 = new Layer_Dense(2,3);
  $Layer2 = new Layer_Dense(3,3);
  $activation1 = new Activation_Relu();

for($i = 0;$i < 10000; $i++){

  //echo "shape ".np::shape($Layer1->weights)." ";
  $Layer1->weights = np::m_operator($Layer1->weights ,"+",np::m_operator(np::rand(2,3,0,1),"x",0.05));

  $Layer1->biases = np::m_operator($Layer1->biases ,"+",(np::m_operator(np::rand(1,3,0,1),"x",0.05)));

  $Layer2->weights = np::m_operator($Layer2->weights,"+",(np::m_operator(np::rand(3,3,0,1),"x",0.05)));
  
  $Layer2->biases = np::m_operator($Layer1->biases ,"+",(np::m_operator(np::rand(1,3,0,1),"x",0.05)));

  
  $Layer1->forward($X);
  $activation1->forward($Layer1->output);
  
  $Layer2->forward($activation1->output);
  $activation2 = new Activation_softMax($Layer2->output);
  $activation2->forward();

  $loss_function = new Loss_CategoricalCrossentropy();
  $loss = $loss_function->calculate($activation2->output, $y);


  if($loss<$lowes_loss){
    $acc = np::accuracy($activation2->output,$y);
    echo "new loss found iteration: $i, loss: $loss, accuracy:  $acc \n";

  $best_dense1_weights = $Layer1->weights;
  $best_dense1_biases = $Layer1->biases;
  $best_dense2_weights = $Layer2->weights;
  $best_dense2_biases = $Layer2->biases;
  $lowes_loss = $loss;
  }else{
    //echo "nop iteration: $i, loss: $loss\n";
  $Layer1->weights = $best_dense1_weights;
  $Layer1->biases = $best_dense1_biases;
  $Layer2->weights = $best_dense2_weights;
  $Layer2->biases = $best_dense2_biases;
  }



}

  echo "\n done \n";
//var_dump($activation2->output);





var_dump($loss);


}




TEST_4();

?>