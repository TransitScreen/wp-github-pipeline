When you're ready to release a new version of the Plugin:

## 1 Update the version number 

* Change in the header comment of the main PHP file to the new version number (in the trunk folder).
* Change the version number in the 'Stable tag' field of the readme.txt file (in the trunk folder).
* Add a new sub-section in the 'changelog' section of the readme.txt file, briefly describing what changed compared to the last release.

## 2 Commit, tag, and push changes to GitHub repo

## 3 Update SVN

* Copy files to SVN trunk/ 
* Commit them with svn: `svn ci -m 'version 1.1 changes'`

## 4 Tag the new version with svn

<code>
# You've just checked in the latest and greatest updates
# to your plugin, let's tag it with a version number, 2.0.
# To do that, copy the files from your trunk/ directory to
# a new directory in tags/.
# Make sure to use `svn cp` instead of the regular `cp`.

my-local-dir/$ svn cp trunk tags/2.0
> A tags/2.0

# Now, as always, check in the changes.

my-local-dir/$ svn ci -m "tagging version 2.0"
> Adding         tags/2.0
> Adding         tags/2.0/my-plugin.php
> Adding         tags/2.0/readme.txt
> Committed revision 11328.
</code>


https://wordpress.org/plugins/about/svn/#task-3