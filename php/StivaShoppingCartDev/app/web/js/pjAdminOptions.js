var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		
		var tabs = ($.fn.tabs !== undefined),
			$tabs = $("#tabs"),
			dialog = ($.fn.dialog !== undefined),
			$dialogDeleteShipping = $("#dialogDeleteShipping");
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs();
		}
		
		$(".field-int").spinner({
			min: 0
		});
		
		function reDrawCode() {
			var code = $("#hidden_code").text(),
				locale = $("select[name='install_locale']").find("option:selected").val(),
				hide = $("input[name='install_hide']").is(":checked") ? "&hide=1" : "";
			locale = parseInt(locale.length, 10) > 0 ? "&locale=" + locale : "";
						
			$("#install_code").text(code.replace(/&action=pjActionLoad"/g, function(match) {
	            return ['&action=pjActionLoad', locale, hide, '"'].join("");
	        }));
		}
		
		$("#content").on("focus", ".textarea_install", function (e) {
			var $this = $(this);
			$this.select();
			$this.mouseup(function() {
				$this.unbind("mouseup");
				return false;
			});
		}).on("keyup", "#uri_page", function (e) {
			var tmpl = $("#hidden_htaccess").text(),
				index = this.value.indexOf("?");
			$("#install_htaccess").text(tmpl.replace('::URI_PAGE::', index >= 0 ? this.value.substring(0, index) : this.value));
		}).on("change", "select[name='install_locale']", function(e) {
            
            reDrawCode.call(null);
            
		}).on("change", "input[name='install_hide']", function (e) {
			
			reDrawCode.call(null);
			
		}).on("change", "select[name='value-enum-o_send_email']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'mail|smtp::mail':
				$(".boxSmtp").hide();
				break;
			case 'mail|smtp::smtp':
				$(".boxSmtp").show();
				break;
			}
		}).on("change", "input[name='value-bool-o_allow_paypal']", function (e) {
			if ($(this).is(":checked")) {
				$(".boxPaypal").show();
			} else {
				$(".boxPaypal").hide();
			}
		}).on("change", "input[name='value-bool-o_allow_authorize']", function (e) {
			if ($(this).is(":checked")) {
				$(".boxAuthorize").show();
			} else {
				$(".boxAuthorize").hide();
			}
		}).on("change", "input[name='value-bool-o_allow_bank']", function (e) {
			if ($(this).is(":checked")) {
				$(".boxBank").show();
			} else {
				$(".boxBank").hide();
			}
		}).on("click", ".btnAddShipping", function () {
			var $c = $("#tmplShipping tbody").clone(),
				r = $c.html().replace(/\{INDEX\}/g, 'new_' + Math.ceil(Math.random() * 99999));
			$("#tblShipping").find("tbody").append(r);
		}).on("click", ".btnDeleteShipping", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($dialogDeleteShipping.length > 0 && dialog) {
				$dialogDeleteShipping.data("link", $(this)).dialog("open");
			}
			return false;
		}).on("click", ".btnRemoveShipping", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});		
			return false;
		});
		
		if ($dialogDeleteShipping.length > 0 && dialog) {
			var buttons = {};
			buttons[myLabel.btn_delete] = function () {
				var $this = $(this),
					$link = $this.data("link"),
					$tr = $link.closest("tr"),
					id = $link.data("id");
			
				$.post("index.php?controller=pjAdminOptions&action=pjActionDeleteShipping", {
					"id": id
				}).done(function (data) {
					if (data.code === undefined) {
						return;
					}
					switch (data.code) {
						case 200:
							$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
								$tr.remove();
								$this.dialog("close");
							});
							break;
					}
				});
			};
			buttons[myLabel.btn_cancel] = function () {
				$(this).dialog("close");
			};
			
			$dialogDeleteShipping.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				buttons: buttons
			});
		}
	});
})(jQuery_1_8_2);