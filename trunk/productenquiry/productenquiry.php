<?php

class productEnquiry extends Module
{
 	function __construct()
 	{
 	 	$this->name = 'productenquiry';
 	 	$this->version = '1.1';
 	 	$this->tab = 'Products';
		
		parent::__construct();
		
		$this->displayName = $this->l('Product Enquiry module');
		$this->description = $this->l('Allows customers to enquire about a product');
 	}

	function install()
	{
	 	if (!parent::install())
	 		return false;
	 	return $this->registerHook('extraLeft');
	}
	
	function hookExtraLeft($params)
	{
		global $smarty;
		$smarty->assign('this_path', $this->_path);
		return $this->display(__FILE__, 'product_page.tpl');
	}

	public function displayFrontForm()
	{
		global $smarty;
		$error = false;
		$confirm = false;
		
		if (isset($_POST['submitAddtoafriend']))
		{
			global $cookie, $link;
			/* Product informations */
			$product = new Product(intval(Tools::getValue('id_product')), false, intval($cookie->id_lang));
			$productLink = $link->getProductLink($product);
			
			/*
				Form Details
			*/
			$form_details	=	array(
									'visitorname'		=>	$_POST['visitorname'] ? $_POST['visitorname'] : "",
									'visitoremail'		=>	$_POST['visitoremail'] ? $_POST['visitoremail'] : "",
									'visitormobile'		=>	$_POST['visitorphone'] ? $_POST['visitorphone'] : "",
									'visitorcountry'	=>	$_POST['id_country'] ? $_POST['id_country'] : "",
									'visitorstate'		=>	$_POST['id_state'] ? $_POST['id_state'] : "",
								);
			
			/* Fields verifications */
			if (empty($_POST['enquiry']) OR empty($_POST['enquiry'])){
				$error = $this->l('You must enter some enquiry.');
			}elseif (empty($_POST['email']) OR empty($_POST['name']) OR empty($_POST['visitorname']) OR empty($_POST['visitoremail']) OR empty($_POST['visitorphone']) OR empty($_POST['id_country'])){
				$error = $this->l('You must fill all fields.');
			}elseif (!Validate::isEmail($_POST['visitoremail'])){
				$error = $this->l('Your email is invalid.');
			}elseif (!Validate::isName($_POST['visitorname'])){
				$error = $this->l('Your name is invalid.');
			}elseif (!Validate::isPhoneNumber($_POST['visitorphone'])){
				$error = $this->l('Your phone number is invalid.');
			}elseif (!isset($_GET['id_product']) OR !is_numeric($_GET['id_product'])){
				$error = $this->l('An error occurred during the process.');
			}else
			{
				$countries = Country::getCountries(intval($cookie->id_lang), true);
				$finalstate	=	'';
				if(isset($countries[$_POST['id_country']]['country'])){
					$states	=	$countries[$_POST['id_country']]['states'];
					foreach($states as $ind => $statevalue){
						
						if($statevalue['id_state']	==	$_POST['id_state']){
							$finalstate	=	$statevalue['name'];
						}
					}
				}
				
				/* Email generation */
				$subject = ($_POST['visitorname']).' '.$this->l('enquired about the product').' '.$product->name;
				$templateVars = array(
					'{product}' => $product->name,
					'{product_link}' => $productLink,
					'{customer}' => $_POST['visitorname'],
					'{customeremail}' => $_POST['visitoremail'],
					'{customerphone}' => $_POST['visitorphone'],
					'{customercountry}' => isset($countries[$_POST['id_country']]['country']) ? $countries[$_POST['id_country']]['country'] : "",
					'{customerstate}' => $finalstate,
					'{name}' => Tools::safeOutput($_POST['name']),
					'{enquiry}' => Tools::safeOutput($_POST['enquiry']),
				);
				
				
				/* Email sending */
				if (!Mail::Send(intval($cookie->id_lang), 'product_enquiry', $subject, $templateVars, $_POST['email'], NULL, ($_POST['visitoremail']), ($_POST['visitorname']), NULL, NULL, dirname(__FILE__).'/mails/'))
					$error = $this->l('An error occurred during the process.');
				else
					$confirm = $this->l('An email has been sent successfully to').' '.Tools::safeOutput($_POST['email']).'.';
			}
		}
		else
		{
			global $cookie, $link;
			
			$customer 	=	new Customer(intval($cookie->id_customer));
			$address	=	new Address(intval($cookie->id_address_delivery));
			
			
			
			/* Product informations */
			$product = new Product(intval(Tools::getValue('id_product')), false, intval($cookie->id_lang));
			$productLink = $link->getProductLink($product);
			
			/*
				Form Details
			*/
			$form_details	=	array(
									'visitorname'		=>	$customer->firstname ? $customer->firstname.' '.$customer->lastname : "",
									'visitoremail'		=>	$customer->email ? $customer->email : "",
									'visitormobile'		=>	$address->phone_mobile ? $address->phone_mobile : "",
									'visitorcountry'	=>	$address->id_country ? $address->id_country : "",
									'visitorstate'		=>	$address->id_state ? $address->id_state : "",
								);
		}
		
		/*
			Get country
		*/
		if (isset($_POST['id_country']) AND !empty($_POST['id_country']) AND is_numeric($_POST['id_country']))
			$selectedCountry = intval($_POST['id_country']);
		elseif (isset($address) AND isset($address->id_country) AND !empty($address->id_country) AND is_numeric($address->id_country))
			$selectedCountry = intval($address->id_country);
		elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$array = preg_split('/,|-/', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			if (!Validate::isLanguageIsoCode($array[0]) OR !($selectedCountry = Country::getByIso($array[0])))
				$selectedCountry = intval(Configuration::get('PS_COUNTRY_DEFAULT'));
		}
		else
			$selectedCountry = intval(Configuration::get('PS_COUNTRY_DEFAULT'));
			
		$countries = Country::getCountries(intval($cookie->id_lang), true);
		$countriesList = '';
		foreach ($countries AS $country)
			$countriesList .= '<option value="'.intval($country['id_country']).'" '.($country['id_country'] == $selectedCountry ? 'selected="selected"' : '').'>'.htmlentities($country['name'], ENT_COMPAT, 'UTF-8').'</option>';
		
		/*
			Customer Info.
		*/
		$visitorname	=	$form_details['visitorname'] ? $form_details['visitorname'] : "";
		$visitoremail	=	$form_details['visitoremail'] ? $form_details['visitoremail'] : "";
		$visitormobile	=	$form_details['visitormobile'] ? $form_details['visitormobile'] : "";
		$visitorcountry	=	$selectedCountry ? $selectedCountry : "";
		$visitorstate	=	$form_details['visitorstate'] ? $form_details['visitorstate'] : "";
		
		$visitor	=	array(
							'fullname'	=>	$visitorname,
							'email'		=>	$visitoremail,
							'mobile'	=>	$visitormobile,
							'country'	=>	$visitorcountry,
							'state'		=>	$visitorstate
						);
		/* Image */
		$images = $product->getImages(intval($cookie->id_lang));
		foreach ($images AS $k => $image)
			if ($image['cover'])
			{
				$cover['id_image'] = intval($product->id).'-'.intval($image['id_image']);
				$cover['legend'] = $image['legend'];
			}
		
		if (!isset($cover))
			$cover = array('id_image' => Language::getIsoById(intval($cookie->id_lang)).'-default', 'legend' => 'No picture');
		
		//CSS ans JS file calls
		$js_files = array(
			_THEME_JS_DIR_.'tools/statesManagement.js'
		);
		
		$smarty->assign(array(
			'cover' => $cover,
			'errors' => $error,
			'confirm' => $confirm,
			'product' => $product,
			'productLink' => $productLink,
			'visitor' => $visitor,
			'countries_list'	=> $countriesList,
			'countries'	=>	$countries,
			'js_files'	=> $js_files
		));
		
		return $this->display(__FILE__, 'productenquiry.tpl');
	}
}
