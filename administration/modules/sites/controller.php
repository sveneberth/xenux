<?php
class sitesController extends AbstractController
{
	private $editSiteID;

	public function __construct($url)
	{
		$this->url = $url;
		$this->modulename = str_replace('Controller', '', get_class());

		if(!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".URL_ADMIN.'/'.$this->modulename.'/home');
	}
	
	public function run()
	{
		global $XenuxDB, $app;

		// append translations
		translator::appendTranslations(PATH_ADMIN . '/modules/sites/translation/');

		if(@$this->url[1] == "home")
		{
			$this->sitesHome();
		}
		elseif(@$this->url[1] == "edit")
		{
			if(isset($this->url[2]) && is_numeric($this->url[2]) && !empty($this->url[2]))
			{
				$this->editSiteID = $this->url[2];
				$this->sitesEdit();
			}
			else
			{
				throw new Exception(__('isWrong', 'SITE ID'));
			}
		}
		elseif(@$this->url[1] == "new")
		{
			$this->sitesEdit(true);
		}
		else
		{
			throw new Exception("404 - $this->modulename template not found");
		}

		return true;
	}

	private function sitesHome()
	{
		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout_home.php");
	
		$template->setVar("messages", '');
		$template->setVar("menu", $this->getMenu());

		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');
		
		echo $template->render();

		$this->page_name = "Sites:home";
	}

	private function getMenu()
	{
		global $XenuxDB;

		$return = '';

		$sites = $XenuxDB->getList('sites', [
			'order' => 'sortindex ASC',
			'where' => [
				'parent_id' => 0
			]
		]);
		if($sites)
		{
			foreach($sites as $site)
			{
				$return .= "	<li id=\"list_".$site->id."\" ".($site->public == false ? 'class="non-public"' : '').">
								<div>
									<span class=\"disclose\"></span>
									<a class=\"edit\" href=\"{{URL_ADMIN}}/sites/edit/".$site->id."\" title=\"".__('click to edit site')."\">".$site->title."</a>
									<a class=\"show\" target=\"_blank\" href=\"".getPageLink($site->id, $site->title)."\">".__('show')."</a>
									<span title=\"".__('deleteSite')."\" class=\"remove remove-icon clickable\"></span>
								</div>";

				$subsites = $XenuxDB->getList('sites', [
					'order' => 'sortindex ASC',
					'where' => [
						'parent_id' => $site->id
					]
				]);
				if($subsites)
				{
				$return .= "\t<ul>\n";
					foreach($subsites as $subsite)
					{
						$return .= "	<li id=\"list_".$subsite->id."\" ".($subsite->public == false ? 'class="non-public"' : '').">
										<div>
											<span class=\"disclose\"></span>
											<a class=\"edit\" href=\"{{URL_ADMIN}}/sites/edit/".$subsite->id."\" title=\"".__('click to edit site')."\">".$subsite->title."</a>
											<a class=\"show\" href=\"".getPageLink($subsite->id, $subsite->title)."\">".__('show')."</a>
											<span target=\"_blank\" title=\"".__('deleteSite')."\" class=\"remove remove-icon clickable\"></span>
										</div>";

						$subsubsites = $XenuxDB->getList('sites', [
							'order' => 'sortindex ASC',
							'where' => [
								'parent_id' => $subsite->id
							]
						]);
						if($subsubsites)
						{
						$return .= "\t\t\t<ul>\n";
							foreach($subsubsites as $subsubsite)
							{
								$return .= "	<li id=\"list_".$subsubsite->id."\" ".($subsubsite->public == false ? 'class="non-public"' : '').">
												<div>
													<span class=\"disclose\"></span>
													<a class=\"edit\" href=\"{{URL_ADMIN}}/sites/edit/".$subsubsite->id."\" title=\"".__('click to edit site')."\">".$subsubsite->title."</a>
													<a class=\"show\" target=\"_blank\" href=\"".getPageLink($subsubsite->id, $subsubsite->title)."\">".__('show')."</a>
													<span title=\"".__('deleteSite')."\" class=\"remove remove-icon clickable\"></span>
												</div>
												</li>";
							}
						$return .= "\t\t\t</ul>\n";
						}
						$return .= "\t\t</li>\n";
					}
				$return .= "\t</ul>\n";
				}
				$return .= "</li>\n";
			}
		}

		return $return;
	}


	private function sitesEdit($new=false)
	{
		$template = new template(PATH_ADMIN."/modules/".$this->modulename."/layout_edit.php");
	
		$template->setVar("messages", '');
		$template->setVar("form", $this->getEditForm($template, $new));
		
		$template->setIfCondition("new", $new);

		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if(isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');
		
		echo $template->render();

		$this->page_name = "Sites:".($new ? 'new' : 'edit');
	}

	private function getEditForm(&$template, $new=false)
	{
		global $XenuxDB, $app;

		if(!$new)
		{
			$site = $XenuxDB->getEntry('sites', [
				'where' => [
					'id' => $this->editSiteID
				]
			]);
		}

		if(!@$site && !$new)
			throw new Exception("error (site 404)");
			
		$formFields = array
		(
			'title' => array
			(
				'type' => 'text',
				'required' => true,
				'label' => __('headline'),
				'value' => @$site->title,
				'class' => 'full_page'
			),
			'text' => array
			(
				'type' => 'textarea',
				'required' => true,
				'label' => __('pageContent'),
				'value' => htmlentities(@$site->text),
				'wysiwyg' => true,
				'showLabel' => false
			),
			'public' => array
			(
				'type' => 'bool_radio',
				'required' => true,
				'label' => __('sitePublic'),
				'value' => @$site->public
			),
			'html' => array
			(
				'type' => 'html',
				'value' => $this->_getContactpersonForForm(),
			),
			'selectAsHomePage' => array
			(
				'type' => 'checkbox',
				'label' => __('selectAsHomePage'),
				'value' => 'true',
				'checked' => $app->getOption('HomePage_ID') == @$site->id,
			),
			'selectAsContactPage' => array
			(
				'type' => 'checkbox',
				'label' => __('selectAsContactPage'),
				'value' => 'true',
				'checked' => $app->getOption('ContactPage_ID') == @$site->id,
			),
			'selectAsImprintPage' => array
			(
				'type' => 'checkbox',
				'label' => __('selectAsImprintPage'),
				'value' => 'true',
				'checked' => $app->getOption('ImprintPage_ID') == @$site->id,
			),
			'author' => array
			(
				'type' => 'readonly',
				'label' => __('author'),
				'value' => isset($news) ? $news->username : $app->user->userInfo->username
			),
			'submit_stay' => array
			(
				'type' => 'submit',
				'label' => __('save&stay'),
				'class' => 'floating'
			),
			'submit_close' => array
			(
				'type' => 'submit',
				'label' => __('save&close'),
				'class' => 'floating space-left'
			),
			'cancel' => array
			(
				'type' => 'submit',
				'label' => __('cancel'),
				'style' => 'background-color:red',
				'class' => 'floating space-left'
			)
		);

		$_allowedTitleChars	= '/[^a-zA-Z0-9_üÜäÄöÖ$€&#,.()\s"\']/';
		$_allowedTags		= '<font><b><strong><a><i><em><u><span><div><p><img><ol><ul><li><h1><h2><h3><h4><h5><h6><table><tr><td><th><br><hr><code><pre><del><ins><blockquote><sub><sup><address><q><cite><var><samp><kbd><tt><small><big><s><iframe><caption><tbody><thead><tfoot><embed><object><param>';

		$form = new form($formFields);
		$form->disableRequiredInfo();

		if($form->isSend() && isset($form->getInput()['cancel']))
		{
			header('Location: '.URL_ADMIN.'/news/home');
			return false;
		}
		if($form->isSend() && $form->isValid())
		{
			$data = $form->getInput();

			$title = preg_replace($_allowedTitleChars, '' , $data['title']);
			$title = htmlentities($title);

			$text = strip_tags($data['text'], $_allowedTags);

			$public = parse_bool($data['public']);

			$author = $app->user->userID;

			if($new)
			{
				$site = $XenuxDB->Insert('sites', [
					'title'				=> $title,
					'text'				=> $text,
					'public'			=> $public,
					'author_id'			=> $author,
					'create_date'		=> date('Y-m-d H:i:s'),
					'lastModified_date'	=> date('Y-m-d H:i:s')
				]);

				if($site !== false)
				{
					$return[] = true;
					$this->editSiteID = $site;
				}
				else
				{
					$return[] = false;
				}
			}
			else
			{
				// update it
				$return[] = $XenuxDB->Update('sites', [
					'title'				=> $title,
					'text'				=> $text,
					'public'			=> $public,
					'lastModified_date'	=> date('Y-m-d H:i:s')
				],
				[
					'id' => $this->editSiteID
				]);
				
				$return[] = $XenuxDB->Delete('site_contactperson', [
					'where' => [
						'site_id' => $this->editSiteID
					]
				]);
			}
			
			$contactpersons = $XenuxDB->getList('contactpersons', [
				'order' => 'name ASC'
			]);
			if($contactpersons)
			{
				foreach($contactpersons as $contactperson)
				{
					if(isset($_POST['contact_'.$contactperson->id]))
						$return[] = $XenuxDB->Insert('site_contactperson', [
							'site_id'			=> $this->editSiteID,
							'contactperson_id'	=> $contactperson->id
						]) !== false;
				}
			}

			if(isset($data['selectAsHomePage']) && parse_bool($data['selectAsHomePage']))
				$return[] = $XenuxDB->Update('main', ['value' => $site->id], ['name' => 'HomePage_ID']) !== false;

			if(isset($data['selectAsContactPage']) && parse_bool($data['selectAsContactPage']))
				$return[] = $XenuxDB->Update('main', ['value' => $site->id], ['name' => 'ContactPage_ID']) !== false;

			if(isset($data['selectAsImprintPage']) && parse_bool($data['selectAsImprintPage']))
				$return[] = $XenuxDB->Update('main', ['value' => $site->id], ['name' => 'ImprintPage_ID']) !== false;

			if(count(array_unique($return)) === 1)
			{
				log::writeLog('saved successful');
				$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');

				if(isset($data['submit_close']))
				{
					header('Location: '.URL_ADMIN.'/sites/home?savingSuccess=true');
					return false;
				}

				header('Location: '.URL_ADMIN.'/sites/edit/'.$this->editSiteID.'?savingSuccess=true');
			}
			else
			{
				$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');
				log::writeLog('saving failed');

				if(isset($data['submit_close']))
				{
					header('Location: '.URL_ADMIN.'/sites/home?savingSuccess=false');
					return false;
				}
				
				header('Location: '.URL_ADMIN.'/sites/edit/'.$this->editSiteID.'?savingSuccess=false');
			}			
		}
		return $form->getForm();
	}

	private function _getContactpersonForForm()
	{
		global $XenuxDB;

		$return  = '<div class="contact-persons-wrapper">';
		$return .= '<h3>' . __('contactpersons') . '</h3>';

		$contactpersons = $XenuxDB->getList('contactpersons', [
			'order' => 'name ASC'
		]);
		if($contactpersons)
		{
			foreach($contactpersons as $contactperson)
			{
				$return .= "<input ";
				$num = $XenuxDB->Count('site_contactperson', [
					'where' => [
						'AND' => [
							'site_id' => $this->editSiteID,
							'contactperson_id' => $contactperson->id
							]
						]
				]);

				if((empty($_POST) && $num >= 1) || isset($_POST['contact_'.$contactperson->id]))
					$return .= 'checked';

				$return .= " type=\"checkbox\" id=\"contact_{$contactperson->id}\" name=\"contact_{$contactperson->id}\" value=\"true\"><label for=\"contact_{$contactperson->id}\">{$contactperson->name}</label>";
				}
		}

		$return .= '</div>';

		return $return;
	}
}
?>