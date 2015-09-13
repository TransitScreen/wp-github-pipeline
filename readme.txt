=== GitHub Pipeline ===
Contributors: TransitScreen
Tags: GitHub, Git, dashboard, Waffle
Requires at least: 4.2
Tested up to: 4.3
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create a custom dashboard for your GitHub project. Show or search for issues, labels, or milestones.

== Description ==

<img src="https://circleci.com/gh/TransitScreen/wp-github-pipeline.svg?style=shield&circle-token=:circle-token" />

Pipeline is a Wordpress plugin which allows you to create a dashboard for your gitHub project. The intended use case for this tool is to provide an intuitive interface for non-technical members of the team. It is not intended to be a SCRUM board, or a full-featured project management tool. The name Pipeline refers to the fact that we make it easy to "pipe in" information from gitHub. Which information you pipe in, and how you present it, is entirely up to you. 

### Requirements:
* A GitHub repository (private or public) 
* A working Wordpress installation (docs [here](https://codex.wordpress.org/Installing_WordPress)) using PHP 5.5+

### Quick Start
1. Activate the plugin
2. Add your GitHub repository information to the plugin settings page under Settings > gitHub. 
3. If the GitHub repository is private,
  1. In the repository settings page, [Register an application](https://github.com/settings/applications/new)
  2. Copy the Client ID and Client Secret into the plugin settings page. Click the button to "Authorize Github."
4. Add shortcodes to pages to start presenting gitHub information

### Shortcodes

`[gh_issues labels="foo,bar" state="all|OPEN|closed" per_page="NULL|#" show_body="FALSE|true|toggle" ]`

`[gh_milestones state="OPEN|closed|all"] sort="DUE_ON|completeness" direction="ASC|desc"`

`[gh_searchform labels="foo,bar" state="ALL|open|closed"]`

NOTE: The GitHub API only allows 10 unauthenticated requests pre minute to the search API. If you register an app (required for private repositories) the limit is increased to 30 requests per minute.

You can also use `[gh_searchform]` in combination with `[gh_issues]`. The issues will appear on the page until the user enters a search, then the page will display the results from the search.

### Pagination

By default, issues will not be paginated and all results will appear on a single screen. This can be cumbersome if there are a lot of screens. The optional `per_page=n` attribute will cause the results to be paginated into groups of `n` issues. The maximum size page length is 50 (this is a quirk of the GitHub API).

### Tips
* Wordpress offers many ways to manage access/privacy. But If you are setting up a tool for internal use by your team only, a plugin like [Private Only](https://wordpress.org/plugins/private-only/) may come in handy.

### Acknowlegements.
This tool wraps the lovely [PHP GitHub API](https://github.com/KnpLabs/php-github-api) by KNP Labs

== Installation ==

1. Install and activate the plugin
2. Add your GitHub repository information to the plugin settings page under Settings > gitHub. 
3. If the GitHub repository is private,
  1. In the repository settings page, [Register an application](https://github.com/settings/applications/new)
  2. Copy the Client ID and Client Secret into the plugin settings page. Click the button to "Authorize Github."
4. Add shortcodes to pages to start presenting gitHub information
