<?PHP
namespace NameSpaceMT_Matrix;
include_once("SharedMemoryHandler.php");
include_once("Threads.php");
include_once("SocketServer.php");
use NameSpaceThreads\Threads;
use NameSpaceSocketServer\SocketServer;
use Exception;

class MT_Maxtrix{

    private static $socketIndex = -1;
    public $output = [];
    private $identifire = "";

    public function __destruct(){
        // echo "\n--DESTROYED--\n";
    }

    // Other functions
    public function __construct(){
       $this->identifire = $this->generateUniqueId();
    }

    public function generateUniqueId() {
    static $generatedIds = array();  // Static array to hold generated IDs

    do {
        // Use microtime for better seeding and md5 for 32 characters
        $id = md5(uniqid(microtime() . mt_rand(), true));

        // Check if the generated ID is already in the array of generated IDs
        $isUnique = !in_array($id, $generatedIds);
    } while (!$isUnique); // Repeat if the ID is not unique

    // Add the unique ID to the array of generated IDs
    $generatedIds[] = $id;

    return $id;
}


    public function dot($matrixA,$matrixB,$threads = 6){

        $shapeA = self::shape($matrixA);
        $shapeB = self::shape($matrixB);

        // if (end($shapeA) !== $shapeB[0]) {
        //     throw new Exception("Shapes " . implode(",", $shapeA) . " and " . implode(",", $shapeB) . " not aligned.");
        // }

        // $this->output = array_fill(0, $shapeA[0], array_fill(0, $shapeB[1], null));
        $this->output = $this->null_like([$shapeA[0],$shapeB[1]]);;
        
        // Perform dot product row by row
        
        // if ($shapeA[0] > $shapeB[1]){

            for ($i = 0; $i < $shapeA[0]; $i++) {
                $rowIndex = $i;
                $extractedRow = self::extractRow($matrixA, $rowIndex);
                $args = [$rowIndex, $extractedRow, $matrixB,$this->identifire];
                Threads::addTask('NameSpaceMT_Matrix\MT_Maxtrix::performRowDotProductAndStore', $args);
            }

        // }else{

        //     for ($i = 0; $i < $shapeB[1]; $i++) {
        //         $cowIndex = $i;
        //         $extractedCol = self::extractColumn($matrixB, $cowIndex);
        //         $args = [$cowIndex, $matrixA, $extractedCol];
        //         Threads::addTask('NameSpaceMT_Matrix\MT_Maxtrix::performColDotProductAndStore', $args);
        //     }
        // }



        self::$socketIndex = SocketServer::init();
        
        $Threads = Threads::run($threads,$waitForoutput = false); 

        // Parent process: Start the server after all children are forked
        $damp = 0;
        $type = NULL;
        while (true) {

            $sockets = SocketServer::acceptClients(self::$socketIndex);
            $msgs = SocketServer::readFromClients($sockets);

            if (!empty($msgs)) {
                $type = $this->matrixType($msgs);
                if ($type=="COL") {
                    $this->addColData($msgs);
                }else{
                    $this->addRowData($msgs);
                }
            }

            if (!$Threads->activeLiveThreads()&&$damp > 4 ) {
                    break; // Break the loop 
                }

                if (!$Threads->activeLiveThreads()) {
                    $damp++;
                }

                usleep(10);

        }
        

        $Threads->killAllProcesses($deleteSharedMem = true);

        if ($type=="COL") {
            $this->output[$this->identifire] = $this->transpose($this->output[$this->identifire]);
        }

        $return = $this->output[$this->identifire];
        $this->output = [];
        return $return;
    }

    public function null_like($shape){
        $temp = [];

        for ($i=0; $i < $shape[0]; $i++) {
            $row = []; 
            for ($j=0; $j < $shape[0]; $j++) { 
                $row[] = [NULL];
            }
            $temp[] = $row;
        }

        return $temp;
        
    }

    public function add($matrixA,$matrixB){

        $shapeA = self::shape($matrixA);
        $shapeB = self::shape($matrixB);

        if ($shapeA !== $shapeB) {
            throw new Exception("Shapes " . implode(",", $shapeA) . " and " . implode(",", $shapeB) . " not aligned.");
        }

        $this->output = array_fill(0, $shapeA[0], array_fill(0, $shapeA[1], null));
        
        // Perform dot product row by row
        
        if ($shapeA[0] > $shapeA[1]){

            for ($i = 0; $i < $shapeA[0]; $i++) {
                $rowIndex = $i;
                $extractedRowA = self::extractRow($matrixA, $rowIndex);
                $extractedRowB = self::extractRow($matrixB, $rowIndex);
                $args = [$rowIndex, $extractedRowA, $extractedRowB,true];
                //performRowPlusMinusAndStore($rowIndex, $array1, $array2,$plus=true)
                Threads::addTask('NameSpaceMT_Matrix\MT_Maxtrix::performRowPlusMinusAndStore', $args);
            }

        }else{

            for ($i = 0; $i < $shapeB[1]; $i++) {
                $colIndex = $i;
                $extractedColA = self::extractRow($matrixA, $colIndex);
                $extractedColB = self::extractRow($matrixB, $colIndex);
                $args = [$colIndex, $extractedColA, $extractedColB,true];
                Threads::addTask('NameSpaceMT_Matrix\MT_Maxtrix::performColPlusMinusAndStore', $args);
            }
        }

        $socketIndex = SocketServer::init();
        
        // die("dead");
        $Threads = Threads::run($threads = 100,$waitForoutput = false); 
        // Parent process: Start the server after all children are forked
        
        $damp = 0;
        $type = NULL;
        while (true) {

            $sockets = SocketServer::acceptClients();
            $msgs = SocketServer::readFromClients($sockets);

            if (!empty($msgs)) {
                $type = $this->matrixType($msgs);
                if ($type=="COL") {
                    $this->addColData($msgs);
                }else{
                    $this->addRowData($msgs);
                }
            }

            if (!$Threads->activeLiveThreads()&&$damp > 1 ) {
                    break; // Break the loop 
                }

                if (!$Threads->activeLiveThreads()) {
                    $damp++;
                }

                usleep(100);

        }

        $Threads->killAllProcesses();

        if ($type=="COL") {
            // echo "coll";
            $this->output = $this->transpose($this->output);
        }

        return $this->output;

    }


public function transpose($array) {
    $transposed = [];
    foreach ($array as $rowKey => $row) {
        foreach ($row as $colKey => $cell) {
            $transposed[$colKey][$rowKey] = $cell;
        }
    }
    return $transposed;
}

public function matrixType($dataArray) {
    foreach ($dataArray as $dataString) {
        if (preg_match('/R\[\d+,\w+\]=>.+/', $dataString)) {
            return "ROW";
        } elseif (preg_match('/C\[\d+,\w+\]=>.+/', $dataString)) {
            return "COL";
        }
    }

    // Default return type if no match is found
    return null;
}



public function addRowData($dataArray) {
    foreach ($dataArray as $dataString) {
        // Adjusted pattern to match R[$rowIndex,$identifier]=>rowData
        if (preg_match('/R\[(\d+),(\w+)]=>(.+)/', $dataString, $matches)) {
            $rowIndex = $matches[1];
            $identifier = $matches[2];
            $rowData = explode(',', $matches[3]);

            // Convert data to the appropriate type
            $rowData = array_map(function($item) {
                return is_numeric($item) ? $item + 0 : $item;
            }, $rowData);

            // Store the exploded rowData in output with identifier and rowIndex as keys
            $this->output[$identifier][$rowIndex] = $rowData;
        }
    }
}



public function addColData($dataArray) {
    foreach ($dataArray as $dataString) {
        // Adjusted pattern to match C[$colIndex,$identifier]=>columnData
        if (preg_match('/C\[(\d+),(\w+)]=>(.+)/', $dataString, $matches)) {
            $colIndex = $matches[1];
            $identifier = $matches[2];
            $columnData = explode(',', $matches[3]);

            // Convert data to the appropriate type
            $columnData = array_map(function($item) {
                return is_numeric($item) ? $item + 0 : $item;
            }, $columnData);

            // Store the exploded columnData in output with identifier and colIndex as keys
            $this->output[$identifier][$colIndex] = $columnData;
        }
    }
}



    public static function performRowDotProductAndStore($rowIndex, $extractedRow, $rightMatrix,$ident) {
        $startTime = microtime(true); // End time
        $shapeRightMatrix = self::shape($rightMatrix);
        $rowData = [];

        for ($colIndex = 0; $colIndex < $shapeRightMatrix[1]; $colIndex++) {
            $column = self::extractColumn($rightMatrix, $colIndex);
            $dotProductResult = self::dotProduct($extractedRow, $column);
            $rowData[] = $dotProductResult; // Add the result to the rowData array
        }
        
        // do call here to socket and wait till available
        $rowData = implode(",", $rowData);
        // $ident = $this->identifire;
        $clientSocket = SocketServer::createAndConnectSocket(self::$socketIndex,$timeout = 60); // will timeout after 60 seconds
        SocketServer::writeToSocket($clientSocket, "R[$rowIndex,$ident]=>$rowData");
        SocketServer::close($clientSocket);

    }


 public static function performColDotProductAndStore($colIndex,$leftMatrix, $extractedCol,$ident) {
        $shapeRightMatrix = self::shape($leftMatrix);
        // var_dump(count($extractedCol));
        $columnData = [];
        for ($rowIndex = 0; $rowIndex < $shapeRightMatrix[0]; $rowIndex++) {
            $extractedRow = self::extractRow($leftMatrix, $rowIndex);
            $dotProductResult = self::dotProduct($extractedRow, $extractedCol);
            $columnData[] = $dotProductResult; // Add the result to the columnData array
        }

        // do call here to socket and wait till available
        $columnData = implode(",", $columnData);
        $ident = $this->identifire;
        $clientSocket = SocketServer::createAndConnectSocket(self::$socketIndex,$timeout = 60); // will timeout after 60 seconds
        SocketServer::writeToSocket($clientSocket, "C[$colIndex,$ident]=>$columnData");
        SocketServer::close($clientSocket);

    }

    public static function performRowPlusMinusAndStore($rowIndex, $array1, $array2,$plus=true) {
        $rowData = self::sumOrMinus($array1, $array2,$plus);
        // do call here to socket and wait till available
        $rowData = implode(",", $rowData);
        $ident = $this->identifire;
        $clientSocket = SocketServer::createAndConnectSocket(self::$socketIndex,$timeout = 60); // will timeout after 60 seconds
        SocketServer::writeToSocket($clientSocket, "R[$rowIndex,$ident]=>$rowData");
        SocketServer::close($clientSocket);
    }


 public static function performColPlusMinusAndStore($colIndex,$array1, $array2,$plus=true) {
        $columnData = self::sumOrMinus($array1, $array2,$plus);
        // do call here to socket and wait till available
        $columnData = implode(",", $columnData);
        $ident = $this->identifire;
        $clientSocket = SocketServer::createAndConnectSocket(self::$socketIndex,$timeout = 60); // will timeout after 60 seconds
        SocketServer::writeToSocket($clientSocket, "C[$colIndex,$ident]=>$columnData");
        SocketServer::close($clientSocket);

    }

    public static function extractRow($matrix, $i) {
    // Extract the ith row from matrix
    $row = $matrix[$i];
    return $row;
}

    public static function extractColumn($matrix, $i) {
    // Extract the ith row from matrix
    $column = array_column($matrix, $i);

    return $column;
}



public static function dotProduct($array1, $array2) {
    $sum = 0;
    $length = count($array1);
    for ($i = 0; $i < $length; $i++) {
        $sum += $array1[$i] * $array2[$i];
    }
    return $sum;
}

public static function sumOrMinus($array1, $array2,$plus=true) {
    $sum = [];
    $length = count($array1);
    for ($i = 0; $i < $length; $i++) {
        if($plus){
            $sum[$i]= ($array1[$i] + $array2[$i]);
        }else{
            $sum[$i]= ($array1[$i] - $array2[$i]);
        }
    }
    return $sum;
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



}




?>