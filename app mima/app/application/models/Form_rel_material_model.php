<?php

class Form_rel_material_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'formulario_rel_materiales';
        parent::__construct($this->table);
    }
	/* Elimina los materiales relacionados a un formulario */
    function delete_materials_related_to_form($form_id){
		
		$form_rel_materials_table = $this->db->dbprefix('formulario_rel_materiales');
		$sql = "DELETE FROM $form_rel_materials_table WHERE";
		$sql .= " $form_rel_materials_table.id_formulario = $form_id";
		
		if($this->db->query($sql)){
			return true;
		} else {
			return false;
		}
	}
	
	/* Retorna los materiales relacionados a un formulario */
	function get_materials_related_to_form($form_id){
		
		$form_rel_materials_table = $this->db->dbprefix('formulario_rel_materiales');
		
		$this->db->query('SET SQL_BIG_SELECTS=1');
		$sql = "SELECT $form_rel_materials_table.* FROM $form_rel_materials_table WHERE";
		$sql.= " $form_rel_materials_table.deleted = 0 AND $form_rel_materials_table.id_formulario = $form_id";
		
		return $this->db->query($sql);
	} 

}
