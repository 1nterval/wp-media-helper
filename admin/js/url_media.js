var l10n = wp.media.view.l10n;

jQuery(function($){

    // watch the moment when the editor will be available
    $(document.body).on( 'click', '.insert-media', function(){

        // get a reference to the main controller
        var editor = wp.media.editor.get('content');
        
        // triggered when the save button is clicked
        editor.urlMediaSave = function(){
        
            var props = this.controller.state().props;

            wp.media.post( 'media-helpers-save', {
                nonce:   'TODO',
                post_id: wp.media.model.settings.post.id,
                src: props.get('url'),
                title: props.get('title')
            }).done( _.bind(function( resp, status, xhr ) {
                
                // ajouter le nouveau media directement
                var lib = this.get('library').add(wp.media.model.Attachment.create( resp, xhr ));
                
                // reset user inputs
                props.set('url', '');
                props.set('title', '');
                
                // afficher la bibliothèque
                this.frame.setState('insert').content.mode('browse');
                
                // le sélectionner
                var selection = this.frame.content.get().options.selection;
                selection.remove(selection.models);
                selection.add(lib.get(resp.id));
                
            }, this.controller.state()));
        
        };
        
        // triggered when the "from URL" tab is shown
        editor.urlMediaRender = function(){
        
            // create the content view
            this.content.set( new wp.media.view.URLMedia({
			    controller: this,
			    model: this.state().props
		    }) );
        
        };
        
        editor.urlMediaRefreshToolbar = function(){
        
            // disable the button if the URL is void
            var url = this.state().props.get('url');
            this.toolbar.get().get('save').model.set( 'disabled', ! url || url === 'http://' );
        
        };
        
        // add a new entry to the router (a new tab)
        editor.router.get().set({
		    url: {
		        text:   l10n.mediahelper_insertFromURL,
		        priority: 60
	        }
        });
        
        // add a new primary button
        editor.toolbar.get().set({
            save: {
                text: l10n.mediahelper_save,
                style: 'primary',
                priority: 80,
                requires: false,
                click: editor.urlMediaSave,
                disabled: true
            }
	    }, { silent: true });
	    
        
        // bind events
        editor.on( 'content:render:url', editor.urlMediaRender, editor);
        
        // add some model to store the data entered by user
        editor.state().props = new Backbone.Model({ url: '', title: '' });
        editor.state().props.on( 'change', function(){
            editor.urlMediaRefreshToolbar();
            this.toolbar.get().refresh();
        }, editor );
        
        // set the view if it's the current one
        if(editor.content.mode() === 'url') {
            editor.urlMediaRender();
            editor.router.get().get('url').$el.addClass('active');
        }
        
    });

    // custom content : this view contains the main panel UI
    wp.media.view.URLMedia = wp.media.View.extend({
	    className: 'urlmedia',
	    template: wp.media.template('mediahelper-url-media'),
	
	    events: {
		    'input':  'update',
		    'keyup':  'update',
		    'change': 'update'
	    },

	    initialize: function() {
	        this.render();
	    },
	
	    render: function(){
	        // copy model data to the screen
	        this.$el.html( this.template({ 
	            url : this.model.get('url') || 'http://',
	            title : this.model.get('title') || ''
	        }) );
	        return this;
	    },
	
	    update: function( event ) {
	        // copy user input into model
		    if(event.target.id === 'mediahelper-url') { this.model.set( 'url', event.target.value ); }
		    if(event.target.id === 'mediahelper-title') { this.model.set( 'title', event.target.value ); }
	    }
    });

});
