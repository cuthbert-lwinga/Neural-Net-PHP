<?PHP

ini_set('memory_limit', '1024M'); // Increase the memory limit to 1024MB
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;
use NameSpaceOptimizerSGD\Optimizer_SGD;
use NameSpaceOptimizerAdagrad\Optimizer_Adagrad;
use NameSpaceOptimizerRMSprop\Optimizer_RMSprop;

list($X, $y) = NumpyLight::sine_data($samples = 10000);
$validation = NumpyLight::sine_data($samples = 10000);

// Initialize loss function and optimizer
$loss_function = new Loss_MeanSquaredError();
$optimizer = new Optimizer_Adam();


echo "\n\n Model Init \n\n";
echo "\n\n🚀 Training on sine wave dataset will commence 🚀\n\n";

$Model = new Model();
$Model->add(new Layer_Dense(1, 512));
$Model->add(new Activation_ReLU());
$Model->add(new Layer_Dense(512, 512));
$Model->add(new Activation_ReLU());
$Model->add(new Layer_Dense(512, 1));
$Model->add(new Activation_Linear());
$Model->set(
	$loss_function = new Loss_MeanSquaredError(),
	$optimizer = new Optimizer_Adam(),
	$accuracy = new Accuracy_Regression()
);

$Model->finalize();

$Model->train($X, $y,$epoch = 20000, $batch_size = 1000,$print_every = 100,$validation_data = $validation);

list($x_test,$y_test) = $validation;

$y_test = NumpyLight::reshape($y_test,[-1,1]);

$flattened_y_test = array_merge(...array_map('array_values', $y_test));
$activation2_y = array_merge(...array_map('array_values', NumpyLight::reshape($Model->output_layer_activation->output,[-1,1])));

$lines = [
    [
        'yValues' => $flattened_y_test,
        'color' => 'blue'
    ],
    [
        'yValues' => $activation2_y,
        'color' => 'red'
    ]
];


$plotterTemp = new LinePlotter(500, 500);
$plotterTemp->setColor('blue', 0, 0, 255); // Blue color
$plotterTemp->setColor('red', 255, 0, 0); // Blue color

// Plot multiple lines using only y-values
$plotterTemp->plotMultipleLinesWithYOnly($lines);

$plotterTemp->save("test-fit.png");


?>