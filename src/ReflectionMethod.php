<?php

namespace Apolinux;

use ReflectionMethod as ReflectionMethodP ;

class ReflectionMethod {
    
    /**
     * reflection over runclass::method to get parameters
     * 
     * @param type $arg_list
     * @param type $runclass
     * @param type $method
     */
    public function getParams($arg_proc, $runclass,$method){
        $rmethod = new \ReflectionMethod($runclass, $method);
        
        $parameter_list = $rmethod->getParameters();
        $param_out=[];
        foreach($parameter_list as $parameter){
            /* @var $parameter \ReflectionParameter */
            if(array_key_exists($parameter->name, $arg_proc)){
                $param_out[] = $this->getValueByType($parameter,$arg_proc[$parameter->name]) ;
            }else{
                if(! $parameter->isOptional()){
                    throw new \Exception("The parameter '$parameter->name' is not defined.".
                            PHP_EOL . "Definition: ". $this->getDetailsMethodParameters($rmethod));
                }
                $param_out[] = $this->getValueByType($parameter) ;
            }
        }

        return $param_out ;
    }
    
    public function getParameterList(\ReflectionMethod $method){
        $parameter_list = $method->getParameters();
        $param_out=[
          "method '". $method->getDeclaringClass()->name ."::" . $method->getName() ."' runs with " . 
          count($parameter_list) .' parameters:' ,
        ];
        $cont=1 ;
        foreach($parameter_list as $parameter){
            /* @var $parameter \ReflectionParameter */
            $param_out[] = sprintf("%s. (%s) %s : %s",$cont++, 
                    $this->getType($parameter), $parameter->name,
                    $parameter->isOptional() ? 'optional' : 'required');
        }
        
        return join(PHP_EOL, $param_out);
    }
    
    private function getType(\ReflectionParameter $parameter){
        $type = $parameter->getType() ?? 'undefined' ;
        $type_name = is_object($type)? ($type->getName() ?? null ): 'undefined';
        return $type_name ;
    }
    
    private function getValueByType(\ReflectionParameter $parameter, $value=null){
        $type = $parameter->getType() ?? 'string' ;
        $type_name = is_object($type)? ($type->getName() ?? null ): null ;
        /* @var $type \ReflectionType */
        switch($type_name){
            case 'int':    $out = (int)$value ; break ;

            case 'bool':   $out = in_array(strtolower($value) ,['1', 'true'],true)
                                          ? true : false ; break ;
            default:
            case 'string': $out = (string)$value ; break ;    
        }
        return $out ;
    }
    
    public function getMethodList(\ReflectionClass $rclass){
        $out = ["method list for class: ". $rclass->getName()];
        foreach($rclass->getMethods(ReflectionMethodP::IS_PUBLIC) as $method){
            $out[] = " * ". $this->getDetailsMethodParameters($method);
        }
        return join(PHP_EOL, $out);
    }
    
    private function getDetailsMethodParameters($method){
        $name = $method->getName();
        $name = ($name =='run' ? 'run [default]' : $name) ;
        $out = "::". $name . " ( " . 
                $this->getMethodParameters($method).
                " )";
        return $out ;
    }
    
    public function getMethodParameters(\ReflectionMethod $method){
        $parameter_list = $method->getParameters();
        $param_out=[];
        foreach($parameter_list as $parameter){
            /* @var $parameter \ReflectionParameter */
            $extra = ($parameter->isOptional() ? 
                    '='.$this->defaultValue($parameter->getDefaultValue()): 
                    '');
            $param_out[] = $this->getType($parameter) . ' $'. $parameter->name . ($extra);
        }
        return join(', ', $param_out);
    }
    
    private function defaultValue($value){
        if(is_null($value)){
            return 'null';
        }
        if($value==''){
            return "''";
        }
        return $value ;
    }
}
