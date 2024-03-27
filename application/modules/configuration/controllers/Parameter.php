<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parameter extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_parameter');
		$this->load->helper('inflector');
	}

	public function index()
	{
		$data['title'] = "Parameter";
		$data['content'] = "parameter/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add']     = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$data['config'] = $this->m_parameter->getData();
		$this->load->view('common/page',$data);
	}

	public function action_add()
	{
		foreach ($_POST as $key => $value) {
			$data[] = array(
				'name' => $key,
				'value' => $value,
				'updated_by' => $this->session->userdata('username'),
				'updated_on' => date('Y-m-d H:i:s')
			);
		}

		$updated = $this->m_parameter->action_add($data);
		if ($updated) {
			echo json_encode(array(
				'code' => 200,
				'message' => 'Data successfully edited',
			));
		}else{
			echo json_encode(array(
				'code' => 101,
				'message' => 'Please contact admin',
			));
		}
	}

}

/* End of file Parameter.php */
/* Location: ./application/controllers/Parameter.php */