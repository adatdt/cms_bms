<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tap_in extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_tapin');
	}

	public function index()
	{
		$data['title'] = "Tap in";
		$data['content'] = "tap_in/index";
		// $data['po'] = $this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		$user_po=$this->m_global->getDataById("master.t_mtr_user_po","user_id=".decode($this->session->userdata('user_id')));

		if ($user_po->num_rows() > 0)
		{
			$data['user_po'] = 1;
			$data['po'] = $this->db->query("SELECT * FROM master.t_mtr_po WHERE status=1 and id_seq=".$user_po->row()->po_id)->row();
		}
		else
		{
			$data['user_po'] = 0;
			$data['po'] = $this->m_global->getData("master.t_mtr_po","where status=1 order by po_name asc");
		}
		$data['busType'] = $this->m_global->getData("master.t_mtr_bus_type","where status=1 order by type asc");
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view('common/page',$data);
		// $this->load->view('tap_in/index');
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_tapin->getData();
		echo json_encode($list);
	}

	// public function download()
	// {


	// 	$data = $this->m_tapin->download()->result();

		
	// 	$file_name = 'Transaction Tap in';
	// 	$this->load->library('XLSExcel');
	// 	$styles1 = array('height'=>50, 'widths' => array(20,20,20,20),'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

	// 	$header = array(
	// 		'Transaction Date' =>'YYYY-MM-DD HH:MM:SS',
	// 		'UID'=>'string',
	// 		'PO Bus'=>'string',
	// 		'Bus Name'=>'string',
	// 		'Type'=>'string',
	// 		'Plate Number'=>'string',
	// 	);

	// 	foreach ($data as $key => $value) {
	// 		$rows[] = array(
	// 						$value->created_on,
	// 						$value->uid,
	// 						$value->po_name,
	// 						$value->bus_name,
	// 						$value->type,
	// 						$value->plate_number,
	// 					);
	// 	}

	// 	$writer = new XLSXWriter();

	// 	$writer->writeSheetHeader('Sheet1', $header,$styles1);

	// 	foreach($rows as $row)
	// 	$writer->writeSheetRow('Sheet1', $row);
	// 	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	// 	header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
	// 	header('Cache-Control: max-age=0');
	// 	$writer->writeToStdOut();
	// }

	public function download()
	{ 
		$this->load->library('exceldownload');
		$data = $this->m_tapin->download()->result();
		$excel = new Exceldownload();
		// Send Header
		$excel->setHeader('Transaction_Tap_In.xls');
		$excel->BOF();
		
		$excel->writeLabel(0, 0, "No"); 
		$excel->writeLabel(0, 1, "Transaction Date");
		$excel->writeLabel(0, 2, "UID");
		$excel->writeLabel(0, 3, "PO Bus");
		$excel->writeLabel(0, 4, "Bus Name");
		$excel->writeLabel(0, 5, "Type");
		$excel->writeLabel(0, 6, "Plate Number");

		$index=1;
		foreach ($data as $key => $value) {
			$excel->writeLabel($index,0, $index);
			$excel->writeLabel($index,1, $value->created_on);
			$excel->writeLabel($index,2, $value->uid);
			$excel->writeLabel($index,3, $value->po_name);
			$excel->writeLabel($index,4, $value->bus_name);
			$excel->writeLabel($index,5, $value->type);
			$excel->writeLabel($index,6, $value->plate_number);

		$index++;
		}
		 
		$excel->EOF();
		exit();
	}

	public function clear($idTapin)
	{
		$id=decode($idTapin);
		$busId=$this->m_tapin->getBusId($id);

		$data=array(
			'status'=>0,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);


		$this->db->trans_begin();

		$this->m_tapin->update("trx.t_trx_tap_in",$data,"id_seq=$id");		
		
		
		if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            echo $this->msg_error("Failed force exit");
        }
        else
        {
            $this->db->trans_commit();
			echo $this->msg_success("Success force exit");
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

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */