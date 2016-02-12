

<form enctype="multipart/form-data" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="500000">
<input type="file" name="f">
<input type="submit">
</form>

<?php
if ($_FILES["f"])
{
	if (is_uploaded_file($_FILES["f"]["tmp_name"])
	{
		$f = fopen($_FILES["f"]["tmp_name"], "rb");
		$d = fread($f, 10);
		echo $d;
	}
}
else
	echo "no file";
?>
