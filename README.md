Pipeline is a Wordpress plugin which allows you to create a dashboard for you gitHub project. The intended use case for this tool is to provide an intuitive interface for non-technical members of the team. It is not intended to be a SCRUM board, or a full-featured project management tool. The name Pipeline refers to the fact that we make it easy to "pipe in" information from gitHub. Which information you pipe in, and how you present it is entirely up to you. 

### Requirements:
* A gitHub repository
* A registered 3rd party application for your repository (if private)
* A working Wordpress installation (docs [here](https://codex.wordpress.org/Installing_WordPress))
* PHP 5.5+

##Installation

Install the plugin on a Wordpress site using any of the following methods.
    
* Use the Wordpress plugin interface to upload the compiled plugin (locatedin the `dist/` directory).   
* [COMING SOON:] Use the Wordpress plugin interface to search for the plugin in the plugin library.
* Fork and/or Clone the repo from here and to run `composer install` to get the dependencies. This is ideal if you plan to contribute.

## Quick Start
1. Activate the plugin
2. [Register an application](https://github.com/settings/applications/new) in the settings page for your GitHub repository.
3. Add your gitHub repository info to the settings page under Settings > gitHub. You will need the Client ID and Client Secret from the application you registered in the previous step.
4. After providing the required credentials you will see the button to "Authorize Github." Click it.
5. Add shortcodes to pages to start presenting gitHub information

Note: If the repository is public you only need to enter the repository settings. 

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
