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

    $testData = load_mnist_dataset('test', $path);
    $X_test = $testData['X'];
    $y_test = $testData['y'];

    // And return all the data
    return [$X_test,$y_test];
}


$mnist_data = create_data_mnist("fashion_mnist_images");

list($X_test, $y_test) = $mnist_data;

// $keys = range(0, NumpyLight::shape($X_test)[0] - 1);

NumpyLight::random()->shuffle($keys);

$X_test = NumpyLight::divide(
		NumpyLight::subtract(
			NumpyLight::reshape(
				$X_test,
				[NumpyLight::shape($X_test)[0],-1])
			,
			127.5)
		,127.5);

$validation = [$X_test, $y_test];

echo "\n\nModel Init\n\n";


$Model = Model::load('saved_model');


// $Model->evaluate($X_test, $y_test);

$confidences = $Model->predict($X_test);
$predictions = $Model->output_layer_activation->predictions($confidences);

$fashion_mnist_labels = [
    0 => 'T-shirt/top',
    1 => 'Trouser',
    2 => 'Pullover',
    3 => 'Dress',
    4 => 'Coat',
    5 => 'Sandal',
    6 => 'Shirt',
    7 => 'Sneaker',
    8 => 'Bag',
    9 => 'Ankle boot'
];

for ($i = 0; $i < count($predictions); $i++) {
    // Prepare the label text
    $labelText = $fashion_mnist_labels[$predictions[$i]] . " actual label " . $fashion_mnist_labels[$y_test[$i]];

    // Pad the label text to a fixed length, e.g., 50 characters
    $paddedLabelText = str_pad($labelText, 50);

    // Echo the padded label text
    echo $paddedLabelText . " ";

    // Check condition and echo the symbol
    if ($fashion_mnist_labels[$predictions[$i]] == $fashion_mnist_labels[$y_test[$i]]) {
        echo "✅"; // Green check mark for true
    } else {
        echo "❌"; // Red cross for false
    }

    echo "\n\n";
}





?>