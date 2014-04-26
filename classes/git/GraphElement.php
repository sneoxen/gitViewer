<?php
namespace git;
/**
 * Description of GraphElement
 *
 * @author rbrunin
 */
class GraphElement {
	const pointSize=30;
	const pointMargin=80;
	const actionArrowSize=25;
	
	private $lastObjectBranch=null;/*@var $lastObjectBranch \git\Branch */
	
	private function drawPoint($actionNumber){
		?><div class="actionPoint <?= $this->lastObjectBranch->getBranchColor() ?>" style="top:<?= ($this->lastObjectBranch->getBranchNumber()*self::pointMargin) ?>px;left:<?= ($actionNumber*self::pointMargin)  ?>px;"><?php
				switch(History::getInst()->getAction($actionNumber)['type']){
					case'commit':
						echo'<span>Ci</span>';
						break;
					
					case 'checkout':
						echo '<span>Co</span>';
						break;
					
					case'merge':
						echo'<span>Me</span>';
						break;
					
					case 'initial':
						echo 'I';
						break;
					
					default:
						echo History::getInst()->getAction($actionNumber)['type'];
						break;
				}
			?></div><?php
	}
	
	function drawBranch($branchName){
		$this->lastObjectBranch=Branch::getInst($branchName);
		foreach($this->lastObjectBranch->getAction() as $actionNumber=>$actionType){
			$this->drawPoint($actionNumber);
			$this->drawInfoArrowBox($actionNumber);
			
			if($actionType=='commit' || $actionType=="checkout" || $actionType=="initial" || $actionType=="merge"){	
				$this->drawArrowFollow($actionNumber);
			}
			if($actionType=="checkout"){
				//var_dump(History::getInst()->getAction($actionNumber));
				$this->drawCheckoutArrowFollowBranch($actionNumber);
			}
		}
	}
	
	function drawCheckoutArrowFollowBranch($actionNumber){
		$aActionData=History::getInst()->getAction($actionNumber);
		$oBranchSource=Branch::getInst($aActionData['sourceBranch']);
		$oBranchDestination=Branch::getInst($aActionData['branch']);
		
		$branchOffsetNumber=$oBranchDestination->getBranchNumber()-$oBranchSource->getBranchNumber();
		$branchSize=$branchOffsetNumber*self::pointMargin;
		if($branchOffsetNumber>0){
			//down
			$angle=(pow(($branchSize+7)-(self::pointSize)+self::pointSize, 2)+pow(self::pointMargin,2));
			$angle=  sqrt($angle);
			$angle= (self::pointMargin)/$angle;
			$angle=  (180*sin($angle))/ pi();
			//$angle+= 4;
			?><div class="arrowFollowDown <?php echo $oBranchSource->getBranchColor() ?>" style="transform: rotate(-<?php echo $angle?>deg);height:<?= ($branchSize+7)-(self::pointSize)-($branchOffsetNumber*2) ?>px;top:<?= ($oBranchSource->getBranchNumber()*self::pointMargin)+21+$branchOffsetNumber ?>;left:<?= (($actionNumber-1)*self::pointMargin)+(self::pointSize/2)+15-(2*$branchOffsetNumber) ?>px">
				<span class="body" style="
					  background: linear-gradient(
						to bottom, 
						<?= $oBranchSource->getBranchColor('second') ?> 0%,
						<?= $oBranchSource->getBranchColor('second') ?> 45%,
						<?= $oBranchDestination->getBranchColor('second') ?> 55%,
						<?= $oBranchDestination->getBranchColor('second') ?> 100%
					);
				"></span>
				<span class="endWhite"></span>
				<span class="end <?= $oBranchDestination->getBranchColor() ?>"></span>
			</div><?php
		}
		else{
			return true;
			$branchSize*=-1;
			//up
			?><div class="arrowFollowUp" style="height:<?= $branchSize-(2*self::pointSize/3) ?>px;top:<?= ($branchOffsetNumber*self::pointMargin) ?>;left:<?= (($actionNumber-1)*self::pointMargin)+(self::pointSize/2)  ?>px">
				<span class="body"></span>
				<span class="endWhite"></span>
				<span class="end"></span>
			</div><?php
		}
		
	}
	
	function drawInfoArrowBox($actionNumber){
		?><div class="arrow_box" style="top:<?= ($this->lastObjectBranch->getBranchNumber()*self::pointMargin)+(self::pointSize+10) ?>px;left:0px;">
			<span class="<?= $this->lastObjectBranch->getBranchColor() ?>"></span>
			<div class="<?= $this->lastObjectBranch->getBranchColor() ?>">
				<?php 
					$aActionInfo=History::getInst()->getAction($actionNumber);
					echo '<span class="titleActionType">'. $aActionInfo['type'] .'</span>';
					echo 'Branche : '.$aActionInfo['branch'].'<br />';
					echo 'Hash : '.substr($aActionInfo['ident'],0,7);
				?>
			</div>
		</div><?php
	}
	
	function drawArrowFollow($actionNumber){
		
		//$oBranch=Branch::getObjectByNumber($actionNumber);
		
		$nextActionNumber=$this->lastObjectBranch->getNextActionNumber($actionNumber);
		if($nextActionNumber-$actionNumber<=0) return false;
		?><div class="arrowFollowRight" style="width:<?= ($nextActionNumber-$actionNumber)*(self::pointMargin)-50 ?>px;top:<?= ($this->lastObjectBranch->getBranchNumber()*self::pointMargin) ?>;left:<?= ($actionNumber*self::pointMargin)+self::pointSize+15  ?>px">
			<span class="body <?php echo $this->lastObjectBranch->getBranchColor() ?>"></span>
			<span class="endWhite"></span>
			<span class="end <?php echo $this->lastObjectBranch->getBranchColor() ?>"></span>
		</div><?php
	}
}