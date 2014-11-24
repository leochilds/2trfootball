var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
	
		var $frmCreateCategory = $('#frmCreateCategory'),
			$frmUpdateCategory = $('#frmUpdateCategory'),
			datagrid = ($.fn.datagrid !== undefined),
			validate = ($.fn.validate !== undefined);
			
		function formatName(val, obj) {
			return Array(obj.deep+1).join('-------') + " " + val.name;
		}
		function formatDown(val, obj) {
			return (obj.down === 1) ? ['<a href="index.php?controller=pjAdminCategories" class="arrow_down" rev="down" rel="', val.id , '" title="', myLabel.down, '"></a>'].join("") : '';
		}
		function formatUp(val, obj) {
			return (obj.up === 1) ? ['<a href="index.php?controller=pjAdminCategories" class="arrow_up" rev="up" rel="', val.id , '" title="', myLabel.up, '"></a>'].join("") : '';
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminCategories&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminCategories&action=pjActionDeleteCategory&id={:id}"}
				          ],
				columns: [{text: myLabel.name, type: "text", sortable: false, editable: false, renderer: formatName, width: 500},
				          {text: myLabel.products, type: "text", sortable: false, editable: false, align: "center"},
				          {text: "", type: "text", sortable: false, editable: false, renderer: formatDown, width: 21},
				          {text: "", type: "text", sortable: false, editable: false, renderer: formatUp, width: 21}
				       ],
				dataUrl: "index.php?controller=pjAdminCategories&action=pjActionGetCategory",
				dataType: "json",
				fields: ['data', 'products', 'data', 'data'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminCategories&action=pjActionDeleteCategoryBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminCategories&action=pjActionSaveCategory&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		if ($frmCreateCategory.length > 0 && validate) {
			$frmCreateCategory.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				}
			});
		}
		
		if ($frmUpdateCategory.length > 0 && validate) {
			$frmUpdateCategory.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				}
			});
		}
		
		$("#content").on("click", ".arrow_up, .arrow_down", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.post("index.php?controller=pjAdminCategories&action=pjActionSetOrder", {
				"id": $(this).attr("rel"),
				"direction": $(this).attr("rev")
			}).done(function (data) {
				var content = $grid.datagrid("option", "content");
				$grid.datagrid("load", "index.php?controller=pjAdminCategories&action=pjActionGetCategory", "id", "ASC", content.page, content.rowCount);
			});
			return false;
		});
	});
})(jQuery_1_8_2);