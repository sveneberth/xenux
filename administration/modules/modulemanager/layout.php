<script>
	var url = window.location.href;
	var title = document.title;
	var newUrl = url.substring(0, url.indexOf('?')) + window.location.hash;
	// replace new url
	if(window.history.replaceState) {
		window.history.replaceState(null, null, newUrl);
	}
</script>

{{messages}}

<section class="box-shadow floating one-column-box">
	<p>upload a module to install.</p>

	<?php
	$formFields = array
		(
			'file' => array
			(
				'type' => 'file',
				'required' => true,
				'multiple' => false,
				'label' => __('upload file')
			),
			'submit' => array
			(
				'type' => 'submit',
				'label' => __('upload')
			)
		);
	
	$form = new form($formFields);
	$form->disableRequiredInfo();

	echo $form->getForm();


	echo "<pre>";
	var_dump($_FILES);
	echo "</pre>";


	if ($form->isSend() && $form->isValid())
		{
			echo "send";

			if (!file_exists(PATH_MAIN."/tmp/")) // create folder, if doesn't exists
				mkdir(PATH_MAIN."/tmp/");

			$uploaddir = PATH_MAIN . '/tmp/'; // uploaddir
			
			$uploadfile = $uploaddir . basename($_FILES['file']['name']); // uploadfile

			#FIXME: accept only .zip files
			if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) // upload file
			{
				echo '<p>uploading successful</p>';

				$zip = new ZipArchive;
				if ($zip->open($uploadfile)) // unzip the file
				{
					$zip->extractTo($uploaddir . '/module/');
					$zip->close();

					echo '<p>unzipping successeful</p>';

					$modulehelper = new modulehelper;
					#$modulehelper->modulepath = 
					include_once($uploaddir . '/module/install.php'); // run installer


					#FIXME: it doesn't work
					deleteDirectory(PATH_MAIN . '/tmp/'); //remove temp-folder
				}
				else // something went wrong
				{
					echo '<p>something went wrong :/</p>';
				}
				
			}
			else
			{
				echo '<p>upload failed</p>';
			}
		}
	?>
</section>