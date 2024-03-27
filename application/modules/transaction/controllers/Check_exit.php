<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Check_exit extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_checkexit');
	}

	public function index()
	{
		// check user po
		$user_id=decode($this->session->userdata('user_id'));
		$check=$this->m_global->getDataById("master.t_mtr_user_po"," user_id=".$user_id);

		$data['title'] = "Terminal Exit";
		$data['content'] = "check_exit/index";
		$data['shelter'] = $this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc ");

		if ($check->num_rows()>0)
		{
			$data['user_po']=1;
			$data['po'] = $this->m_global->getDataById("master.t_mtr_po","id_seq=".$check->row()->po_id)->row();
		}
		else
		{
			$data['user_po']=0;
			$data['po'] = $this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc ");
		}
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view('common/page',$data);
		// $this->load->view('tap_in/index');
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_checkexit->getData();
		echo json_encode($list);
	}


	public function download()
	{

		$this->load->library('exceldownload');
		$data = $this->m_checkexit->download()->result();
		$excel = new Exceldownload();
		// Send Header
		$excel->setHeader('Transaction_Terminal_exit.xls');
		$excel->BOF();


		$excel->writeLabel(0, 0, "No");
		$excel->writeLabel(0, 1, "Transaction Date"); 
		$excel->writeLabel(0, 2, "UID");
		$excel->writeLabel(0, 3, "Shelther");
		$excel->writeLabel(0, 4, "Po Bus");
		$excel->writeLabel(0, 5, "Bus Name");
		$excel->writeLabel(0, 6, "Type");
		$excel->writeLabel(0, 7, "Plate Number");
		$excel->writeLabel(0, 8, "Route");
		$excel->writeLabel(0, 9, "driver");


		$index=1;
		foreach ($data as $key => $value) {
			$excel->writeLabel($index,0, $index);
			$excel->writeLabel($index,1, $value->created_on);
			$excel->writeLabel($index,2, $value->uid);
			$excel->writeLabel($index,3, $value->shelter_name);
			$excel->writeLabel($index,4, $value->po_name);
			$excel->writeLabel($index,5, $value->bus_name);
			$excel->writeLabel($index,6, $value->type);
			$excel->writeLabel($index,7, $value->plate_number);
			$excel->writeLabel($index,8, $value->route_info);
			$excel->writeLabel($index,9, $value->driver_name);


		$index++;
		}
		 
		$excel->EOF();
		exit();
	}

}

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */