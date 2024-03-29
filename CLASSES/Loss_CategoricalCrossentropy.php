<?php
// namespace NameSpaceLossBinaryCrossentropy;
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight as np; // simulating numpy from python 
use NameSpaceRandomGenerator\RandomGenerator;

class Loss {
    protected $trainable_layers = [];
    public $accumulated_sum = 0;
    public $accumulated_count = 0;
    // Method to remember trainable layers
    public function remember_trainable_layers($trainable_layers) {
        $this->trainable_layers = $trainable_layers;
    }

public function regularization_loss() {
        $regularization_loss = 0;
if (is_array($this->trainable_layers) || is_object($this->trainable_layers)){
        foreach ($this->trainable_layers as $layer) {
        // L1 regularization - weights
        if ($layer->weight_regularizer_l1 > 0) {
            $regularization_loss += $layer->weight_regularizer_l1 * np::sum(np::abs($layer->weights));
        }

        // L2 regularization - weights
        if ($layer->weight_regularizer_l2 > 0) {
            $regularization_loss += $layer->weight_regularizer_l2 * np::sum(np::multiply($layer->weights, $layer->weights));
        }

        // L1 regularization - biases
        if ($layer->bias_regularizer_l1 > 0) {
            $regularization_loss += $layer->bias_regularizer_l1 * np::sum(np::abs($layer->biases));
        }

        // L2 regularization - biases
        if ($layer->bias_regularizer_l2 > 0) {
            $regularization_loss += $layer->bias_regularizer_l2 * np::sum(np::multiply($layer->biases, $layer->biases));
        }
    }

    }

        return $regularization_loss;
    }

    // public function calculate($output, $y) {
    //     $sample_losses = $this->forward($output, $y);
    //     $data_loss = np::mean($sample_losses);
    //     return $data_loss;
    // }


    // Modified calculate method
    public function calculate($output, $y, $include_regularization = false) {
        // Calculate data loss
        $sample_losses = $this->forward($output, $y);
        $data_loss = np::mean($sample_losses);

        $this->accumulated_sum += np::sum($sample_losses);
        $this->accumulated_count += count($sample_losses);


        if (!$include_regularization) {
            return $data_loss;
        }

        // Calculate regularization loss
        $regularization_loss = 0;
        if (is_array($this->trainable_layers) || is_object($this->trainable_layers)) {
            $regularization_loss = $this->regularization_loss();
        }

        return [$data_loss, $regularization_loss];
    }

    public function new_pass(){
        $this->accumulated_sum = 0;
        $this->accumulated_count = 0;
    }
    

    public function calculate_accumulated($include_regularization = false){
        # Calculate mean loss
        $data_loss = $this->accumulated_sum / $this->accumulated_count;
        # If just data loss - return it
        if(!$include_regularization)
            return $data_loss;
        # Return the data and regularization losses
        $regularization_loss = $this->regularization_loss();
        return [$data_loss, $regularization_loss]; // check this it need current layer 
    }





}

class Loss_CategoricalCrossentropy extends Loss {
    public $dinputs; // i added this because of deprecation could be an issue
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


class Loss_BinaryCrossentropy extends Loss{
    
    public $dinputs;

    public function forward($y_pred, $y_true) {
        // Clip data to prevent division by 0

        
        $y_pred_clipped = np::clip($y_pred, 1e-7, 1 - 1e-7);

        // Calculate sample-wise loss
        $positive_loss = np::multiply($y_true, np::log($y_pred_clipped));

        $negative_loss = np::multiply(
                            np::subtract(
                                1, 
                                $y_true
                            ), 
                            np::log(
                                np::subtract(
                                    1, 
                                    $y_pred_clipped
                                )
                            )
                        );

        $sample_losses = np::multiply(
                            -1,
                            np::add(
                                $positive_loss,
                                $negative_loss
                            )
                        );

        // Calculate mean loss
        
        $sample_losses = np::mean($sample_losses,$axis = -1);


        return $sample_losses;
    }

    public function backward($dvalues, $y_true) {
        // Number of samples
        $samples = count($dvalues);
        // Number of outputs in every sample
        $outputs = count($dvalues[0]);

        // Clip data to prevent division by 0
        $clipped_dvalues = np::clip($dvalues, 1e-7, 1 - 1e-7);

        // Calculate gradient
        $positive_gradient = np::divide(
                                $y_true, 
                                $clipped_dvalues
                            );

        $negative_gradient = np::divide(
                                np::subtract(
                                    1,
                                    $y_true
                                ), 
                                np::subtract(
                                    1, 
                                    $clipped_dvalues
                                )
                            );
        
        $temp = np::multiply(
                            -1,
                            np::subtract(
                                $positive_gradient,
                                $negative_gradient
                            )
                        );

        $this->dinputs = np::divide($temp, $outputs);
        
        // Normalize gradient
        $this->dinputs = np::divide($this->dinputs, $samples);
    }
}



class Loss_MeanSquaredError extends Loss {

    public $dinputs;

    // Forward pass
    public function forward($y_pred, $y_true) {
        // Calculate loss
        $sample_losses = np::mean(
            np::pow(
                    np::subtract($y_true, 
                                $y_pred)
                , 2
            )
            , $axis = -1);

        // Return losses
        return $sample_losses;
    }

    // Backward pass
    public function backward($dvalues, $y_true) {
        // Number of samples
        $samples = count($dvalues);

        // Number of outputs in every sample
        $outputs = count($dvalues[0]);

        // Gradient on values
        $this->dinputs = np::divide( 
                np::subtract($y_true, $dvalues), 
            $outputs);

        $this->dinputs = np::multiply(-2,$this->dinputs);

        // Normalize gradient
        $this->dinputs = np::divide($this->dinputs, $samples);
    }
}



class Loss_MeanAbsoluteError extends Loss {

    public $dinputs;

    // Forward pass
    public function forward($y_pred, $y_true) {
        // Calculate loss
        $sample_losses = np::mean(
            np::abs(
                np::subtract($y_true, $y_pred)
            ), 
            $axis = -1);

        // Return losses
        return $sample_losses;
    }

    // Backward pass
    public function backward($dvalues, $y_true) {
        // Number of samples
        $samples = count($dvalues);

        // Number of outputs in every sample
        $outputs = count($dvalues[0]);

        // Calculate gradient
        $this->dinputs = np::divide(
            np::sign(
                np::subtract(
                    $y_true, 
                    $dvalues
                )
            ), 
            $outputs
        );

        // Normalize gradient
        $this->dinputs = np::divide($this->dinputs, $samples);
        
    }
}




?>
