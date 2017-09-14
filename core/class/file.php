<?php
/**
 * class file (a part of the Xenux-Cloud)
 * request: {{URL_MAIN}}/file/FILE-ID{flags}
 * flags:
 *  -s(int)	: set the width for an image
 * -c		: get a square images
 * -d		: get the file as download
 * -r		: rotate an image clockwise
 */

class file
{
	private $urlPart;
	private $fileID;
	private $file;

	public function __construct($url = null)
	{
		if (@$url[1] != '')
		{
			$this->urlPart = $url[1];
			$this->fileID  = explode('-', $this->urlPart)[0];

			if (is_numeric($this->fileID))
			{
				$this->render();
			}
			else
			{
				// missing id ...
				log::debug('405 - wrong format of parameter `id`');
				ErrorPage::view(405);
			}
		}
		else
		{
			// missing id ...
			log::debug('405 - missing parameter `id`');
			ErrorPage::view(405);
		}
	}

	private function render()
	{
		global $app, $XenuxDB;

		if ($this->fileID == 0)
		{
			// simulate a folder if root selected
			$this->file           = new stdClass();
			$this->file->id       = 0;
			$this->file->type     = 'folder';
			$this->file->filename = 'root';
		}
		else
		{
			$this->file = $XenuxDB->getEntry('files', [
				'where' => [
					'id' => $this->fileID
				]
			]);
		}

		if ($this->file)
		{
			if ($this->file->type == 'file')
			{
				$this->renderFile();
			}
			elseif ($this->file->type == 'folder')
			{
				// allow download only for admins
				if ($app->user->isLogin())
				{
					$this->renderFolder();
				}
				else
				{
					log::debug('401 - not allowed to download folder <"'.$this->fileID.'">');
					ErrorPage::view(401);
				}
			}
			else
			{
				// no folder and no file ...
				log::debug('500 - error with filetype of <"'.$this->fileID.'">');
				ErrorPage::view(500);
			}
		}
		else
		{
			// item not found ...
			log::debug('404 - requested item <"'.$this->fileID.'"> not found');
			ErrorPage::view(404);
		}
	}


	private function renderFile()
	{
		global $XenuxDB;

		$lastModified = mysql2date('D, d M Y H:i:s', $this->file->lastModified);

		preg_match_all('/-([a-z]?)([0-9]*)/', $this->urlPart, $optionMatches, PREG_SET_ORDER);

		$options = array();
		foreach ($optionMatches as $match)
		{
			$options[$match[1]] = $match[2];
		}

		header("Content-Disposition: " . (isset($options['d']) ? 'attachment' : 'inline') .
			"; filename=\"{$this->file->filename}.{$this->file->file_extension}\"");
		header("Cache-Control: public, max-age=3600");
		header("Last-Modified: {$lastModified} GMT");
		header("Content-Length: {$this->file->size}");

		if (
			in_array($this->file->mime_type, ['image/jpeg', 'image/gif', 'image/png'])
			&& (isset($options['c']) || isset($options['r']) || isset($options['s']))
		)
		{
			$image = imagecreatefromstring($this->file->data);

			if (isset($options['r']) && is_numeric($options['r']))
				$image = imagerotate($image, 360-$options['r'], imageColorAllocateAlpha($image, 0, 0, 0, 127));

			$x = imagesx($image);
			$y = imagesy($image);

			if (isset($options['s']))
				$options['s'] = $options['s'] > $x ? $x : $options['s'];

			if (isset($options['c']))
			{
				$desired_height	= $desired_width = isset($options['s']) && is_numeric($options['s']) ? $options['s'] : $y;
			}
			else
			{
				$desired_width = (isset($options['s']) && is_numeric($options['s'])) ? $options['s'] : $x;
				$desired_height = $y / $x * $desired_width;
			}

			$new = imagecreatetruecolor($desired_width, $desired_height);
			imagealphablending($new, FALSE);
			imagesavealpha($new, TRUE);
			imagecopyresampled($new, $image, 0, 0, 0, 0, $desired_width, $desired_height, $x, $y);
			imagedestroy($image);

			if ($this->file->mime_type == 'image/jpeg')
			{
				header('Content-type: image/jpeg');
				imagejpeg($new);
			}
			elseif ($this->file->mime_type == 'image/gif')
			{
				header('Content-type: image/gif');
				imagegif($new);
			}
			else
			{
				header('Content-type: image/png');
				imagepng($new);
			}
		}
		else
		{
			header("Content-type: {$this->file->mime_type}");
			echo $this->file->data;
		}
	}

	private function renderFolder()
	{
		global $XenuxDB;

		$tmppath = PATH_MAIN . '/tmp/' . generateRandomString(10) . '/';
		create_folder($tmppath);

		$zip = new ZipArchive();
		$filename = $tmppath . 'archive.zip';

		if ($zip->open($filename, ZIPARCHIVE::CREATE) === true)
		{
			$id          = $this->file->id;
			$arrFolder   = array();
			$arrAll      = array();
			$arrFolder[] = $id;
			$arrAll[]    = $id;

			while (!empty($arrFolder))
			{
				$arrTemp = $arrFolder;
				$arrFolder = array();

				foreach ($arrTemp as $val)
				{
					$results = $XenuxDB->getList('files', [
						'where' => [
							'parent_folder_id' => $val
						],
						'order' => 'filename ASC'
					]);

					foreach ($results as $result)
					{
						if ($result->type == 'file')
						{

							$folder = $result->parent_folder_id;
							$breadcrumb = array();
							while ($folder != $this->file->id)
							{
								$row = $XenuxDB->getEntry('files', [
									'columns' => [
										'id',
										'filename',
										'parent_folder_id'
									],
									'where' => [
										'id' => $folder
									]
								]);

								$folder = $row->parent_folder_id;
								$breadcrumb[] = $row->filename;
							}
							$breadcrumb = array_reverse($breadcrumb);
							$path  = implode('/', $breadcrumb);
							$path .= $path == '' ? '' : '/';

							$zip->addFromString($path . $result->filename . '.' . $result->file_extension, $result->data);
						}
						elseif ($result->type == 'folder')
						{
							$arrFolder[] = $result->id;
						}
						$arrAll[] = $result->id;
					}
				}
			}

			$zip->close();

			header("Content-type: application/octet-stream");
			header("Content-Length: " . filesize($tmppath . 'archive.zip'));
			header("Content-Disposition: attachment; filename=\"{$this->file->filename}.zip\"");
			header("Content-Transfer-Encoding: binary");
			clearstatcache(); // make sure the file size isn't cached

			readfile($tmppath . 'archive.zip');

			rrmdir($tmppath); // remove tmp folder
		}
		else
		{
			log::setPHPError("cannot open <$filename>");
			ErrorPage::view(500);
		}
	}
}
