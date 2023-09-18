<?php
use PHPUnit\Framework\TestCase;
use NameSpaceRandomGenerator\RandomGenerator;  // Import the RandomGenerator class from its namespace

class RandomGeneratorTest extends TestCase
{
    public function testSeedBasedGeneration()
    {
        $seed = 42;
        $generator1 = new RandomGenerator($seed);
        $result1 = $generator1->rand(2, 2);
        
        $generator2 = new RandomGenerator($seed);
        $result2 = $generator2->rand(2, 2);
        
        $this->assertEquals($result1, $result2, "Random arrays should be identical for the same seed");
    }

    public function testNoSeedGeneration()
    {
        $generator1 = new RandomGenerator();
        $result1 = $generator1->rand(2, 2);
        
        $generator2 = new RandomGenerator();
        $result2 = $generator2->rand(2, 2);
        
        $this->assertNotEquals($result1, $result2, "Random arrays should differ for different seeds (or no seed)");
    }

    public function testGetSeed()
    {
        $seed = 42;
        $generator = new RandomGenerator($seed);
        
        $this->assertEquals($seed, $generator->getSeed(), "Returned seed should match the given seed");
    }



    
}
?>
