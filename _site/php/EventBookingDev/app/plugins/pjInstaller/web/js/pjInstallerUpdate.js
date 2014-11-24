var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $dialogExecute = $("#dialogExecute"),
			datagrid = ($.fn.datagrid !== undefined),
			dialog = ($.fn.dialog !== undefined);
		
		function formatButton(str, obj) {
			return ['<input type="button" value="', myLabel.execute, '" class="pj-button btn-execute" data-name="', obj.name, '" />'].join("");
		}
		
		if ($("#grid").length > 0 && datagrid) {
			var $grid = $("#grid").datagrid({
				buttons: [],
				columns: [{text: myLabel.name, type: "text", sortable: false, editable: false, width: 830},
				          {text: "", type: "text", sortable: false, editable: false, width: 100, align: "center", renderer: formatButton}],
				dataUrl: "index.php?controller=pjInstaller&action=pjActionSecureGetUpdate",
				dataType: "json",
				fields: ['name', 'id'],
				paginator: {
					actions: [
					   {text: myLabel.execute_selected, url: "index.php?controller=pjInstaller&action=pjActionSecureSetUpdate", render: false, confirmation: myLabel.confirm_selected}
					],
					gotoPage: false,
					paginate: false,
					total: true,
					rowCount: false
				},
				saveUrl: null,
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("click", ".btn-execute", function (e) {
			if ($dialogExecute.length > 0 && dialog) {
				$dialogExecute.data("name", $(this).data("name")).dialog("open");
			}
		});
		
		if ($dialogExecute.length > 0 && dialog) {
			$dialogExecute.dialog({
				modal: true,
				autoOpen: false,
				draggable: false,
				resizable: false,
				close: function () {
					$dialogExecute.find(".i-error-clean").hide().html("");
				},
				buttons: {
					"Execute": function () {
						$.post("index.php?controller=pjInstaller&action=pjActionSecureSetUpdate", {
							"name": $dialogExecute.data("name")
						}).done(function (data) {
							if (data.status == "OK") {
								$dialogExecute.dialog("close");
							} else {
								$dialogExecute.find(".i-error-clean").html(data.text).show();
							}
						});
					},
					"Cancel": function () {
						$dialogExecute.dialog("close");
					}
				}
			});
		}
		
	});
})(jQuery_1_8_2);