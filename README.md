Pipeline is a Wordpress plugin which allows you to create a dashboard for you gitHub project. The intended use case for this tool is to provide an intuitive interface for non-technical members of the team. It is not intended to be a SCRUM board, or a full-featured project management tool. The name Pipeline refers to the fact that we make it easy to "pipe in" information from gitHub. Which information you pipe in, and how you present it is entirely up to you. 

### Requirements:
* A gitHub repository
* A registered 3rd party application for your repository (if private)
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

`[gh_searchform labels="foo,bar" state="ALL|open|closed"]`

You can also use `[gh_searchform]` in combination with `[gh_issues]`. The issues will appear on the page until the user enters a search, then the page will display the results from the search.

## Tips
* Wordpress offers many ways to manage access/privacy. But If you are setting up a tool for internal use by your team only, a plugin like [Private Only](https://wordpress.org/plugins/private-only/) may come in handy.

## Acknowlegements.
This tool wraps the [PHP GitHub API](https://github.com/KnpLabs/php-github-api) by KNP Labs

## TODO:
* Make CSS include optional
* View issue comments
* Allow GitHub users to authenticate as themselves

## Backlog
* Add admin option to hide gitHub credentials
* Add ability to
* Allow users to make comments
