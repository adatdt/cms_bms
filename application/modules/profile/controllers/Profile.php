<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_global');
		$this->load->model('m_profile');
		$this->load->model('login/m_login');
		$this->load->library('bcrypt');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$id=$this->session->userdata('user_id');

		$checkUserB2b=$this->m_global->getDataById("master.t_mtr_user_b2b",'user_id='.decode($id))->num_rows();
		if($checkUserB2b>0)
		{
			$data['title'] = "Profile B2B";
			$data['content'] = "profile/index_b2b";			
		}
		else
		{
			$data['title'] = "Profile";
			$data['content'] = "profile/index";
		}

		$data['id']=$id;
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view('common/page',$data);
	}

	function getProfile()
	{
		// $id=decode($this->input->post('id'));
		$id=decode($this->session->userdata('user_id'));
		$model=$this->m_global->getDataById("core.t_mtr_user","id_seq=$id")->row();

		$data=array(
					'id_seq'=>encode($model->id_seq),
					'full_name'=>$model->first_name." ".$model->last_name,
					'first_name'=>$model->first_name,
					'email'=>$model->email,
					'username'=>$model->username,
					);
		echo json_encode($data);
	}

	function edit($id)
	{
		validateAjax();
		$id=$this->session->userdata('user_id');
		$data['title'] = "Edit Profile";
		// $data['content'] = "profile/index";
		$data['profile']=$this->m_global->getDataById("core.t_mtr_user","id_seq=".decode($id))->row();
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("profile/edit",$data);

	}

	function edit_b2b($id)
	{
		validateAjax();
		$id=$this->session->userdata('user_id');
		$data['title'] = "Edit Profile";
		$data['profile']=$this->m_global->getDataById("core.t_mtr_user","id_seq=".decode($id))->row();
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("profile/edit_b2b",$data);

	}

	function action_edit()
	{
		$id=decode($this->input->post('id'));
		$lastName=trim($this->input->post('lastName'));
		$firstName=trim($this->input->post('firstName'));
		$email=trim($this->input->post('email'));

		$data=array(
			'first_name'=>$firstName,
			'last_name'=>$lastName,
			'email'=>$email,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		// $this->form_validation->set_rules('lastName', 'Last Name', 'required');
		$this->form_validation->set_rules('firstName', 'firt Name', 'required');
		// $this->form_validation->set_rules('email', 'email', 'required');

			
		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }
        else
        {
        	$update=$this->m_global->update('core.t_mtr_user',$data,"id_seq=$id");

        	if ($update)
        	{
        		$userdata['full_name']=$firstName." ".$lastName;
        		$this->session->set_userdata( $userdata);
        		echo $rest=$this->msg_success('Success edit data');
        	}
        	else
        	{
        		echo $rest=$this->msg_error('failed edit data');
        	}
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'profile/profile/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);				
	}

	function action_edit_b2b()
	{
		$id=decode($this->input->post('id'));
		$merchant=trim($this->input->post('merchant'));

		$data=array(
			'first_name'=>$merchant,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$dataMaster=array(
			'merchant_name'=>$merchant,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);


		$this->m_profile->update('core.t_mtr_user',$data,"id_seq=$id");

		$this->form_validation->set_rules('merchant', 'Merchant Name', 'required');
			
		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }
        else
        {

        	$this->db->trans_begin();
        	$this->m_profile->update('core.t_mtr_user',$data,"id_seq=$id");
        	$this->m_profile->update('master.t_mtr_user_b2b',$dataMaster,"user_id=$id");

        	if ($this->db->trans_status() === FALSE)
	        {
	            $this->db->trans_rollback();
	            echo $rest=$this->msg_error('failed edit data');	            
	        }
	        else
	        {
	            $this->db->trans_commit();
	            $userdata['full_name']=$merchant;
        		$this->session->set_userdata( $userdata);
        		echo $rest=$this->msg_success('Success edit data');
	        }

        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'profile/profile/action_edit_B2B';
        $logMethod   = 'UPDATE B2B';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);				
	}
	
	function change_password($id)
	{
		validateAjax();
		$id=$this->session->userdata('user_id');
		$data['title'] = "Change Password";
		$data['id']=$id;
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("profile/change_password",$data);
	}

	function action_change_password()
	{
		// $newPassword=$this->bcrypt->hash(strtoupper(md5($this->input->post('newPassword'))));
		$newPassword=trim($this->input->post('newPassword'));
		// $oldPassword=$this->db->escape_like_str(strtoupper(md5($this->input->post('oldPassword'))));
		$oldPassword=strtoupper(md5(trim($this->input->post('oldPassword'))));
		$repeatPassword=trim($this->input->post('repeatPassword'));
		$id=decode($this->session->userdata('user_id'));

		$data=array(
			'password'=>$this->bcrypt->hash(strtoupper(md5($this->input->post('newPassword')))),
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$this->form_validation->set_rules('oldPassword', 'password', 'required');
		$this->form_validation->set_rules('newPassword', 'password', 'required');
		$this->form_validation->set_rules('repeatPassword', 'password', 'required');

		$check=$this->m_login->check_login($this->session->userdata('username'));
		$checkpass=$this->bcrypt->compare($oldPassword,$check->password);
			
		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }
        else if($checkpass==false)
        {
        	echo $rest=$this->msg_error("Old password doesn't match");
        }
        else if ($newPassword != $repeatPassword)
        {
        	echo $rest=$this->msg_error("New password doesn't match with repeat password");	
        }
        else
        {
        	$update=$this->m_global->update('core.t_mtr_user',$data,"id_seq=$id");

        	if ($update)
        	{
        		echo $rest=$this->msg_success('Success change password');
        	}
        	else
        	{
        		echo $rest=$this->msg_error('failed edit data');
        	}
        }

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'profile/profile/action_change_password';
        $logMethod   = 'CHANGE PASSWORD';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}


	function msg_error($message)
	{
			return	json_encode(array(
				'code' => 101, 
				'header' => 'Error',
				'message' => $message,
				'theme' => 'alert-styled-left bg-danger'));
	}

	function msg_success($message)
	{
			return	json_encode(array(
				'code' => 200, 
				'header' => 'Success',
				'message' => $message,
				'theme' => 'alert-styled-left bg-success'));
	}
}

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */