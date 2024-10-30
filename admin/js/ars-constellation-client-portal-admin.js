(function( $ ) {	
	
	$(document).ready(function(){		

		accp_admin_make_company_select_field_required();
		
		accp_initiate_core_select2_fields();
		
		accp_delete_file_attachment_on_post_permanent_delete();
		
		accp_delete_file_attachment_on_bulk_post_permanent_delete();
				
		accp_delete_file_attachment_on_post_empty_trash();		

		accp_admin_toggle_file_reassign_form();

		accp_admin_toggle_file_replace_form();
		
		accp_reassign_post_company_on_click();

		accp_create_company_directory_on_click();
		
		accp_assign_company_directory_on_click();

		accp_generate_password_on_click();

		accp_toggle_create_new_primary_user_section();

		accp_assign_existing_primary_user_on_click();

		accp_create_and_assign_new_primary_user_on_click();

		accp_reassign_company_directory_on_click();
				
		accp_toggle_create_new_company_home_page_form();		

		accp_generate_new_company_home_page_on_click();
		
		accp_dismiss_duplicate_dir_assignment_message_on_click();
		
		accp_save_bulk_edit_post_data();

	}); // End document ready.


	function accp_save_invoice_bulk_edit(e){

		if( $('#invoice-status-bulk-edit-select').length ){
			
			/**
			 * Disable pointer events instead of setting
			 * a "disabled" property to allow native WP
			 * post data to be saved normally.
			 */
			accp_disable_pointer_events_by_event_target(e);
			accp_add_and_show_status_spinner_after_bulk_add_buttons(e);
				
			var post_ids = new Array();
			var invoice_status = $('#invoice-status-bulk-edit-select').val();
			var nonce = $('#accp-invoice-bulk-edit-section').attr('data-nonce');

			$( 'tr#bulk-edit ul#bulk-titles-list li button' ).each( function() {
				post_ids.push( $( this ).attr( 'id' ).replace( /^(_)/i, '' ) );
			});		
			
			if(invoice_status != "-1"){
			
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					async: false,						
					data: {
						action: 'accp_save_invoice_bulk_edit',
						post_ids: post_ids,
						invoice_status: invoice_status,
						nonce: nonce
					},
					success: function(data){				
									
						
					},
					error: function(jqXHR, textStatus, errorThrown){

						accp_reset_pointer_events_by_selector('#bulk-edit');
						$('.accp-spinner-after-bulk-edit').remove();

						console.log(textStatus, errorThrown);
						console.log(jqXHR);
					},

				});

			}

		}

	}
	

	function accp_save_file_bulk_edit(e){

		if( $('#file-status-bulk-edit-select').length ){

			/**
			 * Disable pointer events instead of setting
			 * a "disabled" property to allow native WP
			 * post data to be saved normally.
			 */
			accp_disable_pointer_events_by_event_target(e);
			accp_add_and_show_status_spinner_after_bulk_add_buttons(e);
								
			var post_ids = new Array();
			var file_status = $('#file-status-bulk-edit-select').val();
			var nonce = $('#accp-file-bulk-edit-section').attr('data-nonce');

			$( 'tr#bulk-edit ul#bulk-titles-list li button' ).each( function() {
				post_ids.push( $( this ).attr( 'id' ).replace( /^(_)/i, '' ) );
			});					
			
			if(file_status != "-1"){
			
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					async: false,						
					data: {
						action: 'accp_save_file_bulk_edit',
						post_ids: post_ids,
						file_status: file_status,
						nonce: nonce
					},
					success: function(data){													
						

					},
					error: function(jqXHR, textStatus, errorThrown){

						accp_reset_pointer_events_by_selector('#bulk-edit');
						$('.accp-spinner-after-bulk-edit').remove();						

						console.log(textStatus, errorThrown);
						console.log(jqXHR);
					},

				});

			}

		}

	}


	function accp_save_global_file_bulk_edit(e){

		if( $('#global-file-status-bulk-edit-select').length ){

			/**
			 * Disable pointer events instead of setting
			 * a "disabled" property to allow native WP
			 * post data to be saved normally.
			 */
			accp_disable_pointer_events_by_event_target(e);
			accp_add_and_show_status_spinner_after_bulk_add_buttons(e);				
								
			var post_ids = new Array();
			var file_status = $('#global-file-status-bulk-edit-select').val();
			var nonce = $('#accp-file-bulk-edit-section').attr('data-nonce');

			$( 'tr#bulk-edit ul#bulk-titles-list li button' ).each( function() {
				post_ids.push( $( this ).attr( 'id' ).replace( /^(_)/i, '' ) );
			});			
			
			if(file_status != "-1"){
			
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					async: false,						
					data: {
						action: 'accp_save_global_file_bulk_edit',
						post_ids: post_ids,
						file_status: file_status,
						nonce: nonce
					},
					success: function(data){

						// DEV
						console.log(data);

					},
					error: function(jqXHR, textStatus, errorThrown){

						accp_reset_pointer_events_by_selector('#bulk-edit');
						$('.accp-spinner-after-bulk-edit').remove();

						console.log(textStatus, errorThrown);
						console.log(jqXHR);
					},

				});

			}

		}

	}
	

	function accp_admin_make_company_select_field_required(){
		
		if( $('.company-assign-container select#company-select').length > -1 ){
			$('select#company-select').attr('required', true);
		}

	}


	function accp_admin_toggle_file_reassign_form(){

		$('#reassign-toggle').click(function () {
				
			if ($('#reassign-form-container').is(":hidden")){

				$('#reassign-form-container').show('slow');
				$('.accp-hide-assigment').show();
				$('#reassign-toggle').text('Cancel');				

			}else{

				$('#reassign-form-container').hide();
				$('.accp-hide-assigment').hide();
				$('#reassign-toggle').text('Reassign');

			}

		});

	}

	
	function accp_admin_toggle_file_replace_form(){
	
		$('#replace-file-toggle').click(function () {
			
			if ($('.accp-file-replace-hide').is(":hidden")){
			
				$('.accp-file-replace-hide').show('slow');			
				$('#replace-file-toggle').text('Cancel');

			}else{			
				
				$('.accp-file-replace-hide').hide();							
				$('#replace-file-toggle').text('Replace File');

			}

		});

	}


	function accp_add_and_show_status_spinner(e, additional_classes = ''){

		var target_elem = e.target;
		var unique_class = 'accp-' + Math.random().toString(36).slice(2) + '-spinner';
		var spinner_elem = '<span class="'+unique_class+' accp-spinner spinner ' + additional_classes + '" style="float: none;"></span>';
		var new_spinner_class = '.' + unique_class + '.accp-spinner.spinner';

		$(spinner_elem).insertAfter(target_elem);
		$(new_spinner_class).css('visibility', 'visible');
		
		return new_spinner_class;

	}


	function accp_add_and_show_status_spinner_after_bulk_add_buttons(e){

		var target_elem = e.target.closest('.submit.inline-edit-save');
		var unique_class = 'accp-' + Math.random().toString(36).slice(2) + '-spinner';
		var spinner_elem = '<span class="'+unique_class+' accp-spinner spinner accp-spinner-after-bulk-edit" style="float: none;"></span>';
		var new_spinner_class = '.' + unique_class + '.accp-spinner.spinner';

		$(spinner_elem).appendTo(target_elem);
		$(new_spinner_class).css('visibility', 'visible');
		
		return new_spinner_class;

	}


	function accp_disable_element_by_event_target(e){

		var target_elem = e.target;

		$(target_elem).prop('disabled', true);

	}


	function accp_disable_pointer_events_by_event_target(e){

		var target_elem = e.target;

		$(target_elem).attr('style', 'pointer-events: none;');

	}

	function accp_reset_pointer_events_by_selector(selector){

		$(selector).attr('style', 'pointer-events: all;');

	}


	function accp_remove_disabled_prop_from_element(selector){

		$(selector).prop('disabled', false);		

	}


	function accp_initiate_core_select2_fields(){

		$('.client-add-company-select').select2({
			placeholder: 'Select a company...',
			multiple: true,
			allowClear: true,
			tags: true,
			tokenSeparators: [',', ' ']	   
		}); 

		$('#company-select').select2({
			placeholder: 'Select a company...',
			multiple: false,
			allowClear: true,
			tags: true,
			tokenSeparators: [',', ' ']	   
		});

		if($('select.accp_admin_list_filter').length){
			
			$('select.accp_admin_list_filter').select2({
				multiple: false							
			});

		}

	}


	function accp_delete_file_attachment_on_post_permanent_delete(){
		
		$('table a.submitdelete').click(function (e) {
			
			if( window.location.href.indexOf("post_status=trash&post_type=accp_clientfile") > -1 ||
				window.location.href.indexOf("post_status=trash&post_type=accp_clientinvoice") > -1 ||
				window.location.href.indexOf("post_status=trash&post_type=accp_global_file") > -1
			){			
				
				var file_del_nonce = $('#clientfile-admin-nonce').attr('data-nonce');	
				var file_post_id = $(this).closest('tr').find('td.doc_id').text();				

				e.preventDefault();

				$.ajax({
					type: 'POST',				
					url: ajaxurl,
					cache: false,
					headers: {
						'cache-control': 'no-cache',					
					},				
					data: { 
						action:'accp_delete_file_on_post_delete',							
						file_del_nonce: file_del_nonce,
						file_post_id: file_post_id,																		
					},
					success: function(data){
						
						window.location.reload(true);

					},
					error: function(jqXHR, textStatus, errorThrown){
						console.log(textStatus, errorThrown);
						console.log(jqXHR);
					},			
									
				});				

			}

		});

	}


	function accp_delete_file_attachment_on_bulk_post_permanent_delete(){
		
		$('#doaction').click(function (e) {		
			
			if ( 
				(
				window.location.href.indexOf("post_status=trash&post_type=accp_clientfile") > -1 ||
				window.location.href.indexOf("post_status=trash&post_type=accp_clientinvoice") > -1 ||
				window.location.href.indexOf("post_status=trash&post_type=accp_global_file") > -1
				) &&
				$('#bulk-action-selector-top').val() == 'delete'
			){	

				e.preventDefault();				
				
				var del_file_post_id_array = $('tr').find('th.check-column input[type="checkbox"]:checked').map(function() {

					var file_ids = $(this).closest('tr').find('td.doc_id').text();

				  return file_ids;
				  
				}).get();
				
				var del_file_post_id_json = JSON.stringify(del_file_post_id_array);	
				var bulk_delete_nonce = $('#clientfile-admin-nonce').attr('data-nonce');									

				$.ajax({
					type: 'POST',				
					url: ajaxurl,
					cache: false,
					headers: {
						'cache-control': 'no-cache',					
					},				
					data: { 
						action:'accp_bulk_delete_file_on_post_delete',
						bulk_delete_nonce: bulk_delete_nonce,							
						del_file_post_id_json: del_file_post_id_json,

					},
					success: function(data){					
						
						window.location.reload(true);

					},
					error: function(jqXHR, textStatus, errorThrown){
						console.log(textStatus, errorThrown);
						console.log(jqXHR);
					},			
									
				});				

			}
		});

	}


	function accp_delete_file_attachment_on_post_empty_trash(){
		
		 $('#delete_all').click(function (e) {		
			
			if (window.location.href.indexOf("post_status=trash&post_type=accp_clientfile") > -1 || window.location.href.indexOf("post_status=trash&post_type=accp_clientinvoice") > -1 || window.location.href.indexOf("post_status=trash&post_type=accp_global_file") > -1 ) {	

				e.preventDefault();

				var empty_trash_nonce = $('#clientfile-admin-nonce').attr('data-nonce');

				if(window.location.href.indexOf("post_status=trash&post_type=accp_clientfile") > -1){
					var post_type = 'accp_clientfile';
				}

				if(window.location.href.indexOf("post_status=trash&post_type=accp_global_file") > -1){
					var post_type = 'accp_global_file';
				}

				if(window.location.href.indexOf("post_status=trash&post_type=accp_clientinvoice") > -1){
					var post_type = 'accp_clientinvoice';
				}

				if(window.location.href.indexOf("post_status=trash&post_type=accp_clientcompany") > -1){
					var post_type = 'accp_clientcompany';
				}
				
				$.ajax({
					type: 'POST',				
					url: ajaxurl,
					cache: false,
					headers: {
						'cache-control': 'no-cache',					
					},				
					data: { 
						action:'accp_bulk_delete_file_on_empty_trash',
						empty_trash_nonce: empty_trash_nonce,
						post_type: post_type
					},
					success: function(data){
					
						window.location.reload(true);

					},
					error: function(jqXHR, textStatus, errorThrown){
						console.log(textStatus, errorThrown);
						console.log(jqXHR);
					},			
									
				});

			}

		});

	}


	function accp_reassign_post_company_on_click(){

		$('#company-select').on('change', function(){

			if( $('.accp-reassign-company-notice').length ){

				$('.accp-reassign-company-notice').remove();

			}

		});

		$('#accp_reassign_btn').click(function (e) {

			e.preventDefault();					

			var post_id = $('#curr_file_container').attr('data-post-id');
			var selected_company = $('select#company-select').children('option:selected').val();
			var reassign_nonce = $('#accp_reassign_btn').attr('data-nonce');
			var current_company_id = $('#accp_reassign_btn').attr('data-current-company-id');
			var button_container = $(this).closest('p');
			
			if( $('.accp-reassign-company-notice').length ){

				$('.accp-reassign-company-notice').remove();

			}
			
			if( selected_company === current_company_id ){

				var error_message = '<p class="accp-reassign-company-notice">The current company cannot be the same as the new company to reassign the post to.</p>';

				$(error_message).insertBefore(button_container);

				return;

			}


			accp_disable_element_by_event_target(e);
			accp_add_and_show_status_spinner(e, 'accp-reassign-company-button-spinner');
			
			if ($('input[name="accp_leave_prev_file"]').is(':checked')){
				var leave_copy = $('input[name="accp_leave_prev_file"]').val();
			}else{
				var leave_copy = 0;
			}

			$.ajax({
				type: 'POST',				
				url: ajaxurl,
				cache: false,
				headers: {
					'cache-control': 'no-cache',					
				},				
				data: { 
					action:'accp_reassign_file_1',
					post_id: post_id,
					selected_company: selected_company,
					leave_copy: leave_copy,
					reassign_nonce: reassign_nonce													
				},
				success: function(data){
					console.log(data);
					
					$('.accp-hide-assigment').hide();
					$('#reassign-toggle').text('Reassign');
					
					
					window.location.reload();

				},
				error: function(jqXHR, textStatus, errorThrown){
					console.log(textStatus, errorThrown);
					console.log(jqXHR);

					$('.accp-spinner').remove();
					accp_remove_disabled_prop_from_element('#accp_reassign_btn');
				},			
								
			});		

		});

	}


	function accp_create_company_directory_on_click(){

		$('#accp-generate-dir-btn').click(function (e) {
			
			e.preventDefault();			

			var post_id = $('#accp-generate-dir-btn').attr('data-post-id');
			var generate_nonce = $('#accp-generate-dir-btn').attr('data-nonce');
			var spinner_elem = '<span class="accp-dir-assign-spinner spinner" style="float: none;"></span>';
			var move_files = '';			
			var update_links = '';

			if( $('#new-dir-move-files').length ){

				if( $('#new-dir-move-files').is(':checked') ){

					var move_files = $('#new-dir-move-files').val();

				}

			}			

			if( $('#new-dir-update-links').length ){

				if( $('#new-dir-update-links').is(':checked') ){

					var update_links = $('#new-dir-update-links').val();

				}

			}			
			
			$(spinner_elem).insertAfter(this);
			$('.accp-dir-assign-spinner.spinner').css('visibility', 'visible');
			$(this).css('pointer-events', 'none');			

			$.ajax({
				type: 'POST',				
				url: ajaxurl,
				cache: false,
				headers: {
					'cache-control': 'no-cache',					
				},				
				data: { 
					action:'accp_generate_company_dir',
					post_id: post_id,
					move_files: move_files,					
					update_links: update_links,
					generate_nonce: generate_nonce																		
				},
				success: function(data){				
					
					$('#upload-directory').remove();
					$('.accp-new-dir-checkbox').prop('checked', false);
					$('.accp-generate-or-assign-dir-container').hide();
					$('.accp-reassign-btn-initial').toggle();
					$('.accp-reassign-btn-cancel').toggle();
					$('#accp-generate-dir-btn').css('pointer-events', 'all');
					$('.accp-dir-assign-spinner.spinner').remove();						
					$('<div id="upload-directory">' + data + '</div>').insertAfter('.accp-company-directory-label');
					
				},
				error: function(jqXHR, textStatus, errorThrown){
					console.log(textStatus, errorThrown);
					console.log(jqXHR);
				},			
								
			});		

		});

	}

	function accp_assign_company_directory_on_click(){

		$('#accp-assign-dir-btn').click(function (e) {
			
			e.preventDefault();
			
			if($('#accp-specify-dir-name').val()){

				var post_id = $('#accp-generate-dir-btn').attr('data-post-id');
				var generate_nonce = $('#accp-generate-dir-btn').attr('data-nonce');
				var dir_name = $('#accp-specify-dir-name').val();

				if( 'global-files' === dir_name.toLowerCase().replace(/\s/g, '') ||
					'globalfiles' === dir_name.toLowerCase().replace(/\s/g, '') ||
					'global_files' === dir_name.toLowerCase().replace(/\s/g, '') ||
					'global.files' === dir_name.toLowerCase().replace(/\s/g, '')
				 ){

					alert('The '+dir_name.toLowerCase().replace(/\s/g, '')+' directory name is reserved and cannot be used.  Please enter a different name.');

					return;

				}

				var spinner_elem = '<span class="accp-dir-assign-spinner spinner" style="float: none;"></span>';
				var move_files = '';
				var overwrite_duplicates = '';
				var update_links = '';

				if( $('#specify-dir-move-files').length ){

					if( $('#specify-dir-move-files').is(':checked') ){

						var move_files = $('#specify-dir-move-files').val();

					}

				}

				if( $('#specify-dir-overwrite-duplicate-files').length ){

					if( $('#specify-dir-overwrite-duplicate-files').is(':checked') ){

						var overwrite_duplicates = $('#specify-dir-overwrite-duplicate-files').val();

					}

				}

				if( $('#specify-dir-update-links').length ){

					if( $('#specify-dir-update-links').is(':checked') ){

						var update_links = $('#specify-dir-update-links').val();

					}

				}
				
				$(spinner_elem).insertAfter(this);
				$('.accp-dir-assign-spinner.spinner').css('visibility', 'visible');
				$(this).css('pointer-events', 'none');			

				$.ajax({
					type: 'POST',				
					url: ajaxurl,
					cache: false,
					headers: {
						'cache-control': 'no-cache',					
					},				
					data: { 
						action:'accp_specify_company_dir',
						post_id: post_id,
						dir_name: dir_name,
						move_files: move_files,
						overwrite_duplicates: overwrite_duplicates,
						update_links: update_links,
						generate_nonce: generate_nonce																		
					},
					success: function(data){	
						
						console.log(data);
						
						$('#upload-directory').remove();
						$('.accp-generate-or-assign-dir-container').hide();
						$('.accp-reassign-btn-initial').toggle();
						$('.accp-reassign-btn-cancel').toggle();
						$('.accp-specify-dir-checkbox').prop('checked', false);
						$('#accp-assign-dir-btn').css('pointer-events', 'all');
						$('#accp-specify-dir-name').val('');
						$('.accp-dir-assign-spinner.spinner').remove();						
						$('<div id="upload-directory">' + data + '</div>').insertAfter('.accp-company-directory-label');
						
					},
					error: function(jqXHR, textStatus, errorThrown){
						console.log(textStatus, errorThrown);
						console.log(jqXHR);
					},			
									
				});
			
			}

		});

	}

	function accp_generate_password_on_click(){

		$('#accp-autogenerate-password').click(function (e) {
			
			e.preventDefault();		
			
			var nonce = $(this).attr('data-nonce');			
			var spinner_elem = '<span class="accp-generate-pw-spinner spinner" style="float: none;"></span>';			
			
			$(spinner_elem).insertAfter(this);
			$('.accp-generate-pw-spinner.spinner').css('visibility', 'visible');
			$(this).css('pointer-events', 'none');			

			$.ajax({
				type: 'POST',				
				url: ajaxurl,
				cache: false,
				headers: {
					'cache-control': 'no-cache',					
				},				
				data: { 
					action:'accp_generate_user_password',
					nonce: nonce																							
				},
				success: function(data){				
					
					$('#accp-new-user-password').val(data);
					$('#accp-autogenerate-password').css('pointer-events', 'all');
					$('.accp-generate-pw-spinner.spinner').remove();					
					
				},
				error: function(jqXHR, textStatus, errorThrown){
					console.log(textStatus, errorThrown);
					console.log(jqXHR);
				},			
								
			});		
			
		});

	}


	function accp_toggle_create_new_primary_user_section(){		

		$('.accp-create-new-primary-user-btn').click(function (e) {

			e.preventDefault();

			$('.accp-create-user-button-text').toggle();
			$('.accp-create-user-cancel-button-text').toggle();
			$('#accp-create-user-container').toggle('slow');

		});

	}


	function accp_assign_existing_primary_user_on_click() {

		$( '#accp-assign-existing-primary-user-btn' ).click( function (e) {
			
			e.preventDefault();
			
			if( $( '#accp_assign_new_primary_user_select' ).val() && $( '#accp_assign_new_primary_user_select' ).val() != 0 ) {
			
				var nonce = $(this).attr('data-nonce');
				var company_id = $(this).attr('data-post-id');
				var user_id = $('#accp_assign_new_primary_user_select').val();
				var spinner_elem = '<span class="accp-assign-prim-user-spinner spinner" style="float: none;"></span>';			
				
				$(spinner_elem).insertAfter(this);
				$('.accp-assign-prim-user-spinner.spinner').css('visibility', 'visible');
				$(this).css('pointer-events', 'none');			

				$.ajax({
					type: 'POST',				
					url: ajaxurl,
					cache: false,
					headers: {
						'cache-control': 'no-cache',					
					},				
					data: { 
						action:'accp_assign_existing_primary_user',
						company_id: company_id,
						user_id: user_id,
						nonce: nonce																							
					},
					success: function(data){				
						
						var response_obj = JSON.parse(data);

						$('.accp-primary-user-section-message').html('Primary User: ' + response_obj.username);
						$('input[name="accp_company_primary_user"').val(response_obj.user_id);					
						$('#accp-assign-primary-user-container').hide();
						$('.accp-no-users-assigned-message').remove();
						$('#accp-assign-existing-primary-user-btn').css('pointer-events', 'all');
						$('.accp-assign-prim-user-spinner.spinner').remove();					
						
					},
					error: function(jqXHR, textStatus, errorThrown){

						console.log(textStatus, errorThrown);
						console.log(jqXHR);

						$('#accp-assign-existing-primary-user-btn').css('pointer-events', 'all');
						$('.accp-assign-prim-user-spinner.spinner').remove();

					},			
									
				});
			
			}
			
		});

	}


	function accp_create_and_assign_new_primary_user_on_click(){

		$('#accp-generate-new-user-btn').click(function (e) {			
			
			e.preventDefault();

			$('.accp-generate-user-message').text('');
			
			if( $('#accp-new-user-username').val() && $('#accp-new-user-email').val() && $('#accp-new-user-password').val() ){
			
				var nonce = $(this).attr('data-nonce');			
				var spinner_elem = '<span class="accp-create-user-spinner spinner" style="float: none;"></span>';
				var username = $('#accp-new-user-username').val();
				var role = $('#accp-new-user-role').val();
				var email = $('#accp-new-user-email').val();
				var password = $('#accp-new-user-password').val();
				var firstname = $('#accp-new-user-firstname').val();
				var lastname = $('#accp-new-user-lastname').val();
				var send_email = '';
				var company_id = $(this).attr('data-post-id');

				if($('#accp-send-user-notification').is(':checked')){
					var send_email = 'send';
				}
				
				$(spinner_elem).insertAfter(this);
				$('.accp-create-user-spinner.spinner').css('visibility', 'visible');
				$(this).css('pointer-events', 'none');			

				$.ajax({
					type: 'POST',				
					url: ajaxurl,
					cache: false,
					headers: {
						'cache-control': 'no-cache',					
					},				
					data: { 
						action:'accp_create_and_assign_primary_user',
						nonce: nonce,
						username: username,
						role: role,
						email: email,
						password: password,
						firstname: firstname,
						lastname: lastname,
						send_email: send_email,
						company_id: company_id

					},
					success: function(data){					

						try {

							var response_obj = JSON.parse(data);

							if (response_obj && typeof response_obj === 'object') {									

								$('.accp-primary-user-section-message').html('Primary User: ' + response_obj.username);
								$('input[name="accp_company_primary_user"').val(response_obj.user_id); // Save to hidden field for Gutenberg.
								$('.accp-create-user-text-field').val('');
								$('#accp-send-user-notification').prop('checked', false);
								$('#accp-assign-primary-user-container').hide();
								$('.accp-no-users-assigned-message').remove();

							}

						  } catch (e) {

							$('.accp-generate-user-message').text(data);

						  }					
						
						$('#accp-generate-new-user-btn').css('pointer-events', 'all');
						$('.accp-create-user-spinner.spinner').remove();						
						
					},
					error: function(jqXHR, textStatus, errorThrown){
						
						console.log(textStatus, errorThrown);
						console.log(jqXHR);

						$('#accp-generate-new-user-btn').css('pointer-events', 'all');
						$('.accp-create-user-spinner.spinner').remove();

					},			
									
				});
			
			}else{

				$('.accp-generate-user-message').text('Please fill out all required(*) fields.');

			}
			
		});

	}


	function accp_reassign_company_directory_on_click(){

		$('.accp-reassign-directory-button.button').click(function(){

			$('.accp-reassign-btn-initial').toggle();
			$('.accp-reassign-btn-cancel').toggle();
			$('.accp-directory-assigned').toggle('slow');

		});

	}


	function accp_toggle_create_new_company_home_page_form(){

		$('.accp-show-new-page-form').click(function () {			

			$(this).toggleClass('accp-open');

			if( $('.accp-show-new-page-form.accp-open').length > 0 ){
				
				$(this).text('Cancel');

			}else{

				$(this).text('Create New Page');
				
			}

			$('.accp-generate-page-form').toggle('slow');

		});

	}


	function accp_generate_new_company_home_page_on_click(){

		$('.accp-generate-new-page').click(function (e) {
			
			e.preventDefault();		
			
			accp_add_and_show_status_spinner(e);
			
			$(this).css('pointer-events', 'none');
			$(this).prop('disabled', true);
			$(this).attr('disabled', true);

			var nonce = $(this).attr('data-nonce');
			var post_title = $('.accp-new-page-title').val();
			var company_post_id = $(this).attr('data-post-id');
			var is_global = 'false';
			
			if( $('#accp-make-page-global-input').length ){

				if( $('#accp-make-page-global-input').is(':checked') ){

					var is_global = 'true';

				}

			}

			$.ajax({
				type: 'POST',				
				url: ajaxurl,
				cache: false,								
				data: { 
					action:'accp_generate_new_client_page',
					post_title: post_title,
					company_post_id: company_post_id,
					is_global: is_global,
					nonce: nonce													
				},
				success: function(data){
					
					if(data != 'accp add post error'){

						var message = 'Page ID ' + data + ' was successfully added.';
						var new_option = '<option value="' + data + '" selected="selected">' + post_title + '</option>';						

						$('.accp-generate-page-message').text(message);
						$('#accp_home_page option').prop('selected', false);
						$(new_option).appendTo('#accp_home_page');
						$('#accp_home_page').val(data);
						$('.accp-new-page-title').val('');
						$('.accp-generate-page-form').toggle('slow');
						$('.accp-generate-new-page').text('Create New Page');
						$('.accp-spinner.spinner').remove();
						$('.accp-generate-new-page').css('pointer-events', 'all');
						$('.accp-generate-new-page').prop('disabled', false);
						$('.accp-generate-new-page').attr('disabled', false);
						$('#accp-make-page-global-input').prop('checked', false);
						$('#accp-make-page-global-input').attr('checked', false);

					}else{

						var message = 'There was a problem adding the page, please refresh the page and try again.';

						$('.accp-generate-page-message').text(message);
						$('.accp-spinner.spinner').remove();
						$('.accp-generate-new-page').css('pointer-events', 'all');
						$('.accp-generate-new-page').prop('disabled', false);
						$('.accp-generate-new-page').attr('disabled', false);
						$('#accp-make-page-global-input').prop('checked', false);
						$('#accp-make-page-global-input').attr('checked', false);

					}					
					
				},
				error: function(jqXHR, textStatus, errorThrown){

					$('.accp-spinner.spinner').remove();
					$('.accp-generate-new-page').css('pointer-events', 'all');
					$('.accp-generate-new-page').prop('disabled', false);
					$('.accp-generate-new-page').attr('disabled', false);
					$('#accp-make-page-global-input').prop('checked', false);
					$('#accp-make-page-global-input').attr('checked', false);

					console.log(textStatus, errorThrown);
					console.log(jqXHR);
					
				},			
								
			});		

		});

	}


	function accp_dismiss_duplicate_dir_assignment_message_on_click(){

		$('.accp-dismiss-dir-assigment-msg.button').click(function (e) {
			
			e.preventDefault();			

			var nonce = $(this).attr('data-nonce');			
			var post_id = $(this).attr('data-post-id');
			var spinner_elem = '<span class="accp-dismiss-notice-spinner spinner" style="float: none;"></span>';
			
			$(spinner_elem).insertAfter(this);
			$('.accp-dismiss-notice-spinner.spinner').css('visibility', 'visible');
			$(this).css('pointer-events', 'none');

			$.ajax({
				type: 'POST',				
				url: ajaxurl,
				cache: false,								
				data: { 
					action:'accp_dismiss_duplicate_dir_assignment_notice',					
					post_id: post_id,
					nonce: nonce													
				},
				success: function(data){					
					
					$('.accp-dismiss-notice-spinner.spinner').remove();
					$('div.accp-duplicate-dir-assignment-notice').remove();
					
				},
				error: function(jqXHR, textStatus, errorThrown){
					console.log(textStatus, errorThrown);
					console.log(jqXHR);
				},			
								
			});		

		});

	}


	function accp_save_bulk_edit_post_data(){

		$('#bulk_edit').on( 'click', function(e) {		

			accp_save_invoice_bulk_edit(e);

			accp_save_file_bulk_edit(e);

			accp_save_global_file_bulk_edit(e);
						
		});

	}

})( jQuery );
