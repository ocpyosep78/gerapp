Ext.define('Stiki.view.Login' ,{
    extend: 'Ext.container.Container',
    alias : 'widget.loginpage',
	
    initComponent: function() {
        Ext.applyIf(this, {
            items: [{ xtype: 'loginwindow' }]
        });
        this.callParent(arguments);
    }
});

Ext.define( 'Stiki.view.LoginWindow', {
    extend: 'Ext.window.Window',
    alias: 'widget.loginwindow',
    y: 106,
    width: 300,
    height: 150,
    closable: false,
    draggable: false,
    layout: 'anchor',
    
    initComponent: function() {
        Ext.apply(this, {
            items: [{ 
                xtype: 'form',
                url: URLS.stiki + '/administrator/',
                border: 0,
                bodyStyle: 'padding: 10px;',
                defaultType: 'textfield',
                items: [{
                    xtype:'container',
                    html: '<div id="loginmsg" style="padding: 10px 0 0 0;"><p>Masukkan username/password untuk login</p></div>'
                },{
                    name: 'username',
                    fieldLabel: 'Username',
                    required:true
                },{
                    name: 'password',
                    fieldLabel: 'Password',
                    inputType: 'password',
                    required:true
                }]
            }],
            buttons: [ {
                name: 'forgetPassword', text: 'Lupa Password',
				handler: function() {
					var WinPassword = new Ext.Window({
						layout: 'fit', width: 410, height: 95, title: 'Lupa Password',
						closeAction: 'hide', plain: true, modal: true,
						buttons: [ {
									text: 'Kirim', handler: function() { WinPassword.SendResetPassword(); }
							}, {	text: 'Batal', handler: function() {
									WinPassword.hide();
							}
						}],
						listeners: {
							show: function(w) {
								var Content = '<div style="padding: 5px;"><div style="float: left; width: 210px; padding: 3px 0 0 0;">Silahkan memasukkan email anda :</div><div style="float: left; width: 175px;"><div id="EmailWP"></div></div></div>'
								w.body.dom.innerHTML = Content;
								WinPassword.email = new Ext.form.TextField({ renderTo: 'EmailWP', width: 175, allowBlank: false, blankText: 'Masukkan Email' });
							},
							hide: function(w) {
								w.destroy();
								w = WinPassword = null;
							}
						},
						SendResetPassword: function() {
							var Param = new Object();
							Param.Action = 'RequestResetPassword';
							Param.email = WinPassword.email.getValue();
							
							// Validation
							var Validation = true;
							if (! WinPassword.email.validate()) {
								Validation = false;
							}
							if (! Validation) {
								return;
							}
							
							Ext.Ajax.request({
								params: Param,
								url: URLS.stiki + '/administrator/ajax',
								success: function(TempResult) {
									eval('var Result = ' + TempResult.responseText)
									Ext.Msg.alert('Informasi', Result.Message);
									
									if (Result.QueryStatus == '1') {
										WinPassword.hide();
									}
								}
							});
						}
					});
					WinPassword.show();
				}
            }, {
                name: 'loginButton',
                text: 'Login'
            } ]
        });
        this.callParent(arguments);
    }
});
