$(document).ready(function(){
	$("input:checkbox").each( function() {
		(this.checked) ? $("#fake"+this.id).addClass('unchecked') : $("#fake"+this.id).removeClass('unchecked');
	});
	$(".checked").click(function(){
		($(this).hasClass('unchecked')) ? $(this).removeClass('unchecked') : $(this).addClass('unchecked');
		$(this.hash).trigger("click");
		return false;
	});
});
