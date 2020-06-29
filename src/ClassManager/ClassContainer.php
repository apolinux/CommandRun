<?php


namespace Apolinux\ClassManager;

class ClassContainer {
    
    private $class_list = [] ;
    
    public function add(ClassTypeInterface $class_type) : void {
        $this->class_list[] = $class_type ;
    }
    
    public function instance(string $classname) {
        $instance = null ;
        foreach($this->class_list as $class_group){
            /* @var $class_group ClassTypeInterface  */
            //if( $class_group->exist($classname)){
            $instance = $class_group->instance($classname);
            if($instance){
                break ;
            }
        }
        if(! $instance){
            throw new ClassNotFoundException($classname);
        }
        
        return $instance ;
    }
    
    /**
     * @todo use yield?
     * @return array
     */
    public function listClasses(){
        $listc = [];
        foreach($this->class_list as $class_group){
            /* @var $class_group ClassTypeInterface  */
            $listc = array_merge($listc, $class_group->getList());
        }
        
        return $listc ;
    }
}
