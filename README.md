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


    use \Apolinux\ClassManager\DirectoryClassType;
    use \Apolinux\ClassManager\ClassContainer;

    require __DIR__ .'/vendor/autoload.php' ;

    $container = new ClassContainer ;
    $container->add(new DirectoryClassType(__DIR__. '/test/unit/Commands'));

    $cmd = new \Apolinux\CommandRun($container);

    $cmd->start();


when is executed:


    ./cmdrun.php


it shows:

    command run 
    Usage: cmdrun.php [ -h | --help ] [ -l | --list ] ClassName [ -h | -help ] [ method ] [ --param1=value1 ] [ --param2=value2 ] ...
    where:
        -h | --help     : shows this help.
        -l | --list     : shows a list of possible classname commands.
        -d | --classdir : specify classes directory where to find class commands.
        ClassName       : name of class that contains "run" method with command instructions.
        -h | -help      : after classname. show class methods available with their respective parameters.
        method          : alternative method to be called.
        --paramX=valueX : parameter names of run (or "method") method in ClassName class.


using a test dir:

    ./cmdrun.php -l                                                                                                                     
    classes list:
     * TestProcess
     * TestRunClass
    To view method details of class run as cmdrun.php ... ClassName -h

    ./cmdrun.php TestRunClass                                                                                                           
    The parameter 'param1' is not defined. Parameters are defined like '--paramX=valueX'.
    Method definition: ::run [default] ( int $param1, bool $param2, undefined $param3=null, string $param4='' )

    ./cmdrun.php TestRunClass --param1=541 --param2=false                                                                               
    in method TestRunClass::run
    receive parameters:  
    param1:541
    param2:false
    param3:
    param4:

    ./cmdrun.php TestRunClass --param1=541 --param2=false --param3=11 --param4="hey you!"                                               
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

New in version 0.7
------------------

Using anonymous and custom classes. It can be used with a anonymous class list. Example:
    
     $argv=['cmd', '-l'];

    class testInternal {
        public function run(){
            echo 'in class testInternal' ;
        }
    };

    class testInternal2{
        public function run(){
            echo 'in class testInternal2';
        }
    };

    $argv=['fido', '-l'];
        
    $class_list = [
        'testanonymous1' => new class{
            public function run(){
                echo 'in class testanonymous1' ;
            }
        } ,
        'testanonymous2' => new class{
              public function run(){
                  echo 'in class testanonymous2' ;
              }
        }
    ];

    // run command
    
    $class_container = new ClassContainer();
    $class_container->add(new DirectoryClassType(__DIR__ .'/Commands'));
    $class_container->add(new AnonymousClassType($class_list));
    $class_container->add(new InternalClassType(['testInternal',testInternal2::class ]) );  // define explicitamente las clases

    $cmd = new \Apolinux\CommandRun($class_container);
    $cmd->start($argv);
    
will get something like this:

    php -d display_errors=1 test.php -l
    classes list:
     * testanonymous1
     * testanonymous2
     * testInternal
     * testInternal2
    To view method details of class run as test.php ... ClassName -h
  
  

Details
-------

The method CommandRun::start receives an optional parameter to replace global $argv argument list if it's required.