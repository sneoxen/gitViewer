<?php
namespace git;
/**
 * Description of History
 *
 * @author rbrunin
 */
class History {
	private $aSearchPattern=array(
		'refLogAction'=>'/^(?P<identSha1>[a-zA-Z0-9]*) \| (?P<action>merge|checkout|clone|pull|commit \(initial\)|commit)[ ]?(?P<pullOption>[a-z]+ [a-z]+)?:? (?P<actionComment>.*)/',
		'checkout'=>'/^moving from (?P<branchSource>.*) to (?P<branchDestination>.*)$/',
		'clone'=>'|^from (?P<protocol>[a-z]*)://(?P<domain>[a-z0-9@\._-]*):?(?P<port>[0-9]*)?(?P<urlName>.*)\|commit (initial)*|i',
		'checkout'=>'/^moving from (?P<branchSource>.*) to (?P<branchDestination>.*)$/',
		'merge'=>'/^(?P<branchMerge>[a-z0-9_\.-]*)(: Fast-forward)?$/i',
		'refLogRenaming'=>'|^(?P<branchCommitSha1>[a-z0-9]*) \| Branch: renamed refs/heads/(?P<branchToRename>.*) to refs/heads/(?P<branchRename>.*)$|i'
		//edb37a1a83773b89b27f42a2b872f221a7987302 | Branch: renamed refs/heads/branchToGoRename to refs/heads/branchIsRename
		//release-newDesign-adaptAllPage: Fast-forward
	);
	private $lastBranchSwitch='master';
	
	static private $inst=null;
	
	private $aActions=array();
	private $aActionByType=array();
	private $incActionIncrement=0;
	
	private function __construct(){
		
	}
	
	function extractAndAddAction($formatString){
		$aLineDataMatch=array();
		preg_match($this->aSearchPattern['refLogAction'],$formatString,$aLineDataMatch);
		
		if(!isset($aLineDataMatch['action']) || $aLineDataMatch['action']==null){
			return false;
		}

		$this->addAction($aLineDataMatch['action'],$aLineDataMatch['identSha1'],$aLineDataMatch['actionComment']);
	}
	
	function addAction($type,$ident,$actionComment){
		switch($type){
			case 'clone':
			case 'commit (initial)':
				//create branch master
				$aLineDataMatch=array();
				preg_match($this->aSearchPattern['clone'],$actionComment,$aLineDataMatch);
				if(empty($aLineDataMatch)) $aLineDataMatch=array(
					'urlName'=>substr($actionComment,5)
				);
				//$branchName=Executor::getInst()->getRefBranchNameFromHash($ident);
				$this->aActions[$this->incActionIncrement]=array(
					'type'=>'initial',
					'ident'=>$ident,
					'action'=>$actionComment,
					'source'=>$aLineDataMatch['urlName'],
					'branch'=>$this->lastBranchSwitch,
				);
				
				Branch::getInst($this->lastBranchSwitch)->addAction('initial', $this->incActionIncrement);
				break;
			
			case'checkout':
				$aLineDataMatch=array();
				preg_match($this->aSearchPattern['checkout'],$actionComment,$aLineDataMatch);
				
				$this->aActions[$this->incActionIncrement]=array(
					'type'=>'checkout',
					'ident'=>$ident,
					'action'=>$actionComment,
					'sourceBranch'=>$aLineDataMatch['branchSource'],
					'branch'=>$aLineDataMatch['branchDestination'],
				);
				
				//if is not the first checkout, then don't add this action on branch because it's not usefull for the future on this branch
				$this->lastBranchSwitch=$aLineDataMatch['branchDestination'];
				if(Branch::getInst($this->lastBranchSwitch)->getBranchActionNumber()>0) return false; 
				Branch::getInst($this->lastBranchSwitch)->addAction('checkout', $this->incActionIncrement);
				break;
			
			case'commit':
				$aLineDataMatch=array();
				$this->aActions[$this->incActionIncrement]=array(
					'type'=>'commit',
					'ident'=>$ident,
					'action'=>$actionComment,
					'branch'=>$this->lastBranchSwitch,
				);
				
				Branch::getInst($this->lastBranchSwitch)->addAction('commit', $this->incActionIncrement);
				break;
			
			case'merge':
				$aLineDataMatch=array();
				
				preg_match($this->aSearchPattern['merge'],$actionComment,$aLineDataMatch);
				if(empty($aLineDataMatch)) return false;
				$this->aActions[$this->incActionIncrement]=array(
					'type'=>'merge',
					'ident'=>$ident,
					'action'=>$actionComment,
					'branch'=>$this->lastBranchSwitch,
					'mergingBranch'=>$aLineDataMatch['branchMerge']
				);
				
				Branch::getInst($this->lastBranchSwitch)->addAction('merge', $this->incActionIncrement);
				break;
				
			default:
				//var_dump($type);
				return false;
				break;
		}
		end($this->aActions);
		$lastKey=key($this->aActions);
		reset($this->aActions);
		$this->aActionByType[$this->aActions[$lastKey]['type']][]=&$this->aActions[$lastKey];
		$this->incActionIncrement++;
	}
	
	
	function getAction($inc=null){
		return $inc===null ? $this->aActions : $this->aActions[$inc];
	}
	
	function getActionNumber(){
		return count($this->aActions);
	}
	/**
	 * 
	 * @return self
	 */
	static function getInst(){
		if(!is_object(self::$inst))self::$inst=new self();
		return self::$inst;
	}
}
