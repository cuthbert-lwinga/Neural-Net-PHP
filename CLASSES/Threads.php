<?php
namespace NameSpaceThreads;
include_once("Headers.php");
include_once("SharedMemoryHandler.php");
include_once("NumpyLight.php");
include_once("SharedFile.php");
use NameSpaceSharedMemoryHandler\SharedMemoryHandler;
use NameSpaceNumpyLight\NumpyLight;
use NameSpaceSharedFile\SharedFile;

use Exception;

class Threads
{
    private $maxProcesses;
    private $processes;
    private $pid;
    private $parentPid;
    private $AllProcesses;
    private static $ExecutionQueue;
    private static $ExecutionQueueAllocation = [];
    private static $activeThreadsManager = null;
    private static $lastTaskThreadIndex = 0;
    private $semId;
    private static $lock = false;
    private static $live = null;
    public static $output = "output";
    public static $ipc = "ipc";
    public static $active = false;
    public function __construct($maxProcesses = 1)
    { 
        self::$activeThreadsManager = $this;
    }

    public static function init($threads = 10){
        if (self::$active) {
            echo "\nthreads active already\n";
            return;
        }
        SharedFile::initialize(Threads::$ipc,$type='w+');
        SharedFile::initialize(Threads::$output,$type='a+');//write(Threads::$output,"");
        SharedFile::write(Threads::$ipc,"");
        SharedFile::emptyFile(Threads::$output);
        self::$activeThreadsManager = new Threads();
        self::$activeThreadsManager->setRunningThreads($threads);
        self::$active = true;
    }

    public function setRunningThreads($maxProcesses = 10)
    {
       $this->parentPid = getmypid();
       $this->maxProcesses = $maxProcesses;
       $pid = $this->parentPid;
       $default_size = $this->spaceForPidStorage();
     $this->pid = SharedMemoryHandler::create('c', $default_size,$preserve_old = false); // preserve old just in case of zombie processes and should be cleared atfer init
     $this->killAllProcesses($deleteSharedMem = false); // kill old mem just in case
     $key = ftok(__FILE__, 'b');
     $this->semId = sem_get($key);
     $this->initProcesses();
     // self::$activeThreadsManager = $this;
 }



 public static function execute($left = NULL,$right = NULL,$operation = NULL){
    self::$activeThreadsManager->acquireLock();
    while(self::$lock){
        usleep(100);
    }
    self::$lock = true;
    SharedFile::emptyFile(Threads::$output);
    SharedFile::write(Threads::$ipc,"");

    $data =  self::$activeThreadsManager->distributeMatrixOperation($left, $right, $operation);
    $row = unserialize($data)["output"];

    $ordered = self::getSavedOutPut();
    $i = 0;
    
    while(count($ordered) < $row){
        usleep(100);
        echo "\n".count($ordered)." < ".$row ." contition "."\n";
        // var_dump(self::threadsBusy());
        $i += 1;
        $ordered = self::getSavedOutPut();
    }
    
    echo "\n\noutput\n\n";
    var_dump($ordered);

    //return [];
    self::$lock = false;
    self::$activeThreadsManager->releaseLock();
    return $ordered;
}


private static function threadsBusy(){
        return count(self::getSavedOutPut()) < 1;
}

private static function getSavedOutPut(){
    
    $output = explode("@",SharedFile::read(Threads::$output));

    $ordered = [];
    foreach ($output as $serializedData) {
        if ($serializedData != ""){
                $dataArray = unserialize($serializedData);
                // var_dump($dataArray);
        
                foreach ($dataArray as $index => $floatValues) {
                    // echo "Inner Index: $index, Value: \n";
                    $ordered[$index] = $floatValues;
                }
        }
    }
    ksort($ordered);
    return $ordered;
}


public function distributeMatrixOperation($left = NULL,$right = NULL,$operation = NULL){

    $data =  $this->prepareMatrixOperation($left, $right, $operation);

    SharedFile::write(Threads::$ipc,$data);


    return $data;
}

private function distributeRowsAmongProcesses($totalRows) {
    $numProcesses = $this->maxProcesses;
    $distribution = [];

    if ($numProcesses == 0) {
        throw new Exception("No available socket clients for distribution.");
    }

    // Distribute the initial rows
    for ($i = 0; $i < $numProcesses; $i++) {
        $distribution[] = [];
    }

    // Distribute the initial rows
    for ($i = 0; $i < $totalRows; $i++) {
        $distribution[($i%$numProcesses)][] = $i;
    }

    return $distribution;
}


private function prepareMatrixOperation($left = NULL, $right = NULL, $operation = NULL) {
    $shapeA = NULL;
    $shapeB = NULL;

    // Validate and get shapes, if the operand is a matrix
    if (is_array($left)) {
        $shapeA = NumpyLight::shape($left);
    }

    if (is_array($right)) {
        $shapeB = NumpyLight::shape($right);
    }

    $type = NULL;
    $row = true;  // Default to true, but will be set based on operation and shape check
    $distribution = [];
    $output = 0;
    // Validate the operation and shapes
    switch ($operation) {
        case 'dot':
            // Check if left matrix rows are more than right matrix columns
        if (is_array($left) && is_array($right) && isset($shapeA[0]) && isset($shapeB[1]) && ($shapeA[1] == $shapeB[0])) {
            $type = 'matrix-matrix';
                $row = ($shapeA[0] >= $shapeB[1]); // Set $row based on shapes
                $output = $row? $shapeA[0] : $shapeB[1];
                $distribution = $this->distributeRowsAmongProcesses($row ? $shapeA[0] : $shapeB[1]);
            } else {
                throw new Exception("Invalid shapes for dot product.");
            }
            break;
            case 'add':
            case 'subtract':
            if (is_array($left) && is_array($right) && $shapeA == $shapeB) {
                $type = 'matrix-matrix';
                $row = ($shapeA[0] >= $shapeB[1]); // Set $row based on shapes
                $distribution = $this->distributeRowsAmongProcesses($row ? $shapeA[0] : $shapeB[1]);
            } elseif (is_array($left) && is_numeric($right)) {
                $type = 'matrix-scalar';
                $row = ($shapeA[0] >= $shapeA[1]); // Set $row based on shapes
                $distribution = $this->distributeRowsAmongProcesses($row ? $shapeA[0] : $shapeA[1]);
            } else {
                throw new Exception("Invalid operands for addition/subtraction.");
            }
            break;
            case 'multiply':
            if (is_array($left) && is_numeric($right)) {
                $type = 'matrix-scalar';
                $row = ($shapeA[0] >= $shapeA[1]); // Set $row based on shapes
                $distribution = $this->distributeRowsAmongProcesses($row ? $shapeA[0] : $shapeA[1]);
            } else {
                throw new Exception("Invalid operands for multiplication.");
            }
            break;
            default:
            throw new Exception("Invalid or unspecified operation.");
        }

        $id = self::generateunid();

        $data = serialize([
            "id" => $id, 
            "left" => $left, 
            "right" => $right, 
            "operation" => $operation, 
            "type" => $type,
            "row" => $row,
            "output" => $output,
            "distribution" => $distribution
        ]);

        return $data;
    }

    
    public function __destruct(){
        echo "\n****destroyed****\n";
        SharedFile::emptyFile(Threads::$ipc);
        SharedFile::close(Threads::$ipc);
        SharedFile::emptyFile(Threads::$output);
        SharedFile::close(Threads::$output);
        $this->killAllProcesses($deleteSharedMem = true); // kill old mem just in case
    }

    public function allocateExecutionQueue(){
        $i = 0;
        $threads = $this->maxProcesses; // no upper bound
        
        foreach(Threads::$ExecutionQueue as $key => $value){
            if(isset(Threads::$ExecutionQueueAllocation[$i%$threads])){
                Threads::$ExecutionQueueAllocation[$i%$threads][] = $key;
            }else{
                Threads::$ExecutionQueueAllocation[$i%$threads] = [$key];
            }
            $i++;
        }
        $this->maxProcesses = count(Threads::$ExecutionQueueAllocation);
    }

    public static function generateunid(){
        return uniqid('task_', true);
    }

    private function mem($shmId){
        $data = SharedMemoryHandler::read($shmId);
        return $data;
    }

    private function spaceForPidStorage(){
        $B = [];
        $pid = getmypid();
        for ($i=0; $i < $this->maxProcesses; $i++){
           $B[] = ["$pid"=>["idle"=>true,"process"=>NULL]];
       }

       $B = strlen(serialize($B));
       $B = $B*2;
       return $B;
   }

   private function enqueue($shmId,$data){
    $this->acquireLock();
    $mem = $this->memToArray($shmId);
    $mem[] = $data;
    SharedMemoryHandler::write($shmId,serialize($mem));
    $this->releaseLock();
}


private function memToArray($shmId){
    $data = $this->mem($shmId);
    if ($data == NULL) {
        return [];
    }
        // var_dump($data);
    $data = unserialize($data);
    return $data;
}

private function initProcesses() {

    for ($i = 0; $i < $this->maxProcesses; $i++) {
        $pid = $this->addBackgroundProcess();
    }

    return $this->runningProcesses();

}

private function acquireLock() {
    sem_acquire($this->semId);
}

private function releaseLock() {
    sem_release($this->semId);
}

private function runningProcesses(){
    $processes = [];
    $mem = $this->memToArray($this->pid);        
    if ($mem){
        for ($i=0; $i < count($mem); $i++){
            foreach ($mem[$i] as $key=>$value) {
                $processes[$key] = $value;
            }
        }
    }
    return $processes;
}

private function updateRunningProcessesState($pid,$isIdle=true,$process=''){
    $this->acquireLock();
    $processes = [];
    $mem = $this->runningProcesses();
        // var_dump($mem);
    if (isset($mem[$pid])) {
        $mem[$pid]["idle"] = $isIdle;
        $mem[$pid]["process"] = $process;
    }

    SharedMemoryHandler::write($this->pid,serialize($mem));

    $this->releaseLock();

    return $processes;
}

private function addBackgroundProcess(){

    $temp = [];
    $pid = $this->createProcess();

    if ($pid < 1) {
        return;
    }

    $data = ["$pid"=>["idle"=>true,"process"=>NULL]];
    $this->enqueue($this->pid,$data);
    return $pid;
}


private function createProcess() {
    $pid = pcntl_fork();
    if ($pid == -1) {
            return -1; //Could not fork.
        } elseif ($pid) {
            return $pid; // child process PID
        } else {
            $this->listenForTaskAllocation(); // child listener for matrix operation 
            exit(0);
        }
    }


    public static function addTask(string $functionName,array $parameters) {
        $taskKey = self::generateunid();
        self::$ExecutionQueue[$taskKey] = ["function"=>$functionName,"parameters"=>$parameters];
    }

    private function processindex($pid) {
        $mem = $this->runningProcesses();
        $i = 0;
        foreach ($mem as $key => $value) {
        // Debugging output

            if ($key == $pid) {
                return $i;
            }
            $i++;
        }
        return NULL;
    }


    private function isProcessRunning($pid) {
        return posix_getpgid($pid) !== false;
    }

    private static function getSharedData(){
        $readData = SharedFile::read(Threads::$ipc);
        if ($readData !== "") {
            $readData = unserialize($readData);
        }

        return $readData;
    }

    public function listenForTaskAllocation() {
        $pid = getmypid();
        $index = NULL;
        $printed = false;
        $active = false;
        $readData = NULL;
        $Output = [];
        while (true) {

            if ($index === NULL){
                $index = $this->processindex($pid);
                continue;
            }



            if ($readData == NULL){
                $readData = SharedFile::read(Threads::$ipc);
                if ($readData !== "") {
                    $readData = unserialize($readData);
                    if(count($readData["distribution"][$index])>0){
                        $active = true;
                    }
                }
            }

            if (!$active) {
                continue;
            }



            if ($readData) {
             if (isset($readData["distribution"][$index])){ 
                
                // echo "\nGOOD! ".count($readData["distribution"][$index])."\n";
                $leftMatrix = $readData["left"];
                $rightMatrix = $readData["right"];
                $row = $readData["row"];

                if(count($readData["distribution"][$index])>0){

                    $rowIndex = $readData["distribution"][$index][0];
                    unset($readData["distribution"][$index][0]);
                    $readData["distribution"][$index] = array_values($readData["distribution"][$index]);

                }else{

                            // queue empty reload data and start over if output not empty post to parent

                    if (!$printed) {
                        SharedFile::write(Threads::$output,serialize($Output)."@");
                        $printed = true;
                    }else{
                        // echo "\nCHECKING FOR NEW\n";
                    }
                    
                    $tempData = Threads::getSharedData();

                    if ($tempData && $tempData != "") {
                        if ($tempData["id"] != $readData["id"]) {

                                $index = NULL;
                                $printed = false;
                                $active = false;
                                $readData = NULL;
                                $Output = [];

                                continue;
                        
                        }else{
                            // echo "\nsame task as before. ".$tempData["id"]." != ".$readData["id"]."\n";
                        }
                    }else{
                        // echo "\ndata retived is empty\n";
                    }

                    continue; 

                }


                switch ($readData["operation"]) {
                    case 'dot':
                    if ($rowIndex>-1){            // Handle dot product operation
                        // echo "\n$index  doing $rowIndex\n";

                        // var_dump($readData["distribution"]);
                         // echo "\n\n\n\n THREAD \n\n\n\n";
                        $Output[$rowIndex] = NumpyLight::nthRowDotProduct($leftMatrix, $rightMatrix, $rowIndex);
                        
                        // var_dump($Output[$rowIndex]);

                    }
                    break;
                    case 'add':
                                // Handle addition operation
                        throw new Exception("Unsupported operation: " . $readData["operation"]);

                    break;
                    case 'subtract':
                                // Handle subtraction operation
                        throw new Exception("Unsupported operation: " . $readData["operation"]);

                    break;
                    case 'multiply':
                                // Handle multiplication operation
                        throw new Exception("Unsupported operation: " . $readData["operation"]);
                    break;
                    default:
                                // Handle other cases or throw an exception
                    throw new Exception("Unsupported operation: " . $readData["operation"]);
                }

                        // Now you can continue with the rest of your code, checking for thread allocation, etc.
            } 
        }



    }

}


public function killAllProcesses($deleteSharedMem = true) {
        // only parent can
    if($this->parentPid == getmypid()){
        $processes = $this->runningProcesses();
        
        foreach ($processes as $pid => $value) {

            if($this->isProcessRunning($pid)){
                // echo "\nKILLED $pid\n";
                        posix_kill($pid, SIGTERM); // kill signal
                    }
                }

                // SharedMemoryHandler::write($this->pid, "");
                if ($deleteSharedMem) {
                    SharedMemoryHandler::delete($this->pid);
                    SharedMemoryHandler::close($this->pid);
                    SharedMemoryHandler::clearMemory($this->pid);
                }

            }
        }

        private function uniqueShuffledKey() {
    static $generated = [];  // Array to store previously generated strings

    $alphabet = range('a', 'z');
    do {
        shuffle($alphabet);
        $shuffled = implode('', $alphabet);
    } while (in_array($shuffled, $generated));  // Regenerate if duplicate

    $generated[] = $shuffled;  // Store the new unique string
    return $shuffled;
}



}



?>
