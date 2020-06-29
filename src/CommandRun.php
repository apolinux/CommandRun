<?php

namespace Apolinux;

use \Apolinux\ClassManager\ClassContainer;
use \Apolinux\ClassManager\ClassNotFoundException;
use \Apolinux\ClassManager\ClassNotDefinedException;

/**
 * main class that parse input commands and call method classes
 * 
 * 
 * By default receive global $argv as argument list but it can receive custom array arguments.
 * 
 * Uses PHP Reflection classes to get information about classes, methods and parameters.
 * 
 * It can define custom classed directory by config or by parameters.
 * 
 * It supports help and listing when it is required.
 */
class CommandRun {
    
    /**
     * class container: contain a list of class containers 
     * @var \Apolinux\ClassManager\ClassContainer
     */
    private $class_container ;
    
    public function __construct(ClassContainer $class_container){
        $this->class_container = $class_container ;
    }
    
    /**
     * run main program
     * 
     * read arguments, instance classes and call method when it's necessary. 
     * Also shows a help about the tool and the classes and methods configured.
     * 
     * @param array $arg_list argument list including running command as first item
     * @return void
     */
    public function start(array $arg_list=null){
        if($arg_list==null){
            $arg_list = $GLOBALS['argv'] ;
        }
        // check args
        try{
            $runclass = $this->checkArgumentsInput($arg_list);
        }catch(CommandRunException $e){
            return $this->message($e->getMessage(), ($e->getCode() === 2) ? false : true) ;
        }
        // end check args
        
        try{
            $object = $this->class_container->instance($runclass) ;
        }catch(ClassNotFoundException $e){
            return $this->message("The classname '". $e->getMessage()."' was not found in class pool") ;
        }catch(ClassNotDefinedException $e){
            return $this->message("The classname '". $e->getMessage()."' was not defined in class pool") ;
        }
        $method = 'run';
        
        try{
            $method_params = $this->parseParamsMethod($arg_list, $object, $method);
        }catch(CommandRunException $e){
            return $this->message($e->getMessage(),false) ;
        }
        
        call_user_func_array([$object, $method], $method_params);
    }
    
    /**
     * parse parameters of method using reflection and return method parameters
     * 
     * @param array $arg_list argument list
     * @param string $runclass running class name
     * @param string $method method name
     * @return array method parameters list
     * @throws \Exception
     */
    private function parseParamsMethod($arg_list, $runclass, &$method){
        $arg_parser = new CliArgumentParser();
        $rmethod = new \ReflectionMethod($runclass, $method);
        
        list( $arg_proc , $arg_extra )= $arg_parser->parse($arg_list);
        $reflection = new ReflectionMethod;
        if($arg_extra == 'help'){
            throw new CommandRunException($reflection->getMethodList($rmethod->getDeclaringClass()));
        }
        if($arg_extra){ // other method
            $method = $arg_extra ;
        }
        $method_params = $reflection->getParams($arg_proc, $runclass,$method);
        
        return $method_params ;
    }
    
    /**
     * validate arguments from argument list
     * 
     * check for list and helper requirement
     * 
     * @param array $arg_list
     * @return string running class, if there is one
     * @throws \Exception
     */
    private function checkArgumentsInput(&$arg_list){
        // validate number of parameters
        if(count($arg_list) < 2){
            throw new CommandRunException('Missing classname as first parameter');
        }
        $thiscmd = array_shift($arg_list) ;
        
        $currarg = $arg_list[0];
        if(in_array($currarg,['--help', '-h'],true)){
            throw new CommandRunException('');
        }
        
        if(in_array($currarg,['--list', '-l'],true)){
            array_shift($arg_list) ;
            // sequence is -l -d | --classdir=class
            $this->detectClassesDir($arg_list);
            throw new CommandRunException($this->listClasses(),2);
        }
        
        $this->detectClassesDir($arg_list);
        
        $currarg = array_shift($arg_list);
        if(in_array($currarg,['--list', '-l'],true)){
            // sequence is -d | --classdir=class  -l
            throw new CommandRunException($this->listClasses(),2);
        }
        $runclass = $currarg;
        
        if(is_null($runclass)){
            throw new CommandRunException('Must specify classname command') ;
        }
        
        return $runclass ;
    }
    
    /**
     * verifies if there is arguments defining classes directory
     * 
     * @param array $arg_list
     */
    private function detectClassesDir(&$arg_list){
        if(isset($arg_list[0]) 
                && in_array($arg_list[0],['-d', '--classdir'],true) 
                && isset($arg_list[1])){
            array_shift($arg_list);
            $this->classes_dir = array_shift($arg_list);
        }
    }
 
    /**
     * shows a message with help optionally
     * 
     * @param string $message
     * @param bool $show_help
     * @return void
     */
    private function message($message, $show_help=true){
        if($show_help){
            return $this->help($message);
        }
        echo "$message". PHP_EOL ;
    }
    
    /**
     * returns main command help
     * 
     * @param string $message
     */
    private function help($message){
        $message = $message ? $message . PHP_EOL : '';
        $command = basename($GLOBALS['argv'][0]) ;
        echo <<<END
command run 
Usage: $command [ -h | --help ] [ -l | --list ] [ -d | --classdir=classdir ] ClassName [ -h | -help ] [ method ] [ --param1=value1 ] [ --param2=value2 ] ...
where:
    -h | --help     : shows this help.
    -l | --list     : shows a list of possible classname commands.
    -d | --classdir : specify classes directory where to find class commands.
    ClassName       : name of class that contains "run" method with command instructions.
    -h | -help      : after classname. show class methods available with their respective parameters.
    method          : alternative method to be called.
    --paramX=valueX : parameter names of run (or "method") method in ClassName class.
$message
END;        
    }
    
    /**
     * get a classes list from directory defined
     * 
     * @return string
     */
    private function listClasses(){
        $out =['classes list:'];
        foreach($this->class_container->listClasses() as $class){
            $out[] = ' * ' . $class ;
        }
        $command = basename($GLOBALS['argv'][0]) ;
        $out[]="To view method details of class run as $command ... ClassName -h";
        return join(PHP_EOL,$out);
    }
}
