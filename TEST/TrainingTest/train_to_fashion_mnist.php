<?PHP
ini_set('memory_limit', '20480M'); // Increase the memory limit to 20480MB (20GB)
include_once("../../CLASSES/Headers.php");
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceRandomGenerator\RandomGenerator;
use NameSpaceActivationRelu\Activation_Relu;
use NameSpaceOptimizerSGD\Optimizer_SGD;
use NameSpaceOptimizerAdagrad\Optimizer_Adagrad;
use NameSpaceOptimizerRMSprop\Optimizer_RMSprop;

function load_mnist_dataset($dataset, $path) {
    $labels = [];
    $dir = $path . '/' . $dataset;
    
    // Check if the main directory exists and is readable
    if (is_readable($dir) && ($dir_content = scandir($dir))) {
        foreach ($dir_content as $item) {
            if ($item !== '.' && $item !== '..' && is_dir($dir . '/' . $item)) {
                $labels[] = $item;
            }
        }
    }

    $X = [];
    $y = [];

    foreach ($labels as $label) {
        $label_path = $dir . '/' . $label;
        if (is_readable($label_path) && ($files = scandir($label_path))) {
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $label_path . '/' . $file;
                    if (is_readable($filePath) && !is_dir($filePath)) {
                        $imageProcessor = new ImageProcessor($filePath);
                        $imageData = $imageProcessor->getImageGrayscaleArray(["rows" => 28, "cols" => 28]);
                        $X[] = $imageData;
                        $y[] = $label;
                    }
                }
            }
        }
    }

    return ["X" => $X, "y" => $y];
}

function create_data_mnist($path) {
    // Load both sets separately
    $trainData = load_mnist_dataset('train', $path);
    $X = $trainData['X'];
    $y = $trainData['y'];

    $testData = load_mnist_dataset('test', $path);
    $X_test = $testData['X'];
    $y_test = $testData['y'];

    // And return all the data
    return [$X,$y,$X_test,$y_test];
}


$mnist_data = create_data_mnist("fashion_mnist_images");

list($X, $y, $X_test, $y_test) = $mnist_data;


$keys = range(0, NumpyLight::shape($X)[0] - 1);

NumpyLight::random()->shuffle($keys);

$X = NumpyLight::rearrangeMatrix($X, $keys);
$y = NumpyLight::rearrangeMatrix($y, $keys);


$X = NumpyLight::divide(
		NumpyLight::subtract(
			NumpyLight::reshape(
				$X,
				[NumpyLight::shape($X)[0],-1])
			,
			127.5)
		,127.5);

$X_test = NumpyLight::divide(
		NumpyLight::subtract(
			NumpyLight::reshape(
				$X_test,
				[NumpyLight::shape($X_test)[0],-1])
			,
			127.5)
		,127.5);

$validation = [$X_test, $y_test];

echo "\n\n Model Init \n\n";
echo "\n\n🚀 Training on MNIST dataset will commence 🚀\n\n";

$Model = new Model();
$Model->add(new Layer_Dense(NumpyLight::shape($X)[1],64));
$Model->add(new Activation_Relu());
$Model->add(new Layer_Dense(64,64));
$Model->add(new Activation_Relu());
$Model->add(new Layer_Dense(64,64));
$Model->add(new Activation_Softmax());
$Model->set(
	$loss_function = new Loss_CategoricalCrossentropy(),
	$optimizer = new Optimizer_Adam($learning_rate = 0.001, $decay = 1e-3),
	$accuracy = new Accuracy_Categorical()
);

$Model->finalize();

$Model->train($X, $y,$epoch = 200, $batch_size = 128,$print_every = 100,$validation_data = $validation);


$Model->save("fashion_mnist_model");

?>