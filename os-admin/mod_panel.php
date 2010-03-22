<?
$modpage=true;
include('header.php');
if(!isset($_SESSION['admin'])){ header('Location: index.php?login=needed&return='.rawurlencode($return)); exit; }
?>
<div id="navigation2">
	<ul>
		<li onclick="window.location='mod_panel.php?page=file';">File Report</li>
	    <li onclick="window.location='mod_panel.php?page=browse'">Browse Reports</li>
	</ul>
</div>

<div id="loading-wrap"><div id="loading" style="display:none"><img src="img/ajax-loader.gif" alt="Loading..." /></div></div>
<div id="content" class="clearfix">
    <div id="welcome-info">
    	<h3>Welcome back, <span style="color: #51a0d8"><?php echo $_SESSION['admin']['name']; ?></span></h3>
    </div>
    
    <? if(!isset($_GET['page']) || $_GET['page']=='browse'): ?>
    <div id="report-list" class="clearfix">
    	<div id="report-list-controls">
    	<p>Report List Controls <span class="report-right-box" style="float:right;margin-top:-16px;">Page <span id="report-cur-page">1</span> of <span id="report-total-page">1</span> (<span id="report-total-num">1</span> Reports)</span></p>
    		<div class="report-nav-level">
    			Report Type: 
    			<select id='report-type' onchange="updateReportType(true)">
    				<option value='0' selected>All</option>
    			    <option value='warning'>Warning</option>
    			    <option value='ban'>Ban</option>
    			    <option value='t_move'>Thread: Move</option>
    			    <option value='t_del'>Thread: Delete</option>
    			    <option value='t_sticky'>Thread: Sticky</option>
    			    <option value='t_lock'>Thread: Lock</option>
    			    <option value='general'>General/Other</option>
    			</select> &nbsp;&bull;&nbsp;
    			# Per Page:
    			<select id='report-num' onchange="updateReportNum(true)">
    				<option value='10'>10</option>
    				<option value='20' selected>20</option>
    				<option value='50'>50</option>
    				<option value='100'>100</option>
    			</select> &nbsp;&bull;&nbsp;
    			Sort by:
    			<select id='report-sort-item' onchange="updateReportSort(true)">
    				<option value='date_filed' selected>Date Filed</option>
    				<option value='filed_by_username'>Filed By</option>
    				<option value='filed_against_username'>Filed Against</option>
    				<option value='type'>Report Type</option>
    				<option value='title'>Title</option>
    			</select>
    			<select id='report-sort-order' onchange="updateReportSort(true)">
    				<option value='ASC'>Ascending</option>
    				<option value='DESC' selected>Descending</option>
    			</select>
    		</div>
    		<div class="report-nav-level" style="margin-top: 4px;">
    			Start Date: <input type="text" style="width:120px" value="" id="report-start-date" onchange="updateReportDate(true)" /> &nbsp;&bull;&nbsp;
    			End Date: <input type="text" style="width:120px" value="<?php echo date('F j,Y',time()); ?>" id="report-end-date" onchange="updateReportDate(true)" /> &nbsp;&bull;&nbsp;
    			Filed By: <input id="report-restrict-mod" type="text" style="width:100px" class="osimo_modsearch" onchange="updateReportFiledBy(true)" /> 
    			<input type="button" value="Go" onclick="updateReportFiledBy(true)" />&nbsp;&bull;&nbsp;
    			Filed Against: <input id="report-restrict-user" type="text" style="width:100px" class="osimo_usernamesearch" onchange="updateReportFiledAgainst(true)" />
    			<input type="button" value="Go" onclick="updateReportFiledAgainst(true)" />
    		</div>
    		<div class="report-nav-level" style="margin-top: 4px;text-align:center;margin-bottom:0px;">
    			Page Controls: 
    			<a href="javascript:reportsPageControl('first')"><img src="img/icons/resultset_first.png" alt="First" title="First" /></a>&nbsp;
    			<a href="javascript:reportsPageControl('previous')"><img src="img/icons/resultset_previous.png" alt="Previous" title="Previous" /></a>&nbsp;
    			<a href="javascript:reportsPageControl('next')"><img src="img/icons/resultset_next.png" alt="Next" title="Next" /></a>&nbsp;
    			<a href="javascript:reportsPageControl('last')"><img src="img/icons/resultset_last.png" alt="Last" title="Last" /></a>&nbsp;&bull;&nbsp;
    			Jump to Page: <input type="text" id="report-jump-to-page" style="width:30px;"/> <input type="button" onclick="reportJumpToPage()" value="Go" />&nbsp;&bull;&nbsp;
    			<a href="#" onclick="loadReportList()"><img src="img/icons/arrow_refresh.png" alt="Refresh" title="Refresh" />&nbsp;Refresh Reports</a>
    		</div>
    	</div>
    	<div id="mod-ajax-content">
    	
    	</div>
    </div>
    
    <div id="report-view-wrap" class="clearfix" style="display:none">
    	<div id="report-info">
    		<span style="display:none" id="report-id"></span>
    		<p id="report-info-title"><span id="report-info-title-content"></span> <span class="report-right-box" style="color: #51a0d8;float:right;margin-top:-16px;cursor:pointer;" onclick="loadReport(0)">Return to Report List</span></p>
    		<p id="report-info-content"><span id="report-info-type"></span> &bull; Filed <span id="report-info-date"></span><span id="report-info-info"></span> &bull; <a href="#" onclick="deleteReport()">Delete Report</a></p>
    	</div>
    	<div id="report-content">
    	
    	</div>
    </div>
    
    <? elseif($_GET['page']=='file'): ?>
    <style type="text/css">
		.ui-dialog-buttonpane,#template-form { font-size: 62.5%; }
		#template-form label,#template-form input { display:block; }
		#template-form input.text { margin-bottom:12px; width:95%; padding: .4em; }
		#template-form fieldset { padding:0; border:0; margin-top:25px; }
		#template-form h1 { font-size: 1.2em; margin: .6em 0; }
		
		.ui-button { outline: 0; margin:0; padding: .4em 1em .5em; text-decoration:none;  !important; cursor:pointer; position: relative; text-align: center; }
		.ui-dialog .ui-state-highlight, .ui-dialog .ui-state-error { padding: .3em;  }		
	</style>

    <div id="template-form" style="display:none">
    	<div id="template_tips" style="text-align:center"></div>
    	<form>
		<fieldset>
			<label for="name">Template Name</label>
			<input type="text" name="template_name" id="template_name" class="text ui-widget-content ui-corner-all" />
		</fieldset>
		</form>
	</div>
    <div id="file-report-wrap" class="clearfix">
    	<div id="file-report-choose-options">
    		<div id="file-report-stage1">
	    		<p>Report Type</p>
	    		<select id="report-choose-type" onchange="loadReportTemplates()">
    			    <option value='warning' <? if(isset($_GET['type'])&&$_GET['type']=='warning'){ echo "selected"; } ?>>Warning</option>
    			    <option value='ban' <? if(isset($_GET['type'])&&$_GET['type']=='ban'){ echo "selected"; } ?>>Ban</option>
    			    <option value='p_del' <? if(isset($_GET['type'])&&$_GET['type']=='p_del'){ echo "selected"; } ?>>Post: Delete</option>
    			    <option value='t_move' <? if(isset($_GET['type'])&&$_GET['type']=='t_move'){ echo "selected"; } ?>>Thread: Move</option>
    			    <option value='t_del' <? if(isset($_GET['type'])&&$_GET['type']=='t_del'){ echo "selected"; } ?>>Thread: Delete</option>
    			    <option value='t_sticky' <? if(isset($_GET['type'])&&$_GET['type']=='t_sticky'){ echo "selected"; } ?>>Thread: Sticky</option>
    			    <option value='t_lock' <? if(isset($_GET['type'])&&$_GET['type']=='t_lock'){ echo "selected"; } ?>>Thread: Lock</option>
    			    <option value='general' <? if(isset($_GET['type'])&&$_GET['type']=='general'){ echo "selected"; } ?>>General/Other</option>
	    		</select>
    		</div>
    		<div id="file-report-stage2">
    			<p>Report Templates</p>
    			<p>
    			<select id="report-templates" onchange="loadReportTemplate()">
    			
    			</select>
    			Templates: <span id="report-num-templates">0</span>
    			</p>
    		</div>
    	</div>
    	<div id="file-report-editor">
    		<div style="overflow:hidden">
    			<div style="float:left;width:320px;">
    				<p>Title <span style="font-size:11px">(optional)</span></p>
    				<input type="text" style="width:300px" id="file-report-title" />
    			</div>
    			<div style="float:left;width:220px;">
    				<p>File Against <span style="font-size:11px">(optional)</span></p>
    				<input type="text" style="width: 200px;" value="<? if($_GET['against']){ echo $_GET['against']; } ?>" id="file-report-against" class="osimo_usernamesearch" />
    			</div>
    			<div style="float:left;width:240px;">
    				<p>Concerning Post/Thread # <span style="font-size:11px">(optional)</span></p>
    				<input type="text" style="width: 130px;" value="<? if(is_numeric($_GET['concerning'])){ echo $_GET['concerning']; } ?>" id="file-report-concerning" />
    			</div>
			</div>
    		<p>Content</p>
    		<textarea id="file-report-content"></textarea>
    		<input class="report-submit" type="button" onclick="submitReport()" value="Submit" />
    		<div id="save-as-template">
    			<input type="checkbox" id="report-as-template" name="report-as-template" /> <label for="report-as-template" style="font-size:14px">Save as Template</label>
    		</div>
    	</div>
    </div>
    <? endif; ?>
</div>

<script>
	loadReportList();
	$("#report-start-date").datepicker({ 
    	dateFormat: "MM d, yy"
	});
	$("#report-end-date").datepicker({
		dateFormat: "MM d, yy"
	});
	<? if($_GET['page']=='file'): ?>
		<? if($_GET['type']): ?>
			loadReportTemplates('<?=$_GET['type']?>');
		<? else: ?>
			loadReportTemplates('warning');
		<? endif; ?>
	<? endif; ?>
</script>
<? include('footer.php'); ?>