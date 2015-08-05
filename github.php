<?php

// use Github\ResultsPager;

class Github {

	protected $client;
	protected $org;
	protected $repo;
	public $single_user_mode;
	private $gh_username;
	private $gh_password;
	public $labels;
	public $has_settings = FALSE;
	public $missing_settings_msg;
	public $page = 1;
	public $has_next_page = NULL;
	public $has_prev_page = NULL;
	public $per_page = NULL;

	public function __construct() {

		$this->repo = get_option('wpghdash_gh_repo');
		$this->org = get_option('wpghdash_gh_org');

		$this->single_user_mode = ( get_option('wpghdash_auth_single_user') ) ? TRUE : FALSE;

		#set credentials
		if ( $this->single_user_mode ) {
			$this->gh_username = get_option('wpghdash_client_id');
			$this->gh_password = get_option('wpghdash_client_secret');
		} else {
	    	
	    	$current_user = wp_get_current_user();
     		if ( !($current_user instanceof WP_User) ) #safety
				return;

			$this->gh_username = get_user_meta($current_user->ID, 'wpghdash_gh_username', TRUE);
			$this->gh_password = get_user_meta($current_user->ID, 'wpghdash_gh_pwd', TRUE);
		}

		$this->check_for_settings();

		$this->client = new \Github\Client();
		
		$auth = $this->client->authenticate( $this->gh_username, $this->gh_password, Github\Client::AUTH_HTTP_PASSWORD);
		
		//TODO: This should be dynamic
		$this->per_page = 50;

		#set up for paginated results
		if ( !empty($_GET['gh_page'] ) )
			$this->page = $_GET['gh_page'];



	}

	public function get_issues($options=array()) {

		$default_options = array(
							'labels'=>'', 
							'state'=>'all',
							'page' => $this->page
							);

		$params = array_merge($default_options, $options);

		$paginator = new Github\ResultPager($this->client);

		try {
			$issues = $paginator->fetch($this->client->api('issue'), 'all', array($this->org, $this->repo, $params));
		} catch (Exception $e) {
			echo "Error! " . $e->getMessage();
			return;
		}
		
		$this->has_next_page = $paginator->hasNext();
		$this->has_prev_page = $paginator->hasPrevious();
		
		return $issues;
	}

	public function search_issues($options=array()) {

		$default_options = array(
							'type' => NULL, # issue | pr
							'in' => NULL, # which fields are searched: title|body|comment (or combo)
							'labels'=>NULL,
							'term' => NULL, 
							'state'=>'all',
							// 'page' => $this->page
							);
		
		$params = array_merge($default_options, $options);
		$state = $params['state'];
		$term = $params['term'];
				
		#add label filter if present
		if ($params['labels']){
			$labels = explode(",", $params['labels']);
			foreach ($labels as $label){
				$term .= " label:{$label}";
			}
		}

		#limit results to this repo
		$term .= " user:{$this->repo}";


		$paginator = new Github\ResultPager($this->client);
		
		try {
			$issues = $paginator->fetchAll( $this->client->api('search'), 'issues', array($term)); #can take two more params sort/order
		} catch (Exception $e) {
			echo "Error! " . $e->getMessage();
			return;
		}

		return $issues;
	}

	public function get_milestones($options=array()) {

		$default_options = array(
							'state'=>'all',
							'page' => 1
							);

		$params = array_merge($default_options, $options );

		$paginator = new Github\ResultPager($this->client);

		//TODO: Add page links instead of full list?
		try {
			$milestones = $paginator->fetchAll($this->client->api('issue')->milestones(), 'all', array($this->org, $this->repo, $params));
		} catch (Exception $e) {
			echo "Error! " . $e->getMessage();
			return;
		}

		// $milestones = (!empty($milestones)) ? $milestones : array();
		foreach ($milestones as &$milestone) {
			$issues = $this->_get_milestone_info($milestone['number']);
			$milestone['issues'] = $issues;
		}

		return $milestones;
	}

	private function _get_milestone_info($number) {

		$paginator = new Github\ResultPager($this->client);
		$issues = $paginator->fetchAll($this->client->api('issue'), 'all', array($this->org, $this->repo, array('milestone'=>$number, 'state'=>'all')));

		return $issues;
	}

	public function check_for_settings($error=TRUE){
		if (	$this->repo && 
				$this->org && 
				$this->gh_username && 
				$this->gh_password
			)
			$this->has_settings = TRUE;
			
			$msg = "<h2>Missing GitHub settings!</h2>";
			if ($this->single_user_mode) {
				$msg .= "<p>Update the <a href='".admin_url('options-general.php?page=wpghdash')."'>settings page</a></p>";
			} else {
				$msg .= "<p>Update the <a href='".admin_url('options-general.php?page=wpghdash')."'>settings page</a> and/or the GitHub credentials on <a href='".get_edit_user_link()."'>your profile page</a></p>";
			}
			$this->missing_settings_msg = $msg;

		return $this->has_settings;
	}

}