<?php

CONST OPRULE = [
    '-' => 1,
    '+' => 2,
    '*' => 3,
    '/' => 4,
];

class TestController
{
    
    public function myTest($str){        

        $result = $this->calculator($str);        
        return 'Result: '.$result;
        
    }

    private function calculator($mathStr){
        
        $splitAry = [];        
        preg_match_all('~(?:\d+(?:\.\d+)?|\w)+|[^\s\w]~',$mathStr,$splitAry);
        return $this->genResult($splitAry[0]);

    }

    private function genResult($splitAry){
        
        $holdStack = [];
        $output = [];
        foreach($splitAry as $v){
            if(is_numeric($v)){
                array_push($output, (float)$v);
            }else{
                // By reference $output, $holdStack
                $this->resolveExpression($output, $holdStack, $v);
            }    
        }

        // By reference $output
        $this->finalizeOutput($output, $holdStack);

        $solveStack = [];
        while(count($output) != 0){

            $curPop = array_shift($output);
            if(is_numeric($curPop)){
                array_push($solveStack,$curPop);
            }else{
                // Run into operator, init math process
                $second = array_pop($solveStack);
                $first = array_pop($solveStack);
                $curRes = $this->calculateResult($first, $second, $curPop);
                array_push($solveStack, $curRes);
            }

        }        
        return $curRes;   
    }

    public function calculateResult($first, $second, $op)
    {
        switch($op){
            case '-':
                return $first-$second;        
                break;
            case '+':
                return $first+$second;        
                break;
            case '*':
                return $first*$second;        
                break;
            case '/':
                return $first/$second;        
                break;
        }
    }

    private function finalizeOutput(&$output, $holdStack)
    {
        $length = count($holdStack);
        for($i=0;$i<$length;$i++){
            array_push($output, array_pop($holdStack));
        }
    }

    private function resolveExpression(&$output, &$holdStack, $currentValue)
    {
        
        $lk = array_key_last($holdStack);
        if(!$holdStack){
            array_push($holdStack, $currentValue);
            return;
        }
        
        while(OPRULE[$holdStack[$lk]] > OPRULE[$currentValue]){
            
            $tmpHome = array_pop($holdStack);
            array_push($output,$tmpHome);
            $lk--;
            if($lk<0) break;

        }
        array_push($holdStack, $currentValue);        
        return;
    }

}

$test = new TestController();
echo $test->myTest('2*2+1*3');
