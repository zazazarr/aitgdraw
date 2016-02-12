

<form enctype="multipart/form-data" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="500000">
<input type="file" name="f">
<input type="submit">
</form>

<?php
if ($_FILES["f"])
	echo $_FILES["f"];
else
	echo "not file";
?>
