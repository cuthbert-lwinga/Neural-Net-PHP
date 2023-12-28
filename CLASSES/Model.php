<?PHP
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight;

class Model {
    private $layers = [];
    private $trainable_layers = [];
    private $loss;
    private $optimizer;
    private $inputLayer;
    private $output_layer_activation;
    private $accuracy;
    private $softmax_classifier_output = null;


    public function __construct() {
        $this->layers = array();
        $this->trainable_layers = array();
    }

    public function add($layer) {
        $this->layers[] = $layer;
    }

    public function set($loss, $optimizer, $accuracy) {
        $this->loss = $loss;
        $this->optimizer = $optimizer;
        $this->accuracy = $accuracy;
    }

public function finalize() {
    $this->inputLayer = new Layer_Input();
    $layerCount = count($this->layers);

    for ($i = 0; $i < $layerCount; $i++) {
        if ($i == 0) {
            $this->layers[$i]->prev = $this->inputLayer;
            $this->layers[$i]->next = $this->layers[$i + 1];
        } elseif ($i < $layerCount - 1) {
            $this->layers[$i]->prev = $this->layers[$i - 1];
            $this->layers[$i]->next = $this->layers[$i + 1];
        } else {
            $this->layers[$i]->prev = $this->layers[$i - 1];
            $this->layers[$i]->next = $this->loss;
            $this->output_layer_activation = $this->layers[$i];
        }

        // Check if the layer is trainable (has weights)
        if (property_exists($this->layers[$i], 'weights')) {
            array_push($this->trainable_layers, $this->layers[$i]);
        }

    }

    // Check if the last layer is Softmax and loss is Categorical Cross-Entropy
    $lastLayer = end($this->layers);
    if ($lastLayer instanceof Activation_Softmax && $this->loss instanceof Loss_CategoricalCrossentropy) {
        $this->softmax_classifier_output = new Activation_Softmax_Loss_CategoricalCrossentropy();
    }

}


public function train($X, $y, $epochs = 1, $printEvery = 1,$validation_data = null) {
    // Initialize accuracy object
    $this->accuracy->init($y);

    // Main training loop
    for ($epoch = 1; $epoch <= $epochs; $epoch++) {
        
        // Perform the forward pass
        $output = $this->forward($X,true);

        // Calculate loss
        list($data_loss, $regularization_loss) = $this->loss->calculate($output, $y,$include_regularization = true);
        $loss = $data_loss + $regularization_loss;

        // Get predictions and calculate an accuracy
        $predictions = $this->output_layer_activation->predictions($output);
        $accuracy = $this->accuracy->calculate($predictions, $y,true);


        // Perform backward pass
        $this->backward($output, $y);



        // Optimize (update parameters)
        $this->optimizer->pre_update_params();
        foreach ($this->trainable_layers as $layer) {
            $this->optimizer->update_params($layer);
        }
        $this->optimizer->post_update_params();

        // Print a summary
        if ($epoch % $printEvery === 0) {
            echo "epoch: {$epoch}, ";
            echo "acc: " . number_format($accuracy, 3) . ", ";
            echo "loss: " . number_format($loss, 3) . " (";
            echo "data_loss: " . number_format($data_loss, 3) . ", ";
            echo "reg_loss: " . number_format($regularization_loss, 3) . "), ";
            echo "lr: " . $this->optimizer->current_learning_rate . "\n";
        }
        // die("\n\n**********dead**********\n\n");

    }

        // If there is the validation data
        if ($validation_data !== null) {
 
            list($X_validation, $y_validation) = $validation_data;

            if (!NumpyLight::areShapesEqual($y, $y_validation)){
                $y_validation = NumpyLight::reshape($y_validation,NumpyLight::shape($y));
            }
            // Perform the forward pass on validation data
            $output_val = $this->forward($X_validation,false);
            // Calculate the loss on validation data
            $loss_val = $this->loss->calculate($output_val, $y_validation);
            // Get predictions and calculate an accuracy on validation data
            $predictions_val = $this->output_layer_activation->predictions($output_val);
            $accuracy_val = $this->accuracy->calculate($predictions_val, $y_validation);

            // Print a summary for validation data
            echo "\n\n\n\n";
            echo "validation, ";
            echo "acc: " . number_format($accuracy_val, 3) . ", ";
            echo "loss: " . number_format($loss_val, 3) . "\n";
        }



}


    private function forward($X, $training) {

    // Call forward method on the input layer with the training flag
    
    $this->inputLayer->forward($X, $training);
    
        foreach ($this->layers as $layer) {

            $layer->forward($layer->prev->output);
        }

        return end($this->layers)->output;
    }


    public function backward($output, $y) {


        if ($this->softmax_classifier_output !== null) {
            $this->softmax_classifier_output->backward($output, $y);
            $this->layers[count($this->layers) - 1]->next->dinputs = $this->softmax_classifier_output->dinputs; # if you are rading from the book this is a bit rticky. i use next because in python [:-1] takes last but when we inint in php we order them in previous next to mimic the data structure of python. Happy coding !!

            // Reverse the layers array for backward pass

            $reversed_layers = array_reverse($this->layers);
            
            // Iterate over the layers in reverse order
            
            foreach ($reversed_layers as $layer) {
            // echo "\n\n --Start-- \n\n";
            // echo get_class($layer)."\n\n";
            // echo get_class($layer->next)."\n\n----------------\n";
            $layer->backward($layer->next->dinputs);
            // echo "\n\n --End-- \n\n";

                
            }

            return;
        }

        // Call backward method on the loss
        // This will set dinputs property that the last layer will try to access shortly
        $this->loss->backward($output, $y);

        // Reverse the layers array for backward pass
        $reversed_layers = array_reverse($this->layers);
        // Iterate over the layers in reverse order
        foreach ($reversed_layers as $layer) {
            $layer->backward($layer->next->dinputs);
            
        }
}



}




?>
