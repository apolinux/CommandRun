<?php

namespace Apolinux;

class CliArgumentParser{
    
    /*public function parseForCmd($args, $params){
        $out=[] ;
        foreach($args as $argument){
            // of form: --ab=cd -a=mn --a -b
            if(preg_match('/^(?|(?:--(\w+))|(?:-(\w)))(?:=(.*))?$/', $argument, $match)){
                $parameter=$match[1];
                $value=$match[2] ?? null;
                
                $out[$parameter]=$value ;
            }
        }
        
        return $out ;
    }*/
    
    /**
     * 
     * argument is like the forms:
     * - ^--(\w+)=(.*)$
     * - ^--(\w+)$  => in this case depend of previous config, can be an argument or not
     * 
     */
    public function parse($args){
        $param_double=[] ; // --param=value or --param value or --param
        $param_extra=null;
        $cont=0; //echo "args:". print_r($args,1);
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