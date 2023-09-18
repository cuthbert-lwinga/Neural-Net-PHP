<?php
class MyClass {
    private $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function __add($other) {
        // Check if $other is an instance of MyClass
        if ($other instanceof MyClass) {
            // Define custom addition behavior here
            return new MyClass($this->value + $other->getValue());
        } else {
            // Handle the case when $other is not an instance of MyClass
            throw new InvalidArgumentException("Unsupported operand");
        }
    }
}

$a = new MyClass(5);
$b = new MyClass(10);

$result = $a + $b; // This will call $a->__add($b)

echo $result->getValue(); // Outputs 15

?>
