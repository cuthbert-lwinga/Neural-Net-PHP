<?PHP
include_once("../CLASSES/headers.php");
use MathOperations as np;



$zerosMatrix = np::zeros(3,1);

$data_hori = array(
  array(2.0, 3.0, 0.5)
);

$data_hori  = np::transform($data_hori);

$data = array(
  array(1, 2, 3),
  array(4, 5, 6)
);

$data2 = array(
  array(2.8, -1.79, 1.885),
  array(6.9, -4.8, -0.3),
  array(-0.59, -1.949, -0.474)
);


//$dot = np::dot($data,$zerosMatrix);
//$dot = np::m_operator($dot,"+",0);
//$dot = np::rand(4,5,-0.1,0.1);


//$result1 = np::m_operator($data, '+', 2); // Add 2 to each element of matrix1
$result2 = np::m_operator($data2, '+', $data_hori); // Perform element-wise subtraction between matrix1



var_dump($zerosMatrix)
// $data = array(
//   array(1, 2, 3),
//   array(4, 5, 6)
// );
// $matrix1 = MatrixOperations::createFromData($data);
// $matrix1Data = $matrix1->getMatrix();
// var_dump($matrix1Data);

// // Create another 3 * 2 matrix
// $matrix2 = new MatrixOperations(3, 2);
// $matrix2Data = $matrix2->getMatrix();
// var_dump($matrix2Data);

// // Perform dot product
// $result = $matrix1->dotProduct($matrix2Data);
// var_dump($result);

// // Perform matrix transformation
// $transformed = $matrix1->transform();
// var_dump($transformed);


?>