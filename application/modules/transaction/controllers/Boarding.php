<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Boarding extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_boarding');
	}

	public function index()
	{
		// check apakah user po
		$user_id=decode($this->session->userdata('user_id'));
		$check=$this->m_global->getDataById("master.t_mtr_user_po","user_id=".$user_id);

		$data['title'] = "Boarding";
		$data['content'] = "boarding/index";

		if($check->num_rows()>0)
		{
			$data['po']=$this->m_global->getDataById("master.t_mtr_po","id_seq=".$check->row()->po_id)->row();
			$data['user_po']=1;
		}
		else
		{
			$data['po']= $this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
			$data['user_po']=0;
		}
		$data['shelter']=$this->m_global->getData("master.t_mtr_shelter","where status=1 order by shelter_name asc");
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view('common/page',$data);
		// $this->load->view('tap_in/index');
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_boarding->getData();
		echo json_encode($list);
	}


	public function download()
	{
		ini_set('memory_limit','1024M');
		
		$this->load->library('exceldownload');
		$data = $this->m_boarding->download()->result();
		$excel = new Exceldownload();
		// Send Header
		$excel->setHeader('Transaction_boarding.xls');
		$excel->BOF();

		$excel->writeLabel(0, 0, "No");
		$excel->writeLabel(0, 1, "Booking Code"); 
		$excel->writeLabel(0, 2, "Ticket Number");
		$excel->writeLabel(0, 3, "Transaction Date");
		$excel->writeLabel(0, 4, "Shelter");
		$excel->writeLabel(0, 5, "PO Bus");
		$excel->writeLabel(0, 6, "Route");
		$excel->writeLabel(0, 7, "Type/Class");
		$excel->writeLabel(0, 8, "Fare");
		// $excel->writeLabel(0, 9, "Status Integration");



		$index=1;
		foreach ($data as $key => $value) {
			$excel->writeLabel($index,0, $index);
			$excel->writeLabel($index,1, $value->booking_code);
			$excel->writeLabel($index,2, $value->ticket_code);
			$excel->writeLabel($index,3, $value->created_on);
			$excel->writeLabel($index,4, $value->shelter_name);
			$excel->writeLabel($index,5, $value->po_name);
			$excel->writeLabel($index,6, $value->route_info);
			$excel->writeLabel($index,7, $value->type);
			$excel->writeLabel($index,8, $value->price2);
			// $excel->writeLabel($index,9, $value->status_integration);


			// $excel->writeLabel($index,9, $value->driver_phone);

		$index++;
		}
		 
		$excel->EOF();
		exit();
	}

}

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */