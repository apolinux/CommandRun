<?php

namespace Apolinux\ClassManager;

class AnonymousClassType implements ClassTypeInterface{
    
    private $class_list ;
    
    public function __construct($class_list){
        $this->class_list = $class_list ;
    }

    private function exist(string $classname): bool {
        return isset($this->class_list[$classname]);
    }

    public function getList(): array {
        return array_keys($this->class_list) ;
    }

    public function instance(string $classname, $params=null) {
        if( ! $this->exist($classname)){
            throw new ClassNotFoundException($classname);
        }
        
        $classres = $this->class_list[$classname] ;
        
        if(! is_object($classres) ||
               strstr( get_class($classres), 'anonymous') === FALSE
               ){
                throw new ClassNotDefinedException($classname) ;
            }
        
        
        return new $classres ;
    }
}
