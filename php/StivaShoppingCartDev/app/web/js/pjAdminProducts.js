var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	if (window.tinyMCE !== undefined) {
		tinymce.init({
		    selector: "textarea.selector-full-desc",
		    plugins: [
		        "advlist autolink lists link image charmap print preview anchor",
		        "searchreplace visualblocks code fullscreen",
		        "insertdatetime media table contextmenu paste"
		    ],
		    //content_css: "app/web/css/ShoppingCart.css?" + new Date().getTime(),
		    width: 550,
		    height: 250,
		    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
		    setup: function (editor) {
		    	editor.on('change', function (e) {
		    		editor.editorManager.triggerSave();
		    		$(":input[name='" + editor.id + "']").valid();
		    	});
		    }
		});
	}
	$(function () {
		"use strict";
		
		var $frmCreateProduct = $('#frmCreateProduct'),
			$frmUpdateProduct = $('#frmUpdateProduct'),
			$frmProduct = $('.frmProduct'),
			$tabs = $("#tabs"),
			$gallery = $("#gallery"),
			$dialogDeleteExtra = $("#dialogDeleteExtra"),
			$dialogCopyExtra = $("#dialogCopyExtra"),
			$dialogCopyAttr = $("#dialogCopyAttr"),
			$dialogDeleteStock = $("#dialogDeleteStock"),
			$dialogImageStock = $("#dialogImageStock"),
			$dialogDeleteDigital = $("#dialogDeleteDigital"),
			
			$dialogAttrGroupDelete = $("#dialogAttrGroupDelete"),
			$dialogAttrDelete = $("#dialogAttrDelete"),
		
			$content = $("#content"),
			$datepick = $(".datepick"),
			gallery = ($.fn.gallery !== undefined),
			multiselect = ($.fn.multiselect !== undefined),
			dialog = ($.fn.dialog !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			autocomplete = ($.fn.autocomplete !== undefined),
			dOpts = {},
			vOpts = {
				ignore: ".ignore",
				rules: {
					name: "required",
					sku: {
						required: true,
						remote: "index.php?controller=pjAdminProducts&action=pjActionCheckSku"
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false
			},
			m = window.location.href.match(/&id=(\d+)/),
			product_id, $similar
		;
		
		if (m !== null) {
			product_id = m[1];
		}
		
		if (multiselect) {
			$("#category_id").multiselect({
				show: ['fade', 250],
				hide: ['fade', 250]
			});
		}
		
		if ($datepick.length > 0) {
			dOpts = $.extend(dOpts, {
				firstDay: $datepick.attr("rel"),
				dateFormat: $datepick.attr("rev")
			});
		}
		
		if ($gallery.length > 0 && gallery) {
			$gallery.gallery({
				compressUrl: "index.php?controller=pjGallery&action=pjActionCompressGallery&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash,
				getUrl: "index.php?controller=pjGallery&action=pjActionGetGallery&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash,
				deleteUrl: "index.php?controller=pjGallery&action=pjActionDeleteGallery",
				emptyUrl: "index.php?controller=pjGallery&action=pjActionEmptyGallery&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash,
				rebuildUrl: "index.php?controller=pjGallery&action=pjActionRebuildGallery&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash,
				resizeUrl: "index.php?controller=pjGallery&action=pjActionResizeGallery&id={:id}&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash + ($frmUpdateProduct.length > 0 ? "&query_string=" + encodeURIComponent("controller=pjAdminProducts&action=pjActionUpdate&id=" + myGallery.foreign_id + "&tab=4") : ""),
				rotateUrl: "index.php?controller=pjGallery&action=pjActionRotateGallery",
				sortUrl: "index.php?controller=pjGallery&action=pjActionSortGallery",
				updateUrl: "index.php?controller=pjGallery&action=pjActionUpdateGallery",
				uploadUrl: "index.php?controller=pjGallery&action=pjActionUploadGallery&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash,
				watermarkUrl: "index.php?controller=pjGallery&action=pjActionWatermarkGallery&foreign_id=" + myGallery.foreign_id + "&hash=" + myGallery.hash
			});
		}
		
		$content.delegate(".datepick", "focusin", function (e) {
			$(this).datepicker(dOpts);
		}).delegate("input[name='digital_choose']", "change", function () {
			switch (parseInt($(this).val(), 10)) {
			case 1:
				$(".digitalFile").show();
				$(".digitalPath").hide();
				break;
			case 2:
				$(".digitalFile").hide();
				$(".digitalPath").show();
				break;
			}
		}).delegate(".btnDigitalDelete", "click", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$("#dialogDeleteDigital").data("id", $(this).attr("rel")).dialog("open");
			return false;
		// Images
		}).delegate(".btnImageStock", "click", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$("#dialogImageStock").data("lnk", $(this)).dialog("open");
			return false;
		// -- Extras
		}).delegate(".btnAddExtra", "click", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $clone = $("#boxClone").clone(),
				c = $clone.html(),
				index1 = Math.ceil(Math.random() * 999999).toString(),
				index2 = Math.ceil(Math.random() * 999999).toString();
			c = c.replace(/\{INDEX\}/g, "x_" + index1);
			c = c.replace(/\{X\}/g, "y_" + index2);
			//$(this).before(c);
			$(this).parent().prev().append(c);
			return false;
		}).delegate(".btnAddExtraItem", "click", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $clone = $("#boxCloneTbl").find("tbody").clone(),
				c = $clone.html(),
				index2 = Math.ceil(Math.random() * 999999).toString();
			c = c.replace(/\{INDEX\}/g, $(this).data("index"));
			c = c.replace(/\{X\}/g, "y_" + index2);
			$(c).appendTo($(this).siblings("table").eq(0).find("tbody"));
			return false;
		}).delegate(".btnDeleteExtra", "click", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$("#dialogDeleteExtra").data("lnk", $(this)).dialog("open");
			return false;
		}).delegate(".btnDeleteExtraTmp", "click", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).closest(".extraBox").remove();
			return false;
		}).delegate(".btnRemoveExtraItem", "click", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).parent().parent().remove();
			return false;
		}).delegate(":input[name^='extra_type[']", "change", function () {
			var $this = $(this),
				$boxSingle = $this.closest(".extraBox").find(".boxSingle"),
				$boxMulti = $this.closest(".extraBox").find(".boxMulti");
			switch ($("option:selected", $this).val()) {
			case 'single':
				$boxSingle.find(":input").prop("disabled", false);
				$boxSingle.show();
				$boxMulti.hide();
				$boxMulti.find(":input").prop("disabled", true);
				break;
			case 'multi':
				$boxSingle.hide();
				$boxSingle.find(":input").prop("disabled", true);
				$boxMulti.find(":input").prop("disabled", false);
				$boxMulti.show();
				break;
			}
		}).on("click", ".btnCopyExtra", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if (dialog && $dialogCopyExtra.length > 0) {
				$dialogCopyExtra.dialog("open");
			}
			return false;
		// -- Digital
		}).on("change", "input[name='is_digital']", function (e) {
			if ($(this).is(":checked")) {
				$tabs.tabs("option", "disabled", [2]);
			} else {
				$tabs.tabs("option", "disabled", []);
			}
		// -- Attributes
		}).on("click", ".btnAddAttribute", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $clone = $("#boxAddAttribute").clone(),
				$this = $(this),
				c = $clone.html(),
				index1 = Math.ceil(Math.random() * 999999).toString(),
				index2 = Math.ceil(Math.random() * 999999).toString();
			c = c.replace(/\{INDEX\}/g, "x_" + index1).replace(/\{X\}/g, "y_" + index2);
			$(c).appendTo("#boxAttributes");
			return false;
		}).on("click", ".btnAddAttr", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $clone = $("#boxAddAttr").clone(),
				$this = $(this),
				c = $clone.html(),
				index = Math.ceil(Math.random() * 999999).toString();
			c = c.replace(/\{INDEX\}/g, $this.attr("rel")).replace(/\{X\}/g, "y_" + index);
			$(c).appendTo( $this.closest(".attrBox").find(".attrBoxRowStick") );
			return false;
		}).on("click", ".btnAttrGroupDelete", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogAttrGroupDelete.data("id", $(this).data("id")).dialog("open");
			return false;
		}).on("click", ".btnAttrGroupRemove", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).closest(".attrBox").remove();
			return false;
		}).on("click", ".btnAttrDelete", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$dialogAttrDelete.data("id", $(this).data("id")).dialog("open");
			return false;
		}).on("click", ".btnAttrRemove", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).closest(".attrBoxRowItems").remove();
			return false;
			
		}).delegate(".btnCopyAttribute", "click", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$("#dialogCopyAttr").dialog("open");
			return false;
		// -- Attributes
			
		// Stock
		}).delegate(".btnStockAdd", "click", function (e) {			
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $clone, c, index,
				$tbody = $(this).parent().siblings(".pj-table").find("tbody");
			
			handleDigitalInStock.call(null, function () {
				$clone = $("#boxStockCloneTbl").find("tbody").clone();
				c = $clone.html();
				index = Math.ceil(Math.random() * 999999).toString();
				c = c.replace(/\{INDEX\}/g, "x_" + index);
				$(c).appendTo($tbody);
			});
			return false;
		}).delegate(".btnRemoveStock", "click", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).parent().parent().remove();
			return false;
		}).delegate(".btnDeleteStock", "click", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$("#dialogDeleteStock").data("lnk", $(this)).dialog("open");
			return false;
		});
		
		function closeAttr() {
			$("#boxAttributes").find(".attrBox").find(".attr, .attr_item").each(function (i, el) {
				$(el).find("input, a").hide().end().find("abbr").show();
			});
		}
		
		if ($frmCreateProduct.length > 0) {
			var validator = $frmCreateProduct.submit(function() {
				// update underlying textarea before submit validation
				tinymce.activeEditor.editorManager.triggerSave();
			}).validate(vOpts);
		}
		
		if ($frmUpdateProduct.length > 0) {
			//$.validator.messages.required = "";
			vOpts.rules.sku.remote += "&id=" + $frmUpdateProduct.find("input[name='id']").val()
			var validator = $frmUpdateProduct.submit(function() {
				handleDigitalInStock.call(null);
				// update underlying textarea before submit validation
				tinymce.activeEditor.editorManager.triggerSave();
			}).validate(vOpts);
		}
		
		function handleDigitalInStock(callback) {
			var $tbody = $(".btnStockAdd").parent().siblings(".pj-table").find("tbody");
			if ($("input[name='is_digital']").is(":checked") && $tbody.find("tr").length > 0) {
				$tbody.find("tr:gt(0)").remove();
			} else {
				if (callback !== undefined && typeof callback === "function") {
					callback.call(null);
				}
			}
		}
		
		var tOpt = {};
		if ($tabs.length > 0) {
			tOpt = {
				select: function (event, ui) {
					$("input[name='tab']").val(ui.index);
					switch (ui.tab.hash) {
						case "#tabs-5":
							handleDigitalInStock.call(null);
							break;
						case "#tabs-7":
							if ($("#boxSimilar").length > 0 && datagrid) {
								
								$similar = $("#boxSimilar").datagrid({
									buttons: [{type: "delete", url: "index.php?controller=pjAdminProducts&action=pjActionDeleteSimilar&id={:id}"}],
									columns: [{text: myLabel.name, type: "text", sortable: true, editable: false},
									          {text: myLabel.sku, type: "text", sortable: true, editable: false},
									          {text: myLabel.status, type: "select", sortable: true, editable: false, options: [
									                                                                                     {label: $.datagrid.messages.pr_status_1, value: 1}, 
									                                                                                     {label: $.datagrid.messages.pr_status_2, value: 2},
									                                                                                     {label: $.datagrid.messages.pr_status_3, value: 3}
									                                                                                     ], applyClass: "pj-status"}],
									dataUrl: "index.php?controller=pjAdminProducts&action=pjActionGetSimilar&id=" + product_id,
									dataType: "json",
									fields: ['name', 'sku', 'status'],
									paginator: {
										actions: [
										   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminProducts&action=pjActionDeleteSimilarBulk", render: true, confirmation: myLabel.delete_confirmation}
										],
										gotoPage: true,
										paginate: true,
										total: true,
										rowCount: true
									},
									saveUrl: null,
									select: {
										field: "id",
										name: "record[]"
									}
								});
								
							}
							break;
					}
				},
				ajaxOptions: {
					success: function () {}
				}
			};
			if ($("input[name='is_digital']").is(":checked")) {
				tOpt.disabled = [2];
			}
			$tabs.tabs(tOpt);
			
			var m = window.location.href.match(/&tab=(\d+)/);
			if (m !== null) {
				$tabs.tabs("option", "active", m[1]);
			}
		}
		
		if ($dialogAttrGroupDelete && dialog) {
			$dialogAttrGroupDelete.dialog({
				autoOpen: false,
				resizable: false,
				draggable: false,
				modal: true,
				buttons: {
					'Delete': function () {
						$.post("index.php?controller=pjAdminProducts&action=pjActionAttrGroupDelete", {
							"id": $dialogAttrGroupDelete.data("id")
						}).done(function () {
							getAttributes.call(null);
							$dialogAttrGroupDelete.dialog("close");
						});
					},
					'Cancel': function () {
						$(this).dialog("close");
					}
				}
			});
		}
				
		if ($dialogAttrDelete && dialog) {
			$dialogAttrDelete.dialog({
				autoOpen: false,
				resizable: false,
				draggable: false,
				modal: true,
				buttons: {
					'Delete': function () {
						$.post("index.php?controller=pjAdminProducts&action=pjActionAttrDelete", {
							"id": $dialogAttrDelete.data("id")
						}).done(function () {
							getAttributes.call(null);
							$dialogAttrDelete.dialog("close");
						});
					},
					'Cancel': function () {
						$(this).dialog("close");
					}
				}
			});
		}
		
		if ($dialogDeleteExtra.length > 0 && dialog) {
			$dialogDeleteExtra.dialog({
				autoOpen: false,
				resizable: false,
				draggable: false,
				modal: true,
				buttons: {
					'Delete': function() {
						var $lnk = $(this).data('lnk');
						$.post("index.php?controller=pjAdminProducts&action=pjActionDeleteExtra", {
							id: $lnk.attr("rel")
						}).done(function (data) {
							if (!data.code) {
								return;
							}
							switch (parseInt(data.code, 10)) {
								case 200:
									$lnk.closest(".extraBox").remove();
									break;
							}
						});
						$(this).dialog('close');			
					},
					'Cancel': function() {
						$(this).dialog('close');
					}
				}
			});
		}
		
		if ($dialogDeleteStock.length > 0 && dialog) {
			$dialogDeleteStock.dialog({
				autoOpen: false,
				resizable: false,
				draggable: false,
				modal: true,
				buttons: {
					'Delete': function() {
						var $lnk = $(this).data('lnk');
						$.post("index.php?controller=pjAdminProducts&action=pjActionDeleteStock", {
							id: $lnk.attr("rel")
						}).done(function (data) {
							if (!data.code) {
								return;
							}
							switch (parseInt(data.code, 10)) {
								case 200:
									$lnk.parent().parent().remove();
									break;
							}
						});
						$(this).dialog('close');			
					},
					'Cancel': function() {
						$(this).dialog('close');
					}
				}
			});
		}
		
		if ($dialogImageStock.length > 0 && dialog) {
			$dialogImageStock.dialog({
				autoOpen: false,
				resizable: false,
				draggable: false,
				modal: true,
				width: 600,
				height: 400,
				open: function () {
					var $lnk = $(this).data("lnk");
					$.get("index.php?controller=pjAdminProducts&action=pjActionLoadImages", {
						product_id: $(":input[name='id']").val(),
						image_id: $lnk.attr("rel")
					}).done(function (data) {
						$dialogImageStock.html(data);
					});
				},
				close: function () {
					$dialogImageStock.html("");
				},
				buttons: {
					'Cancel': function() {
						$(this).dialog('close');
					}
				}
			});
		}
		
		if ($dialogDeleteDigital.length > 0 && dialog) {
			$dialogDeleteDigital.dialog({
				autoOpen: false,
				resizable: false,
				draggable: false,
				modal: true,
				buttons: {
					'Delete': function() {
						var $this = $(this);
						$.post("index.php?controller=pjAdminProducts&action=pjActionDeleteDigital", {
							id: $this.data("id")
						}).done(function (data) {
							$("#boxDigital").html(data);
						});
						$(this).dialog('close');			
					},
					'Cancel': function() {
						$(this).dialog('close');
					}
				}
			});
		}
		
		if ($dialogCopyExtra.length > 0 && dialog) {
			$dialogCopyExtra.dialog({
				autoOpen: false,
				resizable: false,
				draggable: false,
				modal: true,
				width: 640,
				height: 480,
				open: function () {
					$dialogCopyExtra.html("");
					var cOpt = {};
					if ($frmUpdateProduct.length > 0) {
						cOpt.product_id = $frmUpdateProduct.find("input[name='id']").val();
					}
					$.get("index.php?controller=pjAdminProducts&action=pjActionGetProducts&copy=Extra", cOpt).done(function (data) {
						$dialogCopyExtra.html(data);
						$dialogCopyExtra.dialog("option", "position", "center");
						$dialogCopyExtra.find(".btnCopy").button();
					});
				},
				buttons: {
					'Close': function() {
						$(this).dialog('close');
					}
				}
			});
		}
		
		if ($dialogCopyAttr.length > 0 && dialog) {
			$dialogCopyAttr.dialog({
				autoOpen: false,
				resizable: false,
				draggable: false,
				modal: true,
				width: 640,
				height: 480,
				open: function () {
					$dialogCopyAttr.html("");
					var cOpt = {};
					if ($frmUpdateProduct.length > 0) {
						cOpt.product_id = $frmUpdateProduct.find("input[name='id']").val();
					}
					$.get("index.php?controller=pjAdminProducts&action=pjActionGetProducts&copy=Attr", cOpt).done(function (data) {
						$dialogCopyAttr.html(data);
						$dialogCopyAttr.dialog("option", "position", "center");
						$dialogCopyAttr.find(".btnCopy").button();
					});
				},
				buttons: {
					'Close': function() {
						$(this).dialog('close');
					}
				}
			});
		}
		
		if ($("#similar_id").length > 0 && autocomplete) {
			
			var cache = {}, lastXhr;
			$("#similar_id").autocomplete({
				minLength: 2,
				source: function(request, response) {
					var term = request.term;
					//if (term in cache) {
						//response(cache[term]);
						//return;
					//}
					lastXhr = $.getJSON("index.php?controller=pjAdminProducts&action=pjActionSearchProducts&id=" + product_id, request, function(data, status, xhr) {
						//cache[term] = data;
						if (xhr === lastXhr) {
							response(data);
						}
					});
				},
				select: function(event, ui) {
					$.post("index.php?controller=pjAdminProducts&action=pjActionAddSimilar", {
						"product_id": product_id,
						"similar_id": ui.item.value
					}).done(function (data) {
						$("#similar_id").val("");
						var content = $similar.datagrid("option", "content");
						$similar.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetSimilar&id=" + product_id, "name", "ASC", content.page, content.rowCount);
					});
					event.preventDefault();
				}
			});
			
		}
		
		function formatDefault (str, obj) {
			if (obj.role_id == 3) {
				return '<a href="#" class="pj-status-icon pj-status-' + (str == 'F' ? '0' : '1') + '" style="cursor: ' +  (str == 'F' ? 'pointer' : 'default') + '"></a>';
			} else {
				return '<a href="#" class="pj-status-icon pj-status-1" style="cursor: default"></a>';
			}
		}
		
		function formatImage (path, obj) {
			var src = 'app/web/img/frontend/noimg.png';
			if (path !== null && path.length > 0) {
				src = path;
			}
			return ['<a href="index.php?controller=pjAdminProducts&action=pjActionUpdate&id=', obj.id, '"><img src="', src, '" alt="" class="s-Img" /></a>'].join('');
		}
		
		function formatMinPrice (price, obj) {
			return obj.min_price_format;
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminProducts&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminProducts&action=pjActionDeleteProduct&id={:id}"}
				          ],
				columns: [{text: myLabel.image, type: "text", sortable: true, editable: false, renderer: formatImage},
				          {text: myLabel.name, type: "text", sortable: true, editable: true, width: 180},
				          {text: myLabel.sku, type: "text", sortable: true, editable: true},
				          {text: myLabel.stock, type: "text", sortable: true, editable: false, align: "right"},
				          {text: myLabel.price, type: "text", sortable: true, editable: false, renderer: formatMinPrice, align: "right", width: 70},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, options: [
				                                                                                     {label: $.datagrid.messages.pr_status_1, value: 1}, 
				                                                                                     {label: $.datagrid.messages.pr_status_2, value: 2}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminProducts&action=pjActionGetProduct",
				dataType: "json",
				fields: ['pic', 'name', 'sku', 'total_stock', 'min_price', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminProducts&action=pjActionDeleteProductBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminProducts&action=pjActionExportProduct", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminProducts&action=pjActionSaveProduct&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		function formatName(val, obj) {
			var parts, arr = [];
			for (var i = 0, iCnt = obj.stock_attr.length; i < iCnt; i++) {
				parts = obj.stock_attr[i].split("~:~");
				arr.push(parts[0] + ": " + parts[1]);
			}
			return ['<a href="index.php?controller=pjAdminProducts&action=pjActionUpdate&id=', obj.product_id, '" class="s-Pic"><img src="', obj.pic, '" alt="" class="s-Img" /></a>',
			        '<span class="s-Name"><a href="index.php?controller=pjAdminProducts&action=pjActionUpdate&id=', obj.product_id, '">', obj.name, '</a></span>',
			        (arr.length > 0 ? ['<span class="s-Attr">(', arr.join(", "), ')</span>'].join('') : '')
			        ].join("");
		}
		
		function formatPrice(val, obj) {
			return obj.price_formated;
		}
		
		if ($("#grid_stock").length > 0 && datagrid) {
			
			var $grid_stock = $("#grid_stock").datagrid({
				buttons: [],
				columns: [{text: myLabel.name, type: "text", sortable: true, editable: false, renderer: formatName},
				          {text: myLabel.price, type: "text", sortable: true, editable: true, width: 100, editableWidth: 70, renderer: formatPrice},
				          {text: myLabel.qty, type: "spinner", min: 0, max: 4294967295, step: 1, sortable: true, editable: true, width: 100, editableWidth: 70}],
				dataUrl: "index.php?controller=pjAdminProducts&action=pjActionGetStock",
				dataType: "json",
				fields: ['name', 'price', 'qty'],
				paginator: {
					actions: [],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminProducts&action=pjActionSaveStock&id={:id}",
				select: false
			});
		}
		
		function getAttributes() {
			var product_id, hash, obj = {};
			product_id = $frmProduct.find("input[name='id']").val();
			hash = $frmProduct.find("input[name='hash']").val();
			if (product_id !== undefined && product_id !== "") {
				obj.product_id = product_id;
			} else if (hash !== undefined && hash !== "") {
				obj.hash = hash;
			}
			
			$.get("index.php?controller=pjAdminProducts&action=pjActionGetAttributes", obj).done(function (data) {
				$("#boxAttributes").html(data);
			});
		}
		
		function getExtras() {
			$.get("index.php?controller=pjAdminProducts&action=pjActionGetExtras", {
				"product_id": $frmProduct.find("input[name='id']").val()
			}).done(function (data) {
				$("#boxExtras").html(data);
			});
		}

		$(document).on("click", ".btnCopy", function () {
			var product_id, hash,
				$this = $(this),
				obj = {"from_product_id": $this.val()};
			product_id = $frmProduct.find("input[name='id']").val();
			hash = $frmProduct.find("input[name='hash']").val();
			if (product_id !== undefined && product_id !== "") {
				obj.product_id = product_id;
			} else if (hash !== undefined && hash !== "") {
				obj.hash = hash;
			}
			
			if ($this.hasClass("btnCopyAttr")) {
				
				$.post("index.php?controller=pjAdminProducts&action=pjActionAttrCopy", obj).done(function (data) {
					getAttributes.call(null);
				});
				$dialogCopyAttr.dialog('close');
				
			} else if ($this.hasClass("btnCopyExtra")) {
				$.post("index.php?controller=pjAdminProducts&action=pjActionExtraCopy", obj).done(function (data) {
					getExtras.call(null);
				});
				$dialogCopyExtra.dialog('close');
			}
		}).on("click", ".stock-image", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				id = $this.attr("rel"),			
				$a = $("<a>", {
					"href": "#"
				}).addClass("btnImageStock").attr("rel", id);
			
			$("<img>", {
				"src": $this.find("img").attr("src")
			}).addClass("in-stock").appendTo($a);
			
			$dialogImageStock.data("lnk").siblings("span").find("input[name^='stock_image_id']").val(id);
			$dialogImageStock.data("lnk").replaceWith($a);
			$dialogImageStock.dialog("close");
			return false;
		}).keyup(function (e) {
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			if (key == 27) {
				closeAttr.apply(null, []);
			}
		}).delegate("body", "click", function (e) {
			var $target = $(e.target);
			if (!$target.hasClass("attr_sign")) {
				closeAttr.apply(null, []);
			}
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
			$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct", "name", "ASC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct", "name", "ASC", content.page, content.rowCount);
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
			$.post("index.php?controller=pjAdminProducts&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct");
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
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct", "id", "ASC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter-stock", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid_stock.datagrid("option", "content"),
				cache = $grid_stock.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid_stock.datagrid("option", "cache", cache);
			$grid_stock.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetStock", "name", "ASC", content.page, content.rowCount);
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
			cache.q = "";
			if (cache.is_digital) {
				delete cache.is_digital;
			}
			if (cache.is_featured) {
				delete cache.is_featured;
			}
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct", "id", "DESC", content.page, content.rowCount);
			return false;
		}).on("reset", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			
			$(this).find(":input")
				.not(':button, :submit, :reset, :hidden')
				.removeAttr("checked").removeAttr("selected")
				.not(':checkbox, :radio')
				.val("");
		
			
			$grid.datagrid("option", "cache", {});
			$grid.datagrid("load", "index.php?controller=pjAdminProducts&action=pjActionGetProduct", "id", "DESC", 1, 10);
			
			$(".pj-button-detailed").trigger("click");
			
			return false;
		});		
	});
})(jQuery_1_8_2);