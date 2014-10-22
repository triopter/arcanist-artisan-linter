arcanist-artisan-linter
=======================

Integrates the `artisan inspect:sniff` command from BCA-Laravel-Inspect as a linter for Phabricator's Arcanist

Installation
------------

Add the following to your composer.json:

	{
	   "repositories": [
			{
				"url": "https://github.com/triopter/arcanist-artisan-linter",
				"type": "git"
			}
		],
		"require-dev": {
			"tidal/arcanist-artisan-linter": "@dev"
		}
	}

Download the library:

	`php composer.phar update`

Add the following to your .arcconfig to make the library available to Arcanist:

	{
		"load": [
			"relative_path_to_vendor_directory/tidal/arcanist-artisan-linter/src",
		]
	}

Configuration
-------------

Create a `.arclint` file in the root of your repository containing the following:

	{
		"linters": {
			"phpcs_artisan": {
				"type": "phpcs_artisan",
				"include": "/\\.php$/",
				"exclude": "/(^app\\/views\\/|\\.blade\\.php$)/"
			}
		}
	}


The "include" and "exclude" rules above are set to have the linter run against all PHP files except views and
anything with a ".blade.php" extension.  You can update them as needed.

You should now be able to edit a PHP file and run `arc lint` to lint it.  Note that `arc lint` will only lint
files that have changed since the last revision.  To lint your entire project, continue to use 
`artisan inspect:sniff`.
