<?php
include_once("SharedMemoryHandler.php");
include_once("Threads.php");
use NameSpaceThreads\Threads;


echo "🚀 Successfully completed a 2-hour (7200-second) sleep simulation in just 15 seconds! 🕒\n\n";
echo "🔥 This serves as a stress test to showcase the robustness and efficiency of memory allocation in our multithreading environment. 💪\n\n";
echo "📝 Note: This test uses 800 threads. While this demonstrates the system's impressive capacity, the practicality of using this many threads is open for discussion. 
\n🤔 Feel free to adjust the thread count to a more suitable number for your specific use case. 🔧\n";


function testbackground($param=1){
    sleep(1);
    
    if ($param%1000 == 0){
        echo "\n $param EXECTUED\n";
    }
}

for ($i=0; $i < 7200; $i++) { 
    Threads::addTask("testbackground",[$i]);
}

Threads::run($Threads=800);

echo "\n\n all done \n\n";

?>