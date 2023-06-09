<?PHP
include_once("../CLASSES/headers.php");
use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 

class Loss_CategoricalCrossentropy extends Loss {
    // public function forward($y_pred, $y_true) {
    //     $samples = count($y_pred);
    //     $y_pred_clipped = array_map(function($value) {
    //         return max(1e-7, min($value, 1-1e-7));
    //     }, $y_pred);

    //     $correct_confidences = array_map(function($i) use ($y_pred_clipped, $y_true) {
    //         return $y_pred_clipped[$i][$y_true[$i]];
    //     }, range(0, $samples - 1));

    //     $negative_log_likelihoods = array_map(function($value) {
    //         return -log($value);
    //     }, $correct_confidences);

    //     return $negative_log_likelihoods;
    // }

public function forward($y_pred, $y_true) {
        $samples = count($y_pred);
        $y_pred_clipped = np::clip($y_pred,1e-7,1-1e-7); // to not have infinity numbers



        if (np::checkArrayShape($y_true)==1) {
        	$correct_confidence = np::extract_matrix_by_scalar($y_pred_clipped,$y_true);	
        }else{
        	$correct_confidence = np::extract_matrix_one_hot_encoded($y_pred_clipped,$y_true);
        }

        $negavtive_log_likelyhood = np::log($correct_confidence);
        $negavtive_log_likelyhood = np::multiply_scalar($negavtive_log_likelyhood,-1);

        
        return $negavtive_log_likelyhood;
    }

function backward($dvalues, $y_true) {
    // Number of samples
    $samples = count($dvalues);
    // Number of labels in every sample
    $labels = count($dvalues[0]);

    // If labels are sparse, turn them into one-hot vector
    if (np::checkArrayShape($y_true)==1) {
        $y_true = np::eye($labels)[$y_true];
    }

    // Calculate gradient
    $dinputs = [];
    for ($i = 0; $i < $samples; $i++) {
        $row = [];
        for ($j = 0; $j < $labels; $j++) {
            $row[] = -$y_true[$i][$j] / $dvalues[$i][$j];
        }
        $dinputs[] = $row;
    }

    // Normalize gradient
    $dinputs = multiplyArray($dinputs, 1 / $samples);

    $this->dinputs = $dinputs;
}



    public function calculate($output, $y) {
        $sample_losses = $this->forward($output, $y);
        $data_loss = array_sum($sample_losses) / count($sample_losses);
        return $data_loss;
    }
}
// Test code
//$X = spiral_data($samples = 100, $classes = 3);
// list($X, $y) =  np::spiral_data(100, 3);
// $dense1 = new Layer_Dense(2, 3);
// $dense1->foward($X);

// $activation1 = new Activation_ReLU($dense1->output);

// $dense2 = new Layer_Dense(3, 3);
// $dense2->foward($activation1->output);

// $activation2 = new Activation_Softmax($dense2->output);
// $activation2->foward($dense2->output);



// echo "Output:\n";
// print_r(array_slice($activation2->output, 0, 5));

// $loss_function = new Loss_CategoricalCrossentropy();
// $loss = $loss_function->calculate($activation2->output, $y);

// echo "Loss: " . $loss . "\n";



?>