<?PHP
namespace NameSpaceNumpyLight;

include_once("Headers.php");

use NameSpaceRandomGenerator\RandomGenerator;

class NumpyLight{

  // //  M    M   A   TTTTT  RRRRR   IIIII   X   X
  // //  MM  MM  A A    T    R    R    I      X X 
  // //  M MM M AAAAA   T    RRRRR     I       X  
  // //  M    M A   A   T    RR        I      X X 
  // //  M    M A   A   T    R   R   IIIII   X   X  

  // bellow you will find different matrix operations, I MADE THEM EQUIVALENT TO NUMPY  

  //echo PHP_FLOAT_MAX;

  public static function random(){
    return new RandomGenerator();
}



private static function zerosArray($dimensions) {
    if (count($dimensions) === 0) {
        return 0.0;
    }

    $dim = array_shift($dimensions);
    $result = [];

    for ($i = 0; $i < $dim; $i++) {
        $result[] = self::zerosArray($dimensions);
    }

    return $result;
}

public static function zeros(...$dimensions) {
    return self::zerosArray($dimensions);
}

public static function zeros_like($matrix) {
    if (is_array($matrix)) {
        $result = [];
        foreach ($matrix as $row) {
            $result[] = self::zeros_like($row);
        }
        return $result;
    } else {
        return 0.0;
    }
}


public static function shape($array) {
    $shape = [];
    $current_array = $array;
    while (is_array($current_array)) {
        $shape[] = count($current_array);
        $current_array = $current_array[0];
    }
    return $shape;
}

public static function dot($a, $b) {
    $shapeA = self::shape($a);
    $shapeB = self::shape($b);

        // Check for shape compatibility

    // echo json_encode(self::shape($a))." = ".json_encode(self::shape($b))." \n";

    if (end($shapeA) !== $shapeB[0]) {
        throw new Exception("Shapes " . implode(",", $shapeA) . " and " . implode(",", $shapeB) . " not aligned.");
    }

    $result = [];
    for ($i = 0; $i < $shapeA[0]; $i++) {
        $row = [];
        for ($j = 0; $j < $shapeB[1]; $j++) {
            $sum = 0;
            for ($k = 0; $k < $shapeA[1]; $k++) {
                $sum += $a[$i][$k] * $b[$k][$j];
            }
            $row[] = $sum;
        }
        $result[] = $row;
    }
    return $result;
}

public static function argmax($matrix){
    $return = array();
    $matrix_count = count($matrix);
    for ($i = 0; $i < $matrix_count; $i++) {
        $row = $matrix[$i];
        $return[] = array_search(max($row),$row);
    } 
    return $return;   
}

public static function accuracy($matrix,$true_values){
    $correct = 0;
    $matrix_count = count($matrix);
    $prediction = self::argmax($matrix);
    for ($i = 0; $i < $matrix_count; $i++) {
        if ($prediction[$i]==$true_values[$i]) {
            $correct++;
        }
    } 

    return $correct/$matrix_count;   
}

public static function mean($a, $axis = NULL, $dtype = "float64", $keepdims = False) {
    if ($axis === NULL) {
        // Compute the mean of the flattened array
        $flattened_array = self::flatten($a);
        $sum = array_sum($flattened_array);
        $mean = $sum / count($flattened_array);
        return $mean;
    } elseif ($axis === 0) {
        // Compute the mean along axis=0 ("columns")
        $transpose_array = self::transpose($a);
        $axis_means = array();
        foreach ($transpose_array as $sub_array) {
            $sum = array_sum($sub_array);
            $mean = $sum / count($sub_array);
            array_push($axis_means, $mean);
        }
        return $keepdims ? array($axis_means) : $axis_means;
    } elseif ($axis === 1) {
        // Compute the mean along axis=1 ("rows")
        $axis_means = array();
        foreach ($a as $sub_array) {
            $sum = array_sum($sub_array);
            $mean = $sum / count($sub_array);
            array_push($axis_means, $mean);
        }
        return $keepdims ? array($axis_means) : $axis_means;
    }
}

public static function clip($a, $a_min, $a_max) {
    // Base case: if $a is not an array, apply clipping and return
    if (!is_array($a)) {
        if ($a_min > $a_max) {
            return $a_max;
        }
        return min(max($a, $a_min), $a_max);
    }

    // Initialize an empty array to hold the clipped values
    $clipped_array = array();

    // Loop through each element in the array $a
    foreach ($a as $key => $value) {
        // If $value is an array, call the clip function recursively
        if (is_array($value)) {
            $clipped_array[$key] = self::clip($value, $a_min, $a_max);
        } else {
            $min_value = is_array($a_min) ? $a_min[$key] : $a_min;
            $max_value = is_array($a_max) ? $a_max[$key] : $a_max;

            // If a_min > a_max, replace all values with a_max
            if ($min_value > $max_value) {
                $clipped_array[$key] = $max_value;
                continue;
            }

            // Clip the value and add it to the clipped_array
            $clipped_value = max($min_value, min($value, $max_value));
            $clipped_array[$key] = $clipped_value;
        }
    }

    // Return the clipped array
    return $clipped_array;
}


public static function one_hot_encode($matrix, $y_true) {
    // Flatten the matrix and get the unique labels
    $flat_matrix = array_merge(...$matrix);
    $unique_labels = array_unique($flat_matrix);
    $labels = count($unique_labels);
    
    // Initialize the resulting array
    $result = [];

    // Loop through each element in y_true
    foreach ($y_true as $value) {
        // Initialize a new sub-array filled with zeros
        $row = array_fill(0, $labels, 0);
        
        // Only set the index if it's a valid label
        if (in_array($value, $unique_labels)) {
            $row[$value] = 1;
        }

        // Append the one-hot encoded row to the result
        $result[] = $row;
    }

    return $result;
}

public static function modifyOneHotEncoded($dinputs, $y_true, $valueToSubtract = -1) {
    $samples = count($dinputs);
    $modified_dinputs = $dinputs; // Copy array so we can safely modify
    
    for ($i = 0; $i < $samples; $i++) {
        $label = $y_true[$i];
        $modified_dinputs[$i][$label] += $valueToSubtract;  // Added valueToSubtract here
    }
    
    return $modified_dinputs;
}

public static function get_values_from_indexes($array, $indexes) {
    $result = [];
    foreach ($array as $i => $subarray) {
        if (isset($indexes[$i])) {
            $index = $indexes[$i];
            if (isset($subarray[$index])) {
                $result[] = $subarray[$index];
            } else {
                // Handle the case where the index does not exist in the subarray
                $result[] = null;
            }
        } else {
            // Handle the case where the index does not exist in the indexes array
            $result[] = null;
        }
    }
    return $result;
}


public static function select_rows_by_indices($matrix, $indices) {
    $result = [];
    foreach ($indices as $index) {
        if (isset($matrix[$index])) {
            $result[] = $matrix[$index];
        } else {
            // Handle the case where the index does not exist in the matrix
            $result[] = null;
        }
    }
    return $result;
}

    public static function eye($N, $M = null, $k = 0) {
        if ($M === null) {
            $M = $N;
        }

        $result = [];

        for ($i = 0; $i < $N; $i++) {
            $row = [];

            for ($j = 0; $j < $M; $j++) {
                $row[] = ($j == $i + $k) ? 1 : 0;
            }

            $result[] = $row;
        }

        return $result;
    }


public static function log($array) {
    $result = [];
    foreach ($array as $value) {
        if ($value < 0) {
            $result[] = null;  // Logarithm of negative numbers is undefined
        } elseif ($value === 0) {
            $result[] = -INF;  // Logarithm of zero is negative infinity
        } else {
            $result[] = log($value);  // Built-in PHP log function for natural logarithm
        }
    }
    return $result;
}

public static function flatten($array) {
    $result = array();
    foreach ($array as $sub_array) {
        if (is_array($sub_array)) {
            $result = array_merge($result, self::flatten($sub_array));
        } else {
            $result[] = $sub_array;
        }
    }
    return $result;
}


public static function sum($array, $axis=null, $keepdims=false) {
    if ($axis === null) {
        // Sum all elements
        $sum = 0;
        array_walk_recursive($array, function($val) use (&$sum) {
            $sum += $val;
        });

        // Handle keepdims
        if ($keepdims) {
            $dims = self::shape($array);
            $result = $sum;
            foreach ($dims as $dim) {
                $result = [$result];
            }
            return $result;
        } else {
            return $sum;
        }
    } else {
        // Sum along a specific axis
        if ($axis == 0) {
            // Initialize sum array with zeros
            $sums = array_fill(0, count($array[0]), 0);
            
            foreach ($array as $sub_array) {
                foreach ($sub_array as $index => $value) {
                    $sums[$index] += $value;
                }
            }
            
            return $keepdims ? [$sums] : $sums;
        } else {
         return self::sumAxisGreater($array, $axis, $keepdims);
     }
 }
}

public static function sumAxisGreater($array, $axis, $keepdims) {
    $result = [];
    foreach ($array as $sub_array) {
        $sum = 0;
        if (is_array($sub_array)) {
            array_walk_recursive($sub_array, function($val) use (&$sum) {
                $sum += $val;
            });
        } else {
            $sum = $sub_array;
        }

        $result[] = $keepdims ? [$sum] : $sum;
    }
    return $result;
}



public static function maximum_($array1, $array2) {
    if (!is_array($array1) && !is_array($array2)) {
        return max($array1, $array2);
    }

    // Make sure both are arrays for easier processing
    if (!is_array($array1)) {
        $array1 = array_fill(0, count($array2), $array1);
    }

    if (!is_array($array2)) {
        $array2 = array_fill(0, count($array1), $array2);
    }
    
    // If the arrays are of different dimensions, fill the smaller one with its last element
    if (count($array1) !== count($array2)) {
        $size = max(count($array1), count($array2));
        while (count($array1) < $size) {
            array_push($array1, end($array1));
        }
        while (count($array2) < $size) {
            array_push($array2, end($array2));
        }
    }

    $result = [];
    foreach ($array1 as $key => $value1) {
        $value2 = $array2[$key];
        if (is_array($value1) || is_array($value2)) {
            $result[] = self::maximum($value1, $value2);
        } else {
            $result[] = max($value1, $value2);
        }
    }
    
    return $result;
}

public static function maximum($array1, $array2) {



    // If both are scalars, return the maximum.
    if (!is_array($array1) && !is_array($array2)) {
        return max($array1, $array2);
    }

    // Get the shapes of both arrays.
    $array1Shape = NumpyLight::shape($array1);
    $array2Shape = NumpyLight::shape($array2);



    // Check if the shapes are the same.
    if ($array1Shape == $array2Shape) {
        return self::elementWiseMax($array1, $array2);
    } else {
        // Check if the arrays are broadcast-compatible
        if (self::isBroadcastable($array1Shape, $array2Shape)) {
            // Identify the smaller array and expand its dimensions
            if (self::isShape1Bigger($array1Shape, $array2Shape)) {
                $array2 = self::expandDims($array2, $array2Shape, $array1Shape);
            } else {
                $array1 = self::expandDims($array1, $array1Shape, $array2Shape);
            }

            
            return self::elementWiseMax($array1, $array2);
        } else {
            throw new Exception("Arrays are not broadcast-compatible.");
        }
    }
}


public static function elementWiseMax($array1, $array2) {
    $result = [];
    foreach ($array1 as $key => $value1) {
        $value2 = $array2[$key];
        
        // If both are arrays, recurse.
        if (is_array($value1) && is_array($value2)) {
            $result[$key] = self::elementWiseMax($value1, $value2);
        } else {
            // Otherwise, take the max of both values.



// Check for NaN or null and handle them
            if (is_null($value1) || is_null($value2)) {
                $result[$key] = null;
            } elseif ($value1 === null || $value2 === null) {
                $result[$key] = null;
            } else {
                $result[$key] = max($value1, $value2);
            }

            //$result[$key] = max($value1, $value2);
        }
    }
    return $result;
}

public static function isBroadcastable($shape1, $shape2) {
    // Reverse the shapes to compare them from right to left.
    $reversedShape1 = array_reverse($shape1);
    $reversedShape2 = array_reverse($shape2);

    // Get the length of each shape array.
    $len1 = count($reversedShape1);
    $len2 = count($reversedShape2);

    // Get the maximum length to iterate over.
    $maxLen = max($len1, $len2);

    for ($i = 0; $i < $maxLen; $i++) {
        $dim1 = $reversedShape1[$i] ?? 1;  // Default to 1 if the dimension doesn't exist.
        $dim2 = $reversedShape2[$i] ?? 1;  // Default to 1 if the dimension doesn't exist.

        // If the dimensions are not the same and neither is 1, they are not broadcastable.
        if ($dim1 != $dim2 && $dim1 != 1 && $dim2 != 1) {
            return false;
        }
    }

    // If we reach this point, the shapes are broadcastable.
    return true;
}

public static function pow($array, $exponent) {
    if (is_array($array)) {
        $result = [];
        foreach ($array as $element) {
            $result[] = self::pow($element, $exponent);
        }
        return $result;
    } else {
        return pow($array, $exponent);
    }
}


public static function isShape1Bigger($shape1, $shape2) {
    // If shape1 has more dimensions than shape2, it's bigger.
    if (count($shape1) > count($shape2)) {
        return true;
    }
    // If shape2 has more dimensions than shape1, it's bigger.
    elseif (count($shape1) < count($shape2)) {
        return false;
    }
    // If both shapes have the same number of dimensions, 
    // we need to check individual dimensions.
    else {
        for ($i = 0; $i < count($shape1); $i++) {
            if ($shape1[$i] > $shape2[$i]) {
                return true;
            } elseif ($shape1[$i] < $shape2[$i]) {
                return false;
            }
            // If the current dimension is the same for both shapes,
            // continue to check the next dimensions.
        }
    }
    // If we reach this point, both shapes are exactly the same,
    // so neither is "bigger."
    return false;
}


public static function canBroadcast($shape1, $shape2) {
    $len1 = count($shape1);
    $len2 = count($shape2);

    // Align shapes on the right by filling the smaller shape with ones on the left
    while ($len1 < $len2) {
        array_unshift($shape1, 1);
        $len1++;
    }
    while ($len2 < $len1) {
        array_unshift($shape2, 1);
        $len2++;
    }

    // Check for compatibility, dimensions are compatible when:
    // 1. they are equal, or
    // 2. one of them is 1
    for ($i = 0; $i < $len1; $i++) {
        if ($shape1[$i] != $shape2[$i] && $shape1[$i] != 1 && $shape2[$i] != 1) {
            return false;
        }
    }
    return true;
}

public static function broadcastToShape($array, $targetShape) {
    $currentShape = self::shape($array);
    $result = $array;
    
    while ($currentShape != $targetShape) {
        $result = self::expandDims($result, $currentShape, $targetShape);
        $currentShape = self::shape($result);
    }
    
    return $result;
}

// [1,2,3,4] 
// [[1,2,3,4]]

public static function expandDims($array, $currentShape, $targetShape) {
    $len1 = count($currentShape); // check len of shape 1
    $len2 = count($targetShape); // check len of shape 2
    $targetRows = $targetShape[0];
    $targetCols = $targetShape[1];
    $currentRows = ($len1 == 1) ? 1 : $currentShape[0];
    $currentCols = ($len1 == 1) ? $currentShape[0] : $currentShape[1];
    $return = [];
    $array = ($len1 == 1 ? [$array] : $array);

    if (($targetCols % $currentCols) == 0) {
        if ($currentRows <= $targetRows) {
            for ($i = 0; $i < $targetRows; $i++) {
                $populatedCols = [];
                if ($len1 == 1) { // Handle 1D array
                    for ($j = 0; $j < count($array[0]); $j++) {
                        $populatedCols[] = $array[0][$j];
                    }
                } else { // Handle 2D array
                    if (is_array($array[$i % $currentRows])) {
                        $temp = [];
                        $duplicate = $targetCols / count($array[$i % $currentRows]);
                        for ($k = 0; $k < $duplicate; $k++) {
                            $temp = array_merge($temp, $array[$i % $currentRows]);
                        }
                        $populatedCols = $temp;
                    } else {
                        for ($j = 0; $j < $targetCols; $j++) {
                            $populatedCols[] = $array[$i % $currentRows];
                        }
                    }
                }
                $return[] = $populatedCols;
            }
        } else {
            throw new \Exception("Flat array can't be expanded on row because shape has fewer elements than rows");
        }
    } else {
        throw new \Exception("Can't broadcast " . json_encode($currentShape) . " to " . json_encode($targetShape));
    }
    return $return;
}


public static function duplicateArray($array,$currentShape, $targetShape){

}

public static function expandDims_($array, $currentShape, $targetShape) {
    $len1 = count($currentShape);
    $len2 = count($targetShape);

    var_dump("Initial array shape: ", $currentShape);

    while ($len1 < $len2) {
        array_unshift($currentShape, 1);
        $len1++;
    }
    while ($len2 < $len1) {
        array_unshift($targetShape, 1);
        $len2++;
    }

    for ($i = $len1 - 1; $i >= 0; $i--) {
        if ($currentShape[$i] === $targetShape[$i]) {
            continue;
        }
        if ($targetShape[$i] === 1) {
            continue;
        }
        if ($currentShape[$i] === 1) {
            var_dump("Before duplicateArray, shape: ", $currentShape);
            $array = self::duplicateArray($array, $targetShape[$i], $i, $len1, $len2);
            $currentShape[$i] = $targetShape[$i];
            var_dump("After duplicateArray, shape: ", $currentShape);
        }
    }

    var_dump("Final array shape: ", $currentShape);

    return $array;
}

public static function duplicateArray_($array, $times, $axis, $currentLen, $targetLen, $currentAxis = 0) {
    if ($currentAxis === $axis) {
        $newArray = [];
        for ($i = 0; $i < $times; $i++) {
            $newArray = array_merge($newArray, $array);
        }
        if ($currentLen < $targetLen) {
            $nestedArray = [];
            $chunkSize = count($newArray) / $times;
            for ($i = 0; $i < count($newArray); $i += $chunkSize) {
                $nestedArray[] = array_slice($newArray, $i, $chunkSize);
            }
            return $nestedArray;
        }
        return $newArray;
    }

    if (is_array($array[0])) {
        $newArray = [];
        foreach ($array as $subArray) {
            $newArray[] = self::duplicateArray($subArray, $times, $axis, $currentLen, $targetLen, $currentAxis + 1);
        }
        return $newArray;
    }

    return $array;
}






public static function ReLU($array)
{
    if (!is_array($array)) {
        throw new Exception("Input should be an array.");
    }

    return array_map(function($x) {
        // If the current item is an array, apply ReLU recursively
        if (is_array($x)) {
            return self::ReLU($x);
        }
        return max(0, $x);
    }, $array);
}


public static function exp($input_array) {
    $result = array();
    foreach ($input_array as $element) {
        if (is_array($element)) {
            $temp = array();
            foreach ($element as $value) {
                $temp[] = exp((float) $value);
            }
            $result[] = $temp;
        } else {
            $result[] = exp((float) $element);
        }
    }
    return $result;
}



public static function add($matrix1, $matrix2)
{
    // Initialize result array
    $result = [];

    // Handle 1D arrays
    if (is_array($matrix1) && !is_array($matrix1[0]) && is_array($matrix2) && !is_array($matrix2[0])) {
        for ($i = 0; $i < count($matrix1); $i++) {
            $result[] = $matrix1[$i] + $matrix2[$i];
        }
        return $result;
    }

    if (is_int($matrix2) || is_float($matrix2)) {
        // If matrix2 is an integer or float, perform scalar addition
        foreach ($matrix1 as $i => $row) {
            if (is_array($row)) {
                $resultRow = [];
                foreach ($row as $j => $value) {
                    $resultRow[] = $value + $matrix2;
                }
                $result[] = $resultRow;
            } else {
                $result[] = $row + $matrix2;
            }
        }
        return $result;
    }

    // Check for broadcasting scenarios
    $broadcastMatrix1 = is_array($matrix1) && count($matrix1) === 1;
    $broadcastMatrix2 = is_array($matrix2) && count($matrix2) === 1;

    $colCount = is_array($matrix1[0]) ? count($matrix1[0]) : 0;

    // Perform addition
    for ($i = 0; $i < max(count($matrix1), count($matrix2)); $i++) {
        $row = [];
        for ($j = 0; $j < $colCount; $j++) {
            $value1 = $broadcastMatrix1 ? $matrix1[0][$j] : $matrix1[$i][$j];
            $value2 = $broadcastMatrix2 ? $matrix2[0][$j] : $matrix2[$i][$j];
            $row[] = $value1 + $value2;
        }
        $result[] = $row;
    }

    return $result;
}




public static function subtract($matrix1, $matrix2)
{
    // Check if matrix shapes are compatible for 2D matrices
    if (count($matrix1[0]) > 1 && count($matrix2[0]) > 1) {
        if (count($matrix1) !== count($matrix2) || count($matrix1[0]) !== count($matrix2[0])) {
            throw new InvalidArgumentException('Matrix shapes are not compatible for subtraction.');
        }
    }
    // Check if matrix2 is a column vector
    elseif (count($matrix2[0]) === 1) {
        if (count($matrix1) !== count($matrix2)) {
            throw new InvalidArgumentException('Matrix shapes are not compatible for subtraction.');
        }
    }

    $result = [];

    // Perform the subtraction
    for ($i = 0; $i < count($matrix1); $i++) {
        $row = [];
        for ($j = 0; $j < count($matrix1[0]); $j++) {
            if (count($matrix2[0]) === 1) {  // If matrix2 is a column vector, broadcast the subtraction
                $row[] = $matrix1[$i][$j] - $matrix2[$i][0];
            } else {  // Otherwise, perform element-wise subtraction
                $row[] = $matrix1[$i][$j] - $matrix2[$i][$j];
            }
        }
        $result[] = $row;
    }

    return $result;
}



public static function divide($array1, $array2) {

    // this may need revision
    // If array2 is a number, perform scalar division
    if (is_numeric($array2)) {
        return array_map(function($element) use ($array2) {
            if ($array2 == 0) {
                throw new InvalidArgumentException('Division by zero is not allowed.');
            }
            if (is_array($element)) {
                return self::divide($element, $array2);
            }
            return $element / $array2;
        }, $array1);
    }
    
    $array1Shape = NumpyLight::shape($array1);
    $array2Shape = NumpyLight::shape($array2);

//echo json_encode($array1Shape)." ".json_encode($array2Shape)."<- \n";
    // Check if arrays have the same size
    if ($array1Shape==$array2Shape) {
        return self::elementWiseDivide($array1, $array2);
    }else{

            $len1 = count($array1Shape); // check len of shape 1
            $len2 = count($array2Shape); // check len of shape 2
            $array1Cols = ($len1==1)? $array1Shape[0]:$array1Shape[1]; // if single type then give it cols 1
            $array2Cols = ($len2==1)? $array2Shape[0]:$array2Shape[1]; // if single type then give it cols 1

        if (self::isBroadcastable($array1Shape, $array2Shape)) {
            // Identify the smaller array and expand its dimensions


            $temp = NumpyLight::isShape1Bigger($array1Shape,$array2Shape);

            if ($temp) {

                if($array1Cols > $array2Cols){
                    $array2 = NumpyLight::expandDims($array2,NumpyLight::shape($array2),[count($array1),$array1Cols]);
                }else{  
                    $array1 = NumpyLight::expandDims($array1,NumpyLight::shape($array1),[count($array1),$array2Cols]);
                    $array2 = NumpyLight::expandDims($array2,NumpyLight::shape($array2),[count($array1),$array2Cols]);
                }

            }else{
                    

                if($array1Cols > $array2Cols){
                    $array1 = NumpyLight::expandDims($array1,NumpyLight::shape($array1),[count($array2),$array1Cols]);
                    $array2 = NumpyLight::expandDims($array2,NumpyLight::shape($array2),[count($array2),$array1Cols]);
                }else{  
                    $array1 = NumpyLight::expandDims($array1,NumpyLight::shape($array1),[count($array2),$array2Cols]);
                }
            }


            return self::elementWiseDivide($array1, $array2);
    } else {
        throw new Exception("Arrays are not broadcast-compatible.");
    }

throw new InvalidArgumentException('Arrays must have the same size for division.');
}


}

public static function elementWiseDivide($array1, $array2) {
    $result = [];

    for ($i = 0; $i < count($array1); $i++) {
        if (is_array($array1[$i]) && is_array($array2[$i])) {
            $result[] = self::elementWiseDivide($array1[$i], $array2[$i]);
        } elseif (!is_array($array1[$i]) && !is_array($array2[$i])) {
            if ($array2[$i] == 0) {
                throw new InvalidArgumentException('Division by zero is not allowed.');
            }
            $result[] = $array1[$i] / $array2[$i];
        } else {
            throw new InvalidArgumentException('Mismatch in dimensions for division.');
        }
    }

    return $result;
}


// if (self::isBroadcastable($array1Shape, $array2Shape)) {
//             // Identify the smaller array and expand its dimensions
//             if (self::isShape1Bigger($array1Shape, $array2Shape)) {
//                 $array2 = self::expandDims($array2, $array2Shape, $array1Shape);
//             } else {
//                 $array1 = self::expandDims($array1, $array1Shape, $array2Shape);
//             }

//             return self::elementWiseMax($array1, $array2);
//         } else {
//             throw new Exception("Arrays are not broadcast-compatible.");
//         }

public static function transpose($array) {
    $transposed = [];
    foreach ($array as $rowKey => $row) {
        foreach ($row as $colKey => $cell) {
            $transposed[$colKey][$rowKey] = $cell;
        }
    }
    return $transposed;
}

public static function multiply($array1, $array2) {
    // If array2 is a number, perform scalar multiplication
    if (is_numeric($array2)) {
        return array_map(function($element) use ($array2) {
            if (is_array($element)) {
                return self::multiply($element, $array2);
            }
            return $element * $array2;
        }, $array1);
    }
    
    // Check if arrays have the same size
    if (count($array1) !== count($array2)) {
        return "Error: Arrays must have the same size.";
    }

    // Perform element-wise multiplication
    $result = [];
    for ($i = 0; $i < count($array1); $i++) {
        if (is_array($array1[$i]) && is_array($array2[$i])) {
            $result[] = self::multiply($array1[$i], $array2[$i]);
        } elseif (!is_array($array1[$i]) && !is_array($array2[$i])) {
            $result[] = $array1[$i] * $array2[$i];
        } else {
            return "Error: Mismatch in dimensions.";
        }
    }

    return $result;
}

public static function max($array, $axis = null, $keepdims = false) {
    if ($axis === null) {
        // Flattened array
        return max(array_merge(...$array));
    }
    
    $result = [];
    if ($axis === 0) {
        foreach ($array[0] as $key => $_) {
            $col = array_column($array, $key);
            $result[] = max($col);
        }
    } elseif ($axis === 1) {
        foreach ($array as $row) {
            $result[] = max($row);
        }
    }
    
    if ($keepdims) {
        if ($axis === 0) {
            $result = [$result];
        } elseif ($axis === 1) {
            $result = array_map(function($e) { return [$e]; }, $result);
        }
    }
    
    return $result;
}


public static function empty_like($prototype) {
    // Recursively get the shape of the prototype
    $shape = [];
    $temp = $prototype;
    while (is_array($temp)) {
        $shape[] = count($temp);
        $temp = $temp[0];
    }

    // Recursively create an empty array with the same shape
    return self::zeros(...$shape);
}

public static function diagflat($arr, $k = 0) {
    // Flatten the array
    $flat_array = array();
    array_walk_recursive($arr, function($a) use (&$flat_array) { $flat_array[] = $a; });

    // Calculate the dimensions of the 2D array
    $n = count($flat_array);
    $dim = $n + abs($k);

    // Initialize the result as a 2D array filled with zeros
    $result = array();
    for ($i = 0; $i < $dim; $i++) {
        $result[] = array_fill(0, $dim, 0);
    }

    // Fill the diagonal
    for ($i = 0; $i < $n; $i++) {
        $row = $i;
        $col = $i + $k;
        if ($row < $dim && $col < $dim && $col >= 0) {
            $result[$row][$col] = $flat_array[$i];
        }
    }

    return $result;
}


public static function reshape($array, $shape) {
    // Flatten the array
    $flatArray = array();
    array_walk_recursive($array, function($a) use (&$flatArray) { $flatArray[] = $a; });

    // Calculate the total number of elements in the array
    $totalElements = array_product($shape);

    // Check if the total number of elements matches the array size
    if (count($flatArray) !== $totalElements) {
        throw new Exception('Total number of elements does not match the array size.');
    }

    $reshapedArray = array();
    $index = 0;

    for ($i = 0; $i < $shape[0]; $i++) {
        $row = array();
        for ($j = 0; $j < $shape[1]; $j++) {
            $row[] = $flatArray[$index];
            $index++;
        }
        $reshapedArray[] = $row;
    }
    return $reshapedArray;
}


public static function jacobian_matrix($output, $dvalues) {
    // Initialize an empty array to store the derivatives
    $dinputs = array();

    // Check if output is 1D or 2D and handle accordingly
    $is_1D = is_array($output[0]) ? false : true;
    $output = $is_1D ? array($output) : $output;
    $dvalues = $is_1D ? array($dvalues) : $dvalues;

    // Loop through each pair of output and dvalues
    for ($i = 0; $i < count($output); $i++) {
        $single_output = $output[$i];
        $single_dvalues = $dvalues[$i];

        // Reshape single_output to a column vector (nested array)
        $reshaped_single_output = array();
        foreach ($single_output as $value) {
            $reshaped_single_output[] = array($value);
        }

        // Compute the Jacobian matrix
        $jacobian_matrix = array();
        for ($row = 0; $row < count($reshaped_single_output); $row++) {
            $jacobian_row = array();
            for ($col = 0; $col < count($reshaped_single_output); $col++) {
                $value = ($row == $col) ? $reshaped_single_output[$row][0] : 0;
                $value -= $reshaped_single_output[$row][0] * $reshaped_single_output[$col][0];
                $jacobian_row[] = $value;
            }
            $jacobian_matrix[] = $jacobian_row;
        }

        // Compute the dot product of the Jacobian matrix and single_dvalues
        $dot_product = array();
        for ($row = 0; $row < count($jacobian_matrix); $row++) {
            $sum = 0;
            for ($col = 0; $col < count($jacobian_matrix[0]); $col++) {
                $sum += $jacobian_matrix[$row][$col] * $single_dvalues[$col];
            }
            $dot_product[] = $sum;
        }

        $dinputs[] = $dot_product;
    }

    // If original output was 1D, return only the first element of dinputs
    return $is_1D ? $dinputs[0] : $dinputs;
}





public static function sqrt($array) {
    return array_map(function($element) {
        if (is_numeric($element)) {
            if ($element >= 0) {
                return sqrt($element);
            } else {
                return NAN;
            }
        } elseif (is_array($element)) {
            return self::sqrt($element);
        } else {
            throw new InvalidArgumentException("Input must be an array of numbers.");
        }
    }, $array);
}


public static function hasINF($matrix) {
    foreach ($matrix as $row) {
        foreach ($row as $element) {
            if (is_infinite($element)) {
                return true;
            }
        }
    }
    return false;
}


public static function hasNAN($matrix) {
    foreach ($matrix as $row) {
        foreach ($row as $element) {
            if (is_nan($element)) {
                return true;
            }
        }
    }
    return false;
}

public static function spiral_data($samples, $classes) {
    $X = [];
    $y = [];

    for ($class_number = 0; $class_number < $classes; $class_number++) {
        $ix = range($samples * $class_number, $samples * ($class_number + 1) - 1);
        $r = array_map(function($value) use ($samples) {
            return $value / ($samples - 1);
        }, range(0, $samples - 1));
        
        $t = [];
        for ($i = 0; $i < $samples; $i++) {
            $t[] = self::lerp($class_number * 4, ($class_number + 1) * 4, $i / ($samples - 1)) + (mt_rand() / mt_getrandmax() - 0.5) * 0.2;
        }

        foreach ($ix as $i) {
            $X[$i] = [$r[$i % $samples] * sin($t[$i % $samples] * 2.5), $r[$i % $samples] * cos($t[$i % $samples] * 2.5)];
            $y[$i] = $class_number;
        }
    }

    return [$X, $y];
}

public static function vertical_data($samples, $classes) {
    $X = [];
    $y = [];

    for ($class_number = 0; $class_number < $classes; $class_number++) {
        $ix = range($samples * $class_number, $samples * ($class_number + 1) - 1);

        foreach ($ix as $i) {
            $X[$i] = [
                (mt_rand() / mt_getrandmax() - 0.5) * 0.2 + $class_number / 3, // random number centered around class_number / 3
                (mt_rand() / mt_getrandmax() - 0.5) * 0.2 + 0.5             // random number centered around 0.5
            ];
            $y[$i] = $class_number;
        }
    }

    return [$X, $y];
}


public static function circular_data($samples, $classes) {
    $X = [];
    $y = [];

    for ($class_number = 0; $class_number < $classes; $class_number++) {
        $ix = range($samples * $class_number, $samples * ($class_number + 1) - 1);
        $r = $class_number * 0.5 + 0.3;  // Set radius for each class. This will ensure that the classes are distinguishable.

        foreach ($ix as $i) {
            $angle = 2 * M_PI * (mt_rand() / mt_getrandmax());  // Random angle between 0 and 2π
            $radius_noise = (mt_rand() / mt_getrandmax() - 0.5) * 0.1;  // Add some noise to the radius to make it more challenging
            $X[$i] = [
                ($r + $radius_noise) * cos($angle),
                ($r + $radius_noise) * sin($angle)
            ];
            $y[$i] = $class_number;
        }
    }

    return [$X, $y];
}



// Linear interpolation function
public static function lerp($start, $end, $t) {
    return (1 - $t) * $start + $t * $end;
}


public static function displayMatrix($matrix, $maxRows = 5) {
    $rowCount = 0;
    foreach ($matrix as $row) {
        if ($rowCount >= $maxRows) {
            echo "... and more rows\n";
            break;
        }

        echo "[ " . implode(", ", $row) . " ]\n";
        $rowCount++;
    }
}

public static function sliceMatrix($matrix, $maxRows = 5) {
    return array_slice($matrix, 0, $maxRows);
}


public static function apply_relu_backwards($inputs, $dinputs) {
    foreach ($inputs as $key => $value) {
        if ($value <= 0) {
            $dinputs[$key] = 0;
        }
    }
    return $dinputs;
}



}


?>