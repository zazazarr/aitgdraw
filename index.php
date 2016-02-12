

<form enctype="multipart/form-data" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="500000">
<input type="file" name="f">
<input type="submit">
</form>

<?php
if ($_FILES["f"])
{
	if (is_uploaded_file($_FILES["f"]["tmp_name"]))
	{
		$f = fopen($_FILES["f"]["tmp_name"], "rb");
		$f_hdr = fread($f, 14);
		if ($f_hdr == FALSE || strlen($f_hdr) != 14)
			echo "ERROR: bad file";
		
		if ($f_hdr[0] != 'B' || $f_hdr[1] != 'M')
			echo "ERROR: not a bmp";
		
		$hdr_s = $f_hdr[2] + $f_hdr[3]<<8;
		if ($hdr_s != filesize($_FILES["f"]["tmp_name"]))
			echo "ERROR: bad bitmap";
			echo $hdr_s;
			echo filesize($_FILES["f"]["tmp_name"]);
			echo "<br>";
			echo $f_hdr[2]."<br>".$f_hdr[3];
		echo "all good";
	}
}
else
	echo "no file";
?>
