
<div class='card-header'>
	<h4>Translation campaigns:</h4>
</div>
<div class='card-body'>
<form action="coordinator.php?ty=Campaigns" method="POST">
	<input name='ty' value="Campaigns" hidden/>
	  <div class="form-group">
  <table class='table'>
	  <tr>
		<th>#</th>
		<th>category</th>
		<th>display</th>
		<th>depth</th>
		<th>delete</th>
	  </tr>
	<tbody id="tab_logic">

<?php
//---
for($i = 0; $i < count($_POST['del']); $i++ ) {
	$del	= $_POST['del'][$i];
	//---
	if ($del != '') {
		$qua2 = "DELETE FROM categories WHERE id = '$del'";
		quary2($qua2);
	};
};
//---
// if (isset($_POST['cat'])) {
for($i = 0; $i < count($_POST['cat']); $i++ ){
	$cat = $_POST['cat'][$i];
	$dis = $_POST['dis'][$i];
	$ido = $_POST['id'][$i];
	$dep = $_POST['dep'][$i];
	//---
	$qua = "INSERT INTO categories (category, display, depth) SELECT '$cat', '$dis', '$dep'
	WHERE NOT EXISTS (SELECT 1 FROM categories WHERE category = '$cat')";
	//---	
	if (isset($ido)) {
		$qua = "UPDATE categories 
		SET 
		display = '$dis',
		category = '$cat',
		depth = '$dep'
		WHERE id = '$ido'
		";
	};
	quary2($qua);
};
// };
//---
$uuux = '';
//---
// ALTER TABLE `categories` ADD `depth` INT(2) NULL DEFAULT NULL AFTER `display`;
// ALTER TABLE categories DROP depth;
//---
$qq = quary2('select id, category, display, depth from categories;');
//---
$numb = 0;
//---
foreach ( $qq AS $Key => $table ) {
	$numb += 1;
	$id 		= $table['id'];
	$category 	= $table['category'];
	$display 	= $table['display'];
	$depth		= $table['depth'];
    //---
	echo "
	<tr>
	  <td>$numb</td>
	  <td>
	  	<input name='cat[]$numb' value='$category'/>
	  	<input name='id[]$numb' value='$id' hidden/>
	  </td>
	  <td><input name='dis[]$numb' value='$display'/></td>
	  <td><input class='w-25' type='number' name='dep[]$numb' value='$depth'/></td>
	  <td><input type='checkbox' name='del[]$numb' value='$id'/> <label>delete</label></td>
	</tr>";
};
//---
?>

</tbody>
</table>
  <button type="submit" class="btn btn-success">Submit</button>
</form>
<span role='button' id="add_row" class="btn btn-info" style="position: absolute; right: 130px;" onclick='add_row()'>New row</span>
</div>
<script type="text/javascript">
var i = 1;
function add_row() {
	var ii = $('#tab_logic >tr').length + 1;
	var e = "<tr>";
	e = e + "<td>" + ii + "</td>";
	e = e + "<td><input name='cat[]" + ii + "' placeholder='catname'/></td>";
	e = e + "<td><input name='dis[]" + ii + "' placeholder='display'/></td>";
	e = e + "<td><input name='dep[]" + ii + "' value='0'/></td>";
	e = e + "<td></td>";
	e = e + "</tr>";

	$('#tab_logic').append(e);
	i++;
};
</script>
</div>