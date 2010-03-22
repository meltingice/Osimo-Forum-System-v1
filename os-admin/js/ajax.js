var todayStatTimeout;
$(document).ready(function(){
	$('#loading').ajaxStart(function(){
		$(this).slideDown('fast');
	}).ajaxStop(function(){
		$(this).slideUp('slow');
	});
});

function initTodayPoller()
{
	todayStatTimeout = setTimeout(function(){
		updateTodayStats();
		initTodayPoller();
	},10000);
}

function updateTodayStats()
{
	$('#today-stats').load('includes/ajax/statistics.php',{outputTodayStats:true});
}

/* Theme related functions */
function setActiveTheme(newTheme)
{
	$.ajax({
		type:'POST',
		beforeSend:showAjaxLoading(),
		url:'includes/ajax/theme.php',
		data:'setTheme='+newTheme,
		success:function(msg){
			loadPage('active');
		}
	});
}

function setActiveSmilies(smilies)
{
	$.ajax({
		type:'POST',
		beforeSend:showAjaxLoading(),
		url:'includes/ajax/theme.php',
		data:'setSmilies='+smilies,
		success:function(msg){
			loadPage('smilies');
		}
	});
}

function editActiveSmilies()
{
	var smileyNames = '';
	var smileyCode = '';
	$('.active-smiley').each(function(i){
		if(i!=0){ smileyNames += "&"; smileyCode += "&"; }
		smileyNames += "smiley[]="+$(this).attr('name');
		smileyCode += "smileyCode[]="+$(this).attr('value');
	});
	
	$.ajax({
		type:'POST',
		url:'includes/ajax/theme.php',
		data:'editActiveSmilies=true&'+smileyNames+'&'+smileyCode,
		success:function(msg){
			alertMsg('success',"Smilies successfully saved!");
		}
	});
	
}

function loadPage(page)
{
	showAjaxLoading();
	$('#page-content').load('includes/ajax/content.php',{loadPage:page},function(){ hideAjaxLoading(); });
}

function editTheme()
{
	var editTheme = $('#themepicker').attr('value');
	$.ajax({
		type:"POST",
		beforeSend:showAjaxLoading(),
		url:'includes/ajax/theme.php',
		data:"editTheme=true&theme="+editTheme,
		success:function(msg){
			$('#editor-right').html(msg);
			hideAjaxLoading();
		}
	});
}

var curTheme = '';
var curFile = '';
function editThemeFile(theme,file)
{
	$.ajax({
		type:'POST',
		beforeSend:showAjaxLoading(),
		url:'includes/ajax/theme.php',
		data:'editThemeFile=true&theme='+theme+'&file='+file,
		success:function(msg){
			curTheme = theme;
			curFile = file;
			
			$('#theme-editor').attr('value',msg);
			$('#theme-file-title').html("Now editing: "+theme+"/"+file);
			
			hideAjaxLoading();
		}
	});
}

function showEditorHighlighting(lang)
{
	if($('.dp-highlighter').length==0)
	{
		$('#theme-editor').addClass(lang);
		dp.SyntaxHighlighter.HighlightAll('theme-editor');
	}
}

function removeEditorHighlighting()
{
	if($('.dp-highlighter').length>0)
	{
		$('.dp-highlighter').remove();
		var content = $('#theme-editor').attr('value');
		$('#theme-editor').remove();
		$('#theme-file-title').after('<textarea id="theme-editor" wrap="off" name="theme-editor">'+content+'</textarea>');
	}
}

function updateThemeFile()
{
	var content = escape($('#theme-editor').attr('value'));
	$.ajax({
		type:'POST',
		beforeSend:showAjaxLoading(),
		url:'includes/ajax/theme.php',
		data:'updateThemeFile=true&theme='+curTheme+'&file='+curFile+'&content='+content,
		success:function(msg){
			if(msg=='1')
			{
				$('#theme-file-title').html(curTheme+"/"+curFile+" updated!");
			}
			else
			{
				$('#theme-file-title').html('Error!');
			}
			
			hideAjaxLoading();
		}
	});
}

/* User management */
var curPage = 1;
var totPage = 1;
var usersPerPage = 10;
var userSort = 'username';
var userSortOrder = 'ASC';

function userListPageControl(dir)
{
	showAjaxLoading();
	if(dir=='first')
	{
		$('#userlist').load('includes/ajax/user.php',{
		loadUserList:true,
		page:'1',
		numUsers:usersPerPage,
		sort:userSort,
		sortOrder:userSortOrder
		},function(){
			curPage = 1;
			$('#user-curpage').html(curPage);
			hideAjaxLoading();
		});
	}
	if(dir=='previous'&&curPage>1)
	{
		$('#userlist').load('includes/ajax/user.php',{
			loadUserList:true,
			page:(curPage-1),
			numUsers:usersPerPage,
			sort:userSort,
			sortOrder:userSortOrder
			},function(){
				curPage = curPage-1;
				$('#user-curpage').html(curPage);
				hideAjaxLoading();
			});
	}
	if(dir=='next'&&curPage!=totPage)
	{
		$('#userlist').load('includes/ajax/user.php',{
			loadUserList:true,
			page:(curPage+1),
			numUsers:usersPerPage,
			sort:userSort,
			sortOrder:userSortOrder
			},function(){
				curPage = curPage+1;
				$('#user-curpage').html(curPage);
				hideAjaxLoading();
			});
	}
	if(dir=='last')
	{
		$('#userlist').load('includes/ajax/user.php',{
			loadUserList:true,
			page:totPage,
			numUsers:usersPerPage,
			sort:userSort,
			sortOrder:userSortOrder
			},function(){
				curPage = totPage;
				$('#user-curpage').html(curPage);
				hideAjaxLoading();
			});
	}
	if(dir=='jump')
	{
		var page = Number($('#jump-to-page').attr('value'));
		
		if(page>=1&&page<=totPage)
		{
			$('#userlist').load('includes/ajax/user.php',{
				loadUserList:true,
				page:page,
				numUsers:usersPerPage,
				sort:userSort,
				sortOrder:userSortOrder
				},function(){
					curPage = page;
					$('#user-curpage').html(curPage);
					hideAjaxLoading();
				});
		}
	}
}

function userListNumPerPage()
{
	var num = Number($('#num-users-per-page').attr('value'));
	usersPerPage = num;
	
	curPage = 1;
	$('#user-curpage').html(curPage);
	refreshUserList();
	updateUserPagination();
}

function userListChangeSort()
{
	userSort = $('#sort-item').attr('value');
	userSortOrder = $('#sort-order').attr('value');
	
	refreshUserList();
}

function refreshUserList()
{
	showAjaxLoading();
	$('#userlist').load('includes/ajax/user.php',{
		loadUserList:true,
		page:curPage,
		numUsers:usersPerPage,
		sort:userSort,
		sortOrder:userSortOrder
	},function(){
		hideAjaxLoading();
	});
}

function updateUserPagination()
{
	showAjaxLoading();
	$('#user-totpage').load('includes/ajax/user.php',{updatePagination:true,num:usersPerPage},function(msg){
		totPage = Number(msg);
		updateUserPageJumpList();
	});
}

function updateUserPageJumpList()
{
	showAjaxLoading();
	$('#jump-to-page').load('includes/ajax/user.php',{updateUserPageJumpList:true,num:totPage},function(){ hideAjaxLoading(); });
}

function loadQuickUserInfo(id)
{
	showAjaxLoading();
	$('.user').removeClass('active-user');
	$('#user-quick-info').load('includes/ajax/user.php',{quickUserInfo:true,userID:id},function(){
		$('#user_'+id).addClass('active-user');
		hideAjaxLoading();
	});
}

var timeout;
var lastSearch;
function initUserSearch()
{
	$('#user-search').keydown(function(){
		clearTimeout(timeout);
		timeout = setTimeout(function(){
			var search = $('#user-search').attr('value');
			if(search!=lastSearch)
			{
				if(search=='')
				{
					$('#user-search-results').html("<h3 id=\"user-search-begin\">osimo user database search<br /><small>type in search box to begin</small></h3>");
				}
				else
				{
					$('#user-search-results').load('includes/ajax/usersearch.php',{query:search},function(){
						lastSearch = search;
					});
				}
			}
			
		},300);	
	});
}

/* Forum Management */
function refreshForumTree()
{
	showAjaxLoading();
	$('#sorted-forums').load('includes/ajax/forum.php',{refreshTree:true},function(){
		initDraggables();
		hideAjaxLoading();
	});
}

function refreshForumDropDown()
{
	showAjaxLoading();
	$('#add-category-parent').load('includes/ajax/forum.php',{refreshForumDropDown:true},function(){ hideAjaxLoading(); });
}

function refreshCategoryDropDown()
{
	showAjaxLoading();
	$('#add-forum-category').load('includes/ajax/forum.php',{refreshCategoryDropDown:true},function(){ hideAjaxLoading(); });
}

function updateForumStructure(forumID,category,refresh)
{
	$.ajax({
		type:'POST',
		beforeSend:showAjaxLoading(),
		url:'includes/ajax/forum.php',
		data:'updateForum=true&forumID='+forumID+'&category='+category,
		success:function(msg){
			if(refresh){ refreshForumTree(); }
		}
	});
}

function addCategory()
{
	var name = $('#add-category-name').attr('value');
	var parent = $('#add-category-parent').attr('value');
	
	if(name==''){ return false; }
	
	$.ajax({
		type:'POST',
		beforeSend:showAjaxLoading(),
		url:'includes/ajax/forum.php',
		data:'addCategory=true&name='+name+'&parent='+parent,
		success:function(msg){
			if(msg=='1')
			{
				alertMsg('success','Category \''+name+'\' created');
				refreshForumTree();
				refreshCategoryDropDown();
			}
		}
	});
}

function addForum()
{
	var name = $('#add-forum-name').attr('value');
	var parent = $('#add-forum-category').attr('value');
	var description = $('#add-forum-description').attr('value');
	
	if(name==''){ return false; }
	
	$.ajax({
		type:'POST',
		beforeSend:showAjaxLoading(),
		url:'includes/ajax/forum.php',
		data:'addForum=true&name='+name+'&parent='+parent+'&description='+description,
		success:function(msg){
			if(msg=='1')
			{
				alertMsg('success','Forum \''+name+'\' created');
				refreshForumTree();
				refreshForumDropDown();
			}
		}
	});
}

/* Statistics */
var endDate = $.datepicker.formatDate($.datepicker.TIMESTAMP,new Date());
endDate = endDate.substr(0,(endDate.length-3));
var startDate = endDate - 604800;
var graphID = 1;
var graphType = 'views';
function outputGraph(id,type)
{
	$.ajax({
		type:'POST',
		beforeSend:showAjaxLoading(),
		url:'includes/ajax/statistics.php',
		data:'outputGraph=true&id='+id+'&startDate='+startDate+'&endDate='+endDate+'&type='+type,
		success:function(msg){
			$('#stat-info-wrap').html(msg);
			graphID = id;
			graphType = type;
			hideAjaxLoading();
		}
	});
}

function setGraphDates()
{	
	var startTemp = $.datepicker.formatDate($.datepicker.TIMESTAMP,new Date($('#start-date').attr('value')));
	var endTemp = $.datepicker.formatDate($.datepicker.TIMESTAMP,new Date($('#end-date').attr('value')));
	
	startTemp = startTemp.substr(0,(startTemp.length-3));
	endTemp = endTemp.substr(0,(endTemp.length-3));
	
	if(startTemp<endTemp)
	{
		startDate = startTemp;
		endDate = endTemp;
		
		outputGraph(graphID,graphType);
	}
}

function setGraphData()
{
	graphType = $('#stat-data-type').attr('value');
	if(graphType=='newuser')
	{
		graphID = 0;
	}
	else
	{
		graphID = $('#stat-data-forum').attr('value');
	}
	
	outputGraph(graphID,graphType);
}

function updateGeneralOptions()
{
	/* Lets retrieve all the data */
	var siteTitle = $('#option-site-title').attr('value');
	var siteDesc = $('#option-site-desc').attr('value');
	var adminEmail = $('#option-admin-email').attr('value');
	var serverTimeZone = $('#option-admin-timezone').attr('value');
	var newUserEmail;
	$('#email-newuser').each(function(){
		newUserEmail = this.checked;
	});
	var registration = $('#option-registration').attr('value');
	var numThreads = $('#option-num-threads').attr('value');
	var numPosts = $('#option-num-posts').attr('value');
	
	var data = 'siteTitle='+escape(siteTitle)+'&siteDesc='+escape(siteDesc)+'&adminEmail='+escape(adminEmail)+'&serverTimeZone='+serverTimeZone+'&newUserEmail='+newUserEmail+'&registration='+registration+'&numThreads='+numThreads+'&numPosts='+numPosts;
	
	$.ajax({
		type:'POST',
		url:'includes/ajax/general.php',
		data:'updateOptions=true&'+data,
		success:function(msg){
			if(msg=='1')
			{
				alertMsg('success','General options updated!');
			}
			else
			{
				alertMsg('fail','Failed updating general options.');
			}
		}
	});
}

/* Set default data for mod report viewing */
var report_type = 0;
var report_page = 1;
var report_total_pages = 1;
var report_num = 20;
var report_sort_item = 'date_filed';
var report_sort_order = 'DESC';
var report_start_date = 0;
var report_end_date = 0;
var report_restrict_user = 0;
var report_restrict_mod = 0;
function updateReportType(reload)
{
	report_type = $('#report-type').attr('value');
	
	if(reload){ loadReportList(); }
}

function updateReportNum(reload)
{
	report_num = $('#report-num').attr('value');
	
	if(reload){ loadReportList(); }
}

function updateReportSort(reload)
{
	report_sort_item = $('#report-sort-item').attr('value');
	report_sort_order = $('#report-sort-order').attr('value');
	
	if(reload){ loadReportList(); }
}

function updateReportDate(reload)
{
	report_start_date = $('#report-start-date').attr('value');
	report_end_date = $('#report-end-date').attr('value');
	
	if(reload){ loadReportList(); }
}

function updateReportFiledBy(reload)
{
	report_restrict_mod = $('#report-restrict-mod').attr('value');
	
	if(reload){ loadReportList(); }
}

function updateReportFiledAgainst(reload)
{
	report_restrict_user = $('#report-restrict-user').attr('value');
	
	if(reload){ loadReportList(); }
}

function loadReportList()
{
	generateReportList(report_type,report_page,report_num,
						report_sort_item,report_sort_order,
						report_start_date,report_end_date,
						report_restrict_user,report_restrict_mod);
}

function reportsPageControl(dir)
{
	if(dir=='first'){
		if(report_page!=1){
			report_page = 1;
			loadReportList();
		}
	}
	if(dir=='previous'){
		if(report_page>1){
			report_page -= 1;
			loadReportList();
		}
	}
	if(dir=='next'){
		if(report_page<report_total_pages){
			report_page += 1;
			loadReportList();
		}
	}
	if(dir=='last'){
		if(report_page!=report_total_pages){
			report_page = report_total_pages;
			loadReportList();
		}
	}
}

function reportJumpToPage()
{
	var temp = Number($('#report-jump-to-page').attr('value'));
	if(temp>0 && temp<=report_total_pages && temp != report_page){
		report_page = temp;
		loadReportList();
	}
}

function generateReportList(type,page,num,sort_item,sort_order,start_date,end_date,restrict_user,restrict_mod)
{
	var ajaxdata = {"content":"reportlist","type":type,"page":page,"num":num,
				"sort_item":sort_item,"sort_order":sort_order,
				"start_date":start_date,"end_date":end_date,
				"restrict_user":restrict_user,"restrict_mod":restrict_mod};
	$.ajax({
		type:'POST',
		url:'includes/ajax/mod_content.php',
		data:ajaxdata,
		dataType:'json',
		success:function(data){
			$('#report-cur-page').html(page);
			report_total_pages = Number(data.totalPages);
			$('#report-total-page').html(report_total_pages);
			$('#report-total-num').html(data.totalReports);
			$('#mod-ajax-content').html(data.content);
		}
	});
}

function loadReport(id)
{
	/* Report list is showing, hide it */
	if($('#report-list:visible').length == 1){
		$('#report-list').hide("drop",{direction:'down'},500);
	}
	if(id==0){
		$('#report-view-wrap').hide("drop",{direction:'down'},500);
		loadReportList();
		setTimeout(function(){
			$('#report-list').show("drop",{direction:"up"},500);
		},500);
	}
	$.ajax({
		type:'POST',
		url:'includes/ajax/mod_content.php',
		data:'loadreport='+id,
		dataType:'json',
		success:function(data){
			if(!data.error){
				$('#report-info-title-content').html(data.title);
				$('#report-info-type').html(data.type);
				$('#report-content').html(data.report);
				$('#report-info-date').html(data.date_filed);
				$('#report-info-info').html(data.filed_info);
				$('#report-id').html(data.id);
				setTimeout(function(){
					$('#report-view-wrap').show("drop",{direction:"up"},500);
				},200);
			}
		}
	});
}

function loadReportTemplates()
{
	var type = $('#report-choose-type').attr('value');
	
	$.ajax({
		type:'POST',
		url:'includes/ajax/mod_reports.php',
		data:'choosereporttype='+type,
		dataType: 'json',
		success:function(data){
			$('#report-templates').html(data.content);
			$('#report-num-templates').html(data.count);
		}
	});
}

function loadReportTemplate()
{
	var template = $('#report-templates').attr('value');
	
	$.ajax({
		type:'POST',
		url:'includes/ajax/mod_reports.php',
		data:'load_template='+template,
		dataType:'json',
		success:function(data){
			$('#file-report-title').attr('value',data.title);
			tinyMCE.activeEditor.setContent(data.report,{format : 'raw'});
		}
	});
}

function submitReport()
{
	var title = $('#file-report-title').attr('value');
	var report_type = $('#report-choose-type').attr('value');
	var file_against = $('#file-report-against').attr('value');
	var concerning_id = $('#file-report-concerning').attr('value');
	var content = tinyMCE.get('file-report-content').getContent();
	var isTemplate = $('#report-as-template:checked').length;

	if(isTemplate){
		$("#template-form").dialog({
			title:'Save Template',
			bgiframe: true,
			autoOpen: true,
			height: 190,
			width:400,
			modal: true,
			resizable:false,
			buttons: {
				'Create Template & Save': function() {
					var template_title = $('#template_name').attr('value');
					var template_data = {"save_template":true,"template_report":content,"template_title":template_title,"template_type":report_type};
					$.ajax({
						type:'POST',
						url:'includes/ajax/mod_reports.php',
						data:template_data,
						dataType:'json',
						success:function(data){
							if(data.response == '1'){
								sendReport(report_type,title,content,file_against,concerning_id);
							}
							else{
								$('#template_name').addClass('ui-state-error');
								$('#template_tips').text(data.response).effect("highlight",{},1500);
							}
						}
					});
				},
				'Create Template Only':function(){
					var template_title = $('#template_name').attr('value');
					var template_data = {"save_template":true,"template_report":content,"template_title":template_title,"template_type":report_type};
					$.ajax({
						type:'POST',
						url:'includes/ajax/mod_reports.php',
						data:template_data,
						dataType:'json',
						success:function(data){
							if(data.response == '1'){
								$('#template_tips').text("Template created!");
							}
							else{
								$('#template_name').addClass('ui-state-error');
								$('#template_tips').text(data.response).effect("highlight",{},1500);
							}
						}
					});
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			open:function(){
				$('#template_name').attr('value',title);
			},
			close: function() {
				$(this).dialog('destroy');
			}
		});

	}
	else
	{
		sendReport(report_type,title,content,file_against,concerning_id);
	}
}

function sendReport(type,title,content,file_against,concerning_id)
{
	var valid = true;
	if(type=='warning'||type=='ban'){
		if(file_against==''){
			alert("You must specify who this report is about for warnings and bans.");
			valid = false;
		}
	}
	else if(type=='p_del'||type=='t_move'||type=='t_del'||type=='t_sticky'||type=='t_lock'){
		if(concerning_id==0 || concerning_id==''){
			alert("You must specify a post or thread ID when writing a report about a post/thread action.");
			valid = false;
		}
	}
	
	if(valid)
	{
		var report_data = {"send_report":true,"report_type":type,"report_title":title,"report_content":content,"report_file_against":file_against,"report_concerning_id":concerning_id};
		$.ajax({
			type:'POST',
			url:'includes/ajax/mod_reports.php',
			data:report_data,
			dataType:'json',
			success:function(data){
				if(data.response = "1"){
					document.location = 'mod_panel.php';
				}
			}
		});
	}
}

function deleteReport()
{
	var id = Number($('#report-id').html());
	
	$.ajax({
		type:'POST',
		url:'includes/ajax/mod_reports.php',
		data:'deleteReport='+id,
		success:function(data){
			if(data=='1'){
				loadReport(0);
			}
			else{
				alert("Error!");
			}
		}
	});
}