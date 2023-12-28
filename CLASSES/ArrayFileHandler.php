<?PHP
namespace NameSpaceArrayFileHandler;
ini_set('memory_limit', '1024M'); // Increase to 1GB, for example

use Exception;

class ArrayFileHandler
{
    private static $files = [];
    private static $fileHandles = []; // Array to store file handles

    public static function initialize($prefix = 'default_')
    {
        $tempDir = __DIR__;
        $fileName = $tempDir . DIRECTORY_SEPARATOR . $prefix . '.txt';
        self::$files[$prefix] = $fileName;
        
        // Check if the file exists; if not, create it
        // if (!file_exists(self::$files[$prefix])) {
            file_put_contents(self::$files[$prefix], '', LOCK_EX | FILE_USE_INCLUDE_PATH);
        //}

        // Open and store the file handle
        self::$fileHandles[$prefix] = fopen(self::$files[$prefix], 'c+');
    }

    public static function destroy($prefix){
        
        if (isset(self::$fileHandles[$prefix])) {
            fclose(self::$fileHandles[$prefix]); // Close the file handle
            unset(self::$fileHandles[$prefix]);
        }

        if (isset(self::$files[$prefix])){
                if (file_exists(self::$files[$prefix])) {
                    unlink(self::$files[$prefix]);
                }
        }
    }

    public static function appendArray($prefix, $data)
    {
        if (empty(self::$files[$prefix])) {
            throw new Exception('File for the given prefix is not initialized. Call initialize() first.');
        }

        $serializedData = serialize($data);
        $fileHandle = self::$fileHandles[$prefix];

        flock($fileHandle, LOCK_EX); // Acquire an exclusive lock
        fseek($fileHandle, 0, SEEK_END); // Move to the end of the file
        fwrite($fileHandle, $serializedData . PHP_EOL);
        fflush($fileHandle); // Flush output before releasing the lock
        flock($fileHandle, LOCK_UN); // Release the lock
    }

public static function retrieveMatrix($prefix) {
    if (empty(self::$files[$prefix])) {
        throw new Exception('File for the given prefix is not initialized. Call initialize() first.');
    }

    $fileHandle = self::$fileHandles[$prefix];
    
    flock($fileHandle, LOCK_SH); // Acquire a shared lock
    fseek($fileHandle, 0); // Move to the beginning of the file

    $matrix = [];
    while (($line = fgets($fileHandle)) !== false) {
        $data = unserialize(trim($line));
        if ($data !== false) {
            $matrix[] = $data;
        }
    }

    flock($fileHandle, LOCK_UN); // Release the lock

    return $matrix;
}

public static function retrieveTasks($prefix) {
    if (empty(self::$files[$prefix])) {
        throw new Exception('File for the given prefix is not initialized. Call initialize() first.');
    }

    $fileHandle = self::$fileHandles[$prefix];
    
    flock($fileHandle, LOCK_SH); // Acquire a shared lock
    fseek($fileHandle, 0); // Move to the beginning of the file

    $tasks = [];
    $lineFound = 1;
while (($line = fgets($fileHandle)) !== false) {
    $parts = explode("=", trim($line), 2);
    if (count($parts) == 2) {
        $key = $parts[0];
        // echo "Raw Serialized Data: " . $parts[1] . "\n"; // Debugging line
        $data = json_decode($parts[1],true);
        if ($data !== false) {
            $tasks[$key] = $data;
        } else {
            echo "Failed to unserialize:($lineFound) " . $parts[1] . " line:: $line\n"; // Error info
        }
    }
    $lineFound++;
}


    flock($fileHandle, LOCK_UN); // Release the lock

    return $tasks;
}


public static function retrieveAssociativeArray($prefix) {
    var_dump(self::$files);
    if (empty(self::$files[$prefix])) {
        throw new Exception('File for the given prefix is not initialized. Call initialize() first.');
    }

    $associativeArray = [];
    $fileHandle = self::$fileHandles[$prefix];

    flock($fileHandle, LOCK_SH); // Acquire a shared lock
    fseek($fileHandle, 0); // Move to the beginning of the file

    while (($line = fgets($fileHandle)) !== false) {
        $data = unserialize(trim($line));
        if ($data !== false) {
            foreach ($data as $key => $value) {
                $associativeArray[$key] = $value;
            }
        } else {
            throw new Exception('Error unserializing data from file.');
        }
    }

    flock($fileHandle, LOCK_UN); // Release the lock

    return $associativeArray;
}


    public static function clearFile($prefix) {
        if (empty(self::$files[$prefix])) {
            throw new Exception('File for the given prefix is not initialized. Call initialize() first.');
        }

        $fileHandle = self::$fileHandles[$prefix];

        flock($fileHandle, LOCK_EX); // Acquire an exclusive lock
        ftruncate($fileHandle, 0);   // Truncate the file to clear its contents
        fflush($fileHandle);         // Flush output before releasing the lock
        flock($fileHandle, LOCK_UN); // Release the lock
    }


        public static function removeFile($prefix)
    {
        if (empty(self::$files[$prefix])) {
            throw new Exception('File for the given prefix is not initialized. Call initialize() first.');
        }

        self::destroy($prefix);

    }

public static function appendString($prefix, $string) {
    if (empty(self::$files[$prefix])) {
        throw new Exception('File for the given prefix is not initialized. Call initialize() first.');
    }

    $fileHandle = self::$fileHandles[$prefix];

    flock($fileHandle, LOCK_EX); // Acquire an exclusive lock
    fseek($fileHandle, 0, SEEK_END); // Move to the end of the file
    fwrite($fileHandle, $string . PHP_EOL);
    fflush($fileHandle); // Flush output before releasing the lock
    flock($fileHandle, LOCK_UN); // Release the lock
}


}


// ArrayFileHandler::initialize('example_prefix');

// Append strings directly
// ArrayFileHandler::append('example_prefix', 'hello');
// ArrayFileHandler::append('example_prefix', 'bye');
// ArrayFileHandler::append('example_prefix', 'leave');



// Example usage:
// ArrayFileHandler::initialize();

// $array1 = [1, 2, 3, 4, 5];
// $array2 = [6, 7, 8, 9, 10];

// ArrayFileHandler::appendArray($array1);
// ArrayFileHandler::appendArray($array2);

// $matrix = ArrayFileHandler::retrieveMatrix();

// // Output the matrix
// print_r($matrix);

// // Clear the file
// ArrayFileHandler::clearFile();

?>