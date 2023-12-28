<?php
namespace ProcessManager;
include_once("Headers.php");
use NameSpaceQueue\Queue;
use NameSpaceArrayFileHandler\ArrayFileHandler;
use NameSpaceTaskRegistry\TaskRegistry;

class ProcessManager
{
    private $maxProcesses;
    private $processes;
    private $workerPool;
    private $AllProcesses;
    private $tasks;
    private $terminate = false;

    public function __construct($maxProcesses = 2)
    {
        $this->maxProcesses = $maxProcesses;
        $this->processes = new Queue("processesQueued");
        $this->workerPool = new Queue("workerPools".uniqid());
        $this->AllProcesses = new Queue("AllProcesses");
        $this->tasks = new TaskRegistry('tasks');
        $this->initializeWorkerPool();
    }


    private function initializeWorkerPool() {
         
        for ($i = 0; $i < $this->maxProcesses; $i++) {
            $temp = [];
            $pid = $this->createWorkerProcess();

            if ($pid < 1) {
                break;
            }

            $temp[$pid] = ["idle"=>true, "callback"=>null];
            $this->workerPool->enqueue($temp);
        }
        
    }

    private function createWorkerProcess() {
        $pid = pcntl_fork();
        if ($pid == -1) {
           // die("Could not fork.");
            return -1;
        } elseif ($pid) {
            // Parent process
            return $pid;
        } else {
            // Child process
            //echo "\n---->> hello\n";
            $this->listenForTaskAllocation();
            exit(0);
        }
    }
 

public function addTask(string $functionName,$parameters) {

    $taskKey = $this->tasks->generateUniqueKey();
    $this->tasks->addTask($taskKey, $functionName, $parameters);
    $this->processes->enqueue($taskKey);
    $this->AllProcesses->enqueue($taskKey);
}

public function listenForTaskAllocation() {
    
    while (true) {

        if ($this->processes->isEmpty()) {
            continue;
        }


        $pid = getmypid();

        $workerPool = $this->workerPool->getAllAssociativeArrays();

        $currentWorker = $workerPool[$pid] ?? null;

        if ($currentWorker && !$currentWorker["idle"]) {
            // The worker is not idle, skip to the next iteration
            // echo ("currentWorker is not idle $pid $currentWorker  \n");
            continue;
        }
        

        $taskKey = $this->processes->dequeue();
        // echo ">> $taskKey\n\n";
        if ($taskKey) {
        //     // Update the worker state to busy
            $taskData = $this->tasks->getTask($taskKey);

            $temp = [];
            $temp[$pid] = ["idle"=>false, "callback"=> $taskKey];
            $this->workerPool->removeItem($pid);
            $this->workerPool->enqueue($temp);

            if ($taskData !== null) {
                call_user_func_array(($taskData['function']), $taskData['arguments']);
                // $this->tasks->removeTask($taskKey);
            }

            // Update the worker state to idle
            $temp = [];
            $temp[$pid] = ["idle"=>true, "callback"=> NULL];
            $this->workerPool->removeItem($pid);
            $this->workerPool->enqueue($temp);

        }

        $this->AllProcesses->dequeue();

        if ($this->shouldTerminate()) {
            //echo "terminate";
            break;
        }
    
}
}

    
    public function shouldTerminate(){
        return $this->terminate;
    }

    public function terminate($terminate = true){
        $this->terminate = $terminate;
    }

    private function workingThreads(){
        $idleWorkerCount = 0;

        foreach ($this->workerPool as $pid => $worker) {
            if ($worker['idle'] === false) {
                $idleWorkerCount++;
            }
        }
        return $idleWorkerCount;
    }

    public function cleanup()
    {
        $workerPool = $this->workerPool->getAllAssociativeArrays();
        foreach ($workerPool as $pid => $worker) {
            if (pcntl_waitpid($pid, $worker['idle'], WNOHANG) != 0) {
                unset($this->workerPool[$pid]);
            }
        }
    }

    public function killProcesses() {
    $workerPool = $this->workerPool->getAllAssociativeArrays();

    foreach ($workerPool as $pid => $worker) {
        if (isset($worker['idle']) && $worker['idle'] === true) {
            // If the 'idle' flag is true, send a termination signal
            posix_kill($pid, SIGTERM); // or another appropriate signal
            // echo "\nKILLED[$pid]\n";
            // Remove the process from the worker pool
            $this->workerPool->removeItem($pid);
        }
    }

    $this->workerPool->destroy();
    $this->processes->destroy();
    $this->AllProcesses->destroy();
    $this->tasks->destroy();
}

public function waitForAllProcesses() {

    $mili = 0;

    do {
        $allIdle = true;
        $workerPool = $this->workerPool->getAllAssociativeArrays();

        foreach ($workerPool as $pid => $worker) {
            if (!$worker['idle']) {
                $allIdle = false;
                // echo "Waiting for worker $pid to become idle...\n";
                break; // If any worker is not idle, continue checking
            }
        }


//|| !$this->AllProcesses->isEmpty()
        if (!$allIdle || !$this->processes->isEmpty() || !$this->AllProcesses->isEmpty()) {
            // if (!$allIdle) {
            //     echo "Waiting for all workers to become idle...\n";
            // } else if (!$this->processes->isEmpty()) {
            //     echo "Waiting for 'processes' queue to empty...\n";
            // } else if (!$this->AllProcesses->isEmpty()) {
            //     echo "Waiting for 'AllProcesses' queue to empty...\n";
            // }
            usleep(100); // Sleep for 10 milliseconds
            $mili += 100;
        }

        //|| !$this->AllProcesses->isEmpty()
    } while (!$allIdle || !$this->processes->isEmpty() || !$this->AllProcesses->isEmpty());
$seconds = $mili / 1000000;

    echo "\n\nAll processes completed waited($seconds)\n\n";
}





}

// Example
// $processManager = new ProcessManager();

// for ($i = 0; $i < 100; $i++) {
//     $processManager->run(function() use ($i) {
//         // Replace this with your actual task
//         echo "Running task $i\n";
//         sleep(rand(1, 3)); // Simulating a task
//     });
// }

// $processManager->waitForAllProcesses();
// echo "All processes have completed.\n";
// class ProcessManager
// {
//     private $maxProcesses;
//     private $currentProcesses;

//     public function __construct($maxProcesses = 50)
//     {
//         $this->maxProcesses = $maxProcesses;
//         $this->currentProcesses = [];
//     }

//     public function run(callable $function)
//     {
//         $this->cleanup();

//         if (count($this->currentProcesses) >= $this->maxProcesses) {
//             echo "Maximum number of processes reached. Waiting...\n";
//             $this->waitForProcesses();
//         }

//         $pid = pcntl_fork();
//         if ($pid == -1) {
//             die("Could not fork.");
//         } elseif ($pid) {
//             // Parent process
//             $this->currentProcesses[$pid] = true;
//         } else {
//             // Child process
//             call_user_func($function);
//             exit(0);
//         }
//     }

//     private function cleanup()
//     {
//         foreach ($this->currentProcesses as $pid => $status) {
//             if (pcntl_waitpid($pid, $status, WNOHANG) != 0) {
//                 unset($this->currentProcesses[$pid]);
//             }
//         }
//     }

// private function waitForProcesses()
// {
//     while (true) {
//         $this->cleanup();

//         if (count($this->currentProcesses) < $this->maxProcesses) {
//             break;
//         }
//     }
// }


//     public function waitForAllProcesses()
//     {
//         while (!empty($this->currentProcesses)) {
//             sleep(1);
//             $this->cleanup();
//         }
//     }
// }

?>
