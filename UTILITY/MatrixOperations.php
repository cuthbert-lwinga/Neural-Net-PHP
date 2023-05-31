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
    
    $n1 = count($matrix1);
    $m1 = count($matrix1[0]);
    $n2 = count($matrix2);
    $m2 = count($matrix2[0]);
    
    if ($m1 != $n2) {
        echo "Error: Incompatible matrix sizes for dot product.";
        return $result;
    }
    
    for ($i = 0; $i < $n1; $i++) {
        $row = array();
        for ($j = 0; $j < $m2; $j++) {
            $sum = 0;
            for ($k = 0; $k < $m1; $k++) {
                $sum += $matrix1[$i][$k] * $matrix2[$k][$j];
            }
            $row[] = $sum;
        }
        $result[] = $row;
    }
    
    return $result;
}

public static function m_operator($matrix1, $operator, $value) {
    if (is_numeric($value)) {
        foreach ($matrix1 as &$row) {
            foreach ($row as &$element) {
                if ($operator === '+') {
                    $element += $value; // Addition
                } elseif ($operator === '-') {
                    $element -= $value; // Subtraction
                } elseif ($operator === '/') {
                    $element /= $value; // Division
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
                die("Error: Incompatible matrix sizes for element-wise operation.");
            }

            for ($i = 0; $i < $n1; $i++) {
                for ($j = 0; $j < $m1; $j++) {
                    if ($operator === '+') {
                        $matrix1[$i][$j] += $matrix2[$i][$j]; // Element-wise addition
                    } elseif ($operator === '-') {
                        $matrix1[$i][$j] -= $matrix2[$i][$j]; // Element-wise subtraction
                    } else {
                        die("Error: Invalid operator. Only '+' and '-' operators are supported.");
                    }
                }
            }
        } else {
            die("Error: Invalid value. Value must be a numeric scalar or a matrix.");
        }
    
    return $matrix;
}



      private static function isMatrix($matrix) {
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

    public static function rand($n, $m, $min = 0, $max = 1) {
        $matrix = array();
        
        for ($i = 0; $i < $n; $i++) {
            $row = array();
            for ($j = 0; $j < $m; $j++) {
                $row[] = $min + mt_rand() / mt_getrandmax() * ($max - $min);
            }
            $matrix[] = $row;
        }
        
        return $matrix;
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


private static function generateStandardNormalRandom() {
        // Use a suitable method to generate random numbers following a standard normal distribution
        // Example: Box-Muller transform
        $u1 = 1 - random_int(0, mt_getrandmax()) / mt_getrandmax();
        $u2 = 1 - random_int(0, mt_getrandmax()) / mt_getrandmax();
        $z = sqrt(-2 * log($u1)) * cos(2 * pi() * $u2);

        return $z;
    }



}

?>