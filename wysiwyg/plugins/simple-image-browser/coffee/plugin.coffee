###
    Classy Image Browser for CKEditor allows you to load an directory with 
    images / files and use them to include in your content.
    ---
    Author: EpicSoftworks
###


###
    The dialog window. I've moved the GET to an actual hook in CKEditor.
###
CKEDITOR.dialog.add 'simple-image-browser-dialog', (editor) ->
    return {
            title: 'Simple Image Browser',
            minWidth: 800,
            minHeight: 400,
            maxWidth: 800,
            maxHeight: 400,
            contents: [{
                id: 'tab-step1',
                label: 'Browse for images',
                elements: [{
                    type: 'html',
                    align : 'left',
                    id: 'titleid',
                    style: 'font-size: 20px; font-weight: bold;',
                    html: 'Browse for pictures',
                },
                {
                    type: 'html',
                    align : 'left',
                    id: 'msg',
                    style: '',
                    html: '<div id="imageBrowser"></div>',
                }]
            },
            {
                id: 'tab-step2',
                label: 'About this plugin',
                elements: [{
                    type: 'html',
                    align : 'left',
                    id: 'framepreviewtitleid',
                    style: 'font-size: 20px; font-weight: bold;',
                    html: 'About this author',
                },
                {
                    type: 'html',
                    align : 'left',
                    id: 'descriptionid',
                    style: 'position:relative;width:800px;',
                    html: 'EpicSoftworks released this plugin for free under the MIT licence.<br />You are free to use this for personal, educational or commercial use.<br /><br />Free as in, the freedom to use.<br /><br /><a href="http://epicsoftworks.nl/" target="_blank">Visit my website >></a>',
                }]
            }]
    }
    
###
    The plugin itself. Simple stuff.
###
CKEDITOR.plugins.add 'simple-image-browser', {

    init: (editor) ->
    
        ###
            A later to be implemented feature to be able to switch display types
            list / thumbnail 
        ###
        if typeof CKEDITOR.config.simpleImageBrowserListType == 'undefined'
            CKEDITOR.config.simpleImageBrowserListType = 'thumbnails'
    
        ###
            Eventhook for when the dialog opens up.
        ###
        editor.on 'dialogShow', (event) ->
            dialog = event.data
            if dialog.getName() == 'simple-image-browser-dialog'
                $.get CKEDITOR.config.simpleImageBrowserURL, (images) ->
                    console.log images
                    json = $.parseJSON images
                    images = ''
                    $.each json, (key, value) ->
                        if CKEDITOR.config.simpleImageBrowserListType == 'thumbnails'
                            images = images + '<div onclick="CKEDITOR.tools.simpleimagebrowserinsertpicture(\''+value.url+'\');" style="position:relative;width:75px;height:75px;margin:5px;background-image:url(\''+value.url+'\');background-repeat:no-repeat;background-size:125%;background-position:center center;float:left;"></div>'
                        else
                            images = 'link'
                        return
                    $ '#imageBrowser'
                        .html images
            return

        ###
            Add the command to open the dialog window.
        ###
        editor.addCommand 'simple-image-browser-start', new CKEDITOR.dialogCommand 'simple-image-browser-dialog'
 
 
        ###
            The method that injects the image into the editor.
        ###
        CKEDITOR.tools.simpleimagebrowserinsertpicture = (event) ->
            console.log event
            editor = CKEDITOR.currentInstance
            dialog = CKEDITOR.dialog.getCurrent()
            html = '<img src="'+event+'" data-cke-saved-src="'+event+'" alt="'+event+'" />'
            editor.config.allowedContent = true;
            editor.insertHtml html.trim()
            dialog.hide();
            return
 
        ###
            Add a button to the editor to fire the command that opens the dialog
        ###
        editor.ui.addButton 'Simple Image Browser', {
            label: 'Simple Image Browser ',
            command: 'simple-image-browser-start',
            icon: this.path + 'images/icon.png'
        }
        
        return
        
}