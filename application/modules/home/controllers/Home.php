<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
	}

	public function index()
	{
		$data['title'] = "Home";
		$data['content'] = "home";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view('common/page', $data);
	}

}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */