<?php
//---
for($i = 0; $i < count($_POST['del']); $i++ ) {
	$del	= $_POST['del'][$i];
	//---
	if ($del != '') {
		$qu = "DELETE FROM users WHERE user_id = '$del'";
		quary2($qu);
	};
};
//---
for($i = 0; $i < count($_POST['username']); $i++ ){
	//---
	$username 	= $_POST['username'][$i];
	$email 	= $_POST['email'][$i];
	$ido 	= $_POST['id'][$i];
	$wiki 	= $_POST['wiki'][$i];
	$project 	= $_POST['project'][$i];
	// $project 	= '';
	//---
	if ($username != '') {
		//---
		$qua = "INSERT INTO users (username, email, wiki, user_group) SELECT '$username', '$email', '$wiki', '$project'
		WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = '$username')";
		//---	
		if (isset($ido)) {
			$qua = "UPDATE `users` SET
			`username` = '$username',
			`email` = '$email',
			`user_group` = '$project',
			`wiki` = '$wiki'
			WHERE `users`.`user_id` = $ido;
			";
		};
		//---
		quary2($qua);
		//---
	};
};
//---
$new_q = "INSERT INTO users (username, email, wiki, user_group) SELECT DISTINCT user, '', '', '' from pages
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = user)";
//---
// quary2($new_q);
//---
$nn = 0;
foreach(quary2('SELECT count(DISTINCT user) as c from pages;') as $k => $tab) $nn = $tab['c'];
//---
echo "
<div class='card-header'>
<h4>Emails ($nn user):</h4>
</div>
<div class='card-body'>";
//---
?>
<form action="coordinator.php?ty=Emails" method="POST">
	<input name='ty' value="Emails" hidden/>
	  <div class="form-group">
		  <table id='em' class='table table-striped compact'>
			<thead>
			  <tr>
				<th>#</th>
				<th>Username</th>
				<th>Email</th>
				<th>Project</th>
				<th>Wiki</th>
				<th>Live</th>
				<th>Delete</th>
			  </tr>
			</thead>
			<tbody id="tab_ma">
<?PHP
//---
/*
CREATE TABLE users (
	user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL,
	wiki VARCHAR(255) NOT NULL
);
*/
// ALTER TABLE `users` ADD `depth` INT(2) NULL DEFAULT NULL AFTER `display`;
// ALTER TABLE `users` ADD `user_group` VARCHAR(120) NOT NULL AFTER `wiki`;
// ALTER TABLE users DROP depth;
//---
$projects = array();
//---
$projects[] = '';
//---
foreach ( quary2('select g_id, g_title from projects;') AS $Key => $table ) $projects[] = $table['g_title'];
//---
function make_project_to_user($project, $numb){
	global $projects;
	//---
    $str = "<select name='project[]$numb' id='project[]$numb' class='form-select'>";
    //---
    foreach ( $projects AS $n => $g ) {
		$cdcdc = $project == $g ? "selected" : "";
        $str .= "
            <option value='$g' $cdcdc>$g</option>";
    };
    //---
	$str .= "</select>";
    //---
	return $str;
};
//---
$qu2 = "select user_id, username, email, wiki, user_group, 
(select count(target) from pages WHERE target != '' and user = username) as live
from users, pages
where user = username
group by username  
ORDER BY live DESC;
";
//---
$qu1 = '
select user_id, username, email, wiki, user_group
from users
#ORDER BY email DESC
;';
//---
$users_done = array();
//---
foreach ( quary2($qu2) AS $Key => $gg ) if (!isset($users_done[$gg['username']])) $users_done[$gg['username']] = $gg;
//---
// foreach ( quary2($qu1) AS $d => $tat ) if (!in_array($tat['username'], $users_done)) $users_done[$tat['username']] = $tat;
foreach ( quary2($qu1) AS $d => $tat ) if (!isset($users_done[$tat['username']])) $users_done[$tat['username']] = $tat;
//---
$numb = 0;
//---
foreach ( $users_done as $k => $table) {
	//---
	$numb += 1;
	//---
	$live		= isset($table['live']) ? $table['live'] : 0;
	$id			= $table['user_id'];
	$username 	= $table['username'];
	$email 		= $table['email'];
	$wiki		= $table['wiki'];
	$project	= $table['user_group'];
	$project_line = make_project_to_user($project, $numb);
    //---
	echo "
	<tr>
		<td data-order='$numb'>$numb</td>
		<td data-order='$username'>
			<span><a href='users.php?user=$username'>$username</a></span>
			<input name='username[]$numb' id='username[]$numb' value='$username' hidden/>
			<input name='id[]$numb' id='id[]$numb' value='$id' hidden/>
		</td>
		<td data-order='$email'>
			<span style='display: none'>$email</span>
			<input size='25' name='email[]$numb' id='email[]$numb' value='$email'/>
		</td>
		<td data-order='$project'>
			$project_line
		</td>
		<td data-order='$wiki'>
			<input size='4' name='wiki[]$numb' id='wiki[]$numb' value='$wiki'/>
		</td>
		<td data-order='$live'>
			<span>$live</span>
		</td>
		<td><input type='checkbox' name='del[]$numb' value='$id'/> <label>delete</label></td>
	</tr>";
};
//---
//---
?>

			</tbody>
		</table>
		<button type="submit" class="btn btn-success">Submit</button>
		<span role='button' id="add_row" class="btn btn-info" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
	  </div>
</form>
<script type="text/javascript">
var i = 1;
function add_row() {
	var ii = $('#tab_ma >tr').length + 1;
	var e = "<tr>";
	e = e + "<td>" + ii + "</td>";
	e = e + "<td><input name='username[]" + ii + "'/></td>";
	e = e + "<td><input size='25' name='email[]" + ii + "'/></td>";
	e = e + "<td><select name='project[]" + ii + "' id='project[]" + ii + "' class='form-select'><option value=''></option></select></td>";
	e = e + "<td><input size='4' name='wiki[]" + ii + "'/></td>";
	e = e + "<td>0</td>";
	e = e + "<td></td>";
	e = e + "</tr>";

	$('#tab_ma').append(e);
	i++;
};

$(document).ready( function () {
	var t = $('#em').DataTable({
    // paging: false,
	lengthMenu: [[25, 50, 100], [25, 50, 100]],
    // scrollY: 800
	});
} );

</script>
  </div>