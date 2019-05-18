(function() {
    tinymce.PluginManager.add('contact', function( editor, url ) {

        var sh_tag = 'Staff_Directory_WD';

        function getAttr(s, n) {
            n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
            return n ?  window.decodeURIComponent(n[1]) : '';
        };
        function html( cls, data) {
            var placeholder = sc_plugin_url + '/images/Staff_Directory_WD_icon.png';
            data = window.encodeURIComponent( data );
            return '<img src="' + placeholder + '" class="mceItem ' + cls + '" ' + 'data-sh-cont-attr="' + data + '" data-mce-resize="false" data-mce-placeholder="1" />';
        }
        function replaceContShortcodes( content ) {
            //match [spider_faq(attr)]
            return content.replace( /\[Staff_Directory_WD([^\]]*)\]/g, function( all,attr) {
                return html( 'wp-Staff_Directory_WD', attr);
            });
        }
        function restoreContShortcodes( content ) {
            //match any image tag with our class and replace it with the shortcode's content and attributes
            return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function( match, image ) {
                var data = getAttr( image, 'data-sh-cont-attr' );
                if ( data ) {
                    return '<p>[' + sh_tag +' '+ data + ']</p>';
                }
                return match;
            });
        }

		//add popup
		editor.addCommand('cont_popup', function(ui, v, e) {
			//setup defaults
			//open the popup
				var tab = 1;
				if(v.tab)
					tab = v.tab;
				var contact = '';
				if (v.contact)
					contact = v.contact;
				var cats = new Array();
				if (v.cats){
					cats = v.cats.split(',');
				}
				var order = '';
				if (v.order)
					order = v.order;
				var types = 'full';
				if (v.types)
					types = v.types;
				var single_cont = new Array();
				var cont_cats = new Array();
				single_cont[0] = {
					type: 'listbox',
					name: 'contacts',
					label: 'Select Contact',
					value: contact,
					values: contacts
				};
			   
				cont_cats[0] = {
					type:'form',
					label: 'Select Category',
					items: [
					]
				};
				for(var i=0; i<contCats.length;i++){
					var checked = false;
					if(inArray(contCats[i].value,cats))
						checked = true;
					var item = {type:'checkbox',checked:checked, text: contCats[i].text, name:'contCats-'+contCats[i].value};
					cont_cats[0].items.push(item);
				}
				
				cont_cats[2] = {
					type: 'listbox',
					name:'type',
					label: 'View Type',
					value: types,
					onselect: function(e) {

					},
					onclick:function(e){

					},
					values: [
						{text: 'Short', value: 'short',classes:'noSelect'},
						{text: 'Full', value: 'full'},
						{text: 'Chess', value: 'chess',classes:'noSelect'},
						{text: 'Portfolio', value: 'Portfolio',classes:'noSelect'},
						{text: 'Blog', value: 'blog',classes:'noSelect'},
						{text: 'Circle', value: 'circle',classes:'noSelect'},
						{text: 'Square', value: 'square',classes:'noSelect'},
						{text: 'Table', value: 'table',classes:'noSelect'},
					]
				};

			cont_cats[3] = {
				type: 'label',
				text:'Only Full view is available in free version. If you need other views, you need to buy the Paid version.',
				classes:'pro_label'
			};

			
			editor.windowManager.open( 
			{
				title: ' Team WD',
				body:[ {
				type: 'tabpanel',
					activeTab : tab,
					items:[{
					title: 'Single Contact',
					type: "form",
					layout: 'flex',
					direction: 'column',
					align: 'stretch',
					items: single_cont,
				},
				{
					title: 'Contacts Category',
					type: "form",
					layout: 'flex',
					direction: 'column',
					align: 'stretch',
					items: cont_cats,
				}]

				}],
				onsubmit: function(e) {
					var tabIdx = e.control._items[0].activeTabId;
					tabIdx = tabIdx.substr(1);
					var data = e.data;
					if(tabIdx == '0'){
					  if(data.contacts=='' )
						return ;
					}
					var shortcode_str = '[';
					if(tabIdx == '0'){
						shortcode_str += sh_tag + ' id="'+data.contacts+'"]';
					} 
					else {
						shortcode_str += sh_tag + ' cats="';
						var i = 0;
						for (var k in data) {
							if (data.hasOwnProperty(k)) {
							  if (k.indexOf('contCats-') == 0 && data[k]==true) {
								shortcode_str+=k.substr(9)+',';
								i++;
							  }
							}
						}
						if(i==0){
						  return ;
						}
						shortcode_str+='" type="'+data.type+'" order="'+data.order+'" tab="1"]';
					}
					editor.insertContent( shortcode_str );	
				}
			});
		});

		//add button
		editor.addButton('contact', {
			icon: 'contact',
			tooltip: ' Team WD',
			image: sc_plugin_url + '/images/Staff_Directory_WD_menu.png',
			onclick: function() {
                editor.execCommand('cont_popup', '', {
                    contact: '',
                    cats: '',
                    order: 'id',
                    types: 'full',
                });
			}
		});

		//replace from shortcode to an image placeholder
		editor.on('BeforeSetcontent', function(event){
			event.content = replaceContShortcodes( event.content );
		});

		//replace from image placeholder to shortcode
		editor.on('GetContent', function(event){
			event.content = restoreContShortcodes(event.content);
		});

		//open popup on placeholder double click
		editor.on('DblClick', function (e) {
			if (e.target.nodeName == 'IMG' && e.target.className.indexOf('wp-Staff_Directory_WD') > -1) {
				var title = e.target.attributes['data-sh-cont-attr'].value;
				title = window.decodeURIComponent(title);
				editor.execCommand('cont_popup', '', {
					contact: getAttr(title, 'id'),
					cats: getAttr(title, 'cats'),
					order:getAttr(title, 'order'),
					types:getAttr(title, 'type'),
					tab:getAttr(title,'tab')
				});
			}
		});
    });
})();

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}