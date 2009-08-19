<?php

/* Since I don't have access to PEAR, we are going to use ghetto unit testing */
class UnitTestException extends Exception {

}


class UnitTest {

	private $debug;
	
	function __construct($debug = 0){
		$this->debug = $debug;
	}

	function assert_equals($foo, $bar, $msg=null){
		if($foo != $bar) {
			if($msg)
				throw new UnitTestException($msg);
			else
				throw new UnitTestException("'$foo' is not equal to '$bar'"); 
		}
	}
	
	function assert_null($foo, $msg=null){
		if($foo != null) {
			if($msg)
				throw new UnitTestException($msg);
			else
				throw new UnitTestException("Error: '$foo' is not null<br>"); 
		}
	}
	
	function assert_notnull($foo, $msg=null){
		if($foo == null) {
			if($msg)
				throw new UnitTestException($msg);
			else
				throw new UnitTestException("Error: '$foo' is null<br>"); 
		}
	}
	
	function assert_true($foo, $msg=null){
		if(!$foo) {
			if($msg)
				throw new UnitTestException($msg);
			else
				throw new UnitTestException("Error: '$foo' is not true<br>"); 
		}
	}
	
	function assert_false($foo, $msg=null){
		if($foo) {
			if($msg)
				throw new UnitTestException($msg);
			else
				throw new UnitTestException("Error: '$foo' is not false<br>"); 
		}
	}
	
	function run(){
		$methods = get_class_methods(get_class($this));
		echo "Running all the unit tests for " . get_class($this) . "<br><br>";
		foreach($methods as $method){
			if(strstr($method, "test")){
				if($this->debug)
					print $method . "<br>";
				try{
					$this->$method();
				} catch(UnitTestException $e){
					echo $method. ": " . $e->getMessage() . "<br>";
				}
			}
		}
		echo "<br>Done, errors listed above";
	}

}



?>