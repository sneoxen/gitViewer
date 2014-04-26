<?php
namespace git;
/**
 * Description of Interpretor
 *
 * @author rbrunin
 */
class Interpretor {
	
	function __construct($projectPathFile){
		Executor::getInst()->setPathFile($projectPathFile);
		$this->createHistoryLine();
		$this->setActiveBranch();
		//$this->orderBranchByLifeTime();
		$this->displayGraph();
	}
	
	function createHistoryLine(){
		$aHistoryLine=Executor::getInst()->getHistoryLine();
		foreach($aHistoryLine as $formatStringInfo){
			History::getInst()->extractAndAddAction($formatStringInfo);
		}
	}
	
	/**
	 * Add active branch parameters on branch already active on project and not delete
	 */
	function setActiveBranch(){
		
	}
	
	function orderBranchByLifeTime(){
		//by default, branch is create by first number creation
		/*@var $oBranch Branch*/
		$lastActionNumber=History::getInst()->getActionNumber();
		foreach(Branch::getAllBranch() as $branchName=>$oBranch){
			$creationNumber=$oBranch->getCreationActionNumber();
			$finishNumber=$oBranch->getLastActionNumber();
			$lifetime=$lastActionNumber-$finishNumber;
			$lifetime-=$creationNumber;
			$oGraphElement->drawBranch($branchName);
		}
	}
	
	function displayGraph(){
		$oGraphElement=new GraphElement();
		
		/*@var $oBranch Branch*/
		foreach(Branch::getAllBranch() as $branchName=>$oBranch){
			$oGraphElement->drawBranch($branchName);
		}
		//var_dump(Branch::getAllBranch());
	}
}
