<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_register extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		
	}

	public function id_bank($bank)
	{
		return $this->db->query('SELECT "BANK_ID" FROM dbo."M_BANK" WHERE UPPER("NAMA_BANK") = UPPER(\''.$bank.'\')')->row();
	}

	public function getGolVehicle()
	{
		return $this->db->query('SELECT * FROM dbo."T_GOLONGAN" WHERE "AKTIF"=1')->result();
	}

	public function getBank()
	{
		return $this->db->query('SELECT * FROM dbo."M_BANK"')->result();
	}

	public function getUserGroup()
	{
		return $this->db->query('SELECT * FROM core.t_mtr_user_group WHERE status=1')->result();
	}

	public function getObu()
	{
		return $this->db->query('SELECT DISTINCT(OBU.id),OBU.serial,OBU.obu_number FROM dbo."t_mtr_obu" OBU LEFT JOIN dbo."t_mtr_paired_obu_customer_vehicle" PO ON PO."obu_id" = OBU."id" WHERE OBU."id" NOT IN (SELECT "obu_id" FROM dbo."t_mtr_paired_obu_customer_vehicle" WHERE status=1) AND OBU.status=1')->result();
	}

	public function addObu($data)
	{
		return $this->db->insert('dbo.t_mtr_obu', $data);
	}

	public function check_obu($obu_number,$input='')
	{
		if ($input != '') {
			return $this->db->query('SELECT * FROM dbo."t_mtr_obu" WHERE UPPER("obu_number")= UPPER(\''.$obu_number.'\') AND id != '.$input.' AND status=1')->row();
		}else{
			return $this->db->query('SELECT * FROM dbo."t_mtr_obu" WHERE UPPER("obu_number")= UPPER(\''.$obu_number.'\') AND status=1')->row();
		}
	}

	public function check_serial($serial,$input='')
	{
		if ($input != '') {
			return $this->db->query('SELECT * FROM dbo."t_mtr_obu" WHERE UPPER("serial")= UPPER(\''.$serial.'\') AND id != '.$input.' AND status=1')->row();
		}else{
			return $this->db->query('SELECT * FROM dbo."t_mtr_obu" WHERE UPPER("serial")= UPPER(\''.$serial.'\') AND status=1')->row();
		}
	}

	public function check_customer($cust_id)
	{
		return $this->db->query('SELECT * FROM dbo."T_CUSTOMER" WHERE "CUST_ID"= \''.$cust_id.'\'')->row();
	}

	public function addCust($data)
	{
		return $this->db->insert('dbo.T_CUSTOMER',$data);
	}

	public function addVehicle($data)
	{
		return $this->db->insert('dbo.T_VEHICLE', $data);
	}

	public function addCustVehicle($data)
	{
		return $this->db->insert('dbo.T_CUST_VEHICLE', $data);
	}

}

/* End of file M_register.php */
/* Location: ./application/models/M_register.php */