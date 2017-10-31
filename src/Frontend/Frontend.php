<?php namespace TestingPlugin\Frontend;

use TestingPlugin\FileManager;

/**
 * Class Frontend
 *
 * @package TestingPlugin\Frontend
 */
class Frontend {


	/**
	 * @var FileManager
	 */
	private $fileManager;

	public function __construct( FileManager $fileManager ) {
		$this->fileManager = $fileManager;
	}

}