<?php
namespace git;
/**
 * Description of branch
 *
 * @author rbrunin
 */
class Branch {
	static $aObjectBranch=array();
	static $branchColor=array(
		0=>array('class'=>'yellow','first'=>'#fbed37','second'=>'#edd355','third'=>'#ad9a3c'), // first => BG color | second : border color | third : text Color
		1=>array('class'=>'blue','first'=>'#78CFEB','second'=>'#56AFE4','third'=>'#4587ae'),
		2=>array('class'=>'green','first'=>'#BCD54B','second'=>'#9EC61C','third'=>'#778c31'),
		3=>array('class'=>'orange','first'=>'#F9A55E','second'=>'#F59044','third'=>'#c86921'),
		4=>array('class'=>'purple','first'=>'#C899C6','second'=>'#AE74B2','third'=>'#8e5192'),
		5=>array('class'=>'red','first'=>'#F2695F','second'=>'#E33B3C','third'=>'#c31c1c'),
		6=>array('class'=>'grey','first'=>'#D0D1D1','second'=>'#ADADAE','third'=>'#808081')
	);
	
	private $aActionOnBranch=array();
	private $branchNumber=0;
	
	private function __construct(){
		$this->branchNumber=count(self::$aObjectBranch);
	}
	
	function addAction($type,$actionNumber){	
		$this->aActionOnBranch[$actionNumber]=$type;
	}
	
	function getCreationActionNumber(){
		return key($this->aActionOnBranch);
	}
	function getLastActionNumber(){
		$endNumber=key(end($this->aActionOnBranch));
		reset($this->aActionOnBranch);
		return $endNumber;
	}
	
	function getBranchActionNumber(){
		return count($this->aActionOnBranch);
	}
	
	function getAction(){
		return $this->aActionOnBranch;
	}
	
	function getBranchNumber(){
		return $this->branchNumber;
	}
	
	function getNextActionNumber($beginStartNumber){
		$captureNextKey=false;
		foreach($this->aActionOnBranch as $actionNumber=>$aData){
			if($actionNumber==$beginStartNumber){
				$captureNextKey=true;
			}
			elseif($captureNextKey===true) return $actionNumber;
		}
		return $actionNumber;
	}
	
	function getBranchColor($type='class'){
		$offsetNumber=$this->getBranchNumber()/count(self::$branchColor);
		$offsetNumber=floor($offsetNumber);
		
//		var_dump($this->getBranchNumber()-(count(self::$branchColor)*$offsetNumber));
		return self::$branchColor[$this->getBranchNumber()-(count(self::$branchColor)*$offsetNumber)][$type];
	}
	/**
	 * Singleton branch
	 * @param type $branchName
	 * @return self
	 */
	static function getInst($branchName){
		if(!isset(self::$aObjectBranch[$branchName])) self::$aObjectBranch[$branchName]=new self();
		return self::$aObjectBranch[$branchName];
	}
	
	static function getAllBranch(){
		return self::$aObjectBranch;
	}
	
	static function getObjectByNumber($actionNumber){
		$actionData=History::getInst()->getAction($actionNumber);
		return self::getInst($actionData['branch']);
	}
}
