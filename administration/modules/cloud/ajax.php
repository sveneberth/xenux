<?php
// include Xenux-Loader
include_once(dirname(dirname(dirname(__DIR__))) . "/core/xenux-load.php");

if(!(defined('DEBUG') && DEBUG == true))
	error_reporting(0);

if(!$app->user->isLogin())
	ErrorPage::view(404);

$return = array();
$return['success'] = false;

switch(@$_REQUEST['task'])
{
	case 'upload':
		if(isset($_REQUEST['parent_folder']))
		{
			$return['success'] = true;

			$parent_folder = $XenuxDB->escapeString($_REQUEST['parent_folder']);
			foreach ($_FILES as $key => $file)
			{
				$name			= $file['name'];
				$tmpname		= $file['tmp_name'];
				$mime_type		= $file['type'];
				$size			= $file['size'];
				$lastModified	= filemtime($tmpname);

				$hndFile = fopen($tmpname, "r");
				$data = addslashes(fread($hndFile, $size));

				$result = $XenuxDB->Insert('files', [
														'type'				=> 'file',
														'mime_type'			=> $mime_type,
														'#data'				=> $data,
														'filename'			=> $name,
														'size'				=> $size,
														'lastModified'		=> date("Y-m-d H:i:s", $lastModified),
														'parent_folder_id'	=> $parent_folder,
														'author_id'			=> $app->user->userInfo->id
													]);
				if($result === false)
				{
					$return['success'] = false;
					break;
				}
			}
		}
		else
		{
			$return['message'] = 'invalid request';
			$return['success'] = false;
		}
		break;
	case 'create_folder':
		if(isset($_REQUEST['folder_name']) && isset($_REQUEST['parent_folder']))
		{
			$return['success'] = true;

			$folder_name	= $_REQUEST['folder_name'];
			$parent_folder	= $_REQUEST['parent_folder'];

			$result = $XenuxDB->Insert('files', [
													'type'				=> 'folder',
													'filename'			=> $folder_name,
													'lastModified'		=> date("Y-m-d H:i:s"),
													'parent_folder_id'	=> $parent_folder,
												]);

			if($result === false)
				$return['success'] = false;
		}
		else
		{
			$return['message'] = 'invalid request';
			$return['success'] = false;
		}
		break;
	case 'dir_list':
		if(isset($_REQUEST['folder']))
		{
			$folder = $_REQUEST['folder'];

			$result = $XenuxDB->getList('files', [
				'columns' => [
					'id',
					'type',
					'filename',
					'mime_type'
				],
				'where' => [
					'parent_folder_id' => $folder
				],
				'order' => 'filename ASC'
			]);

			if($result === null || empty($result))
			{
				$return['message'] = 'folder not found';
				$return['success'] = false;
			}
			elseif($result !== false)
			{
				$return['data'] = $result;
				$return['success'] = true;
			}
			else
			{
				$return['success'] = false;
			}
		}
		else
		{
			$return['message'] = 'invalid request';
			$return['success'] = false;
		}
		break;
	case 'breadcrumb':
		if(isset($_REQUEST['folder']))
		{
			$return['success'] = true;

			$folder = $_REQUEST['folder'];

			$breadcrumb = array();
			while($folder != 0)
			{
				$row = $XenuxDB->getEntry('files', [
					'columns' => [
						'id',
						'parent_folder_id',
						'filename',
					],
					'where' => [
						'id' => $folder
					],
					'order' => 'filename ASC'
				]);


				if($row === false || $row === null)
				{
					if($row === null)
						$return['message'] = 'folder not found';

					$return['success'] = false;
					break;
				}

				$folder = $row->parent_folder_id;
				$breadcrumb[] = $row;
			}

			$breadcrumb = array_reverse($breadcrumb);
			$return['data'] = $breadcrumb;
		}
		else
		{
			$return['message'] = 'invalid request';
			$return['success'] = false;
		}
		break;
	case 'getFileInfo':
		if(isset($_REQUEST['id']))
		{
			$return['success'] = true;

			$id = $_REQUEST['id'];

			$row = $XenuxDB->getEntry('files', [
				'columns' => [
					'files.id',
					'filename',
					'parent_folder_id',
					'username(author)',
					'mime_type',
					'size',
					'#DATE_FORMAT(lastModified, \'%d.%m.%Y %H:%i:%s\')(lastModified)',
				],
				'where' => [
					'files.id' => $id
				],
				'join' => [
					'[>]users' => ['files.author_id' => 'users.id']
				]
			]);

			if($row)
			{
				$return['data'] = $row;
			}
			else
			{
				$return['success'] = false;
			}
		}
		else
		{
			$return['message'] = 'invalid request';
			$return['success'] = false;
		}
		break;
	case 'remove':
		if(isset($_REQUEST['id']))
		{
			$id = $XenuxDB->escapeString($_REQUEST['id']);

			if($id == 0)
			{
				$XenuxDB->clearTable('files');
				break;
			}

			$num = $XenuxDB->Count('files', [
				'where' => [
					'id' => $id
				]
			]);
			if($num == 0)
			{
				$return['errmsg'] = "to remove file doesn't exist";
				$return['success'] = false;
				break;
			}

			$row = $XenuxDB->getEntry('files', [
				'columns' => [
					'id',
					'type'
				],
				'where' => [
					'id' => $id
				]
			]);

			$XenuxDB->Delete('files', [
				'where' => [
					'id' => $id
				]
			]);

			if($row->type == 'folder')
			{
				$arrFolder = array();
				$arrFolder[] = $id;

				while(!empty($arrFolder))
				{
					$arrTemp = $arrFolder;
					$arrFolder = array();

					foreach ($arrTemp as $val) // for every file/folder
					{
						$results = $XenuxDB->getList('files', [
							'where' => [
								'parent_folder_id' => $val
							]
						]);

						foreach ($results as $result)
						{
							if($result->type == 'folder')
							{
								$arrFolder[] = $result->id;
							}

							$XenuxDB->Delete('files', [
								'where' => [
									'id' => $result->id
								]
							]);
						}
					}
				}
			}
		}
		else
		{
			$return['message'] = 'invalid request';
			$return['success'] = false;
		}
		break;
	case 'move':
		if(isset($_REQUEST['id']) && isset($_REQUEST['to']))
		{
			$id = $XenuxDB->escapeString($_REQUEST['id']);
			$to = $XenuxDB->escapeString($_REQUEST['to']);

			if($id == $to)
			{
				$return['message'] = "can't move folder in self";
				$return['success'] = false;
				break;
			}

			$XenuxDB->Update('files', [
				'parent_folder_id' => $to
			],
			[
				'id' => $id
			]);
		}
		else
		{
			$return['message'] = 'invalid request';
			$return['success'] = false;
		}
		break;
	case 'rename':
		if(isset($_REQUEST['id']) && isset($_REQUEST['newName']))
		{
			$id			= $XenuxDB->escapeString($_REQUEST['id']);
			$newName	= $XenuxDB->escapeString($_REQUEST['newName']);

			$result = $XenuxDB->Update('files', [
				'filename' => $newName,
				'##lastModified' => 'NOW()'
			],
			[
				'id' => $id
			]);
			if($result === false)
				$return['success'] = false;
		}
		else
		{
			$return['message'] = 'invalid request';
			$return['success'] = false;
		}
		break;
	case 'list_all_dirs':
		$return['success'] = true;

		$id				= 0;
		$arrAll			= array();
		$arrAll[]		= $id;
		$arrFolder		= array();
		$arrFolder[]	= $id;

		while(!empty($arrFolder))
		{
			$arrTemp = $arrFolder;
			$arrFolder = array();

			foreach ($arrTemp as $val)
			{
				$results = $XenuxDB->getList('files', [
					'columns' => [
						'id',
						'type',
						'filename',
						'parent_folder_id'
					],
					'where' => [
						'AND' => [
							'type' => 'folder',
							'parent_folder_id' => $val
						]
					],
					'order' => 'filename ASC'
				]);

				foreach ($results as $result)
				{
					$arrFolder[] = $result->id;
					$arrAll[] = $result->id;
				}
			}
		}

		foreach ($arrAll as $val)
		{
			$folder = $val;
			$breadcrumb = array();
			while($folder != 0)
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
				$breadcrumb[] = $row;
			}
			$breadcrumb = array_reverse($breadcrumb);

			$return['breadcrumbs'][$val] = $breadcrumb;
		}

		foreach ($return['breadcrumbs'] as $key => $val)
		{
			$temp = "root";
			foreach ($val as $subval)
			{
				$temp .= "/".$subval->filename;
			}
			$return['data'][$key] = $temp;
		}

		break;
}


header('Content-type: application/json');
echo json_encode($return);

// close the connection to the database
$XenuxDB->closeConnection();
