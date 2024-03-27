<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kiosk extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_kiosk');
		$this->load->library('bcrypt');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Kiosk";
		$data['content'] = "device/kiosk/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_kiosk->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['shelter']=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc");
		$data['airport']=$this->m_global->getData("master.t_mtr_airport","where status=1 order by airport_name asc");
		$data['title'] = "Add Kiosk";
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view("device/kiosk/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$name=trim($this->input->post('name'));
		$username=trim($this->input->post('username'));
		$password=trim($this->input->post('password'));
		$shelter=decode($this->input->post('shelter'));
		$airport=decode($this->input->post('airport'));

		$this->form_validation->set_rules('name','Name','required');
		$this->form_validation->set_rules('password','Password','required');
		$this->form_validation->set_rules('shelter','Shelter','required');
		$this->form_validation->set_rules('airport','Airport','required');

		$dataCore=array(
			'username'=>$username,
			'user_group_id'=>3, // hard cord user group denga kiosk =3
			'password'=>$this->bcrypt->hash(strtoupper(md5($password))),
			'first_name'=>$name,
			'status'=>1,
			'created_by'=>$this->session->userdata('username'),
			'created_on'=>date('Y-m-d H:i:s'),
			);

		$checkUsername=$this->m_global->getDataById("core.t_mtr_user","upper(username)=upper('".$username."')")->num_rows();

		if($this->form_validation->run() == FALSE)
		{
			echo $res=$this->msg_error('Please input the field');
			$data=$dataCore;
		}
		else if($checkUsername>0)
		{
			echo $res=$this->msg_error('Username already in use');
			$data=$dataCore;
		}
		else
		{
			$this->db->trans_begin();

			$user_id=$this->m_kiosk->insert("core.t_mtr_user",$dataCore);

			// cek kode jika shelter bukan belasan 
			if(strlen($shelter)<=1)
			{
				$code="010".$shelter; //hardcord code kiosk =1
			}
			else
			{
				$code="01".$shelter;
			}

			$deviceCode=$this->m_kiosk->generateCode($code);

			$dataTerminalDevice=array(
				'terminal_code'=>$deviceCode,
				'shelter_id'=>$shelter,
				'airport_id'=>$airport,
				'terminal_name'=>$name,
				'terminal_type_id'=>1, //hardcord
				'status'=>1,
				'created_by'=>$this->session->userdata('username'),
				'created_on'=>date('Y-m-d H:i:s'),
			);

			$terminal_id=$this->m_kiosk->insert("master.t_mtr_device_terminal",$dataTerminalDevice);

			$dataMaster=array(
				'user_id'=>$user_id,
				'shelter_id'=>$shelter,
				'terminal_id'=>$terminal_id, 
				'status'=>1,
				'created_by'=>$this->session->userdata('username'),
				'created_on'=>date('Y-m-d H:i:s'),
			);
			$this->m_kiosk->insert("master.t_mtr_user_kiosk",$dataMaster);

			if($this->db->trans_status() === FALSE) {
            	$this->db->trans_rollback();

            	echo $res=$this->msg_error("Failed add data");
	        } 
	        else 
	        {
	            $this->db->trans_commit();
	            echo $res=$this->msg_Success("Success add data");
	        }

	        $data=array($dataCore,$dataTerminalDevice,$dataMaster);
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/kiosk/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

	}

	function edit($id)
	{
		validateAjax();
		$id = decode($id);
		$data['id'] = encode($id);
		$data['title'] = "Edit Kiosk";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['shelter']=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc");
		$data['detail']=$this->m_kiosk->detail($id);
		$data['airport']=$this->m_global->getData("master.t_mtr_airport","where status=1 order by airport_name asc");
		$this->load->view("device/kiosk/edit",$data);

	}

	function action_edit()
	{
		validateAjax();
		$this->m_global->getMenu($this->session->userdata('user_group_id'));
		$name=trim($this->input->post('name'));
		$user_id=decode($this->input->post('id'));
		$shelter=decode($this->input->post('shelter'));

		$this->form_validation->set_rules('name','Name','required');
		$this->form_validation->set_rules('shelter','shelter','required');

		// cek kode jika shelter bukan belasan 
		if(strlen($shelter)<=1)
		{
			$code="010".$shelter; //hardcord code kiosk =1
		}
		else
		{
			$code="01".$shelter;
		}

		$deviceCode=$this->m_kiosk->generateCode($code);

		$dataCore=array('first_name'=>$name,
						'updated_by'=>$this->session->userdata('username'),
						'updated_on'=>date("Y-m-d H:i:s"),
						);

		// ambil data user 
		$dataUser=$this->m_global->getDataById("core.t_mtr_user","id_seq=".$user_id)->row();

		// ambil data master 
		$checkMaster=$this->m_global->getDataById("master.t_mtr_user_kiosk","user_id=".$user_id)->row();

		// check jika user kiosk sudah perbah melakukan transaksi
		$checkBooking=$this->m_global->getDataById("trx.t_trx_booking","upper(created_by)=upper('".$dataUser->username."')")->num_rows();

		if($this->form_validation->run() == FALSE)
		{
			echo $res=$this->msg_error('Please input the field');

			$data=$dataCore;
		}
		else if ($checkBooking>0)
		{
			echo $res=$this->msg_error('Cannot update, user in transaction');
			$data=$dataCore;
		}
		else
		{
			$this->db->trans_begin();
			$this->m_kiosk->update("core.t_mtr_user",$dataCore,"id_seq=$user_id");

			$dataMaster=array(
				'shelter_id'=>$shelter,
				'updated_by'=>$this->session->userdata('username'),
				'updated_on'=>date("Y-m-d H:i:s"),
				);
			$this->m_kiosk->update("master.t_mtr_user_kiosk",$dataMaster,"user_id=$user_id");

			$getDeviceTerminal=$this->m_global->getDataById("master.t_mtr_user_kiosk","user_id=$user_id")->row();

			//validasi jika yang diinput sama dengan shelter 
			if($checkMaster->shelter_id==$shelter)
			{
				$dataTerminalDevice=array('terminal_name'=>$name,
				'shelter_id'=>$shelter,
				'updated_by'=>$this->session->userdata('username'),
				'updated_on'=>date("Y-m-d H:i:s"),
				);
			}
			else
			{
				$dataTerminalDevice=array('terminal_name'=>$name,
				'shelter_id'=>$shelter,
				'terminal_code'=>$deviceCode,
				'updated_by'=>$this->session->userdata('username'),
				'updated_on'=>date("Y-m-d H:i:s"),
				);	
			}

			$this->m_kiosk->update("master.t_mtr_device_terminal",$dataTerminalDevice,"id_seq=".$getDeviceTerminal->terminal_id);

			if ($this->db->trans_status() === FALSE)
	        {
                $this->db->trans_rollback();
                echo $res=$this->msg_error("Failed edit data");
	        }
	        else
	        {
                $this->db->trans_commit();
                echo $res=$this->msg_success("Success edit data");
	        }			

	        $data=array($dataCore,$dataTerminalDevice,$dataMaster);
		    
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/kiosk/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

	}
	
	public function update_password($id_seq)
	{
		validateAjax();
		$user_id = decode($id_seq);
		$password = $this->bcrypt->hash(strtoupper(md5('admin123')));

		$dataCore = array(
			'password' => $password,
			'updated_by' => $this->session->userdata("username"),
			'updated_on' => date("Y-m-d H:i:s"),
		);

		$this->db->trans_begin();
		$this->m_kiosk->update("core.t_mtr_user",$dataCore,"id_seq=$user_id");

		$dataMaster=array(
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date("Y-m-d H:i:s"),
			);
		$this->m_kiosk->update("master.t_mtr_user_kiosk",$dataMaster,"user_id=$user_id");

		$getDeviceTerminal=$this->m_global->getDataById("master.t_mtr_user_kiosk","user_id=$user_id")->row();

		$dataTerminalDevice=array(
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date("Y-m-d H:i:s"),
			);

		$this->m_kiosk->update("master.t_mtr_device_terminal",$dataTerminalDevice,"id_seq=".$getDeviceTerminal->terminal_id);

		if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $res=$this->msg_error("Failed reset password");
        }
        else
        {
            $this->db->trans_commit();
            echo $res=$this->msg_success("Success reset password");
        }			

        $data=array($dataCore,$dataTerminalDevice,$dataMaster);

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/kiosk/update_password';
        $logMethod   = 'RESET PASSWORD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function delete($id,$msg)
	{
		validateAjax();
		$id = decode($id);

		$getUserKiosk=$this->m_global->getDataById("master.t_mtr_user_kiosk","user_id=$id")->row();
		$getUser=$this->m_global->getDataById("core.t_mtr_user","id_seq=$getUserKiosk->user_id")->row();

		if(strtoupper($msg)==strtoupper('disable'))
		{
			$data=array(
				'updated_on'=>date("Y-m-d H:i:s"),
				'created_by'=>$this->session->userdata('username'),
				'status'=>-1,
			);

			// $checkUsername=$this->m_global->getDataById("trx.t_trx_booking","created_by='".$getUser->username."'")->num_rows();
			
			// if($checkUsername>0)
			// {
			// 	echo $this->msg_error("Failed $msg, username active");
			// 	exit();
			// }
		}
		else
		{
			$data=array(
				'updated_on'=>date("Y-m-d H:i:s"),
				'created_by'=>$this->session->userdata('username'),
				'status'=>1,
			);
		}

		$this->db->trans_begin();
		$this->m_kiosk->update("core.t_mtr_user",$data,"id_seq=$id");
		$this->m_kiosk->update("master.t_mtr_user_kiosk",$data,"user_id=$id");
		$this->m_kiosk->update("master.t_mtr_device_terminal",$data,"id_seq=".$getUserKiosk->terminal_id." ");

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			echo $res=$this->msg_error("Failed ".$msg." user");
		}
		else
		{
			$this->db->trans_commit();
			echo $res=$this->msg_success("Success ".$msg." user");
		}
	}

	public function delete_data($id)
	{
		validateAjax();
		$id = decode($id);

		$getUserKiosk=$this->m_global->getDataById("master.t_mtr_user_kiosk","user_id=$id")->row();
		$getUser=$this->m_global->getDataById("core.t_mtr_user","id_seq=$getUserKiosk->user_id")->row();
		// echo json_encode($getUserKiosk);exit;

		$data=array(
			'updated_on'=>date("Y-m-d H:i:s"),
			'created_by'=>$this->session->userdata('username'),
			'status'=>-5,
		);

		$this->db->trans_begin();
		$this->m_kiosk->update("core.t_mtr_user",$data,"id_seq=".$getUserKiosk->user_id." ");
		$this->m_kiosk->update("master.t_mtr_user_kiosk",$data,"user_id=$id");
		$this->m_kiosk->update("master.t_mtr_device_terminal",$data,"id_seq=".$getUserKiosk->terminal_id." ");

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			echo $res=$this->msg_error("Failed delete user");
		}
		else
		{
			$this->db->trans_commit();
			echo $res=$this->msg_success("Success delete user");
		}
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

/* End of file Type.php */
/* Location: ./application/controllers/Type.php */