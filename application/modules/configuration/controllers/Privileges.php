<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Privileges extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_privileges');
	}

	public function index()
	{
		$data['title'] = "Privileges";
		$data['content'] = "privileges/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['group_name']     = $this->m_privileges->group_name();
		$this->load->view('common/page',$data);
	}

	public function get_privileges()
	{
		$id = $this->input->post('user_group_id');
		$privileges = $this->m_privileges->get_privileges($id);

		echo json_encode($privileges);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_user->get();
		echo json_encode($list);
	}

	public function getDetail()
	{
		validateAjax();
		echo json_encode($this->m_privileges->getDetail());
	}

	public function update ()
	{
		$privilege_id=decode($this->input->post('id'));

		$validate=$this->m_privileges->selectById("core.t_mtr_privilege", "id_seq=$privilege_id");

		if($validate->status==1)
		{
			$update=-5;
		}
		else
		{
			$update=1;
		}

		$data=array(
					'status'=>$update,
					'updated_on'=>date('Y-m-d H:i:s'),
					'updated_by'=>$this->session->userdata('username'),
					);
		$update=$this->m_privileges->update("core.t_mtr_privilege",$data,"id_seq=$privilege_id");
	}

	public function insert()
	{
		$menu_detail_id=decode($this->input->post('menu_detail_id'));
		$user_group_id=decode($this->input->post('user_group_id'));
		$menu_id=decode($this->input->post('menu_id'));

		$data=array('user_group_id'=>$user_group_id,
					'menu_id'=>$menu_id,
					'menu_detail_id'=>$menu_detail_id,
					'status'=>1,
					'created_by'=>$this->session->userdata('username'),
					'created_on'=>date('Y-m-d H:i:s'),
					);

		$this->m_privileges->insert('core.t_mtr_privilege',$data);
	}
}

/* End of file Privileges.php */
/* Location: ./application/controllers/Privileges.php */