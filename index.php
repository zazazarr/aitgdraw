

<?php
function get_key_color ($txt)
{
	if (strlen($txt) != 6)
		return 0xFFFFFF;
		
	$s = 0;
	for ($i = 0; $i < 6; $i++)
	{
		if (ord($txt[i]) >= ord('a') && ord($txt[i] <= 'f'))
			$txt[i] = chr(ord($txt[i]) - 32);
			
		if (ord($txt[i]) < ord('0') || (ord($txt[i]) > '9' && ord($txt[i]) < 'A') || ord($txt[i]) > 'F')
			return 0xFFFFFF;
			
		$n = ord($txt[i]) - 48;
		if ($n > 9)
			$n = ord($txt[i])-65+10;
		
		$n <<= i*4;
		
		$s += $n;
	}
	
	return $s;
}

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
			
		$hdr2_size = ord($hdr2[0]) + (ord($hdr2[1])<<8) + (ord($hdr2[2])<<16)  + (ord($hdr2[3])<<24);
		if ($hdr2_size < 40)
			exit("ERROR: bad header size");
			
		$bmp_width  = ord($hdr2[4]) + (ord($hdr2[5])<<8) + (ord($hdr2[6])<<16)  + (ord($hdr2[7])<<24);
		$bmp_height = ord($hdr2[8]) + (ord($hdr2[9])<<8) + (ord($hdr2[10])<<16) + (ord($hdr2[11])<<24);
		
		if ($bmp_width < 40 || $bmp_height < 30)
			exit("ERROR: bad dimensions (must be at least 40x30");
		
		if (ord($hdr2[12]) != 1 || ord($hdr2[13]) != 0)
			exit("ERROR: bad color planes");
		
		$bmp_bpp = ord($hdr2[14]) + (ord($hdr2[15])<<8);
		if ($bmp_bpp != 24 && $bmp_bpp != 32)
			exit("ERROR: must be 24-bit or 32-bit");
			
		$bmp_comp = ord($hdr2[16]) + (ord($hdr2[17])<<8) + (ord($hdr2[18])<<16)  + (ord($hdr2[19])<<24);
		if ($bmp_comp != 0 && $bmp_comp != 3)
			exit("ERROR: compression not supported");
			
		if (fseek($f, $pixel_offset, SEEK_SET) != 0)
			exit("ERROR: fseek failed");
			
		$row_bytes = 4*(int)(($bmp_width * $bmp_bpp + 31)/32);
		
		/*
		echo "w: ".$bmp_width."<br>";
		echo "h: ".$bmp_height."<br>";
		echo "bpp: ".$bmp_bpp."<br>";
		echo "row: ".$row_bytes."<br>";
		echo "pix: ".$pixel_offset."<br>";
		*/
		
		$str_out = "";
		
		$use_key = FALSE;
		$key_color = 0xFFFFFF;
		if ($_POST["clr"])
		{
			if (strlen($_POST["clr"]) == 0)
				$use_key = FALSE;
			else
			{
				$use_key = TRUE;
				$key_color = get_key_color($_POST["clr"]);
			}
		}
		
		echo "key: ".$key_color."<br>";
		
		if ($bmp_bpp == 24)
		{
			for ($i = 0; $i < 30; $i++)
			{
				$row = fread($f, $row_bytes);
				if ($row == FALSE || strlen($row) != $row_bytes)
					exit("ERROR: bad data");
				
				$pos = 0;
				for ($p = 0; $p < 40; $p++)
				{
					$n = ord($row[$pos]) + (ord($row[$pos+1])<<8) + (ord($row[$pos+2])<<16);
					$pos += 3;
					
					if ($use_key == FALSE || $n != $key_color)
					{
						$clr = sprintf("%X", $n);
						
						if ($p < 30)
							$str_out .= (29-$i)*30+$p . "," . $clr . ",100,";
						else
							$str_out .= 900+(29-$i)*10+$p-30 . "," . $clr . ",100,";
					}
				}
			}
		}
		
		if ($bmp_bpp == 32)
		{
			for ($i = 0; $i < 30; $i++)
			{
				$row = fread($f, $row_bytes);
				if ($row == FALSE || strlen($row) != $row_bytes)
					exit("ERROR: bad data");
				
				$pos = 0;
				for ($p = 0; $p < 40; $p++)
				{
					$n = ord($row[$pos]) + (ord($row[$pos+1])<<8) + (ord($row[$pos+2])<<16);
					$a = ord($row[$pos+3]);
					$pos += 4;
					
					if ($a != 0)
					{
						$a_dec = (int)(($a*100)/255);
						$clr = sprintf("%X", $n);
						
						if ($p < 30)
							$str_out .= (29-$i)*30+$p . "," . $clr . ",".$a_dec.",";
						else
							$str_out .= 900+(29-$i)*10+$p-30 . "," . $clr . ",".$a_dec.",";
					}
				}
			}
		}
		
		$str_out .= "END";
		
		echo "<textarea rows=4 cols=20>".$str_out."</textarea>";
		
		// TODO select transparent color
		// TODO maybe resizing code?
		// TODO copy file. show preview
		// TODO closest color for basic board
		
		exit("<br> all good");
	}
}
else
{
?>
<head><title>bmp to drawing</title></head>
bmp must be exactly 40x30 and 24bpp (Paint works)<br>
White color means transparent<br>
maybe I'll add features soon<br><br>
<form enctype="multipart/form-data" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="50000">
<input type="file" name="f">
<input type="submit">
</form>
<br><br>
<b>DISCLAIMER</b>
<br>
whatever it is, I didn't do it. I swear on me mum

<?php
}
?>
