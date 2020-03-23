# CommandRun

Run command line programs calling method from class, using parameters as method arguments.


Description
-----------

Running an unique command it lets to call methods from several classes inside a defined dir.

It shows a complete help about the program itself and the possible classes to be called.

Sometimes it is convenient to call commands with configurable parameters and convert them
to method objects. This class let's to skip manually processing command line arguments and 
get info about every class configured to be called. 

### Example

It is provided a file called cmdrun.php to show how the program must be called:


    #!/bin/env php
    <?php
    require __DIR__ .'/vendor/autoload.php' ;
    $cmd = new \Apolinux\CommandRun();
    $cmd->start();


when is executed:


    ./cmdrun.php


it shows:

    command run                                                                                                                                           
    Usage: cmdrun.php [ -h | --help ] [ -l | --list ] [ -d | --classdir=classdir ] ClassName [ -h | -help ] [ method ] [ --param1=value1 ] [ --param2=value2 ] ...                          
    where:                                                                                                                                                
        -h | --help     : shows this help.
        -l | --list     : shows a list of possible classname commands.
        -d | --classdir : specify classes directory where to find class commands.
        ClassName       : name of class that contains "run" method with command instructions.
        -h | -help      : after classname. show class methods available with their respective parameters.
        method          : alternative method to be called.
        --paramX=valueX : parameter names of run (or "method") method in ClassName class.                           
    Missing classname as first parameter


using a test dir:

    ./cmdrun.php  -d ./test/unit/Commands -l                                                                                                                     
    classes list:
     * TestProcess
     * TestRunClass
    To view method details of class run as cmdrun.php ... ClassName -h
    [drake@pollux CommandRun]$ ./cmdrun.php  -d ./test/unit/Commands TestRunClass                                                                                                           
    The parameter 'param1' is not defined. Parameters are defined like '--paramX=valueX'.
    Method definition: ::run [default] ( int $param1, bool $param2, undefined $param3=null, string $param4='' )
    [drake@pollux CommandRun]$ ./cmdrun.php  -d ./test/unit/Commands TestRunClass --param1=541 --param2=false                                                                               
    in method TestRunClass::run
    receive parameters:  
    param1:541
    param2:false
    param3:
    param4:
    [drake@pollux CommandRun]$ ./cmdrun.php  -d ./test/unit/Commands TestRunClass --param1=541 --param2=false --param3=11 --param4="hey you!"                                               
    in method TestRunClass::run
    receive parameters:  
    param1:541
    param2:false
    param3:11
    param4:hey you!

Where class TestRunClass is defined here:

    <?php 
    use Apolinux\Commandable;

    class TestRunClass implements Commandable{

        public function run(int $param1, bool $param2, $param3=null, string $param4=''){
            echo "in method ". __METHOD__ .PHP_EOL ;
            echo "receive parameters:  ". PHP_EOL;
            echo "param1:". ($param1). PHP_EOL;
            echo "param2:". ($param2 ? 'true' : 'false'). PHP_EOL;
            echo "param3:". ($param3). PHP_EOL;
            echo "param4:". ($param4). PHP_EOL;
        }

        public function processSome($name, $phone, $amount=10){
            echo "name: $name, $phone:$phone, $amount:$amount". PHP_EOL ;
        }
    }

Details
-------

The method CommandRun::start receives an optional parameter to replace global $argv argument list if it's required.