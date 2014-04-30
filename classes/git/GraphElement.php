<?php
namespace git;
/**
 * Description of GraphElement
 *
 * @author neoxen
 */
class GraphElement {
	const pointSize=30;
	const pointMargin=80;
	const actionArrowSize=25;

	static private $maxDisplayNumber=0;
	private $lastObjectBranch=null;/*@var $lastObjectBranch \git\Branch */

	private function drawPoint($actionNumber){
		?><div class="actionPoint <?= $this->lastObjectBranch->getBranchColor() ?>" style="top:<?= ($this->lastObjectBranch->getDisplayGraphBranchNumber()*self::pointMargin) ?>px;left:<?= ($actionNumber*self::pointMargin)  ?>px;"><?php
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
			if($actionType=='merge'){
				$this->drawMergeArrow($actionNumber);
			}
		}
	}

	function drawMergeArrow($actionNumber){
		$aActionData=History::getInst()->getAction($actionNumber);
		$branchSource=Branch::getInst($aActionData['branch']);
		$branchMerging=Branch::getInst($aActionData['mergingBranch']);
		
		$branchOffsetNumber=$branchSource->getDisplayGraphBranchNumber()-$branchMerging->getDisplayGraphBranchNumber();
		$direction='up';
		if($branchOffsetNumber<0){
			//merge with up arrow
			$branchOffsetNumber*=-1;
			$top=(($branchMerging->getDisplayGraphBranchNumber())*self::pointMargin)+6-(self::pointSize/3); //6 for border
			$left=(($actionNumber-1)*self::pointMargin)+(self::pointSize)+6;
		}
		else{
			//merge with down arrow
			$direction='down';
			$top=(($branchMerging->getDisplayGraphBranchNumber())*self::pointMargin)+6+(2*self::pointMargin/5); //6 for border
			$left=(($actionNumber-1)*self::pointMargin)+(self::pointSize)+6;
		}
			$arrowBodySize=($branchOffsetNumber*self::pointMargin)+6;//6 for border
			$realArrowBodySize=sqrt(pow($arrowBodySize-(1.5*self::pointSize),2)+pow(self::pointMargin-1.5*self::pointSize,2));
			
			$angle = (self::pointMargin)/($arrowBodySize);
			$angle = (180*atan($angle))/ pi();
			$angle=($direction=='up' ? 180-$angle : $angle);
			?>
			<div class="arrowFollowDown <?php echo $branchMerging->getBranchColor() ?>"
				 style="
					transform: rotate(-<?php echo $angle?>deg);
					transform-origin:0 0;
					height:<?= ($realArrowBodySize) ?>px;
					top:<?= $top ?>px;
					left:<?= $left ?>px">
				<span class="body" style="
					background: linear-gradient(
						to bottom,
						<?= $branchMerging->getBranchColor('second') ?> 0%,
						<?= $branchMerging->getBranchColor('second') ?> 40%,
						<?= $branchSource->getBranchColor('second') ?> 70%,
						<?= $branchSource->getBranchColor('second') ?> 100%
					);
				"></span>
				<span class="endWhite"></span>
				<span class="end <?= $branchSource->getBranchColor() ?>"></span>
			</div><?php
		
		//var_dump($branchOffsetNumber);
	}
	
	function drawCheckoutArrowFollowBranch($actionNumber){
		$aActionData=History::getInst()->getAction($actionNumber);
		$oBranchSource=Branch::getInst($aActionData['sourceBranch']);
		$oBranchDestination=Branch::getInst($aActionData['branch']);

		$branchOffsetNumber=$oBranchDestination->getDisplayGraphBranchNumber()-$oBranchSource->getDisplayGraphBranchNumber();
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
					top:<?= ($oBranchSource->getDisplayGraphBranchNumber()*self::pointMargin)+(1.2*self::pointSize) ?>px;
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
			// Not implement  because not use 
		}

	}

	function drawInfoArrowBox($actionNumber){
		?><div class="arrow_box" style="top:<?= ($this->lastObjectBranch->getDisplayGraphBranchNumber()*self::pointMargin)+(self::pointSize+10) ?>px;left:0px;">
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
					echo'<br />LastActionNumber : '.$this->lastObjectBranch->getLastActionNumber();
					echo'<br />Actual action : '.$actionNumber;
				?>
			</div>
		</div><?php
	}

	function drawArrowFollow($actionNumber){
		$nextActionNumber=$this->lastObjectBranch->getNextActionNumber($actionNumber);
		if($nextActionNumber-$actionNumber<=0) return false;
		?><div class="arrowFollowRight" style="width:<?= ($nextActionNumber-$actionNumber)*(self::pointMargin)-50 ?>px;top:<?= ($this->lastObjectBranch->getDisplayGraphBranchNumber()*self::pointMargin) ?>;left:<?= ($actionNumber*self::pointMargin)+self::pointSize+15  ?>px">
			<span class="body <?php echo $this->lastObjectBranch->getBranchColor() ?>"></span>
			<span class="endWhite"></span>
			<span class="end <?php echo $this->lastObjectBranch->getBranchColor() ?>"></span>
		</div><?php
	}
	
	static function setMaxDisplayNumber($number){
		self::$maxDisplayNumber=$number;
	}
	
	static function getMaxDisplayNumber(){
		return self::$maxDisplayNumber;
	}
}