var jQuery_1_8_2 = $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateBooking = $("#frmCreateBooking"),
			$frmUpdateBooking = $("#frmUpdateBooking"),
			$frmResendConfirm = $("#frmResendConfirm"),
			$frmResendPayment = $("#frmResendPayment"),
			$dialogMessage = $("#dialogMessage"),
			validate = ($.fn.validate !== undefined),
			chosen = ($.fn.chosen !== undefined);
			dialog = ($.fn.dialog !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
			
		
		var calculatePrice = function(){
			var total_price = 0,
				tax = 0,
				deposit = 0,
				total = 0,
				customer_people = 0;
			$(".pj-price").each(function(){
				total_price += parseFloat($(this).val(), 10) * parseFloat($(this).attr('lang'));
				customer_people += parseFloat($(this).val(), 10);
			});
			tax = (total_price * parseFloat(myLabel.tax, 10) ) / 100;
			total = total_price + tax;
			deposit = (total * parseFloat(myLabel.deposit, 10) ) / 100;
			$('#booking_price').val(total_price.toFixed(2));
			$('#booking_total').val(total.toFixed(2));
			$('#booking_tax').val(tax.toFixed(2));
			$('#booking_deposit').val(deposit.toFixed(2));
			if(customer_people > 0)
			{
				$('#customer_people').val(customer_people);
			}else{
				$('#customer_people').val("");
			}
		};
		
		if ($frmResendConfirm.length > 0 && validate) {
			$frmResendConfirm.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ".ignore"
			});
		}
		
		if ($frmResendPayment.length > 0 && validate) {
			$frmResendPayment.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ".ignore"
			});
		}
		
		if ($frmCreateBooking.length > 0 && validate) {
			$frmCreateBooking.validate({
				rules: {
					"unique_id": {
						required: true,
						remote: "index.php?controller=pjAdminBookings&action=pjActionCheckUniqueId"
					}
				},
				messages: {
					"customer_people": {
						required: myLabel.price_at_least
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ".ignore"
			});
			
		}
		if ($frmUpdateBooking.length > 0 && validate) {
			$frmUpdateBooking.validate({
				rules: {
					"unique_id": {
						required: true,
						remote: "index.php?controller=pjAdminBookings&action=pjActionCheckUniqueId&id=" + $frmUpdateBooking.find("input[name='id']").val()
					}
				},
				messages: {
					"customer_people": {
						required: myLabel.price_at_least
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "re"
			});
			
			var event_id = $('#event_id').val(),
				booking_id = $('#booking_id').val();
			$.ajax({
				type: "GET",
				dataType: "html",
				url: "index.php?controller=pjAdminBookings&action=pjActionGetUpdatePrices&id=" + event_id + "&booking_id="+booking_id,
				success: function (res) {
					$('#price_container').html(res);
					calculatePrice();
				}
			});
		}
		$(document).on("click", "#use_ticket", function(e){
			var $this = $(this);
			var tid = $(this).attr('lang');
			if ($this.is(':checked')) {
				$("#dialogTicketConfirmation").dialog({
					autoOpen: false,
					resizable: false,
					draggable: false,
					width: 380,
					height:150,
					modal: true,
					
					buttons: {
						'Yes': function() {
							$.ajax({
								type: "POST",
								data: {
									id: tid
								},
								dataType: 'json',
								url: "index.php?controller=pjAdminBookings&action=pjActionSetUseTicket",
								success: function (res) {
									if(res.status == 1){
										$this.attr("disabled", true);
									}
								}
							});
							$(this).dialog('close');			
						},
						'No': function() {
							$this.removeAttr('checked');
							$(this).dialog('close');
						}
					}
				});
				$("#dialogTicketConfirmation").dialog('open');
		    }
		});
		if ($("#price_container").length > 0)
		{
			$("#price_container").on("change", ".pj-price", function (e) {
				calculatePrice();
			});
		}
		if (chosen) {
			$("#event_id").chosen();
			
			$("#customer_country").chosen();
			if ($frmCreateBooking.length > 0 || $frmUpdateBooking.length > 0)
			{
				$("#event_id").chosen().change(function(e){
					var event_id = $(this).val();
					var ajax_url = "index.php?controller=pjAdminBookings&action=pjActionGetPrices&id=" + event_id;
					if($frmUpdateBooking.length > 0){
						booking_id = $('#booking_id').val();
						ajax_url = "index.php?controller=pjAdminBookings&action=pjActionGetUpdatePrices&id=" + event_id + "&booking_id="+booking_id;
					}
					$.ajax({
						type: "GET",
						dataType: "html",
						url: ajax_url,
						success: function (res) {
							$('#price_container').html(res);
							$('#booking_price').val('');
							$('#booking_total').val('');
							$('#booking_tax').val('');
							$('#booking_deposit').val('');
							if($frmUpdateBooking.length > 0){
								calculatePrice();
							}
						}
					});
				});
			}
		}
		
		var $frmFilter = $(".frm-filter");
		if ($frmFilter.length > 0) {
			$frmFilter.on("change", "select[name='event_id']", function (e) {
				$frmFilter.submit();	
			});
		}
		
		var $PM = $("#payment_method");
		if ($PM.length > 0) {
			$PM.bind("change", function () {
				if ($("option:selected", this).val() == 'creditcard') {
					$(".ebcCC").show();
				} else {
					$(".ebcCC").hide();
				}
			});	
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminBookings&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBooking&id={:id}"},
						  {type: "menu", url: "#", text: '', items:[
  				              {text: myLabel.resend, url: "index.php?controller=pjAdminBookings&action=pjActionResend&id={:id}", ajax: false, render: true},
  				              {text: myLabel.print_tickets, url: myLabel.ticket_url + '{:unique_id}.pdf', ajax: false, render: true, target:"_blank"}
  				           ]}],
				columns: [
				          {text: myLabel.name, type: "text", sortable: true, editable: false, width: 150},
				          {text: myLabel.eventdate, type: "text", sortable: true, width: 150},
				          {text: myLabel.tickets, type: "text", sortable: true},
				          {text: myLabel.price, type: "text", sortable: true, editable: false, width:70},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100, options: [
				                                                                                     {label: myLabel.pending, value: "pending"}, 
				                                                                                     {label: myLabel.confirmed, value: "confirmed"},
				                                                                                     {label: myLabel.cancelled, value: "cancelled"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString,
				dataType: "json",
				fields: ['customer_name', 'event_date', 'customer_people', 'booking_total', 'booking_status'],
				paginator: {
					actions: [
					   {text: myLabel.export_selected, url: "index.php?controller=pjAdminBookings&action=pjActionExportBooking", ajax: false},
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminBookings&action=pjActionDeleteBookingBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminBookings&action=pjActionExportBooking", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminBookings&action=pjActionSaveBooking&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
			
			$(document).on("click", ".btn-all", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).addClass("pj-button-hover").siblings(".pj-button").removeClass("pj-button-hover");
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					status: "",
					q: "",
					unique_id: "",
					customer_name: "",
					customer_email: "",
					event_id: "",
					from_ticket: "",
					to_ticket: "",
					from_price: "",
					to_price: ""
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "id", "DESC", content.page, content.rowCount);
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
				$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "id", "DESC", content.page, content.rowCount);
				return false;
			}).on("submit", ".frm-filter", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					q: $this.find("input[name='q']").val(),
					unique_id: "",
					customer_name: "",
					customer_email: "",
					event_id: "",
					from_ticket: "",
					to_ticket: "",
					from_price: "",
					to_price: ""
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "id", "ASC", content.page, content.rowCount);
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
				$grid.datagrid("load", "index.php?controller=pjAdminBookings&action=pjActionGetBooking" + pjGrid.queryString, "id", "ASC", content.page, content.rowCount);
				return false;
			}).on("reset", ".frm-filter-advanced", function (e) {
				$(".pj-button-detailed").trigger("click");
				if (chosen) {
					$("#event_id").val('').trigger("liszt:updated");
				}
			});
			
		}
		
	});
})(jQuery_1_8_2);