<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_group extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_user_group');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "User Group";
		$data['content'] = "user_group/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add']     = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$data['edit']    = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$data['delete']  = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'delete');
		$this->load->view('common/page',$data);
	}

	public function add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/user_group','add');
		$data['title'] = "Add User Group";
		$data['access_type']=$this->m_global->getData("core.t_mtr_access_type","where status=1 order by access_type asc");
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("user_group/add",$data);
	}


	public function action_add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/user_group','add');
		$group_name = trim($this->input->post('group_name'));
		$access_type= decode($this->input->post('access_type'));

		$data = array(
			'group_name' => $group_name,
			'access_type_id'=>$access_type,
			'status' => 1,
			'created_by' => $this->session->userdata('user_group_id')
		);
		
		$this->form_validation->set_rules('group_name',"Group Name","required");
		$this->form_validation->set_rules('access_type',"Access Type","required");

		$check_group_name = $this->m_user_group->check_data('group_name',$group_name);

		if ($check_group_name)
		{

			echo $res=$this->msg_error("User group already in use");
		}
		else if($this->form_validation->run()==false)
		{
			echo $res=$this->msg_error("Please input the field!");	
		}
		else
		{
			$insert = $this->m_user_group->insert($data);

			if ($insert) {

				echo $res=$this->msg_success("Success add data");
			}
			else
			{
				echo $res=$this->msg_error("Failed add data");
			}
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'configuration/user_group/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function edit($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/user_group','edit');
		$id = decode($id);
		$data['access_type']=$this->m_global->getData("core.t_mtr_access_type","where status=1 order by access_type asc");
		$data['group_name'] = $this->m_user_group->get_edit($id);
		$data['id'] = encode($id);
		$data['detail']=$this->m_user_group->get_detail($id)->row();
		$data['title'] = "Edit User Group";
		// $data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("user_group/edit",$data);
	}

	public function update()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),'configuration/user_group','edit');
		$group_name = $this->input->post('group_name');
		$access_type = decode($this->input->post('access_type'));
		$id = decode($this->input->post('id'));
		
		$data = array(
			'group_name' => $group_name,
			'status' => 1,
			'access_type_id'=>$access_type,
			'updated_by' => $this->session->userdata('user_group_id'),
			'updated_on' => date("Y-m-d H:i:s")
		);

		$check_group_name = $this->m_user_group->check_data('group_name',$group_name,$id);

		//jika user group sudah pernah dipake di user, user group tidak bisa di update namanya
		$checkUser=$this->m_global->getDataById("core.t_mtr_user","user_group_id=".$id)->num_rows();

		$this->form_validation->set_rules('group_name','Group Name','required');
		$this->form_validation->set_rules('access_type','Access Type','required');

		if ($check_group_name)
		{

			echo $res=$this->msg_error("User group already in use");
		}
		else if($this->form_validation->run()==false)
		{
			echo $res=$this->msg_error("Please input the field");	
		}
		else if ($checkUser>0)
		{
			echo $res=$this->msg_error("Cannot update, user group already paired to user");	
		}
		else
		{
			$insert = $this->m_user_group->update($id,$data);
			if ($insert) {

				echo $res=$this->msg_success("Success update data");
			}
			else
			{
				echo $res=$this->msg_error("Failed update data");	
			}
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'configuration/user_group/update';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function delete($id)
	{
		validateAjax();
		$id = decode($id);

		$checkStatus=$this->m_global->getDataById("core.t_mtr_user_group","id_seq=$id")->row();

		// $checkStatus->status==1?$status=-5:$status=1;

		$data = array(
			'updated_by' => $this->session->userdata('user_group_id'),
			'updated_on' => date("Y-m-d H:i:s"),
			'status' =>'-5',
		);

		// cek apakah ada user yang sudah pake user group ini
		$checkUserGroup=$this->m_global->getdataById("core.t_mtr_user","user_group_id=$id")->num_rows();


		if ($checkUserGroup>0)
		{
				echo $res=$this->msg_error("Cannot delete, user group alredy paired to user");
		}
		else
		{
			$deleted = $this->m_user_group->delete($id,$data);

			if ($deleted) 
			{
				echo $res=$this->msg_success("Success delete data");
			}
			else
			{
				echo $res=$this->msg_error("Failed delete data");
			}
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'configuration/user_group/delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_user_group->get();
		echo json_encode($list);
	}

	public function msg_error($msg)
	{
		return  json_encode(array(
			'code' => 101, 
			'header' => 'Error',
			'message' => $msg,
			'theme' => 'alert-styled-left bg-danger'
		));
	}

	public function msg_success($msg)
	{
		return  json_encode(array(
			'code' => 200, 
			'header' => 'Success',
			'message' => $msg,
			'theme' => 'alert-styled-left bg-success'
		));
	}

}

/* End of file User.php */
/* Location: ./application/controllers/User.php */