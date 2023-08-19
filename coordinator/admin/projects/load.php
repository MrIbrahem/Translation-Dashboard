<?php
//---
?>
<div class='card-header'>
	<h4>Projects:</h4>
</div>
<div class='card-body'>
<form action="coordinator.php?ty=projects" method="POST">
	<input name='ty' value="projects" hidden/>
	  <div class="form-group">
		<table class='table table-striped compact table-mobile-responsive table-mobile-sided' style="width:50%;">
			<thead>
				<tr>
					<th>Id</th>
					<th>Project</th>
					<th>Delete</th>
				</tr>
			</thead>
			<tbody id="g_tab">
<?php
//---
$numb = 0;
//---
foreach ( execute_query('select g_id, g_title from projects;') AS $g_title => $tab ) {
	$numb += 1;
    //---
	$g_id = $tab['g_id'];
	$g_title = $tab['g_title'];
    //---
	echo <<<HTML
	<tr>
		<td data-content='id'>
			<span><b>$numb</b></span>
			<input name='g_id[]$numb' value='$g_id' hidden/>
		</td>
	  	<td data-content='Project'>
	  		<input name='g_title[]$numb' value='$g_title'/>
		</td>
	  	<td data-content='Delete'>
	  		<input type='checkbox' name='del[]$numb' value='$g_id'/> <label> delete</label>
	  	</td>
	</tr>
	HTML;
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
	var ii = $('#g_tab >tr').length + 1;
	var e = "<tr>";
	e = e + "<td>" + ii + "<input name='g_id[]' value='0' hidden/></td>";
	e = e + "<td><input name='g_title[]" + ii + "'/></td>";
	e = e + "<td></td>";
	e = e + "</tr>";
	$('#g_tab').append(e);
	i++;
};
</script>
</div>