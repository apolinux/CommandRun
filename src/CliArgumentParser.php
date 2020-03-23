<?php

namespace Apolinux;

/**
 * parse argument list
 * 
 */
class CliArgumentParser{
    
    /**
     * parse argument list of form:
     * *  --param1=value1 -p=t --param2 -q -help other
     * using regular expresions
     *  * param double : of form: --paramx=valuex or --paramx
     *  * param extra: -help -h or paramY
     * @param array $args argument list
     * @return array containing param double and param extra
     */
    public function parse($args){
        $param_double=[] ; // --param=value or --param value or --param
        $param_extra=null;
        $cont=0; 
        foreach($args as $argument){
            $cont++;
            if(preg_match('/^--(\w+)=(.*)$/', $argument, $match)){
                $parameter=$match[1];
                $value=$match[2];
                $param_double[$parameter]=$value ;
            }elseif(preg_match('/^--(\w+)$/', $argument, $match)){
                $parameter=$match[1];
                $param_double[$parameter]=null;
            }elseif(preg_match('/^-(help|h)$/', $argument, $match)){
                //$parameter=$match[1];
                $param_extra='help';
            }elseif(preg_match('/^\w+$/',$argument,$match) && $cont==1){
                //echo "aca: ".print_r($match,1);
                $param_extra=$match[0] ;
            }
        }
        return [ $param_double , $param_extra ];
    }
}