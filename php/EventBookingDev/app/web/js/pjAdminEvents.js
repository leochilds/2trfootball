var jQuery_1_8_2 = $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmCreateEvent = $("#frmCreateEvent"),
			$frmUpdateEvent = $("#frmUpdateEvent"),
			validate = ($.fn.validate !== undefined),
			spinner = ($.fn.spinner !== undefined),
			$tabs = $("#tabs"),
			tipsy = ($.fn.tipsy !== undefined),			
			datagrid = ($.fn.datagrid !== undefined),
			tOpt = {
				select: function (event, ui) {
					$(":input[name='tab_id']").val(ui.panel.id);
				}
			};
		setCustom = function()
		{
			var step_1 = $('#clone_step_1').text();
			var layout = $('#layout').val();
			var file_name = $('#css_file').val();
			if(file_name == '')
			{
				step_1 = step_1.replace('{CSSFile}', '');
			}else{
				step_1 = step_1.replace('{CSSFile}', '&cssfile=' + file_name);
			}
			step_1 = step_1.replace('{LAYOUT}', layout);
			
			$('#install_step_1').val(step_1);
		};
		if ($tabs.length > 0 && tabs) {
			var $t = $(":input[name='tab_id']");
			$tabs.tabs({
				select: function(event, ui) {
					if ($t.length > 0) {
						$t.val(ui.index);
						if(ui.index > 3)
						{
							$('#button_container').css('display', 'none');
						}else{
							$('#button_container').css('display', 'block');
						}
					}
				}
			});
			if ($t.length > 0) {
				$tabs.tabs("select", parseInt($t.val(), 10));
			}
		}
		if($('#install_step_1').length > 0)
		{
			setCustom();
		}
		if (tipsy) {
			$(".listing-tip").tipsy({
				offset: 1,
				opacity: 1,
				html: true,
				gravity: "nw",
				className: "tipsy-listing"
			});
		}
		
		if ($frmCreateEvent.length > 0 && validate) {
			$frmCreateEvent.validate({
				rules: {
		            'price[]': {
		                required:true,
		                number: true
		            },
		            'title[]':{
		            	required:true
		            }
		        },
		        messages: {
		        	'price[]': {
		                required: myLabel.lblFieldRequired,
		                number: myLabel.lblInvalidPrice
		            },
		            'title[]':{
		            	required:myLabel.lblFieldRequired
		            }
		        },
				errorPlacement: function (error, element) {
					var element_name = element.attr('name');
					if( element_name.indexOf("price" ) !== -1 || (element_name.indexOf("title" ) !== -1 && element_name != 'event_title') )
					{
						error.insertAfter(element);
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function(event, validator) {
					$('#event_title').focus();
                }
			});
		}
		if ($frmUpdateEvent.length > 0 && validate) {
			$frmUpdateEvent.validate({
				rules: {
		            'price[]': {
		                required:true,
		                number: true
		            },
		            'title[]':{
		            	required:true
		            }
		        },
		        messages: {
		        	'price[]': {
		                required: myLabel.lblFieldRequired,
		                number: myLabel.lblInvalidPrice
		            },
		            'title[]':{
		            	required:myLabel.lblFieldRequired
		            }
		        },
				errorPlacement: function (error, element) {
					var element_name = element.attr('name');
					if( element_name.indexOf("price" ) !== -1 || (element_name.indexOf("title" ) !== -1 && element_name != 'event_title') )
					{
						error.insertAfter(element);
					}else{
						error.insertAfter(element.parent());
					}
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: "",
				invalidHandler: function (event, validator) {
				    if (validator.numberOfInvalids()) {
				    	var index = $(validator.errorList[0].element, this).closest("div[id^='tabs-']").index();
				    	if ($tabs.length > 0 && tabs && index !== -1) {
				    		$tabs.tabs(tOpt).tabs("option", "active", index-1);
				    	}
				    }
				}
			});
			
		}
		if (spinner) {
			$(".field-int").spinner({
				min: 0
			});
		}
		
		$('#repeat-monthly-date').on('change', function(e){
			if($(this).val() == 0)
			{
				$('#repeat-monthly-each').removeAttr('disabled');
				$('#repeat-monthly-day').removeAttr('disabled');
			}else{
				$('#repeat-monthly-each').attr('disabled', 'disabled');
				$('#repeat-monthly-day').attr('disabled', 'disabled');
			}
		});
		
		$('#repeat').on('change', function(e){
			$('p[id^="repeat_"]').css('display','none');
			$('div[id^="repeat_"]').css('display','none');
			$('#repeat_' + $(this).val()).css('display','block');
			if($(this).val() == 'none')
			{
				$('#repeat_box').css('display','none');
			}else{
				$('#repeat_box').css('display','block');
			}
		});
		
		$('.delete-image').on('click', function(e){
			e.preventDefault();
			if($frmCreateEvent.length > 0)
			{
				$("#image_container").remove();
				$("#copy_image").val(0);
			}else{
				var event_id = $(this).attr('rev');
				$("#dialogDeleteImage").dialog({
					autoOpen: false,
					resizable: false,
					draggable: false,
					width: 380,
					height:150,
					modal: true,
					
					buttons: {
						'Delete': function() {
							$.ajax({
								type: "POST",
								data: {
									id: event_id
								},
								dataType: 'json',
								url: "index.php?controller=pjAdminEvents&action=pjActionDeleteImage",
								success: function (res) {
									if(res.status == 1){
										$("#image_container").remove();
									}
								}
							});
							$(this).dialog('close');			
						},
						'Cancel': function() {
							$(this).dialog('close');
						}
					}
				});
				$("#dialogDeleteImage").dialog('open');
			}
		});
		
		if ($("#grid").length > 0 && datagrid) {
			function showBookings (str, obj) {
				if(obj.linked == '1')
				{
					return '<a href="index.php?controller=pjAdminBookings&action=pjActionIndex&event_id='+obj.id+'">'+str+'</a>';
				}else{
					return str;
				}
			}
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminEvents&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminEvents&action=pjActionDeleteEvent&id={:id}"},
				          {type: "menu", url: "#", text: myLabel.more, items:[
				              {text: myLabel.copy, url: "index.php?controller=pjAdminEvents&action=pjActionCreate&id={:id}", ajax: false, render: true},
				              {text: myLabel.tickets, url: "index.php?controller=pjAdminEvents&action=pjActionUpdate&id={:id}&tab_id=5", ajax: false, render: true}
				           ]}
				          ],
				columns: [{text: myLabel.eventdate, type: "text", sortable: true, editable: false, width:220},
				          {text: myLabel.eventtitle, type: "text", sortable: true, editable: true, width: 200},
				          {text: myLabel.tickets, type: "text", sortable: true, editable: false, width:75, align: 'center', renderer: showBookings},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width:80 ,options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminEvents&action=pjActionGetEvent",
				dataType: "json",
				fields: ['event_date', 'event_title', 'tickets', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminEvents&action=pjActionDeleteEventBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.revert_status, url: "index.php?controller=pjAdminEvents&action=pjActionStatusEvent", render: true},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminEvents&action=pjActionExportEvent", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminEvents&action=pjActionSaveEvent&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
			
		$(document).on("focusin", ".textarea_install", function (e) {
			$(this).select();
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
			$grid.datagrid("load", "index.php?controller=pjAdminEvents&action=pjActionGetEvent", "event_title", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminEvents&action=pjActionGetEvent", "event_title", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-status-1", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			return false;
		}).on("click", ".pj-status-0", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.post("index.php?controller=pjAdminEvents&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminEvents&action=pjActionGetEvent");
			});
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
				page: 1
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminEvents&action=pjActionGetEvent", "event_title", "ASC", content.page, content.rowCount);
			return false;
		}).on("focusin", ".datetimepick", function (e) {
			var minDateTime, maxDateTime,
				$this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
					timeFormat: $this.attr("lang"),
					stepMinute: 5
			};
			switch ($this.attr("name")) {
			case "event_start_ts":
				if($(".datetimepick[name='event_end_ts']").val() != '')
				{
					maxDateTime = $(".datetimepick[name='event_end_ts']").datetimepicker({
						firstDay: $this.attr("rel"),
						dateFormat: $this.attr("rev"),
						timeFormat: $this.attr("lang")
					}).datetimepicker("getDate");
					$(".datetimepick[name='event_end_ts']").datepicker("destroy").removeAttr("id");
					if (maxDateTime !== null) {
						custom.maxDateTime = maxDateTime;
					}
				}
				break;
			case "event_end_ts":
				if($(".datetimepick[name='event_start_ts']").val() != '')
				{
					minDateTime = $(".datetimepick[name='event_start_ts']").datetimepicker({
						firstDay: $this.attr("rel"),
						dateFormat: $this.attr("rev"),
						timeFormat: $this.attr("lang")
					}).datetimepicker("getDate");
					$(".datetimepick[name='event_start_ts']").datepicker("destroy").removeAttr("id");
					if (minDateTime !== null) {
						custom.minDateTime = minDateTime;
					}
				}
				break;
			}
			
			$(this).datetimepicker($.extend(o, custom));
			
		}).on("focusin", ".datepick", function (e) {
			var $this = $(this);
			$this.not('.hasDatepicker').datepicker({
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev")
			});
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
		}).on("click", "#ebc_add_price", function (e) {
			
			var len = $("#price_container > .price-box").length;
			var i = len + 1;
			var price_box_content = $('#clone_container').html();
			price_box_content = price_box_content.replace('{fieldint}', 'field-int');
			if (len === 1) {
				$("#price_container").append(price_box_content.replace(/{index}/g, 2));
			} else {
				$("#price_container > .price-box:last").after(price_box_content.replace(/{index}/g, i));
			}
			if (spinner) {
				$(".field-int").spinner({
					min: 0
				});
			}
			$('#num_prices').val(i);
			
		}).on("click", ".btn-remove-price", function (e) {
			var index = parseInt($(this).attr('lang'));
			var num_prices = parseInt($('#num_prices').val());
			
			$('#price_box_' + index).remove();
			if(index < num_prices){
				for(var i = index + 1; i <= num_prices; i++)
				{
					var new_index = i - 1;
					$('#price_box_' + i).attr('id', 'price_box_' + new_index);
					$('#title' + i).attr('id', 'title' + new_index);
					$('#price' + i).attr('id', 'price' + new_index);
					$('#available' + i).attr('id', 'available' + new_index);
					$('#ebc_remove_price_' + i).attr('lang', new_index);
					$('#ebc_remove_price_' + i).attr('id', 'ebc_remove_price_' + new_index);
				}
			}
			
			var len = $("#price_container > .price-box").length;
			if(len == 0)
			{
				len = 1;
				var price_box_content = $('#clone_container_1').html();
				price_box_content = price_box_content.replace('{fieldint}', 'field-int');
				$("#price_container").append(price_box_content.replace(/{index}/g, len));
				if (spinner) {
					$(".field-int").spinner({
						min: 0
					});
				}
				$('#num_prices').val(1);
			}else{
				$('#num_prices').val(len);
			}
		}).on("change", "#layout", function (e) {
			var layout = $(this).val();
			$('#css_file').val('front_' + layout + '.css');
			setCustom();
		});
	});
})(jQuery_1_8_2);