$(document).ready(function(){
	$('#mainWrapper').css('width',(lastActionLeftOffset+100)+'px');
	$('#mainWrapper').css('min-height',(heightDisplayOffset+200)+'px');
	$('.actionPoint').hover(function(){
		var objectInfoAction=$(this).next();
		objectInfoAction.show();

		var aInfosElement={
			leftPointValue:parseInt($(this).css('left').substr(0,$(this).css('left').length-2),10),
			widthInfoValue:parseInt(objectInfoAction.width(),10),
			widthPointValue:parseInt($(this).width(),10)
		};

		var leftPosition=aInfosElement.leftPointValue-(aInfosElement.widthInfoValue/2)+(aInfosElement.widthPointValue/2)+3;

		objectInfoAction.css({
			'left':leftPosition+'px'
		});

	},function(){
		$(this).next().hide();
	});
});