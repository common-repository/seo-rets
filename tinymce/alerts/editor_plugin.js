tinymce.create('tinymce.plugins.srAlerts', {
	init : function(ed, url) {
		ed.addCommand('sr-alerts', function() {
					ed.selection.setContent( ed.selection.getContent() + '<p>[sr-subscribe]</p>');
		});
		ed.addButton('sralerts', {
			title : 'Insert an email signup form, where users can sign up to get updates on the types of houses they\'re looking for.',
			cmd : 'sr-alerts',
			image : url + '/img/email.png'
		});
		ed.onNodeChange.add(function(ed, cm, n) {
			cm.setActive('sralerts', !tinymce.isIE && /^\[sr-subscribe]/.test(n.innerHTML));
		});
	},
	createControl : function(n, cm) {
		return null;
	},
	getInfo : function() {
		return {
			longname : 'Insert Email Signup Form',
			author : 'SEO RETS',
			authorurl : 'http://www.seorets.com',
			infourl : 'javascript:void(0)',
			version : "1.0"
		};
	}
});
tinymce.PluginManager.add('sralerts', tinymce.plugins.srAlerts);