<?PHP
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight;

abstract class Accuracy {

    public $accumulated_sum = 0;
    public $accumulated_count = 0;

    // Calculates an accuracy given predictions and ground truth values
    public function calculate($predictions, $y) {
        $comparisons = $this->compare($predictions, $y);
        $correct_predictions = count(array_filter($comparisons));
        $total_predictions = count($comparisons);

        # Add accumulated sum of matching values and sample count
        $this->accumulated_sum += NumpyLight::sum($comparisons);
        $this->accumulated_count += count($comparisons);

        return $total_predictions > 0 ? $correct_predictions / $total_predictions : 0;
    }

    public function new_pass(){
        $this->accumulated_sum = 0;
        $this->accumulated_count = 0;
    }
    
    public function calculate_accumulated(){
        # Calculate mean loss
        $data_loss = $this->accumulated_sum / $this->accumulated_count;
        return $data_loss;
    }

    // Abstract method for comparing predictions with ground truth
    abstract protected function compare($predictions, $y);
}


class Accuracy_Regression extends Accuracy {
    private $precision;

    public function __construct() {
        $this->precision = null;
    }

    public function init($y, $reinit = false) {
        if ($this->precision === null || $reinit) {
            $std_dev = NumpyLight::std($y);
            $this->precision = $std_dev / 250;
        }
    }

protected function compare($predictions, $y) {
    if (!is_array($predictions) || !is_array($y)) {
        throw new Exception("Predictions and ground truths must be arrays.");
    }
    if (count($predictions) !== count($y)) {
        throw new Exception("Predictions and ground truths must be of the same length.");
    }

    $comparisons = array();
    foreach ($predictions as $index => $prediction) {
        // Extract the numeric value if $prediction and $y[$index] are arrays
        $pred_value = is_array($prediction) && count($prediction) > 0 ? $prediction[0] : $prediction;
        $y_value = is_array($y[$index]) && count($y[$index]) > 0 ? $y[$index][0] : $y[$index];

        // Ensure both $pred_value and $y_value are numbers
        if (!is_numeric($pred_value) || !is_numeric($y_value)) {
            throw new Exception("Both predictions and ground truths must be numeric.");
        }

        $comparisons[] = abs($pred_value - $y_value) < $this->precision;
    }
    return $comparisons;
}

}

class Accuracy_Categorical extends Accuracy {
    // No initialization is needed
    public function init($y) {
        // No initialization code needed
    }

    // Compares predictions to the ground truth values
    protected function compare($predictions, $y) {
        // Check if y is a two-dimensional array
        $shapeY = NumpyLight::shape($y);
        if (count($shapeY) == 2) {
            // If y is two-dimensional, use argmax to find the maximum index in each row
            $y = NumpyLight::argmax($y);
        } else {
            // Extract the single value from each sub-array
             $y = array_map(function($item) {
            return is_array($item) ? $item[0] : $item;
        }, $y);
        }

        // Compare predictions with the maximum index (or value) of y
        $comparisons = array();
        foreach ($predictions as $index => $prediction) {
            // Extract the single value from each sub-array in predictions
            $pred_value = is_array($prediction) ? $prediction[0] : $prediction;

            if (!is_numeric($pred_value)) {
                throw new Exception("Predictions must be numeric.");
            }
            $comparisons[] = ($pred_value == $y[$index]);
        }

        return $comparisons;
    }

}



?>