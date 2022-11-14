
//jQuery(document).ready(function() {
	
	function footable_tr_default(tblID){
		var vTR = tblID + " tr";
		
		jQuery(vTR).css("background-color","#ffffff");
		
		jQuery(vTR).mouseover(function(){
			if (jQuery(this)[0].rowIndex == 0){
				return;
			}
			jQuery(this).css("background-color","#e0ffff") .css("cursor","pointer")
	 	});
		jQuery(vTR).mouseout(function(){
	  		if (jQuery(this)[0].rowIndex == 0){
				return;
			}
	  		jQuery(this).css("background-color","#ffffff") .css("cursor","normal")
	 	});
	}

	function footable_tr_click(trID){
		trID.css("background-color","#CCFFCC");

		jQuery("#group_list").find("tr").removeClass("selected");

		trID.addClass("selected");
		
		trID.mouseover(function(){
			if (jQuery(this)[0].rowIndex == 0){
				return;
			}
			jQuery(this).css("background-color","#e0ffff") .css("cursor","pointer")
		});
		
		trID.mouseout(function(){
			if (jQuery(this)[0].rowIndex == 0){
				return;
			}
			jQuery(this).css("background-color","#CCFFCC") .css("cursor","normal")
		});
	}
	
	//�e�[�u���̃`�F�b�N�{�b�N�X�J�����̃`�F�b�N��Ԃ�S��ON�ɂ���
	function footable_set_row_all_check(select, check){
		jQuery(select + ' tr').each(function (index, row) {
			jQuery(row).find('input:checkbox').prop('checked', check);
		});
	}
//});
