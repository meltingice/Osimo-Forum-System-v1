$(document).ready(function(){
	$('#loading').ajaxStart(function(){
		$(this).fadeIn('fast');
	}).ajaxStop(function(){
		setTimeout(function(){
			$('#loading').fadeOut('slow');
		},500);
	});
	
	if(String(window.location).indexOf('messages.php')!=-1)
	{
		if(String(window.location).indexOf('sendto=')!=-1)
		{
			toggleMessageCompose();
		}
	}
});

function showNewThreadForm()
{
	$('#osimo_newthreadform').slideToggle('normal');
}

function showPostPreview()
{
	if($('#osimo_postbox').length>0&&$('#osimo_postbox').attr('value')!='')
	{
		$('#postpreview_title').fadeIn('normal');
	}
	if($('#osimo_messagepost').length>0&&$('#osimo_messagepost').attr('value')!='')
	{
		$('#postpreview_title').fadeIn('normal');
	}
}

function toggleMessageCompose()
{
	$('#new-message').slideToggle('normal');
}

function updateMessageType(type)
{
	if(type=="inbox")
	{
		$('#message-type').html('Inbox');
	}
	if(type=="sent")
	{
		$('#message-type').html('Sent Messages');
	}
}

function showAlert()
{
	$('#alertbox').fadeIn('slow');
}

function returnToTop()
{
	$('html, body').animate({scrollTop:0}, 'slow'); 
}

function moveThisThread(threadID)
{
	var destForum = $('#osimo_forum_selector').attr('value');
	
	moveThread(threadID,destForum);
}