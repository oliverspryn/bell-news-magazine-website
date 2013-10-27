$(document).ready(function(){
	$("input:checkbox").each( function() {
		(this.checked) ? $("#fake"+this.id).addClass('hidden') : $("#fake"+this.id).removeClass('hidden');
	});
	$(".visible").click(function(){
		($(this).hasClass('hidden')) ? $(this).removeClass('hidden') : $(this).addClass('hidden');
		$(this.hash).trigger("click");
		return false;
	});
});
