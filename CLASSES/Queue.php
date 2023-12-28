<?PHP
namespace  NameSpaceQueue;
include_once("Headers.php");
use NameSpaceArrayFileHandler\ArrayFileHandler;

class Queue {
    private $semId;
    private $prefix = 'queue_'; // Prefix for the file

    public function __construct($prefix = 'queue_') {
        $this->prefix = $prefix;
        $this->initializeStorage();
        $key = ftok(__FILE__, 'b');
        $this->semId = sem_get($key);
    }


    public function destroy(){
        ArrayFileHandler::removeFile($this->prefix);
    }

    private function initializeStorage() {
        ArrayFileHandler::initialize($this->prefix);
    }

public function enqueue($item) {
    $this->acquireLock();
    // Serialize and append the item directly
    ArrayFileHandler::appendString($this->prefix, serialize($item));
    $this->releaseLock();
}


    public function enqasdasdueue($item) {
        $this->acquireLock();
        $queue = $this->getQueue();
        array_push($queue, $item);
        // ArrayFileHandler::clearFile($this->prefix);

        // Check if the item is an associative array
        if (is_array($item) && array_keys($item) !== range(0, count($item) - 1)) {
            // Append associative array
            ArrayFileHandler::appendArray($this->prefix, $queue);
        } else {
            // Append as a string
        
            foreach ($queue as $queueItem) {
            
                ArrayFileHandler::appendString($this->prefix, serialize($queueItem));
            }
        }

        $this->releaseLock();
    }

    public function dequeue() {
        $this->acquireLock();
        $queue = $this->getQueue();
        $item = null;
        if (!empty($queue)) {
            $item = array_shift($queue);
            ArrayFileHandler::clearFile($this->prefix);
            foreach ($queue as $queueItem) {
                ArrayFileHandler::appendString($this->prefix, serialize($queueItem));
            }
        }
        $this->releaseLock();
        return $item;
    }

    public function peek() {
        $queue = $this->getQueue();
        return empty($queue) ? null : $queue[0];
    }

    public function count() {
        $queue = $this->getQueue();
        return count($queue);
    }

public function getQuesdfsdfue() {
    $data = ArrayFileHandler::retrieveMatrix($this->prefix);
    $queue = [];

// var_dump($data);
    foreach ($data as $item) {
        // Check if the item is a string (which indicates it's serialized)
        if (is_string($item)) {

            $unserializedItem = unserialize($item);
            if ($unserializedItem !== false || $item === 'b:0;') {
                // Successfully unserialized or special case for serialized 'false'
                $queue[] = $unserializedItem;
            }
        } else {
            // If the item is not a string, add it directly to the queue
            $queue[] = $item;
        }
    }

    return $queue;
}


public function getQueue() {
    // $this->acquireLock();
    $data = ArrayFileHandler::retrieveMatrix($this->prefix);
    // $this->releaseLock();
    $queue = [];

    foreach ($data as $item) {
        // Check if the item is a serialized string
        if (is_string($item) && $this->isSerialized($item)) {
            $unserializedItem = unserialize($item);
            $queue[] = $unserializedItem !== false ? $unserializedItem : $item;
        } else {
            // If the item is not a serialized string, add it directly to the queue
            $queue[] = $item;
        }
    }

    return $queue;
}

private function isSerialized($data) {
    if (!is_string($data)) {
        return false;
    }
    return @unserialize($data) !== false || $data === 'b:0;';
}


public function getAllAssociativeArrays() {
    $this->acquireLock();
    $queue = $this->getQueue();
    $combinedAssociativeArray = [];

    foreach ($queue as $item) {
        if ($this->isAssociativeArray($item)) {
            foreach ($item as $key => $value) {
                $combinedAssociativeArray[$key] = $value;
            }
        }
    }
    $this->releaseLock();
    return $combinedAssociativeArray;
}

private function isAssociativeArray($arr) {
    if (!is_array($arr)) {
        return false;
    }
    return array_keys($arr) !== range(0, count($arr) - 1);
}

public function removeItem($keyOrString) {
    $this->acquireLock();
    $queue = $this->getQueue();
    $modified = false;

    foreach ($queue as $index => $item) {
        if (is_array($item) && isset($item[$keyOrString])) {
            // Remove the item with the specified key from the associative array
            unset($item[$keyOrString]);
            if (empty($item)) {
                // If the array is now empty, remove it entirely
                unset($queue[$index]);
            } else {
                // Otherwise, update the queue with the modified array
                $queue[$index] = $item;
            }
            $modified = true;
        } elseif ($item === $keyOrString) {
            // If the item is a string and matches, remove it from the queue
            unset($queue[$index]);
            $modified = true;
        }
    }

    // Reindex the queue to eliminate gaps, if necessary
    if ($modified) {
        $queue = array_values($queue);

        // Rewrite the modified queue back to the file
        ArrayFileHandler::clearFile($this->prefix);
        foreach ($queue as $queueItem) {
            ArrayFileHandler::appendString($this->prefix, serialize($queueItem));
        }
    }

    $this->releaseLock();
}

    private function acquireLock() {
        sem_acquire($this->semId);
    }

    private function releaseLock() {
        sem_release($this->semId);
    }

    public function __destruct() {
        // Handle resource cleanup if necessary
    }

    public function isEmpty() {
        return count($this->getQueue()) < 1;
    }
}



// $queue = new Queue("testing");

// $temp = [];
// $temp[10] = ["val"=>1];
// $queue->enqueue($temp);
// $temp = [];
// $temp[12] = ["val"=>2];
// $queue->enqueue($temp);
// $temp = [];
// $temp[14] = ["val"=>3];
// $queue->enqueue($temp);

//  $queue->removeItem(12);
//  $temp = [];
// $temp[12] = ["val"=>2];
// $queue->enqueue($temp);

// // $temp = [];
// // $temp[125] = "1234";
// // $queue->enqueue($temp);

// // $queue->removeItem(1235);
// // // // $queue->enqueue("world");

// var_dump($queue->dequeue());

// Dequeue and display each item
// while (!$queue->isEmpty()) {
//     $item = $queue->dequeue();
//     var_dump($item);
//     // if (is_array($item)) {
//     //     echo json_encode($item) . PHP_EOL; // Display array as JSON for readability
//     // } else {
//     //     echo $item . PHP_EOL;
//     // }
// }
// $queue = new Queue();

// $queue->enqueue('item1');
// $queue->enqueue('item2');
// $queue->enqueue('item3');

// var_dump($queue->isEmpty());
// ;
//echo $queue->peek()."\n";  // Output: item1
// echo $queue->dequeue()."\n";     // Output: item2
// echo $queue->dequeue()."\n";     // Output: item3
// echo $queue->isEmpty() ? 'Queue is empty' : 'Queue is not empty';  // Output: Queue is not empty

?>