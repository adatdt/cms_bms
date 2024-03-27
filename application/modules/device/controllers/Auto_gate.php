<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auto_gate extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_autogate');
		$this->load->library('bcrypt');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Auto Gate";
		$data['content'] = "device/auto_gate/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_autogate->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['shelter']=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc");
		$data['airport']=$this->m_global->getData("master.t_mtr_airport","where status=1 order by airport_name asc");
		$data['title'] = "Add Auto Gate";
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view("device/auto_gate/add",$data);
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
			'user_group_id'=>6, // hard code user group dengan bgate = 6
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

			$user_id=$this->m_autogate->insert("core.t_mtr_user",$dataCore);

			// cek kode jika kode bukan belasan 
			if(strlen($shelter)<=1)
			{
				$code="040".$shelter; //hardcode code bgate = 4
			}
			else
			{
				$code="04".$shelter;
			}

			$deviceCode=$this->m_autogate->generateCode($code);

			$dataTerminalDevice=array(
				'terminal_code'=>$deviceCode,
				'shelter_id'=>$shelter,
				'airport_id'=>$airport,
				'terminal_name'=>$name,
				'terminal_type_id'=>4, //hardcode
				'status'=>1,
				'created_by'=>$this->session->userdata('username'),
				'created_on'=>date('Y-m-d H:i:s'),
			);

			$terminal_id=$this->m_autogate->insert("master.t_mtr_device_terminal",$dataTerminalDevice);

			$dataMaster=array(
				'user_id'=>$user_id,
				'shelter_id'=>$shelter,
				'terminal_id'=>$terminal_id, 
				'status'=>1,
				'created_by'=>$this->session->userdata('username'),
				'created_on'=>date('Y-m-d H:i:s'),
			);
			$this->m_autogate->insert("master.t_mtr_user_boarding_gate",$dataMaster);

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
        $logUrl      = site_url().'device/auto_gate/action_add';
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
		$data['title'] = "Edit Auto Gate";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['shelter']=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc");
		$data['detail']=$this->m_autogate->detail($id);
		$data['airport']=$this->m_global->getData("master.t_mtr_airport","where status=1 order by airport_name asc");
		$this->load->view("device/auto_gate/edit",$data);

	}

	function action_edit()
	{
		validateAjax();
		$this->m_global->getMenu($this->session->userdata('user_group_id'));
		$user_id=decode($this->input->post('id'));
		$name=trim($this->input->post('name'));
		$username=trim($this->input->post('username'));
		$shelter=decode($this->input->post('shelter'));
		$airport=decode($this->input->post('airport'));

		$this->form_validation->set_rules('name','Name','required');
		$this->form_validation->set_rules('username','Username','required');
		$this->form_validation->set_rules('shelter','Shelter','required');
		$this->form_validation->set_rules('airport','Airport','required');

		$dataCore=array(
						'username'=>$username,
						'first_name'=>$name,
						'updated_by'=>$this->session->userdata('username'),
						'updated_on'=>date("Y-m-d H:i:s"),
						);
		

		if($this->form_validation->run() == FALSE)
		{
			echo $res=$this->msg_error('Please input the field');

			$data=$dataCore;
		}
		else
		{
			$checkBoarding=$this->db->query("SELECT a.*, b.id_seq, b.username FROM trx.t_trx_boarding a JOIN core.t_mtr_user b ON a.created_by=b.username where b.id_seq = '".$user_id."'")->num_rows();

			if($checkBoarding>0)
			{
				echo $res=$this->msg_error("Cannot update, user in transaction");
				$data=$dataCore;
			}
			else
			{
				$getUsernameOld=$this->m_global->getDataById("core.t_mtr_user","id_seq=$user_id")->row();

				//check username exist with current and old username
				$checkUsername=$this->m_global->getDataById("core.t_mtr_user","upper(username)=upper('".$username."') and upper(username)<>upper('".$getUsernameOld->username."')")->num_rows();
				if($checkUsername>0)
				{
					echo $res=$this->msg_error('Username already in use');
					$data=$dataCore;
				}
				else
				{
					$this->db->trans_begin();
					$this->m_autogate->update("core.t_mtr_user",$dataCore,"id_seq=$user_id");

					// cek kode jika kode bukan belasan 
					if(strlen($shelter)<=1)
					{
						$code="040".$shelter; //hardcode code bgate = 4
					}
					else
					{
						$code="04".$shelter;
					}

					// cek shelter_id, jika shelter tidak ganti maka menggunakan terminal_code lama
					$checkShelterId = $this->m_global->getDataById("master.t_mtr_user_boarding_gate", "user_id=$user_id")->row();
					if($checkShelterId->shelter_id==$shelter)
					{
						$getTerminalCode=$this->db->query("select terminal_code from master.t_mtr_user_boarding_gate ub left join master.t_mtr_device_terminal dt on ub.terminal_id=dt.id_seq where user_id=$user_id")->row();
						$deviceCode=$getTerminalCode->terminal_code;
					}
					else
					{
						$deviceCode=$this->m_autogate->generateCode($code);	
					}
					

					$dataTerminalDevice=array(
						'terminal_code'=>$deviceCode,
						'shelter_id'=>$shelter,
						'airport_id'=>$airport,
						'terminal_name'=>$name,
						'updated_by'=>$this->session->userdata('username'),
						'updated_on'=>date("Y-m-d H:i:s"),
					);

					$getDeviceTerminal=$this->m_global->getDataById("master.t_mtr_user_boarding_gate","user_id=$user_id")->row();

					$this->m_autogate->update("master.t_mtr_device_terminal",$dataTerminalDevice,"id_seq=".$getDeviceTerminal->terminal_id);

					$dataMaster=array(
						'user_id'=>$user_id,
						'shelter_id'=>$shelter,
						'terminal_id'=>$getDeviceTerminal->terminal_id, 
						'updated_by'=>$this->session->userdata('username'),
						'updated_on'=>date("Y-m-d H:i:s")
					);
					$this->m_autogate->update("master.t_mtr_user_boarding_gate",$dataMaster,"user_id=$user_id");		

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
	       	}
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'device/auto_gate/action_edit';
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
		$this->m_autogate->update("core.t_mtr_user",$dataCore,"id_seq=$user_id");

		$dataMaster=array(
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date("Y-m-d H:i:s"),
			);
		$this->m_autogate->update("master.t_mtr_user_boarding_gate",$dataMaster,"user_id=$user_id");

		$getDeviceTerminal=$this->m_global->getDataById("master.t_mtr_user_boarding_gate","user_id=$user_id")->row();

		$dataTerminalDevice=array(
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date("Y-m-d H:i:s"),
			);

		$this->m_autogate->update("master.t_mtr_device_terminal",$dataTerminalDevice,"id_seq=".$getDeviceTerminal->terminal_id);

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
        $logUrl      = site_url().'device/auto_gate/update_password';
        $logMethod   = 'RESET PASSWORD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function delete($id,$msg)
	{
		validateAjax();
		$id = decode($id);

		$getUser=$this->m_global->getDataById("core.t_mtr_user","id_seq=$id")->row();
		$getUserKiosk=$this->m_global->getDataById("master.t_mtr_user_boarding_gate","user_id=$id")->row();

		if(strtoupper($msg)==strtoupper('disable'))
		{
			$data=array(
				'updated_on'=>date("Y-m-d H:i:s"),
				'created_by'=>$this->session->userdata('username'),
				'status'=>-1,
			);

			$checkUsername=$this->m_global->getDataById("trx.t_trx_booking","created_by='".$getUser->username."'")->num_rows();
			
			if($checkUsername>0)
			{
				echo $this->msg_error("Failed $msg, username active");
				exit();
			}
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
		$this->m_autogate->update("core.t_mtr_user",$data,"id_seq=$id");
		$this->m_autogate->update("master.t_mtr_user_boarding_gate",$data,"user_id=$id");
		$this->m_autogate->update("master.t_mtr_device_terminal",$data,"id_seq=".$getUserKiosk->terminal_id." ");

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

/* End of file Auto_gate.php */
/* Location: ./application/controllers/Auto_gate.php */