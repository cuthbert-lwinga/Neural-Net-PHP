<?PHP
namespace NameSpaceTaskRegistry;
include_once("Headers.php");
use NameSpaceArrayFileHandler\ArrayFileHandler;

class TaskRegistry {
    private $taskFileName;

    public function __construct($fileNamePrefix = 'tasks_') {
        $this->taskFileName = $fileNamePrefix . uniqid();
        ArrayFileHandler::initialize($this->taskFileName);
    }

    public function addTask($key, $functionName, array $arguments = []) {
        $taskData = [
            'function' => $functionName,
            'arguments' => $arguments
        ];
        $jsonTaskData = json_encode($taskData);
        ArrayFileHandler::appendString($this->taskFileName, "$key=$jsonTaskData");
        return $key;
    }

        public function getTask($key) {
            $tasks = ArrayFileHandler::retrieveTasks($this->taskFileName);
            return $tasks[$key] ?? null;
        }


    public function generateUniqueKey() {
        return uniqid('task_', true);
    }

    public function getTaskCount() {
        $tasks = ArrayFileHandler::retrieveTasks($this->taskFileName);
        return count($tasks);
    }

    public function removeTask($key) {
        $tasks = ArrayFileHandler::retrieveTasks($this->taskFileName);
        unset($tasks[$key]);

        ArrayFileHandler::clearFile($this->taskFileName);
        foreach ($tasks as $taskKey => $taskData) {
            $jsonTaskData = json_encode($taskData);
            ArrayFileHandler::appendString($this->taskFileName, "$taskKey=$jsonTaskData");
        }

        return !isset($tasks[$key]);
    }

    public function clearAllTasks() {
        ArrayFileHandler::clearFile($this->taskFileName);
    }

    public function destroy() {
        ArrayFileHandler::removeFile($this->taskFileName);
    }
}




// TaskRegistry::initialize('operation1_');
// $key = TaskRegistry::generateUniqueKey();
// $taskKey = TaskRegistry::addTask($key, 'functionName', ['arg1', 'arg2']);



// var_dump(TaskRegistry::getTask($key));




?>