<?php
namespace git;
/**
 * Description of Executor
 *
 * @author neoxen
 */
class Executor {
	private $projectPathFile=null;
	private $aGitCmdExec=array();
	static private $instance=null;

	private function __construct(){
		//git reflog show --all | grep 9c2153c1899ad03b505cd2ca544466eb1fca4ae9
	}

	function setPathFile($pathfileProject){
		$this->projectPathFile=substr($pathfileProject,-1)!='/' ? $pathfileProject.'/' : $pathfileProject;
	}

	/**
	 * Singleton to get the same instance of object
	 * @return self
	 */
	public static function getInst(){
		if(!is_object(self::$instance)) self::$instance=new self();
		return self::$instance;
	}

	function getHistoryLine(){
		return array_reverse($this->execGitCmd('log -g --oneline --format="%H | %gs"'));
	}

	function execGitCmd($cmd){
		$cmdOutpout=array();
		exec('git --git-dir='. $this->projectPathFile.'.git '.$cmd, $cmdOutpout);
		$this->aGitCmdExec[]='git --git-dir='. $this->projectPathFile.'.git '.$cmd;
		return $cmdOutpout;
	}

	function getActiveBranch(){
		$aActualBranch=$this->execGitCmd('branch');
		foreach($aActualBranch as &$branchName){
			if(substr($branchName,0,1=='*')) Branch::getInst(substr($branchName,2))->setActualWorkingBranch();
			$branchName=substr($branchName,2);
		}
		return $aActualBranch;
	}

	function getCmd($index=null){
		if($index===null)	return $this->aGitCmdExec;
		else if($index==='last') return end($this->aGitCmdExec);
		else return $this->aGitCmdExec[$index];
	}

	function getLastCmd(){
		return $this->getCmd('last');
	}

	/**
	 * DON'T WORK
	 * @param type $hash
	 * @return array
	 */
	function getRefBranchNameFromHash($hash){
		$stringBranchFormat = $this->execGitCmd('reflog show --all | grep '.substr($hash,0,7));
		var_dump($stringBranchFormat);
		$stringBranchFormat=explode(' ',$stringBranchFormat[0]);
		$stringBranchFormat=explode('/',$stringBranchFormat[1]);

		//at this moment source seem to be this :  branchName@{XXX}:
		$patternExtractBranchName='/^(?P<branchName>.*)@{[0-9]+}:$/';
		$aLineDataMatch=array();
		preg_match($patternExtractBranchName,$stringBranchFormat[2],$aLineDataMatch);

		return $aLineDataMatch['branchName'];
	}
}