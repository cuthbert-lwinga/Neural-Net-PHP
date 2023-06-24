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

public static function compare($expected, $input, $comparison = null) {
    $marginal_error = 0.5;

    if (is_array($expected) && is_array($input)) {
        if (count($expected) !== count($input)) {
            echo "[FAILED] Arrays have different lengths\n";
            return;
        }

        foreach ($expected as $key => $value) {
            if (is_numeric($value) && is_numeric($input[$key])) {
                $rounded_value = round($value);
                $rounded_input = round($input[$key]);

                if ($comparison === '>') {
                    if (!($rounded_input > $rounded_value - $marginal_error)) {
                        echo "[FAILED] Values at index $key are not greater than: expected $rounded_value, got $rounded_input\n";
                        return;
                    }
                } elseif ($comparison === '<') {
                    if (!($rounded_input < $rounded_value + $marginal_error)) {
                        echo "[FAILED] Values at index $key are not less than: expected $rounded_value, got $rounded_input\n";
                        return;
                    }
                } else {
                    if (abs($rounded_value - $rounded_input) > $marginal_error) {
                        echo "[FAILED] Values at index $key are not similar: expected $rounded_value, got $rounded_input\n";
                        return;
                    }
                }
            } elseif ($value !== $input[$key]) {
                echo "[FAILED] Values at index $key are not similar: expected " . print_r($value, true) . ", got " . print_r($input[$key], true) . "\n";
                return;
            }
        }

        echo "[SUCCESS] Arrays are similar\n";
    } else {
        if (is_numeric($expected) && is_numeric($input)) {
            $rounded_value = round($expected);
            $rounded_input = round($input);

            if ($comparison === '>') {
                if (!($rounded_input > $rounded_value - $marginal_error)) {
                    echo "[FAILED] Value is not greater than: expected $rounded_value, got $rounded_input\n";
                    return;
                }
            } elseif ($comparison === '<') {
                if (!($rounded_input < $rounded_value + $marginal_error)) {
                    echo "[FAILED] Value is not less than: expected $rounded_value, got $rounded_input\n";
                    return;
                }
            } else {
                if (abs($rounded_value - $rounded_input) > $marginal_error) {
                    echo "[FAILED] Values are not similar: expected $rounded_value, got $rounded_input\n";
                    return;
                }
            }
        } elseif ($expected !== $input) {
            echo "[FAILED] Values are not similar: expected " . print_r($expected, true) . ", got " . print_r($input, true) . "\n";
            return;
        }

        echo "[SUCCESS] Values are similar\n";
    }
}



}

?>