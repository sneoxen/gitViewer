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
		$this->rewriteBranchNumberDisplay();
		$this->orderBranchByLifeTime();
		$this->displayGraph();
	}

	function createHistoryLine(){
		$aHistoryLine=Executor::getInst()->getHistoryLine();
		foreach($aHistoryLine as $formatStringInfo){
			History::getInst()->extractAndAddAction($formatStringInfo);
		}

	}

	function rewriteBranchNumberDisplay(){
		Executor::getInst()->getActiveBranch();
	}

	function orderBranchByLifeTime(){
		//by default, branch is create by first number creation
		/*@var $oBranch Branch*/
		var_dump('decallage');
		var_dump('decallage');
		var_dump('decallage');
		var_dump('decallage');
		var_dump('decallage');
		var_dump('decallage');
		var_dump('decallage');
		
		$aBranchLife=array(); // array with all branch during lifetime
		$incBranchNumber=0;
		$lastBranchNumber=0; // this variable contain the last display branch number at any time
		//$lastBranchName=null; //this variable contain last branchName lifetime
		//$oLastBranch=null; //this variable contain lastbranch treat
	//	$aTest=array(); // test
		
		$lastBranchNameAdd=null;
		foreach(Branch::getAllBranch() as $branchName=>$oBranch){
			
			//for each new branch we need to remove  branch with lifetime expire
			if($incBranchNumber>0){
				$aBackupLifeBranch=$aBranchLife;
				foreach($aBranchLife as $key=>$branchLifeName){
					$newBranchBeginActionNumber=$oBranch->getCreationActionNumber(); // first action number
					$lifeBranchLastNumber=Branch::getInst($branchLifeName)->getLastActionNumber();// last action number
							
					// if begin action number of new branch is greater than life branch, then delete  this branch from life branch array
					if($newBranchBeginActionNumber>$lifeBranchLastNumber)
						unset($aBackupLifeBranch[$key]);
				}
				$aBranchLife=$aBackupLifeBranch;
				
				//get last branch to know displayGrpahNumber
				$lastBranchNameAdd=end($aBranchLife);
				$lastBranchNumber=Branch::getInst($lastBranchNameAdd)->getDisplayGraphBranchNumber()+1;
			}
			
			
			
			$aBranchLife[$lastBranchNumber]=$oBranch->getBranchName();
			$oBranch->setDisplayGraphBranchNumber($lastBranchNumber);
			$incBranchNumber++;			
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
