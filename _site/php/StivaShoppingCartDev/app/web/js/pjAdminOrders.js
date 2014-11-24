var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		
		var validator,
			$frmUpdateOrder = $("#frmUpdateOrder"),
			$dialogStockDelete = $("#dialogStockDelete"),
			$dialogStockEdit = $("#dialogStockEdit"),
			$dialogStockAdd = $("#dialogStockAdd"),
			$dialogConfirm = $("#dialogConfirm"),
			$dialogPayment = $("#dialogPayment"),
			datagrid = ($.fn.datagrid !== undefined),
			dialog = ($.fn.dialog !== undefined),
			spinner = ($.fn.spinner !== undefined),
			tabs = ($.fn.tabs !== undefined),
			validate = ($.fn.validate !== undefined),
			chosen = ($.fn.chosen !== undefined);

		function getStockProducts(order_id) {
			$.get("index.php?controller=pjAdminOrders&action=pjActionStockGet", {
				"order_id": order_id
			}).done(function (data) {
				$("#boxStockProducts").html(data);
			});
		}
		
		if (chosen) {
			$("select.custom-chosen").chosen();
		}
		
		if ($frmUpdateOrder.length > 0) {
			if (tabs) {
				$("#tabs").tabs();
			}
			
			$frmUpdateOrder.on("change", "#client_id", function () {
				var $this = $(this),
					$edit = $this.closest("fieldset").find(".pj-icon-edit"),
					client_id = $this.find("option:selected").val();
				
				if (parseInt(client_id, 10) > 0) {
					$edit.attr("href", $edit.attr("href").replace(/id=(\d+)?/, 'id=' + client_id)).show();
				} else {
					$edit.hide();
				}
			}).on("click", ".btnCreateInvoice", function () {
				$("#frmCreateInvoice").trigger("submit");
			});
			
			if (validate) {
				$frmUpdateOrder.validate();
			}
			getStockProducts.call(null, $frmUpdateOrder.find("input[name='id']").val());
		}
		
		function formatTotal(val, obj) {
			return obj.total_formated;
		}
		
		function formatClient(val, obj) {
			return ['<a href="index.php?controller=pjAdminClients&action=pjActionUpdate&id=', obj.client_id, '">', obj.client_name, '</a>'].join("");
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var qs = "",
				m = window.location.href.match(/&client_id=(\d+)/);
			if (m !== null) {
				qs += m[0];
			}
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminOrders&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminOrders&action=pjActionDeleteOrder&id={:id}"}
				          ],
				columns: [{text: myLabel.uuid, type: "text", sortable: true, editable: true, width: 120},
				          {text: myLabel.client, type: "text", sortable: true, editable: false, renderer: formatClient},
				          {text: myLabel.created, type: "text", sortable: true, editable: false, width: 130},
				          {text: myLabel.total, type: "text", sortable: true, renderer: formatTotal, align: "right", width: 110},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, applyClass: "pj-status", options: [
                             {label: myLabel.statuses.cancelled, value: "cancelled"},
                             {label: myLabel.statuses.completed, value: "completed"},
                             {label: myLabel.statuses['new'], value: "new"},
                             {label: myLabel.statuses.pending, value: "pending"}
                          ]}
				       ],
				dataUrl: "index.php?controller=pjAdminOrders&action=pjActionGetOrder" + qs,
				dataType: "json",
				fields: ['uuid', 'client_name', 'created', 'total', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminOrders&action=pjActionDeleteOrderBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminOrders&action=pjActionExportOrder", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminOrders&action=pjActionSaveOrder&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
			
			if (m !== null) {
				$grid.datagrid("option", "cache", {
					"client_id": m[1]
				});
			}
		}
		
		function formatDefault (str) {
			return myLabel[str] || str;
		}
		
		function formatId (str) {
			return ['<a href="index.php?controller=pjInvoice&action=pjActionUpdate&id=', str, '">#', str, '</a>'].join("");
		}
		
		function formatCreated(str) {
			if (str === null || str.length === 0) {
				return myLabel.empty_datetime;
			}
			
			if (str === '0000-00-00 00:00:00') {
				return myLabel.invalid_datetime;
			}
			
			if (str.match(/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/) !== null) {
				var x = str.split(" "),
					date = x[0],
					time = x[1],
					dx = date.split("-"),
					tx = time.split(":"),
					y = dx[0],
					m = parseInt(dx[1], 10) - 1,
					d = dx[2],
					hh = tx[0],
					mm = tx[1],
					ss = tx[2];
				return $.datagrid.formatDate(new Date(y, m, d, hh, mm, ss), pjGrid.jsDateFormat + ", hh:mm:ss");
			}
		}
		
		if ($("#grid_invoices").length > 0 && datagrid) {
			var $grid_invoices = $("#grid_invoices").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjInvoice&action=pjActionUpdate&id={:id}", title: "Edit"},
				          {type: "delete", url: "index.php?controller=pjInvoice&action=pjActionDelete&id={:id}", title: "Delete"}],
				columns: [
				    {text: myLabel.num, type: "text", sortable: true, editable: false, renderer: formatId},
				    {text: myLabel.order_id, type: "text", sortable: true, editable: false},
				    {text: myLabel.issue_date, type: "date", sortable: true, editable: false, renderer: $.datagrid._formatDate, dateFormat: pjGrid.jsDateFormat},
				    {text: myLabel.due_date, type: "date", sortable: true, editable: false, renderer: $.datagrid._formatDate, dateFormat: pjGrid.jsDateFormat},
				    {text: myLabel.created, type: "text", sortable: true, editable: false, renderer: formatCreated},
				    {text: myLabel.status, type: "text", sortable: true, editable: false, renderer: formatDefault},	
				    {text: myLabel.total, type: "text", sortable: true, editable: false, align: "right", renderer: formatTotal}
				],
				dataUrl: "index.php?controller=pjInvoice&action=pjActionGetInvoices&q=" + $frmUpdateOrder.find("input[name='uuid']").val(),
				dataType: "json",
				fields: ['id', 'order_id', 'issue_date', 'due_date', 'created', 'status', 'total'],
				paginator: {
					actions: [
					   {text: myLabel.delete_title, url: "index.php?controller=pjInvoice&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_body}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		if ($("#grid_client_orders").length > 0 && datagrid) {
			var $grid_client_orders = $("#grid_client_orders").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminOrders&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminOrders&action=pjActionDeleteOrder&id={:id}"}
				          ],
				columns: [{text: myLabel.uuid, type: "text", sortable: true, editable: true, width: 150},
				          {text: myLabel.created, type: "text", sortable: true, editable: false, width: 150},
				          {text: myLabel.total, type: "text", sortable: true, renderer: formatTotal, align: "right", width: 120},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, applyClass: "pj-status", options: [
                             {label: myLabel.statuses.cancelled, value: "cancelled"},
                             {label: myLabel.statuses.completed, value: "completed"},
                             {label: myLabel.statuses['new'], value: "new"},
                             {label: myLabel.statuses.pending, value: "pending"}
                          ]}
				       ],
				dataUrl: "index.php?controller=pjAdminOrders&action=pjActionGetOrder&client_id=" + $("#client_id").find("option:selected").val() + "&order_id=" + $("input[name='id']").val(),
				dataType: "json",
				fields: ['uuid', 'created', 'total', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminOrders&action=pjActionDeleteOrderBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminOrders&action=pjActionSaveOrder&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}

		$("#content").on("change", "#client_id", function () {
			var client_id = $(this).find("option:selected").val();
			if (parseInt(client_id, 10) > 0) {
				$.get("index.php?controller=pjAdminOrders&action=pjActionGetClient", {
					"client_id": client_id
				}).done(function (data) {
					$("#boxClient").html(data);
				});
				$.get("index.php?controller=pjAdminOrders&action=pjActionGetAddressBook", {
					"client_id": client_id,
					"order_id": $("input[name='id']").val()
				}).done(function (data) {
					$("#boxAddressBook").html(data);
				});
			} else {
				$("#boxClient").html("");
			}
			
			var content = $grid_client_orders.datagrid("option", "content"),
				cache = $grid_client_orders.datagrid("option", "cache"),
				obj = {};
			
			obj.order_id = $("input[name='id']").val();
			obj.client_id = parseInt(client_id, 10) > 0 ? client_id : 9090909090;
			
			$.extend(cache, obj);
			$grid_client_orders.datagrid("option", "cache", cache);
			$grid_client_orders.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "id", "DESC", content.page, content.rowCount);
			
		}).on("change", "#address_id", function () {
			if (parseInt($("option:selected", this).val(), 10) > 0) {
				$(".btnCopy").prop("disabled", false);
				$.get("index.php?controller=pjAdminOrders&action=pjActionGetAddress", {
					"id": $("option:selected", this).val()
				}).done(function (data) {
					$("#boxAddress").html(data);
				});
			} else {
				$(".btnCopy").prop("disabled", true);
				$("#boxAddress").html("");
			}
		}).on("click", ".btnCopyShipping", function () {
			
			$.get("index.php?controller=pjAdminOrders&action=pjActionGetAddress", {
				"id": $("option:selected", $("#address_id")).val(),
				"json": 1
			}).done(function (data) {
				$("#s_name").val(data.name);
				$("#s_country_id").val(data.country_id).trigger("liszt:updated");
				$("#s_state").val(data.state);
				$("#s_city").val(data.city);
				$("#s_zip").val(data.zip);
				$("#s_address_1").val(data.address_1);
				$("#s_address_2").val(data.address_2);
				$("#same_as").prop("checked", false);
				$(".boxSame").show();
			});
			
		}).on("click", ".btnCopyBilling", function () {
			
			$.get("index.php?controller=pjAdminOrders&action=pjActionGetAddress", {
				"id": $("option:selected", $("#address_id")).val(),
				"json": 1
			}).done(function (data) {
				$("#b_name").val(data.name);
				$("#b_country_id").val(data.country_id).trigger("liszt:updated");
				$("#b_state").val(data.state);
				$("#b_city").val(data.city);
				$("#b_zip").val(data.zip);
				$("#b_address_1").val(data.address_1);
				$("#b_address_2").val(data.address_2);
			});
			
		}).on("change", "#same_as", function () {
			if ($(this).is(":checked")) {
				$(".boxSame").hide();
			} else {
				$(".boxSame").show();
			}
		}).on("click", ".stock-edit", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($dialogStockEdit.length > 0 && dialog) {
				$dialogStockEdit.data("id", $(this).data("id")).dialog("open");
			}
			return false;
		}).on("click", ".stock-add", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($dialogStockAdd.length > 0 && dialog) {
				$dialogStockAdd.data("order_id", $frmUpdateOrder.find("input[name='id']").val()).dialog("open");
			}
			return false;
		}).on("click", ".stock-delete", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($dialogStockDelete.length > 0 && dialog) {
				$dialogStockDelete.data("id", $(this).data("id")).dialog("open");
			}
			return false;
		}).on("click", ".order-calc", function () {
			var $this = $(this),
				$form = $this.closest("form");
			$.post("index.php?controller=pjAdminOrders&action=pjActionGetPrice", $form.serialize()).done(function (data) {
				if (data.status == 'OK') {
					$form.find("#price").val(data.data.price.toFixed(2));
					$form.find("#discount").val(data.data.discount.toFixed(2));
					$form.find("#insurance").val(data.data.insurance.toFixed(2));
					$form.find("#shipping").val(data.data.shipping.toFixed(2));
					$form.find("#tax").val(data.data.tax.toFixed(2));
					$form.find("#total").val(data.data.total.toFixed(2));
				}
			});
		}).on("focusin", ".datepick", function (e) {
			var $this = $(this);
			$this.datepicker({
				firstDay: $this.attr("rel"),
				dateFormat: $this.attr("rev"),
				onClose: function (selectedDate) {
					var name = $this.attr("name");
					if (name == "date_from") {
						$this.closest("p").find(".datepick[name='date_to']").datepicker("option", "minDate", selectedDate);
					} else if (name == "date_to") {
						$this.closest("p").find(".datepick[name='date_from']").datepicker("option", "maxDate", selectedDate);
					}
				}
			});
		}).on("click", ".btn-confirm", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if (dialog && $dialogConfirm.length > 0) {
				$dialogConfirm.data("id", $(this).data("id")).dialog("open");
			}
			return false;
		}).on("click", ".btn-payment", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if (dialog && $dialogPayment.length > 0) {
				$dialogPayment.data("id", $(this).data("id")).dialog("open");
			}
			return false;
		});
		
		$(document).on("change", ".stock-product", function () {
			var $this = $(this);
			$.get("index.php?controller=pjAdminOrders&action=pjActionStockGetByProduct", {
				"product_id": $this.find("option:selected").val()
			}).done(function (data) {
				var $wrapper = $this.closest("form").find(".stock-products");
				$wrapper.html(data);
				if (spinner) {
					$wrapper.find("input[name^='qty[']").each(function (i) {
						var $this = $(this);
						$this.spinner({
							min: 0,
							max: $this.data("max")
						});
					});
				}
				$this.closest(".ui-dialog-content").dialog("option", "position", "center");
			});
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "id", "DESC", content.page, content.rowCount);
			return false;
			
		}).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "id", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "id", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-button-detailed, .pj-button-detailed-arrow", function (e) {
			e.stopPropagation();
			$(".pj-form-filter-advanced").toggle();
		}).on("submit", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var obj = {},
				$this = $(this),
				arr = $this.serializeArray(),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
				obj[arr[i].name] = arr[i].value;
			}
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminOrders&action=pjActionGetOrder", "id", "DESC", content.page, content.rowCount);
			return false;
		}).on("reset", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(".pj-button-detailed").trigger("click");
			return false;
		});
		
		if ($dialogStockEdit.length > 0 && dialog) {
			$dialogStockEdit.dialog({
				modal: true,
				resizable: false,
				draggable: false,
				autoOpen: false,
				width: 640,
				open: function () {
					var $this = $(this);
					$dialogStockEdit.html("");
					$.get("index.php?controller=pjAdminOrders&action=pjActionStockEdit", {
						"order_stock_id": $this.data("id")
					}).done(function (data) {
						$dialogStockEdit.html(data);
						if (spinner) {
							var $qty = $dialogStockEdit.find("input[name='qty']");
							$qty.spinner({
								min: 1,
								max: $qty.data("max")
							});
						}
						$this.dialog("option", "position", "center");
					});
				},
				buttons: {
					"Update": function () {
						var $this = $(this);
						$.post("index.php?controller=pjAdminOrders&action=pjActionStockEdit", $dialogStockEdit.find("form").serialize()).done(function (data) {
							getStockProducts.call(null, $frmUpdateOrder.find("input[name='id']").val());
							$this.dialog("close");
						});
					},
					"Cancel": function () {
						$(this).dialog("close");
					}
				}
			});
		}
		
		if ($dialogStockAdd.length > 0 && dialog) {
			$dialogStockAdd.dialog({
				modal: true,
				resizable: false,
				draggable: false,
				autoOpen: false,
				width: 765,
				open: function () {
					var $this = $(this);
					$dialogStockAdd.html("");
					$.get("index.php?controller=pjAdminOrders&action=pjActionStockAdd", {
						"order_id" : $this.data("order_id")
					}).done(function (data) {
						$dialogStockAdd.html(data);
						if (chosen) {
							$dialogStockAdd.find("select.stock-product").chosen();
						}
						$this.dialog("option", "position", "center");
					});
				},
				buttons: {
					"Add": function () {
						var $this = $(this);
						$.post("index.php?controller=pjAdminOrders&action=pjActionStockAdd", $dialogStockAdd.find("form").serialize()).done(function (data) {
							getStockProducts.call(null, $frmUpdateOrder.find("input[name='id']").val());
							$this.dialog("close");
						});
					},
					"Cancel": function () {
						$(this).dialog("close");
					}
				}
			});
		}
		
		if ($dialogStockDelete.length > 0 && dialog) {
			$dialogStockDelete.dialog({
				modal: true,
				resizable: false,
				draggable: false,
				autoOpen: false,
				buttons: {
					"Delete": function () {
						var $this = $(this);
						$.post("index.php?controller=pjAdminOrders&action=pjActionStockDelete", {
							"id": $this.data("id")
						}).done(function (data) {
							getStockProducts.call(null, $frmUpdateOrder.find("input[name='id']").val());
							$this.dialog("close");
						});
					},
					"Cancel": function () {
						$(this).dialog("close");
					}
				}
			});
		}
		
		if ($dialogConfirm.length > 0 && dialog) {
			$dialogConfirm.dialog({
				modal: true,
				resizable: false,
				draggable: false,
				autoOpen: false,
				width: 590,
				open: function () {
					$dialogConfirm.html("");
					$.get("index.php?controller=pjAdminOrders&action=pjActionSend", {
						"id": $dialogConfirm.data("id"),
						"type": "confirm"
					}).done(function (data) {
						$dialogConfirm.html(data);
						validator = $dialogConfirm.find("form").validate({
							errorClass: "error_clear"
						});
						$dialogConfirm.dialog("option", "position", "center");
					});
				},
				buttons: {
					"Send": function () {
						if (validator.form()) {
							$.post("index.php?controller=pjAdminOrders&action=pjActionSend", $dialogConfirm.find("form").serialize()).done(function (data) {
								$dialogConfirm.dialog("close");
							});
						}
					},
					"Cancel": function () {
						$(this).dialog("close");
					}
				}
			});
		}
		
		if ($dialogPayment.length > 0 && dialog) {
			$dialogPayment.dialog({
				modal: true,
				resizable: false,
				draggable: false,
				autoOpen: false,
				width: 590,
				open: function () {
					$dialogPayment.html("");
					$.get("index.php?controller=pjAdminOrders&action=pjActionSend", {
						"id": $dialogPayment.data("id"),
						"type": "payment"
					}).done(function (data) {
						$dialogPayment.html(data);
						validator = $dialogPayment.find("form").validate();
						$dialogPayment.dialog("option", "position", "center");
					});
				},
				buttons: {
					"Send": function () {
						if (validator.form()) {
							$.post("index.php?controller=pjAdminOrders&action=pjActionSend", $dialogPayment.find("form").serialize()).done(function (data) {
								$dialogPayment.dialog("close");
							});
						}
					},
					"Cancel": function () {
						$(this).dialog("close");
					}
				}
			});
		}
	});
})(jQuery_1_8_2);