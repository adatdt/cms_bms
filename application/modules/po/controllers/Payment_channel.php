<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_channel extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_pc');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Payment Channel";
		$data['content'] = "po/payment_channel/index";
		$data['po']=$this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		$data['pc']=$this->m_global->getData("master.t_mtr_payment_channel","where status=1 order by payment_channel asc");
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_pc->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$data['title'] = "Add Payment Channel";
		$data['po']=$this->m_global->getdata("master.t_mtr_po","where status=1 order by po_name asc");
		$data['pc']=$this->m_global->getdata("master.t_mtr_payment_channel","where status=1 order by payment_channel asc");
		$this->load->view("po/payment_channel/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$po = decode($this->input->post('po'));
		$pc = decode($this->input->post('pc'));
		$mid = $this->input->post('mid');

		$check_data = $this->m_pc->check_data($po,$pc);
		// echo json_encode($check_data);exit;

		if ($check_data->status == 1) {
			 echo $rest=$this->msg_error('Data already exist');
		}elseif ($check_data->status == -5) {
			$id = $check_data->id_seq;

			$data=array(
				'status'=>1,
				'mid'=>$mid,
				'created_by'=>$this->session->userdata('username'),
				'created_on'=>date('Y-m-d H:i:s'),
			);

			$delete=$this->m_global->update("master.t_mtr_payment_channel_detail",$data,"id_seq=$id");

			if ($delete)
			{
				echo $rest=$this->msg_success('Success add data');
			}else{
				echo $rest=$this->msg_error('Failed add data');
			}

			/* Fungsi Create Log */
			$createdBy   = $this->session->userdata('full_name');
			$logUrl      = site_url().'po/payment_channel/add';
			$logMethod   = 'ADD';
			$logParam    = json_encode($data);
			$logResponse = $rest;

			$this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
		}else{
			$data=array(
				'po_id'=>$po,
				'payment_channel_id'=>$pc,
				'mid'=>$mid,
				'status'=>1,
				'created_by'=>$this->session->userdata('username'),
			);

			$this->form_validation->set_rules('po', 'PO Bus', 'required');
			$this->form_validation->set_rules('pc', 'Payment Type Channel', 'required');

			if ($this->form_validation->run() == FALSE)
	        {
	            echo $rest=$this->msg_error('Please input the field!');
	        }

	        else
	        {
		       $insert=$this->m_global->insert("master.t_mtr_payment_channel_detail",$data);

				if ($insert)
				{
					echo $rest=$this->msg_success('Success add data');
				}
				else
				{
					 echo $rest=$this->msg_error('Failed add data');
				}
	        }

	        /* Fungsi Create Log */
	        $createdBy   = $this->session->userdata('full_name');
	        $logUrl      = site_url().'po/payment_channel/action_add';
	        $logMethod   = 'ADD';
	        $logParam    = json_encode($data);
	        $logResponse = $rest;

	        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	    }
	}

	function edit($id)
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$id = decode($id);
		$data['po'] = $this->m_global->getdata("master.t_mtr_po","where status=1 order by po_name asc");
		$data['pc'] = $this->m_global->getdata("master.t_mtr_payment_channel","where status=1 order by payment_channel asc");
		$data['detail'] = $this->m_pc->get_edit($id);
		$data['title'] = "Edit Payment Channel";
		$this->load->view("po/payment_channel/edit",$data);
	}

	function action_edit()
	{
		validateAjax();
		$this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'edit');
		$id = decode($this->input->post('id'));
		$po = decode($this->input->post('po'));
		$pc = decode($this->input->post('pc'));
		$mid = $this->input->post('mid');

		$check_data = $this->m_pc->check_data($po,$pc,$id);

		// echo json_encode($check_data);exit;

		if ($check_data->id_seq != $id) {
			if ($check_data->status == 1) {
				echo $rest=$this->msg_error('Data already exist');
			}
		}else{
			$data=array(
				'po_id' => $po,
				'payment_channel_id' => $pc,
				'mid' => $mid,
				'updated_by' => $this->session->userdata('username'),
				'updated_on' => date('Y-m-d H:i:s'),
			);

			// echo json_encode($data);exit;

			$this->form_validation->set_rules('po', 'PO Bus', 'required');
			$this->form_validation->set_rules('pc', 'Payment Type Channel', 'required');

	    	$this->db->trans_begin();
	    	$this->m_pc->update('master.t_mtr_payment_channel_detail',$data,"id_seq=$id");

	    	if ($this->db->trans_status() === FALSE)
	        {
	            $this->db->trans_rollback();
	            echo $rest=$this->msg_error('failed edit data');
	            
	        }else{
	            $this->db->trans_commit();
	            echo $rest=$this->msg_success('Success edit data');
	        }

	         /* Fungsi Create Log */
	        $createdBy   = $this->session->userdata('full_name');
	        $logUrl      = site_url().'po/payment_channel/action_edit';
	        $logMethod   = 'UPDATE';
	        $logParam    = json_encode($data);
	        $logResponse = $rest;

	        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
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

		$delete=$this->m_global->update("master.t_mtr_payment_channel_detail",$data,"id_seq=$id");

		if ($delete)
		{
			echo $rest=$this->msg_success('Success delete data');
		}else{
			echo $rest=$this->msg_error('Failed delete data');
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/payment_channel/delete';
        $logMethod   = 'DELETE';
        $logParam    = json_encode($data);
        $logResponse = $rest;

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

		$disabled = $this->m_global->masterDisable('master.t_mtr_payment_channel_detail',$id,$data);

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

		$disabled = $this->m_global->masterDisable('master.t_mtr_payment_channel_detail',$id,$data);

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