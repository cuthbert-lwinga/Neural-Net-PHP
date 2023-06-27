<?PHP

class MathOperations {

  // //  M    M   A   TTTTT  RRRRR   IIIII   X   X
  // //  MM  MM  A A    T    R    R    I      X X 
  // //  M MM M AAAAA   T    RRRRR     I       X  
  // //  M    M A   A   T    RR        I      X X 
  // //  M    M A   A   T    R   R   IIIII   X   X  

  // bellow you will find different matrix operations   
  // Function to perform dot product between two matrices
    public static function dot($matrix1, $matrix2) {
        $result = array();
        $matrix1_shape = MathOperations::shape($matrix1);
        $matrix2_shape = MathOperations::shape($matrix2);
    $n1 = count($matrix1);// rows for matrix1
    $m1 = count($matrix1[0]);// cols for matrix1
    $n2 = count($matrix2); // rows for matrix2
    $m2 = count($matrix2[0]);// cols for matrix2
    //if ($m1 != $n2)
    if ($m1 != $n2) {
        echo "Error: Incompatible matrix sizes for dot product. Attempting $matrix1_shape . $matrix2_shape\n";
        return $result;
    }
    
    $Matrix_1_has_NAN = MathOperations::findNaN($matrix1);
    $Matrix_2_has_NAN = MathOperations::findNaN($matrix2);

    if ($Matrix_1_has_NAN !== null) {
        $rowIndex = $Matrix_1_has_NAN['row'];
        $colIndex = $Matrix_1_has_NAN['col'];
        echo "Error: NaN found in Matrix 1 at row $rowIndex, col $colIndex\n";
    } elseif ($Matrix_2_has_NAN !== null) {
        $rowIndex = $Matrix_2_has_NAN['row'];
        $colIndex = $Matrix_2_has_NAN['col'];
        echo "Error: NaN found in Matrix 2 at row $rowIndex, col $colIndex\n";
    } else {
    // Code when no NaN is found in either matrix
    // ...
    }

    for ($i = 0; $i < $n1; $i++) {
        $row = array();
        for ($j = 0; $j < $m2; $j++) {
            $sum = 0;
            for ($k = 0; $k < $m1; $k++) {
                //$sum += $matrix1[$i][$k] * $matrix2[$k][$j];
                $product = $matrix1[$i][$k] * $matrix2[$k][$j];
                if (is_nan($product)) {
                    echo "Error: NaN encountered at indices ".$matrix1[$i][$k]." and ".$matrix1[$i][$k].".\n";
                    return $result;
                }
                $sum += $product;

            }
            $row[] = $sum;
        }
        $result[] = $row;
    }
    
    return $result;
}

public static function findNaN($matrix) {
    $numRows = count($matrix);
    $numCols = count($matrix[0]);
    
    for ($row = 0; $row < $numRows; $row++) {
        for ($col = 0; $col < $numCols; $col++) {
            if (is_nan($matrix[$row][$col])) {
                return array('row' => $row, 'col' => $col);
            }
        }
    }
    
    return null;
}

public static function findNegativeNumber($matrix) {
    $numRows = count($matrix);
    $numCols = count($matrix[0]);
    
    for ($row = 0; $row < $numRows; $row++) {
        for ($col = 0; $col < $numCols; $col++) {
            // Check if the current element is a negative number
            if ($matrix[$row][$col] < 0) {
                return array('row' => $row, 'col' => $col);
            }
        }
    }
    
    // If no negative number is found, return null
    return null;
}


public static function applyThreshold($inputs,$threshold) {
    $rows = count($inputs);

$dinputs = []; // Initialize the $dinputs array

for ($i = 0; $i < $rows; $i++) {
    $cols = count($inputs[$i]);
    for ($j = 0; $j < $cols; $j++) {
        if ($inputs[$i][$j] <= $threshold) {
            $dinputs[$i][$j] = 0;
        } else {
            $dinputs[$i][$j] = $inputs[$i][$j];
        }
    }
}
return $dinputs;
}

public static function sqr($matrix) {
    $rows = count($matrix);
    $cols = count($matrix[0]);

    // Create a result matrix with the same dimensions
    $result = array();
    for ($i = 0; $i < $rows; $i++) {
        $result[$i] = array();
        for ($j = 0; $j < $cols; $j++) {
            $result[$i][$j] = ($matrix[$i][$j] * $matrix[$i][$j]);
        }
    }

    return $result;
}

public static function sqrt($matrix) {
    $rows = count($matrix);
    $cols = count($matrix[0]);

    // Create a result matrix with the same dimensions
    $result = array();
    for ($i = 0; $i < $rows; $i++) {
        $result[$i] = array();
        for ($j = 0; $j < $cols; $j++) {
            $result[$i][$j] = sqrt($matrix[$i][$j]);
        }
    }

    return $result;
}


public static function log($array) {
    $result = array();
    foreach ($array as $value) {
        $result[] = log($value);
    }
    return $result;
}


public static function relu($input){
    return max(0, $x);
}

public static function empty_like($array) {
    $emptyArray = array();

    // Iterate over the dimensions of the $array
    foreach ($array as $dimension) {
        $emptyArray[] = array_fill(0, count($dimension), null);
    }

    return $emptyArray;
}

public static function JacobianMatrix($input) {
    $inputMatrix = MathOperations::reshape($input, [1, count($input)]);
    $flattened = MathOperations::diagflat($input);
    $dot = MathOperations::dot($inputMatrix,MathOperations::transform($inputMatrix));
    $temp = MathOperations::m_operator($flattened, "-", $dot);
    return $temp;

}

public static function duplicate($arr,$rows){
    $return = [];

    for ($i=0; $i < $rows; $i++) { 
        $return[] = $arr;
    }
    return $return;
}

public static function npreshape($array, $rows, $columns) {
    $totalElements = count($array);
    
    if ($rows === -1) {
        if ($totalElements % $columns !== 0) {
            throw new Exception("Invalid reshape dimensions");
        }
        
        $rows = $totalElements / $columns;
    }
    
    $reshapedArray = [];
    
    for ($i = 0; $i < $rows; $i++) {
        $row = [];
        
        for ($j = 0; $j < $columns; $j++) {
            $elementIndex = $i * $columns + $j;
            
            if ($elementIndex < $totalElements) {
                $row[] = $array[$elementIndex];
            } else {
                $row[] = null; // Fill remaining elements with null
            }
        }
        
        $reshapedArray[] = $row;
    }
    
    return $reshapedArray;
}


public static function diagflat($input) {
    // Get the length of the input array
    $length = count($input);
    
    // Create an empty 2-D array
    $result = [];
    
    // Fill the array with zeros
    for ($i = 0; $i < $length; $i++) {
        $result[$i] = [];
        for ($j = 0; $j < $length; $j++) {
            $result[$i][$j] = 0;
        }
    }
    
    // Set the input values along the diagonal
    for ($i = 0; $i < $length; $i++) {
        $result[$i][$i] = $input[$i];
    }
    
    return $result;
}

public static function flattenArray($input) {
  $result = [];

  foreach ($input as $item) {
    $result[] = $item[0];
}

return $result;
}


public static function reshape($array, $shape) {
    // Calculate the total number of elements in the array
    $totalElements = array_product($shape);

    // Check if the total number of elements matches the array size
    if (count($array) !== $totalElements) {
        throw new Exception('Total number of elements does not match the array size.');
    }

    // Reshape the array
    $reshapedArray = array_chunk($array, $shape[1]);

    // Transpose the reshaped array
    $reshapedArray = array_map(null, ...$reshapedArray);

    if ($shape[0]==1) {
        for ($i=0; $i < count($reshapedArray); $i++) { 
            $reshapedArray[$i] = [$reshapedArray[$i]];
        }
    }

    return $reshapedArray;
}


public static function multiply_scalar($array, $number) {
    $result = array();
    foreach ($array as $element) {
        $result[] = $element * $number;
    }
    return $result;
}

public static function multiplyArrayByScalar($array, $scalar) {
    return array_map(function($subArray) use ($scalar) {
        return array_map(function($element) use ($scalar) {
            return $element * $scalar;
        }, $subArray);
    }, $array);
}



public static function m_operator($matrix1, $operator, $value) {
    if (is_numeric($value)) {

        if (is_array($matrix1)&&$operator=="x") {

            if (!is_array($matrix1[0])){
                return MathOperations::multiplyArrayByScalar($matrix1, $value);
            }
        }

        foreach ($matrix1 as &$row) {
            foreach ($row as &$element) {
                if ($operator === '+') {
                    $element += $value; // Addition
                } elseif ($operator === '-') {
                    $element -= $value; // Subtraction
                } elseif ($operator === '/') {
                    $element /= $value; // Division
                } elseif ($operator === 'x') {
                    $element *= $value; // Division
                } else {
                    die("Error: Invalid operator. Only '+', '-', and '/' operators are supported.");
                }
            }
        }
    }elseif (is_array($value) && self::isMatrix($value)) {
        $matrix2 = $value;

            $n1 = count($matrix1); // number of rows
            $m1 = count($matrix1[0]); // number of cols
            $n2 = count($matrix2); // number of rows
            $m2 = count($matrix2[0]); // number of cols

            if( $m1==$n2 && $m2==1 ){
              // then this is good to process

                for ($i = 0; $i < $n1; $i++) {
                    for ($j = 0; $j < $m1; $j++) {
                        if ($operator === '+') {
                        $matrix1[$i][$j] += $matrix2[$j][0]; // Element-wise addition
                    } elseif ($operator === '-') {
                        $matrix1[$i][$j] -= $matrix2[$j][0]; // Element-wise subtraction
                    } else {
                        die("Error: Invalid operator. Only '+' and '-' operators are supported.");
                    }
                }
            }


            return $matrix1;

        }

        if ($n1 !== $n2 || $m1 !== $m2) {
                // Perfom padding

            if($n2<$n1){
                $matrix2 = MathOperations::padMatrix($matrix1,$n1);
                return MathOperations::m_operator($matrix1, $operator,$matrix1);
            }else{
                die("Error: Incompatible matrix sizes for element-wise operation.");
            }
        }

        for ($i = 0; $i < $n1; $i++) {
            for ($j = 0; $j < $m1; $j++) {
                if ($operator === '+') {
                        $matrix1[$i][$j] += $matrix2[$i][$j]; // Element-wise addition
                    } elseif ($operator === '-') {
                        $matrix1[$i][$j] -= $matrix2[$i][$j]; // Element-wise subtraction
                    } elseif ($operator === '/') {
                        $matrix1[$i][$j] /= $matrix2[$i][$j]; // Element-wise subtraction
                    } else {
                        die("Error: Invalid operator. Only '+', '-' and '/' operators are supported.");
                    }
                }
            }
        } else {


            if(is_array($value)){

                $matrix2 = array();
                $matrix2[] = $value;
                $n1 = count($matrix1); // number of rows
                $m1 = count($matrix1[0]); // number of cols
                $n2 = count($matrix2); // number of rows
                $m2 = count($matrix2[0]); // number of co

                if($n2<$n1){
                    $matrix2 = MathOperations::padMatrix($matrix1,$n1);
                    
                    return MathOperations::m_operator($matrix1, $operator,$matrix1);
                }else{
                    die("Error: Incompatible matrix sizes for element-wise operation.");
                }

            }else{
                echo "Error: Invalid value. Value must be a numeric scalar or a matrix. while performing $operator by $value";
                
                MathOperations::printMatrix($matrix1,5);
                die();
            }
        }
        
        return $matrix1;
    }


    private static function padMatrix($array, $numRows) {
        $result = array();
        
        for ($i = 0; $i < $numRows; $i++) {
            $result[] = $array;
        }
        
        return $result;
    }

    private static function isMatrix($matrix) {
        if(!is_array($matrix)){
            return false;
        }

        if(!is_array($matrix[0])){
            return false;
        }
        $numRows = count($matrix);
        $numCols = count($matrix[0]);

        foreach ($matrix as $row) {
            if (!is_array($row) || count($row) !== $numCols) {
                return false;
            }
        }

        return true;
    }
    
  // Function to create an n * m matrix filled with zeros
    public static function zeros($n, $m) {
        $matrix = array();
        for ($i = 0; $i < $n; $i++) {
          $row = array();
          for ($j = 0; $j < $m; $j++) {
            $row[] = 0;
        }
        $matrix[] = $row;
    }

    return $matrix;

}

public static function zeros_like($matrix) {
    $result = array();
    
    foreach ($matrix as $row) {
        $resultRow = array();
        
        foreach ($row as $value) {
            $resultRow[] = 0;
        }
        
        $result[] = $resultRow;
    }
    
    return $result;
}


public static function transform($inputMatrix) {
  $result = array();
  
  $n = count($inputMatrix);
  $m = count($inputMatrix[0]);
  
  for ($j = 0; $j < $m; $j++) {
    $row = array();
    for ($i = 0; $i < $n; $i++) {
      $row[] = $inputMatrix[$i][$j];
  }
  $result[] = $row;
}

return $result;
}

public static function rand($n, $m) {

    $matrix = array();
    
    for ($i = 0; $i < $n; $i++) {
        $row = array();
        for ($j = 0; $j < $m; $j++) {
            $u1 = 1 - random_int(0, mt_getrandmax()) / mt_getrandmax();
            $u2 = 1 - random_int(0, mt_getrandmax()) / mt_getrandmax();
            $randStdNormal = sqrt(-2 * log($u1)) * cos(2 * pi() * $u2);
            $row[] = $randStdNormal;
        }
        $matrix[] = $row;
    }
    


    return $matrix;
}

public static function exp($matrix) {
    $numRows = count($matrix);
    $numCols = count($matrix[0]);
    
    $result = array();
    
    for ($row = 0; $row < $numRows; $row++) {
        $resultRow = array();
        for ($col = 0; $col < $numCols; $col++) {
            $resultRow[] = exp($matrix[$row][$col]);
        }
        $result[] = $resultRow;
    }
    
    return $result;
}

public static function deductMaxValueByRow($matrix) {
    $result = array();
    
    foreach ($matrix as $row) {
        $maxValue = max($row);
        $resultRow = array();
        
        foreach ($row as $value) {
            $resultRow[] = $value - $maxValue;
        }
        
        $result[] = $resultRow;
    }
    
    return $result;
}

public static function normalizeRows($matrix) {
    $result = array();
    
    foreach ($matrix as $row) {
        $rowSum = array_sum($row);
        
        if ($rowSum != 0) {
            $normalizedRow = array_map(function($value) use ($rowSum) {
                return $value / $rowSum;
            }, $row);
            
            $result[] = $normalizedRow;
        } else {
            $result[] = $row; // If the row sum is zero, keep the original row
        }
    }
    
    return $result;
}

public static function deductMax($matrix) {
    $numRows = count($matrix);
    $numCols = count($matrix[0]);
    
    $result = array();
    
    for ($row = 0; $row < $numRows; $row++) {
        $resultRow = array();
        for ($col = 0; $col < $numCols; $col++) {
            $resultRow[] = exp($matrix[$row][$col]);
        }
        $result[] = $resultRow;
    }
    
    return $result;
}


public static function clip($array, $minValue, $maxValue) {
    foreach ($array as &$subarray) {
        foreach ($subarray as &$value) {
            $value = max(min($value, $maxValue), $minValue);
        }
    }
    return $array;
}


public static function sum($array, $axis = null) {
    if ($axis === null) {
        return array_sum(array_merge(...$array));
    }

    $result = [];
    if ($axis === 0) {
        foreach ($array[0] as $index => $value) {
            $column = array_column($array, $index);
            $result[] = array_sum($column);
        }
    } elseif ($axis === 1) {
        foreach ($array as $row) {



            $result[] = array_sum($row);
        }
    }

    return $result;
}


public static function printMatrix($matrix, $limit = null)
{
    if (!is_array($matrix)) {
        echo "Invalid matrix format.";
        return;
    }

    if (!is_array($matrix[0])) {
        // code...
        $matrix = MathOperations::padMatrix($matrix, 1);
    }

    $rows = count($matrix);
    $columns = 0;

    if ($rows > 0) {
        $columns = count($matrix[0]);
    }

    if ($limit !== null) {
        $rows = min($rows, $limit);
        $columns = min($columns, $limit);
    }

    for ($i = 0; $i < $rows; $i++) {
        echo '[';
        for ($j = 0; $j < $columns; $j++) {
            if (is_array($matrix[$i][$j])) {
                echo '[' . implode(', ', $matrix[$i][$j]) . ']';
            } else {
                echo $matrix[$i][$j];
            }
            if ($j < $columns - 1) {
                echo ', ';
            }
        }
        echo ']' . PHP_EOL;
    }
}



public static function np_eye_index($labels, $y_true)
{
    $eyeMatrix = MathOperations::eye($labels);
    $result = array();
    foreach ($y_true as $index) {
        $result[] = $eyeMatrix[$index];
    }
    return $result;
}

public static function extract_matrix_one_hot_encoded($matrix,$encode){
    $return = array();
    $matrix_count = count($matrix);
    if ($matrix_count!=count($encode)) {
        die("one_hot_encoding_linear: length of matrix and hot encoding linear array don't match");
    }

    for ($i = 0; $i < $matrix_count; $i++) {
        $max = $matrix[$i];
        $index = array_search(1,$encode[$i]);
        if (is_numeric($index)){
            if ($index>=0&&$index<count($max)) {
                $return[] = $matrix[$i][$index]; 
            }else{
                die("one_hot_encoding_linear: index out of bound");
            }
        }else{
            die("one_hot_encoding_linear: not hot encoded found AKA: no 1 found @ encode[$i]");
        }
    } 

    return $return;   
}

public static function subtractFromDInputs($dinputs, $y_true,$value) {
  for ($i = 0; $i < count($y_true); $i++) {
    $dinputs[$i][$y_true[$i]] -= $value;
}
return $dinputs;
}

public static function max($inputs, $axis, $keepdims = true)
{
    $result = [];

    if ($axis === 0) {
        $size = count($inputs);
        $cols = count($inputs[0]);

        for ($j = 0; $j < $cols; $j++) {
            $column = array_column($inputs, $j);
            $result[] = max($column);
        }
    } elseif ($axis === 1) {
        foreach ($inputs as $row) {
            $result[] = max($row);
        }
    }

    if (!$keepdims) {
        $result = array_values($result);
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
    $prediction =MathOperations::argmax($matrix);
    for ($i = 0; $i < $matrix_count; $i++) {
        if ($prediction[$i]==$true_values[$i]) {
            $correct++;
        }
    } 
    return $correct/$matrix_count;   
}


public static function eye($n) {
    $eye = array();
    for ($i = 0; $i < $n; $i++) {
        $row = array();
        for ($j = 0; $j < $n; $j++) {
            $row[] = ($i === $j) ? 1 : 0;
        }
        $eye[] = $row;
    }
    return $eye;
}


public static function extract_matrix_by_scalar($matrix,$encode){
    $return = array();
    $matrix_count = count($matrix);
    if ($matrix_count!=count($encode)) {
        die("extract_matrix_by_scalar: length of matrix and hot encoding linear array don't match");
    }

    for ($i = 0; $i < $matrix_count; $i++) {
        $max = $matrix[$i];
        if ($encode[$i]>=0&&$encode[$i]<count($max)) {
            $return[] = $matrix[$i][$encode[$i]]; 
        }else{
            die("extract_matrix_by_scalar: index out of bound");
        }
    } 

    return $return;   
} 


public static function luDecomp($matrix) {
    $n = count($matrix);
    
    // Initialize L and U matrices as zero matrices
    $L = $U = array_fill(0, $n, array_fill(0, $n, 0));
    
    // Fill U matrix with the original matrix values
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            $U[$i][$j] = $matrix[$i][$j];
        }
    }
    
    // Fill diagonal of L matrix with ones
    for ($i = 0; $i < $n; $i++) {
        $L[$i][$i] = 1;
    }
    
    for ($k = 0; $k < $n - 1; $k++) {
        // Find pivot element
        $pivot = 0;
        $pivotRow = $k;
        for ($i = $k; $i < $n; $i++) {
            if (abs($U[$i][$k]) > $pivot) {
                $pivot = abs($U[$i][$k]);
                $pivotRow = $i;
            }
        }
        
        // Swap rows if necessary
        if ($pivotRow != $k) {
            list($U[$k], $U[$pivotRow]) = array($U[$pivotRow], $U[$k]);
            list($L[$k], $L[$pivotRow]) = array($L[$pivotRow], $L[$k]);
        }
        
        // Perform elimination
        for ($i = $k + 1; $i < $n; $i++) {
            $factor = $U[$i][$k] / $U[$k][$k];
            $L[$i][$k] = $factor;
            
            for ($j = $k; $j < $n; $j++) {
                $U[$i][$j] = $U[$i][$j] - $factor * $U[$k][$j];
            }
        }
    }
    
    return array('L' => $L, 'U' => $U);
}



/// Other Functions 


public static function checkArrayShape($array) {
    if (!is_array($array)) {
        return 0;
    }
    
    $shape = 0;
    $currentLevel = 1;
    
    while (is_array($array)) {
        $shape = max($shape, $currentLevel);
        
        $firstElement = reset($array);
        $array = is_array($firstElement) ? $firstElement : null;
        $currentLevel++;
    }
    
    return $shape;
}

public static function shape($array) {
    if (!is_array($array)) {
        return "Invalid input: Not an array.";
    }
    
    $rows = count($array);
    $cols = count($array[0]);
    
    return "($rows, $cols)";
}


private static function generateStandardNormalRandom() {
        // Use a suitable method to generate random numbers following a standard normal distribution
    // $u1 = 1 - random_int(0, mt_getrandmax()) / mt_getrandmax();
    // $u2 = 1 - random_int(0, mt_getrandmax()) / mt_getrandmax();
    // $z = sqrt(-2 * log($u1)) * cos(2 * pi() * $u2);

    // return $z;

    $u1 = 1 - rand() / (getrandmax() + 1);
    $u2 = 1 - rand() / (getrandmax() + 1);
    $z = sqrt(-2 * log($u1)) * cos(2 * pi() * $u2);

    return $z;

}



public static function spiral_data($points, $classes) {
    $X = array();
    $y = array();
    
    for ($class_number = 0; $class_number < $classes; $class_number++) {
        $ix = range($points * $class_number, $points * ($class_number + 1));
        $r = MathOperations::linspace(0.0, 1, $points); // radius
        $t = MathOperations::linspace($class_number * 4, ($class_number + 1) * 4, $points);
        for ($i = 0; $i < $points; $i++) {
            $t[$i] += MathOperations::randn() * 0.2;
            $X[$ix[$i]] = array($r[$i] * sin($t[$i] * 2.5), $r[$i] * cos($t[$i] * 2.5));
            $y[$ix[$i]] = $class_number;
        }
    }
    
    return array($X, $y);
}


public static function vertical_data($samples, $classes) {
    $X = array();
    $y = array();

    for ($class_number = 0; $class_number < $classes; $class_number++) {
        $ix = range($samples * $class_number, $samples * ($class_number + 1));
        $r = MathOperations::linspace(0.0, 1, $samples); // radius
        $t = MathOperations::linspace($class_number * 4, ($class_number + 1) * 4, $samples);
        for ($i = 0; $i < $samples; $i++) {
            $t[$i] += MathOperations::randn() * 0.2;
            $X[$ix[$i]] = array($r[$i] * sin($t[$i] * 2.5), $r[$i] * cos($t[$i] * 2.5));
            $y[$ix[$i]] = $class_number;
        }
    }

    return array($X, $y);
}


public static function linspace($start, $end, $points) {
    $interval = ($end - $start) / ($points - 1);
    $range = [];
    for ($i = 0; $i < $points; $i++) {
        $range[] = $start + ($interval * $i);
    }
    return $range;
}


public static function randn($min = 0, $max = 1) {
    $n = 1;
    $m = 1;
    $matrix = [];

    for ($i = 0; $i < $n; $i++) {
        $row = [];
        for ($j = 0; $j < $m; $j++) {
            $row[] = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        }
        $matrix[] = $row;
    }

    return $matrix[0][0];
}



}

?>