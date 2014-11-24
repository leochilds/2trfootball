(function (window, undefined) {
	
	var d = window.document;
	
	function PhpEvtCal(options) {
		if (!(this instanceof PhpEvtCal)) {
			return new PhpEvtCal(options);
		}
		this.options = {};
		this.main_content = null;
		this.event_detail = null;
		this.month = null;
		this.year = null;
		this.current_month = null;
		this.current_year = null;
		this.view_mode = null;
		this.category = null;
		this.page = null;
		this.period = null;
		this.num_events = null;
		this.selected_date = null;
		this.event_id = null;
		this.init(options);
		return this;
	}
	
	PhpEvtCal.prototype = {
		disableButtons: function(){
			var self = this;
			var arr = JABB.Utils.getElementsByClass("ebcal-button", d.getElementById('phpevtcal_content_' + self.options.index), "INPUT");
			for (i = 0, len = arr.length; i < len; i++) {
				arr[i].disabled = true;
				JABB.Utils.addClass(arr[i], 'ebcal-button-disabled');
			}
		},
		enableButtons: function(){
			var self = this;
			var arr = JABB.Utils.getElementsByClass("ebcal-button", d.getElementById('phpevtcal_content_' + self.options.index), "INPUT");
			for (i = 0, len = arr.length; i < len; i++) {
				arr[i].disabled = false;
				JABB.Utils.removeClass(arr[i], 'ebcal-button-disabled');
			}
		},
		bindCategory: function()
		{
			var self = this;
			JABB.Utils.addEvent(d.getElementById('phpevtcal_category_' + self.options.index), "change", function () {
				var $value = this.value;
				self.category = $value;
				self.loadEvents();
			});
		},
		bindMonthView: function()
		{
			var self = this;
			var arr = JABB.Utils.getElementsByClass("nav-arrow", d.getElementById('phpevtcal_nav_bar_' + self.options.index), "a");
			for (i = 0, len = arr.length; i < len; i++) {
				arr[i].onclick = function () {
					var rev = this.getAttribute("rev");
					var m = parseInt(self.month, 10),
						y = parseInt(self.year, 10);
					if(rev == 'next')
					{
						m = m + 1;
						if(m > 12){
							m = 1;
							y = y + 1;
						}
					}else{
						m = m -1;
						if(m < 1){
							m = 12;
							y = y - 1;
						}
					}
					if(m < 10){
						self.month = '0' + m;
					}else{
						self.month = m;
					}
					self.year = y;
					
					self.loadEvents();			
				};
			}
			var arr = JABB.Utils.getElementsByClass("short-month", d.getElementById('phpevtcal_month_bar_' + self.options.index), "a");
			for (i = 0, len = arr.length; i < len; i++) {
				arr[i].onclick = function () {
					var rev = this.getAttribute("rev"),
						rel = this.getAttribute("rel");
					self.month = rel;
					self.year = rev;
					self.loadEvents();
				};
			}
			
			var btn_arr = JABB.Utils.getElementsByClass("ebcal-buy-ticket", self.event_detail, "input");
			for (i = 0, len = btn_arr.length; i < len; i++) {
				btn_arr[i].onclick = function () {
					var event_id = this.getAttribute("lang");
					self.event_detail.innerHTML = self.options.message_2;
					self.loadBookingForm(event_id, null);
				};
			}
		},
		validateBookingForm: function (btn) {
			var self = this,
				re = /([0-9a-zA-Z\.\-\_]+)@([0-9a-zA-Z\.\-\_]+)\.([0-9a-zA-Z\.\-\_]+)/,
				message = "";
			
			var frm = d.forms[this.options.booking_form_name];
			for (var i = 0, len = frm.elements.length; i < len; i++) {
				var cls = frm.elements[i].className;
				if (cls.indexOf("ebcal-required") !== -1 && frm.elements[i].disabled === false) {
					switch (frm.elements[i].nodeName) {
					case "INPUT":
						switch (frm.elements[i].type) {
						case "checkbox":
						case "radio":
							if (!frm.elements[i].checked && frm.elements[i].getAttribute("lang")) {
								message += "\n - " + frm.elements[i].getAttribute("lang"); 
							}
							break;
						default:
							if (frm.elements[i].value.length === 0 && frm.elements[i].getAttribute("lang")) {
								message += "\n - " + frm.elements[i].getAttribute("lang");
							}
							break;
						}
						break;
					case "TEXTAREA":
						if (frm.elements[i].value.length === 0 && frm.elements[i].getAttribute("lang")) {						
							message += "\n - " + frm.elements[i].getAttribute("lang");
						}
						break;
					case "SELECT":
						switch (frm.elements[i].type) {
						case 'select-one':
							if (frm.elements[i].value.length === 0 && frm.elements[i].getAttribute("lang")) {
								message += "\n - " + frm.elements[i].getAttribute("lang"); 
							}
							break;
						case 'select-multiple':
							var has = false;
							for (j = frm.elements[i].options.length - 1; j >= 0; j = j - 1) {
								if (frm.elements[i].options[j].selected) {
									has = true;
									break;
								}
							}
							if (!has && frm.elements[i].getAttribute("lang")) {
								message += "\n - " + frm.elements[i].getAttribute("lang");
							}
							break;
						}
						break;
					default:
						break;
					}
				}
				if (cls.indexOf("ebcal-email") !== -1) {
					if (frm.elements[i].nodeName === "INPUT" && frm.elements[i].value.length > 0 && frm.elements[i].value.match(re) == null) {
						message += "\n - " + this.options.validation.error_email;
					}
				}
			}
			var price_arr = JABB.Utils.getElementsByClass(this.options.class_name_price, d.forms[this.options.booking_form_name], "SELECT"),
				cnt = 0;
			for (var i = 0, len = price_arr.length; i < len; i++) {
				cnt += parseInt(price_arr[i].options[price_arr[i].selectedIndex].value, 10);
			}
			if (cnt === 0) {
				message += "\n - " + this.options.validation.error_min;
			}
			if (message.length === 0) {
				return true;
			} else {
				this.errorHandler(message);		
				//btn.disabled = false;
				self.enableButtons();
				return false;
			}
		},
		validateBookingSummary: function (btn) {
			var self = this,
				pass = true,
				message = "\n" + this.options.validation.error_payment;
			
			if (pass) {
				return true;
			} else {
				this.errorHandler(message);
				self.enableButtons();
				//btn.disabled = false;
				return false;
			}
		},
		loadPrices: function()
		{
			var self = this,
				total_price = 0,
				tax = 0,
				deposit = 0,
				total_amount = 0,
				p = d.getElementById(self.options.container_price),
				t = d.getElementById(self.options.container_tax),
				de = d.getElementById(self.options.container_deposit),
				a = d.getElementById(self.options.container_total_amount),
				related = JABB.Utils.getElementsByClass("ebcal-price-related", d.forms[self.options.booking_form_name], "P"),
				arr = JABB.Utils.getElementsByClass(self.options.class_name_price, d.forms[self.options.booking_form_name], "SELECT");
			for (var i = 0, len = arr.length; i < len; i++) {
				var price = arr[i].getAttribute("lang"),
					num_people = arr[i].options[arr[i].selectedIndex].value; 
				total_price += parseFloat(price) * parseInt(num_people, 10);
			}
			if(total_price <= 0)
			{
				p.innerHTML = "";
				for (var i = 0, len = related.length; i < len; i++) {
					related[i].style.display = "none";
					required = JABB.Utils.getElementsByClass("ebcal-required", related[i], null);
					for (var j = 0, jlen = required.length; j < jlen; j++) {
						required[j].disabled = true;
					}
				}
			}else{
				tax = parseFloat(self.options.tax) * total_price / 100;
				total_amount = total_price + tax;
				deposit = parseFloat(self.options.deposit) * total_amount / 100;
				p.innerHTML = self.formatCurrency(total_price, self.options.currency);
				t.innerHTML = self.formatCurrency(tax, self.options.currency);
				a.innerHTML = self.formatCurrency(total_amount, self.options.currency);
				de.innerHTML = self.formatCurrency(deposit, self.options.currency);
				
				d.forms[self.options.booking_form_name]['total_price'].value = total_price;
								
				for (var i = 0, len = related.length; i < len; i++) {
					related[i].style.display = "";
					required = JABB.Utils.getElementsByClass("ebcal-required", related[i], null);
					for (var j = 0, jlen = required.length; j < jlen; j++) {
						required[j].disabled = false;
					}
				}
			}
			
		},
		loadPaymentForm: function(obj)
		{
			var self = this,
				mc, div;
			JABB.Ajax.sendRequest(self.options.get_payment_form_url, function (req) {
				mc = d.getElementById(self.options.container_message)
				if (mc && mc.parentNode) {
					div = d.createElement("div");
					div.innerHTML = req.responseText;
					mc.parentNode.insertBefore(div, mc);
					
					if (typeof d.forms[self.options.payment[obj.payment]] != 'undefined') {
						
						d.forms[self.options.payment[obj.payment]].submit();
					} else {
						self.event_detail.innerHTML = self.options.message_6;
						window.location.href = self.options.thankyou_url;
					}
				}
				
			}, "id=" + obj.booking_id);
		},
		bindBookingSummary: function(post)
		{
			var self = this;
			if (d.forms[self.options.booking_summary_name] && d.forms[self.options.booking_summary_name][self.options.booking_summary_submit_name]) {
				JABB.Utils.addEvent(d.forms[self.options.booking_summary_name][self.options.booking_summary_submit_name], "click", function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					var $this = this;
					var btn_cancel = d.forms[self.options.booking_summary_name][self.options.booking_summary_cancel_name];
					
					self.disableButtons();
					
					d.getElementById(self.options.container_message).style.display = "block";
					if (!self.validateBookingSummary($this)) {
						return;
					}
					JABB.Ajax.postJSON(self.options.load_booking_save_url, function (json) {
						switch (json.code) {
						case 100:
							self.errorHandler('\n' + self.options.message_7);
							self.enableButtons();
							break;
						case 200:
							d.getElementById(self.options.container_message).style.display = "none";
							self.loadPaymentForm(json);
							break;
						}																								
					}, post + "&" + JABB.Utils.serialize(d.forms[self.options.booking_summary_name]) + "&event_id=" + self.event_id);
				});
			}
			if (d.forms[self.options.booking_summary_name] && d.forms[self.options.booking_summary_name][self.options.booking_summary_cancel_name]) {
				JABB.Utils.addEvent(d.forms[self.options.booking_summary_name][self.options.booking_summary_cancel_name], "click", function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					self.disableButtons();
					
					self.event_detail.innerHTML = self.options.message_2;
					self.loadBookingForm(self.event_id, post);
				});
			}
			
			var back_arr = JABB.Utils.getElementsByClass("ebcal-back", d.getElementById('phpevtcal_container_' + self.options.index), "A");
			if(back_arr.length > 0)
			{
				back_arr[0].onclick = function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					var event_id = this.getAttribute('rev');
					self.event_detail.innerHTML = self.options.message_2;
					self.loadBookingForm(self.event_id, post);
				};
			}
			
		},
		loadBookingSummary: function(post)
		{
			var self = this,
				qs = ["&layout=", self.options.layout, "&cate=", self.category, "&event_id=", self.event_id].join("");
			JABB.Ajax.sendRequest(self.options.load_booking_summary_url + qs, function (req) {
				self.event_detail.innerHTML = req.responseText;
				self.bindBookingSummary(post);
				self.bindClose();
			}, post);
		},
		bindBookingForm: function()
		{
			var self = this;
			// Add "change" event to Payment method combo box
			if (d.forms[self.options.booking_form_name] && d.forms[self.options.booking_form_name][self.options.booking_form_payment_method]) {
				JABB.Utils.addEvent(d.forms[self.options.booking_form_name][self.options.booking_form_payment_method], "change", function () {
					// if there will be any credit card option...
					if (self.options.cc_data_flag) {
						var bookingForm = d.forms[self.options.booking_form_name],
							$ccData = JABB.Utils.getElementsByClass(self.options.cc_data_wrapper, bookingForm, null),
							$bankData = JABB.Utils.getElementsByClass(self.options.bank_data_wrapper, bookingForm, null),
							$related = JABB.Utils.getElementsByClass("ebcal-price-related", bookingForm, null),
							$value = this.options[this.selectedIndex].value;
						if ($value == 'creditcard') {
							// show the credit cards fields
							for (var i = 0, len = $ccData.length; i < len; i++) {
								$ccData[i].style.display = "";
							}
							for (var i = 0, len = $related.length; i < len; i++) {
								$related[i].style.display = "";
							}
							// for each field add a requered class name
							for (var i = 0, len = self.options.cc_data_names.length; i < len; i++) {
								JABB.Utils.addClass(d.forms[self.options.booking_form_name][self.options.cc_data_names[i]], 'ebcal-required');
							}
							// hide the bank fields
							for (var i = 0, len = $bankData.length; i < len; i++) {
								$bankData[i].style.display = "none";
							}
						} else if($value == 'bank'){
							// hide the credit cards fields
							for (var i = 0, len = $ccData.length; i < len; i++) {
								$ccData[i].style.display = "none";
							}
							
							// for each field remove the requered class name
							for (var i = 0, len = self.options.cc_data_names.length; i < len; i++) {
								JABB.Utils.removeClass(d.forms[self.options.booking_form_name][self.options.cc_data_names[i]], 'ebcal-required');
							}
							// show the bank fields
							for (var i = 0, len = $bankData.length; i < len; i++) {
								$bankData[i].style.display = "";
							}
						} else {
							// hide the credit cards fields
							for (var i = 0, len = $ccData.length; i < len; i++) {
								$ccData[i].style.display = "none";
							}
							
							// for each field remove the requered class name
							for (var i = 0, len = self.options.cc_data_names.length; i < len; i++) {
								JABB.Utils.removeClass(d.forms[self.options.booking_form_name][self.options.cc_data_names[i]], 'ebcal-required');
							}
							// hide the bank fields
							for (var i = 0, len = $bankData.length; i < len; i++) {
								$bankData[i].style.display = "none";
							}
						}
					}
				});
			}
			//Add "click" event to Cancel button
			if (d.forms[self.options.booking_form_name] && d.forms[self.options.booking_form_name][self.options.booking_form_cancel_name]) {
				JABB.Utils.addEvent(d.forms[self.options.booking_form_name][self.options.booking_form_cancel_name], "click", function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					
					var $this = this;
					//this.disabled = true;
					self.disableButtons();
					if(self.view_mode == 'monthly' || self.view_mode == 'list')
					{
						if(self.options.layout == 'layout_2')
						{
							var event_id = this.getAttribute('data-id'),
								post = JABB.Utils.serialize($this.form);
							self.event_detail.innerHTML = self.options.message_9;
							self.loadView(event_id, post);
						}else{
							self.event_detail.innerHTML = self.options.message_1;
							self.loadEvents();
						}
					}else if(self.view_mode == 'calendar'){
						self.event_detail.innerHTML = self.options.message_1;
						self.loadEventDetail();
					}	
				});
			}
			//Add "click" event to Submit button
			if (d.forms[self.options.booking_form_name] && d.forms[self.options.booking_form_name][self.options.booking_form_submit_name]) {
				JABB.Utils.addEvent(d.forms[self.options.booking_form_name][self.options.booking_form_submit_name], "click", function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					
					var $this = this;
					//$this.disabled = true;
					self.disableButtons();
					
					if (!self.validateBookingForm($this)) {
						return;
					}
					
					if ($this.form.captcha) {
						JABB.Ajax.sendRequest(self.options.check_captcha_url + "&captcha=" + $this.form.captcha.value, function (req) {
							var code = req.responseText;
							if(code == '100')
							{
								var post = JABB.Utils.serialize($this.form);
								self.event_detail.innerHTML = self.options.message_3;
								self.loadBookingSummary(post);
							}else if(code == '101'){
								self.errorHandler('\n' + self.options.validation.error_captcha);
								//$this.disabled = false;
								self.enableButtons();
							}
						});
					} else {
						var post = JABB.Utils.serialize($this.form);
						self.event_detail.innerHTML = self.options.message_3;
						self.loadBookingSummary(post);
					}			
				});
			}
			var priceSelect = JABB.Utils.getElementsByClass(self.options.class_name_price, d.forms[self.options.booking_form_name], "SELECT");
			if (d.forms[self.options.booking_form_name]) {
				for (var i = 0, len = priceSelect.length; i < len; i++) {
					JABB.Utils.addEvent(priceSelect[i], "change", function (event) {
						self.loadPrices();
					});
				}
			}
			
			var back_arr = JABB.Utils.getElementsByClass("ebcal-back", d.getElementById('phpevtcal_container_' + self.options.index), "A");
			if(back_arr.length > 0)
			{
				back_arr[0].onclick = function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					var event_id = this.getAttribute('rev'),
						post = JABB.Utils.serialize(d.forms[self.options.booking_form_name]);
					self.event_detail.innerHTML = self.options.message_9;
					self.loadView(event_id, post);
				};
			}
		},
		loadBookingForm: function(event_id, post){
			var self = this,
				qs = ["&layout=", self.options.layout, "&cate=", self.category, "&event_id=", event_id].join("");
			if (typeof event_id !== "undefined") {
				this.event_id = event_id;
			}
			JABB.Ajax.sendRequest(self.options.load_booking_form_url + qs, function (req) {
				self.event_detail.innerHTML = req.responseText;
				self.bindBookingForm();
				self.loadPrices();
				self.bindClose();
			}, post);
		},
		bindEventDetail: function(){
			var self = this;
			var arr = JABB.Utils.getElementsByClass("phpevtcal-detail-close", self.event_detail, "a");
			for (i = 0, len = arr.length; i < len; i++) {
				arr[i].onclick = function () {
					var event_id = this.getAttribute("rev");
					var num_events = self.num_events;
					num_events--;
					self.num_events = num_events;
					if(num_events == 0)
					{
						d.getElementById('phpevtcal_table_calendar_' + self.options.index).style.display = 'block';
					}
					d.getElementById('phpevtcal_event_box_'+self.options.index+'_' + event_id).style.display = 'none';
				};
			}
			var btn_arr = JABB.Utils.getElementsByClass("ebcal-buy-ticket", self.event_detail, "input");
			for (i = 0, len = btn_arr.length; i < len; i++) {
				btn_arr[i].onclick = function () {
					var event_id = this.getAttribute("lang");
					self.event_detail.innerHTML = self.options.message_2;
					self.loadBookingForm(event_id, null);
				};
			}
		},
		loadEventDetail: function(){
			var self = this;
			var qs = ["&layout=", self.options.layout, "&cate=", self.category, "&dt=", self.selected_date, "&show_cate=", self.options.enable_categories].join("");
			JABB.Ajax.sendRequest(self.options.load_event_detail_url + qs, function (req) {
				self.event_detail.innerHTML = req.responseText;
				if(self.options.display_events == 'replace')
				{
					d.getElementById('phpevtcal_table_calendar_' + self.options.index).style.display = 'none';
				}
				self.bindEventDetail();
			});
		},
		loadView: function(event_id, post){
			var self = this;
			var qs = ["&layout=", self.options.layout, "&cate=", self.category, "&id=", event_id, "&show_cate=", self.options.enable_categories].join("");
			JABB.Ajax.sendRequest(self.options.load_view_url + qs, function (req) {
				self.event_detail.innerHTML = req.responseText;
				
				self.bindView();
			}, post);
		},
		bindView: function(){
			var self = this;
			var back_arr = JABB.Utils.getElementsByClass("ebcal-back", d.getElementById('phpevtcal_container_' + self.options.index), "A");
			back_arr[0].onclick = function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.loadEvents();
			};
			
			var buy_arr = JABB.Utils.getElementsByClass("ebcal-buy-ticket", d.getElementById('phpevtcal_container_' + self.options.index), "INPUT");
			if(buy_arr.length > 0)
			{
				buy_arr[0].onclick = function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					
					var price_arr = JABB.Utils.getElementsByClass("ebcal-price", self.event_detail, "SELECT"),
						total_tickets = 0;
					for (i = 0, len = price_arr.length; i < len; i++) {
						total_tickets += parseInt(price_arr[i].value, 10);
					}
					if(total_tickets > 0)
					{
						self.disableButtons();
						
						var detail_form = d.forms[self.options.detail_form_name],
							post = JABB.Utils.serialize(detail_form),
						event_id = parseInt(this.getAttribute("data-id"),10);
						self.loadBookingForm(event_id, post);
					}else{
						var notes_arr = JABB.Utils.getElementsByClass("ebcal-buy-ticket-notes", d.getElementById('phpevtcal_container_' + self.options.index), "DIV");
						if(notes_arr.length > 0){
							notes_arr[0].style.display = 'block';
						}
					}
				};
			}
		},
		bindCalendarView: function(){
			var self = this;
			var arr = JABB.Utils.getElementsByClass("has-event", d.getElementById('phpevtcal_table_calendar_' + self.options.index), "td");
			for (i = 0, len = arr.length; i < len; i++) {
				if(self.options.event_title_position == 'tooltip')
				{
					arr[i].onmouseover = function () {
						var axis = this.getAttribute("axis");
						var tooltip = d.getElementById('phpevtcal_tooltip_' + self.options.index + '_' + axis);
						tooltip.style.visibility = 'visible';
					};
					arr[i].onmouseout = function () {
						var axis = this.getAttribute("axis");
						var tooltip = d.getElementById('phpevtcal_tooltip_' + self.options.index + '_' + axis);
						tooltip.style.visibility = 'hidden';
					};
				}
				arr[i].onclick = function () {
					var day = parseInt(this.getAttribute("axis"), 10);
					var num_events = parseInt(this.getAttribute("lang"), 10);
					var dt = null;
					if(day < 10){
						dt = self.year + '-' + self.month + '-0' + day;
					}else{
						dt = self.year + '-' + self.month + '-' + day;
					}
					self.selected_date = dt;
					self.num_events = parseInt(num_events);
					self.event_detail.innerHTML = self.options.message_1;
					self.loadEventDetail();
				};
			}
			
			var arr = JABB.Utils.getElementsByClass("month-nav", d.getElementById('phpevtcal_table_calendar_' + self.options.index), "a");
			for (i = 0, len = arr.length; i < len; i++) {
				arr[i].onclick = function () {
					var rev = this.getAttribute("rev"),
						rel = this.getAttribute("rel");
					self.month = rev;
					self.year = rel;
					self.loadEvents();
				}
			}
		},
		bindMenu: function()
		{
			var self = 	this;
			var arr = JABB.Utils.getElementsByClass("phpevtcal-view-mode", d.getElementById('phpevtcal_menu_' + self.options.index), "a");
			
			for (i = 0, len = arr.length; i < len; i++) {
				arr[i].onclick = function () {
					var rev = this.getAttribute("rev");
					self.month = self.current_month;
					self.year = self.current_year;
					self.page = 1;
					self.view_mode = rev;
					
					self.loadEvents();
				};
			}
		},
		bindListView: function()
		{
			var self = 	this;
			var arr = JABB.Utils.getElementsByClass("phpevtcal-paging", d.getElementById('phpevtcal_pagination_' + self.options.index), "a");
			for (i = 0, len = arr.length; i < len; i++) {
				arr[i].onclick = function () {
					var page = this.getAttribute("rev");
					self.page = page;
					self.loadEvents();
				};
			}
			
			var btn_arr = JABB.Utils.getElementsByClass("ebcal-buy-ticket", self.event_detail, "input");
			for (i = 0, len = btn_arr.length; i < len; i++) {
				btn_arr[i].onclick = function () {
					self.disableButtons();
					var event_id = this.getAttribute("lang");
					self.event_detail.innerHTML = self.options.message_2;
					self.loadBookingForm(event_id, null);
				};
			}
			
			var menu_arr = JABB.Utils.getElementsByClass("ebcal-menu-item", d.getElementById('phpevtcal_container_' + self.options.index), "A");
			if(menu_arr.length > 0)
			{
				for (i = 0, len = menu_arr.length; i < len; i++) {
					menu_arr[i].onclick = function (e) {
						if (e && e.preventDefault) {
							e.preventDefault();
						}
						
						var period = this.getAttribute("rev");
						self.period = period;
						self.page = 1;
						self.loadEvents()
					};
				}
			}
						
			var box_arr = JABB.Utils.getElementsByClass("ebcal-event-box", d.getElementById('phpevtcal_container_' + self.options.index), "DIV");
			for (i = 0, len = box_arr.length; i < len; i++) {
				box_arr[i].onmouseover = function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					
					var event_id = this.getAttribute("data-id"),
						overlay = d.getElementById('ebcal_overlay_'+self.options.index+'_' + event_id);
					overlay.style.display = 'block';
				};
				
				box_arr[i].onmouseout = function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					var event_id = this.getAttribute("data-id"),
						overlay = d.getElementById('ebcal_overlay_'+self.options.index+'_' + event_id);
					overlay.style.display = 'none';
				};
			}
			
			var btn_detail_arr = JABB.Utils.getElementsByClass("ebcal-view-details", d.getElementById('phpevtcal_container_' + self.options.index), "INPUT");
			for (i = 0, len = btn_detail_arr.length; i < len; i++) {
				btn_detail_arr[i].onclick = function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					var event_id = this.getAttribute("data-id");
					self.event_detail.innerHTML = self.options.message_9;
					self.loadView(event_id, null);
				};
			}
			
			var paging_arr = JABB.Utils.getElementsByClass("ebcal-page-clickable", d.getElementById('phpevtcal_container_' + self.options.index), "A");
			for (i = 0, len = paging_arr.length; i < len; i++) {
				paging_arr[i].onclick = function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					var page = this.getAttribute("rev");
					self.page = page;
					self.loadEvents();
				};
			}
		},
		loadEvents: function()
		{
			var self = 	this,
						qs = ["&layout=", this.options.layout, "&cate=", this.category, "&month=", this.month, "&year=", this.year, "&view_mode=", this.view_mode, "&period=", this.period, "&page=", this.page, "&show_cate=", this.options.enable_categories].join("");
			var event_id = parseInt(self.options.one_event, 10);
			if(event_id > 0)
			{
				qs += "&event_id=" + event_id;
			}
			JABB.Ajax.sendRequest(this.options.load_events_url + qs, function (req) {
				self.main_content.innerHTML = req.responseText;
				if(self.view_mode == 'monthly')
				{
					self.event_detail = d.getElementById("phpevtcal_content_" + self.options.index);
					self.bindMonthView();
				}else if(self.view_mode == 'calendar'){
					self.event_detail = d.getElementById("phpevtcal_event_detail_" + self.options.index);
					self.bindCalendarView();
				}else if(self.view_mode == 'list'){
					self.event_detail = d.getElementById("phpevtcal_content_" + self.options.index);
					self.bindListView();
				}
			});
		},
		
		bindClose: function () {
			var i, len, self = this,
			btnClose = JABB.Utils.getElementsByClass(this.options.class_close_form, d.getElementById(self.event_detail), "a");
			for (i = 0, len = btnClose.length; i < len; i++) {
				btnClose[i].onclick = function () {
					self.event_detail.innerHTML = self.options.message_1;
					if(self.view_mode == 'monthly' || self.view_mode == 'list')
					{
						self.loadEvents();
					}else if(self.view_mode == 'calendar'){
						self.loadEventDetail();
					}
					return false;
				};
			}
		},
		errorHandler: function (message) {
			var err = JABB.Utils.getElementsByClass("ebcal-error-container", d.forms[this.options.booking_form_name], "P");
			if (err[0]) {
				err[0].innerHTML = '<span></span>' + this.options.validation.error_title + message.replace(/\n/g, "<br />");
				err[0].style.display = '';
			} else {
				alert(this.options.validation.error_title + message);
			}
		},
		formatCurrency: function(price, currency)
		{
			var format = '---';
			switch (currency)
			{
				case 'USD':
					format = "$" + price.toFixed(2);
					break;
				case 'GBP':
					format = "&pound;" + price.toFixed(2);
					break;
				case 'EUR':
					format = "&euro;" + price.toFixed(2);
					break;
				case 'JPY':
					format = "&yen;" + price.toFixed(2);
					break;
				case 'AUD':
				case 'CAD':
				case 'NZD':
				case 'CHF':
				case 'HKD':
				case 'SGD':
				case 'SEK':
				case 'DKK':
				case 'PLN':
					format = price.toFixed(2) + currency;
					break;
				case 'NOK':
				case 'HUF':
				case 'CZK':
				case 'ILS':
				case 'MXN':
					format = currency + price.toFixed(2);
					break;
				default:
					format = price.toFixed(2) + currency;
					break;
			}
			return format;
		},
		init: function (calObj) {
			var self = this;
			var view_mode = calObj.default_view,
				month = calObj.current_month,
				year = calObj.current_year;
			self.main_content = d.getElementById("phpevtcal_content_" + calObj.index);
			self.options = calObj;
			self.month = month;
			self.year = year;
			self.current_month = month;
			self.current_year = year;
			self.view_mode = view_mode;
			self.category = calObj.category_id;
			self.page = 1;
			self.loadEvents();
			if(self.options.show_header == '1')
			{
				if(self.options.enable_categories == 'Yes')
				{
					self.bindCategory();
				}
				if(self.options.enable_monthly_view == 'Yes' || self.options.enable_list_view == 'Yes')
				{	
					self.bindMenu();
				}
			}
		}
	}
	return (window.PhpEvtCal = PhpEvtCal);
})(window);