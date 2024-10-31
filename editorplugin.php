<?php
$label = $_GET['label'];
$shortcode = $_GET['shortcode'];
$image = $_GET['image'];
$border = $_GET['border'];
$bgcolor = $_GET['bgcolor'];

//var_dump ($label);

echo "function ssm_skriv_$shortcode(content) {
    return '[$shortcode]' + content + '[/$shortcode]';
}
 
(function() {

	tinymce.create('tinymce.plugins.$shortcode', {
 
		init : function(ed, url){
			ed.addButton('$shortcode', {
				title : '$label',
				image : url+'/images/$image',
				onclick : function() {
					ed.selection.setContent(ssm_skriv_$shortcode(ed.selection.getContent()));
				}
			});
		},
		createControl : function(n, cm) {  
		    return null;  
		},
		getInfo: function () {
			return {
			    longname: 'Säsongsmat för Wordpress',
			    author: 'Leo Wallentin / säsongsmat.nu',
			    authorurl: 'http://xn--ssongsmat-v2a.nu',
			    infourl: 'http://xn--ssongsmat-v2a.nu',
			    version: '0.1'
			};
		}
	});

	tinymce.PluginManager.add('$shortcode', tinymce.plugins.$shortcode);
 
})();";
?>
