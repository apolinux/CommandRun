<?php

namespace Apolinux;

use ReflectionMethod as ReflectionMethodP ;

/**
 * Reflection class to get information about classes, methods and parameters
 */
class ReflectionMethod {
    
    /**
     * Reflection over runclass::method and using argument list 
     * to get a parameter value list related to specified method
     * 
     * @param array $arg_proc
     * @param string $runclass
     * @param string $method
     * @return array list of parameter values
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
                    throw new \Exception("The parameter '$parameter->name' is not defined. Parameters are defined like '--paramX=valueX'.".
                            PHP_EOL . "Method definition: ". $this->getDetailsMethodParameters($rmethod));
                }
                $param_out[] = $this->getValueByType($parameter) ;
            }
        }

        return $param_out ;
    }
    
    /**
     * gets a description of parameters method
     * 
     * @param ReflectionMethodP $method
     * @return string
     */
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
    
    /**
     * get type of parameter 
     * 
     * @param \ReflectionParameter $parameter
     * @return string
     */
    private function getType(\ReflectionParameter $parameter){
        $type = $parameter->getType() ?? 'undefined' ;
        $type_name = is_object($type)? ($type->getName() ?? null ): 'undefined';
        return $type_name ;
    }
    
    /**
     * get parameter value according to type
     * 
     * @param \ReflectionParameter $parameter
     * @param mixed $value
     * @return mixed
     */
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
    
    /**
     * get a class method list as string
     * 
     * @param \ReflectionClass $rclass
     * @return string
     */
    public function getMethodList(\ReflectionClass $rclass){
        $out = ["method list for class: ". $rclass->getName()];
        foreach($rclass->getMethods(ReflectionMethodP::IS_PUBLIC) as $method){
            $out[] = " * ". $this->getDetailsMethodParameters($method);
        }
        return join(PHP_EOL, $out);
    }
    
    /**
     * get a parameter details list from some method with method name
     * 
     * @param ReflectionParameterP $method
     * @return string
     */
    private function getDetailsMethodParameters(ReflectionMethodP $method){
        $name = $method->getName();
        $name = ($name =='run' ? 'run [default]' : $name) ;
        $out = "::". $name . " ( " . 
                $this->getMethodParameters($method).
                " )";
        return $out ;
    }
    
    /**
     * Get a list of parameter details from some method
     * @param ReflectionMethodP $method
     * @return string
     */
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
    
    /**
     * get default value to be printed
     * 
     * @param mixed $value
     * @return mixed
     */
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
