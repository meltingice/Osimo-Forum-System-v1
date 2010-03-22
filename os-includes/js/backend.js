/*
*	Osimo - next-generation forum system
*	Licensed under GPLv3 (GPL-LICENSE.txt)
*
*	os-includes/js/backend.js - non-ajax functions for Osimo
*/

$(window).ready(function(){
	if($('.osimo_usernamesearch').length>0)
	{
		$('.osimo_usernamesearch').autocomplete('os-includes/ajax/usersearch.php',{
			selectFirst: false,
			minChars: 2
		});
		
		/* Check to see if we're viewing the inbox */
		if(String(window.location).indexOf('messages.php')!=-1)
		{
			if(String(window.location).indexOf('sendto=')!=-1)
			{
				/* We have a username entered in the URL, lets get it */
				var temp1 = String(window.location).split('sendto=');
				var temp2 = temp1[1].split('&'); //makes sure there aren't other GET variables in the way
				var getName = temp2[0];
				$('.osimo_usernamesearch').attr('value',getName);
			}
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
		var temp = curUrl.split('#');
		if(temp.length==2){ return temp[1]; }
		else
		{
			var splitUrl = temp[1].split('&');
			return splitUrl;
		}
	}
}

function forgotPassword()
{
	if($('#osimo_forgotpasswordbox').length==0)
	{
		$('body').prepend("<div id=\"osimo_forgotpasswordbox\"></div>");
	}
	
	$('#osimo_forgotpasswordbox').dialog({
		title: 'Forgot Password',
        modal: true,
	    resizable:false,
	    overlay: {
	    	opacity: 0.5,
	    	background: "black"
	    },
        height: '400px',
        width: '550px',
        close:function(){
        	$(this).dialog('destroy');
        },
        open:function(){
            resetPassword(0,0);
        }
	});
}

function BBHelp()
{
	if($('#osimo_bbhelpbox').length==0)
	{
		$('body').prepend("<div id=\"osimo_bbhelpbox\"></div>");
	}
	$("#osimo_bbhelpbox").dialog({
	    title: 'BBCode Help',
        resizable: true,
        height: 450,
        width: 600,
        close:function(){
        	$(this).dialog('destroy');
        },
        open:function(){
            $.ajax({
            	type:'POST',
            	url:'os-includes/ajax/content.php',
            	data:'content=bbhelp',
            	success:function(msg){
            		if(msg!='0')
            		{
            			$('#osimo_bbhelpbox').html(msg).show();
            		}
            		else
            		{
            			$('#osimo_bbhelpbox').dialog('destroy');
            		}
	        	}
	        });
        }
	});
}

function editPostBox(postID)
{
	if($("#osimo_editpostbox").length==0)
	{
		$('body').prepend("<div id=\"osimo_editpostbox\"></div>");
	}
	$("#osimo_editpostbox").dialog({
	    title: 'Edit Post',
        resizable: true,
        height: 345,
        width: 400,
        resize:function(){
        	var height = $('#osimo_editpostbox').height();
        	var width = $('#osimo_editpostbox').width();
        	$("#osimo_editpostcontent").css({'width':width-20+"px",'height':height-50+"px"});
        },
        close:function(){
        	$(this).dialog('destroy');
        },
        open:function(){
            $.ajax({
            	type:'POST',
            	url:'os-includes/ajax/content.php',
            	data:'content=editpostbox&postID='+postID,
            	success:function(msg){
            		if(msg!='0')
            		{
            			$('#osimo_editpostbox').html(msg).show();
            			var height = $('#osimo_editpostbox').height();
        				var width = $('#osimo_editpostbox').width();
	        			$("#osimo_editpostcontent").css({'width':width-20+"px",'height':height-50+"px"});
            		}
            		else
            		{
            			$('#osimo_editpostbox').dialog('destroy');
            		}
	        	}
	        });
        }
	});
}

function editProfile(userID)
{
	if($("#osimo_editprofilebox").length==0)
	{
		$('body').prepend("<div id=\"osimo_editprofilebox\"></div>");
	}
	$("#osimo_editprofilebox").dialog({
	    title: 'Edit Profile',
	    modal: true,
	    resizable:false,
	    overlay: {
	    	opacity: 0.5,
	    	background: "black"
	    },
        height: 500,
        width: 670,
        open:function(){
            $.ajax({
            	type:'POST',
            	url:'os-includes/ajax/content.php',
            	data:'content=editprofilebox&userID='+userID,
            	success:function(msg){
            		if(msg!='0')
            		{
            			$('#osimo_editprofilebox').html(msg).show();
            		}
            		else
            		{
            			$(this).dialog('destroy');
            		}
	        	}
	        });
        },
        close:function(){
        	$(this).dialog('destroy');
        }
	});
}

function showMemberList()
{
	getMemberList(-1,-1,-1,-1);
}

var curMemberPage = 1;
var curMemberNum = 15;
var curMemberSort = 'id';
var curMemberSortDir = 'ASC';
function getMemberList(page,num,sort,dir)
{
	var totPages = Number($('#osimo_memberlist-totpage').html());
	if(page==-1){ page = curMemberPage; }
	else if(page=='first'){ curMemberPage = 1; page = 1; }
	else if(page=='prev')
	{
		if(curMemberPage>1)
		{
			curMemberPage = curMemberPage-1; page = curMemberPage;
		}
		else
		{
			page = curMemberPage;
		}
	}
	else if(page=='next')
	{
		if(curMemberPage<totPages)
		{
			curMemberPage = curMemberPage+1; page = curMemberPage;
		}
		else
		{
			page = curMemberPage;
		}
	}
	else if(page=='last'){ curMemberPage = totPages; page = curMemberPage; }
	else{ curMemberPage = page; }
	
	if(num==-1){ num = curMemberNum; }
	else{ curMemberNum = num; }
	
	if(sort==-1){ sort = curMemberSort; }
	else{ curMemberSort = sort; }
	
	if(dir==-1){ dir = curMemberSortDir; }
	else if(dir==-2)
	{
		if(curMemberSortDir=='ASC'&&sort==curMemberSort){ curMemberSortDir = 'DESC'; }
		else if(curMemberSortDir=='DESC'&&sort==curMemberSort){ curMemberSortDir = 'ASC'; }
		
		dir = curMemberSortDir;
	}
	else{ curMemberSortDir = dir; }
	
	if($("#osimo_memberlistbox").length==0)
	{
		$('body').prepend("<div id=\"osimo_memberlistbox\" style='display:none'></div>");
	}
	if($('#osimo_memberlistbox:visible').length==0)
	{
		$("#osimo_memberlistbox").dialog({
			title: 'Memberlist',
			modal: true,
			overlay: {
				opacity: 0.5,
				background: "black"
			},
			resizable:false,
    		height: 480,
    		width: 700,
    		open:function(){
    		    $.ajax({
    		    	type:'POST',
    		    	url:'os-includes/ajax/user.php',
    		    	data:'memberlist=true&page='+page+'&num='+num+'&sort='+sort+'&sortDir='+dir,
    		    	success:function(msg){
    		    		if(msg!='0')
    		    		{
    		    			$('#osimo_memberlistbox').html(msg).show();
    		    			totPages = Number($('#osimo_memberlist-totpage').html());
    		    		}
    		    		else
    		    		{
    		    			$('#osimo_memberlistbox').dialog('destroy');
    		    		}
			    	}
			    });
    		},
    		close:function(){
    			$(this).dialog('destroy');
    		}
		});
		
	}
	else
	{
		$.ajax({
    		type:'POST',
    		url:'os-includes/ajax/user.php',
    		data:'memberlist=true&page='+page+'&num='+num+'&sort='+sort+'&sortDir='+dir,
    		success:function(msg){
    		    if(msg!='0')
    		    {
    		    	$('#osimo_memberlistbox').html(msg).show();
    		    	totPages = Number($('#osimo_memberlist-totpage').html());
    		    }
    		    else
    		    {
    		    	$('#osimo_memberlistbox').dialog('close');
    		    }
			}
		});
	}
}

function userCP()
{
	if($("#osimo_usercpbox").length==0)
	{
		$('body').prepend("<div id=\"osimo_usercpbox\"></div>");
	}
	$("#osimo_usercpbox").dialog({
	    title: 'User Control Panel',
	    modal: true,
	    resizable:false,
	    overlay: {
	    	opacity: 0.5,
	    	background: "black"
	    },
        height: 500,
        width: 700,
        open:function(){
            $.ajax({
            	type:'POST',
            	url:'os-includes/ajax/content.php',
            	data:'content=usercpbox',
            	success:function(msg){
            		if(msg=='mustlogin')
            		{
            			$('#osimo_usercpbox').dialog('destroy');
            			alert('You must be logged in to use this feature!');
            		}
            		else if(msg=='0')
            		{
            			$('#osimo_usercpbox').dialog('destroy');
            		}
            		else
            		{
            			$('#osimo_usercpbox').html(msg).show();
            		}
	        	}
	        });
        },
        close:function(){
        	$(this).dialog('destroy');
        }
	});
}

function showUserCPSection(which)
{
	if(which=='personal')
	{
		$('#osimo_usercp_personal').animate({
	    	"height": "toggle", "opacity": "toggle"
    	}, "slow");
	}
}

function changeActiveThreadPage(page)
{
	$('.osimo-active-thread-page').removeClass('osimo-active-thread-page');
	
	$('#osimo_pagenav-'+page).addClass('osimo-active-thread-page');
}

function changeActiveForumPage(page)
{
	$('.osimo-active-forum-page').removeClass('osimo-active-forum-page');
	
	$('#osimo_pagenav-'+page).addClass('osimo-active-forum-page');
}

function setOsimoSearchQuery()
{
	var type = $('#osimo_search_type').attr('value');
	if(type=='postsby')
	{
		if(!$('#osimo_search_query').hasClass('osimo_usernamesearch'))
		{
			$('#osimo_search_query').autocomplete('os-includes/ajax/usersearch.php',{
				selectFirst: false,
				minChars: 2
			}).addClass('osimo_usernamesearch');
		}
	}
	else
	{
		if($('#osimo_search_query').hasClass('osimo_usernamesearch'))
		{
			$('#osimo_search_query').removeClass('osimo_usernamesearch').unautocomplete();
		}
	}
}