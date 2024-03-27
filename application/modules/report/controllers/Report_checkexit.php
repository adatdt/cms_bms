<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_checkexit extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_reportcheckexit');
	}

	public function index()
	{
		$data['title'] = "Report Terminal Exit";
		$data['content'] = "report_checkexit/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view('common/page',$data);
		// $this->load->view('tap_in/index');
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_reportcheckexit->getData();
		echo json_encode($list);
	}

	public function download()
	{

		$data = $this->m_checkexit->download()->result();
		
		$file_name = 'Transaction Terminal Exit';
		$this->load->library('XLSExcel');
		$styles1 = array('height'=>50, 'widths' => array(20,20,20,20,20,20,),'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');

		$header = array(
			'Transaction Date' =>'YYYY-MM-DD HH:MM:SS',
			'Shelther'=>'string',
			'Po Bus'=>'string',
			'Plate Number'=>'string',
			'Route'=>'string',
			'driver'=>'string',
		);

		foreach ($data as $key => $value) {
			$rows[] = array($value->created_on,
							$value->shelter_name,
							$value->po_name,
							$value->plate_number,
							$value->route_info,
							$value->driver_name,
						);
		}

		$writer = new XLSXWriter();

		$writer->writeSheetHeader('Sheet1', $header,$styles1);

		foreach($rows as $row)
			$writer->writeSheetRow('Sheet1', $row);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"');
		header('Cache-Control: max-age=0');
		$writer->writeToStdOut();
	}

}

/* End of file Gate_in.php */
/* Location: ./application/controllers/Gate_in.php */