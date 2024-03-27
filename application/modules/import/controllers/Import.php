<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_import');
		$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		getSession();
	}

	public function index()
	{
		$data['title'] = "Import";
		$data['content'] = "import";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view('common/page',$data);
	}

	public function get_checklist($param)
	{
		$checklist = null;
		if ($param != null && $param !='X') {
			$checklist = 1;
		}else{
			$checklist = 0;
		}

		return $checklist;
	}

	public function get_date($param)
	{
		$date = null;
		if ($param != null && $param != '') {
			$date = date('Y-m-d', strtotime($param));
		}

		return $date;
	}

	public function get_salary($param)
	{
		$salary = null;
		$salary = (int)str_replace(',','',trim($param,"Rp. "));

		return $salary;
	}

	public function upload()
	{
		ini_set('memory_limit', -1 );
		$month_salary = $this->input->post('month_salary');
		$fileName = time().$_FILES['file']['name'];

		$config['upload_path'] = './assets/upload/';
		$config['file_name'] = $fileName;
		$config['allowed_types'] = 'xls|xlsx|csv';
		$config['max_size'] = 10000;

		$this->load->library('upload');
		$this->upload->initialize($config);

		if(! $this->upload->do_upload('file') )
			$this->upload->display_errors();

		$media = $this->upload->data('file');
		$inputFileName = base_url().'assets/upload/'.$config['file_name'];
		$inputFileName = base_url().'assets/upload/'.$config['file_name'];
		delete_files($config['upload_path'],TRUE);
		try {
			$inputFileType = IOFactory::identify($_FILES['file']['tmp_name']);
			$objReader = IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($_FILES['file']['tmp_name']);
		}catch(Exception $e){
			die('Error loading file "'.pathinfo(($_FILES['file']['tmp_name']),PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		$sheetCount = $objPHPExcel->getSheetCount();
		$t_user_employee = array();
		$t_mtr_alocate_salary = array();
		$t_salary_employee = array();
		$data_dua = array();
		$data_addendum = array();
		$data_sp = array();
		$data_liberty = array();
		for ($jml = 0; $jml <= $sheetCount -1; $jml++)
		{
			$sheet = $objPHPExcel->getSheet($jml);
			$highestRow = $sheet->getHighestRow();
			$highestColumn = $sheet->getHighestColumn();
			$rows = $jml == 0 ? 3 : 8 ;
			$kolom = $jml == 0 ? 'A' : 'A' ;
			$i = 0;
			for ($row = $rows; $row <= $highestRow; $row++)
			{ //  Read a row of data into an array                 
				$rowData = $sheet->rangeToArray($kolom . $row . ':' . $highestColumn . $row,
					NULL,TRUE);

				if ($jml == 0) {
					if ($rowData[0][0] != '') {
						$data_addendum[] = array(
							'nik' => str_replace(' ','',$rowData[0][2]),
							'id_contract' => $rowData[0][30],
							'addendum' => array(
								$rowData[0][34],
								$rowData[0][35],
								$rowData[0][36],
								$rowData[0][37],
								$rowData[0][38],
								$rowData[0][39]
							));

						$data_sp[] = array(
							'nik' => str_replace(' ','',$rowData[0][2]),
							'sp' => array(
								$rowData[0][54],
								$rowData[0][55],
								$rowData[0][56]
							));

						$data_liberty[] = array(
							'nik' => str_replace(' ','',$rowData[0][2]),
							'liberty' => array(
								$this->get_date($rowData[0][45]),
								$this->get_date($rowData[0][46]),
								$this->get_date($rowData[0][47]),
								$this->get_date($rowData[0][48]),
								$this->get_date($rowData[0][49]),
								$this->get_date($rowData[0][50]),
								$this->get_date($rowData[0][51]),
								$this->get_date($rowData[0][52])
							));

						// print("<pre>".print_r($this->m_import->insert_addendum(str_replace(' ','',$rowData[0][2]),$t_user_addendum),true)."</pre>");
						$t_user_employee[] = array(
							"employee_nik"=> str_replace(' ','',$rowData[0][2]),
							"employee_name"=> $rowData[0][1],
							"hire_date"=> $this->get_date($rowData[0][3]),
							"end_contract"=> $this->get_date($rowData[0][4]),
							"date_permanent_employee"=> $rowData[0][5],
							"born_city"=> $rowData[0][6],
							"born_date"=> date('Y-m-d', strtotime($rowData[0][7])),
							"alocate_salary"=> (int)$this->m_import->check_insert('t_mtr_alocate_salary','alocate_salary_name',$rowData[0][11]),
							"alocate_salary_project"=> (int)$this->m_import->check_insert('t_mtr_alocate_salary_project','name',$rowData[0][12]),
							"division"=> (int)$this->m_import->check_insert('t_mtr_division','division_name',$rowData[0][14]),
							"department"=> (int)$this->m_import->insert_department($rowData[0][13],$rowData[0][14]),
							"position"=> (int)$this->m_import->check_insert('t_mtr_position','position_name',$rowData[0][15]),
							"competency_based"=> (int)$this->m_import->check_insert('t_mtr_competency_based','competency_based_name',$rowData[0][16]),
							"function"=> (int)$this->m_import->check_insert('t_mtr_function','function_name',$rowData[0][17]),
							"status_contract"=> (int)$this->m_import->check_insert('t_mtr_status_contract','status_contract_name',$rowData[0][18]),
							"job_level"=> (int)$this->m_import->check_insert('t_mtr_job_level','job_level_name',$rowData[0][19]),
							"education_group"=> (int)$this->m_import->check_insert('t_mtr_education_group','education_group_name',$rowData[0][20]),
							"education_level" => (int)$this->m_import->insert_education_level($rowData[0][21],$rowData[0][20]),
							"education_majors" => (int)$this->m_import->check_insert('t_mtr_education_majors','education_majors_name',$rowData[0][22]),
							"gender" => $rowData[0][24],
							"address"=> $rowData[0][27],
							"id_ktp"=> $rowData[0][28],
							"id_telephone"=> (string)$rowData[0][29],
							"religion"=> $rowData[0][31],
							"status_married" => (int)$this->m_import->check_insert('t_mtr_status_married','status_married_name',$rowData[0][32]),
							"private_health_insurance"=> $rowData[0][33],
							"jamsostek"=> $this->get_checklist($rowData[0][57]),
							"bpjs"=> $this->get_checklist($rowData[0][58]),
							"grg"=> $this->get_checklist($rowData[0][59]),
							"postgree"=> $this->get_checklist($rowData[0][60]),
							"vivotek"=> $this->get_checklist($rowData[0][61]),
							"ataki_mechanic"=> $this->get_checklist($rowData[0][62]),
							"instek_digital"=> $this->get_checklist($rowData[0][63]),
							"bimtek"=> $this->get_checklist($rowData[0][64]),
							"ataki_electronic"=> $this->get_checklist($rowData[0][65]),
							"magnetic"=> $this->get_checklist($rowData[0][66]),
							"vocational_agency"=> $this->get_checklist($rowData[0][67]),
							"non_hr_manager"=> $this->get_checklist($rowData[0][68]),
							"financial_tools"=> $this->get_checklist($rowData[0][69]),
							"guarantee"=> $rowData[0][70],
							"created_by"=>$this->session->userdata('user_group_id'));

						$lastname = explode(' ',$t_user_employee[$i]["employee_name"]);
						$t_mtr_user[] = array(
							// "user_group_id"=>$t_user_employee[$i]["employee_nik"],
							"username"=>$t_user_employee[$i]["employee_nik"],
							"password"=>base64_encode(strtolower(end($lastname)).''.date('d',strtotime($t_user_employee[$i]["born_date"]))),
							"nik"=>$t_user_employee[$i]["employee_nik"],
							"status"=>1,
							"created_by"=>$this->session->userdata('user_group_id'),
						);

						$i++;
					}
				}elseif ($jml == 1) {
					if ($rowData[0][0] != '') {
						$t_salary_employee[] = array(
						// "id" =>$rowData[0][0],
							// "name"=> $rowData[0][1],
							// "department"=> $rowData[0][2],
							// "project"=> $rowData[0][3],
							// "position"=> $rowData[0][4],
							"month_salary"=>$month_salary,
							// "employee_nik"=> str_replace(' ','',$rowData[0][2]),
							"employee_nik"=> $t_user_employee[$i]["employee_nik"],
							"basic"=> $this->get_salary($rowData[0][5]),
							"cola"=> $this->get_salary($rowData[0][6]),
							"position"=> $this->get_salary($rowData[0][7]),
							"achievement"=> $this->get_salary($rowData[0][8]),
							"overtime"=> $this->get_salary($rowData[0][9]),
							"experience"=> $this->get_salary($rowData[0][10]),
							"other"=> $this->get_salary($rowData[0][11]),
							"total"=> $this->get_salary($rowData[0][12]),
							"project_allowance"=> $this->get_salary($rowData[0][13]),
							"additional_overtime"=> $this->get_salary($rowData[0][14]),
							"more_refund"=> $this->get_salary($rowData[0][15]),
							"total_additional"=> $this->get_salary($rowData[0][16]),
							"cooperative_x"=> $this->get_salary($rowData[0][17]),
							"loan_x"=> $this->get_salary($rowData[0][18]),
							"other_x"=> $this->get_salary($rowData[0][19]),
							"stamp_x"=> $this->get_salary($rowData[0][20]),
							"jamsostek_x"=> $this->get_salary($rowData[0][21]),
							"bpjs_x"=> $this->get_salary($rowData[0][22]),
							"insurance_x"=> $this->get_salary($rowData[0][23]),
							"total_x"=> $this->get_salary($rowData[0][24]),
							"paid_salary"=> $this->get_salary($rowData[0][25]),
							"created_by"=>$this->session->userdata('user_group_id'),
						);
						$i++;
						// "total_paid"=> $rowData[0][26]);
					}
				}
			}
		}
		// echo json_encode($t_mtr_alocate_salary);
		// echo json_encode($t_user_employee);
		// echo json_encode($t_salary_employee);
		// echo json_encode($data_sp);
		// echo json_encode($data_liberty);
		// $this->m_import->insert_alocate_salary($t_mtr_alocate_salary);
		// echo json_encode($t_user_employee);
		// $this->m_import->insert_employee($data_dua);
		$this->m_import->insert_user($t_user_employee);
		$this->m_import->insert_salary($t_salary_employee);
		$this->m_import->insert_addendum($data_addendum);
		$this->m_import->insert_sp($data_sp);
		$this->m_import->insert_liberty($data_liberty);

		// $this->db->insert_batch("t_user_employee",$t_user_employee);
		// $this->db->insert_batch("t_salary_employee",$t_salary_employee);
		// echo "MANTABS";
		// // echo "mantap";
		
		// redirect('salary','refresh');
	// print_r($data_addendum);
	// print("<pre>".print_r($t_mtr_user,true)."</pre>");
	// print("<pre>".print_r($this->m_import->insert_mtr_user($t_mtr_user),true)."</pre>");
	// print("<pre>".print_r($this->m_import->insert_addendum($data_addendum),true)."</pre>");
	// print("<pre>".print_r($this->m_import->insert_sp($data_sp),true)."</pre>");
	// print("<pre>".print_r($this->m_import->insert_liberty($data_liberty),true)."</pre>");
	// print("<pre>".print_r($this->m_import->insert_user($t_user_employee),true)."</pre>");
		// print("<pre>".print_r($data_sp,true)."</pre>");
	}

}

/* End of file Import.php */
/* Location: ./application/controllers/Import.php */