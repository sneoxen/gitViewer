<?php
namespace git;
/**
 * Description of branch
 *
 * @author neoxen
 */
class Branch {
	static $aObjectBranch=array();
	static $branchColor=array(
		array('class'=>'black','first'=>'#4E4E4E','second'=>'#606060','third'=>'#CCCCCC'),
		array('class'=>'yellow','first'=>'#fbed37','second'=>'#edd355','third'=>'#ad9a3c'), // first => BG color | second : border color | third : text Color
		array('class'=>'blue','first'=>'#78CFEB','second'=>'#56AFE4','third'=>'#4587ae'),
		array('class'=>'green','first'=>'#BCD54B','second'=>'#9EC61C','third'=>'#778c31'),
		array('class'=>'grey','first'=>'#D0D1D1','second'=>'#ADADAE','third'=>'#808081'),
		array('class'=>'orange','first'=>'#F9A55E','second'=>'#F59044','third'=>'#c86921'),
		array('class'=>'purple','first'=>'#C899C6','second'=>'#AE74B2','third'=>'#8e5192'),
		array('class'=>'red','first'=>'#F2695F','second'=>'#E33B3C','third'=>'#c31c1c'),
		//array('class'=>'emerald','first'=>'#a0c79a','second'=>'#52864a','third'=>'#167d50'),
		array('class'=>'white','first'=>'#FFFFFF','second'=>'#E0E0E0','third'=>'#000000')
	);

	private $aActionOnBranch=array();
	private $branchNumber=0;
	private $displayGraphBranchNumber=0;
	private $isActualWorkingBranch=false;
	private $branchName=null;
	private $isAlive=false;


	private function __construct($branchName){
		$this->branchNumber=count(self::$aObjectBranch);
		$this->branchName=$branchName;
	}

	function setAlive(){
		$this->isAlive=true;
	}
	
	function isAlive(){
		return $this->isAlive;
	}
	
	function setActualWorkingBranch(){
		$this->isActualWorkingBranch=true;
		$this->setAlive();
	}

	function addAction($type,$actionNumber){
		$this->aActionOnBranch[$actionNumber]=$type;
	}

	function getCreationActionNumber(){
		return key($this->aActionOnBranch);
	}
	
	function getLastActionNumber(){
		$aKeys=array_keys($this->aActionOnBranch);
		return end($aKeys);

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

	function setDisplayGraphBranchNumber($number){
		$this->displayGraphBranchNumber=$number;
	}
	
	function getDisplayGraphBranchNumber(){
		return $this->displayGraphBranchNumber;
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

	function changeBranchName($newName){
		$aOldBranchObject=self::$aObjectBranch;
		self::$aObjectBranch=array();
		foreach($aOldBranchObject as $branchName=>$aBranchValue){
			if($branchName==$this->branchName) $branchName=$newName;
			self::$aObjectBranch[$branchName]=$aBranchValue;
		}
		$this->branchName=$newName;
	}
	
	function getBranchName(){
		return $this->branchName;
	}
	/**
	 * Singleton branch
	 * @param type $branchName
	 * @return self
	 */
	static function getInst($branchName){
		if(!isset(self::$aObjectBranch[$branchName])) self::$aObjectBranch[$branchName]=new self($branchName);
		return self::$aObjectBranch[$branchName];
	}

	static function getAllBranch(){
		return self::$aObjectBranch;
	}

	static function getObjectByNumber($actionNumber){
		$actionData=History::getInst()->getAction($actionNumber);
		return self::getInst($actionData['branch']);
	}

	static function getAllBranchColorInfo(){
		return self::$branchColor;
	}
}
