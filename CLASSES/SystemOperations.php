<?php

class SystemOperations {

    // Static method to initialize the operations
    public static function init() {
        echo "  // //  N   N EEEEE U   U RRRR   OOO  N   N\n";
        echo "  // //  NN  N E     U   U R   R O   O NN  N\n";
        echo "  // //  N N N EEEE  U   U RRRR  O   O N N N\n";
        echo "  // //  N  NN E     U   U R  R  O   O N  NN\n";
        echo "  // //  N   N EEEEE  UUU  R   R  OOO  N   N\n";
        echo "\n🚀 Neural Network Acceleration Setup\n";
        echo "Attempting to implement system operations for enabling faster neural network processing...\n";

        if (self::isCppSupported()) {
            self::performMakeOperations();
        } else {
            echo "Warning: C++ is not supported on this system.\n";
        }
    }

    public static function executeAndFetchJson($executable, $urlParam, $outputJson) {
        // Execute the executable and capture the output
        $output = [];
        exec("$executable $urlParam $outputJson", $output);

        // Check the result of the executable
        $result = trim($output[0] ?? '');

        if ($result === 'Successful') {
            return true;
        } else {
            return false;
        }
    }


    // Private method to check if C++ is supported
    private static function isCppSupported() {
        $output = [];
        $returnValue = 0;
        exec('g++ --version', $output, $returnValue);
        return $returnValue === 0; // Returns true if g++ is found
    }

    // Private method to perform make operations
    private static function performMakeOperations() {
    $classesDir = __DIR__; // Get the current directory of the script
    echo "📂 Changing to $classesDir directory...\n";
    if (!chdir($classesDir)) {
        echo "❌ Failed to change directory to $classesDir.\n";
        return;
    }

    echo "🧹 Executing 'make clean'...\n";
    exec('make clean', $outputClean, $returnClean);
    if ($returnClean === 0) {
        echo "✅ 'make clean' completed successfully.\n";
    } else {
        echo "❌ Error in 'make clean' operation.\n";
    }

    echo "🔨 Executing 'make'...\n";
    exec('make', $outputMake, $returnMake);
    if ($returnMake === 0) {
        echo "✅ 'make' completed successfully.\n";
    } else {
        echo "❌ Error in 'make' operation.\n";
    }
}

}

// Using the class
// SystemOperations::init();

?>
