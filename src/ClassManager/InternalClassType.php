<?php

namespace Apolinux\ClassManager;

class InternalClassType implements ClassTypeInterface{
    
    private $class_list ;
    
    public function __construct($class_list){
        $this->class_list = $class_list ;
    }

    private function exist(string $classname): bool {
        return in_array($classname, $this->class_list, true);
    }

    public function getList(): array {
        return $this->class_list ;
    }

    public function instance(string $classname, $params=null) {
        if( ! $this->exist($classname)){
            throw new ClassNotFoundException($classname);
        }
        
        if( $this->isInternal($classname) ){
                throw new ClassNotDefinedException($classname) ;
            }
        
        
        return new $classname ;
    }
    
    private function isInternal($classname){
        $reflect = new \ReflectionClass($classname);

        // Only user defined classes, exclude internal or classes added by PHP extensions.
        return  $reflect->isInternal() ;
    }
}
