<?php
namespace git;
/**
 * Description of Interpretor
 *
 * @author neoxen
 */
class Interpretor {	
	function __construct($projectPathFile){
		Executor::getInst()->setPathFile($projectPathFile);
		$this->createHistoryLine();
		$this->defineActiveBranch();
		$this->orderBranchByLifeTime();
		$this->displayGraph();
	}

	function createHistoryLine(){
		$aHistoryLine=Executor::getInst()->getHistoryLine();
		foreach($aHistoryLine as $formatStringInfo){
			History::getInst()->extractAndAddAction($formatStringInfo);
		}

	}

	function defineActiveBranch(){
		Executor::getInst()->getAndSetActiveBranch();
	}

	function orderBranchByLifeTime(){
		//by default, branch is create by first number creation
		/*@var $oBranch Branch*/
		
		$aBranchLife=array(); // array with all branch during lifetime
		$incBranchNumber=0;
		$lastBranchNumber=0; // this variable contain the last display branch number at any time
		$maxBranchNumber=0; // this variable contain max branch display number 
		$lastBranchNameAdd=null;
		foreach(Branch::getAllBranch() as $branchName=>$oBranch){
			
			//for each new branch we need to remove  branch with lifetime expire
			if($incBranchNumber>0){
				$aBackupLifeBranch=$aBranchLife;
				foreach($aBranchLife as $key=>$branchLifeName){
					$newBranchBeginActionNumber=$oBranch->getCreationActionNumber(); // first action number
					$lifeBranchLastNumber=Branch::getInst($branchLifeName)->getLastActionNumber();// last action number
							
					// if begin action number of new branch is greater than life branch, then delete  this branch from life branch array
					if($newBranchBeginActionNumber>$lifeBranchLastNumber && !Branch::getInst($branchLifeName)->isAlive())
						unset($aBackupLifeBranch[$key]);
				}
				$aBranchLife=$aBackupLifeBranch;
				
				//get last branch to know displayGrpahNumber
				$lastBranchNameAdd=end($aBranchLife);
				$lastBranchNumber=Branch::getInst($lastBranchNameAdd)->getDisplayGraphBranchNumber()+1;
			}
			
			$aBranchLife[$lastBranchNumber]=$oBranch->getBranchName();
			if($lastBranchNumber>$maxBranchNumber)$maxBranchNumber=$lastBranchNumber;
			$oBranch->setDisplayGraphBranchNumber($lastBranchNumber);
			$incBranchNumber++;
		}
		GraphElement::setMaxDisplayNumber($maxBranchNumber);
		
	}

	function displayGraph(){
		$oGraphElement=new GraphElement();
		
		//get active branch
		
		
		/*@var $oBranch Branch*/
		foreach(Branch::getAllBranch() as $branchName=>$oBranch){
			$oGraphElement->drawBranch($branchName);
		}
	}
}
