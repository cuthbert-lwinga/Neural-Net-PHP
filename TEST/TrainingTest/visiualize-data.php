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

$imageProcessor = new ImageProcessor("");

for ($i=0; $i < 10; $i++) { 
    $imageProcessor->printGrayscaleArray($X[$i]);
    echo "\n\n\n\n\n\n";
}



?>