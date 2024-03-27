<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_action extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_menu_action');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Menu Action";
		$data['content'] = "menu_action/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add']     = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add Menu Action";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("menu_action/add",$data);
	}

	public function edit($id)
	{
		validateAjax();
		$id = decode($id);
		$data['title'] = "Edit Menu Action";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['action_name'] = $this->m_menu_action->getname($id);
		$data['id'] = encode($id);
		$this->load->view("menu_action/edit",$data);
	}

	public function action_add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/menu_action','add');
		$name = trim($this->input->post('name'));

		// $check = $this->m_menu_action->check_menu($name);

		// pengecekjan nama yang sama dengan status yang aktif
		$check= $this->m_global->getDataById("core.t_mtr_menu_action","upper(name)=upper('".$name."') and status=1")->num_rows();

			$data = array(
				'name' =>strtolower($name), // confert ke huruf kecil pada saat add
				'status' => 1,
				'created_by' => $this->session->userdata('username'),
			);

		$this->form_validation->set_rules('name', 'Name', 'required');

		if($this->form_validation->run()==false)
		{
			echo $res=$this->msg_error("Please input the field!");
		}
		else if ($check>0) 
		{
			echo $res=$this->msg_error("Menu action already in use");
		}
		else
		{
			$insert_menu_action = $this->m_menu_action->insert($data);

			if ($insert_menu_action)
			{
				echo $res=$this->msg_success("Success add data");
			}
			else
			{
				echo $res=$this->msg_error("Failed add data");
			}
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'configuration/menu_action/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function action_edit()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/menu_action','edit');
		$id = decode($this->input->post('id'));
		$name = trim($this->input->post('name'));

		$data = array(
			'name' => $name,
			'updated_by' => $this->session->userdata('username'),
			'updated_on' => date("Y-m-d H:i:s"),
		);
		// $check = $this->m_menu_action->check_menu($name);

		// pengecekan nama yang sama dengan status yang aktif
		$check= $this->m_global->getDataById("core.t_mtr_menu_action","upper(name)=upper('".$name."') and id_seq!=$id and status=1")->num_rows();

		//pengecekan data apakah ini sudah pernah ada di menu detail
		$checkMenuDetail=$this->m_global->getDataById("core.t_mtr_menu_detail","action_id=".$id)->num_rows();

		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('id', 'id', 'required');

		if($this->form_validation->run()==false)
		{
			echo $res=$this->msg_error("Please input the field!");
		}
		else if ($checkMenuDetail>0)
		{
			echo $res=$this->msg_error("Cannot update, menu action already paired to menu");
		}
		else if ($check>0)
		{

			echo $res=$this->msg_error("Menu action already in use");
		}
		else
		{
			$edited = $this->m_menu_action->update($id,$data);

			if ($edited)
			{

				echo $res=$this->msg_success("Success edit data");
			}
			else
			{

				echo $res=$this->msg_error("Failed edit data");
			}
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'configuration/menu_action/action_update';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function delete($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/menu_action','edit');
		$id = decode($id);
		$data = array(
			'status' => -5,
			'updated_by' => $this->session->userdata('username'),
			'updated_on' => date("Y-m-d H:i:s"),
		);

			//pengecekan data apakah ini sudah pernah ada di menu detail
		$checkMenuDetail=$this->m_global->getDataById("core.t_mtr_menu_detail","action_id=".$id)->num_rows();

		if($checkMenuDetail>0)
		{
			echo $res=$this->msg_error("Cannot delete, menu action already paired to menu");
		}
		else
		{
			$deleted  = $this->m_menu_action->delete($id,$data);
		
			if ($deleted) {

				echo $res=$this->msg_success("Success delete data");
			}
			else
			{
				echo $res=$this->msg_error("Failed delete data");
			}
		}
		
		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'configuration/menu_action/action_update';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function getList()
	{
		validateAjax();
		$menu = $this->m_menu_action->get();
		echo json_encode($menu);
	}

	function msg_error($msg)
	{
		return json_encode(array(
				'code' => 101, 
				'header' => 'Error',
				'message' => $msg,
				'theme' => 'alert-styled-left bg-danger'
			));
	}

	function msg_success($msg)
	{
		return json_encode(array(
				'code' => 200, 
				'header' => 'Error',
				'message' => $msg,
				'theme' => 'alert-styled-left bg-success'
			));
	}

}