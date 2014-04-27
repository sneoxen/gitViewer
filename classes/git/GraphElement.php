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
		$branchSize=($branchOffsetNumber*self::pointMargin);

		//because branch size is on rectangle triangle, we calcul hypotenuse to determine real branch size
		/*
		 * O\
		 * |\
		 * O \ <----- represent checkout arrow size between 2 point
		 * |  \
		 * O__O
		 *
		 */

		$realBranchSize=sqrt(pow($branchSize-(1.3*self::pointSize),2)+pow(self::pointMargin-1.3*self::pointSize/*+self::pointSize*/,2));
		if($branchOffsetNumber>0){
			//down
			$angle = (self::pointMargin-self::pointSize)/($branchSize-self::pointSize);
			$angle = (180*atan($angle))/ pi();
			//$angle+= 4;
			?><div class="arrowFollowDown <?php echo $oBranchSource->getBranchColor() ?>"
				 style="
					transform: rotate(-<?php echo $angle?>deg);
					transform-origin:0 0;
					height:<?= ($realBranchSize) ?>px;
					top:<?= ($oBranchSource->getBranchNumber()*self::pointMargin)+(1.2*self::pointSize) ?>;
					left:<?= (($actionNumber-1)*self::pointMargin)+(1.2*self::pointSize) ?>px">
				<span class="body" style="
					background: linear-gradient(
						to bottom,
						<?= $oBranchSource->getBranchColor('second') ?> 0%,
						<?= $oBranchSource->getBranchColor('second') ?> 40%,
						<?= $oBranchDestination->getBranchColor('second') ?> 70%,
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
					echo '<strong>Branche</strong> : '.$aActionInfo['branch'].'<br />';
					echo '<strong>Hash</strong> : '.substr($aActionInfo['ident'],0,7);
					if($aActionInfo['type']=='commit'){
						echo'<br /><strong>Message de commit</strong> : <p>'. $aActionInfo['action'] .'</p>';
					}
					elseif($aActionInfo['type']=='merge'){
						echo'<br /><strong>Merging branch</strong> : '.$aActionInfo['mergingBranch'];
					}
					elseif($aActionInfo['type']=='checkout'){
						echo'<br /><strong>Source branch</strong> : '.$aActionInfo['sourceBranch'];
					}
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