<?php
class form
{
	private $data;
	private $fields;
	private $class;
	private $id;
	private $action;
	private $method;
	private $error_msg = array();
	private $requiredInfo = true;

	public function __construct(array $fields, $class=null, $id=null, $action=null, $method='post')
	{
		$this->fields = $fields;
		$this->class  = $class;
		$this->id     = $id;
		$this->action = $action;
		$this->method = strtolower($method);

		$this->data = $this->isSend() ? $this->getInput() : array();
	}

	public function isSend()
	{
		return $_SERVER['REQUEST_METHOD'] == strtoupper($this->method);
	}

	public function getInput()
	{
		if (full($this->data))
			return $this->data;

		$data = $this->method == 'post' ? $_POST : ($this->method == 'get' ? $_GET : false);
		$return = [];

		foreach ($this->fields as $name => $props)
		{
			if (in_array($props['type'], ['html', 'file'])) // those types has no value
				continue;

			$value = isset($data[$name]) ? $data[$name] : null;

			switch($props['type']) // custom handling
				{
					case 'text':
					case 'textarea':
						$value = htmlentities($value, ENT_SUBSTITUTE, "UTF-8");
						break;
					case 'wysiwyg':
						$allowedTags =	'<b><strong><a><i><em><u><span><div><p><img><ol><ul><li>' .
										'<h1><h2><h3><h4><h5><h6><br><hr><code><pre><blockquote><sub><sup>';

						$value = strip_tags($value, $allowedTags);
						break;
				}

			$return[$name] = $value;
		}

		return $return;
	}

	public function getForm()
	{
		global $app;

		$formTemplate = new template($this->getFormTemplateURL('form.php'));

		$formTemplate->setVar('class', $this->class);
		$formTemplate->setVar('id', $this->id);
		$formTemplate->setVar('action', $this->action);
		$formTemplate->setVar('method', $this->method);
		$formTemplate->setVar('fields', $this->getFormFields($this->fields));
		$formTemplate->setVar('error_msg', $this->isSend() ? $this->getErrorMsg() : '');

		$formTemplate->setIfCondition('requiredInfo', $this->requiredInfo);

		return $formTemplate->render();
	}

	public function isValid()
	{
		global $XenuxDB, $app;

		foreach ($this->fields as $name => $props)
		{
			if ($props['type'] == 'html')
				continue;

			if ($props['type'] == 'file')
			{
				if (!isset($_FILES[$name]) || ($props['multiple'] ? $_FILES[$name]['error'][0] : $_FILES[$name]['error']) == 4)
				{
					$this->setErrorMsg(__('please select a file in field', $props['label']));
					$this->setFieldInvalid($name);
				}
				continue;
			}

			$this->fields[$name]['validInput'] = true;

			if (isset($props['required']) && $props['required'] == true && empty($this->data[$name]))
			{
				$this->setErrorMsg(__('please fill field', $props['label']));
				$this->setFieldInvalid($name);
			}

			if (!empty($this->data[$name]))
			{
				switch($props['type'])
				{
					case 'text':
					case 'textarea':
					case 'wysiwyg':
						// what can you do wrong here? ;)
						break;
					case 'password':
						$min_length = isset($props['min_length']) ? $props['min_length'] : 8;
						if (strlen($this->data[$name]) < $min_length)
						{
							$this->setErrorMsg(__('password to short. minlength', $props['label'], $min_length));
							$this->setFieldInvalid($name);
						}
						break;
					case 'email':
						if (filter_var($this->data[$name], FILTER_VALIDATE_EMAIL) == false)
						{
							$this->setErrorMsg(__('please fill in a valid eMail-adress', $props['label']));
							$this->setFieldInvalid($name);
						}
						break;
					case 'number':
						if (!is_numeric($this->data[$name]))
						{
							$this->setErrorMsg(__('only number in field', $props['label']));
							$this->setFieldInvalid($name);
						}
						break;
					case 'cloud-file':
						$props['allowedTypes'] = isset($props['allowedTypes']) ? $props['allowedTypes'] : '*';
						if (is_string($props['allowedTypes'])) $props['allowedTypes'] = [$props['allowedTypes']];

						$result = $XenuxDB->getEntry('files', [
							'columns' => [
								'type',
								'mime_type'
							],
							'where' => [
								'id' => $this->data[$name]
							]
						]);

						$typeCategory = explode('/', @$result->mime_type)[0];

						var_dump($props);
						if (!is_numeric($this->data[$name]) || @$result->type != 'file' || (
							(
								!in_array(@$result->mime_type, $props['allowedTypes']) ||
								!in_array($typeCategory . '/*', $props['allowedTypes'])
							) && !in_array('*', $props['allowedTypes'])))
						{
							$this->setErrorMsg(__('file not allowed in field', $props['label']));
							$this->setFieldInvalid($name);
						}
						break;
					case 'date':
						if (!preg_match('/[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/', $this->data[$name]))
						{
							$this->setErrorMsg(__('please fill in a valid date', $props['label']));
							$this->setFieldInvalid($name);
						}
						break;
					case 'time':
						if (!preg_match('/(2[0-3]|[01][0-9]):([0-5][0-9])(:[0-5][0-9])?/', $this->data[$name]))
						{
							$this->setErrorMsg(__('please fill in a valid time', $props['label']));
							$this->setFieldInvalid($name);
						}
						break;
				}
			}
		}

		if (!empty($this->error_msg))
			return false;

		return true;
	}

	public function disableRequiredInfo()
	{
		$this->requiredInfo	= false;
	}

	public function setErrorMsg($msg)
	{
		$this->error_msg[]	= $msg;
	}

	public function setFieldInvalid($field)
	{
		$this->fields[$field]['validInput'] = false;
	}

	private function getErrorMsg()
	{
		global $app;

		$this->error_msg = array(); // no duplicates
		$this->isValid(); // needed to get the error messages

		$messages = '';
		foreach ($this->error_msg as $message)
		{
			$msgTemplate = new template($this->getFormTemplateURL('_form_error_msg.php'));
			$msgTemplate->setVar('err_message', $message);
			$messages .= $msgTemplate->render();
		}

		$errTemplate = new template($this->getFormTemplateURL('_form_error.php'));
		$errTemplate->setVar('error_messages', $messages);

		return $errTemplate->render();
	}

	private function getFormFields()
	{
		$form_fields = '';

		foreach ($this->fields as $name => $props)
		{
			$form_fields .= $this->getFormField($name, $props) . "\n";
			if (isset($props['info']) && !empty($props['info']))
				$form_fields .= '<span class="info">' . $props['info'] . '</span>' . "\n";
		}

		return $form_fields;
	}

	private function getFormField($fieldname, array $props)
	{
		global $app;

		$props['type']      = isset($props['type'])         ? $props['type']             : 'text';
		$props['class']     = isset($props['class'])        ? $props['class']            : '';
		$props['style']     = isset($props['style'])        ? $props['style']            : '';
		$props['value']     = isset($props['value'])        ? $props['value']            : '';
		$props['label']     = isset($props['label'])        ? $props['label']            : '';
		$props['checked']   = isset($props['checked'])      ? $props['checked']          : false;
		$props['required']  = isset($props['required'])     ? $props['required']         : false;
		$props['showLabel'] = isset($props['showLabel'])    ? $props['showLabel']        : true;
		$props['label']     = isset($props['label'])        ? $props['label']            : '';

		$class  = $props['class'] . (isset($props['validInput']) && $props['validInput'] == false ? ' wrong' : '');
		$value  = isset($this->data[$fieldname]) ? $this->data[$fieldname] : $props['value'];
		$label  = isset($props['label:before']) ? $props['label:before'] . ' ' : '';
		$label .= $props['label'];
		$label .= isset($props['label:after'])  ? ' '.$props['label:after'] : '';
		$label .= $this->isRequired($props['required'] && $this->requiredInfo == true) ? ' ('.__('required').')' : '';

		$fieldTemplate = new template();

		$fieldTemplate->setVar('class', $class);
		$fieldTemplate->setVar('style', $props['style']);
		$fieldTemplate->setVar('label', $props['label']);
		$fieldTemplate->setVar('name', $fieldname);
		if ($props['type'] != 'file')
			$fieldTemplate->setVar('value', $value);
		$fieldTemplate->setVar('required', $this->isRequired($props['required']));

		$fieldTemplate->setIfCondition('showLabel',	$props['showLabel']);

		switch(strtolower($props['type']))
		{
			case 'text':
				return $fieldTemplate->render($this->getFormTemplateURL('_form_text.php'));
				break;
			case 'password':
				return $fieldTemplate->render($this->getFormTemplateURL('_form_password.php'));
				break;
			case 'email':
				return $fieldTemplate->render($this->getFormTemplateURL('_form_email.php'));
				break;
			case 'textarea':
				return $fieldTemplate->render($this->getFormTemplateURL('_form_textarea.php'));
				break;
			case 'wysiwyg':
				$fieldTemplate->setVar('class', $class . ' ckeditor');
				$fieldTemplate->setVar('value', htmlentities($value, ENT_SUBSTITUTE, "UTF-8"));
				$app->addJS(URL_ADMIN . '/wysiwyg/ckeditor.js');
				return $fieldTemplate->render($this->getFormTemplateURL('_form_textarea.php'));
				break;
			case 'select':
				$props['options'] = isset($props['options']) ? $props['options'] : '';
				$fieldTemplate->setVar('options', $this->getSelectOptions($props['options'], $value));
				return $fieldTemplate->render($this->getFormTemplateURL('_form_select.php'));
				break;
			case 'radio':
				$props['options'] = isset($props['options']) ? $props['options'] : '';
				$fieldTemplate->setVar('radios', $this->getRadioOptions($props['options'], $fieldname, $value, $class, $props['style']));
				return $fieldTemplate->render($this->getFormTemplateURL('_form_radio_fieldset.php'));
				break;
			case 'bool_radio':
				$props['value'] = $value ? 'true' : 'false';
				$props['type'] = 'radio';
				$props['options'] = array
				(
					array
					(
						'value' => 'true',
						'label' => __('yes')
					),
					array
					(
						'value' => 'false',
						'label' => __('no')
					),
				);
				return $this->getFormField($fieldname, $props);
				break;
			case 'checkbox':
				$fieldTemplate->setVar('checked', $this->isChecked($props['checked']));
				return $fieldTemplate->render($this->getFormTemplateURL('_form_checkbox.php'));
				break;
			case 'number':
				$props['min_number'] = isset($props['min_number']) ? $props['min_number'] : '';
				$props['max_number'] = isset($props['max_number']) ? $props['max_number'] : '';
				$fieldTemplate->setVar('min_number', $props['min_number']);
				$fieldTemplate->setVar('max_number', $props['max_number']);
				return $fieldTemplate->render($this->getFormTemplateURL('_form_number.php'));
				break;
			case 'date':
				return $fieldTemplate->render($this->getFormTemplateURL('_form_date.php'));
				break;
			case 'time':
				return $fieldTemplate->render($this->getFormTemplateURL('_form_time.php'));
				break;
			case 'readonly':
				return $fieldTemplate->render($this->getFormTemplateURL('_form_text_readonly.php'));
				break;
			case 'hidden':
				return $fieldTemplate->render($this->getFormTemplateURL('_form_hidden.php'));
				break;
			case 'html':
				return $props['value'];
				break;
			case 'file':
				$props['multiple'] = isset($props['multiple']) ? $props['multiple'] : false;
				$fieldTemplate->setIfCondition('multiple', $props['multiple']);
				return $fieldTemplate->render($this->getFormTemplateURL('_form_file_upload.php'));
				break;
			case 'cloud-file':
				$props['allowedTypes'] = isset($props['allowedTypes']) ? $props['allowedTypes'] : '*';
				if (is_string($props['allowedTypes'])) $props['allowedTypes'] = [$props['allowedTypes']];
				$app->addCSS(URL_ADMIN . '/modules/cloud/explorer.min.css');
				$app->addJS(URL_ADMIN . '/modules/cloud/explorer.js');
				$fieldTemplate->setVar('allowedTypes', json_encode($props['allowedTypes']));
				return $fieldTemplate->render($this->getFormTemplateURL('_form_cloud-file.php'));
				break;
			case 'submit':
				return $fieldTemplate->render($this->getFormTemplateURL('_form_submit.php'));
				break;
		}

		return false;
	}

	private function getSelectOptions(array $options, $value=null)
	{
		global $app;

		$select_options = '';

		foreach ($options as $option)
		{
			$option['disabled'] = isset($props['disabled']) ? $props['disabled'] : '';

			$optionTemplate = new template($this->getFormTemplateURL('_form_select_option.php'));

			$optionTemplate->setVar('value', $option['value']);
			$optionTemplate->setVar('label', $option['label']);
			$optionTemplate->setVar('selected', $this->isSelected($option['value'] == $value));
			$optionTemplate->setVar('disabled', $this->isDisabled($option['disabled']));

			$select_options .= $optionTemplate->render() . "\n";
		}

		return $select_options;
	}

	private function getRadioOptions(array $options, $fieldname, $value=null, $class=null, $style=null)
	{
		global $app;

		$radio_options = '';

		foreach ($options as $option)
		{
			$option['disabled'] = isset($props['disabled']) ? $props['disabled'] : '';

			$optionTemplate = new template($this->getFormTemplateURL('_form_radio.php'));

			$optionTemplate->setVar('value', $option['value']);
			$optionTemplate->setVar('name', $fieldname);
			$optionTemplate->setVar('label', $option['label']);
			$optionTemplate->setVar('class', $class);
			$optionTemplate->setVar('style', $style);
			$optionTemplate->setVar('checked', $this->isChecked($option['value'] == $value));
			$optionTemplate->setVar('disabled', $this->isDisabled($option['disabled']));

			$radio_options .= $optionTemplate->render() . "\n";
		}

		return $radio_options;
	}

	private function isRequired($required)
	{
		return $required == true ? 'required' : '';
	}

	private function isDisabled($disabled)
	{
		return $disabled == true ? 'disabled="disabled"' : '';
	}

	private function isSelected($selected)
	{
		return $selected == true ? 'selected="selected"' : '';
	}

	private function isChecked($checked)
	{
		return $checked == true ? 'checked="checked"' : '';
	}

	private function getFormTemplateURL($formname)
	{
		global $app;

		if (file_exists(PATH_MAIN . '/templates/' . $app->getOption('template') . '/form/' . $formname))
			return PATH_MAIN . '/templates/' . $app->getOption('template') . '/form/' . $formname;

		return PATH_MAIN . '/core/template/form/' . $formname;
	}
}
