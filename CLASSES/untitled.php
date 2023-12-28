<?php
include_once("SharedMemoryHandler.php");
include_once("Threads.php");
use NameSpaceThreads\Threads;

function testbackground($param=1){
    mt_srand(); // Seed the random number generator
    $randomNumber = mt_rand(0, 4);
    echo "\n $param will sleep for $randomNumber s\n";
    sleep($randomNumber);
    echo "\n $param woke up\n";
}

Threads::addTask("testbackground",[1]);
Threads::addTask("testbackground",[2]);
Threads::addTask("testbackground",[3]);
Threads::addTask("testbackground",[4]);
Threads::addTask("testbackground",[5]);
Threads::addTask("testbackground",[6]);
Threads::addTask("testbackground",[7]);
Threads::addTask("testbackground",[8]);
Threads::run(10);
echo "\n\n all done \n\n";

?>