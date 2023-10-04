<?php
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight as np; // simulating numpy from python 
use NameSpaceRandomGenerator\RandomGenerator;

class Loss {
    public function calculate($output, $y) {
        $sample_losses = $this->forward($output, $y);
        $data_loss = np::mean($sample_losses);
        return $data_loss;
    }
}

class Loss_CategoricalCrossentropy extends Loss {
    public $dinputs; // i added this because of deprecation, could be an issue
    public function forward($y_pred, $y_true) {
        $samples = count($y_pred);

        $y_pred_clipped = np::clip($y_pred, 1e-7, 1 - 1e-7);
        
        if (count(np::shape($y_true)) == 1) {
            $correct_confidences = np::get_values_from_indexes($y_pred_clipped, $y_true);
        } elseif (count(np::shape($y_true))==2) {
            $correct_confidences = np::sum(np::multiply($y_pred_clipped, $y_true), 1);
        }

        $negative_log_likelihoods = np::multiply(np::log($correct_confidences),-1);
        return $negative_log_likelihoods;
    }

    public function backward($dvalues, $y_true) {
        $samples = count($dvalues);
        $labels = count($dvalues[0]);

        if (count(np::shape($y_true)) == 1) {
            $y_true = np::select_rows_by_indices(np::eye($labels),$y_true ); //np.eye(label)[y_true]
        }

        $this->dinputs = np::divide(np::multiply($y_true,-1), $dvalues);
        $this->dinputs = np::divide($this->dinputs, $samples);

    }
}
?>
