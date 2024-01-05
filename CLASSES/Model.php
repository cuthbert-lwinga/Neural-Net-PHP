<?PHP
include_once("Headers.php");
use NameSpaceNumpyLight\NumpyLight;

class Model {
    public $layers = [];
    public $trainable_layers = [];
    public $loss;
    public $optimizer;
    public $inputLayer;
    public $output_layer_activation;
    public $accuracy;
    public $softmax_classifier_output = null;


    public function __construct() {
        $this->layers = array();
        $this->trainable_layers = array();
    }

    public function add($layer) {
        $this->layers[] = $layer;
    }

    public function set($loss = null, $optimizer = null, $accuracy = null) {
            if ($loss !== null) {
                $this->loss = $loss;
            }
            if ($optimizer !== null) {
                $this->optimizer = $optimizer;
            }
            if ($accuracy !== null) {
                $this->accuracy = $accuracy;
            }
        }


 public function set_parameters($parameters) {
        foreach ($parameters as $index => $parameter_set) {
            if (isset($this->trainable_layers[$index])) {
                $this->trainable_layers[$index]->set_parameters($parameter_set[0], $parameter_set[1]);
            }
        }
    }

    public function get_parameters(){
        $parameters = [];
        foreach ($this->trainable_layers as $layer){
            $parameters[] = $layer->get_parameters();
        }
        return $parameters;
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
        
        if ($this->loss !== null){
            // Check if the layer is trainable (has weights)
            if (property_exists($this->layers[$i], 'weights')) {
                array_push($this->trainable_layers, $this->layers[$i]);
            }
        }

        $this->loss->remember_trainable_layers($this->trainable_layers);


    }

    // Check if the last layer is Softmax and loss is Categorical Cross-Entropy
    $lastLayer = end($this->layers);
    if ($lastLayer instanceof Activation_Softmax && $this->loss instanceof Loss_CategoricalCrossentropy) {
        $this->softmax_classifier_output = new Activation_Softmax_Loss_CategoricalCrossentropy();
    }

}

public function evaluate($X_val, $y_val, $batch_size = null) {
        // Default value if batch size is not set
        $validation_steps = 1;

        // Calculate number of steps
        if ($batch_size !== null) {
            $validation_steps = intdiv(count($X_val), $batch_size);
            // Add 1 to include the not full minibatch
            if ($validation_steps * $batch_size < count($X_val)) {
                $validation_steps += 1;
            }
        }

        // Reset accumulated values in loss and accuracy objects
            $this->loss->new_pass();
            $this->accuracy->new_pass();

            // Iterate over validation steps
            for ($step = 0; $step < $validation_steps; $step++) {
                // If batch size is not set, validate using one step and full dataset
                if ($batch_size === null) {
                    $batch_X = $X_val;
                    $batch_y = $y_val;
                } else {
                    // Otherwise slice a batch
                    $batch_X = array_slice($X_val, $step * $batch_size, $batch_size);
                    $batch_y = array_slice($y_val, $step * $batch_size, $batch_size);
                }

                // Perform the forward pass
                $output = $this->forward($batch_X, false);

                // Calculate the loss
                $this->loss->calculate($output, $batch_y);

                // Get predictions and calculate an accuracy
                $predictions = $this->output_layer_activation->predictions($output);
                $this->accuracy->calculate($predictions, $batch_y);
            }

            // Get and print validation loss and accuracy
            $validation_loss = $this->loss->calculate_accumulated();
            $validation_accuracy = $this->accuracy->calculate_accumulated();
            echo "validation, ";
            echo "acc: " . number_format($validation_accuracy, 3) . ", ";
            echo "loss: " . number_format($validation_loss, 3) . "\n";
    }


public function train($X, $y, $epochs = 1, $batch_size = null, $print_every = 1, $validation_data = null) {
    // Initialize accuracy object
    $this->accuracy->init($y);

    // Default value if batch size is not set
    $train_steps = 1;

    // Calculate number of steps
    if ($batch_size !== null) {
        $train_steps = intval(count($X) / $batch_size);
        // Add 1 to include the not full batch
        if ($train_steps * $batch_size < count($X)) {
            $train_steps++;
        }

    }

    // Main training loop
    for ($epoch = 1; $epoch <= $epochs; $epoch++) {
        
        echo "epoch: {$epoch}\n";
        // Reset accumulated values in loss and accuracy objects
        $this->loss->new_pass();
        $this->accuracy->new_pass();

        // Iterate over steps
        for ($step = 0; $step < $train_steps; $step++) {
            // If batch size is not set, train using one step and full dataset
            if ($batch_size === null) {
                $batch_X = $X;
                $batch_y = $y;
            } else {
                // Otherwise slice a batch
                $batch_X = array_slice($X, $step * $batch_size, $batch_size);
                $batch_y = array_slice($y, $step * $batch_size, $batch_size);
            }

            // Perform the forward pass
            $output = $this->forward($batch_X, true);

            // Calculate loss
            list($data_loss, $regularization_loss) = $this->loss->calculate($output, $batch_y, $include_regularization = true);
            $loss = $data_loss + $regularization_loss;

            // Get predictions and calculate an accuracy
            $predictions = $this->output_layer_activation->predictions($output);
            $accuracy = $this->accuracy->calculate($predictions, $batch_y);

            // Perform backward pass
            $this->backward($output, $batch_y);

            // Optimize (update parameters)
            $this->optimizer->pre_update_params();
            foreach ($this->trainable_layers as $layer) {
                $this->optimizer->update_params($layer);
            }
            $this->optimizer->post_update_params();

            // Print a summary
            if ($step % $print_every === 0 || $step == $train_steps - 1) {
                echo "step: {$step}, ";
                echo "acc: " . number_format($accuracy, 3) . ", ";
                echo "loss: " . number_format($loss, 3) . " (";
                echo "data_loss: " . number_format($data_loss, 3) . ", ";
                echo "reg_loss: " . number_format($regularization_loss, 3) . "), ";
                echo "lr: " . $this->optimizer->current_learning_rate . "\n";
            }
        }

        // Print epoch loss and accuracy
        list($epoch_data_loss, $epoch_regularization_loss) = $this->loss->calculate_accumulated($include_regularization = True );
        $epoch_loss = $epoch_data_loss + $epoch_regularization_loss;
        $epoch_accuracy = $this->accuracy->calculate_accumulated();
         echo "training, ";
                echo "acc: " . number_format($epoch_accuracy, 3) . ", ";
                echo "loss: " . number_format($epoch_loss, 3) . " (";
                echo "data_loss: " . number_format($epoch_data_loss, 3) . ", ";
                echo "reg_loss: " . number_format($epoch_regularization_loss, 3) . "), ";
                echo "lr: " . $this->optimizer->current_learning_rate . "\n";


        // Validation logic, if validation data is available
        if ($validation_data !== null) {
            
            list($X_val, $y_val) = $validation_data;
            $this->evaluate($X_val, $y_val, $batch_size);

        }
    }
}



public function trainOld($X, $y, $epochs = 1, $batch_size = NULL, $printEvery = 1,$validation_data = null) {
    // Initialize accuracy object
    $this->accuracy->init($y);

    # Default value if batch size is not set
    $train_steps = 1;

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
            $validation_steps = 1;
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


public function predict($X, $batch_size = null) {
        $prediction_steps = 1;

        // Calculate number of steps
        if ($batch_size !== null) {
            $prediction_steps = intdiv(count($X), $batch_size);
            // Add 1 to include the not full batch
            if ($prediction_steps * $batch_size < count($X)) {
                $prediction_steps += 1;
            }
        }

        // Model outputs
        $output = [];

        // Iterate over steps
        for ($step = 0; $step < $prediction_steps; $step++) {
            // If batch size is not set, use one step and full dataset
            if ($batch_size === null) {
                $batch_X = $X;
            } else {
                // Otherwise slice a batch
                $batch_X = array_slice($X, $step * $batch_size, $batch_size);
            }

            // Perform the forward pass
            $batch_output = $this->forward($batch_X, false);

            // Append batch prediction to the list of predictions
            $output[] = $batch_output;
        }

        // Combine all batch outputs for final result
        // Assuming each $batch_output is an array of predictions
        return array_merge(...$output);
    }


public function save_parameters($path = null, $override = true) {
        if ($path === null) {
            $path = 'model_parameters.txt'; // Default filename
        }

        // Check if file exists and if override is false
        if (!$override && file_exists($path)) {
            $path_info = pathinfo($path);
            $unique_id = uniqid(); // Generate a unique ID
            $path = $path_info['dirname'] . DIRECTORY_SEPARATOR .
                    $path_info['filename'] . '_' . $unique_id .
                    '.' . $path_info['extension'];
        }

        // Serialize the parameters
        $serialized_parameters = serialize($this->get_parameters());

        // Save to file
        file_put_contents($path, $serialized_parameters);
    }

    public function load_parameters($path) {
        if (!file_exists($path)) {
            throw new Exception("File not found: " . $path);
        }

        // Load and unserialize the parameters
        $serialized_parameters = file_get_contents($path);
        $parameters = unserialize($serialized_parameters);

        // Update the model with these parameters
        $this->set_parameters($parameters);
    }

public function save($path = null) {
        // Set default file name if path is null
        if ($path === null) {
            $path = 'saved_model';
        }

        // Serialize and unserialize the model to create a deep copy
        $model_copy = unserialize(serialize($this));

        // Reset accumulated values in loss and accuracy objects
        if (isset($model_copy->loss)) {
            $model_copy->loss->new_pass();
        }

        if (isset($model_copy->accuracy)) {
            $model_copy->accuracy->new_pass();
        }

        // Remove data from the input layer and gradients from the loss object
        unset($model_copy->input_layer->output);
        unset($model_copy->loss->dinputs);

        // For each layer, remove certain properties
        foreach ($model_copy->layers as $layer) {
            foreach (['inputs', 'output', 'dinputs', 'dweights', 'dbiases'] as $property) {
                unset($layer->$property);
            }
        }

        // Serialize the model copy and save to a file
        file_put_contents($path, serialize($model_copy));
    }


    public static function load($path) {
        if (!file_exists($path)) {
            throw new Exception("File not found: " . $path);
        }

        // Read the serialized model from the file
        $serializedModel = file_get_contents($path);

        // Unserialize the model
        $model = unserialize($serializedModel);

        // Return the model object
        return $model;
    }




}




?>
