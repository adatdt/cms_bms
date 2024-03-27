<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Po extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_po');
		$this->load->model('m_global');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Master PO";
		$data['content'] = "po/po/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
		// $this->load->view('tap_in/index');
	}

	public function getList()
	{
		// validateAjax();
		$list = $this->m_po->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add PO";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("po/po/add",$data);
	}

	public function action_add()
	{
		$config['upload_path']          = './assets/images/po_icon/';
		$config['allowed_types']        = 'jpeg|jpg|png';
		$config['max_size']             = 10000;
		$config['max_width']            = 10240;
		$config['max_height']           = 7680;

		$this->load->library('upload', $config);

		$uploaded = $this->upload->do_upload('icon');
		$file_name = "";

		if ($uploaded) {
			$upload_data = $this->upload->data();
			$file_name = $upload_data['file_name'];
		}

		$poName=trim($this->input->post('poName'));
		$picEmail=trim($this->input->post('picEmail'));
		$picName=trim($this->input->post('picName'));
		$picPhone=trim($this->input->post('picPhone'));
		$poAddress=trim($this->input->post('poAddress'));
		$prefix_qr=trim($this->input->post('prefix_qr'));
		$prefix=strtoupper(str_replace(' ', '',$this->input->post('prefix')));

		if(substr($picPhone,0,2)=='62')
		{
			$phoneNo="0".substr($picPhone,2);
		}

		else if(substr($picPhone,0,3)=='+62')
		{
			$phoneNo="0".substr($picPhone,3);
		}
		else
		{
			$phoneNo=$picPhone;	
		}

		$this->form_validation->set_rules('poName', 'Po', 'required');
		$this->form_validation->set_rules('picPhone', 'Pic Phone', 'required');
		$this->form_validation->set_rules('poAddress', 'Address', 'required');
		$this->form_validation->set_rules('picEmail', 'Email', 'required');
		$this->form_validation->set_rules('prefix', 'Prefix', 'required');

		if(empty($prefix_qr))
		{

			$data=array(
				'po_name'=>$poName,
				'prefix'=>$prefix,
				'po_code'=>$this->createPoCode(),
				'pic_name'=>$picName,
				'pic_email'=>$picEmail,
				'pic_phone'=>$phoneNo,
				'address'=>$poAddress,
				'status'=>1,
				'created_on'=>date('Y-m-d H:i:s'),
				'created_by'=>$this->session->userdata('username'),
				'icon'=>$file_name,
			);
			
			$checkPrefix=$this->m_global->getDataById('master.t_mtr_po',"prefix='".$prefix."' and status=1")->num_rows();
			$checkCharacter=strlen($prefix);

			$checkPoName=$this->m_global->getDataById('master.t_mtr_po',"upper(po_name)=upper('".$poName."') and status=1")->num_rows();

			
			if ($this->form_validation->run() == FALSE)
	        {
	            echo $rest=$this->msg_error('Please input the field!');
	        }
	        else if($checkPoName)
	        {
	        	echo $rest=$this->msg_error('PO name already in use');
	        }
	        else if($checkPrefix>0)
	        {
	        	echo $rest=$this->msg_error('Prefix already in use');
	        }
	        else if($prefix_qr==='0')
	        {
	        	echo $rest=$this->msg_error('Prefix integration already in use');
	        }
	        else if($checkCharacter>2)
	        {
	        	echo $rest=$this->msg_error('Max prefix 2 Character');
	        }
	        else if(!is_numeric($picPhone))
	        {
	        	echo $rest=$this->msg_error('Phone number must be numerik !');
	        }
	        else
	        {
	        	$this->db->trans_begin();

	        	$this->m_po->insert("master.t_mtr_po",$data);
				$maxIdPo=$this->db->query("select max(id_seq) as max_id from master.t_mtr_po")->row_array();
				$idPo=$maxIdPo['max_id'];
				$dataShelter=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by id_seq asc");
				foreach ($dataShelter as $dataShelter)
		        {
		            $data2[] = array(
		                'po_id' =>$idPo, 
		                'airport_id'=>1, // masih satu airport
		                'shelter_id' => $dataShelter->id_seq,
		                'queue_number'=>0,
		                'status'=>1,
		                'created_by'=>$this->session->userdata('username'),
		                );
		        }
			    $this->m_po->insertQueue($data2);

				if($this->db->trans_status() === FALSE)
				{
					$this->db->trans_rollback();
					echo $rest=$this->msg_error('Failed add Queue');					
				}
				else
				{
					$this->db->trans_commit();
					echo $rest=$this->msg_success('success add data');
				}

	        }
		}

		else
		{
			$data=array(
				'po_name'=>$poName,
				'prefix'=>$prefix,
				'po_code'=>$this->createPoCode(),
				'pic_name'=>$picName,
				'pic_email'=>$picEmail,
				'pic_phone'=>$phoneNo,
				'address'=>$poAddress,
				'prefix_qr'=>$prefix_qr,
				'status'=>1,
				'created_on'=>date('Y-m-d H:i:s'),
				'created_by'=>$this->session->userdata('username'),
				'icon'=> $file_name,
			);
			
			$checkPrefix=$this->m_global->getDataById('master.t_mtr_po',"prefix='".$prefix."' and status=1")->num_rows();
			$checkCharacter=strlen($prefix);

			$checkPrefixQr=$this->m_global->getDataById('master.t_mtr_po',"prefix_qr='".$prefix_qr."' and status=1")->num_rows();

			
			if ($this->form_validation->run() == FALSE)
	        {
	            echo $rest=$this->msg_error('Please input the field!');
	        }
	        else if($checkPrefix>0)
	        {
	        	echo $rest=$this->msg_error('Prefix already in use');
	        }
	        else if($prefix_qr==='0')
	        {
	        	echo $rest=$this->msg_error('Prefix integration already in use');
	        }
	        else if($checkPrefixQr>0)
	        {
	        	echo $rest=$this->msg_error('Prefix integration already in use');
	        }
	        else if($prefix_qr<0)
	        {
	        	echo $rest=$this->msg_error('Prefix integration must be numeric');
	        }
	        else if($checkCharacter>2)
	        {
	        	echo $rest=$this->msg_error('Max prefix 2 Character');
	        }
	        else if(!is_numeric($picPhone))
	        {
	        	echo $rest=$this->msg_error('Phone number must be numerik !');
	        }
	        else
	        {
	        	$this->db->trans_begin();

	        	$this->m_po->insert("master.t_mtr_po",$data);
				$maxIdPo=$this->db->query("select max(id_seq) as max_id from master.t_mtr_po")->row_array();
				$idPo=$maxIdPo['max_id'];
				$dataShelter=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by id_seq asc");
				foreach ($dataShelter as $dataShelter)
		        {
		            $data2[] = array(
		                'po_id' =>$idPo, 
		                'airport_id'=>1, // masih satu airport
		                'shelter_id' => $dataShelter->id_seq,
		                'queue_number'=>0,
		                'status'=>1,
		                'created_by'=>$this->session->userdata('username'),
		                );
		        }
			    $this->m_po->insertQueue($data2);

				if($this->db->trans_status() === FALSE)
				{
					$this->db->trans_rollback();
					echo $rest=$this->msg_error('Failed add Queue');					
				}
				else
				{
					$this->db->trans_commit();
					echo $rest=$this->msg_success('success add data');
				}

	        }
    	}

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/po/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

	}

	function edit($id)
	{
		validateAjax();
		$id = decode($id);
		$data['po'] = $this->m_global->getDataById('master.t_mtr_po',"id_seq=$id ")->row();
		$data['icon'] = $this->m_po->getIcon($id);
		$icon = $this->m_po->getIcon($id);
		$data['id'] = encode($id);
		$data['title'] = "Edit PO";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));

		//pengecekan yang pernah transaksi
		$checkTapIn=$this->m_global->getDataById("trx.t_trx_tap_in","po_id=$id")->num_rows();
		$checkbooking=$this->m_global->getDataById("trx.t_trx_Booking","po_id=$id")->num_rows();

		$checkTapIn2=$this->m_global->getDataById("trx.t_trx_tap_in","po_id=$id and status=1")->num_rows();
		$checkTapOut2=$this->m_global->getDataById("trx.t_trx_tap_out","po_id=$id and status=1")->num_rows();

		if ($checkTapIn>0 or $checkbooking>0)
		{
			$this->load->view("po/po/edit2",$data);
		}
		else
		{
			$this->load->view("po/po/edit",$data);
		}

	}
	
	function action_edit()
	{
		$config['upload_path']          = './assets/images/po_icon/';
		$config['allowed_types']        = 'jpeg|jpg|png';
		$config['max_size']             = 10000;
		$config['max_width']            = 10240;
		$config['max_height']           = 7680;

		$this->load->library('upload', $config);

		$uploaded = $this->upload->do_upload('icon');
		$file_name = $this->m_po->get_icon_name(decode($this->input->post('id')));

		if ($uploaded) {
			// $path_to_file = $this->m_po->get_path_icon();
			$path_to_file = "assets/images/po_icon/";
			$get_icon_name = $this->m_po->get_icon_name(decode($this->input->post('id')));

			if ($get_icon_name) {
				$icon_to_delete = $path_to_file . $get_icon_name;
				if (file_exists($icon_to_delete)) {
					unlink($icon_to_delete);
				}
			}

			$upload_data = $this->upload->data();
			$file_name = $upload_data['file_name'];
		}

		$poName=trim($this->input->post('poName'));
		$picEmail=trim($this->input->post('picEmail'));
		$picName=trim($this->input->post('picName'));
		$picPhone=trim($this->input->post('picPhone'));
		$poAddress=trim($this->input->post('poAddress'));
		$prefix_qr=trim($this->input->post('prefix_qr'));
		$id=decode($this->input->post('id'));

		if(substr($picPhone,0,2)=='62')
		{
			$phoneNo="0".substr($picPhone,2);
		}

		else if(substr($picPhone,0,3)=='+62')
		{
			$phoneNo="0".substr($picPhone,3);
		}
		else
		{
			$phoneNo=$picPhone;	
		}

		$this->form_validation->set_rules('poName', 'Po', 'required');
		$this->form_validation->set_rules('picPhone', 'pic Phone', 'required');
		$this->form_validation->set_rules('poAddress', 'Address', 'required');
		$this->form_validation->set_rules('picEmail', 'Email', 'required');

		$checkFare=$this->m_global->getDataById("master.t_mtr_fare","po_id=$id and status=1")->num_rows();
		$checkDriver=$this->m_global->getDataById("master.t_mtr_driver","po_id=$id and status=1")->num_rows();
		$checkBus=$this->m_global->getDataById("master.t_mtr_bus","po_id=$id and status=1")->num_rows();

		//pengecekan yang pernah transaksi
		$checkTapIn=$this->m_global->getDataById("trx.t_trx_tap_in","po_id=$id")->num_rows();
		$checkbooking=$this->m_global->getDataById("trx.t_trx_Booking","po_id=$id")->num_rows();

		// cek po name
		$checkPoName=$this->m_global->getDataById('master.t_mtr_po',"upper(po_name)=upper('".$poName."') and status=1 and id_seq !=$id")->num_rows();
		//jika prefix integrasii qrnya kosong
		if(empty($prefix_qr))
		{
			$data=array(
				'po_name'=>$poName,
				'pic_name'=>$picName,
				'pic_email'=>$picEmail,
				'pic_phone'=>$phoneNo,
				'address'=>$poAddress,
				'updated_by'=>$this->session->userdata('username'),
				'updated_on'=>date('Y-m-d H:i:s'),
				'icon'=>$file_name,
			);
			
			if ($this->form_validation->run() == FALSE)
	        {
	            echo $res=$this->msg_error('Please input the field!');
	        }
	        else if ($checkPoName>0)
	        {
	            echo $res=$this->msg_error('PO name already in use');
	        }
	        else if ($prefix_qr==='0')
	        {
	            echo $res=$this->msg_error('Prefix integration already in use');
			}
			// this comment by Kusnadi
	        // else if($checkbooking>0 or $checkTapIn>0)
	        // {
	        // 	echo $res=$this->msg_error('Cannot update, Po in transaction ');	
	        // }
	        else
	        {
	        	$update=$this->m_global->update("master.t_mtr_po",$data,"id_seq='".$id."'");
				if ($update)
				{
					echo $res=$this->msg_success('Success update data');
				}
				else
				{
					echo $res=$this->msg_error('Failed update data');
				}
	        }
    	}
    	else
    	{
    		$data=array(
				'po_name'=>$poName,
				'pic_name'=>$picName,
				'pic_email'=>$picEmail,
				'pic_phone'=>$phoneNo,
				'address'=>$poAddress,
				'prefix_qr'=>$prefix_qr,
				'updated_by'=>$this->session->userdata('username'),
				'updated_on'=>date('Y-m-d H:i:s'),
				'icon'=>$file_name,
			);

			$checkPrefixQr=$this->m_global->getDataById("master.t_mtr_po","prefix_qr='".$prefix_qr."' and status=1 ")->num_rows();

			if ($this->form_validation->run() == FALSE)
	        {
	            echo $res=$this->msg_error('Please input the field!');
	        }
	        else if (!is_numeric($prefix_qr))
	        {
	        	echo $res=$this->msg_error('Prefix integration must be numeric');
			}
			// this comment by Kusnadi
	        // else if($checkbooking>0 or $checkTapIn>0)
	        // {
	        // 	echo $res=$this->msg_error('Cannot update, Po in transaction ');	
	        // }
	        else if ($prefix_qr<0)
	        {
	        	echo $res=$this->msg_error('Prefix integration must be numeric');	
	        }
	        else if($checkPrefixQr>0  )
	        {
	        	echo $res=$this->msg_error('Prefix integration already in use');
	        }
	        else
	        {
	        	$update=$this->m_global->update("master.t_mtr_po",$data,"id_seq='".$id."'");
				if ($update)
				{
					echo $res=$this->msg_success('Success update data');
				}
				else
				{
					echo $res=$this->msg_error('Failed update data');
				}
	        }
    	}

        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/po/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);		
	}

	function action_edit2()
	{
		$config['upload_path']          = './assets/images/po_icon/';
		$config['allowed_types']        = 'jpeg|jpg|png';
		$config['max_size']             = 10000;
		$config['max_width']            = 10240;
		$config['max_height']           = 7680;

		$this->load->library('upload', $config);

		$uploaded = $this->upload->do_upload('icon');
		$file_name = $this->m_po->get_icon_name(decode($this->input->post('id')));

		if ($uploaded) {
			$path_to_file = "assets/images/po_icon/";
			// $path_to_file = $this->m_po->get_path_icon();
			$get_icon_name = $this->m_po->get_icon_name(decode($this->input->post('id')));

			if ($get_icon_name) {
				$icon_to_delete = $path_to_file . $get_icon_name;
				if (file_exists($icon_to_delete)) {
					unlink($icon_to_delete);
				}
			}
			
			$upload_data = $this->upload->data();
			$file_name = $upload_data['file_name'];
		}

		$picEmail=trim($this->input->post('picEmail'));
		$picName=trim($this->input->post('picName'));
		$picPhone=trim($this->input->post('picPhone'));
		$poAddress=trim($this->input->post('poAddress'));
		$prefix_qr=trim($this->input->post('prefix_qr'));
		$id=decode($this->input->post('id'));

		if(substr($picPhone,0,2)=='62')
		{
			$phoneNo="0".substr($picPhone,2);
		}

		else if(substr($picPhone,0,3)=='+62')
		{
			$phoneNo="0".substr($picPhone,3);
		}
		else
		{
			$phoneNo=$picPhone;	
		}

		$this->form_validation->set_rules('picPhone', 'PIC Phone', 'required');
		$this->form_validation->set_rules('picName', 'PIC Name', 'required');
		$this->form_validation->set_rules('poAddress', 'Address', 'required');
		$this->form_validation->set_rules('picEmail', 'Email', 'required');

		//pengecekan jika po dalam transaksi transaksi
		$checkTapIn=$this->m_global->getDataById("trx.t_trx_tap_in","po_id=$id and status=1")->num_rows();
		$checkTapOut=$this->m_global->getDataById("trx.t_trx_tap_out","po_id=$id and status=1")->num_rows();

		//jika prefixnya integrasinya kosong
		if(empty($prefix_qr))
		{
			$data=array(
				'pic_name'=>$picName,
				'pic_email'=>$picEmail,
				'pic_phone'=>$phoneNo,
				'address'=>$poAddress,
				'updated_by'=>$this->session->userdata('username'),
				'updated_on'=>date('Y-m-d H:i:s'),
				'icon'=>$file_name,
			);
			
			if ($this->form_validation->run() == FALSE)
	        {
	            echo $res=$this->msg_error('Please input the field!');
	        }
	        else if ($prefix_qr==='0')
	        {
	            echo $res=$this->msg_error('Prefix integration already in use');
			}
			// this comment by Kusnadi
	        // else if( $checkTapIn>0 or $checkTapOut>0)
	        // {
	        // 	echo $res=$this->msg_error('Cannot update, Po in transaction ');	
	        // }
	        else
	        {
	        	$update=$this->m_global->update("master.t_mtr_po",$data,"id_seq='".$id."'");
				if ($update)
				{
					echo $res=$this->msg_success('Success update data');
				}
				else
				{
					echo $res=$this->msg_error('Failed update data');
				}
	        }
    	}

    	//jika prefixnya keisi
    	else
    	{
    		$data=array(
				'pic_name'=>$picName,
				'pic_email'=>$picEmail,
				'pic_phone'=>$phoneNo,
				'address'=>$poAddress,
				'prefix_qr'=>$prefix_qr,
				'updated_by'=>$this->session->userdata('username'),
				'updated_on'=>date('Y-m-d H:i:s'),
				'icon'=>$file_name,
			);

			$checkPrefixQr=$this->m_global->getDataById("master.t_mtr_po","prefix_qr='".$prefix_qr."' and status=1 ")->num_rows();
			
			if ($this->form_validation->run() == FALSE)
	        {
	            echo $res=$this->msg_error('Please input the field!');
	        }
	        else if (!is_numeric($prefix_qr))
	        {
	        	echo $res=$this->msg_error('Prefix integration must be numeric');
	        }
	        else if ($prefix_qr<0)
	        {
	        	echo $res=$this->msg_error('Prefix integration must be numeric');	
	        }
	        else if($checkPrefixQr>0  )
	        {
	        	echo $res=$this->msg_error('Prefix integration already in use');
	        }
			// this comment by Kusnadi
			// else if( $checkTapIn>0 or $checkTapOut>0)
	        // {
	        // 	echo $res=$this->msg_error('Cannot update, Po in transaction ');	
	        // }
	        else
	        {
	        	$update=$this->m_global->update("master.t_mtr_po",$data,"id_seq='".$id."'");
				if ($update)
				{
					echo $res=$this->msg_success('Success update data');
				}
				else
				{
					echo $res=$this->msg_error('Failed update data');
				}
	        }
    	}

        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/po/action_edit';
        $logMethod   = 'UPDATE';
        $logParam    = json_encode($data);
        $logResponse = $res;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);		
	}

	public function disable($id)
	{
		$id = decode($id);
		$data = array(
			'id_seq' => $id,
			'status' => -1,
			'updated_by' => $this->session->userdata('username'),
			'updated_on' => date('Y-m-d H:i:s'),
		);

		$disabled = $this->m_global->masterDisable('master.t_mtr_po',$id,$data);

		if ($disabled) {
			echo json_encode(array(
				'code' => 200,
				'message' => 'Data successfully disabled',
			));
		}else{
			echo json_encode(array(
				'code' => 101,
				'message' => 'Please contact admin',
			));
		}
	}

	public function enable($id)
	{
		$id = decode($id);
		$data = array(
			'id_seq' => $id,
			'status' => 1,
			'updated_by' => $this->session->userdata('username'),
			'updated_on' => date('Y-m-d H:i:s'),
		);

		$disabled = $this->m_global->masterDisable('master.t_mtr_po',$id,$data);

		if ($disabled) {
			echo json_encode(array(
				'code' => 200,
				'message' => 'Data successfully enabled',
			));
		}else{
			echo json_encode(array(
				'code' => 101,
				'message' => 'Please contact admin',
			));
		}
	}
	
	public function delete($id)
	{
		validateAjax();
		$id = decode($id);

		$data=array(
			'status'=>-5,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);


		//pengecekan jika data sudah pernah melakukan di transaksi
		$checkTapIn=$this->m_global->getDataById("trx.t_trx_tap_in","po_id=$id")->num_rows();
		$checkBooking=$this->m_global->getDataById("trx.t_trx_booking","po_id=$id")->num_rows();

		//pengecekan apah sudah di pairing
		$checkFare=$this->m_global->getDataById("master.t_mtr_fare","po_id=$id and status=1")->num_rows();
		$checkDriver=$this->m_global->getDataById("master.t_mtr_driver","po_id=$id and status=1")->num_rows();
		$checkBus=$this->m_global->getDataById("master.t_mtr_bus","po_id=$id and status=1")->num_rows();

		if($checkTapIn>0 or $checkBooking>0)
		{
			echo $rest=$this->msg_error("Cannot delete, PO in transaction");
		}
		else if($checkFare>0)
		{
			echo $rest=$this->msg_error("Cannot delete, PO already paired to fare");
		}
		else if($checkDriver>0)
		{
			echo $rest=$this->msg_error("Cannot delete, PO already paired to driver");
		}
		else if($checkBus>0)
		{
			echo $rest=$this->msg_error("Cannot delete, PO already paired to bus");
		}
		else
		{
			$this->db->trans_begin();
	    	$this->m_po->update("master.t_mtr_po",$data,"id_seq=$id");
			$this->m_po->update("master.t_mtr_po_queue",$data,"po_id=$id");

			if($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
				echo $rest=$this->msg_error('Failed delete data');			
			}
			else
			{
				$this->db->trans_commit();
				echo $rest=$this->msg_success('Success delete data');	
			}
		}
		
		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/po/delete';
        $logMethod   = 'DELETE';
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

	function createPoCode()
	{

		$max=$this->db->query("select max (po_code) as max_code from master.t_mtr_po")->row();
		$kode=$max->max_code;
		$noUrut = (int) substr($kode, 2, 3);
		$noUrut++;
		$char = "PO";
		$poCode = $char . sprintf("%03s", $noUrut);
		return $poCode;
	}
	

}

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */