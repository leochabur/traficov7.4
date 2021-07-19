function addElementSelect(id,key,value, w){
		$("#"+id).append($('<option>', {value: key, text: value}));
		$('#'+id).selectmenu({width: w});
}

function delElementSelect(id,key,w){
		$("#"+id+" option[value='"+key+"']").remove();
		$('#'+id).selectmenu({width: w});
}
