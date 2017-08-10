# MisysNews #
Misys News is a Wordpress plugin displaying external, corporate & colleagues' news on Misys' Cafeterias' TVs.

## Installation ##
* Install an EasyPhp instance on your local machine, configured to be accessed from internet (Listen *:80, and Allow from all for localweb VH)
* Install the last version of Wordpress in the localweb/ directory, configured to be accessed from internet (use DNS or IP for WP's URL)
* Checkout this repository inside wp-content/ of your Wordpress installation, copy/paste everything under created misysnews/ in wp-content/.
* Install Node.js (with npm) and, in wp-content/,
* for windows run:
* * misysnews.bat // to get the parameter information
* * misysnews.bat -s // to setup and compile th project
* * misysnews.bat -e // to export plugin and theme
* * misysnews.bat -a // to setup and export the plugin and theme
* * misysnews.bat -e plugin // to export plugin
* * misysnews.bat -e theme // to export theme
* for unix system run:
* * sh misysnews.sh // to get the parameter information
* * sh misysnews.sh -s // to setup and compile th project
* * sh misysnews.sh -e // to export plugin and theme
* * sh misysnews.sh -a // to setup and export the plugin and theme
* * sh misysnews.sh -e plugin // to export plugin
* * sh misysnews.sh -e theme // to export theme
* The plugin and theme should be stored in the folder zip after exporting
* Activate both MisysNews plugin and theme in your WP admin panel.
* Create a WP page with 'misysnews' as slug and choose "Misys News" as its template.
* Configure your WP to display this page as home page.
* Copy the content of /wordpress/plugins/misysnews/feeds.json into the MisysNews WP Admin settings page.
* Launch your browser on your wordpress' home.
