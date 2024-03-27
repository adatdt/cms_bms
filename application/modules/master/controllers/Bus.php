<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bus extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_register');
		$this->load->model('m_bus');
		$this->load->library('Restcurl');
		$this->_userGroup = $this->session->userdata('user_group_id');
	}

	public function index()
	{
		$data['title'] = "List Bus";
		$data['content'] = "bus/index";
		$data['add']=$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$data['menu'] = $this->m_global->getMenu($this->_userGroup);
		$data['add']     = $this->m_global->menuAccess($this->_userGroup,$this->uri->uri_string(),'add');

		$this->load->view('common/page',$data);
	}

	public function listDetail()
	{
		validateAjax();
		$id=$this->input->post('id');
		$data=$this->m_customer->getDetail($id);

		echo json_encode($data);

	}

	public function list()
	{
		validateAjax();
		$bus = $this->m_bus->get();
		echo json_encode($bus);
	}


}

/* End of file Customer.php */
/* Location: ./application/controllers/Register.php */