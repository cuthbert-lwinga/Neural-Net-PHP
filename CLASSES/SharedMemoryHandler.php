<?PHP
namespace NameSpaceSharedMemoryHandler;
use NameSpaceSharedMemoryHandler\SharedMemoryHandler;
use Exception;

class SharedMemoryHandler {
    /**
     * Create or open a shared memory block.
     *
     * @param string $key A string to generate a System V IPC key.
     * @param int $size The size of the shared memory block in bytes.
     * @return int The shared memory block identifier.
     */
    public static function create($key, $size,$preserve_old = true) {
        $ipcKey = ftok(__FILE__, $key);
        $size += 2; // For \n operator

         try {
            $shmId = shmop_open($ipcKey, "c", 0644, $size);
            if ($shmId === false) {
                throw new \Exception("Unable to create or open shared memory segment.");
            }
            return $shmId;
        } catch (\Exception $e) {
            // Handle the exception and print the error
            echo "Error: " . $e->getMessage() . "\n";
            // Optionally, you can re-throw the exception if you want it to be handled by the caller
        }

        if (!$preserve_old) {
            SharedMemoryHandler::write($shmId, "");            
        }

        return $shmId;
    }



/**
     * Read data from a shared memory block.
     *
     * @param int $shmId The shared memory block identifier.
     * @return string|null The data read from the shared memory, or null if no data is set.
     */
    public static function read($shmId) {
        $size = shmop_size($shmId);
        if ($size === 0) {
            return null;  // No data set in the shared memory block
        }

        $data = shmop_read($shmId, 0, $size);
        if ($data === false || trim($data) === '' || $data === str_repeat("\0", $size)) {
            return null;  // No data, empty data, or all null bytes
        }
        $data = explode('\n', $data);
        if (strlen($data[0]) === 0) {
            return NULL;
        }

        return $data[0];
    }

    /**
     * Write data into a shared memory block.
     *
     * @param int $shmId The shared memory block identifier.
     * @param string $data The data to write into the shared memory.
     */
    public static function write($shmId, $data) {

        $size = shmop_size($shmId);
        if (strlen($data)>=$size) {
             throw new \Exception("memory overflow ".strlen($data)."B being allocated to ".$size);
             return;
        }

        $data.= '\n'; // could have conflict with insert data with this marker
        
        shmop_write($shmId, $data, 0);
    }

    /**
     * Delete a shared memory block.
     *
     * @param int $shmId The shared memory block identifier.
     */
    public static function delete($shmId) {
        shmop_delete($shmId);
    }

    /**
     * Close a shared memory block.
     *
     * @param int $shmId The shared memory block identifier.
     */
    public static function close($shmId) {
        shmop_close($shmId);
    }

    /**
     * Clear the data in a shared memory segment.
     *
     * @param int $shmId The shared memory block identifier.
     */
    public static function clearMemory($shmId) {
        // Check if the shared memory ID is valid
        if (!$shmId) {
            throw new \Exception("Invalid shared memory ID.");
        }

        // Get the size of the current shared memory segment
        $shmSize = shmop_size($shmId);
        if ($shmSize === false) {
            throw new \Exception("Failed to get shared memory size.");
        }

        // Create a string of zeros to overwrite the memory
        $clearData = str_repeat("\0", $shmSize);

        // Overwrite the shared memory with the clear data
        shmop_write($shmId, $clearData, 0);
    }

}


// Create a shared memory block with a size of 100 bytes
// $length = 8;
// $uniqueHex = bin2hex(random_bytes($length / 2));
// $shmId = SharedMemoryHandler::create($uniqueHex, 2);
// // Write data to the shared memory block
// // SharedMemoryHandler::write($shmId, "ab");
// // SharedMemoryHandler::write($shmId, "ab");
// // $shmId = SharedMemoryHandler::overwrite($shmId, "Cuthbert");


// // Read data from the shared memory block
// $data = SharedMemoryHandler::read($shmId);
// echo "Data read from shared memory: $data\n";

// // Delete and close the shared memory block
// SharedMemoryHandler::delete($shmId);
// SharedMemoryHandler::close($shmId);



?>