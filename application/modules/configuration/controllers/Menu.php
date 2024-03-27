<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		getSession();
		$this->load->model('m_menu');
	}

	public function index()
	{
		$data['title'] = "Menu";
		$data['content'] = "menu/index";
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['add']     = $this->m_global->menuAccess($this->session->userdata('user_group_id'),$this->uri->uri_string(),'add');
		$this->load->view('common/page',$data);
	}

	public function add()
	{
		validateAjax();
		$data['title'] = "Add Menu";
		$data['parent'] = $this->m_menu->getDetail();
		//$data['icon'] = $this->m_menu->icon();
		$data['menu_action'] = $this->m_menu->menu_action();
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$this->load->view("menu/add",$data);
	}

	public function edit($id)
	{
		validateAjax();
		$id = decode($id);
		$data['title'] = "Edit Menu";
		$data['parent'] = $this->m_menu->getDetail();
		//$data['icon'] = $this->m_menu->icon();
		$data['menu_action'] = $this->m_menu->menu_action();
		$data['menu'] = $this->m_global->getMenu($this->session->userdata('user_group_id'));
		$data['row']= $this->m_menu->detail_edit($id);
		$data['action']= $this->m_menu->sub_detail($id);
		$data['id'] = $id;
		$this->load->view("menu/edit",$data);
	}

	public function action_add()
	{
		validateAjax();
		$menu_action = $this->input->post('menu_action');
		$parent = $this->input->post('parent') == '' ? 0 : $this->input->post('parent');
		$order = $this->input->post('order');

		$check = $this->m_menu->check_menu($parent,$order);

		if ($check) {

			echo $this->msg_error("Order already in use ");
		}
		else
		{
			$data = array(
				'parent_id' => $parent,
				'name' => $this->input->post('name'),
				'icon' => $this->input->post('icon'),
				'slug' => $this->input->post('slug'),
				'menu_order' => $order,
				'status' => 1,
				'created_by' => $this->session->userdata('username'),
			);

			$this->db->trans_begin();
			$insert_menu=$this->m_menu->insert($data);

			foreach ($menu_action as $key => $value) {
				$data_detail[] = array(
					'menu_id' => $insert_menu,
					'action_id' => $value,
					'status' => 1,
					'created_by' => $this->session->userdata('username'),
				);
			}

			$this->m_menu->insert_detail($data_detail);

			if($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
	            echo $this->msg_error("Failed add data");
	        } 
	        else 
	        {
	            $this->db->trans_commit();
	            echo $this->msg_success("Success add data");
	        }
		}
	}

	public function action_edit()
	{
		validateAjax();
		$id_menu = decode($this->input->post('id'));
		$menu_action = $this->input->post('menu_action');
		$order = $this->input->post('order');
		$parent = $this->input->post('parent') == '' ? 0 : $this->input->post('parent');

		$check = $this->m_menu->check_menu($parent,$order,$id_menu);

		if ($check) 
		{
			echo $this->msg_error("Order already in use");
		}
		else
		{
			$data = array(
				'parent_id' => $parent,
				'name' => $this->input->post('name'),
				'icon' => $this->input->post('icon'),
				'slug' => $this->input->post('slug'),
				'menu_order' => $order,
				'updated_by' => $this->session->userdata('username'),
				'updated_on' => date("Y-m-d H:i:s"),
			);

			$this->db->trans_begin();
			$this->m_menu->update_menu($id_menu,$data);
			$check_detail = $this->m_menu->check_detail($id_menu);

			$checked = array();

			foreach ($check_detail as $key => $value) {
				$checked[] = $value->action_id;
			}

			$toinsert = array_diff($menu_action, $checked);
			$todelete = array_diff($checked, $menu_action);

			if ($toinsert) {
				$insert = array();
				$update = array();
				foreach ($toinsert as $key => $value) {
					$check_insert_deleted = $this->m_menu->check_insert_deleted($id_menu,$value);
					if ($check_insert_deleted) 
					{
						$update[] = array(
							'id_seq' => $check_insert_deleted,
							'menu_id' => $id_menu,
							'action_id' => $value,
							'status' => 1,
							'updated_on'=> date("Y-m-d H:i:s"),
							'updated_by' => $this->session->userdata('username'),
						);
					}
					else
					{
						$insert[] = array(
							'menu_id' => $id_menu,
							'action_id' => $value,
							'status' => 1,
							'created_by' => $this->session->userdata('username'),
						);
					}
				}
				if($insert){
						$this->m_menu->insert_detail($insert);
				}

				if($update){
						$this->m_menu->edit_detail($update);
				}
			}

			if ($todelete) {
				foreach ($todelete as $key => $value) {
					$id_detail[] = $this->m_menu->select_detail_id($id_menu,$value);
				}

				foreach ($id_detail as $key => $value) 
				{
					$deleted_privileges = $this->m_menu->check_edit_privilege($id_menu,$value);
					if ($deleted_privileges) {
						$data_delete_detail[] = array(
							'id_seq' => $deleted_privileges,
							'status' => -5,
							'updated_on' => date("Y-m-d H:i:s"),
							'updated_by' => $this->session->userdata('username'),
						);
						$this->m_menu->delete_privilege_menu($data_delete_detail);
					}
					$data_delete_detail[] = array(
						'id_seq' => $value,
						'status' => -5,
						'updated_on' => date("Y-m-d H:i:s"),
						'updated_by' => $this->session->userdata('username'),
					);
					$this->m_menu->edit_detail($data_delete_detail);
				}
			}

			// json_edited();
			if($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
	            echo $this->msg_error("Failed add data");
	        } 
	        else 
	        {
	            $this->db->trans_commit();
	            echo $this->msg_success("Success add data");
	        }
		}
	}

	public function delete($id)
	{
		$id = decode($id);
		$data = array(
			'status' => -5,
			'updated_by' => $this->session->userdata('username'),
			'updated_on' => date("Y-m-d H:i:s"),
		);

		$deleted  = $this->m_menu->delete($id,$data);
		if ($deleted) {
			json_deleted();
		}else{
			json_error();
		}
	}

	public function getList()
	{
		validateAjax();
		$menu = $this->m_menu->getDetail();
		echo json_encode($menu);
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

/* End of file Menu.php */
/* Location: ./application/controllers/Menu.php */