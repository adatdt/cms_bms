<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Monitoring extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_monitoring');
	}

	public function index()
	{
		$data['title'] = "Device Monitoring";
		$data['content'] = "monitoring/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_monitoring->getDetail();
		echo json_encode($list);
	}

}

/* End of file Monitoring.php */
/* Location: ./application/controllers/Monitoring.php */