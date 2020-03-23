<?php

namespace Apolinux;

class CommandRun {
    
    private $classes_dir ;
    
    public function __construct($classes_dir=''){
        $this->classes_dir = $classes_dir ;
    }
    
    public function start(array $arg_list=null){
        if($arg_list==null){
            $arg_list = $GLOBALS['argv'] ;
        }
        // check args
        try{
            $runclass = $this->checkArgumentsInput($arg_list);
        }catch(\Exception $e){
            return $this->message($e->getMessage()) ;
        }
        // end check args
        
        $class_file = $this->classes_dir .'/' . $runclass. '.php' ;
        
        if(! file_exists($class_file)){
            return $this->message("file '$class_file' of class '$runclass' can not be loaded") ;
        }
        
        require_once $class_file;
        
        $object = new $runclass;
        $method = 'run';
        
        try{
            $method_params = $this->parseParamsMethod($arg_list, $runclass, $method);
        }catch(\Exception $e){
            return $this->message($e->getMessage(),false) ;
        }
        
        call_user_func_array([$object, $method], $method_params);
    }
    
    private function parseParamsMethod($arg_list, $runclass, &$method){
        $arg_parser = new CliArgumentParser();
        $rmethod = new \ReflectionMethod($runclass, $method);
        
        list( $arg_proc , $arg_extra )= $arg_parser->parse($arg_list);
        $reflection = new ReflectionMethod;
        if($arg_extra == 'help'){
            throw new \Exception($reflection->getMethodList($rmethod->getDeclaringClass()));
        }
        if($arg_extra){ // other method
            $method = $arg_extra ;
        }
        $method_params = $reflection->getParams($arg_proc, $runclass,$method);
        
        return $method_params ;
    }
    
    private function checkArgumentsInput(&$arg_list){
        // validate number of parameters
        if(count($arg_list) < 2){
            throw new \Exception('Missing classname as first parameter');
        }
        $thiscmd = array_shift($arg_list) ;
        
        $currarg = $arg_list[0];
        if(in_array($currarg,['--help', '-h'],true)){
            throw new \Exception('');
        }
        
        if(in_array($currarg,['--list', '-l'],true)){
            array_shift($arg_list) ;
            // sequence is -l -d | --classdir=class
            $this->detectClassesDir($arg_list);
            throw new \Exception($this->listClasses());
        }
        
        $this->detectClassesDir($arg_list);
        
        $currarg = array_shift($arg_list);
        if(in_array($currarg,['--list', '-l'],true)){
            // sequence is -d | --classdir=class  -l
            throw new \Exception($this->listClasses());
        }
        $runclass = $currarg;
        
        if(is_null($runclass)){
            throw new \Exception('Must specify classname command') ;
        }
        
        return $runclass ;
    }
    
    private function detectClassesDir(&$arg_list){
        if(isset($arg_list[0]) 
                && in_array($arg_list[0],['-d', '--classdir'],true) 
                && isset($arg_list[1])){
            array_shift($arg_list);
            $this->classes_dir = array_shift($arg_list);
        }
    }
    
    /*private function fail($msg){
        throw new \Exception("$msg". PHP_EOL) ;
    }*/
    
    private function message($message, $show_help=true){
        if($show_help){
            return $this->help($message);
        }
        echo "$message". PHP_EOL ;
    }
    
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
    
    private function listClasses(){
        $out =['classes list:'];
        foreach(glob($this->classes_dir .'/*.php') as $file){
            $out[] = preg_replace('/.php$/','',basename($file)) ;
        }
        $out[]='';
        return join(PHP_EOL,$out);
    }
}
