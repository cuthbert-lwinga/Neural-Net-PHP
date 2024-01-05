<?php
namespace NameSpaceThreads;
include_once("SharedMemoryHandler.php");
use NameSpaceSharedMemoryHandler\SharedMemoryHandler;
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
    private static $activeProcesses = 20000;
    private $semId;

    public function __construct($maxProcesses = 1)
    {
        if (empty(self::$ExecutionQueue)) {
         throw new \Exception("Nothing to execute, add functions to run on multi thread.");
         return;
     }

     $this->parentPid = getmypid();
     $pid = $this->parentPid;
     $this->maxProcesses = $maxProcesses;
     $this->allocateExecutionQueue();
    //5000;
     $default_size = $this->spaceForPidStorage();//$this->maxProcesses*strlen(serialize(["$pid"=>["idle"=>true,"process"=>NULL]]));
     $this->pid = SharedMemoryHandler::create('c', $default_size,$preserve_old = false);//preserve old just in case of zombie processes and should be cleared atfer init
     $this->killAllProcesses($deleteSharedMem = false);
     $key = ftok(__FILE__, 'b');
     $this->semId = sem_get($key);
     $this->initProcesses();
     
     }

     public static function run($threads = 2,$waitForoutput = true){
        $Threads = new Threads($threads);

        if ($waitForoutput){
            $Threads->waitForAllProcessesToFinish();
            Threads::$ExecutionQueueAllocation = [];
            Threads::$ExecutionQueueAllocation = [];
        }else{
            return $Threads;
        }
    }
    public function __destruct(){
        // Delete and close the shared memory block
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
        return $B;
    }

    private function enqueue($shmId,$data){
        $this->acquireLock();
        $mem = $this->memToArray($shmId);
        $mem[] = $data;
        // echo "\nserialize ".strlen(serialize($mem))."\n";
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
            $this->listenForTaskAllocation(); // child listens
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

    public function listenForTaskAllocation() {
        $pid = getmypid();
        $index = NULL;
        $printed = false;

        while (true) {

            if ($index === NULL){
                $index = $this->processindex($pid);
                continue;
            }
            // echo "\n\nindex $index\n\n";
            // var_dump(Threads::$ExecutionQueueAllocation);
            if (count(Threads::$ExecutionQueueAllocation[$index])<1){
                posix_kill($pid, SIGTERM); // kill thread, tasks allocated finished
            break; // finished 
        }


        $task = Threads::$ExecutionQueueAllocation[$index][0];
        unset(Threads::$ExecutionQueueAllocation[$index][0]);
        Threads::$ExecutionQueueAllocation[$index] = array_values(Threads::$ExecutionQueueAllocation[$index]);

        if (isset( self::$ExecutionQueue[$task])) {
            call_user_func_array(self::$ExecutionQueue[$task]['function'],  self::$ExecutionQueue[$task]['parameters']);
        }

    }

}


public function killAllProcesses($deleteSharedMem = true) {
        // only parent can
    if($this->parentPid == getmypid()){
        $processes = $this->runningProcesses();
        
        foreach ($processes as $pid => $value) {

            if($this->isProcessRunning($pid)){
                        posix_kill($pid, SIGTERM); // kill signal
                    }
                }

                SharedMemoryHandler::write($this->pid, "");
                if ($deleteSharedMem) {
                    SharedMemoryHandler::delete($this->pid);
                    SharedMemoryHandler::close($this->pid);
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



        public function waitForAllProcessesToFinish() {
        // only parent can
            if($this->parentPid == getmypid()){
                $processes = $this->runningProcesses();
                foreach ($processes as $pid => $value) {
                    while($this->isProcessRunning($pid)){
                        usleep(1000);
                    }
                }
            }
            $this->killAllProcesses($deleteSharedMem = true);
        }

        public function activeLiveThreads() {
        // only parent can
            if($this->parentPid == getmypid()){
                $processes = $this->runningProcesses();
                foreach ($processes as $pid => $value) {
                    while($this->isProcessRunning($pid)){
                        return true;
                    }
                }
            }
            return false;
        }


    }



    ?>
