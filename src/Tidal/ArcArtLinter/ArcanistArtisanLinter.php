<?php

/**
 * Wraps Arcanist's bundled ArcanistPhpcsLinter class to use `artisan inspect:sniff`
 * command provided by BCA-Laravel-Inspect utility
 */
class ArcanistArtisanLinter extends ArcanistExternalLinter {
	private $reports;
	private $phpcs_linter;

	public function __construct() {
		$self->phpcs_linter = New ArcanistPhpcsLinter();
	}

	public function getInfoName() {
		return 'PHP_CodeSniffer_via_Artisan';
	}

	public function getInfoURI() {
		return 'https://github.com/brodkinca/BCA-Laravel-Inspect';
	}

	public function getInfoDescription() {
		return pht(
			'PHP_CodeSniffer tokenizes PHP, JavaScript and CSS files and '.
			'detects violations of a defined set of coding standards. '.
			'This variant wraps a Laravel Artisan integration of PHPCS.');
	}

	public function getLinterName() {
		return 'PHPCS_Artisan';
	}

	public function getLinterConfigurationName() {
		return 'phpcs_artisan';
	}

	public function getMandatoryFlags() {
		return array('--report=xml');
	}

	public function getInstallInstructions() {
		return 'By installing this package, you\'ve already installed all dependencies!';
	}

	public function getDefaultFlags() {
		return $this->getDeprecatedConfiguration('lint.phpcs_artisan.options', array());
	}

	public function getDefaultBinary() {
		return $this->getDeprecatedConfiguration('lint.phpcs_artisan.bin', 'artisan inspect:sniff');
	}

	public function getVersion() {
		list($stdout) = execx('%C --version', $this->getExecutableCommand());
	}

	public function shouldExpectCommandErrors() {
		return true;
	}

	public function supportsReadDataFromStdin() {
		return true;
	}

	protected function parseLinterOutput($path, $err, $stdout, $stderr) {
		return $self->phpcs_linter->parseLinterOutput($path, $err, $stdout, $stderr);
	}

	protected function getDefaultMessageSeverity($code) {
		return $self->phpcs_linter->getDefaultMessageSeverity($code);
	}

	protected function getLintCodeFromLinterConfigurationKey($code) {
		return $self->phpcs_linter->getLintCodeFromLinterConfigurationKey($code);
	}
}
