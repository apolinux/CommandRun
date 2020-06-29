<?php

use PHPUnit\Framework\TestCase;
use \Apolinux\ClassManager\AnonymousClassType;
use \Apolinux\ClassManager\InternalClassType ;
use \Apolinux\ClassManager\ClassContainer;
use \Apolinux\ClassManager\DirectoryClassType;

class CommandRunTest extends TestCase{
    
    public function testRunCmd(){
        // define enviroment and input arguments
        $argv=['fido', 'TestRunClass', '--param1=555','--param2=true'];
        
        // run command
        ob_start();
        // define command running class
        $class_container = new ClassContainer();
        $class_container->add(new DirectoryClassType(__DIR__ .'/Commands'));
        $cmd = new \Apolinux\CommandRun($class_container);
        
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('receive parameters', $output);
        $this->assertStringContainsString('555', $output);
        $this->assertStringContainsString('param2', $output);
    }
    
    public function testRunCmdHelp(){
        // define enviroment and input arguments
        $argv=['fido', '-h'];
        
        // define command running class
        
        // run command
        ob_start();
        $class_container = new ClassContainer();
        $class_container->add(new DirectoryClassType(__DIR__ .'/Commands'));
        $cmd = new \Apolinux\CommandRun($class_container);
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('shows this help', $output);
        $this->assertStringContainsString('Usage:', $output);
        
        $argv=['fido', '--help'];
        
        // define command running class
        
        // run command
        ob_start();
        $class_container = new ClassContainer();
        $class_container->add(new DirectoryClassType(__DIR__ .'/Commands'));
        $cmd = new \Apolinux\CommandRun($class_container);
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('shows this help', $output);
        $this->assertStringContainsString('Usage:', $output);
    }
    
    public function testRunCmdList(){
        // define enviroment and input arguments
        $argv=['fido', '-l'];
        
        // run command
        ob_start();
        $class_container = new ClassContainer();
        $class_container->add(new DirectoryClassType(__DIR__ .'/Commands'));
        $cmd = new \Apolinux\CommandRun($class_container);
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('classes list', $output);
        $this->assertStringContainsString('TestRunClass', $output);
        $this->assertStringContainsString('TestProcess', $output);
    }
    
    public function testRunCmdClassHelp(){
        // define enviroment and input arguments
        $argv=['fido', 'TestRunClass', '-h'];
        
        // run command
        ob_start();
        $class_container = new ClassContainer();
        $class_container->add(new DirectoryClassType(__DIR__ .'/Commands'));
        $cmd = new \Apolinux\CommandRun($class_container);
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('method list for class', $output);
        $this->assertStringContainsString('::run [default] ( int $param1', $output);
    }
    
    public function testRunCmdMissingParameter(){
        // define enviroment and input arguments
        $argv=['fido', 'TestRunClass', 'run'];
        
        // run command
        ob_start();
        $class_container = new ClassContainer();
        $class_container->add(new DirectoryClassType(__DIR__ .'/Commands'));
        $cmd = new \Apolinux\CommandRun($class_container);
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString("The parameter 'param1' is not defined", $output);
    }
    
    public function testRunCmdListingMethods(){
        // define enviroment and input arguments
        $argv=['fido', 'TestRunClass', '-h'];
        
        // run command
        ob_start();
        $class_container = new ClassContainer();
        $class_container->add(new DirectoryClassType(__DIR__ .'/Commands'));
        $cmd = new \Apolinux\CommandRun($class_container);
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString(' * ::run [default] ( int $param1', $output);
    }
    
    public function testRunCmdClassNotValid(){
        // define enviroment and input arguments
        $argv=['fido', 'NotValidClass'];
        
        // run command
        ob_start();
        $class_container = new ClassContainer();
        $class_container->add(new DirectoryClassType(__DIR__ .'/Commands'));
        $cmd = new \Apolinux\CommandRun($class_container);
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString("The classname 'NotValidClass' was not found in class pool", $output);
    }
    
    public function testRunCmdMethodNotValid(){
        // define enviroment and input arguments
        $argv=['fido', 'TestRunClass' ,'notvalidmethod'];
        
        // run command
        ob_start();
        $class_container = new ClassContainer();
        $class_container->add(new DirectoryClassType(__DIR__ .'/Commands'));
        $cmd = new \Apolinux\CommandRun($class_container);
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString("Method TestRunClass::notvalidmethod() does not exist", $output);
    }
    
    public function testContainerClassAnonynmous(){
        // define enviroment and input arguments
        
        $class_container = new ClassContainer();
        $class_list = [
            'testanonymous1' => new class{
                public function run(){
                    echo 'in class testanonymous1' ;
                }
            } ,
            'testanonymous2' => new class{
                  public function run(){
                      echo 'in class testanonymous2' ;
                  }
            }
        ];
        $class_container->add(new AnonymousClassType($class_list));
        
        $cmd = new \Apolinux\CommandRun($class_container);
        $argv=['fido', 'testanonymous1' ,'run'];
        ob_start();
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('in class testanonymous1', $output);
        
        $argv=['fido', 'testanonymous2' ,'run'];
        ob_start();
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('in class testanonymous2', $output);
        
    }
    
    public function testContainerClassInternalDeclared(){
        $class_container = new ClassContainer();
        
        $class_container->add(new InternalClassType(['testInternal',testInternal2::class ]) );  // define explicitamente las clases
        
        $cmd = new \Apolinux\CommandRun($class_container);
        $argv=['fido', 'testInternal' ,'run'];
        ob_start();
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('in class testInternal', $output);
        
        $argv=['fido', 'testInternal2' ,'run'];
        ob_start();
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('in class testInternal2', $output);
    }
    
    public function testContainerDirClass(){
        $class_container = new ClassContainer();
        $class_container->add(new DirectoryClassType(__DIR__ .'/Commands'));
        
        $cmd = new \Apolinux\CommandRun($class_container);
        $argv=['fido', 'TestProcess' ,'run'];
        ob_start();
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString("I'm here!", $output);
        
        $argv=['fido', 'TestRunClass' ,'run','--param1=1','--param2=2'];
        ob_start();
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('in method', $output);
    }
    
    public function testListAllTypes(){
        // define enviroment and input arguments
        $argv=['fido', '-l'];
        
        $class_list = [
            'testanonymous1' => new class{
                public function run(){
                    echo 'in class testanonymous1' ;
                }
            } ,
            'testanonymous2' => new class{
                  public function run(){
                      echo 'in class testanonymous2' ;
                  }
            }
        ];
        
        // run command
        ob_start();
        $class_container = new ClassContainer();
        $class_container->add(new DirectoryClassType(__DIR__ .'/Commands'));
        $class_container->add(new AnonymousClassType($class_list));
        $class_container->add(new InternalClassType(['testInternal',testInternal2::class ]) );  // define explicitamente las clases
        
        $cmd = new \Apolinux\CommandRun($class_container);
        $cmd->start($argv);
        $output = ob_get_clean();
        //echo "output:". $output ;
        // validate answer
        $this->assertStringContainsString('classes list', $output);
        $this->assertStringContainsString('TestRunClass', $output);
        $this->assertStringContainsString('TestProcess', $output);
        
        $this->assertStringContainsString('testanonymous1', $output);
        $this->assertStringContainsString('testanonymous2', $output);
        $this->assertStringContainsString('testInternal', $output);
        $this->assertStringContainsString('testInternal2', $output);
    }
}


class testInternal {
    public function run(){
        echo 'in class testInternal' ;
    }
};

class testInternal2{
    public function run(){
        echo 'in class testInternal2';
    }
};