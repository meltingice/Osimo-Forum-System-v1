/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/js/ajax.js - standardized ajax functions for Osimo
*/

function submitPost(threadID)
{
	var postContent = OsimoEditor.prototype.getContents("#osimo_postbox");
	
	if(postContent!='')
	{
		var postData = {"newpost":true,"threadID":threadID,"postContent":postContent};
		
		$.ajax({
			beforeSend:function(){
				if($('#osimo_postpreview').is(':visible'))
				{
					$('#osimo_postpreview').fadeOut('normal');
				}
			},
			type:'POST',
			url:'os-includes/ajax/post.php',
			data:postData,
			success:function(msg){
				if(msg=='0')
				{
					alert('Error!');
				}
				else if(msg=='wait')
				{
					alert('You must wait at least 10 seconds in between posting in order to prevent spam.');
				}
				else
				{
					$('#osimo_posts').html(msg);
					$('#osimo_postbox').attr('value','');
					
					updatePagination('thread',threadID,'last');
				}
			}
		});		
	}
}

function updatePagination(page,id,activePage)
{
	$('#osimo_pagination').load('os-includes/ajax/content.php',{updatePagination:true,page:page,id:id,activePage:activePage});
}

function refreshPosts(threadID)
{
	$('#osimo_posts').load('os-includes/ajax/post.php',{refresh:true,threadID:threadID,page:curPostPage});
}

function postPreview()
{
	var postContent;
	if($('#osimo_postbox').length>0)
	{
		postContent = OsimoEditor.prototype.getContents("#osimo_postbox");
		var url = "post.php";
	}
	if($('#osimo_messagepost').length>0)
	{
		
		postContent = OsimoEditor.prototype.getContents('#osimo_messagepost');
		var url = "messages.php";
	}
	
	if(postContent!='')
	{
		var postData = {"postpreview":true,"content":postContent};
		$.ajax({
			type:'POST',
			url:'os-includes/ajax/'+url,
			data:postData,
			success:function(msg){
				if($('#osimo_postpreview').is(':hidden'))
				{
					$('#osimo_postpreview').fadeIn('normal');
				}
				
				$('#osimo_postpreview').html(msg);
			}
		});		
	}
}

function editPost(postID,threadID)
{
	var postContent = $('#osimo_editpostcontent').attr('value');
	
	var postData = {"postedit":true,"postID":postID,"content":postContent};
	$.ajax({
		type:'POST',
		url:'os-includes/ajax/post.php',
		data:postData,
		success:function(msg){
			if(msg=='0')
			{
				alert('Error!');
			}
			else
			{
				$('#osimo_editpostbox').html('').dialog('close');
				refreshPosts(threadID);
			}
		}
	});
}

function reportPost(post_id){
	if(confirm("Report this post?")){
		var postData = {"reportPost":post_id};
		$.ajax({
			type:'POST',
			url:'os-includes/ajax/post.php',
			data:postData,
			dataType:'json',
			success:function(data){
				if(data.error){
					alert(data.error);
				}
				else{
					alert(data.content);
				}
			}
		});
	}
}

function loadPostPage(threadID,page)
{
	var curLoc = String(window.location);

	$.ajax({
		type:'POST',
		url:'os-includes/ajax/post.php',
		data:'loadpage=true&threadID='+threadID+'&page='+page,
		success:function(msg){
			if(msg=='0')
			{
				alert('Error!');
			}
			else
			{
				$('#osimo_posts').html(msg);
				curPostPage = page;
				
				window.location = "#page="+page;
				
				updatePagination('thread',threadID,curPostPage);
			}
		}
	});
}

function nextPostPage(threadID)
{
	if(curPostPage<totPostPages)
	{
		loadPostPage(threadID,curPostPage+1);
	}
}

function prevPostPage(threadID)
{
	if(curPostPage>1)
	{
		loadPostPage(threadID,curPostPage-1);
	}
}

function newThread(forumID)
{
	var threadTitle = $('#osimo_newthreadtitle').attr('value');
	var threadDescription = $('#osimo_newthreaddescription').attr('value');
	var postContent = OsimoEditor.prototype.getContents('#osimo_newthreadpost');
	if(postContent!=''&&threadTitle!='')
	{
		var postData = {"newthread":true,"forumID":forumID,"threadTitle":escape(threadTitle),"threadDescription":escape(threadDescription),"postContent":postContent};
		$.ajax({
			type:'POST',
			url:'os-includes/ajax/thread.php',
			data:postData,
			success:function(msg){
				if(msg=='0')
				{
					alert('Error!');
				}
				else if(msg=='wait')
				{
					alert('You must wait at least 10 seconds in between posting in order to prevent spam.');
				}
				else
				{
					$('#osimo_threads').html(msg);
				}
			}
		});
	}
}

function deleteThread(threadID)
{
	if(confirm("Are you sure you want to delete this thread?"))
	{
		$.ajax({
			type:'POST',
			url:'os-includes/ajax/thread.php',
			data:'deleteThread='+threadID,
			success:function(msg){
				if(msg=='0')
				{
					alert('Error!');
				}
				else
				{
					if(confirm("Would you like to file a report?")){
						window.location='os-admin/mod_panel.php?page=file&type=t_del&concerning='+threadID;
					}
					else{
						window.location = 'index.php?thread=deleted';
					}
				}
			}
		});		
	}
}

function deletePost(postID)
{
	if(confirm("Are you sure you want to delete this post?"))
	{
		$.ajax({
			type:'POST',
			url:'os-includes/ajax/post.php',
			data:'deletePost='+postID,
			success:function(msg){
				if(msg=='0')
				{
					alert('Error!');
				}
				else
				{
					if(confirm("Would you like to file a report?")){
						window.location='os-admin/mod_panel.php?page=file&type=p_del&concerning='+postID;
					}
					else{
						refreshPosts(msg);
					}
				}
			}
		});		
	}
}

function moveThread(threadID, destForum)
{
	$.ajax({
		type:'POST',
		url:'os-includes/ajax/thread.php',
		data:'moveThread='+threadID+'&destForum='+destForum,
		success:function(msg){
			if(msg=='1')
			{
				if(confirm("Would you like to file a report?")){
					window.location='os-admin/mod_panel.php?page=file&type=t_move&concerning='+threadID;
				}
				else{
					window.location.reload();
				}
			}
			else
			{	
				alert('Thread move failed...');
			}
		}
	});
}

function loadThreadPage(forumID,page,sticky)
{
	$.ajax({
		type:'POST',
		url:'os-includes/ajax/thread.php',
		data:'loadpage=true&forumID='+forumID+'&page='+page+'&sticky='+sticky,
		success:function(msg){
			if(msg=='0')
			{
				alert('Error!');
			}
			else
			{
				$('#osimo_threads').html(msg);
				curThreadPage = page;
				
				updatePagination('forum',forumID,curThreadPage);
			}
		}
	});
}

function newMessageThread()
{
	var recipient = $("#osimo_recipient").attr('value');
	var threadSubj = $('#osimo_messagesubj').attr('value');
	var postContent = OsimoEditor.prototype.getContents('#osimo_messagecontent');
	
	if(recipient!=''&&postContent!='')
	{
		if(threadSubj==''){ threadSubj = 'No Subject'; }
		var postData = {"newmessage":true,"recipient":recipient,"subj":threadSubj,"content":postContent};
		$.ajax({
			type:'POST',
			url:'os-includes/ajax/messages.php',
			data:postData,
			success:function(msg){
				if(msg=='0')
				{
					alert('Error!');
				}
				else
				{
					$('#osimo_messages').html(msg);
				}
			}
		});
	}
}

function deletePM(message_id){
	if(confirm("Are you sure you want to delete this private message?")){
		var postData = {"deletePM":true,"message_id":message_id};
		$.ajax({
			type:'POST',
			url:'os-includes/ajax/messages.php',
			data:postData,
			success:function(data){
				if(data == '0'){
					alert("Error deleting personal message thread");
				}
				else{
					$("#osimo_messages").html(data);
				}
			}
		});
	}
}

function deleteAllPMs(){
	if(confirm("Are you sure you want to delete *ALL* of your private messages?")){
		var postData = {"deleteAllPMs":true};
		$.ajax({
			type:'POST',
			url:'os-includes/ajax/messages.php',
			data:postData,
			success:function(data){
				if(data == '0'){
					alert("Error deleting personal message thread");
				}
				else{
					$("#osimo_messages").html(data);
				}
			}
		});
	}
}

function newMessagePost(threadID)
{
	var postContent = OsimoEditor.prototype.getContents('#osimo_messagepost');
	
	if(postContent!='')
	{
		var postData = {"newmessagepost":true,"thread":threadID,"content":postContent};
		$.ajax({
			beforeSend:function(){
				if($('#osimo_postpreview').is(':visible'))
				{
					$('#osimo_postpreview').fadeOut('normal');
				}
			},
			type:'POST',
			url:'os-includes/ajax/messages.php',
			data:postData,
			success:function(msg){
				if(msg=='0')
				{
					alert('Error!');
				}
				else
				{
					$('#osimo_messageposts').html(msg);
				}
			}
		});
	}
}

function showInboxMessages()
{
	$.ajax({
		type:'POST',
		url:'os-includes/ajax/messages.php',
		data:'refreshMessages=true&which=inbox',
		success:function(msg){
			if(msg=='0')
			{
				alert('Error !')
			}
			else
			{
				$('#osimo_messages').html(msg);
			}
		}
	});
}

function showSentMessages()
{
	$.ajax({
		type:'POST',
		url:'os-includes/ajax/messages.php',
		data:'refreshMessages=true&which=sent',
		success:function(msg){
			if(msg=='0')
			{
				alert('Error !')
			}
			else
			{
				$('#osimo_messages').html(msg);
			}
		}
	})
}

function stickyThread(id,sticky)
{
	$.ajax({
		type:'POST',
		url:'os-includes/ajax/thread.php',
		data:'threadID='+id+'&sticky='+sticky,
		success:function(msg){
			if(msg=='0')
			{
				alert('Error !');
			}
			else
			{
				if(confirm("Would you like to file a report?")){
					window.location='os-admin/mod_panel.php?page=file&type=t_sticky&concerning='+id;
				}
				else {
					window.location.reload();
				}
			}
		}
	});
}

function lockThread(id,lock)
{
	$.ajax({
		type:'POST',
		url:'os-includes/ajax/thread.php',
		data:'threadID='+id+'&lock='+lock,
		success:function(msg){
			if(msg=='0')
			{
				alert('Error !');
			}
			else
			{
				if(confirm("Would you like to file a report?")){
					window.location='os-admin/mod_panel.php?page=file&type=t_lock&concerning='+id;
				}
				else{
					window.location.reload();
				}
			}
		}
	});
}

function updateProfile(section)
{
	if(section=='info')
	{
		var data = $('#osimo_profile_info').serialize();
	}
	if(section=='contact')
	{
		var data = $('#osimo_profile_contact').serialize();
	}
	if(section=='bio')
	{
		var data = $('#osimo_profile_bio_container').serialize();
	}
	if(section=='sig')
	{
		var data = $('#osimo_profile_sig_container').serialize();
	}
	
	$.ajax({
		type:'POST',
		url:'os-includes/ajax/user.php',
		data:'section='+section+'&'+data,
		success:function(msg){
			if(msg=='0')
			{
				alert('Error!');
			}
			else
			{
				$('.ui-dialog-title').fadeOut('slow',function(){
    				$(this).html('Profile Updated!').fadeIn('slow');
				});

				var timer = setTimeout(function(){
    				$('.ui-dialog-title').fadeOut('slow',function(){
    					$(this).html('Edit Profile').fadeIn('slow');
					});
				},4000);
				
				if(section=='sig')
				{
					$('#osimo_profile_sig_preview').html(msg);
				}
			}
		}
	});
}

function resetPassword(step,type)
{
	var valid = true;
	var data = 'content=forgotpassword&step='+step+'&type='+type;
	if(step==1&&type==1)
	{
		var user = $('#osimo_forgotpass_username').attr('value');
		data += '&user='+user;
	}
	if(step==1&&type==2)
	{
		var email = $('#osimo_forgotpass_email').attr('value');
		data += '&email='+email;
	}
	if(step==2)
	{
		var code = $('#osimo_forgotpass_code').attr('value');
		var pass1 = $('#osimo_forgotpass_newpass1').attr('value');
		var pass2 = $('#osimo_forgotpass_newpass2').attr('value');
		if(pass1!=pass2){ valid = false; }
		data += '&code='+code+'&pass1='+pass1+'&pass2='+pass2;
	}
	
	if(valid)
	{
		$.ajax({
    		type:'POST',
        	url:'os-includes/ajax/content.php',
        	data:data,
        	success:function(msg){
        	    if(msg!='0')
        	    {
        	    	if(msg=='pass')
        	    	{
        	    		alert("The passwords entered do not match");
        	    	}
        	    	else if(msg=='code')
        	    	{
        	    		alert("The code entered is not correct, please try again");
        	    	}
        	    	else
        	    	{
        	    		$('#osimo_forgotpasswordbox').html(msg).show();
        	    		var height = $(this).height();
        	    		var width = $(this).width();
	    	    		$("#osimo_forgotpassword").css({'width':width+"px",'height':height+"px"});
        	    	}
        	    }
        	    else
        	    {
        	    	if(step=='1'&&type=='1')
        	    	{
        	    		alert('That username is not recognized, please try again.');
        	    	}
        	    	if(step=='1'&&type=='2')
        	    	{
        	    		alert('That email address is not recognized, please try again.');
        	    	}
        	    }
	    	}
		});		
	}
}

function updatePersonalInfo()
{
	var displayName = escape($('#osimo_usercp_displayname').attr('value'));
	var email = escape($('#osimo_usercp_emailaddr').attr('value'));
	var timeZone = escape($('#osimo_usercp_timezone').attr('value'));
	
	if(displayName=='')
	{
		alert("You must enter a display name");
	}
	if(email=='')
	{
		alert("You must enter a valid email address");
	}
	if(timeZone == '')
	{
		alert("You must pick a time zone");
	}
	
	var curPassword = escape($('#osimo_usercp_curpassword').attr('value'));
	var newPassword = escape($('#osimo_usercp_newpassword').attr('value'));
	var newPassword2 = escape($('#osimo_usercp_newpassword2').attr('value'));
	if(curPassword!=''&&newPassword!=''&&newPassword2!='')
	{
		var data = "updatepersonal=true&displayName="+displayName+"&email="+email+"&timeZone="+timeZone+"&curPassword="+curPassword+"&newPassword="+newPassword+"&newPassword2="+newPassword2;
	}
	else
	{
		var data = "updatepersonal=true&displayName="+displayName+"&email="+email+"&timeZone="+timeZone;
	}
	
	$.ajax({
		type:'POST',
		url:'os-includes/ajax/user.php',
		data:data,
		success:function(msg){
			if(msg=='passmismatch'){ alert('The passwords entered do not match'); }
			else if(msg=='passincorrect'){ alert('The current password entered is not correct.'); }
			else if(msg=='1'||msg=='11')
			{
				$('#osimo_usercp_curpassword').attr('value','');
				$('#osimo_usercp_newpassword').attr('value','');
				$('#osimo_usercp_newpassword2').attr('value','');
				
				$('.ui-dialog-title').fadeOut('slow',function(){
    				$(this).html('User Settings Updated!').fadeIn('slow');
				});

				var timer = setTimeout(function(){
    				$('.ui-dialog-title').fadeOut('slow',function(){
    					$(this).html('User Control Panel').fadeIn('slow');
					});
				},4000);
			}
			else{ alert('There was an error processing your request'); }
		}
	});
}

function warnUser(user,postID)
{
	/*
	$.ajax({
		type:'POST',
		url:'os-includes/ajax/user.php',
		data:'warnuser='+userID+'&warnpost='+postID,
		success:function(msg){
			if(msg=='1'){ alert("User warned successfully."); }
			else{ alert("Either you do not have admin/mod permissions or warn failed."); }
		}
	});*/
	document.location = 'os-admin/mod_panel.php?page=file&type=warning&against='+user+'&concerning='+postID;
}

function quotePost(postID)
{
	var postData = {"getQuote":postID};
	$.ajax({
		type:'POST',
		url:'os-includes/ajax/post.php',
		dataType:'json',
		data:postData,
		success:function(data){
			if(data.error){
				alert(data.error);
			}
			else{
				var initial = OsimoEditor.prototype.getContents('#osimo_postbox');
				var newContent = initial + "\n[quote="+data.username+"]"+data.content+"[/quote]";
				OsimoEditor.prototype.setContents("#osimo_postbox",newContent);
				var offset = $('#osimo_postbox').offset();
				$('html, body').animate({scrollTop:offset.top}, 'slow');
				$("#osimo_postbox").focus();
			}
		}
	});
}