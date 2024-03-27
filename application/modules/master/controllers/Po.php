<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Po extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_po');
		$this->load->model('m_global');
	}

	public function index()
	{
		$data['title'] = "Master PO";
		$data['content'] = "po/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add'] = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
		// $this->load->view('tap_in/index');
	}

	public function getList()
	{
		validateAjax();
		$list = $this->m_po->getData();
		echo json_encode($list);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add PO";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("po/add",$data);
	}

	public function action_add()
	{

		$poName=$this->input->post('poName');
		$picEmail=$this->input->post('picEmail');
		$picName=$this->input->post('picName');
		$picPhone=$this->input->post('picPhone');
		$poAddress=$this->input->post('poAddress');

		// $phoneVal=substr($picPhone,2);
		// if($phoneVal=='62')
		// {
		// 	$phoneNo=
		// }


		$this->form_validation->set_rules('poName', 'Po', 'required');
		$this->form_validation->set_rules('picPhone', 'Po', 'required');
		$this->form_validation->set_rules('poAddress', 'Po', 'required');
		$this->form_validation->set_rules('picEmail', 'Email', 'required');

		$data=array(
			'po_name'=>$poName,
			'po_code'=>$this->createPoCode(),
			'pic_name'=>$picName,
			'pic_email'=>$picEmail,
			'pic_phone'=>$picPhone,
			'address'=>$poAddress,
			'created_by'=>$this->session->userdata('username'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field!');
        }
        else if(!is_integer($picPhone))
        {
        	echo $this->msg_error('Phone number must be integer !');
        }
        else
        {
        	$insert=$this->m_global->insert("master.t_mtr_po",$data);
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
		$data['po'] = $this->m_global->getDataById('master.t_mtr_po',"id_seq=$id")->row();
		$data['id'] = encode($id);
		$data['title'] = "Edit User";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("po/edit",$data);

	}
	function action_edit()
	{
		$poName=$this->input->post('poName');
		$picEmail=$this->input->post('picEmail');
		$picName=$this->input->post('picName');
		$picPhone=$this->input->post('picPhone');
		$poAddress=$this->input->post('poAddress');
		$id=decode($this->input->post('id'));

		$this->form_validation->set_rules('poName', 'Po', 'required');
		$this->form_validation->set_rules('picPhone', 'pic Phone', 'required');
		$this->form_validation->set_rules('poAddress', 'Address', 'required');
		$this->form_validation->set_rules('picEmail', 'Email', 'required');

		$data=array(
			'po_name'=>$poName,
			'pic_name'=>$picName,
			'pic_email'=>$picEmail,
			'pic_phone'=>$picPhone,
			'address'=>$poAddress,
			'updated_by'=>$this->session->userdata('username'),
			'updated_on'=>date('Y-m-d H:i:s'),
		);
		
		if ($this->form_validation->run() == FALSE)
        {
            echo $this->msg_error('Please input the field!');
        }
        else
        {
        	$update=$this->m_global->update("master.t_mtr_po",$data,"po_code='".$id."'");
			if ($update)
			{
				echo $this->msg_success('Success update data');
			}
			else
			{
				echo $this->msg_error('Failed update data');
			}
        }		
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

    	$delete=$this->m_global->update("master.t_mtr_po",$data,"id_seq=$id");
		if ($delete)
		{
			echo $this->msg_success('Success delete data');
		}
		else
		{
			echo $this->msg_error('Failed delete data');
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