<?PHP
include_once("../CLASSES/headers.php");
use MathOperations as np; // simulating numpy from python 
use TestOperations as Test; // For testing 

class Loss_CategoricalCrossentropy extends Loss {
    public $dinputs;

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

        $isFlat  = false;
    // If labels are sparse, turn them into one-hot vector
        if (np::checkArrayShape($y_true)==1) {
            $isFlat  = true;
        $y_true = np::np_eye_index($labels,$y_true);//;np::eye($labels)[$y_true];
    }

    // Calculate gradient
    $temp = np::m_operator($y_true,"x",-1);
    $dinputs = np::m_operator($temp,"/",$dvalues);
//     np::printMatrix($dinputs);
// echo "\n\n";
    // if ($isFlat) {
    //     $temp = $dvalues;

    //     for ($i=0; $i < count($dinputs); $i++) { 
    //         $index = array_search(1, $y_true[$i]);
    //         $temp[$i][$index] = $dinputs[$i][$index];
    //     }
    // $dinputs = $temp;
    // }
  
  // np::printMatrix($temp);
  //           echo "\n";
  //       die();


    $this->dinputs = np::m_operator($dinputs,"x",(1 / $samples));
    
}



//     public function calculate($output, $y) {
//     $sample_losses = $this->forward($output, $y);
//     $data_loss = array_sum($sample_losses) / count($sample_losses);
//     return $data_loss;
// }
}




?>