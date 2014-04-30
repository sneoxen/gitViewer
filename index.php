<?php
spl_autoload_register(function($class) {
	$class = str_replace('\\', '/', $class);
	if (is_readable('classes/' . $class . '.php'))
		require 'classes/' . $class . '.php';
});
error_reporting(E_ALL);
ini_set('display_errors', 1);

//$oGitProject=new GitProject();
?>
<html>
	<head>
		<title>Graphical git viewer branch graph</title>
		<script type="text/javascript" src="javascript/jquery-2.0.3.min.js"></script>
		<script type="text/javascript" src="javascript/default.js"></script>
		<style>
			body{
				background-color:#FFFFFF;
			}

			#mainWrapper{
				margin:0 0 0 75px;
				position:relative;
				width:auto;
				height:auto;
			}

			.actionPoint{
				position:absolute;
				background-color:#D0D1D1;
				border:3px solid #ADADAE;
				display:block;
				width:<?php echo \git\GraphElement::pointSize;?>px;
				height:<?php echo \git\GraphElement::pointSize;?>px;
				border-radius:<?php echo \git\GraphElement::pointSize;?>px;
				text-align:center;
				line-height:<?php echo \git\GraphElement::pointSize;?>px;
				cursor:pointer;
			}
			.actionPoint > span{
				display:inline-block;
				vertical-align:middle;
			}

			.arrow_box {
				position: absolute;
				display:none;
				text-align:center;
				z-index:4;
			}
			.arrow_box > span:first-child {
				border-left: 10px solid transparent;
				border-bottom-width: 15px;
				border-bottom-style: solid;
				border-right: 10px solid transparent;
				background-color:transparent;
				height: 0;
				width: 0;
				z-index:1;
				display:inline-block;
				border-radius:0px;
			}
			.arrow_box > span:first-child + div {
				width:auto;
				padding:5px;
				border-style:solid;
				display:block;
				border-width:3px;
				text-align:left;
			}

			.arrow_box .titleActionType{
				display:block;
				text-align:center;
				padding:5px 0;
				font-size:14px;
				font-weight:bold;
				text-transform: uppercase;
			}

			.arrow_box > span:first-child +div > p{
				max-width:300px;
				margin:3px 0;
			}

			<?php
				$aArrowDirection=array(
					/*'left'=>array(
					),*/
					'right'=>array(
						'borderDirection'=>array(
							'bottom','left','top'
						),
						'borderOffset'=>array(
							array(
								'direction'=>'right',
								'value'=>-2
							)
						),
						'body'=>array(
							'width'=>'100%',
							'height'=>'4px',
							'top'=>'13px'
						)
					),
					'up'=>array(
						'borderDirection'=>array(
							'left','bottom','right'
						),
						'borderOffset'=>array(
							array(
								'direction'=>'top',
								'value'=>-14
							),
							array(
								'direction'=>'left',
								'value'=>-13
							)
						),
						'body'=>array(
							'height'=>'100%',
							'width'=>'4px'
						)
					),
					'down'=>array(
						'borderDirection'=>array(
							'left','top','right'
						),
						'borderOffset'=>array(
							array(
								'direction'=>'bottom',
								'value'=>-2,
							),
							array(
								'direction'=>'left',
								'value'=>-13
							)
						),
						'body'=>array(
							'height'=>'100%',
							'width'=>'4px'
						)
					)
				);

				foreach($aArrowDirection as $direction=>$aDataInfoDirection){
					// direction
					echo '.arrowFollow'. ucfirst($direction) .'{position:absolute;display:block}'."\n";

					//arrow end
					echo '.arrowFollow'. ucfirst($direction) .' .end{'.
							'border-'. $aDataInfoDirection['borderDirection'][0] .': 15px solid transparent;'.
							'border-'. $aDataInfoDirection['borderDirection'][1] .'-width: 20px;'.
							'border-'. $aDataInfoDirection['borderDirection'][1] .'-style: solid;'.
							'border-'. $aDataInfoDirection['borderDirection'][2] .': 15px solid transparent;'.
							'height: 0;'.
							'background-color:transparent;'.
							'width: 0;'.
							'position:absolute;';
					foreach($aDataInfoDirection['borderOffset'] as $aDataForArrowBorder){
						echo $aDataForArrowBorder['direction'].':'. $aDataForArrowBorder['value'] .'px;';
					}
					echo	'z-index:1;'.
							'display:inline-block;'.
							'border-radius:15px}'."\n";

					//arrow end white shadow
					echo '.arrowFollow'. ucfirst($direction) .' .endWhite{'.
							'border-'. $aDataInfoDirection['borderDirection'][0] .': 15px solid transparent;'.
							'border-'. $aDataInfoDirection['borderDirection'][1] .'-width: 20px;'.
							'border-'. $aDataInfoDirection['borderDirection'][1] .'-style: solid;'.
							'border-'. $aDataInfoDirection['borderDirection'][2] .': 15px solid transparent;'.
							'height: 0;'.
							'width: 0;'.
							'color: #FFFFFF;'.
							'position:absolute;';
					foreach($aDataInfoDirection['borderOffset'] as $inc=>$aDataForArrowBorder){
						echo $aDataForArrowBorder['direction'].':'. ($aDataForArrowBorder['value']+($inc==0 ? 7 : 0 )) .'px;';
					}
					echo	'z-index:2;'.
							'background-color:transparent;'.
							'display:inline-block;'.
							'border-radius:15px}'."\n";


					echo '.arrowFollow'. ucfirst($direction) .' .body{'.
							'display:inline-block;'.
							'position:absolute;'.
							'border-radius:4px;'.
							'z-index:3;';
					foreach($aDataInfoDirection['body'] as $attr=>$value){
						echo $attr.':'. $value .';';
					}
					echo '}'."\n";

					foreach(\git\Branch::getAllBranchColorInfo() as $aColorInfo){
						echo '.arrowFollow'. ucfirst($direction) .' .body.'. $aColorInfo['class'] .'{
							background-color:'. $aColorInfo['second'] .';'.
						'}'."\n";

						echo '.arrowFollow'. ucfirst($direction) .' .body.'. $aColorInfo['class'] .'{'.
							'border-left-color:'. $aColorInfo['second'] .';'.
						'}'."\n";

					}
				}

				foreach(\git\Branch::getAllBranchColorInfo() as $aColorInfo){
					echo '.'.$aColorInfo['class'].'{'.
						'background-color:'.$aColorInfo['first'].';'.
						'border-color:'.$aColorInfo['second'].';'.
						'color:'.$aColorInfo['third'].';'.
					'}'."\n";
				}
			?>
		</style>
	</head>
	<body style="margin:0px; padding:0px">
		<div><?php ?><br /></div>
		<div id="mainWrapper"><br /><br /><br />
			<?php $oGitProject=new \git\Interpretor('/home/neoxen/workspace/speexit'); ?>
		</div>
		<script type="text/javascript">
			//we need to get last action to retrieve width of wrapper
			var lastActionLeftOffset=<?php echo (\git\History::getLastActionNumber()*\git\GraphElement::pointMargin)+\git\GraphElement::pointSize; ?>;
			var heightDisplayOffset=<?php echo (\git\GraphElement::getMaxDisplayNumber()*\git\GraphElement::pointMargin)+\git\GraphElement::pointSize; ?>;
		</script>
	</body>
</html>
