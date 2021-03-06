<?php
class Inv_ruangan extends CI_Controller {

    public function __construct(){
		parent::__construct();
		$this->load->add_package_path(APPPATH.'third_party/tbs_plugin_opentbs_1.8.0/');
		require_once(APPPATH.'third_party/tbs_plugin_opentbs_1.8.0/demo/tbs_class.php');
		require_once(APPPATH.'third_party/tbs_plugin_opentbs_1.8.0/tbs_plugin_opentbs.php');		
		
		$this->load->model('inventory/inv_ruangan_model');
		//$this->load->model('inventory/permohonanbarang_model');
		$this->load->model('mst/puskesmas_model');
	}

	function filter(){
		if($_POST) {
			if($this->input->post('code_cl_phc') != '') {
				$this->session->set_userdata('filter_code_cl_phc',$this->input->post('code_cl_phc'));
			}
		}
	}

	function json(){
		$this->authentication->verify('inventory','show');

		if($_POST) {
			$fil = $this->input->post('filterscount');
			$ord = $this->input->post('sortdatafield');

			for($i=0;$i<$fil;$i++) {
				$field = $this->input->post('filterdatafield'.$i);
				$value = $this->input->post('filtervalue'.$i);

				$this->db->like($field,$value);
			}

			if(!empty($ord)) {
				$this->db->order_by($ord, $this->input->post('sortorder'));
			}
		}

		if($this->session->userdata('filter_code_cl_phc') != '') {
			$this->db->where('code_cl_phc',$this->session->userdata('filter_code_cl_phc'));
		}
		$rows_all = $this->inv_ruangan_model->get_data();


		if($_POST) {
			$fil = $this->input->post('filterscount');
			$ord = $this->input->post('sortdatafield');

			for($i=0;$i<$fil;$i++) {
				$field = $this->input->post('filterdatafield'.$i);
				$value = $this->input->post('filtervalue'.$i);

				$this->db->like($field,$value);
			}

			if(!empty($ord)) {
				$this->db->order_by($ord, $this->input->post('sortorder'));
			}
		}

		if($this->session->userdata('filter_code_cl_phc') != '') {
			$this->db->where('code_cl_phc',$this->session->userdata('filter_code_cl_phc'));
		}
		
		$rows = $this->inv_ruangan_model->get_data($this->input->post('recordstartindex'), $this->input->post('pagesize'));
		$data = array();
		foreach($rows as $act) {
			$data[] = array(
				'id_mst_inv_ruangan'	=> $act->id_mst_inv_ruangan,
				'nama_ruangan'			=> $act->nama_ruangan,
				'keterangan'			=> $act->keterangan,
				'code_cl_phc'			=> $act->code_cl_phc,
				'value'					=> $act->value,
				'edit'					=> 1,
				'delete'				=> 1
			);
		}

		$size = sizeof($rows_all);
		$json = array(
			'TotalRows' => (int) $size,
			'Rows' => $data
		);

		echo json_encode(array($json));
	}
	
	function json_detail($code_cl_phc = 0, $id_ruang=0){
		$this->authentication->verify('inventory','show');

		
		if($_POST) {
			$fil = $this->input->post('filterscount');
			$ord = $this->input->post('sortdatafield');

			for($i=0;$i<$fil;$i++) {
				$field = $this->input->post('filterdatafield'.$i);
				$value = $this->input->post('filtervalue'.$i);

				$this->db->like($field,$value);
			}

			if(!empty($ord)) {
				$this->db->order_by($ord, $this->input->post('sortorder'));
			}
		}
		/*
		if($this->session->userdata('filter_code_cl_phc') != '') {
			$this->db->where('code_cl_phc',$this->session->userdata('filter_code_cl_phc'));
		}*/
		


		if($_POST) {
			$fil = $this->input->post('filterscount');
			$ord = $this->input->post('sortdatafield');

			for($i=0;$i<$fil;$i++) {
				$field = $this->input->post('filterdatafield'.$i);
				$value = $this->input->post('filtervalue'.$i);

				$this->db->like($field,$value);
			}

			if(!empty($ord)) {
				$this->db->order_by($ord, $this->input->post('sortorder'));
			}
		}
		/*
		if($this->session->userdata('filter_code_cl_phc') != '') {
			$this->db->where('code_cl_phc',$this->session->userdata('filter_code_cl_phc'));
		}
		*/
		if(!empty($this->session->userdata('filter_group')) and $this->session->userdata('filter_group') == '1'){
			$rows = $this->inv_ruangan_model->get_data_detail_group($this->input->post('recordstartindex'), $this->input->post('pagesize'));
			$kondisi = $this->inv_ruangan_model->get_pilihan_kondisi()->result();			
			
			$data = array();			
			$cek = "0";
			$n = 0;
			for($index = 0; $index < count($rows); $index++){										
				
				$col = array();
				$i=0;															
				
				$col = array(
					'id_mst_inv_barang' 		=> substr(chunk_split($rows[$index]['id_mst_inv_barang'], 2, '.'),0,14),
					'nama_barang' 				=>$rows[$index]['nama_barang'],
					'register' 					=>'-',
					'tahun' 					=>$rows[$index]['tahun'],					
					'harga' 					=>$rows[$index]['harga']
				);				
					
				foreach($kondisi as $k){
					
					$col[$k->id]=$this->inv_ruangan_model->get_jumlah_kondisi($k->id, $rows[$index]['barang_kembar_proc']);
					#$col[$k->id] = 2;
					
				}
				$data[] = $col;
												
			}
			$rows_all = $this->inv_ruangan_model->get_data_detail_group();
			
			$size = count($rows_all);
			$json = array(
				'TotalRows' => (int) $size,
				'Rows' => $data
			);
		}else{
			
			$rows = $this->inv_ruangan_model->get_data_detail($this->input->post('recordstartindex'), $this->input->post('pagesize'));
			$kondisi = $this->inv_ruangan_model->get_pilihan_kondisi()->result();
			
			$data = array();
			$x = '';
			$n = 0;
			$jml =1;
			$tem = array();
			foreach($rows as $r) {
				$data_kondisi = array();
				$col = array();
				$i=0;
				$real_kondisi = $r->kondisi;
				if(!empty($this->session->userdata('filter_tanggal')) and $this->session->userdata('filter_tanggal') != '0' ){
					$tgl = $this->session->userdata('filter_tanggal');
					if($this->inv_ruangan_model->get_detail_kondisi($r->id_inventaris_barang, $tgl) != '0'){
						$real_kondisi = $this->inv_ruangan_model->get_detail_kondisi($r->id_inventaris_barang, $tgl);
					}else{
						$real_kondisi = $r->kondisi;
					}
				}else{
					$real_kondisi = $r->kondisi;
				}
	
					$col = array(
						'id_mst_inv_barang' 		=> chunk_split($r->id_mst_inv_barang, 2, '.'),
						'nama_barang' 				=>$r->nama_barang,
						'register' 					=>$r->register,
						'tahun' 					=>$r->tahun,
						'pilihan_keadaan_barang' 	=>$real_kondisi,
						'harga' 					=>$r->harga
					);				
					
					foreach($kondisi as $k){
						$data_kondisi[$i]=$k->id;
						if($real_kondisi == $k->id){
							$col[$k->id]='1';
						}else{
							$col[$k->id]='0';
						}				
						$i++;
					}
					
					$data[] = $col;
									
			}
			if($n > 0){
				$rows_all = $n;
			}else{
				$rows_all = $this->inv_ruangan_model->get_data_detail();
			}
			$size = sizeof($rows_all);
			$json = array(
				'TotalRows' => (int) $size,
				'Rows' => $data
			);
		}
		echo json_encode(array($json));
	}
	
	function set_detail_filter(){
		if(!empty($this->input->post('filter_code_cl_phc')) and !empty($this->input->post('filter_id_ruang')) and !empty($this->input->post('filter_tanggal')) ){
			$this->session->set_userdata('filter_code_cl_phc',$this->input->post('filter_code_cl_phc'));
			$this->session->set_userdata('filter_id_ruang', $this->input->post('filter_id_ruang'));
			$this->session->set_userdata('filter_tanggal', $this->input->post('filter_tanggal'));
			$this->session->set_userdata('filter_group', $this->input->post('filter_group'));
			$q = $this->inv_ruangan_model->get_data_deskripsi($this->input->post('filter_code_cl_phc'),$this->input->post('filter_id_ruang'));
			foreach($q as $r){
				echo $r->value."_data_".$r->nama_ruangan."_data_".$r->keterangan;
			}
		}				
	}
	
	function index(){
		$this->authentication->verify('inventory','edit');
		$data['title_group'] = "Inventory";
		$data['title_form'] = "Inventaris Ruangan";

		$this->db->like('code','p'.substr($this->session->userdata('puskesmas'),0,7));

		$kodepuskesmas = $this->session->userdata('puskesmas');
		if(substr($kodepuskesmas, -2)=="01"){
			$this->db->like('code','P'.substr($kodepuskesmas, 0,7));
		}else {
			$this->db->like('code','P'.$kodepuskesmas);
		}

		$data['datapuskesmas'] 	= $this->inv_ruangan_model->get_data_puskesmas();
		$data['content'] = $this->parser->parse("inventory/inv_ruangan/show",$data,true);

		$this->template->show($data,"home");
	}
	
	public function get_ruangan()
	{
		if($this->input->is_ajax_request()) {
			$code_cl_phc = $this->input->post('code_cl_phc');
			$id_mst_inv_ruangan = $this->input->post('id_mst_inv_ruangan');

			$kode 	= $this->inv_ruangan_model->getSelectedData('mst_inv_ruangan',$code_cl_phc)->result();
			
			if($this->input->post('code_cl_phc') != '') {
				$this->session->set_userdata('filter_cl_phc',$this->input->post('code_cl_phc'));
				$this->session->set_userdata('filterruangan','');
			}else{
				$this->session->set_userdata('filter_cl_phc','');
				$this->session->set_userdata('filterruangan','');
			}
			echo "<option value=\"all\">Semua Ruangan</option>";
			foreach($kode as $kode) :
				echo $select = $kode->id_mst_inv_ruangan == $id_mst_inv_ruangan ? 'selected' : '';
				echo '<option value="'.$kode->id_mst_inv_ruangan.'" '.$select.'>' . $kode->nama_ruangan . '</option>';
			endforeach;

			return FALSE;
		}

		show_404();
	}
	function do_detail($kode=0,$id=""){
		
		$this->authentication->verify('inventory','edit');

		$data = $this->inv_ruangan_model->get_data_row($kode,$id); 

		$data['title_group'] = "Inventory";
		$data['title_form']="Detail Inventaris Ruangan";
		$data['kode']= $kode;
		$data['id'] = $id;

		$kodepuskesmas = $this->session->userdata('puskesmas');
		if(substr($kodepuskesmas, -2)=="01"){
			$this->db->like('code','P'.substr($kodepuskesmas,0,7));
		}else{
			$this->db->like('code','P'.$kodepuskesmas);
		}
		$data['kodepuskesmas'] = $this->puskesmas_model->get_data();
		$data['kondisi'] = $this->inv_ruangan_model->get_pilihan_kondisi()->result();
		$data['n_kondisi'] = $this->inv_ruangan_model->get_pilihan_kondisi()->num_rows();
	
		$data['barang'] = $this->parser->parse("inventory/inv_ruangan/barang",$data,true);
		$data['content'] = $this->parser->parse("inventory/inv_ruangan/detail",$data,true);
		$this->template->show($data,"home");
		
	}

	function detail($kode=0,$id="")
	{
		$this->session->set_userdata('filter_code_cl_phc',$kode);
		$this->session->set_userdata('filter_id_ruang',$id);
		$this->session->set_userdata('filter_tgl','0');
		
		$this->do_detail($kode, $id);
		
	}


	function add(){
		$this->load->model('inventory/inv_ruangan_model');

		$this->authentication->verify('inventory','add');

        $this->form_validation->set_rules('nama_ruangan', 'Nama Ruangan', 'trim|required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required');
        $this->form_validation->set_rules('codepus', 'Puskesmas', 'trim|required');

		$kodepuskesmas = $this->session->userdata('puskesmas');
		if(substr($kodepuskesmas, -2)=="01"){
			$this->db->like('code','P'.substr($kodepuskesmas,0,7));
		}else{
			$this->db->like('code','P'.$kodepuskesmas);
		}
		$data['kodepuskesmas'] = $this->puskesmas_model->get_data();

		if($this->form_validation->run()== FALSE){
			$data['code']		 		= $this->session->userdata('puskesmas');
			$data['title_group'] 		= "Inventory";
			$data['title_form']  		= "Tambah Inventaris Ruangan";
			$data['action']      		= "add";
			$data['kode']				= "";

		
			$data['content'] = $this->parser->parse("inventory/inv_ruangan/form",$data,true);
			$this->template->show($data,"home");
		}elseif($this->inv_ruangan_model->insert_entry()){
			$this->session->set_flashdata('alert', 'Save data successful...');
			redirect(base_url()."inventory/inv_ruangan/");
		}else{
			$this->session->set_flashdata('alert_form', 'Save data failed...');
			redirect(base_url()."inventory/inv_ruangan/add");
		}
	}

	function edit($kode=0,$id="")
	{
		$this->authentication->verify('inventory','add');

        // $this->form_validation->set_rules('id_mst_inv_ruangan', 'Id', 'trim|required');
        $this->form_validation->set_rules('nama_ruangan', 'Nama ruangan', 'trim|required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|required');
        // $this->form_validation->set_rules('codepus', 'Kode', 'trim|required');

		if($this->form_validation->run()== FALSE){
			$data = $this->inv_ruangan_model->get_data_row($kode,$id); 

			$data['title_group'] = "Inventory";
			$data['title_form']="Ubah Inventory Ruangan";
			$data['action']="edit";
			$data['kode']= $kode;
			$data['id'] = $id;
			// $data['codepus']= $kode;
			// var_dump($data);
			// exit();
			$kodepuskesmas = $this->session->userdata('puskesmas');
			if(substr($kodepuskesmas, -2)=="01"){
				$this->db->like('code','P'.substr($kodepuskesmas,0,7));
			}else{
				$this->db->like('code','P'.$kodepuskesmas);
			}
			$data['kodepuskesmas'] = $this->puskesmas_model->get_data();

		
			$data['content'] = $this->parser->parse("inventory/inv_ruangan/edit",$data,true);
			$this->template->show($data,"home");
		}elseif($this->inv_ruangan_model->update_entry($kode,$id)){
			$this->session->set_flashdata('alert_form', 'Save data successful...');
			redirect(base_url()."inventory/inv_ruangan/".$this->input->post('kode'));
		}else{
			$this->session->set_flashdata('alert_form', 'Save data failed...');
			redirect(base_url()."inventory/inv_ruangan/edit/".$kode);
		}
	}

	function dodel($kode=0,$id=""){
		$this->authentication->verify('inventory','del');

		if($this->inv_ruangan_model->delete_entry($kode,$id)){
			$this->session->set_flashdata('alert', 'Delete data ('.$kode.')');
			redirect(base_url()."inventory/inv_ruangan");
		}else{
			$this->session->set_flashdata('alert', 'Delete data error');
			redirect(base_url()."inventory/inv_ruangan");
		}
	}
	
	function export_detail(){
		
		$TBS = new clsTinyButStrong;		
		$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
		//[data_tabel.no;block=tbs:row]	[data_tabel.tgl]	[data_tabel.ruangan]	[data_tabel.jumlah]	[data_tabel.keterangan]	[data_tabel.status]
		
		$this->authentication->verify('inventory','show');
		
		$activity = $this->inv_ruangan_model->get_data_detail();
		$kondisi = $this->inv_ruangan_model->get_pilihan_kondisi();
		
		$data_tabel = array();
		foreach($activity as $act) {
			$data_tabel[] = array(
				'kode_barang'							=> $no++,				
				'nama_barang'   				=> $act->nama_barang,
				'register'						=> $act->jumlah,
				'tahun'					=> $act->keterangan				
			);
		}

		
		if(empty($this->input->post('nama_puskesmas')) or $this->input->post('nama_puskesmas') == 'Pilih Puskesmas'){
			$nama = 'Semua Data Puskesmas';
		}else{
			$nama = $this->input->post('nama_puskesmas');
		}
		$tanggal = $this->input->post('tanggal');
		$keterangan = $this->input->post('keterangan');
		$ruang = $this->input->post('ruang');
		$puskesmas = $nama;
		
		#$data_puskesmas[] = array('nama_puskesmas' => $nama, 'tanggal'=> $tanggal, 'keterangan'=>$keterangan, 'ruang'=>$ruang);
		$data_puskesmas['nama_puskesmas'] = $nama;
		$data_puskesmas['tanggal'] = $tanggal;
		$data_puskesmas['ruang'] = $ruang;
		$data_puskesmas['keterangan'] = $keterangan;
		
		$TBS->ResetVarRef(false);
		$TBS->VarRef =  &$data_puskesmas;	
		$template = dirname(__FILE__).'\..\..\..\public\files\template\inventory\permohonan_barang_detail.xlsx';		
		$TBS->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

		
		// Merge data in the first sheet
		$TBS->MergeBlock('a', $data_tabel);
		#$TBS->MergeBlock('b', $data_puskesmas);
		
		$code = date('Y-m-d-H-i-s');
		$output_file_name = dirname(__FILE__).'\..\..\..\public\files\hasil\hasil_detail_export_'.$code.'.xlsx';
		$TBS->Show(OPENTBS_FILE, $output_file_name); // Also merges all [onshow] automatic fields.
		
		echo base_url().'public/files/hasil/hasil_detail_export_'.$code.'.xlsx' ;
		
	
		
		echo "http://google.com";
	}
}
