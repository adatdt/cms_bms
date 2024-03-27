<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pids extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_pids');
		$this->load->model('m_global');
	}

	public function index()
	{
		$data['title'] = "Master PIDS";
		$data['content'] = "pids/pids/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['shelter'] = $this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc ");
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
		// $this->load->view('tap_in/index');
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_pids->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add PO";
		$data['po']=$this->m_global->getdata("master.t_mtr_po","where status=1 order by po_name asc");
		$data['type']=$this->m_global->getdata("master.t_mtr_bus_type","where status=1 order by type asc");
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("po/bus/add",$data);
	}

	public function action_add()
	{
		$busName=$this->input->post('busName');
		$plateNumber=str_replace(' ', '',$this->input->post('plateNumber'));
		$po=$this->input->post('po');
		$type=$this->input->post('type');
		$seat=$this->input->post('seat');

		$data=array(
			'po_id'=>$po,
			'bus_type_id'=>$type,
			'bus_name'=>$busName,
			'plate_number'=>$plateNumber,
			'total_seat'=>$seat,
			'status'=>1,
			'created_by'=>$this->session->userdata('username'),
			'created_on'=>date('Y-m-d H:i:s'),
		);

		$this->form_validation->set_rules('busName', 'Bus Name', 'required');
		$this->form_validation->set_rules('plateNumber', 'Plate Number', 'required');
		$this->form_validation->set_rules('po', 'PO Bus', 'required');
		$this->form_validation->set_rules('type', 'Type', 'required');
		$this->form_validation->set_rules('seat', 'Seat', 'required');

		$checkPlate=$this->m_global->getDataById('master.t_mtr_bus',"plate_number='".$plateNumber."' ")->num_rows();

		if ($checkPlate>0)
		{
			echo $this->msg_error('Plate number already exist');
		}

		else if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field!');
        }

        else
        {
	       $insert=$this->m_global->insert("master.t_mtr_bus",$data);

			if ($insert)
			{
				echo $this->msg_success('Success add data');
			}
			else
			{
				 echo $this->msg_error('Failed add data');
			}
        }
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
		$plateNumber=str_replace(' ', '',$this->input->post('plateNumber'));
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

		$checkPlate=$this->m_global->getDataById('master.t_mtr_bus',"plate_number='".$plateNumber."' and id_seq !=$id")->num_rows();

		if ($checkPlate>0)
		{
			echo $this->msg_error('Plate number already exist');
		}

		else if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field!');
        }
        else
        {
        	$update=$this->m_global->update('master.t_mtr_bus',$data,"id_seq=$id");

        	if ($update)
        	{
        		echo $this->msg_success('Success edit data');
        	}
        	else
        	{
        		echo $this->msg_error('failed edit data');
        	}
        }				
	}
	
	public function delete($id)
	{
		validateAjax();
		$id = decode($id);

		$delete=$this->m_global->deleteData("master.t_mtr_pids","id_seq=$id");

		if ($delete)
		{
			echo $this->msg_success('Success delete data');
		}
		else
		{
			echo $this->msg_error('Failed delete data');	
		}

		// $data=array(
		// 	'status'=>-5,
		// 	'updated_by'=>$this->session->userdata('username'),
		// 	'updated_on'=>date('Y-m-d H:i:s'),
		// );

  //   	$delete=$this->m_global->update("master.t_mtr_bus",$data,"id_seq=$id");
		// if ($delete)
		// {
		// 	echo $this->msg_success('Success delete data');
		// }
		// else
		// {
		// 	echo $this->msg_error('Failed delete data');
		// }
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
		$noUrut = (int) substr($kode, 3, 3);
		$noUrut++;
		$char = "PO";
		$poCode = $char . sprintf("%03s", $noUrut);
		return $poCode;
	}
	

}

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */