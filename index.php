

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
		
		$hdr_s = ord($f_hdr[2]) + (ord($f_hdr[3]) << 8);
		if ($hdr_s != filesize($_FILES["f"]["tmp_name"]))
			echo "ERROR: bad bmp size";
			
		$hdr2 = fread($f, 40);
		if ($hdr2 == FALSE || strlen($hdr2) != 40)
			echo "ERROR: bad header";
			
		if (ord($hdr2[0]) != 40 || ord($hdr2[1]) != 0 || ord($hdr2[2]) != 0 || ord($hdr2[3]) != 0)
			echo "ERROR: bad header size";
			
		$bmp_width  = ord($hdr2[4]) + (ord($hdr2[5])<<8) + (ord($hdr2[6])<<16)  + (ord($hdr2[7])<<24);
		$bmp_height = ord($hdr2[8]) + (ord($hdr2[9])<<8) + (ord($hdr2[10])<<16) + (ord($hdr2[11])<<24);
		
		if (ord($hdr2[12]) != 1 || ord($hdr2[13]) != 0)
			echo "ERROR: bad color planes";
		
		$bmp_bpp = ord($hdr2[14]) + (ord($hdr2[15])<<8);
		if ($bmp_bpp != 24 && $bmp_bpp != 32)
			echo "ERROR: bad bpp";
			
		if (ord($hdr2[16]) != 0 || ord($hdr2[17]) != 0 || ord($hdr2[18]) != 0 || ord($hdr2[19]) != 0)
			echo "ERROR: compression not supported";
			
		if (fseek($f, 111, SEEK_SET) != 0)
			echo "ERROR: fseek failed";
			
		$row_bytes = (int)(($bmp_width * $bmp_bpp + 31)/32);
		
		echo "w: ".$bmp_width."<br>";
		echo "h: ".$bmp_height."<br>";
		echo "bpp: ".$bmp_bpp."<br>";
		echo "row: ".$row_bytes."<br>";
		
		
		
		for ($i = 0; $i < $bmp_height; $i++)
		{
			echo "<br>".$row_bytes;
		}
		
		
		echo "all good";
	}
}
else
	echo "no file";
?>
