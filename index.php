

<form method="post">
<input type="text" name="a1">
<input type="text" name="a2">
<input type="submit">
</form>

<?php
if ($_POST["a1"])
	echo "a";
	echo "".$_POST["a1"];
else
	echo "b";
?>
