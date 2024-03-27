<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_import extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	public function insert_employee($data = array() )
	{
		foreach ($data as $row) {
			$query = $this->db->get_where('t_mtr_user', array(
				'nik'=>(string)$row['nik']
			));
			if ($query->num_rows() < 1) {
				// echo json_encode($row);
				if ($this->db->insert('t_mtr_user',$row)) {
					$status = true;
				}else{
					$status = false;
				}
			}else{
				$status = false;
			}
		}
		return $status;
	}

	// public function insert_alocate_salary($data = array())
	// {
	// 	// foreach ($data as $row) {
	// 		$query = $this->db->get_where('t_mtr_alocate_salary', array(
	// 			'alocate_salary_name' => $data['alocate_salary_name']
	// 		));

	// 		if ($query->num_rows() < 1) {
	// 			$this->db->insert('t_mtr_alocate_salary', $data);
	// 			return $this->db->insert_id();
	// 		}else{
	// 			return false;
	// 		}
		// 		if ($this->db->insert('t_mtr_alocate_salary', $data)) {
		// 			$status = true;
		// 		}else{
		// 			$status = false;
		// 		}
		// 	}else{
		// 		$status = false;
		// 	}
		// // }
		// return $status;
	// }

	// public function get_alocate_salary($data,$name)
	// {
	// 	$query = $this->db->query("SELECT id FROM t_mtr_alocate_salary WHERE LOWER(alocate_salary_name) = LOWER('$name')")->result();
	// 	foreach ($query as $row) {
	// 		if ($query >= 1) {
	// 			$balikan = $row->id;
	// 		}else{
	// 			$balikan = $this->insert_alocate_salary($data);
	// 		}
	// 	}
	// 	return $balikan;
	// }

	public function check_insert($table,$field_name,$name)
	{
		$query = $this->db->query("SELECT id FROM ".$table." WHERE LOWER(".$field_name.") = LOWER('$name')")->row();

		if($query){
			return $query->id;
		}else{
			$data = array(
				$field_name => $name,
				'status' => 1,
				'created_by' => $this->session->userdata('user_group_id')
			);
			$this->db->insert($table, $data);
			return $this->db->insert_id();
		}
	}

	public function insert_department($department_name,$name)
	{
		$query = $this->db->query("SELECT id FROM t_mtr_department WHERE LOWER(department_name) = LOWER('$department_name')")->row();

		if ($query){
			return $query->id;
		}else{
			$data = array(
				'department_name' => $department_name,
				'division_id' => $this->check_insert('t_mtr_division','division_name',$name),
				'status' => 1,
				'created_by' => $this->session->userdata('user_group_id')
			);
			$this->db->insert('t_mtr_department', $data);
			return $this->db->insert_id();
		}
	}

	public function insert_education_level($name,$education_group_id)
	{
		$query = $this->db->query("SELECT id FROM t_mtr_education_level WHERE LOWER(education_level_name) = LOWER('$name')")->row();

		if ($query){
			return $query->id;
		}else{
			$data = array(
				'education_level_name' => $name,
				'education_group_id' => $this->check_insert('t_mtr_education_group','education_group_name',$education_group_id),
				'status' => 1,
				'created_by' => $this->session->userdata('user_group_id')
			);
			$this->db->insert('t_mtr_education_level', $data);
			return $this->db->insert_id();
		}
	}

	public function to_date($date)
	{
		return $this->db->query("SELECT to_date('$date','DD Mon YY') AS date")->row()->date;
	}

	public function insert_user($data)
	{
		foreach ($data as $key => $value) {
				$t_user_employee = array(
					"employee_nik"=> $value['employee_nik'],
					"employee_name"=> $value['employee_name'],
					"hire_date"=> $value['hire_date'],
					"end_contract"=> $value['end_contract'],
					"date_permanent_employee"=> $value['date_permanent_employee'],
					"born_city"=> $value['born_city'],
					"born_date"=> $value['born_date'],
					"alocate_salary"=> $value['alocate_salary'],
					"alocate_salary_project"=> $value['alocate_salary_project'],
					"division"=> $value['division'],
					"department"=> $value['department'],
					"position"=> $value['position'],
					"competency_based"=> $value['competency_based'],
					"function"=> $value['function'],
					"status_contract"=> $value['status_contract'],
					"job_level"=> $value['job_level'],
					"education_group"=> $value['education_group'],
					"education_level"=> $value['education_level'],
					"education_majors"=> $value['education_majors'],
					"gender"=> $value['gender'],
					"address"=> $value['address'],
					"id_ktp"=> $value['id_ktp'],
					"id_telephone"=> $value['id_telephone'],
					"religion"=> $value['religion'],
					"status_married"=> $value['status_married'],
					"private_health_insurance"=> $value['private_health_insurance'],
					"jamsostek"=> $value['jamsostek'],
					"bpjs"=> $value['bpjs'],
					"grg"=> $value['grg'],
					"postgree"=> $value['postgree'],
					"vivotek"=> $value['vivotek'],
					"ataki_mechanic"=> $value['ataki_mechanic'],
					"instek_digital"=> $value['instek_digital'],
					"bimtek"=> $value['bimtek'],
					"ataki_electronic"=> $value['ataki_electronic'],
					"magnetic"=> $value['magnetic'],
					"vocational_agency"=> $value['vocational_agency'],
					"non_hr_manager"=> $value['non_hr_manager'],
					"financial_tools"=> $value['financial_tools'],
					"status_active_employee"=>1,
					"created_by"=> $value['created_by'],
				);
			$query = $this->db->query("SELECT * FROM t_user_employee WHERE employee_nik='".$value['employee_nik']."'")->result();
			if (!$query) {
				$this->db->insert('t_user_employee',$t_user_employee);
			}else{
				unset($t_user_employee['employee_nik']);
				$this->db->where('employee_nik',$value['employee_nik']);
				$this->db->update('t_user_employee',$t_user_employee);
			}
		}
	}

	public function insert_mtr_user($data)
	{
		foreach ($data as $key => $value) {
				$t_mtr_user = array(
					"user_group_id"=> 2,
					"username"=> $value['username'],
					"password"=> $value['password'],
					"nik"=> $value['nik'],
					"status"=> $value['status'],
					"created_by"=> $value['created_by'],
				);
			$query = $this->db->query("SELECT * FROM t_mtr_user WHERE username='".$value['nik']."'")->result();
			if (!$query) {
				$this->db->insert('t_mtr_user',$t_mtr_user);
			}else{
				unset($t_mtr_user['nik']);
				$this->db->where('nik',$value['nik']);
				$this->db->update('t_mtr_user',$t_mtr_user);
			}
		}
	}

	public function insert_salary($data)
	{
		foreach ($data as $key => $value) {
				$t_salary_employee = array(
					"month_salary"=> $value['month_salary'],
					"employee_nik"=> $value['employee_nik'],
					"basic"=> $value['basic'],
					"cola"=> $value['cola'],
					"position"=> $value['position'],
					"achievement"=> $value['achievement'],
					"overtime"=> $value['overtime'],
					"experience"=> $value['experience'],
					"other"=> $value['other'],
					"total"=> $value['total'],
					"project_allowance"=> $value['project_allowance'],
					"additional_overtime"=> $value['additional_overtime'],
					"more_refund"=> $value['more_refund'],
					"total_additional"=> $value['total_additional'],
					"cooperative_x"=> $value['cooperative_x'],
					"loan_x"=> $value['loan_x'],
					"other_x"=> $value['other_x'],
					"stamp_x"=> $value['stamp_x'],
					"jamsostek_x"=> $value['jamsostek_x'],
					"bpjs_x"=> $value['bpjs_x'],
					"insurance_x"=> $value['insurance_x'],
					"total_x"=> $value['total_x'],
					"paid_salary"=> $value['paid_salary'],
					"status"=>1,
					"created_by"=> $value['created_by'],
				);
			$query = $this->db->query("SELECT * FROM t_salary_employee WHERE month_salary='".$value['month_salary']."' AND employee_nik='".$value['employee_nik']."'")->result();
			if (!$query) {
				$this->db->insert('t_salary_employee',$t_salary_employee);
			}else{
				unset($t_salary_employee['employee_nik']);
				$this->db->where('employee_nik',$value['employee_nik']);
				$this->db->where('month_salary',$value['month_salary']);
				$this->db->update('t_salary_employee',$t_salary_employee);
			}
		}
	}

	public function insert_addendum($data)
	{
		$insert = array();
		foreach ($data as $key => $value) {
			foreach ($value['addendum'] as $k => $v) {
				if($v != null){
					$no 	  = $k + 1;
					$date     = explode('-', $v);
					$query = $this->db->query("SELECT * FROM t_user_addendum WHERE employee_nik='".$value['nik']."' AND LOWER(addendum_name)=LOWER('addendum ".$no."')")->result();
					if (!$query) {
						$insert[] = array(
							'employee_nik' => $value['nik'],
							'addendum_name' => 'Addendum '.$no,
							'start_contract' =>  $this->to_date(trim($date[0])),
							'end_contract' =>  $this->to_date(trim($date[1])),
							'id_contract' => $value['id_contract'],
							'status' => 1,
							'created_by' => $this->session->userdata('user_group_id'),
						);
					}
				}
			}
		}
		// return $insert;
		if ($insert) {
		$this->db->insert_batch("t_user_addendum",$insert);
		}
	}

	public function insert_sp($data)
	{
		$insert = array();
		foreach ($data as $key => $value) {
			foreach ($value['sp'] as $k => $v) {
				if($v != null){
					$no 	  = $k + 1;
					$date     = str_replace('-',' ',$v);
					$query = $this->db->query("SELECT * FROM t_user_sp WHERE employee_nik='".$value['nik']."' AND LOWER(sp_name)=LOWER('sp ".$no."')")->result();
					if (!$query) {
						$insert[] = array(
							'sp_name' => 'SP '.$no,
							'employee_nik' => $value['nik'],
							'date' =>  $this->to_date($date),
							'status' => 1,
							'created_by' => $this->session->userdata('user_group_id'),
						);
					}
				}
			}
		}
		// return $insert;
		if ($insert) {
		$this->db->insert_batch("t_user_sp",$insert);
		}
	}

	public function insert_liberty($data)
	{
		$insert = array();
		foreach ($data as $key => $value) {
			foreach ($value['liberty'] as $k => $v) {
				if($v != null){
					$no 	  = $k + 1;
					$date     = $v;
					$query = $this->db->query("SELECT * FROM t_user_liberty WHERE employee_nik='".$value['nik']."' AND LOWER(day)=LOWER('cuti ".$no."')")->result();
					if (!$query) {
						$insert[] = array(
							'day' => 'Cuti '.$no,
							'employee_nik' => $value['nik'],
							'start_date' =>  $date,
							'end_date' =>  $date,
							'apply_date' =>  $date,
							'status' => 1,
							'created_by' => $this->session->userdata('user_group_id'),
						);
					}
				}
			}
		}
		// return $insert;
		if ($insert) {
		$this->db->insert_batch("t_user_liberty",$insert);
		}
	}
}

/* End of file m_import.php */
/* Location: ./application/models/m_import.php */