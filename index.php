

<?php
if ($_FILES["f"])
{
	if (is_uploaded_file($_FILES["f"]["tmp_name"]))
	{
		$f = fopen($_FILES["f"]["tmp_name"], "rb");
		$f_hdr = fread($f, 14);
		if ($f_hdr == FALSE || strlen($f_hdr) != 14)
			exit("ERROR: bad file");
		
		if ($f_hdr[0] != 'B' || $f_hdr[1] != 'M')
			exit("ERROR: not a bmp");
		
		$hdr_s = ord($f_hdr[2]) + (ord($f_hdr[3]) << 8);
		if ($hdr_s != filesize($_FILES["f"]["tmp_name"]))
			exit("ERROR: bad bmp size");
			
		$pixel_offset = ord($f_hdr[10]) + (ord($f_hdr[11])<<8) + (ord($f_hdr[12])<<16) + (ord($f_hdr[13])<<24);
			
		$hdr2 = fread($f, 40);
		if ($hdr2 == FALSE || strlen($hdr2) != 40)
			exit("ERROR: bad header");
			
		if (ord($hdr2[0]) != 40 || ord($hdr2[1]) != 0 || ord($hdr2[2]) != 0 || ord($hdr2[3]) != 0)
			exit("ERROR: bad header size");
			
		$bmp_width  = ord($hdr2[4]) + (ord($hdr2[5])<<8) + (ord($hdr2[6])<<16)  + (ord($hdr2[7])<<24);
		$bmp_height = ord($hdr2[8]) + (ord($hdr2[9])<<8) + (ord($hdr2[10])<<16) + (ord($hdr2[11])<<24);
		
		if ($bmp_width == 0 || $bmp_height == 0)
			exit("ERROR: bad dimensions");
		
		if (ord($hdr2[12]) != 1 || ord($hdr2[13]) != 0)
			exit("ERROR: bad color planes");
		
		$bmp_bpp = ord($hdr2[14]) + (ord($hdr2[15])<<8);
		if ($bmp_bpp != 24 && $bmp_bpp != 32)
			exit("ERROR: bad bpp");
			
		if (ord($hdr2[16]) != 0 || ord($hdr2[17]) != 0 || ord($hdr2[18]) != 0 || ord($hdr2[19]) != 0)
			exit("ERROR: compression not supported");
			
		if (fseek($f, $pixel_offset, SEEK_SET) != 0)
			exit("ERROR: fseek failed");
			
		$row_bytes = (int)(($bmp_width * $bmp_bpp + 31)/32);
		
		echo "w: ".$bmp_width."<br>";
		echo "h: ".$bmp_height."<br>";
		echo "bpp: ".$bmp_bpp."<br>";
		echo "row: ".$row_bytes."<br>";
		echo "pix: ".$pixel_offset."<br>";
		
		if ($bmp_bpp == 24)
		{
			for ($i = 0; $i < $bmp_height; $i++)
			{
				$row = fread($f, $row_bytes);
				if ($row == FALSE || strlen($row) != $row_bytes)
					exit("ERROR: bad data");
				
				$pos = 0;
				for ($p = 0; $p < $bmp_width; $p++)
				{
					$clr = bin2hex(substr($row, pos, 3);
					echo "<br>".$clr;
					$pos += 3;
				}
				
				echo "row ".$i." done<br>";
			}
		}
		
		// TODO 32 bpp
		
		exit("all good");
	}
}
else
{
?>

<form enctype="multipart/form-data" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="500000">
<input type="file" name="f">
<input type="submit">
</form>

<?php
}
?>
