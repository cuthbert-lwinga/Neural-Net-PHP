<?php
namespace NameSpaceRandomGenerator;

include_once("Headers.php");


// class RandomGenerator {
    
//     private $seed;
    
//     public function __construct($seed = null) {
//         if ($seed === null) {
//             $seed = hexdec(bin2hex(random_bytes(5)));  //Use random_bytes to generate a random seed. so its unique
//         }
        
//         $this->seed = $seed;
//         mt_srand($this->seed);  // Initialize the Mersenne Twister RNG with the seed
//     }
    
//     private function randomFloat() {
//         return mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax();
//     }

//     private function randomArray($dimensions) {
//         if (count($dimensions) === 0) {
//             return $this->randomFloat();
//         }
        
//         $dim = array_shift($dimensions);
//         $result = [];
        
//         for ($i = 0; $i < $dim; $i++) {
//             $result[] = $this->randomArray($dimensions);
//         }
        
//         return $result;
//     }

//     public function rand(...$dimensions) {
//         return $this->randomArray($dimensions);
//     }
    
//     public function getSeed() {
//         return $this->seed;
//     }
// }

class RandomGenerator {
    
    private $seed;
    
    public function __construct($seed = null) {
        if ($seed === null) {
            $seed = hexdec(bin2hex(random_bytes(5)));  // Use random_bytes to generate a random seed, so it's unique
        }
        
        $this->seed = $seed;
        mt_srand($this->seed);  // Initialize the Mersenne Twister RNG with the seed
    }
    
    // Box-Muller transform to generate numbers with normal distribution
    public function randn() {
        $u1 = $this->randomFloat();
        $u2 = $this->randomFloat();
        $z0 = sqrt(-2 * log($u1)) * cos(2 * pi() * $u2);
        return $z0;
    }

    private function randomFloat() {
        return mt_rand() / mt_getrandmax();
    }

    private function randomArray($dimensions) {
        if (count($dimensions) === 0) {
            return $this->randn();  // Changed from randomFloat to randn
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
}
?>
