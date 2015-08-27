/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    $('select.selectize').each(function() {
	    $(this).selectizeMe();
	});
});

$.fn.selectizeMe = function() {
	
	//Variables
	var $this = $(this);
	var name = $(this).attr('name');
    var url = $(this).attr('data-search-url') || false;
    var createUrl = $(this).attr('data-create-url') || false;
    var searchField = ($(this).attr('data-search-field') || 'name').split(',');
    var labelField = $(this).attr('data-label-field') || 'name';
    var labelFieldAppend = ($(this).attr('data-label-field-append') || '').split(',');
    var valueField = $(this).attr('data-value-field') || 'id';
    var allowCreate = $(this).attr('data-allow-create') || false;
    
    //Helpers
    var findNestedIndex = function(obj,i) {return obj[i]};
    
	var config = {
		valueField: valueField,
		labelField: labelField,
		searchField: searchField,
		options: [],
		
		plugins: {
			'remove_button': {},
			'public_button': {
    			ajaxUrl: createUrl
            }
        
		},
		
		//Custom rendered to append any labelFieldAppend values
		render: {
			option: function(item, escape) {
				var html = '<div data-value="'+item[valueField]+'" data-selectable="" class="option">';
				html += escape(item[labelField]);
				if (labelFieldAppend) {
					for(var i=0; i<labelFieldAppend.length; i++) {
						var fields = labelFieldAppend[i].split('||');
						for	(var ii=0; ii<fields.length; ii++) {
							var append = fields[ii].split('.').reduce(findNestedIndex, item);
							if (append != null && typeof(append) != 'undefined') {
								html += ' <small>' + escape(append) + '</small>';
								break;
							}
						}
					}
				}
				html += '</div>';
				return  html;
			}
		},
		load: function (query, callback) {
			
			//No ajax url or query provided
			if (!query.length || url == false) {
				return callback();
			}
			
			//Load up our query results
			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'json',
				data: { 'q':query },
				success: function(results) {
					for(var i=0;i<results.data.length;i++) {
						for (var k in searchField) {
							if (typeof results.data[i][searchField[k]] == 'undefined') {
								results.data[i][searchField[k]] = searchField[k].split('.').reduce(findNestedIndex, results.data[i]);
							}
						}
					}
					callback(results.data);
				}
			});
		},
		create: function(input, callback) {			
			if (createUrl) {
    			var token = $this.parents('form').find('[name="_token"]').val();
				$.ajax({
    				type: 'POST',
					url: createUrl,
					data: {
    				    'name':input,
    				    '_token':token
    				},
					success: function(data) {
                        return callback(data);
					},
				});
			}
		}
	};
	
	$(this).selectize(config);
}

Selectize.define('public_button', function(options) {
	if (this.settings.mode === 'single') return;
	
	options = $.extend({
		label     : '<i class="fa fa-eye"></i>',
		title     : 'Public',
		className : 'public',
		append    : true,
		ajaxUrl   : false
	}, options);
		
	if (options.ajaxUrl === false) return;

	var self = this;
	var html = {
    	public : '<a href="javascript:void(0)" class="' + options.className + '" tabindex="-1" title="' + options.title + '"><i class="fa fa-eye"></i></a>',
    	private : '<a href="javascript:void(0)" class="' + options.className + '" tabindex="-1" title="' + options.title + '"><i class="fa fa-eye-slash"></i></a>'
    };
	
	/**
	 * Appends an element as a child (with raw HTML).
	 *
	 * @param {string} html_container
	 * @param {string} html_element
	 * @return {string}
	 */
	var append = function(html_container, html_element) {
		var pos = html_container.search(/(<\/[^>]+>\s*)$/);
		return html_container.substring(0, pos) + html_element + html_container.substring(pos);
	};

	this.setup = (function() {
		var original = self.setup;
		
		return function() {
			// override the item rendering method to add the button to each
			if (options.append) {
				var render_item = self.settings.render.item;
				self.settings.render.item = function(data) {
    				var _html = data.public == 1 ? html.public : html.private;
					return append(render_item.apply(this, arguments), _html);
				};
			}
			
			original.apply(this, arguments);	
			
			// add event listener
			this.$control.on('click', '.' + options.className, function(e) {
				$('i', e.currentTarget).toggleClass('fa-eye').toggleClass('fa-eye-slash');
				var _public = $('i', e.currentTarget).hasClass('fa-eye') ? 1 : 0;
				$(e.currentTarget).parent().removeClass('active');
				$item = $(e.currentTarget).parent();
				self.setActiveItem(false);
				
				var token = $item.parents('form').find('[name="_token"]').val();
				var ajaxUrl = options.ajaxUrl + '/' + $item.attr('data-value');
				$.ajax({
    				type: 'PUT',
					url: ajaxUrl,
					data: {
    				    'public': _public,
    				    '_token':token,
    				    '_method': 'PUT'
    				},
					success: function(data) {
                        console.log(data);
					},
				});
				/*if (self.isLocked) return;

				var $item = $(e.currentTarget).parent();
				self.setActiveItem($item);
				if (self.deleteSelection()) {
					self.setCaret(self.items.length);
				}*/
			});

		};
	})();

});