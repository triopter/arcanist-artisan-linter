<?php

/**
 * Wraps Arcanist's bundled ArcanistPhpcsLinter class to use `artisan inspect:sniff`
 * command provided by BCA-Laravel-Inspect utility
 */
class ArcanistArtisanLinter extends ArcanistExternalLinter {
	private $reports;
	private $phpcs_linter;

	public function __construct() {
		$this->phpcs_linter = New ArcanistPhpcsLinter();
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
		return array('inspect:sniff', '--report=xml', '-n');
	}

	public function getInstallInstructions() {
		return 'By installing this package, you\'ve already installed all dependencies!';
	}

	public function getDefaultFlags() {
		return $this->getDeprecatedConfiguration('lint.phpcs_artisan.options', array());
	}

	public function getDefaultBinary() {
		$root = $this->getEngine()->getWorkingCopy()->getProjectRoot();
		$path = Filesystem::resolvePath('artisan', $root);

		return $this->getDeprecatedConfiguration('lint.phpcs_artisan.bin', $path);
	}

	public function getVersion() {
		list($stdout) = execx('%C --version', $this->getExecutableCommand());
	}

	public function shouldExpectCommandErrors() {
		return true;
	}

	public function supportsReadDataFromStdin() {
		return false;
	}

	protected function getPathArgumentForLinterFuture($path) {
		// Artisan expects a relative path and appends it to the project root path
		// Arcanist also prepends the project root path.
		// doing so twice breaks everything, so strip it from Arcanist's copy
		// before sending to Artisan
		$root = $this->getEngine()->getWorkingCopy()->getProjectRoot();
		$clean_path = str_replace($root . '/', '', $path);

		return csprintf('--path=%s', $clean_path);
	}

	protected function parseLinterOutput($path, $err, $stdout, $stderr) {
		// artisan outputs some junk before the PHPCS output
		// we need to strip that before passing the XML to PHPCS
		$xml_pos = strpos($stdout, '<?xml');
		$xml_end = strpos($stdout, '</phpcs>') + strlen('</phpcs>');
		$clean_stdout = substr($stdout, $xml_pos, $xml_end - $xml_pos);

		return $this->phpcs_linter->parseLinterOutput($path, $err, $clean_stdout, $stderr);
	}

	protected function getDefaultMessageSeverity($code) {
		return $this->phpcs_linter->getDefaultMessageSeverity($code);
	}

	protected function getLintCodeFromLinterConfigurationKey($code) {
		return $this->phpcs_linter->getLintCodeFromLinterConfigurationKey($code);
	}
}
