function showAjax(formid, webpage,resultDiv, fn=function(){}){
	var fnPostSuccess = function(data) {
		$('#'+resultDiv).html(''); $('#'+resultDiv).html(data);
		fn();
			};
	var fnPostFail = function(data,txtstatus,errorthrown) {
		$('#'+resultDiv).html('');
		$('#'+resultDiv).html(data.toString()+' '+txtstatus+' '+webpage+' '+errorthrown);
	};
    $('#'+resultDiv).empty();

	if(webpage==null || resultDiv == ''){
		alert('invalid parameters');
	}else{
		if(formid==null || formid ==''){
			if(window.location.protocol=="file:" && iOSversion()!="none"){
				var filePath = window.location.href.substr(0,window.location.href.indexOf(".html")+5);
				filePath = filePath.substr(0,filePath.lastIndexOf("/")+1)
				dopost({
					"type": "POST",
					"url": api_link + "/getlocalfile.php",
					"data": {
						"location": filePath,
						"page": webpage
					},
					"success": fnPostSuccess,
					"error": fnPostFail
				});
			}
			else
			$.get(webpage, fnPostSuccess).fail(fnPostFail);

		}else{
			if(window.location.protocol=="file:" && iOSversion()!="none"){
				var filePath = window.location.href.substr(0,window.location.href.indexOf(".html")+5);
				filePath = filePath.substr(0,filePath.lastIndexOf("/")+1)
				dopost({
					"type": "POST",
					"url": api_link + "/getlocalfile.php",
					"data": {
						"location": filePath,
						"page": webpage
					},
					"success": fnPostSuccess,
					"error": fnPostFail
				});
			}
			else
			$.post(webpage, $("#"+formid).serialize(), fnPostSuccess).fail(fnPostFail);
		}

	}
}