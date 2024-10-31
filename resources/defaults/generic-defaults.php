<?php

$this->seo_defaults = array(
	'title'       => '%address%, %city%, %state% %zip%',
	'keywords'    => '%address% %city% %state% %zip%, MLS# %mls_id%',
	'description' => 'Listing information for %address% %city%, %state%, %zip% MLS# %mls_id%'
);

$this->popup_defaults = array(
			'title' => 'Some of the best real estate is sold before it ever hits the MLS',
			'num'   => '3',
			'sub'   => 'Let one of our agents contact you and get in on the best deals as they become available:',
			'btn'   => 'Submit',
			'css'   => '* {
	font-family: arial;
}
h1 {
	color: red;
	text-align: center;
}
h3 {
	color: #122358;
	text-align: center;
}
table {
	margin: auto;
}
td {
	text-align: center;
	font-weight: bold;
}
input[type=text] {
	padding: 10px;
	width: 300px;
	font-size: 18px;
	text-align: center;
}

#sr-button {
	height: 58px;
	margin: auto;
	display: table;
	margin-top:20px;
}
#sr-button div {
	float: left;
	height: 100%;
}
#sr-button-center input {
	font-weight: bold;
	cursor: pointer;
	font-size: 18px;
	padding:10px;
}',
	'success' => 'One of our agents will contact you shortly.',
	'error' => 'Please fill out form.',
	'email' => get_bloginfo('admin_email')
);

$this->text_defaults = array(
	'login'  => 'After logging in you can view your favorite properties and favorite searches. If you have not already signed up, <a href="<?php echo get_bloginfo(\'url\')?>/sr-signup">click here</a>.',
	'signup' => 'Use the form below to sign up for an account.',
	'forgot' => 'Enter the email address used when you registered your account. We will send you an email with a link to reset your password.'
);

$this->template_settings = get_option("sr_template");

if ( !$this->template_settings || is_string($this->template_settings) ) {
	$tmp = array(
		"type"      => "all",
		"all-value" => (string)$this->template_settings,
		"every-values" => array(
			"User Favorites"  => '',
			"Listing Details" => '',
			"Forgot Password" => '',
			"User Login"      => '',
			"Password Reset"  => '',
			"User Signup"     => '',
			"Search"          => '',
			"Email Subscribe" => '',
			"Verify User"     => ''
		)
	);
	
	$this->template_settings = $tmp;
	
	update_option("sr_template", $tmp);
}



$this->state_change = array(
  'AL' => 'Alabama',
  'AK' => 'Alaska',
  'AZ' => 'Arizona',
  'AR' => 'Arkansas',
  'CA' => 'California',
  'CO' => 'Colorado',
  'CT' => 'Connecticut',
  'DE' => 'Delaware',
  'DC' => 'District Of Columbia',
  'FL' => 'Florida',
  'GA' => 'Georgia',
  'HI' => 'Hawaii',
  'ID' => 'Idaho',
  'IL' => 'Illinois',
  'IN' => 'Indiana',
  'IA' => 'Iowa',
  'KS' => 'Kansas',
  'KY' => 'Kentucky',
  'LA' => 'Louisiana',
  'ME' => 'Maine',
  'MD' => 'Maryland',
  'MA' => 'Massachusetts',
  'MI' => 'Michigan',
  'MN' => 'Minnesota',
  'MS' => 'Mississippi',
  'MO' => 'Missouri',
  'MT' => 'Montana',
  'NE' => 'Nebraska',
  'NV' => 'Nevada',
  'NH' => 'New Hampshire',
  'NJ' => 'New Jersey',
  'NM' => 'New Mexico',
  'NY' => 'New York',
  'NC' => 'North Carolina',
  'ND' => 'North Dakota',
  'OH' => 'Ohio',
  'OK' => 'Oklahoma',
  'OR' => 'Oregon',
  'PA' => 'Pennsylvania',
  'RI' => 'Rhode Island',
  'SC' => 'South Carolina',
  'SD' => 'South Dakota',
  'TN' => 'Tennessee',
  'TX' => 'Texas',
  'UT' => 'Utah',
  'VT' => 'Vermont',
  'VA' => 'Virginia',
  'WA' => 'Washington',
  'WV' => 'West Virginia',
  'WI' => 'Wisconsin',
  'WY' => 'Wyoming'
);