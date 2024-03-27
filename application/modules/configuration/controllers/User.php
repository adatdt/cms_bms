<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_user');
		$this->load->model('m_register');
		$this->load->model('m_global');
		$this->load->library('bcrypt');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "User";
		$data['content'] = "user/index";
		$data['user_group_data'] = $this->m_global->getData("core.t_mtr_user_group","where status=1 and access_type_id in (1,3) order by group_name asc ");
		$data['user_group'] = $this->m_register->getUserGroup();
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add']     = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$data['edit']    = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$data['change_password']    = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'change_password');
		$data['delete']  = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'delete');
		$this->load->view('common/page',$data);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add User";
		// $data['user_group'] = $this->m_register->getUserGroup();
		$data['user_group'] = $this->m_global->getData("core.t_mtr_user_group","where status=1 and access_type_id in (1,3) order by group_name asc ");
		$data['shelter'] = $this->m_global->getData('master.t_mtr_shelter','where status=1 order by shelter_name asc');
		$data['deviceTerminal'] = $this->m_global->getData('master.t_mtr_device_terminal','where status=1 order by terminal_name asc');
		// $data['deviceTerminal']=$this->m_user->getTerminalDevice();
		$data['po'] = $this->m_global->getData('master.t_mtr_po','where status=1 order by id_seq asc');
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("user/add",$data);
	}

	public function edit($id)

	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$id = decode($id);

		$id_group=$this->m_global->getDataById('core.t_mtr_user',"id_seq=$id")->row();
		
		if($id_group->user_group_id==3)
		{
			$data['user'] = $this->m_user->getUserKiosk($id);
			$data['lane'] = $this->m_global->getData('master.t_mtr_lane','where status=1 order by lane_name asc');
			$data['deviceTerminal'] = $this->m_global->getData('master.t_mtr_device_terminal','where status=1 order by terminal_name asc');
		}
		else if($id_group->user_group_id==6)
		{
			$data['user'] = $this->m_user->getBoardingGate($id);
			$data['lane'] = $this->m_global->getData('master.t_mtr_lane','where status=1 order by lane_name asc');
			$data['deviceTerminal'] = $this->m_global->getData('master.t_mtr_device_terminal','where status=1 order by terminal_name asc');
		}
		else if($id_group->user_group_id==7)
		{
			$checkLane=$this->m_global->getDataById('master.t_mtr_user_manless_gate',"user_id=$id")->row();
			$shelterId=$checkLane->shelter_id;
			$data['user'] = $this->m_user->getManlessGate($id);
			$data['lane'] = $this->m_global->getData('master.t_mtr_lane',"where shelter_id=$shelterId and status=1 order by lane_name asc");
		}
		else if($id_group->user_group_id==5)
		{
			$data['user'] = $this->m_user->getUserValidator($id);
			$data['lane'] = $this->m_global->getData('master.t_mtr_lane','where status=1 order by lane_name asc');
			$data['deviceTerminal'] = $this->m_global->getData('master.t_mtr_device_terminal','where status=1 order by terminal_name asc');
		}
		else if($id_group->user_group_id==4)
		{
			$data['user'] = $this->m_user->getUserPos($id);
			$data['lane'] = $this->m_global->getData('master.t_mtr_lane','where status=1 order by lane_name asc');
			// hard cord yang type device 2
			$data['deviceTerminal']=$this->m_global->getDataById("master.t_mtr_device_terminal","terminal_type_id=2 and status=1 ")->result();
		}
		else if($id_group->user_group_id==8)
		{
			$data['user'] = $this->m_user->getPengendapanGate($id);
			$data['lane'] = $this->m_global->getData('master.t_mtr_lane','where status=1 order by lane_name asc');
			$data['deviceTerminal'] = $this->m_global->getData('master.t_mtr_device_terminal','where status=1 order by terminal_name asc');
		}

		else if($id_group->user_group_id==9)
		{
			$data['user'] = $this->m_user->getB2b($id);
			$data['lane'] = $this->m_global->getData('master.t_mtr_lane','where status=1 order by lane_name asc');
			$data['deviceTerminal'] = $this->m_global->getData('master.t_mtr_device_terminal','where status=1 order by terminal_name asc');
		}

		else if($id_group->user_group_id==11)
		{
			$data['user'] = $this->m_user->getUserPo($id);
			$data['lane'] = $this->m_global->getData('master.t_mtr_lane','where status=1 order by lane_name asc');
			$data['deviceTerminal'] = $this->m_global->getData('master.t_mtr_device_terminal','where status=1 order by terminal_name asc');
		}

		else
		{
			$data['user'] = $this->m_user->getUserAll($id);
			$data['lane'] = $this->m_global->getData('master.t_mtr_lane','where status=1 order by lane_name asc');
			$data['deviceTerminal'] = $this->m_global->getData('master.t_mtr_device_terminal','where status=1 order by terminal_name asc');
		}

		$data['shelter'] = $this->m_global->getData('master.t_mtr_shelter','where status=1 order by shelter_name asc');
		// $data['deviceTerminal'] = $this->m_global->getData('master.t_mtr_device_terminal','where status=1 order by terminal_name asc');
		$data['po'] = $this->m_global->getData('master.t_mtr_po','where status=1 order by po_name asc');
		$data['id'] = encode($id);
		$data['title'] = "Edit User";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));

		$this->load->view("user/edit",$data);

	}

	public function change_password($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'change_password');
		$id = decode($id);
		$data['username'] = $this->m_user->get_username($id);
		$data['id'] = encode($id);
		$data['title'] = "Change Password";
		$data['user_group'] = $this->m_register->getUserGroup();
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("user/change_password",$data);
	}

	public function update_password($id_seq)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'change_password');
		$id = decode($id_seq);
		$password = $this->bcrypt->hash(strtoupper(md5('admin123')));

		$data = array(
			'password' => $password,
			'updated_by' => $this->session->userdata("username"),
			'updated_on' => date("Y-m-d H:i:s"),
		);

		$change_password = $this->m_user->update_password($id,$data);
		if ($change_password) {

			echo $res=$this->msg_success('Success reset password');

		}else
		{
			echo $res=$this->msg_success('Failed reset password');
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'configuration/user/update_password';
        $logMethod   = 'RESET PASSWORD';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	public function action_add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$userName=trim($this->input->post('userName'));
		$email=trim($this->input->post('email'));
		$password=$this->bcrypt->hash(strtoupper(md5(trim($this->input->post('password')))));
		$pass=$this->input->post('password');
		$deviceTerminal=$this->input->post('deviceTerminal');
		$po=$this->input->post('po');
		$firstName=trim($this->input->post('firstName'));
		$lastName=trim($this->input->post('lastName'));
		$user_group=$this->input->post('user_group');
		$shelter=$this->input->post('shelter');
		$laneId=$this->input->post('lane');
		$merchant=trim($this->input->post('merchant'));
		$generatePass=$this->bcrypt->hash(strtoupper(md5(trim($this->input->post('generatePass')))));
		$genPass=$this->input->post('generatePass');

		$dataCore=array(
			'user_group_id'=>$user_group,
			'username'=>$userName,
			'password'=>$password,
			'first_name'=>$firstName,
			'last_name'=>$lastName,
			'email'=>$email,
			'status'=>1,
			'created_by'=>$this->session->userdata('username'),
			'created_on'=>date('Y-m-d H:i:s'),
		);

		$userCheck=$this->m_global->getDataById('core.t_mtr_user',"username='".$userName."'")->num_rows();

		if($userCheck>0)
		{
			echo $this->msg_error('Username already exist');
		}
		else
		{

			if ($user_group==3)
			{
				if (empty($userName)||empty($pass)|| empty($shelter)||empty($deviceTerminal) || empty($user_group)|| empty($firstName))
				{
					echo $this->msg_error('The field is required');
				}
				else
				{
					$this->db->trans_begin();
					$this->m_user->insertData('core.t_mtr_user',$dataCore);
					$userId=$this->m_global->getDataById('core.t_mtr_user',"username='".$userName."'")->row();
					$dataMaster=array(
						'user_id'=>$userId->id_seq,
						'shelter_id'=>$shelter,
						'terminal_id'=>$deviceTerminal,
						'status'=>1,
						'created_by'=>$this->session->userdata('username')
					);

					$this->m_user->insertData('master.t_mtr_user_kiosk',$dataMaster);
					
					if($this->db->trans_status() === FALSE) {
			           	$this->db->trans_rollback();
			           	echo $this->msg_error("Failed insert data");
			        } 
			        else 
			        {
			            $this->db->trans_commit();
			            echo $this->msg_success("success");
			        }
				}
			}

			else if ($user_group==6)
			{
				if (empty($user_group) || empty($userName) || empty($pass) || empty($shelter)||empty($deviceTerminal) || empty($firstName))
				{
					echo $this->msg_error('The field is required');
				}
				else
				{
					$this->db->trans_begin();
					$this->m_user->insertData('core.t_mtr_user',$dataCore);
					$userId=$this->m_global->getDataById('core.t_mtr_user',"username='".$userName."'")->row();

						$dataMaster=array(
							'user_id'=>$userId->id_seq,
							'shelter_id'=>$shelter,
							'terminal_id'=>$deviceTerminal,
							'status'=>1,
							'created_by'=>$this->session->userdata('username'),
						);

					$this->m_user->insertData('master.t_mtr_user_boarding_gate',$dataMaster);

					if($this->db->trans_status() === FALSE) {
			            $this->db->trans_rollback();
			            echo $this->msg_error("Failed insert data");
			        } 
			        else 
			        {
			            $this->db->trans_commit();
			            echo $this->msg_success("success insert data");
			        }
				}
			}

			else if ($user_group==7)
			{
				if (empty($userName)||empty($pass)||empty($shelter)||empty($deviceTerminal) || empty($firstName) || empty($laneId))
				{
					echo $this->msg_error('The field is required');
				}
				else

				{
					$this->db->trans_begin();
					$this->m_user->insertData('core.t_mtr_user',$dataCore);

					$userId=$this->m_global->getDataById('core.t_mtr_user',"username='".$userName."'")->row();

					$dataMaster=array(
						'user_id'=>$userId->id_seq,
						'shelter_id'=>$shelter,
						'lane_id'=>$laneId,
						'terminal_id'=>$deviceTerminal,
						'status'=>1,
						'created_by'=>$this->session->userdata('username'),
					);
					$this->m_user->insertData('master.t_mtr_user_manless_gate',$dataMaster);
				
					if($this->db->trans_status() === FALSE) {
			        	$this->db->trans_rollback();
			          	echo $this->msg_error("Failed insert data");
			        } 
			        else 
			        {
			            $this->db->trans_commit();
			            echo $this->msg_success("Success insert data");
			        }							
				}
			}
			// grou user id 8 smasuk ke t_mtr_user manless gate tetapi tidak selternya null
			else if ($user_group==8)
			{
				if (empty($userName)||empty($pass)||empty($deviceTerminal)|| empty($firstName))
				{
					echo $this->msg_error('The field is required');
				}
				else
				{

					$this->db->trans_begin();
					$this->m_user->insertData('core.t_mtr_user',$dataCore);

					$userId=$this->m_global->getDataById('core.t_mtr_user',"username='".$userName."'")->row();

						$dataMaster=array(
							'user_id'=>$userId->id_seq,
							'terminal_id'=>$deviceTerminal,
							'status'=>1,
							'created_by'=>$this->session->userdata('username'),
						);
					$this->m_user->insertData('master.t_mtr_user_manless_gate',$dataMaster);
					
					if($this->db->trans_status() === FALSE) {
			        	$this->db->trans_rollback();
			        	echo $this->msg_error("Failed insert data");
			        } 
			        else 
			        {
			            $this->db->trans_commit();
			            echo $this->msg_success("Success insert data");
			        }	
				}
			}

			else if ($user_group==4)
			{
				if (empty($user_group)||empty($userName)||empty($pass) ||empty($deviceTerminal)|| empty($firstName))
				{
					echo $this->msg_error('The field is required');
				}
				else
				{

					$this->db->trans_begin();
					
					$this->m_user->insertData('core.t_mtr_user',$dataCore);
					$userId=$this->m_global->getDataById('core.t_mtr_user',"username='".$userName."'")->row();

					$dataMaster=array(
						'user_id'=>$userId->id_seq,
						'terminal_id'=>$deviceTerminal,
						'lastname'=>$lastName,
						'firstname'=>$firstName,
						'status'=>1,
						'created_by'=>$this->session->userdata('username'),
					);
					$this->m_user->insertData('master.t_mtr_user_pos',$dataMaster);

					if($this->db->trans_status() === FALSE) {
			            $this->db->trans_rollback();
			       		echo $this->msg_error("Failed insert data");
			        } 
			        else 
			        {
			            $this->db->trans_commit();
			            echo $this->msg_success("Success insert data");
			        }
						
				}
			}

			else if ($user_group==5)
			{
				if (empty($userName)||empty($pass) || empty($shelter)||empty($po) || empty($user_group)|| empty($firstName))
				{
					echo $this->msg_error('The field is required');
				}
				else
				{
					$this->db->trans_begin();
					$this->m_user->insertData('core.t_mtr_user',$dataCore);
					$userId=$this->m_global->getDataById('core.t_mtr_user',"username='".$userName."'")->row();

					$dataMaster=array(
						'user_id'=>$userId->id_seq,
						'shelter_id'=>$shelter,
						'po_id'=>$po,
						'status'=>1,
						'created_by'=>$this->session->userdata('username'),
						'created_on'=>date('Y-m-d H:i:s'),
					);

					$this->m_user->insertData('master.t_mtr_user_validator',$dataMaster);

					if($this->db->trans_status() === FALSE) {
			            $this->db->trans_rollback();
			            echo $this->msg_error("Failed insert data");
			        } 
			        else 
			        {
			            $this->db->trans_commit();
			            echo $this->msg_success("Success insert data");
			        }
				}
			}

			else if ($user_group==9)
			{
				if (empty($userName)|| empty($genPass) || empty($user_group) || empty($merchant))
				{
					echo $this->msg_error('The field is required');
				}
				else
				{
					$dataCoreMerchant=array(
						'user_group_id'=>$user_group,
						'username'=>$userName,
						'password'=>$generatePass,
						'first_name'=>$merchant,
						'status'=>1,
						'created_by'=>$this->session->userdata('username'),
					);

					$this->db->trans_begin();
					$this->m_user->insertData('core.t_mtr_user',$dataCoreMerchant);
					$userId=$this->m_global->getDataById('core.t_mtr_user',"username='".$userName."'")->row();

					$dataMaster=array(
						'user_id'=>$userId->id_seq,
						'merchant_name'=>$merchant,
						'status'=>1,
						'created_by'=>$this->session->userdata('username'),
					);

					$this->m_user->insertData('master.t_mtr_user_b2b',$dataMaster);

					if($this->db->trans_status() === FALSE) {
			            $this->db->trans_rollback();
			            echo $this->msg_error("Failed insert data");
			        } 
			        else 
			        {
			            $this->db->trans_commit();
			            echo $this->msg_success("Success insert data");
			        }	
				}
			}

			else if ($user_group==11)
			{
				if (empty($userName)|| empty($password) || empty($user_group) || empty($firstName) || empty($po))
				{
					echo $this->msg_error('The field is required');
				}
				else
				{

					$this->db->trans_begin();
					$this->m_user->insertData('core.t_mtr_user',$dataCore);
					$userId=$this->m_global->getDataById('core.t_mtr_user',"username='".$userName."'")->row();

					$dataMaster=array(
						'user_id'=>$userId->id_seq,
						'po_id'=>$po,
						'status'=>1,
						'created_by'=>$this->session->userdata('username'),
					);

					$this->m_user->insertData('master.t_mtr_user_po',$dataMaster);

					if($this->db->trans_status() === FALSE) {
			            $this->db->trans_rollback();
			            echo $this->msg_error("Failed insert data");
			        } 
			        else 
			        {
			            $this->db->trans_commit();
			            echo $this->msg_success("Success insert data");
			        }	
				}
			}

			else
			{
				if (empty($userName)||empty($pass)|| empty($user_group) || empty($firstName))
				{
					echo $this->msg_error('The field is required');
				}
				else
				{
					$this->db->trans_begin();
					$insertCore=$this->m_user->insertData('core.t_mtr_user',$dataCore);
					
					if($this->db->trans_status() === FALSE) {
			            $this->db->trans_rollback();
			            echo $this->msg_error("Failed insert data");
			        } 
			        else 
			        {
			            $this->db->trans_commit();
			            echo $this->msg_success("Success insert data");
			        }
				}
			}

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

	public function update()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');

		 $id = decode($this->input->post('id'));
		// $userName=$this->input->post('userName');
		$email=trim($this->input->post('email'));
		$password=$this->bcrypt->hash(strtoupper(md5(trim($this->input->post('password')))));
		$deviceTerminal=$this->input->post('deviceTerminal');
		$po=$this->input->post('po');
		$firstName=$this->input->post('firstName');
		$lastName=$this->input->post('lastName');
		$user_group=$this->input->post('groupId');
		$shelter=$this->input->post('shelter');
		$laneId=trim($this->input->post('lane'));
		$merchant=trim($this->input->post('merchant'));

		$dataCore=array(
			// 'username'=>$userName,
			'email'=>$email,
			'first_name'=>$firstName,
			'last_name'=>$lastName,
			'updated_by' => $this->session->userdata('username'),
			'updated_on' => date("Y-m-d H:i:s")
		);

		if ($user_group==3)
		{
			if (empty($user_group) || empty($shelter)||empty($deviceTerminal) || empty($firstName))
			{
				echo $this->msg_error('The field is required');
			}
			else
			{
				$this->db->trans_begin();
				$this->m_user->updateData('core.t_mtr_user',$dataCore,"id_seq=$id");
				
				$dataMaster=array(
					'shelter_id'=>$shelter,
					'terminal_id'=>$deviceTerminal,
					'updated_by'=>$this->session->userdata('username'),
					'updated_on'=>date('Y-m-d H:i:s'),
				);

				$this->m_user->updateData('master.t_mtr_user_kiosk',$dataMaster,"user_id=$id");

				if ($this->db->trans_status() === FALSE)
		        {
		                $this->db->trans_rollback();
		                echo $this->msg_error('Failed edit data');
		        }
		        else
		        {
		                $this->db->trans_commit();
		                echo $this->msg_success('Success edit data');
		        }
			}
		}

		else if ($user_group==6)
		{
			if (empty($user_group)|| empty($shelter)||empty($deviceTerminal) || empty($firstName))
			{
				echo $this->msg_error('The field is required');
			}
			else
			{
				$this->db->trans_begin();
				$this->m_user->updateData('core.t_mtr_user',$dataCore,"id_seq=$id");

				$dataMaster=array(
					'shelter_id'=>$shelter,
					'terminal_id'=>$deviceTerminal,
					'updated_by'=>$this->session->userdata('username'),
					'updated_on'=>date("Y-m-d H:i:s"),
				);

				$this->m_global->update('master.t_mtr_user_boarding_gate',$dataMaster,"user_id=$id");

				if ($this->db->trans_status() === FALSE)
		        {
	                $this->db->trans_rollback();
	                echo $this->msg_error('Failed edit data');
		        }
		        else
		        {
	                $this->db->trans_commit();
	                echo $this->msg_success('Success edit data');
		        }
			}
		}

		else if ($user_group==7)
		{
			if (empty($user_group)|| empty($shelter)||empty($deviceTerminal) || empty($firstName)|| empty($laneId))
			{
				echo $this->msg_error('The field is required');
			}
			else
			{
				$this->db->trans_begin();
				$this->m_user->updateData('core.t_mtr_user',$dataCore,"id_seq=$id");

				$dataMaster=array(
					'shelter_id'=>$shelter,
					'terminal_id'=>$deviceTerminal,
					'lane_id'=>$laneId,
					'updated_by'=>$this->session->userdata('username'),
					'updated_on'=>date("Y-m-d H:i:s"),
				);

				$this->m_user->updateData('master.t_mtr_user_manless_gate',$dataMaster,"user_id=$id");

				if ($this->db->trans_status() === FALSE)
		        {
	                $this->db->trans_rollback();
	                echo $this->msg_error('Failed edit data');
		        }
		        else
		        {
	                $this->db->trans_commit();
	                echo $this->msg_success('Success edit data');
		        }
			}
		}

		// masuk ke manless gate juga tetapi shelter tidak di maikin user group 8
		else if ($user_group==8)
		{
			if (empty($user_group)||empty($deviceTerminal)|| empty($firstName))
			{
				echo $this->msg_error('The field is required');
			}
			else
			{
				$this->db->trans_begin();
				$this->m_user->updateData('core.t_mtr_user',$dataCore,"id_seq=$id");

				$dataMaster=array(
					'terminal_id'=>$deviceTerminal,
					'updated_by'=>$this->session->userdata('username'),
					'updated_on'=>date("Y-m-d H:i:s"),
				);

				$this->m_user->updateData('master.t_mtr_user_manless_gate',$dataMaster,"user_id=$id");
				
				if ($this->db->trans_status() === FALSE)
		        {
	                $this->db->trans_rollback();
	                echo $this->msg_error('Failed edit data');
		        }
		        else
		        {
	                $this->db->trans_commit();
	                echo $this->msg_success('Success edit data');
		        }
			}
		}

		else if ($user_group==4)
		{
			if (empty($user_group)||empty($deviceTerminal)|| empty($firstName))
			{
				echo $this->msg_error('The field is required');
			}
			else
			{
				$this->db->trans_begin();
				$this->m_user->updateData('core.t_mtr_user',$dataCore,"id_seq=$id");

				$dataMaster=array(
					'terminal_id'=>$deviceTerminal,
					'lastname'=>$lastName,
					'firstname'=>$firstName,
					'updated_by'=>$this->session->userdata('username'),
					'updated_on'=>date('Y-m-d H:i:s'),
				);

				$this->m_user->updateData('master.t_mtr_user_pos',$dataMaster,"user_id=$id");
				
				if ($this->db->trans_status() === FALSE)
		        {
	                $this->db->trans_rollback();
	                echo $this->msg_error('Failed edit data');
		        }
		        else
		        {
	                $this->db->trans_commit();
	                echo $this->msg_success('Success edit data');
		        }	
				
			}
		}

		else if ($user_group==5)
		{
			if (empty($user_group)|| empty($shelter)||empty($po)|| empty($firstName))
			{
				echo $this->msg_error('The field is required');
			}
			else
			{
				$this->db->trans_begin();
				$this->m_user->updateData('core.t_mtr_user',$dataCore,"id_seq=$id");

				$dataMaster=array(
					'shelter_id'=>$shelter,
					'po_id'=>$po,
					'updated_by'=>$this->session->userdata('username'),
					'updated_on'=>date('Y-m-d H:i:s'),
				);

				$this->m_user->updateData('master.t_mtr_user_validator',$dataMaster,"user_id=".$id);

				if ($this->db->trans_status() === FALSE)
		        {
	                $this->db->trans_rollback();
	                echo $this->msg_error('Failed edit data');
		        }
		        else
		        {
	                $this->db->trans_commit();
	                echo $this->msg_success('Success edit data');
		        }	
			}
		}

		else if ($user_group==9)
		{
			if (empty($merchant))
			{
				echo $this->msg_error('The field is required');
			}
			else
			{
				$dataCoreMerchant=array(
					'first_name'=>$merchant,
					'updated_by'=>$this->session->userdata('username'),
					'updated_on'=>date('Y-m-d H:i:s'),
				);

				$this->db->trans_begin();
				$this->m_user->updateData('core.t_mtr_user',$dataCoreMerchant,"id_seq=$id");

				$dataMaster=array(
					'merchant_name'=>$merchant,
					'updated_by'=>$this->session->userdata('username'),
					'updated_on'=>date("Y-m-d H:i:s"),
				);

				$this->m_user->updateData('master.t_mtr_user_b2b',$dataMaster,"user_id=$id");

				if($this->db->trans_status() === FALSE) {
		            $this->db->trans_rollback();
		            echo $this->msg_error("Failed edit data");
		        } 
		        else 
		        {
		            $this->db->trans_commit();
		            echo $this->msg_success("Success edit data");
		        }	
			}
		}

		else if ($user_group==11)
		{
			if (empty($firstName) || empty($po))
			{
				echo $this->msg_error('The field is required');
			}
			else
			{

				$this->db->trans_begin();
				$this->m_user->updateData('core.t_mtr_user',$dataCore,"id_seq=$id");

				$dataMaster=array(
					'po_id'=>$po,
					'updated_by'=>$this->session->userdata('username'),
					'updated_on'=>date("Y-m-d H:i:s"),
				);

				$this->m_user->updateData('master.t_mtr_user_po',$dataMaster,"user_id=$id");

				if($this->db->trans_status() === FALSE) {
		            $this->db->trans_rollback();
		            echo $this->msg_error("Failed edit data");
		        } 
		        else 
		        {
		            $this->db->trans_commit();
		            echo $this->msg_success("Success edit data");
		        }	
			}
		}

		else
		{
			if (empty($firstName) )
			{
				echo $this->msg_error('The field is required');
			}
			else
			{
				$updateCore=$this->m_global->update('core.t_mtr_user',$dataCore,"id_seq=$id");
				if($updateCore)
				{
					echo $this->msg_success('Success edit data');
				}
				else
				{
					echo $this->msg_error('Failed edit data');
				}
			}
		}
		
	}

	public function delete($id,$msg)
	{
		validateAjax();
		$id = decode($id);

		// mencari tau statusnya
		$checkStatus=$this->m_global->getDataById("core.t_mtr_user","id_seq=$id")->row();

		$checkStatus->status==1?$status=-1:$status=1;

		$dataCore = array(
			'updated_by' => $this->session->userdata('username'),
			'updated_on' => date("Y-m-d H:i:s"),
			'status' => $status,
		);

		$selectUser=$this->m_global->getDataById("core.t_mtr_user","id_seq=$id")->row();
		$idGroup=$selectUser->user_group_id;

		$deleteCore=$this->m_global->update('core.t_mtr_user',$dataCore,"id_seq=$id");

			if($idGroup==3)
			{
				$this->db->trans_begin();
				$this->m_user->delete('core.t_mtr_user',$dataCore,"id_seq=$id");
				
				$dataMaster=array(
								'updated_by' => $this->session->userdata('username'),
								'updated_on' => date("Y-m-d H:i:s"),
								'status' => $status);

				$this->m_user->delete('master.t_mtr_user_kiosk',$dataMaster,"user_id=$id");

				if($this->db->trans_status() === FALSE)
				{
					$this->db->trans_rollback();
					echo $this->msg_error('Failed '.$msg.' user');
				}
				else
				{
					$this->db->trans_commit();
					echo $this->msg_success('Success '.$msg.' user');
				}
			}

			else if($idGroup==4)
			{
				$this->db->trans_begin();
				$this->m_user->delete('core.t_mtr_user',$dataCore,"id_seq=$id");

				$dataMaster=array(
								'updated_by' => $this->session->userdata('username'),
								'updated_on' => date("Y-m-d H:i:s"),
								'status' => $status);

				$this->m_user->delete('master.t_mtr_user_pos',$dataMaster,"user_id=$id");


				if($this->db->trans_status() === FALSE)
				{
					$this->db->trans_rollback();
					echo $this->msg_error('Failed '.$msg.' user');
				}
				else
				{
					$this->db->trans_commit();
					echo $this->msg_success('Success '.$msg.' user');
				}

			}	
			else if($idGroup==5)
			{
				$this->db->trans_begin();
				$this->m_user->delete('core.t_mtr_user',$dataCore,"id_seq=$id");

				
				$dataMaster=array(
								'updated_by' => $this->session->userdata('username'),
								'updated_on' => date("Y-m-d H:i:s"),
								'status' => $status);

				$this->m_user->delete('master.t_mtr_user_validator',$dataMaster,"user_id=$id");
				
				if($this->db->trans_status() === FALSE)
				{
					$this->db->trans_rollback();
					echo $this->msg_error('Failed '.$msg.' user');
				}
				else
				{
					$this->db->trans_commit();
					echo $this->msg_success('Success '.$msg.' user');
				}
			}
			else if ($idGroup==6)
			{
				$this->db->trans_begin();
				$this->m_user->delete('core.t_mtr_user',$dataCore,"id_seq=$id");

				$dataMaster=array(
								'updated_by' => $this->session->userdata('username'),
								'updated_on' => date("Y-m-d H:i:s"),
								'status' => $status);

				$this->m_user->delete('master.t_mtr_user_boarding_gate',$dataMaster,"user_id=$id");

				if($this->db->trans_status() === FALSE)
				{
					$this->db->trans_rollback();
					echo $this->msg_error('Failed '.$msg.' user');
				}
				else
				{
					$this->db->trans_commit();
					echo $this->msg_success('Success '.$msg.' user');
				}
			}

			else if ($idGroup==7)
			{
				$this->db->trans_begin();
				$this->m_user->delete('core.t_mtr_user',$dataCore,"id_seq=$id");

				$dataMaster=array(
								'updated_by' => $this->session->userdata('username'),
								'updated_on' => date("Y-m-d H:i:s"),
								'status' => $status);

				$this->m_user->delete('master.t_mtr_user_manless_gate',$dataMaster,"user_id=$id");
				
				if($this->db->trans_status() === FALSE)
				{
					$this->db->trans_rollback();
					echo $this->msg_error('Failed '.$msg.' user');
				}
				else
				{
					$this->db->trans_commit();
					echo $this->msg_success('Success '.$msg.' user');
				}

			}
			
			else if ($idGroup==8)
			{
				$this->db->trans_begin();
				$this->m_user->delete('core.t_mtr_user',$dataCore,"id_seq=$id");

				$dataMaster=array(
							'updated_by' => $this->session->userdata('username'),
							'updated_on' => date("Y-m-d H:i:s"),
							'status' => $status);

				$this->m_user->delete('master.t_mtr_user_manless_gate',$dataMaster,"user_id=$id");
				
				if($this->db->trans_status() === FALSE)
				{
					$this->db->trans_rollback();
					echo $this->msg_error('Failed '.$msg.' user');
				}
				else
				{
					$this->db->trans_commit();
					echo $this->msg_success('Success '.$msg.' user');
				}

			}

			else if ($idGroup==9)
			{
				$this->db->trans_begin();
				$this->m_user->delete('core.t_mtr_user',$dataCore,"id_seq=$id");

				$dataMaster=array(
							'updated_by' => $this->session->userdata('username'),
							'updated_on' => date("Y-m-d H:i:s"),
							'status' => $status);

				$this->m_user->delete('master.t_mtr_user_b2b',$dataMaster,"user_id=$id");
				
				if($this->db->trans_status() === FALSE)
				{
					$this->db->trans_rollback();
					echo $this->msg_error('Failed '.$msg.' user');
				}
				else
				{
					$this->db->trans_commit();
					echo $this->msg_success('Success '.$msg.' user');
				}

			}

			else
			{
				$deleteCore=$this->m_global->update('core.t_mtr_user',$dataCore,"id_seq=$id");

				if($deleteCore)
				{
					echo $this->msg_success('Success '.$msg.' user');
				}
				else
				{
					echo $this->msg_error('Failed '.$msg.' user');
				}
			}

	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_user->get();
		echo json_encode($list);
	}

	public function getTerminalDevice()
	{
		$data=$this->m_user->getTerminalDevice();

		echo json_encode($data);
	}

	public function dataLane()
	{
		$shelterId=$this->input->post('id');
		
		$dataLane=$this->m_global->getDataById("master.t_mtr_lane","shelter_id=$shelterId and status=1 ")->result();

		echo json_encode ($dataLane);
	}

	public function getDevicePos()
	{
		// di hardcord user type pos adalah 2
		$data=$this->m_global->getDataById("master.t_mtr_device_terminal","terminal_type_id=2 and status=1 ")->result();

		echo json_encode ($data);
	}

	public function generateCode()
	{
		$character="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRXYZ";
		$string="";

		for($i=0;$i<8;$i++)
		{
			$pos=rand(0,strlen($character)-1);
			$string.=$character{$pos};
		}

		echo json_encode($string);

	}

}

/* End of file User.php */
/* Location: ./application/controllers/User.php */