<?php

namespace Apolinux;

class GetOpts{
    
    /**
     * 
     * argument is like the forms:
     * - ^--(\w+)=(.*)$
     * - ^-(\w)=(.*)$
     * - ^--(\w+)$  => in this case depend of previous config, can be an argument or not
     * - ^-(\w)$ => same as above
     * - ^(\w+)$ => and indexed value in parameters array
     * 
     * 
$longopts  = array(
    "required:",     // Required value
    "optional::",    // Optional value
    "option",        // No value
    "opt",           // No value
     */
    public function parseArgs($args,$short_options='', $long_options=[]){
        $param_simple=[] ; // -p=value or -p value or -p
        $param_double=[] ; // --param=value or --param value or --param
        $param_noarg =[] ; // paramx
        
        $short_options_proc = $this->parseShort($short_options);
        $long_options_proc = $this->parseLong($long_options);
        
        
        foreach($args as $argument){
            if(preg_match('/^--(\w+)=(.*)$/', $argument, $match)){
                $parameter=$match[1];
                $value=$match[2];
                $param_double[$parameter]=$value ;
            }elseif(preg_match('/^-(\w)=(.*)$/', $argument, $match)){
                $parameter=$match[1];
                $value=$match[2];
                $param_simple[$parameter]=$value ;
            }elseif(preg_match('/^--(\w+)$/', $argument, $match)){
                $parameter=$match[1];
                $param_double[$parameter]=null;
            }elseif(preg_match('/^-(\w+)$/', $argument, $match)){
                $parameter=$match[1];
                $param_simple[$parameter]=null;
            }elseif(preg_match('/^(\w+)$/', $argument, $match)){
                $parameter=$match[1];
                $param_noarg[]=$value ;
            }
        }
        
        return [
          'simple' => $param_simple ,
          'double' => $param_double ,
          'indexed' =>  $param_noarg ,
        ] ;
    }
    
    /**
     * $shortopts  = "";
$shortopts .= "f:";  // Required value
$shortopts .= "v::"; // Optional value
$shortopts .= "abc"; // These options do not accept values

     * @param type $short_options
     * @return string
     */
    private function parseShort($short_options){
        $charnum=strlen($short_options);
        $short_options_proc=[];
        $last=null;
        $num=0;
        for($i=0 ; $i< $charnum ; $i++){
            $c=$short_options[$i] ;
            if($c !== ':'){
                $short_options_proc[$c]=null;
                $last=$c;
                $num=0;
            }elseif($c== ':' && $last){
                $num++;
                if($num==1){
                    $short_options_proc[$c]='R';
                }elseif($num==2){
                    $short_options_proc[$c]='O';
                    $last=null;
                    $num=0;
                }else{
                    $last=null;
                    $num=0;
                }
            }
        }
        return $short_options_proc;
    }
}