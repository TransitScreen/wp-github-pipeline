<?php

/* Github class wraps the knplabs/github-api library */

class WPGHPL_Github {

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
	protected $token = NULL;
	public $prefix = 'wpghpl_';

	public function __construct() {

		$this->repo = get_option($this->prefix . 'gh_repo');

		$this->org = get_option($this->prefix . 'gh_org');

		$this->single_user_mode = ( get_option($this->prefix . 'auth_single_user') ) ? TRUE : FALSE;

		$this->token = get_option($this->prefix . 'token');

		$this->client = new \Github\Client();

		$this->check_for_settings();

		$this->missing_settings_msg = "<h2>Missing GitHub settings!</h2><p>Update the <a href='".admin_url('options-general.php?page='.( substr($this->prefix, 0, -1) ) )."'>settings page</a></p>";
		
		if ($this->token)
			$auth = $this->client->authenticate( $this->token, NULL, Github\Client::AUTH_HTTP_TOKEN);
		
		//TODO: This should be configurable
		$this->per_page = 50;

		#set up for paginated results
		if ( !empty($_GET['gh_page'] ) )
			$this->page = $_GET['gh_page'];

	}

	public function get_issues($options=array()) {

		$options = (empty($options)) ? array() : $options; #if zero attributes passed, $options = ''
		
		$default_options = array(
							'labels'=>'', 
							'state'=>'open',
							'page' => $this->page,
							'per_page' => NULL,
							'show_body' => FALSE
							);

		$params = array_merge($default_options, $options);

		$paginator = new Github\ResultPager($this->client);

		try {
			if ( !empty($params['per_page']) ) {
				$issues = $paginator->fetch($this->client->api('issue'), 'all', array($this->org, $this->repo, $params));
			} else {
				$issues = $paginator->fetchAll($this->client->api('issue'), 'all', array($this->org, $this->repo, $params));
				$issues = apply_filters('github_issues_list', $issues);
			}
		} catch (Exception $e) {
			echo "Error! " . $e->getMessage();
			return;
		}
		
		$this->has_next_page = $paginator->hasNext();
		$this->has_prev_page = $paginator->hasPrevious();
		
		return $issues;
	}

	public function search_issues($options=array()) {

		$options = (empty($options)) ? array() : $options; #if zero attributes passed, $options = ''

		$default_options = array(
							'type' => NULL, # issue | pr
							'in' => NULL, # which fields are searched: title|body|comment (or combo)
							'labels'=>NULL,
							'term' => NULL, 
							'state'=>'all',
							'show_body' => FALSE
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

		$options = (empty($options)) ? array() : $options; #if zero attributes passed, $options = ''

		$default_options = array(
							'state'=>'open',
							'direction' => 'asc',
							'page' => 1,
							'sort' => 'due_on',
							'direction' => 'asc'
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

	/**
	 * Check for the mandatory settings.
	 * If token is missing, check if repo is public and then save
	 * @return bool
	 */
	public function check_for_settings($error=TRUE){

		if (	
				$this->repo && 
				$this->org && 
				// $this->gh_username && 
				// $this->gh_password
				$this->token
			) {

			$this->has_settings = TRUE;

		} elseif (
				$this->repo &&
				$this->org &&
				get_option($this->prefix . 'repo_is_public')
			) {

			$this->has_settings = TRUE;

		} 

		return $this->has_settings;
	}

}