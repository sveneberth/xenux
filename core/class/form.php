<?php
class form
{
	private $data;
	private $fields;
	private $class;
	private $action;
	private $method;
	private $error_msg = Array();
	private $requiredInfo = true;

	public function __construct(array $fields, $class=null, $action=null, $method="post")
	{
		$this->fields = $fields;
		$this->class  = $class;
		$this->action = $action;
		$this->method = strtolower($method);

		$this->data = $this->isSend() ? $this->getInput() : Array();
	}
	
	public function isSend()
	{
		return $_SERVER['REQUEST_METHOD'] == strtoupper($this->method);
	}

	public function getInput()
	{
		return $this->method == 'post' ? $_POST : ($this->method == 'get' ? $_GET : false);
	}

	public function getForm()
	{
		global $app;

		$formTemplate = new template(PATH_MAIN."/core/template/form/form.php");
		
		$formTemplate->setVar("class", $this->class);
		$formTemplate->setVar("action", $this->action);
		$formTemplate->setVar("method", $this->method);
		$formTemplate->setVar("fields", $this->getFormFields($this->fields));
		$formTemplate->setVar("error_msg", $this->GetErrorMsg());

		$formTemplate->setIfCondition("requiredInfo", $this->requiredInfo);

		return $formTemplate->render();
	}
		
	public function isValid()
	{
		foreach($this->fields as $name => $props)
		{
			if($props['type'] == 'html')
				continue;

			$this->fields[$name]['validInput'] = true;
			
			if(isset($props['required']) && $props['required'] == true && empty($this->data[$name]))
			{
				$this->setErrorMsg(__('please fill field', $props['label']));
				$this->setFieldInvalid($name);
			}
			
			if(!empty($this->data[$name]))
			{
				switch($props['type'])
				{
					case "text":
					case "textarea":
						// what can you do wrong here? ;)
						break;
					case "password":
						$min_length = isset($props['min_length']) ? $props['min_length'] : 6;
						if(strlen($this->data[$name]) < $min_length)
						{
							$this->setErrorMsg(__('password to short. minlength', $props['label'], $min_length));
							$this->setFieldInvalid($name);
						}
						break;
					case "email":
						if(filter_var($this->data[$name], FILTER_VALIDATE_EMAIL) == false)
						{
							$this->setErrorMsg(__('please fill in a valid eMail-adress', $props['label']));
							$this->setFieldInvalid($name);
						}
						break;
					case "number":
						if(!is_numeric($this->data[$name]))
						{
							$this->setErrorMsg(__('only number in field', $props['label']));
							$this->setFieldInvalid($name);
						}
						break;
					case "date":
						if(!preg_match("/[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/", $this->data[$name]))
						{
							$this->setErrorMsg(__('please fill in a valid date', $props['label']));
							$this->setFieldInvalid($name);							
						}
						break;
					case "time":
						if(!preg_match("/(2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9])?/", $this->data[$name]))
						{
							$this->setErrorMsg(__('please fill in a valid time', $props['label']));
							$this->setFieldInvalid($name);
							echo 'wring';
						}
						break;
				}
			}
		}

		if(!empty($this->error_msg))
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

		$messages = '';
		foreach($this->error_msg as $message)
		{
			$msgTemplate = new template(PATH_MAIN."/core/template/form/_form_error_msg.php");
			$msgTemplate->setVar("err_message", $message);
			$messages .= $msgTemplate->render();
		}

		$errTemplate = new template(PATH_MAIN."/core/template/form/_form_error.php");
		$errTemplate->setVar("error_messages", $messages);

		return $errTemplate->render();
	}
	
	private function getFormFields()
	{
		$form_fields	= "";

		foreach($this->fields as $name => $props)
		{
			$form_fields .= $this->getFormField($name, $props) . "\n";
			if(isset($props['info']) && !empty($props['info']))
				$form_fields .= '<span class="info">' . $props['info'] . '</span>' . "\n";
		}

		return $form_fields;
	}
	
	private function getFormField($fieldname, array $props)
	{
		global $app;

		$props['type']			= isset($props['type'])			? $props['type']		: 'text';
		$props['class']			= isset($props['class'])		? $props['class']		: '';
		$props['value']			= isset($props['value'])		? $props['value']		: '';
		$props['label']			= isset($props['label'])		? $props['label']		: '';
		$props['checked']		= isset($props['checked'])		? $props['checked']		: false;
		$props['required']		= isset($props['required'])		? $props['required']	: false;
		$props['wysiwyg']		= isset($props['wysiwyg'])		? $props['wysiwyg']		: false;
		$props['showLabel']		= isset($props['showLabel'])	? $props['showLabel']	: true;

		$props['label'] = (isset($props['label:before']) ? $props['label:before'].' ' : '') . $props['label'] . (isset($props['label:after']) ? ''.$props['label:after'] : '');
		
		$class = $props['class'] . (isset($props['validInput']) && $props['validInput'] == false ? ' wrong' : '');
		$value = isset($this->data[$fieldname]) ? $this->data[$fieldname] : $props['value'];

		$fieldTemplate = new template();
		
		$fieldTemplate->setVar("class",		$class);
		$fieldTemplate->setVar("label",		$props['label'] . ($this->isRequired($props['required'] && $this->requiredInfo == true) ? '*' : ''));
		$fieldTemplate->setVar("name",		$fieldname);
		$fieldTemplate->setVar("value",		$value);
		$fieldTemplate->setVar("required",	$this->isRequired($props['required']));

		$fieldTemplate->setIfCondition("showLabel",	$props['showLabel']);

		switch(strtolower($props['type']))
		{
			case 'text':
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_text.php");
				break;
			case 'password':
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_password.php");
				break;
			case 'email':
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_email.php");
				break;
			case 'textarea':
				if($props['wysiwyg'])
					$fieldTemplate->setVar("class", $class.' ckeditor');
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_textarea.php");
				break;
			case 'select':
				$props['options'] = isset($props['options']) ? $props['options'] : '';
				$fieldTemplate->setVar("options", $this->getSelectOptions($props['options'], $value));
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_select.php");
				break;
			case 'radio':
				$props['options'] = isset($props['options']) ? $props['options'] : '';
				$fieldTemplate->setVar("radios", $this->getRadioOptions($props['options'], $fieldname, $value, $class));
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_radio_fieldset.php");
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
				$fieldTemplate->setVar("checked", $this->isChecked($props['checked']));
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_checkbox.php");
				break;
			case 'number':
				$props['min_number'] = isset($props['min_number']) ? $props['min_number'] : '';
				$props['max_number'] = isset($props['max_number']) ? $props['max_number'] : '';
				$fieldTemplate->setVar("min_number", $props['min_number']);
				$fieldTemplate->setVar("max_number", $props['max_number']);		
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_number.php");
				break;
			case 'date':
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_date.php");
				break;
			case 'time':
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_time.php");
				break;
			case 'readonly':
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_text_readonly.php");
				break;
			case 'hidden':
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_hidden.php");
				break;
			case 'html':
				return $props['value'];
				break;
			case 'submit':
				return $fieldTemplate->render(PATH_MAIN."/core/template/form/_form_submit.php");
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

			$optionTemplate = new template(PATH_MAIN."/core/template/form/_form_select_option.php");
		
			$optionTemplate->setVar("value", $option['value']);
			$optionTemplate->setVar("label", $option['label']);
			$optionTemplate->setVar("selected", $this->isSelected($option['value'] == $value));
			$optionTemplate->setVar("disabled", $this->isDisabled($option['disabled']));

			$select_options .= $optionTemplate->render() . "\n";
		}

		return $select_options;
	}

	private function getRadioOptions(array $options, $fieldname, $value=null, $class=null)
	{
		global $app;

		$radio_options = '';

		foreach ($options as $option)
		{
			$option['disabled'] = isset($props['disabled']) ? $props['disabled'] : '';

			$optionTemplate = new template(PATH_MAIN."/core/template/form/_form_radio.php");
		
			$optionTemplate->setVar("value", $option['value']);
			$optionTemplate->setVar("name", $fieldname);
			$optionTemplate->setVar("label", $option['label']);
			$optionTemplate->setVar("class", $class);
			$optionTemplate->setVar("checked", $this->isChecked($option['value'] == $value));
			$optionTemplate->setVar("disabled", $this->isDisabled($option['disabled']));

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
}
?>