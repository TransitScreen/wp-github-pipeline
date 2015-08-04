Pipeline is a Wordpress plugin which allows you to create a dashboard for you gitHub project. The intended use case for this tool is to provide an intuitive interface for non-technical members of the team. It is not intended to be a SCRUM board, or a full-featured project management tool. The name Pipeline refers to the fact that we make it easy to "pipe in" information from gitHub. Which information, and how you present it is up to you. 

The goals was to be as versatile as possible while still being quick and easy to install and set up. 

### Requirements:
* A gitHub repository
* A working Wordpress installation (docs [here](https://codex.wordpress.org/Installing_WordPress))
* PHP 5.5+

##Quick Start
1. Install and activate the plugin into a Wordpress site. If you download it from here you'll need to run `composer install` inside the plugin.
2. Add your gitHub repository info to the settings page under Settings > gitHub
3. COMING SOON: Update your user(s) gitHub credentials
4. Add shortcodes to pages to start presenting gitHub information

###Shortcodes

`[gh_issues labels="foo,bar,NULL" state="ALL|open|closed" show_body="FALSE|true|toggle" ]`

`[gh_milestones state="open|closed|all"]`

`[gh_searchform]`

You can also use `[gh_searchform]` in combination with `[gh_issues]`. The issues will appear on the page until the user enters a search, then the page will display the results from the search.

## Acknowlegements.
This tool wraps the [PHP GitHub API](https://github.com/KnpLabs/php-github-api) by KNP Labs

## TODO:
* Paginate results
* Update search to use new gitHub search endpoint
* Add assigned to search results?
* Remove 10 result limit on search
* View issue comments

## Backlog
* Make CSS inclusion optional
* Add admin option to hide gitHub credentials
* Add ability to
* Allow users to make comments
