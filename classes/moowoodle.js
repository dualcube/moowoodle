// purpose: register a moowoodle button in the rich editor in the admin interface
(function() {
    tinymce.create('tinymce.plugins.moowoodle', {
        init : function(ed, url) {
            ed.addButton('moowoodle', {
                title : 'moowoodle',
                image : url+'/dualcube.png',
                onclick : function() {
                     ed.selection.setContent('[moowoodle cohort="" group="" course="" class="moowoodle" target="_self" authtext="" activity="0"]' + ed.selection.getContent() + '[/moowoodle]');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('moowoodle', tinymce.plugins.moowoodle);
})();
