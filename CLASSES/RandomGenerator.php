<?php
namespace NameSpaceRandomGenerator;

include_once("Headers.php");


class RandomGenerator {
    
    private $seed;
    
    public function __construct($seed = null) {
        if ($seed === null) {
            $seed = hexdec(bin2hex(random_bytes(5)));  //Use random_bytes to generate a random seed. so its unique
        }
        
        $this->seed = $seed;
        mt_srand($this->seed);  // Initialize the Mersenne Twister RNG with the seed
    }
    
    private function randomFloat() {
        return mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax();
    }

    private function randomArray($dimensions) {
        if (count($dimensions) === 0) {
            return $this->randomFloat();
        }
        
        $dim = array_shift($dimensions);
        $result = [];
        
        for ($i = 0; $i < $dim; $i++) {
            $result[] = $this->randomArray($dimensions);
        }
        
        return $result;
    }

    public function rand(...$dimensions) {
        return $this->randomArray($dimensions);
    }
    
    public function getSeed() {
        return $this->seed;
    }


public function binomial($n, $p, $shape_or_int) {
    if (is_int($shape_or_int)) {
        $result = [];
        for ($i = 0; $i < $shape_or_int; $i++) {
            $result[] = ($n == 1 && mt_rand() / mt_getrandmax() < $p) ? 1 : 0;
        }
        return $result;
    } else if (is_array($shape_or_int) && count($shape_or_int) == 0) {
        return ($n == 1 && mt_rand() / mt_getrandmax() < $p) ? 1 : 0;
    } else {
        $shape = $shape_or_int;
        $dimension = array_shift($shape);
        $result = [];
        for ($i = 0; $i < $dimension; $i++) {
            $result[] = self::binomial($n, $p, $shape);
        }
        return $result;
    }
}


public function shuffle(&$array) {
        if (!is_array($array) || empty($array)) {
            return;
        }

        $isMultidimensional = is_array($array[0]);
        if ($isMultidimensional) {
            // Shuffle only the first level of the array
            $keys = array_keys($array);
            shuffle($keys);
            $randomArray = [];
            foreach ($keys as $key) {
                $randomArray[$key] = $array[$key];
            }
            $array = $randomArray;
        } else {
            // Shuffle a one-dimensional array
            shuffle($array);
        }
    }



}

?>
