<?php
include '../../config/config.php';
$q=mysqli_query($conn,"
SELECT users.username,chickens.tag_number,user_logs.behavior,user_logs.log_date
FROM user_logs
JOIN users ON users.user_id=user_logs.user_id
JOIN chickens ON chickens.chicken_id=user_logs.chicken_id
");
?>
<link rel="stylesheet" href="../../assets/css/style.css">
<h2>Behavior Logs</h2>
<table>
<tr>
<th>User</th>
<th>Chicken</th>
<th>Behavior</th>
<th>Date</th>
</tr>
<?php while($row=mysqli_fetch_assoc($q)){ ?>
<tr>
<td><?php echo $row['username']; ?></td>
<td><?php echo $row['tag_number']; ?></td>
<td><?php echo $row['behavior']; ?></td>
<td><?php echo $row['log_date']; ?></td>
</tr>
<?php } ?>
</table>