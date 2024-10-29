<?php 
//get_header(); 
//echo 1; exit;
?>
<html>
<title></title>
<head>
</head>
<body>
<center>
	<!-- Start: Right Panel -->
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div style='text-align:center'>
		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
		<?php
			//<h1 class="top_heading"><span class="left"></span>
			//the_title(); 
			//<span class="right"></span></h1>
		?>
			<div class="entryContent" style='text-align:left;'>
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
			</div>
		</div>
	</div>
	<?php 
	//comments_template(); 
	?>

	<?php endwhile; endif; ?>
	<!-- End: Right Panel -->
	<!-- Start: Left Panel -->
	<?php 
		//get_sidebar(); 
	?>
	<!-- End: Left Panel -->
</center>
</body>
</html>
<?php 
	//get_footer(); 
?>
