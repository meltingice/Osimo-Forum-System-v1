$(window).ready(function(){
	if($('.osimo_usernamesearch').length>0)
	{
		$('.osimo_usernamesearch').autocomplete('../os-includes/ajax/usersearch.php',{
			selectFirst: false,
			minChars: 2
		});
	}
	if($('.osimo_modsearch').length>0)
	{
		$('.osimo_modsearch').autocomplete('includes/ajax/modsearch.php',{
			selectFirst: false,
			minChars: 2
		});
	}
	if($('.report-right-box').length>0)
	{
		if($.browser.safari)
		{
			$('.report-right-box').css({"margin-top":"0"});
		}
	}
});

function ajaxBookmark()
{
	var curUrl = new String(window.location);
	if(curUrl.indexOf('#')==-1)
	{
		return false;
	}
	else
	{
		var splitUrl = curUrl.split('#');
		return splitUrl[1];
	}
}

function showAjaxLoading()
{
	//$('#loading').slideDown('fast');
}

function hideAjaxLoading()
{
	//$('#loading').slideUp('fast');
}

function enableDraggable()
{
    $('.forum').draggable({
    	revert:'invalid'
    });
}

function initDraggables()
{
	enableDraggable();

	$('.drop-box').droppable({
	    accept:'.forum',
	    over:function(e,ui){
	    	$(this).css({'background-color':'#b4df96'});
	    },
	    out:function(e,ui){
	    	$(this).css({'background-color':'#f4f4f4'});
	    },
	    drop:function(e,ui){
	    
	    	var dropbox = $(this).attr('id').split("_");
	    	var categoryID = dropbox[1];
	    	var temp2 = $(ui.draggable).attr('id').split("_");
	    	var forumID = temp2[1];
	    	
	    	var content = $(ui.draggable).html();
	    	
			if($(".parent_"+forumID).length>0){ var refresh = true; }
			else{ var refresh = false; }
	    	
	    	$('#forumlist_'+categoryID).append("<li id=\"forum_"+forumID+"\" class=\"forum drop-box\">"+content+"</li>");
	    	
	    	$(ui.draggable).remove();
	    	
	    	$('.forum').draggable('destroy');
	    	$(this).css({'background-color':'#f4f4f4'});
	    	enableDraggable();
	    	updateForumStructure(forumID,categoryID,refresh);
	    }
	});
}

function userSearchOnTxt()
{
	if($('#user-search').attr('value')=='enter username here')
	{
		$('#user-search').attr('value','').css({'color':'#666666'});
		$('#user-search').focus();
	}
}

function userSearchOffTxt()
{
	if($('#user-search').attr('value')=='')
	{
		$('#user-search').attr('value','enter username here').css({'color':'#d8d8d8'});
	}
}

function alertMsg(type,text)
{
	$('#alert-msg-text').html(text);
	$('#alert-msg').removeClass('alert-success').removeClass('alert-fail');
	if(type=='success')
	{
		$('#alert-msg').addClass('alert-success').fadeIn('slow');
	}
	if(type=='fail')
	{
		$('#alert-msg').addClass('alert-fail').fadeIn('slow');
	}
	
	setTimeout(function(){
		$('#alert-msg').fadeOut('slow');
	},5000);
}

function statCheckDataType()
{
	var data = $('#stat-data-type').attr('value');
	if(data=='newuser')
	{
		$('#stat-forum-table').slideUp('slow');
	}
	else
	{
		$('#stat-forum-table').slideDown('slow');
	}
}