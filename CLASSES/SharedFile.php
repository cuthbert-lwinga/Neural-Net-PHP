<?php
namespace NameSpaceSharedFile;

class SharedFile
{
    private static $fileHandles = [];

    /**
     * Initialize the class for a specific instance identified by $name.
     *
     * @param string $name The name of the instance.
     */
    public static function initialize($name,$type='w+')
    {
        if (!isset(self::$fileHandles[$name])) {
            // $tempFile = tempnam(sys_get_temp_dir(), 'shared_file_' . $name . '_');
            $tempFile = 'shared_file_' . uniqid();
            $fileHandle = fopen($tempFile, $type);
            self::$fileHandles[$name] = ['file' => $tempFile, 'handle' => $fileHandle,"type"=>$type];
        }
    }

    public static function getFileHandles(){
        return self::$fileHandles;
    }

    /**
     * Write data to the shared file for a specific instance.
     *
     * @param string $name The name of the instance.
     * @param string $data The data to write.
     */
    public static function write($name, $data)
    {
        // self::initialize($name);

        flock(self::$fileHandles[$name]['handle'], LOCK_EX);

        // Check if the file is opened in "w+" mode
        if (self::$fileHandles[$name]['type'] === 'w+') {
            fseek(self::$fileHandles[$name]['handle'], 0); // Set the file pointer to the beginning
        }

        fwrite(self::$fileHandles[$name]['handle'], $data);
        fflush(self::$fileHandles[$name]['handle']);
        flock(self::$fileHandles[$name]['handle'], LOCK_UN);
    }

    /**
     * Read data from the shared file for a specific instance.
     *
     * @param string $name The name of the instance.
     * @return string The read data.
     */
    public static function read($name)
    {
        self::initialize($name);
        $content = "";
        if (file_exists(self::$fileHandles[$name]['file'])){

            flock(self::$fileHandles[$name]['handle'], LOCK_SH);
            $content = file_get_contents(self::$fileHandles[$name]['file']);
            flock(self::$fileHandles[$name]['handle'], LOCK_UN);
        
        }

        return $content;
    }

    /**
     * Get the file handle for a specific instance.
     *
     * @param string $name The name of the instance.
     * @return resource|bool The file handle or false if not found.
     */
    public static function getHandle($name)
    {
        if (isset(self::$fileHandles[$name])) {
            return self::$fileHandles[$name]['handle'];
        }

        return false;
    }

        /**
     * Empty the contents of the shared file for a specific instance.
     *
     * @param string $name The name of the instance.
     */
        public static function emptyFile($name)
        {
            if (isset(self::$fileHandles[$name])) {
                ftruncate(self::$fileHandles[$name]['handle'], 0); // Truncate the file to length 0
                fseek(self::$fileHandles[$name]['handle'], 0); // Move the file pointer to the beginning
                file_put_contents(self::$fileHandles[$name]['file'],"");
            }
        }


    /**
     * Close the file handle for a specific instance.
     *
     * @param string $name The name of the instance.
     */
    public static function close($name)
    {
        if (isset(self::$fileHandles[$name])) {
            fclose(self::$fileHandles[$name]['handle']);
            unlink(self::$fileHandles[$name]['file']);
            unset(self::$fileHandles[$name]);
        }
    }
}

// Example Usage:

// // Write data to the shared file
// SharedFile::write("Hello, world!");

// // Read data from the shared file
// $readData = SharedFile::read();

// echo $readData; // Output: Hello, world!

// // Close the file handle when done
// SharedFile::close();
// SharedFile::write('instance1', 'Data for instance 1');
// SharedFile::write('instance2', 'Data for instance 2');

// echo SharedFile::read('instance1'); // Output: Data for instance 1
// echo SharedFile::read('instance2'); // Output: Data for instance 2

// SharedFile::close('instance1');
// SharedFile::close('instance2');


?>
