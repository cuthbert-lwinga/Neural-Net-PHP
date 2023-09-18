<?php
use PHPUnit\Framework\TestCase;
use NameSpaceNumpyLight\NumpyLight;
//ini_set('memory_limit', '256M');
ini_set("precision", "16");

// must have bcmath for extra precision


class NumpyLightTest extends TestCase
{
    public function testZeros1D()
    {
                echo "\n TESTING....[testZeros1D]\n";

        $numpyLight = new NumpyLight();
        $result = NumpyLight::zeros(3);
        $this->assertEquals([0.0, 0.0, 0.0], $result);
    }

    public function testZeros2D()
    {
        echo "\n TESTING....[testZeros2D]\n";
        $numpyLight = new NumpyLight();
        $result = NumpyLight::zeros(2, 2);
        $this->assertEquals([[0.0, 0.0], [0.0, 0.0]], $result);
    }

    public function testZeros3D()
    {
                echo "\n TESTING....[testZeros3D]\n";

        $numpyLight = new NumpyLight();
        $result = NumpyLight::zeros(2, 2, 2);
        $this->assertEquals([[[0.0, 0.0], [0.0, 0.0]], [[0.0, 0.0], [0.0, 0.0]]], $result);
    }

    public function testZerosLike()
{
    echo "\n TESTING....[testZerosLike]\n";

    $numpyLight = new NumpyLight();
    
    // Test 1D array
    $result = NumpyLight::zeros_like([1, 2, 3]);
    $this->assertEquals([0.0, 0.0, 0.0], $result);

    // Test 2D array
    $result = NumpyLight::zeros_like([[1, 2], [3, 4]]);
    $this->assertEquals([[0.0, 0.0], [0.0, 0.0]], $result);

    // Test 3D array
    $result = NumpyLight::zeros_like([[[1, 2], [3, 4]], [[5, 6], [7, 8]]]);
    $this->assertEquals([[[0.0, 0.0], [0.0, 0.0]], [[0.0, 0.0], [0.0, 0.0]]], $result);
}

public function testPow()
{
    echo "\n TESTING....[testPow]\n";

    $numpyLight = new NumpyLight();

    // Test 1D array
    $result = NumpyLight::pow([1, 2, 3], 2);
    $this->assertEquals([1, 4, 9], $result);

    // Test 2D array
    $result = NumpyLight::pow([[1, 2], [3, 4]], 2);
    $this->assertEquals([[1, 4], [9, 16]], $result);
}


    public function testDot()
    {
                echo "\n TESTING....[testDot]\n";

        // Smaller example
        $a1 = [
            [1, 0],
            [0, 1]
        ];
        $b1 = [
            [4, 1],
            [2, 2]
        ];

        $result1 = NumPyLight::dot($a1, $b1);

        $expected1 = [
            [4, 1],
            [2, 2]
        ];

        $this->assertEquals($expected1, $result1);

        // Larger example
        $a2 = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
            [10, 11, 12]
        ];
        $b2 = [
            [2, 0, 1],
            [1, 2, 1],
            [1, 1, 0]
        ];

        $result2 = NumPyLight::dot($a2, $b2);

        //var_dump($result2);

        $expected2 = [
            [ 7,  7,  3],
            [19, 16,  9],
            [31, 25, 15],
            [43, 34, 21]
        ];

        $this->assertEquals($expected2, $result2);
    }

    public function testSumNoAxisNoKeepdims()
    {

                echo "\n TESTING....[testSumNoAxisNoKeepdims]\n";

        $array_2d = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $result = NumpyLight::sum($array_2d);
        $this->assertEquals(45, $result, "testSumNoAxisNoKeepdims is not equal output: $result");

    }

    public function testArgmax()
{
    echo "\n TESTING....[testArgmax]\n";

    $numpyLight = new NumpyLight();

    // Test 2D array
    $result = NumpyLight::argmax([[1, 2, 3], [4, 5, 1], [2, 8, 6]]);
    $this->assertEquals([2, 1, 1], $result);

    // Test with negative numbers
    $result = NumpyLight::argmax([[-1, -2, -3], [-4, -5, -1], [-2, -8, -6]]);
    $this->assertEquals([0, 2, 0], $result);

    // Test with duplicate maximum values
    $result = NumpyLight::argmax([[1, 2, 2], [4, 4, 1], [2, 8, 8]]);
    $this->assertEquals([1, 0, 1], $result);
}


    public function testSumNoAxisKeepdims()
    {
                echo "\n TESTING....[testSumNoAxisKeepdims]\n";

        $array_2d = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $result = NumpyLight::sum($array_2d, null, true);
        $temp = json_encode($result);
        $this->assertEquals([[45]], $result, "testSumNoAxisKeepdims is not equal output: $temp");
    }

    public function testSumAxis0NoKeepdims()
    {

                echo "\n TESTING....[testSumAxis0NoKeepdims]\n";

        $array_2d = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $result = NumpyLight::sum($array_2d, 0);
        $this->assertEquals([12, 15, 18], $result);
    }

    public function testSumAxis0Keepdims()
    {

                echo "\n TESTING....[testSumAxis0Keepdims]\n";

        $array_2d = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $result = NumpyLight::sum($array_2d, 0, true);
        $temp = json_encode($result);

        $this->assertEquals([[12, 15, 18]], $result, "testSumAxis0Keepdims is not equal output: $temp");
    }

    public function testSumAxis1NoKeepdims()
    {

                echo "\n TESTING....[testSumAxis1NoKeepdims]\n";

        $array_2d = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $result = NumpyLight::sum($array_2d, 1);
        $temp = json_encode($result);

        $this->assertEquals([6, 15, 24], $result, "testSumAxis1NoKeepdims is not equal output: $temp");
    }

    public function testSumAxis1Keepdims()
    {
                echo "\n TESTING....[testSumAxis1Keepdims]\n";

        $array_2d = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $result = NumpyLight::sum($array_2d, 1, true);
        $temp = json_encode($result);

        $this->assertEquals([[6], [15], [24]], $result, "testSumAxis1Keepdims is not equal output: $temp");
    }


    public function testMaximum1D()
    {
                echo "\n TESTING....[testMaximum1D]\n";

        $result = NumpyLight::maximum([2, 3, 4], [1, 5, 2]);
        $this->assertEquals([2, 5, 4], $result);
    }

    public function testMaximumBroadcast()
    {
                echo "\n TESTING....[testMaximumBroadcast]\n";

        $result = NumpyLight::maximum([[1.0, 0.0], [0.0, 1.0]], [0.5, 2.0]);
        $result_json = json_encode($result);
    $expected = [[1.0, 2.0], [0.5, 2.0]];  // Update this to the expected output if needed
    $expected_json = json_encode($expected);
    $this->assertEquals($expected, $result, "testMaximumBroadcast failed. Expected: $expected_json, Got: $result_json");
}

public function testMaximumNaN()
{
            echo "\n TESTING....[testMaximumNaN]\n";

    $result = NumpyLight::maximum([null, 0, null], [0, null, null]);
    $result_json = json_encode($result, JSON_PRETTY_PRINT);
    $expected = [null, null, null];  // Update this to the expected output if needed
    $expected_json = json_encode($expected, JSON_PRETTY_PRINT);
    if ($expected != $result) {
        echo "Expected: $expected_json\n";
        echo "Got: $result_json\n";
    }
    $this->assertEquals($expected, $result, "testMaximumNaN failed. Expected: $expected_json, Got: $result_json");
}


public function testMaximumInf()
{        

    echo "\n TESTING....[testMaximumInf]\n";

    $result = NumpyLight::maximum(INF, 1);
    $this->assertEquals(INF, $result);
}

public function testReLU()
{
            echo "\n TESTING....[testReLU]\n";

    // Testing ReLU on a small array
    $small_array = [-1, 0, 1, 2, -2];
    $result_small = NumpyLight::ReLU($small_array);
    $this->assertEquals([0, 0, 1, 2, 0], $result_small, "testReLU failed for small array.");

    // Testing ReLU on a large array
    $large_array = range(-10, 10);
    $result_large = NumpyLight::ReLU($large_array);
    $expected_large = array_map(function($x) { return max(0, $x); }, range(-10, 10));
    $this->assertEquals($expected_large, $result_large, "testReLU failed for large array.");
}


public function testExp()
{
        echo "\n TESTING....[testExp]\n";

    $input_array = [0.00186744273, 0.0432139183, 1.0, 23.1406926, 535.491656];
    $result = NumpyLight::exp($input_array);
    $expected = [1.00186919,1.04416124,2.71828183,11216958254.78396,3.639747915916645e+232];
    
    // Define a custom function to compare float values with tolerance
    $compareFloats = function($a, $b, $tolerance = 1e-5) {
        return abs($a - $b) < $tolerance;
    };

    // Compare each element in the result and expected arrays
    for ($i = 0; $i < count($expected); $i++) {
        $this->assertTrue($compareFloats($expected[$i], $result[$i]), "Element $i does not match. Expected: ".$expected[$i]." Result: ".$result[$i]);
    }
}

public function testAddMatrixwithIntOrFloat()
{
    echo "\n TESTING....[testAddMatrixwithIntOrFloat]\n";

    $numpyLight = new NumpyLight();

    // Test 1D array with integer
    $result = NumpyLight::add([1, 2, 3], 2);
    $this->assertEquals([3, 4, 5], $result);

    // Test 2D array with integer
    $result = NumpyLight::add([[1, 2], [3, 4]], 2);
    $this->assertEquals([[3, 4], [5, 6]], $result);
}

public function testAdd1DArrays()
    {
         echo "\n TESTING....[testAdd1DArrays]\n";
        $matrix1 = [1, 2, 3];
        $matrix2 = [4, 5, 6];
        $expectedResult = [5, 7, 9];
        
        $actualResult = NumpyLight::add($matrix1, $matrix2);
        
        $this->assertEquals($expectedResult, $actualResult);
    }

public function testAddMatrices() {
        echo "\n TESTING....[testAddMatrices]\n";

        $matrix1 = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $matrix2 = [
            [9, 8, 7],
            [6, 5, 4],
            [3, 2, 1]
        ];

        $result = NumpyLight::add($matrix1, $matrix2);
        $this->assertEquals([[10, 10, 10], [10, 10, 10], [10, 10, 10]], $result);
    }


public function testAddWithLargerArrays()
{

     echo "\n TESTING....[testAddWithLargerArrays]\n";
    $matrix1 = [
        [1, 2, 3, 4, 5],
        [6, 7, 8, 9, 10],
        [11, 12, 13, 14, 15],
        [16, 17, 18, 19, 20],
        [21, 22, 23, 24, 25]
    ];

    $matrix2 = [
        [1, 1, 1, 1, 1]
    ];

    $expected = [
        [2, 3, 4, 5, 6],
        [7, 8, 9, 10, 11],
        [12, 13, 14, 15, 16],
        [17, 18, 19, 20, 21],
        [22, 23, 24, 25, 26]
    ];

    $result = NumpyLight::add($matrix1, $matrix2);
    $this->assertEquals($expected, $result);
}



    public function testSubtractMatrices() {
        echo "\n TESTING....[testSubtractMatrices]\n";

        $matrix1 = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $matrix2 = [
            [9, 8, 7],
            [6, 5, 4],
            [3, 2, 1]
        ];

        $result = NumpyLight::subtract($matrix1, $matrix2);
        $this->assertEquals([[-8, -6, -4], [-2, 0, 2], [4, 6, 8]], $result);
    }


public function testSubtractWithBroadcasting()
{
    echo "\n TESTING....[testSubtractWithBroadcasting]\n";
    $matrix1 = [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9]
    ];

    $matrix2 = [
        [1],
        [2],
        [3]
    ];

    $expected = [
        [0, 1, 2],
        [2, 3, 4],
        [4, 5, 6]
    ];

    $result = NumpyLight::subtract($matrix1, $matrix2);

    $this->assertEquals($expected, $result);
}


    public function testDivideMatrices() {
        echo "\n TESTING....[testDivideMatrices]\n";

        $matrix1 = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $matrix2 = [
            [9, 8, 7],
            [6, 5, 4],
            [3, 2, 1]
        ];

        $result = NumpyLight::divide($matrix1, $matrix2);
        $this->assertEquals([[0.1111111111111111,0.25,0.42857142857142855],[0.6666666666666666,1,1.5],[2.3333333333333335,4,9]], $result, "Elements do not match. Result: ".json_encode($result), $delta = 0.0001);
    }


public function testDivideVariantShapes() {
    echo "\n TESTING....[testDivideVariantShapes]\n";

    $array1 = [2, 4, 6, 8];
    $array2 = [[1], [2], [3]];
    $expected = [
        [2.0, 4.0, 6.0, 8.0],
        [1.0, 2.0, 3.0, 4.0],
        [0.66666667, 1.33333333, 2.0, 2.66666667]
    ];

    $result = NumpyLight::divide($array1, $array2);

    // Define a custom function to compare float values with tolerance
    $compareFloats = function ($a, $b, $tolerance = 1e-5) {
        return abs($a - $b) < $tolerance;
    };

    // Compare each element in the result and expected arrays
    for ($i = 0; $i < count($expected); $i++) {
        for ($j = 0; $j < count($expected[$i]); $j++) {
            $this->assertTrue(
                $compareFloats($expected[$i][$j], $result[$i][$j]),
                "Element [$i][$j] does not match. Expected: " . $expected[$i][$j] . " Result: " . $result[$i][$j]
            );
        }
    }
}

public function testDivideVariantShapes2() {
    echo "\n TESTING....[testDivideVariantShapes2]\n";

    $array1 = [[1], [2], [3]];
    $array2 = [2, 4, 6, 8];
   $expected = [
        [0.5, 0.25, 0.16666667, 0.125],
        [1.0, 0.5, 0.33333333, 0.25],
        [1.5, 0.75, 0.5, 0.375],
    ];

    $result = NumpyLight::divide($array1, $array2);

    // Define a custom function to compare float values with tolerance
    $compareFloats = function ($a, $b, $tolerance = 1e-5) {
        return abs($a - $b) < $tolerance;
    };

    // Compare each element in the result and expected arrays
    for ($i = 0; $i < count($expected); $i++) {
        for ($j = 0; $j < count($expected[$i]); $j++) {
            $this->assertTrue(
                $compareFloats($expected[$i][$j], $result[$i][$j]),
                "Element [$i][$j] does not match. Expected: " . $expected[$i][$j] . " Result: " . $result[$i][$j]
            );
        }
    }
}


public function testDivide() {
    // Test scalar division
    echo "\n TESTING....[testDivide by Scalar]\n";
    $array1 = [[1, 2, 3], [4, 5, 6]];
    $scalar = 2;
    $expected1 = [[0.5, 1, 1.5], [2, 2.5, 3]];
    $result1 = NumpyLight::divide($array1, $scalar);
    $this->assertEquals($expected1, $result1);

    // Test element-wise division
    $array2 = [[1, 2], [3, 4]];
    $array3 = [[1, 2], [1, 2]];
    $expected2 = [[1, 1], [3, 2]];
    $result2 = NumpyLight::divide($array2, $array3);
    $this->assertEquals($expected2, $result2);

    // Test division by zero exception
    //$this->expectException(InvalidArgumentException::class);
    // NumpyLight::divide([1, 2, 3], [1, 0, 1]);
}


    public function testTransposeSmall() {
    $input = [[1, 2], [3, 4]];
    $expected = [[1, 3], [2, 4]];
    $result = NumpyLight::transpose($input);
    $this->assertEquals($expected, $result);
}

public function testTransposeLarge() {
    echo "\n TESTING....[testTransposeLarge]\n";

    $input = [
        [7, 4, 3, 7, 6, 5, 1, 1, 3, 9],
        [9, 4, 4, 4, 2, 9, 4, 8, 6, 6],
        [8, 9, 9, 9, 7, 8, 8, 2, 9, 1],
        [1, 6, 6, 5, 7, 8, 7, 2, 5, 4],
        [8, 1, 6, 6, 6, 9, 3, 1, 3, 2],
        [4, 6, 2, 9, 3, 3, 9, 1, 2, 1],
        [4, 2, 9, 2, 5, 4, 7, 1, 6, 8],
        [5, 2, 8, 6, 1, 8, 1, 6, 3, 7],
        [5, 1, 3, 5, 4, 1, 7, 9, 3, 1],
        [9, 6, 3, 4, 2, 6, 4, 7, 1, 9]
    ];

    $expected = [
        [7, 9, 8, 1, 8, 4, 4, 5, 5, 9],
        [4, 4, 9, 6, 1, 6, 2, 2, 1, 6],
        [3, 4, 9, 6, 6, 2, 9, 8, 3, 3],
        [7, 4, 9, 5, 6, 9, 2, 6, 5, 4],
        [6, 2, 7, 7, 6, 3, 5, 1, 4, 2],
        [5, 9, 8, 8, 9, 3, 4, 8, 1, 6],
        [1, 4, 8, 7, 3, 9, 7, 1, 7, 4],
        [1, 8, 2, 2, 1, 1, 1, 6, 9, 7],
        [3, 6, 9, 5, 3, 2, 6, 3, 3, 1],
        [9, 6, 1, 4, 2, 1, 8, 7, 1, 9]
    ];

    $result = NumpyLight::transpose($input);
    $this->assertEquals($expected, $result);
}


public function testMultiplyArrays() {
    echo "\n TESTING....[testMultiplyArrays]\n";

    $array1 = [1, 2, 3];
    $array2 = [4, 5, 6];
    $result = NumpyLight::multiply($array1, $array2);
    
    $this->assertEquals([4, 10, 18], $result);
}

public function testMultiplyArraysByScalar() {
    echo "\n TESTING....[testMultiplyArraysByScalar]\n";

    $array1 = [1, 2, 3];
    $scalar = 2;
    $result = NumpyLight::multiply($array1, $scalar);
    
    $this->assertEquals([2, 4, 6], $result);
}



    public function testMaxSmall() {
         echo "\n TESTING....[testMaxSmall]\n";

        $small_array = [[1, 2, 3], [4, 5, 6], [7, 8, 9]];
        
        // Test cases based on the Python output
        $this->assertEquals(9, NumpyLight::max($small_array));
        $this->assertEquals([7, 8, 9], NumpyLight::max($small_array, 0));
        $this->assertEquals([[7, 8, 9]], NumpyLight::max($small_array, 0, true));
        $this->assertEquals([3, 6, 9], NumpyLight::max($small_array, 1));
        $this->assertEquals([[3], [6], [9]], NumpyLight::max($small_array, 1, true));
    }
    
    public function testMaxLarge() {
 echo "\n TESTING....[testMaxLarge]\n";

        $large_array = [
            [12, 32, 43, 98, 45],
            [58, 21, 36, 12, 46],
            [23, 56, 83, 21, 22],
            [17, 92, 41, 13, 21],
            [34, 11, 76, 23, 49]
        ];
        
        // Test cases based on the Python output
        $this->assertEquals(98, NumpyLight::max($large_array));
        $this->assertEquals([58, 92, 83, 98, 49], NumpyLight::max($large_array, 0));
        $this->assertEquals([[58, 92, 83, 98, 49]], NumpyLight::max($large_array, 0, true));
        $this->assertEquals([98, 58, 83, 92, 76], NumpyLight::max($large_array, 1));
        $this->assertEquals([[98], [58], [83], [92], [76]], NumpyLight::max($large_array, 1, true));
    }


public function testEmptyLike() {
     echo "\n TESTING....[Emptylike]\n";

    $prototype_1d = [1, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3, 2, 3];
    $empty_like_1d = NumpyLight::empty_like($prototype_1d);
    $this->assertCount(count($prototype_1d), $empty_like_1d);

    $prototype_2d = [[1, 2], [3, 4]];
    $empty_like_2d = NumpyLight::empty_like($prototype_2d);
    $this->assertCount(2, $empty_like_2d);
    $this->assertCount(2, $empty_like_2d[0]);

    $prototype_3d = [[[1, 2], [3, 4]], [[5, 6], [7, 8]]];
    $empty_like_3d = NumpyLight::empty_like($prototype_3d);
    $this->assertCount(2, $empty_like_3d);
    $this->assertCount(2, $empty_like_3d[0]);
    $this->assertCount(2, $empty_like_3d[0][0]);

    $prototype_dtype = [1.0, 2.0, 3.0];
    $empty_like_dtype = NumpyLight::empty_like($prototype_dtype);
    $this->assertCount(3, $empty_like_dtype);
}

public function testDiagflat() {
    echo "\n TESTING....[testDiagflat]\n";
    // Test case based on Python output for diagflat([[1, 2], [3, 4]])
    $expected1 = [
        [1, 0, 0, 0],
        [0, 2, 0, 0],
        [0, 0, 3, 0],
        [0, 0, 0, 4]
    ];
    $this->assertEquals($expected1, NumpyLight::diagflat([[1, 2], [3, 4]]));

    // Test case based on Python output for diagflat([1, 2], 1)
    $expected2 = [[0, 1, 0], [0, 0, 2], [0, 0, 0]];
    $this->assertEquals($expected2, NumpyLight::diagflat([1, 2], 1));
}

public function testReshape() {
    echo "\n TESTING....[testReshape]\n";
    // Test case 1
    $array1 = [1, 2, 3, 4, 5, 6];
    $shape1 = [2, 3];
    $expected1 = [[1, 2, 3], [4, 5, 6]];
    $this->assertEquals($expected1, NumpyLight::reshape($array1, $shape1));

    // Test case 2
    $array2 = [1, 2, 3, 4, 5, 6, 7, 8, 9];
    $shape2 = [3, 3];
    $expected2 = [[1, 2, 3], [4, 5, 6], [7, 8, 9]];
    $this->assertEquals($expected2, NumpyLight::reshape($array2, $shape2));

    // Test case 3
    $array3 = [1, 2, 3, 4];
    $shape3 = [1, 4];
    $expected3 = [[1, 2, 3, 4]];
    $this->assertEquals($expected3, NumpyLight::reshape($array3, $shape3));
}

public function testJacobianMatrix() {
    $output = [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6],[0.1, 0.2, 0.3], [0.4, 0.5, 0.6]];
    $dvalues = [[0.1, 0.2, -0.3], [-0.2, 0.3, -0.1],[0.1, 0.2, -0.3], [-0.2, 0.3, -0.1]];
    
    $expected = [
    [0.014, 0.048, -0.078],
    [-0.084, 0.145, -0.066],
    [0.014, 0.048, -0.078],
    [-0.084, 0.145, -0.066]
];
    
    $result = NumpyLight::jacobian_matrix($output, $dvalues);

    // Iterate over each sub-array and element for comparison
    foreach ($result as $i => $subArray) {
        foreach ($subArray as $j => $value) {
            // Using assertEquals with a delta for floating-point comparison
            $this->assertTrue(abs($expected[$i][$j] - $value) < 0.0001);
        }
    }
}


public function testSqrt() {
    $input1 = [1, 4, 9];
    $expected1 = [1.0, 2.0, 3.0];
    $result1 = NumpyLight::sqrt($input1);
    foreach ($result1 as $i => $value) {
        $this->assertEquals($expected1[$i], $value, '', $delta = 0.0001);
    }

    // For complex numbers, you'd need to decide how to handle them in PHP as PHP doesn't have built-in support for complex numbers.

    $input2 = [4, -1, INF];
    $expected2 = [2.0, NAN, INF];
    $result2 = NumpyLight::sqrt($input2);
    foreach ($result2 as $i => $value) {
        if (is_nan($expected2[$i])) {
            $this->assertTrue(is_nan($value));
        } else {
            $this->assertEquals($expected2[$i], $value, '', $delta = 0.0001);
        }
    }
}



public function testMean() {
    echo "\n TESTING....[testMean]\n";

    $numpyLight = new NumpyLight();

    // Test 1: Mean of a flattened array
    $a1 = array(array(1, 2), array(3, 4));
    $result1 = NumpyLight::mean($a1);
    $this->assertEquals(2.5, $result1);

    // Test 2: Mean along axis=0
    $a2 = array(array(1, 2), array(3, 4));
    $result2 = NumpyLight::mean($a2, 0);
    $this->assertEquals(array(2, 3), $result2);

    // Test 3: Mean along axis=1
    $a3 = array(array(1, 2), array(3, 4));
    $result3 = NumpyLight::mean($a3, 1);
    $this->assertEquals(array(1.5, 3.5), $result3);

    // Test 4: Mean along axis=0 with keepdims=True
    $a4 = array(array(1, 2), array(3, 4));
    $result4 = NumpyLight::mean($a4, 0, "float64", True);
    $this->assertEquals(array(array(2, 3)), $result4);


// Test 1: 1D array [1, 2, 3]
    $this->assertEquals(2.0, NumpyLight::mean([1, 2, 3]));

    // Test 2: 2D array [[1, 2], [3, 4]]
    $this->assertEquals(2.5, NumpyLight::mean([[1, 2], [3, 4]]));
    $this->assertEquals([2.0, 3.0], NumpyLight::mean([[1, 2], [3, 4]], 0));
    $this->assertEquals([1.5, 3.5], NumpyLight::mean([[1, 2], [3, 4]], 1));

    // Test 3: 3x3 array
    $this->assertEquals(5.0, NumpyLight::mean([[1, 2, 3], [4, 5, 6], [7, 8, 9]]));
    $this->assertEquals([4.0, 5.0, 6.0], NumpyLight::mean([[1, 2, 3], [4, 5, 6], [7, 8, 9]], 0));
    $this->assertEquals([2.0, 5.0, 8.0], NumpyLight::mean([[1, 2, 3], [4, 5, 6], [7, 8, 9]], 1));



}


public function testClip() {
    echo "\n TESTING....[testClip]\n";

    // Test 1: Clip values between 1 and 8
    $this->assertEquals([1, 1, 2, 3, 4, 5, 6, 7, 8, 8], NumpyLight::clip([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], 1, 8));

    // Test 2: Clip values between 8 and 1
    $this->assertEquals([1, 1, 1, 1, 1, 1, 1, 1, 1, 1], NumpyLight::clip([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], 8, 1));

    // Test 3: Clip values between 3 and 6
    $this->assertEquals([3, 3, 3, 3, 4, 5, 6, 6, 6, 6], NumpyLight::clip([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], 3, 6));

    // Test 4: Clip values between variable minimums and 8
    $this->assertEquals([3, 4, 2, 3, 4, 5, 6, 7, 8, 8], NumpyLight::clip([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], [3, 4, 1, 1, 1, 4, 4, 4, 4, 4], 8));
}

public function testGetValuesFromIndexes() {
    echo "\n TESTING....[testGetValuesFromIndexes]\n";

    // Test 1: Simple 2D array
    $array1 = [[0, 1, 2], [3, 4, 5], [6, 7, 8]];
    $indexes1 = [0, 1, 2];
    $this->assertEquals([0, 4, 8], NumpyLight::get_values_from_indexes($array1, $indexes1));

    // Test 2: Indices are the same
    $array2 = [[1, 2, 3], [4, 5, 6], [7, 8, 9]];
    $indexes2 = [1, 1, 1];
    $this->assertEquals([2, 5, 8], NumpyLight::get_values_from_indexes($array2, $indexes2));

    // Test 3: Indices are out of bounds
    $array3 = [[10, 11], [12, 13]];
    $indexes3 = [1, 3];  // 3 is out of bounds
    $this->assertEquals([11, null], NumpyLight::get_values_from_indexes($array3, $indexes3));

    // Test 4: Array has more dimensions
    $array4 = [[[1, 2], [3, 4]], [[5, 6], [7, 8]]];
    $indexes4 = [0, 1];
    $this->assertEquals([[1, 2], [7, 8]], NumpyLight::get_values_from_indexes($array4, $indexes4));
}

public function testLog() {

    echo "\n TESTING....[testLog]\n";
    $testCases = [
        // Test 1: Example from the numpy documentation
        [[1, exp(1), exp(2), 0], [0.0, 1.0, 2.0, -INF]],
        
        // Test 2: Simple test case
        [[2, 4, 8], [0.6931471805599453, 1.3862943611198906, 2.0794415416798357]],
        
        // Test 3: Powers of 10
        [[1, 100, 10000], [0.0, 4.605170185988092, 9.210340371976182]],
        
        // Test 4: Powers of e
        [[exp(1), exp(2), exp(3)], [1.0, 2.0, 3.0]],
        
        // Test 5: Fractional values
        [[0.5, 0.25, 0.125], [-0.6931471805599453, -1.3862943611198906, -2.0794415416798357]],
        
        // Test 6: Negative values (Should return NaN, you may need to handle this in your PHP code)
        [[-1, -1*exp(1), -1*exp(2)], [null, null, null]]
    ];

foreach ($testCases as $testCaseIndex => $testCase) {
    $input = $testCase[0];
    $expected = $testCase[1];
    $result = NumpyLight::log($input);

    foreach ($result as $i => $value) {
        if ($expected[$i] === null) {
            if ($value !== null) {
                $this->fail("Test case {$testCaseIndex}, Element {$i}: Expected null, got {$value}");
            }
        } else {
            if (abs($expected[$i] - $value) >= 1e-12) {
                $this->fail("Test case {$testCaseIndex}, Element {$i}: Expected {$expected[$i]}, got {$value}");
            }
        }
    }
}
$this->assertTrue(true);


}

    public function testEye() {

        echo "\n TESTING....[testEye]\n";

        // Test case 1: N = 2
        $this->assertEquals([[1, 0], [0, 1]], NumpyLight::eye(2));

        // Test case 2: N = 3, M = 4
        $this->assertEquals([[1, 0, 0, 0], [0, 1, 0, 0], [0, 0, 1, 0]], NumpyLight::eye(3, 4));

        // Test case 3: N = 4, k = 1
        $this->assertEquals([[0, 1, 0, 0], [0, 0, 1, 0], [0, 0, 0, 1], [0, 0, 0, 0]], NumpyLight::eye(4, null, 1));

        // Test case 4: N = 4, k = -1
        $this->assertEquals([[0, 0, 0, 0], [1, 0, 0, 0], [0, 1, 0, 0], [0, 0, 1, 0]], NumpyLight::eye(4, null, -1));

        // Test case 5: N = 3, M = 3, k = 1
        $this->assertEquals([[0, 1, 0], [0, 0, 1], [0, 0, 0]], NumpyLight::eye(3, 3, 1));

        // Test case 6: N = 3, M = 3, k = -1
        $this->assertEquals([[0, 0, 0], [1, 0, 0], [0, 1, 0]], NumpyLight::eye(3, 3, -1));
    }

    public function testSelectRowsByIndices() {

    echo "\n TESTING....[testSelectRowsByIndices]\n";

    // Test case 1: 2x2 matrix, y_true = [0, 1]
    $this->assertEquals([[1.0, 0.0], [0.0, 1.0]], NumpyLight::select_rows_by_indices([[1.0, 0.0], [0.0, 1.0]], [0, 1]));

    // Test case 2: 3x3 matrix, y_true = [0, 2]
    $this->assertEquals([[1.0, 0.0, 0.0], [0.0, 0.0, 1.0]], NumpyLight::select_rows_by_indices([[1.0, 0.0, 0.0], [0.0, 1.0, 0.0], [0.0, 0.0, 1.0]], [0, 2]));

    // Test case 3: 4x4 matrix, y_true = [0, 0, 3]
    $this->assertEquals([[1.0, 0.0, 0.0, 0.0], [1.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 1.0]], NumpyLight::select_rows_by_indices([[1.0, 0.0, 0.0, 0.0], [0.0, 1.0, 0.0, 0.0], [0.0, 0.0, 1.0, 0.0], [0.0, 0.0, 0.0, 1.0]], [0, 0, 3]));

    // Test case 4: 3x3 matrix, y_true = [2, 2, 2]
    $this->assertEquals([[0.0, 0.0, 1.0], [0.0, 0.0, 1.0], [0.0, 0.0, 1.0]], NumpyLight::select_rows_by_indices([[1.0, 0.0, 0.0], [0.0, 1.0, 0.0], [0.0, 0.0, 1.0]], [2, 2, 2]));

    // Test case 5: 5x5 matrix, y_true = [0, 4]
    $this->assertEquals([[1.0, 0.0, 0.0, 0.0, 0.0], [0.0, 0.0, 0.0, 0.0, 1.0]], NumpyLight::select_rows_by_indices([[1.0, 0.0, 0.0, 0.0, 0.0], [0.0, 1.0, 0.0, 0.0, 0.0], [0.0, 0.0, 1.0, 0.0, 0.0], [0.0, 0.0, 0.0, 1.0, 0.0], [0.0, 0.0, 0.0, 0.0, 1.0]], [0, 4]));

    // Test case 6: 2x2 matrix, y_true = [1]
    $this->assertEquals([[0.0, 1.0]], NumpyLight::select_rows_by_indices([[1.0, 0.0], [0.0, 1.0]], [1]));
}



public function testOneHotEncode() {

    echo "\n TESTING....[testOneHotEncode]\n";

    $testCases = [
        [
            'matrix' => [[0, 1], [1, 0]],
            'y_true' => [0, 1, 0, 1],
            'expected' => [[1, 0], [0, 1], [1, 0], [0, 1]]
        ],
        [
            'matrix' => [[0, 1, 2], [2, 1, 0]],
            'y_true' => [0, 1, 2, 0],
            'expected' => [[1, 0, 0], [0, 1, 0], [0, 0, 1], [1, 0, 0]]
        ],
        [
            'matrix' => [[0, 1, 2], [2, 1, 0]],
            'y_true' => [3],
            'expected' => [[0, 0, 0]]  // Assuming 3 is not a valid label in this context
        ],
        [
            'matrix' => [[0, 1], [2, 3], [4, 5]],
            'y_true' => [0, 3, 5],
            'expected' => [[1, 0, 0, 0, 0, 0], [0, 0, 0, 1, 0, 0], [0, 0, 0, 0, 0, 1]]
        ],
        [
            'matrix' => [],  // Empty matrix
            'y_true' => [],
            'expected' => []
        ]
    ];

    foreach ($testCases as $testCase) {
        $result = NumpyLight::one_hot_encode($testCase['matrix'], $testCase['y_true']);
        $this->assertEquals($testCase['expected'], $result);
    }
}


public function testModifyOneHotEncoded() {

    echo "\n TESTING....[testModifyOneHotEncoded]\n";

    $testCases = [
        [
            'dinputs' => [[0.1, 0.2, 0.7], [0.3, 0.4, 0.3], [0.1, 0.6, 0.3], [0.7, 0.2, 0.1]],
            'y_true' => [2, 1, 1, 0],
            'valueToSubtract' => -1,
            'expected' => [[0.1, 0.2, -0.3], [0.3, -0.6, 0.3], [0.1, -0.4, 0.3], [-0.3, 0.2, 0.1]]
        ],
        // ... (add more test cases here, based on the Python output)
    ];

foreach ($testCases as $testCase) {
    $result = NumpyLight::modifyOneHotEncoded($testCase['dinputs'], $testCase['y_true'], $testCase['valueToSubtract']);
    foreach ($result as $index => $row) {
        foreach ($row as $colIndex => $value) {
            $this->assertEqualsWithDelta($testCase['expected'][$index][$colIndex], $value, 0.00001);
        }
    }
}



}

public function testCalculate() {

 echo "\n TESTING....[testCalculate in Loss ]\n";

        $output = [
            [0.7, 0.2, 0.1],
            [0.5, 0.1, 0.4],
            [0.02, 0.9, 0.08]
        ];
        $y_true = [0, 2, 1];
        
        $loss = new Loss_CategoricalCrossentropy();
        $result = $loss->calculate($output, $y_true);
        
        $this->assertEquals(0.4594, round($result, 4));
    }


public function testForward()
    {
         echo "\n TESTING....[Activation_Softmax Forward]\n";

        $softmax = new Activation_Softmax();
        $input = [
            [1, 2, 3],
            [1, 1, 1],
            [1, 1, 1]
        ];
        $softmax->forward($input);
        $output = $softmax->output;

        $expected_output = [
            [0.09003057, 0.24472847, 0.66524096],
            [0.33333333, 0.33333333, 0.33333333],
            [0.33333333, 0.33333333, 0.33333333]
        ];

        $this->assertEqualsWithDelta($expected_output, $output, 0.0001, "Forward pass does not match expected output");

    }

public function testBackward()
{
     echo "\n TESTING....[Activation_Softmax Backward]\n";
    $softmax = new Activation_Softmax();
    $input = [
        [1, 2, 3],
        [1, 1, 1],
        [1, 1, 1]
    ];
    $softmax->forward($input);
    $dvalues = [
        [-0.1, -0.1, 0.2],
        [0.0, 0.0, 0.0],
        [0.0, 0.0, 0.0]
    ];
    $softmax->backward($dvalues);
    $dinputs = $softmax->dinputs;

    $expected_dinputs = [
    [-0.01796761, -0.04884102,  0.06680863],
    [0.0, 0.0, 0.0],
    [0.0, 0.0, 0.0]
];


    $this->assertEqualsWithDelta($expected_dinputs, $dinputs, 0.0001, "Backward pass does not match expected output");

}


public function testActivation_Softmax_Loss_CategoricalCrossentropy_Forward()
{
    echo "\n TESTING....[Activation_Softmax_Loss_CategoricalCrossentropy Forward]\n";

    $act_loss = new Activation_Softmax_Loss_CategoricalCrossentropy();
    $inputs = [
        [1, 2, 3],
        [1, 1, 1],
        [1, 1, 1],
        [2, 3, 4],
        [2, 2, 2]
    ];
    $y_true = [
        [0, 0, 1],
        [0, 1, 0],
        [1, 0, 0],
        [0, 0, 1],
        [0, 1, 0]
    ];
    $calculated_loss = $act_loss->forward($inputs, $y_true);

    $expected_loss = 0.8222;  // The value we got from the Python code

    $this->assertEqualsWithDelta($expected_loss, $calculated_loss, 0.0001,"Forward pass does not match expected output");
}

public function testActivation_Softmax_Loss_CategoricalCrossentropy_Backward()
{
    echo "\n TESTING....[Activation_Softmax_Loss_CategoricalCrossentropy Backward]\n";

    $act_loss = new Activation_Softmax_Loss_CategoricalCrossentropy();
    $dvalues = [
        [0.1, 0.1, -0.2],
        [0.1, -0.2, 0.1],
        [-0.1, 0.1, 0.1],
        [0.2, 0.1, -0.3],
        [0.1, -0.1, 0.1]
    ];
    $y_true = [
        [0, 0, 1],
        [0, 1, 0],
        [1, 0, 0],
        [0, 0, 1],
        [0, 1, 0]
    ];
    $act_loss->backward($dvalues, $y_true);

    $calculated_dinputs = $act_loss->dinputs;

    $expected_dinputs = [
        [0.02, 0.02, -0.24],
        [0.02, -0.24, 0.02],
        [-0.22, 0.02, 0.02],
        [0.04, 0.02, -0.26],
        [0.02, -0.22, 0.02]
    ];  // The values we got from the Python code

    $this->assertEqualsWithDelta($expected_dinputs, $calculated_dinputs, 0.0001,"Backward pass does not match expected output");
}



}
?>
