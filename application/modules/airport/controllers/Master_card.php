<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Master_card extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_mastercard');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "Ticket Master";
		$data['content'] = "airport/master_card/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_mastercard->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add Ticket Master";
		$this->load->view("airport/master_card/add",$data);
	}

	public function action_add()
	{
		validateAjax();
		$name=trim($this->input->post('name'));
		$phone=trim($this->input->post('phone'));

		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('phone', 'Phone', 'required');

		if(substr($phone,0,2)=='62')
		{
			$phoneNo="0".substr($phone,2);
		}

		else if(substr($phone,0,3)=='+62')
		{
			$phoneNo="0".substr($phone,3);
		}
		else
		{
			$phoneNo=$phone;	
		}

		$ticketCode=$this->createCode();

		$data=array(
			'ticket_code'=>$ticketCode,
			'pic_name'=>$name,
			'pic_phone'=>$phoneNo,
			'created_by'=>$this->session->userdata('username'),
			'status'=>1,
			'qr_code'=>"MST-".md5($ticketCode),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field');
        }
        else if(!is_numeric($phone))
        {
        	echo $rest=$this->msg_error('Phone number must be numerik !');
        }
        else
        {
        	$insert=$this->m_global->insert("master.t_mtr_qr_boarding",$data);
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
        $logUrl      = site_url().'airport/master_card/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);

	}

	function edit($id)
	{
		validateAjax();
		$id = decode($id);
		$data['title'] = "Edit Ticket Master ";
		$data['edit'] = $this->m_global->getDataById("master.t_mtr_qr_boarding","id_seq=$id")->row();
		$this->load->view("airport/master_card/edit",$data);

	}

	function action_edit()
	{
		validateAjax();
		$id = decode($this->input->post('id'));
		$name=trim($this->input->post('name'));
		$phone=trim($this->input->post('phone'));

		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('phone', 'Phone', 'required');

		if(substr($phone,0,2)=='62')
		{
			$phoneNo="0".substr($phone,2);
		}

		else if(substr($phone,0,3)=='+62')
		{
			$phoneNo="0".substr($phone,3);
		}
		else
		{
			$phoneNo=$phone;	
		}

		$data=array(
			'pic_name'=>$name,
			'pic_phone'=>$phoneNo,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field');
        }
        else if(!is_numeric($phone))
        {
        	echo $rest=$this->msg_error('Phone number must be numerik !');
        }
        else
        {
        	$update=$this->m_global->update("master.t_mtr_qr_boarding",$data,"id_seq='".$id."'");
			if ($update)
			{
				echo $rest=$this->msg_success('Success update data');
			}
			else
			{
				echo $rest=$this->msg_error('Failed update data');
			}
        }	

        /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'airport/master_card/action_edit';
        $logMethod   = 'EDIT';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);	
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

    	$delete=$this->m_global->update("master.t_mtr_qr_boarding",$data,"id_seq=$id");
		if ($delete)
		{
			echo $rest=$this->msg_success('Success delete data');
		}
		else
		{
			echo $rest=$this->msg_error('Failed delete data');
		}

		/* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'airport/master_card/delete';
        $logMethod   = "DELETE";
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);	
	}

	function download()
	{
		$this->load->library('exceldownload');
		$data = $this->m_mastercard->download()->result();
		$excel = new Exceldownload();
		// Send Header
		$excel->setHeader('Transaction_boarding.xls');
		$excel->BOF();


		$excel->writeLabel(0, 0, "No");
		$excel->writeLabel(0, 1, "Ticket Code"); 
		$excel->writeLabel(0, 2, "PIC Name");
		$excel->writeLabel(0, 3, "PIC Phone");
		$excel->writeLabel(0, 4, "Qr Code");

		$index=1;
		foreach ($data as $key => $value) {
			$excel->writeLabel($index,0, $index);
			$excel->writeLabel($index,1, $value->ticket_code);
			$excel->writeLabel($index,2, $value->pic_name);
			$excel->writeLabel($index,3, $value->pic_phone);
			$excel->writeLabel($index,4, $value->qr_code);


		$index++;
		}
		 
		$excel->EOF();
		exit();
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

	function createCode()
	{
		$chekCode=$this->db->query("select * from master.t_mtr_qr_boarding where left(ticket_code,9)='MST".date('ymd')."'")->num_rows();

		if($chekCode<1)
		{
			$shelterCode="MST".date('ymd')."001";
			return $shelterCode;
		}
		else
		{
			$max=$this->db->query("select max (ticket_code) as max_code from master.t_mtr_qr_boarding where left(ticket_code,9)='MST".date('ymd')."'")->row();
			$kode=$max->max_code;
			$noUrut = (int) substr($kode, 9, 3);
			$noUrut++;
			$char = "MST".date("ymd");
			$shelterCode = $char . sprintf("%03s", $noUrut);
			return $shelterCode;
		}
	}

}