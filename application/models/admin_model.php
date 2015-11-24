<?php
class Admin_model extends CI_Model {

    var $tabel     = 'app_theme';

    function __construct() {
        parent::__construct();
    }
    
 	function get_theme($id){
		$data = array();
		$options = array('id_theme' => $id);
		$query = $this->db->get_where($this->tabel,$options,1);
		if ($query->num_rows() > 0){
			$data = $query->row_array();
		}

		$query->free_result();    
		return $data;
	}


	function get_inv_barang(){
		$query = $this->db->query("SELECT SUM(jml) AS jml, SUM(nilai) AS nilai FROM ((SELECT COUNT(id_inventaris_barang) AS jml,SUM(harga) AS nilai FROM inv_inventaris_barang WHERE id_pengadaan=0)UNION(SELECT COUNT(id_inventaris_barang) AS jml,SUM(harga) AS nilai FROM inv_inventaris_barang INNER JOIN inv_pengadaan ON inv_pengadaan.id_pengadaan=inv_inventaris_barang.id_pengadaan)) AS aset");

		return $query->result();
	}

	function get_inv_barang1(){
		$query = $this->db->query("SELECT COUNT(id_mst_inv_ruangan) as jml from mst_inv_ruangan ");

		return $query->result();
	}

	function get_jum_aset(){
		$query =  $this->db->query("select count(id_inventaris_barang) as jml from inv_inventaris_barang where pilihan_keadaan_barang = 'B'");

		return $query->result();
	}

	function get_jum_aset1(){
		$query =  $this->db->query("select count(id_inventaris_barang) as jml from inv_inventaris_barang where pilihan_keadaan_barang = 'RR'");

		return $query->result();
	}

	function get_jum_aset2(){
		$query =  $this->db->query("select count(id_inventaris_barang) as jml from inv_inventaris_barang where pilihan_keadaan_barang = 'RB'");

		return $query->result();
	}

}