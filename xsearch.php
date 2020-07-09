<p><span>X Search</span> - <a href="xsearch.php">reset</a></p>

<?php $paths = array('/', '/app', '/app/code', '/app/design', '/skin', '/js', '/pub/static','/app/locale','/vendor'); ?>

<form method="post">
	<label for="keyword">Key world:</label>
	<input type="text" name="keyword" id="keyword" value="" />
	<select name="path">
		<?php 
		foreach ($paths as $path) {
			echo '<option value="' . $path . '">' . $path . '</option>';
		}
		?>
	</select>
	<input type="submit" value="search" >
</form>

<?php if (isset($_GET['sec_mod']) && $_GET['sec_mod'] == 1) : ?>
	<form action="" method="get">
		<label for="file">Cat file</label>
		<input type="text" name="file_path" value="" />
		<input type="hidden" name="sec_mod" value="1" />
		<input type="submit" value="View">
	</form>

	<?php
		if(isset($_GET['file_path']) && $_GET['file_path'] != "") :
		echo "<pre>";
		try {
			echo htmlentities( file_get_contents($_GET['file_path']) );
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		echo "</pre>";
		endif;
	?>

<?php endif; ?>

<?php
if($_POST){
	function rsearch($folder, $pattern) {
	    $dir = new RecursiveDirectoryIterator($folder);
	    $ite = new RecursiveIteratorIterator($dir);
	    $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
	    $fileList = array();
	    foreach($files as $file) {
	        $fileList[] = $file[0];
	    }
	    return $fileList;
	}

	function count_line_no($file, $str)
	{
	    $arr_lines = array();
	    $handle = fopen($file, "r");
	    if ($handle) {
	        $count = 0;
	        $arr = array();
	        while (($line = fgets($handle)) !== false) {
	            $count+=1;
	            if (strpos($line, $str) !== false) {
	                $arr_lines[$file][] = array('line' => htmlentities($line), "line_number"=>$count);
	            }
	        }
	    }

	    return $arr_lines;
	}

	function xsearch($keyword, $path, $show_content = false)
	{
		// $keyword = 'mw_onestepcheckout';
		// $files = rsearch('./mage/stagging/1939/', "/^.*\.(php)$/i");
		$files = rsearch($path, "/^.*\.(xml|phtml|php|js|csv|html)$/i");
		$arr = array();
		$count = 0;
		foreach ($files as $file) {
			
			$arr = count_line_no($file, $keyword);
			if(!empty($arr)){
				foreach ($arr as $key => $val) {
					echo $key  . "<br>";
					echo "---------------------------------------------------------------------------------------------------------------------------------------------------------- <br>";
					foreach ($val as $value) {
						echo "<pre>";
						echo 'line ' . $value['line_number'] . ' ';
						if($show_content){
							echo trim($value['line']);
						} 
						$count++;
						echo "</pre>";

					}
					echo "---------------------------------------------------------------------------------------------------------------------------------------------------------- <br>";
				}
			}
		}

		echo "<p><b>Found " . $count . " " . $keyword . "  in path " . $path ."</b></p>";
	}

	if($_POST['keyword']){
		$s_keyword = $_POST['keyword'];
	}

	if($_POST['path']){
		$s_path = $_POST['path'];
	}

	if($s_keyword != "" && $path != ""){
		xsearch($s_keyword, __DIR__.$s_path, true);
	}
}
?>
