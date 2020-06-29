<?php

namespace Apolinux\ClassManager ;

class DirectoryClassType implements ClassTypeInterface{

    private $classes_dir ;
    
    public function __construct($classes_dir){
        $this->classes_dir = $classes_dir ;
    }
    
    public function getList(): array {
        $out = [] ;
        foreach(glob($this->classes_dir .'/*.php') as $file){
            $out[] = preg_replace('/.php$/','',basename($file)) ;
        }
        return $out ;
    }

    public function instance(string $classname, $params=null) {
        $class_file = $this->classes_dir .'/' . $classname. '.php' ;
        
        if(! file_exists($class_file)){
            //return $this->message("file '$class_file' of class '$runclass' can not be loaded") ;
            throw new ClassNotFoundException($classname) ;
        }
        
        require_once $class_file;
        
        /*if( ! $this->exist($classname)){
            throw new ClassNotFoundException($classname);
        }
        
        if( $this->isInternal($classname) ){
                throw new ClassNotDefinedException($classname) ;
            }
        
        */
        if( ! class_exists($classname)){
            throw new ClassNotDefinedException($classname) ;
        }
        
        return new $classname ;
    }
}
