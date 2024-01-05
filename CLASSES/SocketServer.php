<?PHP
namespace NameSpaceSocketServer;
use exception;
class SocketServer {
    private static $sockets = [];
    private static $clients = [];
    private static $socketPath = "/tmp/myapp.sock"; // Default socket path

    public static function init() {
        if (!extension_loaded('sockets')) {
            throw new Exception("The sockets extension is not loaded.");
        }

        $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
        if ($socket === false) {
            throw new Exception("Could not create socket: " . socket_strerror(socket_last_error()));
        }

        $socketPath = self::$socketPath . count(self::$sockets); // Unique path for each socket
        if (file_exists($socketPath)) {
            unlink($socketPath);  // Remove the file if it already exists
        }

        if (!socket_bind($socket, $socketPath)) {
            throw new Exception("Could not bind to socket: " . socket_strerror(socket_last_error($socket)));
        }

        if (!socket_listen($socket)) {
            throw new Exception("Could not listen on socket: " . socket_strerror(socket_last_error($socket)));
        }

        socket_set_nonblock($socket);

        self::$sockets[] = $socket;
        return array_key_last(self::$sockets); // Return the index of the new socket
    }

public static function createAndConnectSocket($socketIndex, $timeout = 5) {
    if (!isset(self::$sockets[$socketIndex])) {
        throw new Exception("Invalid socket index: " . $socketIndex);
    }

    $endTime = time() + $timeout;
    while (time() < $endTime) {
        $clientSocket = @socket_create(AF_UNIX, SOCK_STREAM, 0);
        if ($clientSocket === false) {
            continue;
        }

        $socketPath = self::$socketPath . $socketIndex; // Use the path of the specified server socket
        if (@socket_connect($clientSocket, $socketPath)) {
            return $clientSocket;
        }

        socket_close($clientSocket);
        usleep(100); // Wait for 0.1 seconds before retrying
    }
    throw new Exception("Could not connect to server: timed out after {$timeout} seconds");
}




public static function listen(){
        try {
            $startTime = time(); // Record the start time
            $damp = 0;
            // Main loop
            while (true) {
                $sockets = SocketServer::acceptClients();
                $msgs = SocketServer::readFromClients($sockets);
                
                if (!empty($msgs)) {
                    var_dump($msgs);
                }

                // Sleep for 0.1 seconds
                

                // Check if 4 seconds have elapsed
                // if (empty($sockets)&&$damp > 20 ) {
                //     echo "\nall sockets closed\n";
                //     break; // Break the loop 
                // }

                // if (empty($sockets)) {
                //     echo "\nSocket sleeping\n";
                //     $damp++;
                // }

                usleep(1000);


            }

            SocketServer::close(self::$socket);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
    
public static function acceptClients($socketIndex) {
    if (!isset(self::$sockets[$socketIndex])) {
        throw new Exception("Invalid socket index: " . $socketIndex);
    }

    $clients = [];
    $serverSocket = self::$sockets[$socketIndex];

    while ($client = @socket_accept($serverSocket)) {
        if ($client === false) {
            break;
        }
        $clients[] = $client;
        // Optionally, you can echo or log that a client has connected.
        // echo "Client connected to socket {$socketIndex}\n";
    }

    return $clients;
}


            // $data = @socket_read($client, 1024, PHP_NORMAL_READ);
            // if ($data === false) {
            //     continue; // No data available to read
            // }
            // if ($data === "") {
            //     // Client disconnected
            //     socket_close($client);
            //     unset($clients[$key]);
            //     //echo "Client disconnected\n";
            // } else {
            //     // Process the data
            //     // echo "Received from client: $data\n";
            //     $msgs[] = $data;
            // }
    public static function readFromClients(&$clients) {
        $msgs = [];
        foreach ($clients as $key => $client) {
            $buffer = "";

            while (true) {
            $part = @socket_read($client, 2048, PHP_NORMAL_READ);
            if ($part === false) {
                // Handle the error
                break;
            }

            // Append the part to the buffer
            $buffer .= $part;

            if ($part === "") {
                // Client disconnected
                socket_close($client);
                unset($clients[$key]);
                echo "Client disconnected\n";
            }else if(strpos($buffer, '\0') !== false) {
                $buffer = str_replace('\0',"",$buffer.'\0');
                break;
            }
        }


        $msgs[] = $buffer;


        }
        return $msgs;
    }

    public static function writeToSocket($socket, $data) {
        $data = $data.'\0';
        if (socket_write($socket, $data, strlen($data)) === false) {
            throw new Exception("Could not write to socket: " . socket_strerror(socket_last_error($socket)));
        }
    }

    public static function close($socket) {
        socket_close($socket);
    }
}


// $numChildren = 1; // Number of child processes to create

// for ($i = 0; $i < $numChildren; $i++) {
//     $pid = pcntl_fork();

//     if ($pid == -1) {
//         die('Could not fork.');
//     } elseif ($pid) {
//         // Parent process: Continue to fork more children
//         continue;
//     } else {

//         $temp = [];
//         $min = 0.1;
//         $max = 0.4;
//         for ($i=0; $i < 500; $i++) { 
//             $temp[] = ($min + mt_rand() / mt_getrandmax() * ($max - $min));
//         }

//         $temp = implode(",",$temp);
//         // Child process: Act as a client
//         $childPid = getmypid();
//         $clientSocket = SocketServer::createAndConnectSocket($timeout = 60);
//         SocketServer::writeToSocket($clientSocket, $temp);
//         SocketServer::close($clientSocket);
//         echo "Child $childPid completed\n";
//         exit(0); // Terminate the child process
//     }
// }

// // Parent process: Start the server after all children are forked
// SocketServer::init();
// SocketServer::listen();




?>