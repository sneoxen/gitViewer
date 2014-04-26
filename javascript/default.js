$(document).ready(function(){
	
	console.log($('#mainWrapper').width());
	
	$('#mainWrapper').css('width',$('#mainWrapper .actionPoint').last().css('left'));
	$('.actionPoint').hover(function(){
		var objectInfoAction=$(this).next();
		objectInfoAction.show();
		
		
		var aInfosElement={
			leftPointValue:parseInt($(this).css('left').substr(0,$(this).css('left').length-2),10),
			widthInfoValue:parseInt(objectInfoAction.width(),10),
			widthPointValue:parseInt($(this).width(),10)
		};
		console.log(aInfosElement);
		var leftPosition=aInfosElement.leftPointValue-(aInfosElement.widthInfoValue/2)+(aInfosElement.widthPointValue/2)+3;
		
		
		
		console.log(leftPosition);
		
		
		objectInfoAction.css({
			'left':leftPosition+'px'
		});
		
	},function(){
		$(this).next().hide();
	});
});
