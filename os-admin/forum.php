<?php
include('header.php');
include('includes/forum.php');
include('includes/ajax/forum.php');

$categories = $admin->getCategoryList();
?>
<div id="loading-wrap"><div id="loading" style="display:none"><img src="img/ajax-loader.gif" alt="Loading..." /></div></div>
<div id="alert-msg" style="display:none"><p id="alert-msg-text"></p></div>
<h3 style="color: #444444;float: left;margin:15px 0 0px 0;width: 400px;font-family:Georgia,Verdana,Arial,sans-serif;">Forum Structure</h3>
<h3 style="color: #444444;float: right;margin: 15px 0 0px 0;width: 450px;font-family:Georgia,Verdana,Arial,sans-serif;">Controls</h3>
<div id="forums" style="width: 400px; float:left;">
<?php
/* Now lets output the data in a organized manner */
echo "<div id=\"sorted-forums\">\n";
	$forums = $admin->getForumList('all=true');
	if($forums==false)
	{
		echo "<h4>You have not created any forums yet!  You must create a category first.</h4>";
	}
	else
	{
		$data = parseForums($forums);
		outputForums($data,-1);
	}
echo "</div>\n";
?>
</div>
<div id="forum-controls-wrap">
	<div id="category-controls" class="user-content-box">
		<h4><img src="img/icons/folder_add.png" alt="Add Category" title="Add Category" /> Add Category</h4>
		<form id="add-category-form" class="forum-mod-form">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><p>Category Name:</p></td>
					<td><p><input type="text" id="add-category-name" /></p></td>
				</tr>
				<tr>
					<td><p>Category Parent:</p></td>
					<td>
						<select id="add-category-parent">
						<?php outputForumDropDown(); ?>
						</select>
					</td>
				</tr>
			</table>
			<p><input class="submit-button" type="button" value="Create" onclick="addCategory()" /></p>
		</form>
	</div>
	<div id="forum-controls" class="user-content-box">
		<h4><img src="img/icons/page_add.png" alt="Add Forum" title="Add Forum" /> Add Forum</h4>
		<form id="add-forum-form" class="forum-mod-form">
			<table>
				<tr>
					<td><p>Forum Name:</p></td>
					<td><p><input type="text" id="add-forum-name" /></p></td>
				</tr>
				<tr>
					<td><p>Category:</p></td>
					<td>
						<select id="add-forum-category">
							<?php outputCategoryDropDown(); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><p>Description</p></td>
					<td><p><input type="text" id="add-forum-description" /></p></td>
				</tr>
			</table>
			<p><input class="submit-button" type="button" value="Create" onclick="addForum()" /></p>
		</form>
	</div>
	<div id="forum-quick-info" class="user-content-box">
		<h4>Quick Info</h4>
	</div>
</div>

<div id="debuginfo">

</div>

<script>
	initDraggables();
</script>
<?php include('footer.php'); ?>