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
				margin:0 0 0 10px;
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

			.arrowFollowRight{
				position:absolute;
				height:30px;
				display:block;
			}
			.arrowFollowRight .end{
				border-bottom: 15px solid transparent;
				border-left-width: 20px;
				border-left-style: solid;
				border-top: 15px solid transparent;
				height: 0;
				background-color:transparent;
				width: 0;
				position:absolute;
				right:-2px;
				z-index:1;
				display:inline-block;
				border-radius:15px;
			}
			.arrowFollowRight .endWhite{
				border-radius:15px;
				border-bottom: 15px solid transparent;
				border-left: 20px solid white;
				border-top: 15px solid transparent;
				height: 0;
				width: 0;
				position:absolute;
				right:5px;
				z-index:2;
				display:inline-block;
			}
			.arrowFollowRight .body{
				display:inline-block;
				width:100%;
				position:absolute;
				height:4px;
				/*background-color:#ADADAE;*/
				border-radius:5px;
				left:0px;
				top:13px;
				z-index:3;
				border-radius:4px;
			}
			.arrowFollowDown{
				position:absolute;
				display:block;

				transform-origin: 0px 0px;
			}
			.arrowFollowDown .end{
				border-radius:15px;
				border-left: 15px solid transparent;
				border-top: 20px solid #ADADAE;
				border-right: 15px solid transparent;
				height: 0;
				width: 0;
				position:absolute;
				bottom:-14px;
				left:-13px;
				z-index:1;
				display:inline-block;
			}
			.arrowFollowDown .endWhite{
				border-radius:15px;
				border-left: 15px solid transparent;
				border-top: 20px solid white;
				border-right: 15px solid transparent;
				height: 0;
				width: 0;
				position:absolute;
				bottom:-8px;
				left:-13px;
				z-index:2;
				display:inline-block;
			}
			.arrowFollowDown .body{
				display:inline-block;
				height:100%;
				position:absolute;
				width:4px;
				background-color:#ADADAE;
				border-radius:5px;
				top:13px;
				left:0px;
				z-index:3;
				border-radius:4px;
			}

			.arrowFollowUp {
				position:absolute;
				display:block;
				transform-origin: 46px 3px;
			}
			.arrowFollowUp .end{
				border-radius:15px;
				border-left: 15px solid transparent;
				border-bottom: 20px solid #ADADAE;
				border-right: 15px solid transparent;
				height: 0;
				width: 0;
				position:absolute;
				top:-14px;
				left:-13px;
				z-index:1;
				display:inline-block;
			}
			.arrowFollowUp .endWhite{
				border-radius:15px;
				border-left: 15px solid transparent;
				border-bottom: 20px solid white;
				border-right: 15px solid transparent;
				height: 0;
				width: 0;
				position:absolute;
				top:-8px;
				left:-13px;
				z-index:2;
				display:inline-block;
			}
			.arrowFollowUp .body{
				display:inline-block;
				height:100%;
				position:absolute;
				width:4px;
				background-color:#ADADAE;
				border-radius:5px;
				top:13px;
				left:0px;
				z-index:3;
				border-radius:4px;
			}

			/*.actionPoint:hover + .arrow_box{
				display:block;
			}*/


			.yellow{
				background-color:#fbed37;
				border-color:#edd355;
				color:#ad9a3c;
			}
			.arrowFollowRight .body.yellow{
				background-color:#edd355;

			}
			.arrowFollowDown .end.yellow,
			.arrowFollowRight .end.yellow{
				border-left-color:#edd355;

			}

			.blue{
				background-color:#78CFEB;
				border-color:#56AFE4;
				color:#4587ae;
			}
			.arrowFollowRight .body.blue{
				background-color:#56AFE4;
			}
			.arrowFollowRight .end.blue,
			.arrowFollowRight .end.blue{
				border-left-color:#56AFE4;
			}

			.green{
				background-color:#BCD54B;
				border-color:#9EC61C;
				color:#778c31;
			}
			.arrowFollowRight .body.green{
				background-color:#9EC61C;
			}
			.arrowFollowDown .end.green,
			.arrowFollowRight .end.green{
				border-left-color:#9EC61C;
			}

			.orange{
				background-color:#F9A55E;
				border-color:#F59044;
				color:#c86921;
			}
			.arrowFollowRight .body.orange{
				background-color:#F59044;
			}
			.arrowFollowRight .end.orange{
				border-left-color:#F59044;
			}
			.arrowFollowDown .end.orange{
				border-top-color:#F59044;
			}

			.purple{
				background-color:#C899C6;
				border-color:#AE74B2;
				color:#8e5192;
			}
			.arrowFollowRight .body.purple{
				background-color:#AE74B2;
			}
			.arrowFollowRight .end.purple{
				border-left-color:#AE74B2;
			}
			.arrowFollowDown .end.purple{
				border-top-color:#AE74B2;
			}

			.red{
				background-color:#F2695F;
				border-color:#E33B3C;
				color:#c31c1c;
			}
			.arrowFollowRight .body.red{
				background-color:#E33B3C;
			}
			.arrowFollowRight .end.red{
				border-left-color:#E33B3C;
			}
			.arrowFollowDown .end.red{
				border-top-color:#E33B3C;
			}

			.grey{
				background-color:#D0D1D1;
				border-color:#ADADAE;
				color:#808081;
			}
			.arrowFollowRight .body.grey{
				background-color:#ADADAE;
			}
			.arrowFollowRight .end.grey{
				border-left-color:#ADADAE;
			}
			.arrowFollowDown .end.grey{
				border-top-color:#ADADAE;
			}
		</style>
	</head>
	<body style="margin:0px; padding:0px">
		<div><img src="/images/big-logo-manorga.png" /><?php echo 'Nom du projet : Portail'?></div>
		<div id="mainWrapper">
			<?php $oGitProject=new \git\Interpretor('/home/neoxen/workspace/speexit');	?>
		</div>
	</body>
</html>
