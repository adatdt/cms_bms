<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_cms extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_usercms');
		$this->load->model('m_global');
		$this->load->library('log_activitytxt');
	}

	public function index()
	{
		$data['title'] = "User CMS";
		$data['content'] = "user/user_cms/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_usercms->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add User CMS";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("user/user_cms/add",$data);
	}

	public function action_add()
	{
		$name=trim($this->input->post('name'));
		$username=trim($this->input->post('username'));

		$data=array(

		);

		$this->form_validation->set_rules('busName', 'Bus Name', 'required');


		$checkPlate=$this->m_global->getDataById('master.t_mtr_bus',"plate_number='".$plateNumber."' and status=1")->num_rows();

		if ($checkPlate>0)
		{
			echo $rest=$this->msg_error('Plate number already exist');
		}

		else if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }

        else
        {
	       $insert=$this->m_global->insert("master.t_mtr_bus",$data);

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
        $logUrl      = site_url().'po/bus/action_add';
        $logMethod   = 'ADD';
        $logParam    = json_encode($data);
        $logResponse = $rest;

        $this->log_activitytxt->createLog($createdBy, $logUrl, $logMethod, $logParam, $logResponse);
	}

	function edit($id)
	{
		validateAjax();
		$id = decode($id);
		$data['po']=$this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		$data['type']=$this->m_global->getData("master.t_mtr_bus_type","where status=1 order by type asc");
		$data['bus']=$this->m_global->getDataById("master.t_mtr_bus","id_seq=$id")->row();
		$data['title'] = "Edit Bus";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("po/bus/edit",$data);

	}
	function action_edit()
	{
		$busName=$this->input->post('busName');
		$id=$this->input->post('id');
		$plateNumber=strtoupper(str_replace(' ', '',$this->input->post('plateNumber')));
		$po=$this->input->post('po');
		$type=$this->input->post('type');
		$seat=$this->input->post('seat');

		$data=array(
			'po_id'=>$po,
			'bus_type_id'=>$type,
			'bus_name'=>$busName,
			'plate_number'=>$plateNumber,
			'total_seat'=>$seat,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);

		$this->form_validation->set_rules('busName', 'Bus Name', 'required');
		$this->form_validation->set_rules('plateNumber', 'Plate Number', 'required');
		$this->form_validation->set_rules('po', 'PO Bus', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		$this->form_validation->set_rules('seat', 'Seat', 'required');

		$checkPlate=$this->m_global->getDataById('master.t_mtr_bus',"plate_number='".$plateNumber."' and id_seq !=$id and status=1")->num_rows();

		if ($checkPlate>0)
		{
			echo $rest=$this->msg_error('Plate number already exist');
		}

		else if ($this->form_validation->run() == FALSE)
        {
            echo $rest=$this->msg_error('Please input the field!');
        }
        else
        {
        	$update=$this->m_global->update('master.t_mtr_bus',$data,"id_seq=$id");

        	if ($update)
        	{
        		echo $rest=$this->msg_success('Success edit data');
        	}
        	else
        	{
        		echo $rest=$this->msg_error('failed edit data');
        	}
        }

         /* Fungsi Create Log */
        $createdBy   = $this->session->userdata('full_name');
        $logUrl      = site_url().'po/bus/action_edit';
        $logMethod   = 'UPDATE';
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

    	$delete=$this->m_global->update("master.t_mtr_bus",$data,"id_seq=$id");
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
        $logUrl      = site_url().'po/bus/delete';
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

	

}

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */