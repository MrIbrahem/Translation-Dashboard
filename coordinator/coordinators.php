
<h4>Coordinators:</h4>
<form action="coordinator.php?ty=coordinators" method="POST">
	<input name='ty' value="coordinators" hidden/>
	  <div class="form-group">
		<table class='table' style="width:50%;">
			<tr>
				<th>id</th>
				<th>User</th>
				<th>Delete</th>
			</tr>
			<tbody id="user_tab">

<?php
//---
for($i = 0; $i < count($_POST['del']); $i++ ) {
	$del	= $_POST['del'][$i];
	//---
	if ($del != '') {
		$qua2 = "DELETE FROM coordinator WHERE id = '$del'";
		quary2($qua2);
	};
};
//---
for($i = 0; $i < count($_POST['user']); $i++ ) {
	$ido  	= $_POST['id'][$i];
	$user 	= $_POST['user'][$i];
	//---
	if ($user != '' && $ido == '') {
		$qua = "INSERT INTO coordinator (user) SELECT '$user' WHERE NOT EXISTS (SELECT 1 FROM coordinator WHERE user = '$user')";
		//---
		quary2($qua);
	};
};
//---
$qq = quary2('select id, user from coordinator;');
//---
$numb = 0;
//---
foreach ( $qq AS $Key => $table ) {
	$numb += 1;
	$ide	= $table['id'];
	$usere	= $table['user'];
    //---
	echo "
	<tr>
		<td>
		<span><b>$ide</b></span>
	  	<input name='id[]$numb' value='$ide' hidden/>
	  </td>
	  <td>
	  	<span><b>$usere</b></span>
	  	<input name='user[]$numb' value='$usere' hidden/>
		</td>
	  <td>
	  	<input type='checkbox' name='del[]$numb' value='$ide'/> <label> delete</label>
	  </td>
	</tr>";
};
//---
?>

</tbody>
</table>
  <button type="submit" class="btn btn-success">send</button>
</form>
<span role='button' id="add_row" class="btn btn-info" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
</div>
<script type="text/javascript">
var i = 1;
function add_row() {
	var ii = $('#user_tab >tr').length + 1;
	var e = "<tr><td><input name='user[]" + ii + "'/></td><td></td></tr>";
	$('#user_tab').append(e);
	i++;
};
</script>
</div>