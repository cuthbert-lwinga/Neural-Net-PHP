<?PHP
include_once("../CLASSES/headers.php");

class Activation_ReLU_Test {
    public function runTests() {
       

        // Test case 1: Single array input
        $input1 = [[2.5, -1.8, 3.2]];
       	$relu = new Activation_ReLU($input1);
        $relu->forward();
        $expectedOutput1 = [[2.5, 0, 3.2]];
        echo "TES CASE 1: ".$this->assertEqual($relu->output, $expectedOutput1)."\n";

        // Test case 2: Multiple array inputs
        $input2 = [1.2, -3.4, 0];
        $input3 = [5.6, -2.1, 4.3];
        $relu = new Activation_ReLU([$input2, $input3]);
        $relu->forward();
        $expectedOutput2 = [[1.2, 0.0, 0.0], [5.6, 0.0, 4.3]];
        echo "TES CASE 2: ".$this->assertEqual($relu->output, $expectedOutput2)." <Fails because of floating point>\n";

        // // Test case 3: Empty input
        $input4 = [];
        $relu = new Activation_ReLU($input4);
        $relu->forward();
        $expectedOutput3 = [];
        echo "TES CASE 3: ".$this->assertEqual($relu->output, $expectedOutput3)."\n";

        // // Add more test cases as needed...

        // echo "All tests passed!\n";
    }

    private function assertEqual($actual, $expected) {
        if ($actual === $expected) {
            return "<< Pass >> ";
        } else {
            return "<< Fail >>";
        }
    }
}

$test = new Activation_ReLU_Test();
$test->runTests();

echo "\n";
?>