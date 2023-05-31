<?PHP

class TestOperations{
	    public static function reluCheck($input) {
        foreach ($input as $array) {
            foreach ($array as $value) {
                if ($value < 0) {
                    return false;
                }
            }
        }
        return true;
    }

    private static function assertEqual($actual, $expected) {
        if ($actual === $expected) {
            return "<< Pass >> ";
        } else {
            return "<< Fail >>";
        }
    }


}

?>