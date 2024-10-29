<?php

	/***************************************************************************************/

	

	/*

	* Plugin Name:Auto Poster

	*

	* Description:This plugin will automatically read the input .zip file and read the files inside it and post them to your wordpress site .

	*

	* Author:Sukhchain Singh

 	*

	*

	*Version: 1.2
	*

	*

	*

	*/

	

	/**********************************************************************************************/

	add_action('admin_menu', 'my_plugin_menu');

	register_activation_hook(__FILE__,'act_fun');

	register_deactivation_hook( __FILE__, 'deact_all' );

	function act_fun()

	{

		add_option('FilesArr','');

		add_option('up_dir','uploads_auto_poster');

		add_option('tmp_dir','auto_poster_files_tmp');

		add_option('main_dir','auto_poster_files');

		add_option('error_zip','');

		add_option('filenames','');

		add_option('names','');

		add_option('DestDir','');

		add_option('PostsTitles','');

		add_option('PostsDesc','');

	

	}

	function my_plugin_menu() 

	{

		add_options_page('Auto Poster', 'Auto Poster', 'manage_options', 'auto_p', 'fun_auto');

	}

	function fun_auto()

	{?>
		<script type="text/javascript" src="<?php echo plugins_url()?>/auto-poster/ckeditor/ckeditor.js"></script>
		<script src="<?php echo plugins_url()?>/auto-poster/_samples/sample.js" type="text/javascript"></script>
		<link href="<?php echo plugins_url()?>/auto-poster/_samples/sample.css" rel="stylesheet" type="text/css" />
	<?php
	
		echo '<h2>Auto Poster</h2>';
	//	echo 'For Help <a href="'.plugins_url().'/autp_poster/readme.txt">Click Here</a>';
		echo '<div id="helpDiv" style="display:none">';
				include_once ('help.html');
		echo '</div>';
		if((!$_GET['step']==2) && (!$_GET['step']==3) && (!$_GET['step']==4) || ($_GET['step']==1))

		{

			echo '<h3>Step One:-</3>Upload .zip file';

			fun_step_one();

		}

		elseif($_GET['step']==2)

		{

			echo '<h3>Step Two:-</3>File found';

			fun_step_two();

		}

		elseif($_GET['step']==3)

		{		

			echo '<h3>Step Three:-</3>Add Posts Title and Descirptions';

			fun_step_three();

		}	

		elseif($_GET['step']==4)

		{		

			echo '<h3>Step Fourth:-</3>Publish the posts';

			fun_step_fourth();

		}	

	}

	function fun_step_one()

	{

	

		echo '<form method="post" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?page=auto_p&step=1">';

			echo '<ul>';

				echo '<li><label>Select Zip File</label></li>';

				echo '<input type="file" name="posts_dir" />';

				echo '<input type="submit" value="Upload Files" />';

			echo '</ul>';

		echo '</form>';

		//$r=explode('/',get_option('siteurl'));

		

		$DirPath=ABSPATH.'/wp-content/'.get_option('up_dir');

		if(!file_exists($DirPath))

		{

			mkdir($DirPath,0755);

		}

		$Dir1=$DirPath.'/'.get_option('main_dir');

		update_option('DestDir',$Dir1);

		if(!file_exists($Dir1))

		{

			@mkdir($Dir1,0755);

		}

		$TmpDir=$DirPath.'/'.get_option('tmp_dir');

		if(!file_exists($TmpDir))

		{

			@mkdir($TmpDir,0755);

		}

		update_option('FilesArr',$TmpDir);

		if((isset($_FILES)) && (!empty($_FILES['posts_dir']['tmp_name'])))

		{

			$ErrorInZip=array();

			$zip = new ZipArchive() ;

			$res = $zip->open($_FILES['posts_dir']['tmp_name']);

			if ($res === TRUE) 

			{

				$zip->extractTo($TmpDir);

				$zip->close();

				

					echo '<form method="post"><ul><li><input type="submit" value="Next Step" name="submit_1"/> </li></ul></form>';

			}

			else 

			{

				update_option('error_zip','Error While Unzip !');

			}

		}	

		if(isset($_POST['submit_1']))

		{

			echo '<script type="text/javascript">window.location="options-general.php?page=auto_p&step=2"</script>';

		}

	}



	function fun_step_two()

	{

		$DirPath=get_option('FilesArr');

		$Filename=array();

		$Fileext=array();

		$FileLink=array();

		$Name=array();

		$doc=0;

		$xlsx=0;

		$pptx=0;

		$txt=0;
		
		$Otrs=0;
		/* This is the correct way to loop over the directory. */

		if ($handle = opendir($DirPath)) 

		{

			$Filename=array();

			$Fileext=array();

			$FileLink=array();

			$Name=array();

			$doc=0;

			$xlsx=0;

			$pptx=0;

			$txt=0;
			
			$Otrs=0;
			/* This is the correct way to loop over the directory. */

			while (false !== ($file = readdir($handle)))

			{

				if(!empty($file) && ($file!='.') && ($file!='..'))

				{

					$Files[]=$file;

					$ext=pathinfo($file);

					$f=$ext['filename'].'.'.$ext['extension'];

					$Filename[]=$f;

					$Name[]=$ext['filename'];

					$Fileext[]=$ext['extension'];

					if(($ext['extension']=='doc') || ($ext['extension']=='docx'))

						$doc=$doc + 1;

					if(($ext['extension']=='xls') || ($ext['extension']=='xlsx'))

						$xlsx=$xlsx + 1;

					if(($ext['extension']=='ppt') || ($ext['extension']=='pptx'))

						$pptx=$pptx + 1;

					if(($ext['extension']=='txt'))

						$txt=$txt + 1;

					if(($ext['extension']!='txt') && ($ext['extension']!='ppt') && ($ext['extension']!='pptx') && ($ext['extension']!='xls') && ($ext['extension']!='xlsx') && ($ext['extension']!='doc') && ($ext['extension']!='docx'))

						$Otrs=$Otrs + 1;


			

				}

			}

			closedir($handle);

		}
		update_option('fileseses',$Files);
		update_option('filenames',$Filename);

		update_option('names',$Name);

		echo '<ul>';

			echo '<li>&nbsp;</li>';

			echo '<li>'.$doc.' Word Document files found</li>';

			echo '<li>'.$xlsx.' Excel files found</li>';

			echo '<li>'.$pptx.' Powerpoint files found<li>';

			echo '<li>'.$txt.' Text files found</li>';
			
			echo '<li>'.$Otrs.' Other files found</li>';

		echo '</ul>';

		echo '<form method="post"><ul><li><input type="submit" value="Next Step" name="submit_2"/> </li></ul></form>';

		if(isset($_POST['submit_2']))

		{

			echo '<script type="text/javascript">window.location="options-general.php?page=auto_p&step=3"</script>';

		}	

	}

	function fun_step_three()

	{

			echo '<form method="post">

				<ul>

					<li>

						<label>Posts Title</label>

						<input type="text" name="auto_post_title" value="This is the post ttitle %posttitle%" size="80"/>

					</li>

					<li>

						<label>Description</label>

						<textarea cols="62" rows="10" name="auto_poster_description" class="ckeditor">This will be the post description %link%</textarea>

					</li>

					<li>

						<input type="submit" value="Publish Posts" name="submit_3"/>

					</li>

				</ul>	

			</form>';

		if((isset($_POST)) && isset($_POST['submit_3']))

		{		

			update_option('PostsTitles',$_POST['auto_post_title']);

			update_option('PostsDesc',$_POST['auto_poster_description']);

			echo '<script type="text/javascript">window.location="options-general.php?page=auto_p&step=4"</script>';

		}	

	}

	function fun_step_fourth()

	{

		echo '<br />';

		echo '<h3>Post Preview</h3>';

		echo get_option('PostsTitles');

		echo '<br />';

		echo get_option('PostsDesc');

		echo '<br />';

		

		echo '<form method="post">';

			echo '<ul>';

				echo '<li>';

					echo '<input type="submit" value="Publish All Files as a Post" name="submit_4"/>';

				echo '</li>';

			echo '</ul>';

		echo'</form>';

		if(isset($_POST['submit_4']))

		{

			$Filename=get_option('filenames');

			$Name=get_option('names');

			$UpDir=get_option('up_dir');

			$MainDir=get_option('main_dir');

			$Src=get_option('FilesArr');

			$Dest=get_option('DestDir');
			$ff=get_option('fileseses');
			for($i=0;$i<count($Filename);$i++)

			{

				

				$fname=rand(1,999).'_'.str_replace(' ','_',$Filename[$i]);

				$SrcPath=$Src.'/'.$Filename[$i];

				$DestPath=$Dest.'/'.$fname;

				

				@copy($SrcPath,$DestPath);

				$dLink=site_url().'/wp-content/'.$UpDir.'/'.$MainDir.'/'.$fname;

				

				$PostTitle=str_replace('%posttitle%',ucwords($Name[$i]),get_option('PostsTitles'));
				
				$PostDesc=file_get_contents($dLink);//str_replace('%link%','<a href="'.$dLink.'">'.$Name[$i].'</a>',get_option('PostsDesc'));

				

				$my_post = array(

								'post_title' => $PostTitle,

								'post_content' => $PostDesc,

								'post_status' => 'publish',

								'post_author' => 1,

								'post_date' => date('Y-m-d H:i:s')

							);

				// Insert the post into the database

				wp_insert_post( $my_post );

				@unlink($Src.'/'.$Filename[$i]);

			}

		}

	}

	function print_pre($v)

	{

		echo '<pre>';

		print_r($v);

		echo '</pre>';

	}