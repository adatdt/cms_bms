<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__file__).'/tcpdf/tcpdf.php';
class download_salary extends TCPDF {

	protected $ci;

	public function __construct()
	{
		$this->ci =& get_instance();
	}

}