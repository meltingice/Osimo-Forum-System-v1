<?php
/* Include the header */
include_once(THEMEPATH.'header.php');

/* Retrieve the Member List */
$args = ""; # "forum=$parentForum","page=1"
$start_id = max(0, ($_GET['page'] * 25) - 25);
$pages = $osimo->getPagination('table=users', 'num=10');
$memberlist = $osimo->getMemberList(1, 10);
?>
<div id="main-content">

	<div id="page_nav" style="margin-right: 20px;"><p>Page: 
	<?php
	$count = 0;
	for($i=1;$i<=$pages;$i++)
	{
		$count++;
		echo "<a href='#' onclick=\"getMemberList($i,10,'id')\">$count</a> ";
	}
	?>
	</p></div>

	<div id="osimo_memberlist">
		<?php
		$count=0;
		foreach($memberlist as $member)
		{
			include(THEMEPATH.'singlememberlist.php');
		}
		?>
	</div>

</div>

<?php
/* Include the Footer */
include_once(THEMEPATH.'footer.php');
?>