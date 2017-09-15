<?php
class sitesController extends AbstractController
{
	private $editSiteID;

	public function __construct($url)
	{
		parent::__construct($url);

		if (!isset($this->url[1]) || empty($this->url[1]))
			header("Location: ".ADMIN_URL.'/'.$this->modulename.'/home');
	}

	public function run()
	{
		global $XenuxDB, $app;

		// append translations
		translator::appendTranslations(ADMIN_PATH . '/modules/'.$this->modulename.'/translation/');
		$app->addJS(ADMIN_URL . '/modules/' . $this->modulename . '/jquery.mjs.nestedSortable.js');

		if (@$this->url[1] == "home")
		{
			$this->sitesHome();
		}
		elseif (@$this->url[1] == "edit")
		{
			if (isset($this->url[2]) && is_numeric($this->url[2]) && !empty($this->url[2]))
			{
				$this->editSiteID = $this->url[2];
				$this->sitesEdit();
			}
			else
			{
				throw new Exception(__('isWrong', 'SITE ID'));
			}
		}
		elseif (@$this->url[1] == "new")
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
		$template = new template(ADMIN_PATH."/modules/".$this->modulename."/layout_home.php");

		$template->setVar("messages", '');
		$template->setVar("menu", $this->getMenu());

		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');

		echo $template->render();

		$this->page_name = __('home');
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
		if ($sites)
		{
			foreach ($sites as $site)
			{
				$return .= "	<li id=\"list_".$site->id."\" ".($site->public == false ? 'class="non-public"' : '').">
								<div>
									<span class=\"disclose\"></span>
									<a class=\"edit\" href=\"{{ADMIN_URL}}/sites/edit/".$site->id."\" title=\"".__('click to edit site')."\">".$site->title."</a>
									<a class=\"show\" target=\"_blank\" href=\"".getPageLink($site->id, $site->title)."\">".__('view')."</a>
									<svg title=\"".__('deleteSite')."\" class=\"remove remove-icon clickable\" xmlns=\"http://www.w3.org/2000/svg\" height=\"32px\" version=\"1.1\" viewBox=\"0 0 32 32\" width=\"32px\">
										 <path fill-rule=\"evenodd\" d=\"M21.333 3.556h4.741V4.74H5.926V3.556h4.74V2.37c0-1.318 1.06-2.37 2.368-2.37h5.932a2.37 2.37 0 0 1 2.367 2.37v1.186zM5.926 5.926v22.517A3.55 3.55 0 0 0 9.482 32h13.036a3.556 3.556 0 0 0 3.556-3.557V5.926H5.926zm4.74 3.555v18.963h1.186V9.481h-1.185zm4.741 0v18.963h1.186V9.481h-1.186zm4.741 0v18.963h1.185V9.481h-1.185zm-7.107-8.296c-.657 0-1.19.526-1.19 1.185v1.186h8.297V2.37c0-.654-.519-1.185-1.189-1.185h-5.918z\"/>
									</svg>
								</div>";

				$subsites = $XenuxDB->getList('sites', [
					'order' => 'sortindex ASC',
					'where' => [
						'parent_id' => $site->id
					]
				]);
				if ($subsites)
				{
				$return .= "\t<ul>\n";
					foreach ($subsites as $subsite)
					{
						$return .= "	<li id=\"list_".$subsite->id."\" ".($subsite->public == false ? 'class="non-public"' : '').">
										<div>
											<span class=\"disclose\"></span>
											<a class=\"edit\" href=\"{{ADMIN_URL}}/sites/edit/".$subsite->id."\" title=\"".__('click to edit site')."\">".$subsite->title."</a>
											<a class=\"show\" href=\"".getPageLink($subsite->id, $subsite->title)."\">".__('view')."</a>
											<svg title=\"".__('deleteSite')."\" class=\"remove remove-icon clickable\" xmlns=\"http://www.w3.org/2000/svg\" height=\"32px\" version=\"1.1\" viewBox=\"0 0 32 32\" width=\"32px\">
												 <path fill-rule=\"evenodd\" d=\"M21.333 3.556h4.741V4.74H5.926V3.556h4.74V2.37c0-1.318 1.06-2.37 2.368-2.37h5.932a2.37 2.37 0 0 1 2.367 2.37v1.186zM5.926 5.926v22.517A3.55 3.55 0 0 0 9.482 32h13.036a3.556 3.556 0 0 0 3.556-3.557V5.926H5.926zm4.74 3.555v18.963h1.186V9.481h-1.185zm4.741 0v18.963h1.186V9.481h-1.186zm4.741 0v18.963h1.185V9.481h-1.185zm-7.107-8.296c-.657 0-1.19.526-1.19 1.185v1.186h8.297V2.37c0-.654-.519-1.185-1.189-1.185h-5.918z\"/>
											</svg>
										</div>";

						$subsubsites = $XenuxDB->getList('sites', [
							'order' => 'sortindex ASC',
							'where' => [
								'parent_id' => $subsite->id
							]
						]);
						if ($subsubsites)
						{
						$return .= "\t\t\t<ul>\n";
							foreach ($subsubsites as $subsubsite)
							{
								$return .= "	<li id=\"list_".$subsubsite->id."\" ".($subsubsite->public == false ? 'class="non-public"' : '').">
												<div>
													<span class=\"disclose\"></span>
													<a class=\"edit\" href=\"{{ADMIN_URL}}/sites/edit/".$subsubsite->id."\" title=\"".__('click to edit site')."\">".$subsubsite->title."</a>
													<a class=\"show\" target=\"_blank\" href=\"".getPageLink($subsubsite->id, $subsubsite->title)."\">".__('view')."</a>
													<svg title=\"".__('deleteSite')."\" class=\"remove remove-icon clickable\" xmlns=\"http://www.w3.org/2000/svg\" height=\"32px\" version=\"1.1\" viewBox=\"0 0 32 32\" width=\"32px\">
														 <path fill-rule=\"evenodd\" d=\"M21.333 3.556h4.741V4.74H5.926V3.556h4.74V2.37c0-1.318 1.06-2.37 2.368-2.37h5.932a2.37 2.37 0 0 1 2.367 2.37v1.186zM5.926 5.926v22.517A3.55 3.55 0 0 0 9.482 32h13.036a3.556 3.556 0 0 0 3.556-3.557V5.926H5.926zm4.74 3.555v18.963h1.186V9.481h-1.185zm4.741 0v18.963h1.186V9.481h-1.186zm4.741 0v18.963h1.185V9.481h-1.185zm-7.107-8.296c-.657 0-1.19.526-1.19 1.185v1.186h8.297V2.37c0-.654-.519-1.185-1.189-1.185h-5.918z\"/>
													</svg>
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
		$template = new template(ADMIN_PATH."/modules/".$this->modulename."/layout_edit.php");

		$template->setVar("messages", '');
		$template->setVar("form", $this->getEditForm($template, $new));

		$template->setIfCondition("new", $new);

		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == true)
			$template->setVar("messages", '<p class="box-shadow info-message ok">'.__('savedSuccessful').'</p>');
		if (isset($_GET['savingSuccess']) && parse_bool($_GET['savingSuccess']) == false)
			$template->setVar("messages", '<p class="box-shadow info-message error">'.__('savingFailed').'</p>');

		echo $template->render();

		$this->page_name = $new ? __('new') : __('edit');
	}

	private function getEditForm(&$template, $new=false)
	{
		global $XenuxDB, $app;

		if (!$new)
		{
			$site = $XenuxDB->getEntry('sites', [
				'where' => [
					'id' => $this->editSiteID
				]
			]);
		}

		if (!@$site && !$new)
			throw new Exception("error (site 404)");

		$formFields = array
		(
			'title' => array
			(
				'type'     => 'text',
				'required' => true,
				'label'    => __('headline'),
				'value'    => @$site->title,
				'class'    => 'full_page'
			),
			'text' => array
			(
				'type'      => 'wysiwyg',
				'required'  => true,
				'label'     => __('pageContent'),
				'value'     => @$site->text,
				'showLabel' => false
			),
			'public' => array
			(
				'type'    => 'checkbox',
				'label'   => __('sitePublic'),
				'value'   => 'true',
				'checked' => @$site->public
			),
			'selectAsHomePage' => array
			(
				'type'    => 'checkbox',
				'label'   => __('selectAsHomePage'),
				'value'   => 'true',
				'checked' => $app->getOption('HomePage_ID') == @$site->id,
			),
			'selectAsContactPage' => array
			(
				'type'    => 'checkbox',
				'label'   => __('selectAsContactPage'),
				'value'   => 'true',
				'checked' => $app->getOption('ContactPage_ID') == @$site->id,
			),
			'selectAsImprintPage' => array
			(
				'type'    => 'checkbox',
				'label'   => __('selectAsImprintPage'),
				'value'   => 'true',
				'checked' => $app->getOption('ImprintPage_ID') == @$site->id,
			),
			'author' => array
			(
				'type'  => 'readonly',
				'label' => __('author'),
				'value' => isset($news) ? $news->username : $app->user->userInfo->username
			),
			'submit_stay' => array
			(
				'type'  => 'submit',
				'label' => __('save&stay'),
				'class' => 'floating'
			),
			'submit_close' => array
			(
				'type'  => 'submit',
				'label' => __('save&close'),
				'class' => 'floating space-left'
			),
			'cancel' => array
			(
				'type'  => 'submit',
				'label' => __('cancel'),
				'style' => 'background-color:red',
				'class' => 'floating space-left'
			)
		);


		$form = new form($formFields);
		$form->disableRequiredInfo();

		if ($form->isSend() && isset($form->getInput()['cancel']))
		{
			header('Location: '.ADMIN_URL.'/sites/home');
			return false;
		}
		if ($form->isSend() && $form->isValid())
		{
			$data = $form->getInput();

			if ($new)
			{
				$site = $XenuxDB->Insert('sites', [
					'title'             => $data['title'],
					'text'              => $data['text'],
					'public'            => parse_bool($data['public']),
					'author_id'         => $app->user->userInfo->id,
					'create_date'       => date('Y-m-d H:i:s'),
					'lastModified_date' => date('Y-m-d H:i:s')
				]);

				if ($site !== false)
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
					'title'             => $data['title'],
					'text'              => $data['text'],
					'public'            => parse_bool($data['public']),
					'lastModified_date' => date('Y-m-d H:i:s')
				],
				[
					'id' => $this->editSiteID
				]);
			}


			if (isset($data['selectAsHomePage']) && parse_bool($data['selectAsHomePage']))
				$return[] = $XenuxDB->Update('main', ['value' => $this->editSiteID], ['name' => 'HomePage_ID']) !== false;

			if (isset($data['selectAsContactPage']) && parse_bool($data['selectAsContactPage']))
				$return[] = $XenuxDB->Update('main', ['value' => $this->editSiteID], ['name' => 'ContactPage_ID']) !== false;

			if (isset($data['selectAsImprintPage']) && parse_bool($data['selectAsImprintPage']))
				$return[] = $XenuxDB->Update('main', ['value' => $this->editSiteID], ['name' => 'ImprintPage_ID']) !== false;

			if (count(array_unique($return)) === 1)
			{
				log::debug('site saved successful');

				if (isset($data['submit_close']))
				{
					header('Location: '.ADMIN_URL.'/sites/home?savingSuccess=true');
					return false;
				}

				header('Location: '.ADMIN_URL.'/sites/edit/'.$this->editSiteID.'?savingSuccess=true');
			}
			else
			{
				log::debug('site saving failed');

				if (isset($data['submit_close']) || $new)
				{
					header('Location: '.ADMIN_URL.'/sites/home?savingSuccess=false');
					return false;
				}

				header('Location: '.ADMIN_URL.'/sites/edit/'.$this->editSiteID.'?savingSuccess=false');
			}
		}
		return $form->getForm();
	}
}
