var jQuery_1_8_2 = $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmOption = $("#frmOption"),
			validate = ($.fn.validate !== undefined),
			tabs = ($.fn.tabs !== undefined),
			tipsy = ($.fn.tipsy !== undefined),
			$tabs = $("#tabs");
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs();
		}
		
		$(".field-int").spinner({
			min: 0
		});
		$(".item-per-page").spinner({
			min: 1
		});
		
		setCustom = function()
		{
			var step_1 = $('#clone_step_1').text();
			var layout = $('#layout').val();
			var view = $('#install_view').val();
			var icons = $('#hide_icons').val();
			var cats = $('#hide_categories').val();
			var cid = $('#category_id').val();
			var file_name = $('#css_file').val();
			if(file_name == '')
			{
				step_1 = step_1.replace('{CSSFile}', '');
			}else{
				step_1 = step_1.replace('{CSSFile}', '&cssfile=' + file_name);
			}
			if(layout == 'layout_1'){
				$('.layout1-settings').css('display', 'block');
			}else{
				$('.layout1-settings').css('display', 'none');
				view = 'list';
			}
			step_1 = step_1.replace('{LAYOUT}', layout);
			step_1 = step_1.replace('{VIEW}', view);
			step_1 = step_1.replace('{ICONS}', icons);
			step_1 = step_1.replace('{CATS}', cats);
			step_1 = step_1.replace('{CID}', cid);
			
			$('#install_step_1').val(step_1);
		};
		
		if($('#install_step_1').length > 0)
		{
			setCustom();
			var clone_explanation = $('#clone_explanation').html();
			var layout = $('#layout').val();
			clone_explanation = clone_explanation.replace('{DefaultCSS}', 'front_' + layout + '.css');
			$('#install_css_explanation').html(clone_explanation);
		}
		
		if ($frmOption.length > 0 && validate) {
			$frmOption.validate({
				rules: {
					"value-int-o_events_per_page": {
				        min: 1,
				        number: true
				    }
		        },
				errorPlacement: function (error, element) {
					
					error.insertAfter(element.parent());
					
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "dt",
				ignore: ""
			});
		}
		
		$("#content").on("focusin", ".textarea_install", function (e) {
			$(this).select();
		}).on('keyup', '#css_file', function(e){
			setCustom();
		}).on('change', '#install_view', function(e){
			setCustom();
		}).on('change', '#hide_icons', function(e){
			setCustom();
		}).on('change', '#hide_categories', function(e){
			setCustom();
		}).on('change', '#category_id', function(e){
			setCustom();
		}).on('change', '#layout', function(e){
			var layout = $(this).val();
			var clone_explanation = $('#clone_explanation').html();
			clone_explanation = clone_explanation.replace('{DefaultCSS}', 'front_' + layout + '.css');
			$('#install_css_explanation').html(clone_explanation);
			$('#css_file').val('front_' + layout + '.css');
			$('#install_view').val('list');
			setCustom();
		}).on('click', '.pj-install-preview', function(e){
			e.preventDefault();
			var view = $('#install_view').val();
			var icons = $('#hide_icons').val();
			var cats = $('#hide_categories').val();
			var cid = $('#category_id').val();
			var file_name = $('#css_file').val();
			var layout = $('#layout').val();
			window.open('index.php?controller=pjAdminOptions&action=pjActionPreview&layout=' + layout + '&cssfile=' + file_name + '&view=' + view + '&icons=' + icons + '&cats=' + cats + '&cid=' + cid);
		}).on("change", "select[name='value-enum-o_send_email']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'mail|smtp::mail':
				$(".boxSmtp").hide();
				break;
			case 'mail|smtp::smtp':
				$(".boxSmtp").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_paypal']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxPaypal").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxPaypal").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_authorize']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxAuthorize").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxAuthorize").show();
				break;
			}
		}).on("change", "select[name='value-enum-o_allow_bank']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'Yes|No::No':
				$(".boxBankAccount").hide();
				break;
			case 'Yes|No::Yes':
				$(".boxBankAccount").show();
				break;
			}
		});
		
		if (tipsy) {
			$(".listing-tip").tipsy({
				offset: 1,
				opacity: 1,
				html: true,
				gravity: "nw",
				className: "tipsy-listing"
			});
		}
		
	});
})(jQuery_1_8_2);