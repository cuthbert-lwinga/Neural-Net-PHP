<?PHP
namespace NameSpaceMatrixFileHandler;

include_once("Headers.php");
include_once("Queue.php");
use NameSpaceQueue\Queue;

class MatrixFileHandler {
    private static $fileLockQueue;

    public static function init($fileName, $rows, $cols) {
        $uniqueFileName = $fileName . "_" . uniqid() . ".txt";
        self::$fileLockQueue = new Queue($uniqueFileName . '_file_lock_');

        self::initializeMatrixFile($uniqueFileName, $rows, $cols);
        return $uniqueFileName;
    }

    private static function initializeMatrixFile($fileName, $rows, $cols) {
        $handle = fopen($fileName, 'w');
        if (!$handle) {
            throw new \Exception("Unable to open file: " . $fileName);
        }

        for ($i = 0; $i < $rows; $i++) {
            fwrite($handle, implode(' ', array_fill(0, $cols, 'null')) . PHP_EOL);
        }

        fclose($handle);
    }

    public static function updateRow($fileName, $row, array $rowData) {
        // Wait for the file lock to be released
        while (self::isFileLocked($fileName)) {
            usleep(10000); // Wait for 10 milliseconds
        }

        self::setFileLock($fileName, true); // Acquire file lock
        $matrix = self::readMatrix($fileName);
        $matrix[$row] = $rowData;
        self::writeMatrix($fileName, $matrix);
        self::setFileLock($fileName, false); // Release file lock
    }

    private static function isFileLocked($fileName) {
        $lockStatus = self::$fileLockQueue->peek();
        return !empty($lockStatus) && $lockStatus === true;
    }

    private static function setFileLock($fileName, $isLocked) {
        if ($isLocked) {
            self::$fileLockQueue->enqueue(true);
        } else {
            self::$fileLockQueue->dequeue(); // Assuming the queue has only one item
        }
    }

    private static function readMatrix($fileName) {
        $matrix = [];
        $handle = fopen($fileName, 'r');

        while (($line = fgets($handle)) !== false) {
            $matrix[] = explode(' ', trim($line));
        }

        fclose($handle);
        return $matrix;
    }

    private static function writeMatrix($fileName, $matrix) {
        $handle = fopen($fileName, 'w');

        foreach ($matrix as $row) {
            fwrite($handle, implode(' ', $row) . PHP_EOL);
        }

        fclose($handle);
    }

    public static function getMatrixAsArray($fileName) {
        if (!file_exists($fileName)) {
            throw new \Exception("File not found: " . $fileName);
        }

        $matrixArray = [];
        $handle = fopen($fileName, 'r');
        if (!$handle) {
            throw new \Exception("Unable to open file: " . $fileName);
        }

        while (($line = fgets($handle)) !== false) {
            $elements = explode(' ', trim($line));
            $parsedRow = array_map('self::parseElement', $elements);
            $matrixArray[] = $parsedRow;
        }

        fclose($handle);
        return $matrixArray;
    }

    private static function parseElement($element) {
        if (strtolower($element) === 'null') {
            return null;
        }
        return is_numeric($element) ? $element + 0 : $element;
    }

    public static function destroy($fileName) {
        if (file_exists($fileName)) {
            unlink($fileName);
        }
        self::$fileLockQueue->destroy();
    }
}



// Initialize a matrix file
// $uniqueFileName = MatrixFileHandler::init("zzzzzzzzzz", 3, 3);

// // Update an element in the matrix
// MatrixFileHandler::updateElement($uniqueFileName, 1, 1, 5);

// Destroy the matrix file when done
// MatrixFileHandler::destroy($uniqueFileName);










?>