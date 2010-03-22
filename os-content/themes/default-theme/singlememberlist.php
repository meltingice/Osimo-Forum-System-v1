<?php if($count==0): ?>
<table cellpadding='4' cellspacing='0' id="memberlist">
	<tr style="font-weight: bold;">
		<td onclick="getMemberList(1,10,'id')">ID</td>
		<td onclick="getMemberList(1,10,'username')">Username</td>
		<td>Status</td>
		<td onclick="getMemberList(1,10,'birthday')">Birthday</td>
		<td onclick="getMemberList(1,10,'rank_level')">Rank</td>
		<td onclick="getMemberList(1,10,'posts')">Posts</td>
	</tr>
<?php endif; ?>

<?php
# id, username, staff, birthday, rank, posts
	
echo "
<tr>
    <td>" . $member['id'] . "</td>
    <td>" . $member['username'] . "</td>
    <td>" . $member['status'] . "</td>
    <td>" . $member['birthday'] . "</td>
    <td>" . $member['rank'] . "</td>
    <td>" . $member['posts'] . "</td>
</tr>";

if($count==9){ echo "</table>"; }

$count++;
?>
