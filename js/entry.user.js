Ext.Loader.setConfig({ enabled: true });
Ext.Loader.setPath('Ext.ux', '../../extjs/examples/ux');
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.ux.grid.FiltersFeature',
    'Ext.toolbar.Paging'
]);

Ext.onReady(function() {
	Ext.QuickTips.init();
	if (! Renderer.AllowedRead()) {
		return;
	}
    
	var AccessUserID = Ext.get('AccessUserID').getValue();
	Ext.getStore('GroupStore').load();
    
	Ext.define('UserModel', {
		extend: 'Ext.data.Model',
		fields: [
			{ name: 'id', type: 'int' },
			{ name: 'username', type: 'string' },
			{ name: 'email', type: 'string' },
			{ name: 'name', type: 'string' },
			{ name: 'group_name', type: 'string' }
		]
	});
	var UserStore = Ext.create('Ext.data.Store', {
		model: 'UserModel', autoLoad: true, pageSize: 25, remoteSort: true,
        sorters: [{ property: 'name', direction: 'ASC' }],
		proxy: {
			type: 'ajax', extraParams: { RequestName: 'User', UserID: AccessUserID },
			url : Web.HOST + '/administrator/grid',
			reader: { root: 'UserData', totalProperty: 'UserCount' }
		}
	});
    
	var UserGrid = new Ext.grid.GridPanel({
		viewConfig: { forceFit: true }, store: UserStore, height: 335, renderTo: 'grid-member',
		features: [{ ftype: 'filters', encode: true, local: false }],
		columns: [ {
					header: 'User Name', dataIndex: 'username', sortable: true, filter: true, width: 200
			}, {	header: 'Name', dataIndex: 'name', sortable: true, filter: true, width: 200
			}, {	header: 'Email', dataIndex: 'email', sortable: true, filter: true, width: 200
			}, {	header: 'Group', dataIndex: 'group_name', sortable: true, filter: true, width: 200
		} ],
		tbar: [ {
				text: 'Tambah', width: 100, iconCls: 'addIcon', tooltip: 'Tambah user', id: 'AddTB', handler: function() { CallWindowUser({ UserID: 0 }); }
			}, '-', {
				text: 'Ubah', width: 100, iconCls: 'editIcon', tooltip: 'Ubah user', id: 'UpdateTB', handler: function() { UserGrid.Update({ }); }
			}, '-', {
				text: 'Upload', width: 100, iconCls: 'editIcon', tooltip: 'Ubah user', id: 'UploadTB', handler: function() { UserGrid.Update({ Window: 'Upload' }); }
			}, '-', {
				text: 'Hapus', width: 100, iconCls: 'delIcon', tooltip: 'Hapus user', id: 'DeleteTB', handler: function() {
					if (UserGrid.getSelectionModel().getSelection().length == 0) {
						Ext.Msg.alert('Informasi', 'Silahkan memilih data.');
						return false;
					}
					
					Ext.MessageBox.confirm('Konfirmasi', 'Apa anda yakin akan menghapus data ini ?', UserGrid.Delete);
				}
			}, '->', {
                id: 'SearchPM', xtype: 'textfield', tooltip: 'Cari user', emptyText: 'Cari', width: 100, listeners: {
                    'specialKey': function(field, el) {
                        if (el.getKey() == Ext.EventObject.ENTER) {
                            var value = Ext.getCmp('SearchPM').getValue();
                            if ( value ) {
								UserGrid.LoadGrid({ RequestName: 'User', UserID: AccessUserID, NameLike: value });
                            }
                        }
                    }
                }
            }, '-', {
				text: 'Reset', tooltip: 'Reset pencarian', iconCls: 'refreshIcon', width: 100, handler: function() {
					UserGrid.LoadGrid({ RequestName: 'User', UserID: AccessUserID });
				}
		} ],
		bbar: new Ext.PagingToolbar( {
			store: UserStore, displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: 'No topics to display'
		} ),
		listeners: {
			'itemdblclick': function(model, records) {
				UserGrid.Update({ });
            }
        },
		LoadGrid: function(Param) {
			UserStore.proxy.extraParams = Param;
			UserStore.load();
		},
		Update: function(Param) {
			Param.Window = (Param.Window == null) ? '' : Param.Window;
			var Data = UserGrid.getSelectionModel().getSelection();
			if (Data.length == 0) {
				Ext.Msg.alert('Informasi', 'Silahkan memilih data.');
				return false;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/administrator/ajax',
				params: { Action: 'GetUserByID', UserID: Data[0].data.id },
				success: function(Result) {
					eval('var Record = ' + Result.responseText)
					Record.UserID = Record.id;
					
					if (Param.Window == 'Upload') {
						Upload(Record);
					} else {
						CallWindowUser(Record);
					}
				}
			});
		},
		Delete: function(Value) {
			if (Value == 'no') {
				return;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/administrator/ajax',
				params: { Action: 'DeteleUserByID', UserID: UserGrid.getSelectionModel().getSelection()[0].data.id },
				success: function(TempResult) {
					eval('var Result = ' + TempResult.responseText)
					
					Ext.Msg.alert('Informasi', Result.Message);
					if (Result.QueryStatus == '1') {
						UserStore.load();
					}
				}
			});
		}
	});
	
	function CallWindowUser(User) {
		var WinUser = new Ext.Window({
			layout: 'fit', width: 350, height: 215,
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Save', id: 'DeleteED', handler: function() { WinUser.SaveUser(); }
				}, {	text: 'Close', handler: function() {
						WinUser.hide();
				}
			}],
			listeners: {
				show: function(w) {
					var Title = (User.UserID == 0) ? 'Entry User - [New]' : 'Entry User - [Edit]';
					w.setTitle(Title);
					
					Ext.Ajax.request({
						url: Web.HOST + '/administrator/request/entry-user-popup',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							WinUser.UserID = User.UserID;
							WinUser.username = new Ext.form.TextField({ renderTo: 'usernameED', width: 225, allowBlank: false, blankText: 'Masukkan User Name' });
							WinUser.email = new Ext.form.TextField({ renderTo: 'emailED', width: 225, allowBlank: false, blankText: 'Masukkan Email' });
							WinUser.name = new Ext.form.TextField({ renderTo: 'nameED', width: 225, allowBlank: false, blankText: 'Masukkan Nama' });
                            WinUser.group = new Ext.form.ComboBox({
                                triggerAction: 'all', lazyRender: true, forceSelection: true,
                                queryMode: 'local', store: Ext.getStore('GroupStore'), width: 175,
                                valueField: 'group_id', displayField: 'group_name', typeAhead: true,
                                renderTo: 'groupED', readonly: true, editable: false
                            });
							
							if (WinUser.UserID > 0) {
								WinUser.password = new Ext.form.TextField({ renderTo: 'passwordED', width: 225, blankText: 'Masukkan Password' });
							} else {
								WinUser.password = new Ext.form.TextField({ renderTo: 'passwordED', width: 225, allowBlank: false, blankText: 'Masukkan Password' });
							}
							
                            
							// Populate Record
							if (User.UserID > 0) {
								WinUser.username.setValue(User.username);
								WinUser.username.disable();
								WinUser.email.setValue(User.email);
								WinUser.name.setValue(User.name);
								WinUser.group.setValue(User.group_id);
								
								if (AccessUserID != 0) {
									WinUser.group.disable();
								}
								
								if (User.foto.length > 0) {
									Ext.get('CntFoto').dom.innerHTML = '<img src="' + User.FotoLink + '" />';
								}
							}
						}
					});
					
					if (! Renderer.AllowedWrite()) {
						Ext.getCmp('DeleteED').hide();
					}
				},
				hide: function(w) {
					w.destroy();
					w = WinUser = null;
				}
			},
			SaveUser: function() {
				var Param = new Object();
				Param.Action = 'EditUser';
				Param.UserID = WinUser.UserID;
				Param.username = WinUser.username.getValue();
				Param.email = WinUser.email.getValue();
				Param.name = WinUser.name.getValue();
				Param.password = WinUser.password.getValue();
				Param.group_id = WinUser.group.getValue();
				
				// Validation
				var Validation = true;
				if (! WinUser.username.validate()) {
					Validation = false;
				}
				if (! WinUser.email.validate()) {
					Validation = false;
				}
				if (! WinUser.name.validate()) {
					Validation = false;
				}
                if (Param.UserID == 0) {
                    if (! WinUser.password.validate()) {
                        Validation = false;
                    }
                }
				
				if (! Validation) {
					return;
				}
				
				Ext.Ajax.request({
					params: Param,
					url: Web.HOST + '/administrator/ajax',
					success: function(TempResult) {
						eval('var Result = ' + TempResult.responseText)
                        Ext.Msg.alert('Informasi', Result.Message);
                        
                        if (Result.QueryStatus == '1') {
							UserStore.load();
							WinUser.hide();
                        }
					}
				});
			}
		});
		WinUser.show();
	}
	
	function Upload(User) {
		var WinUser = new Ext.Window({
			layout: 'fit', width: 325, height: 230, title: 'Update Photo',
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Save', id: 'DeleteED', handler: function() { WinUser.SaveUser(); }
				}, {	text: 'Close', handler: function() {
						WinUser.hide();
				}
			}],
			listeners: {
				show: function(w) {
					Ext.Ajax.request({
						url: Web.HOST + '/administrator/request/entry-user-foto-popup',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							WinUser.UserID = User.UserID;
							WinUser.foto = Ext.create('Ext.form.field.File', { name: 'foto', renderTo: 'fotoED', width: 225, hideLabel: true });
							
							// Populate Record
							if (User.UserID > 0) {
								Ext.get('CntFoto').dom.innerHTML = '<img src="' + User.FotoLink + '" />';
							}
						}
					});
					
					if (! Renderer.AllowedWrite()) {
						Ext.getCmp('DeleteED').hide();
					}
				},
				hide: function(w) {
					w.destroy();
					w = WinUser = null;
				}
			},
			SaveUser: function() {
				var Param = new Object();
				Param.Action = 'EditUser';
				Param.UserID = WinUser.UserID;
				
				var Form = new Ext.FormPanel({
					renderTo: 'FormPanel', fileUpload: true,
					width: 500, frame: true, autoHeight: true, labelWidth: 50, 
					items: [
						{ 	xtype: 'textfield', fieldLabel: 'FormType', name: 'FormType', value: 'UserPhoto' },
						{ 	xtype: 'textfield', fieldLabel: 'UserID', name: 'UserID', value: WinUser.UserID },
						WinUser.foto
					],
					defaults: { anchor: '95%', allowBlank: false, msgTarget: 'side' }
				});
				
				
				Form.getForm().submit({
					url: Web.HOST + '/administrator/upload',
					waitMsg: 'Upload Document ...',
					success: function(DataForm, Ajax) {
						Ext.get('FormPanel').dom.innerHTML = '';
						Ext.Msg.alert('Informasi', Ajax.result.Message);
						
						if (Ajax.result.UploadStatus == 1) {
							UserStore.load();
							WinUser.hide();
						} else {
							WinUser.foto = Ext.create('Ext.form.field.File', { name: 'foto', renderTo: 'fotoED', width: 225, hideLabel: true });
						}
					}
				});
			}
		});
		WinUser.show();
	}
	
	if (! Renderer.AllowedWrite()) {
		Ext.getCmp('AddTB').disable();
		Ext.getCmp('UpdateTB').disable();
		Ext.getCmp('UploadTB').disable();
		Ext.getCmp('DeleteTB').disable();
	} else if (AccessUserID != 0) {
		Ext.getCmp('AddTB').disable();
		Ext.getCmp('DeleteTB').disable();
	}
	
	Renderer.InitWindowSize({ Panel: -1, Grid: UserGrid, Toolbar: 70 });
});