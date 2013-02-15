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
	
	var GetAccessGerejaID = Ext.get('AccessGerejaID').getValue();
	
	Ext.define('GerejaModel', {
		extend: 'Ext.data.Model',
		fields: [
			{ name: 'id', type: 'int' },
			{ name: 'nama', type: 'string' },
			{ name: 'alamat', type: 'string' },
			{ name: 'kota', type: 'string' },
			{ name: 'propinsi', type: 'string' },
			{ name: 'negara', type: 'string' },
			{ name: 'name', type: 'string' },
			{ name: 'logo', type: 'string' }
		]
	});
	var GerejaStore = Ext.create('Ext.data.Store', {
		model: 'GerejaModel', autoLoad: true, pageSize: 25, remoteSort: true,
        sorters: [{ property: 'nama', direction: 'ASC' }],
		proxy: {
			type: 'ajax', extraParams: { RequestName: 'Gereja', id: GetAccessGerejaID },
			url : Web.HOST + '/administrator/grid',
			reader: { root: 'GerejaData', totalProperty: 'GerejaCount' }
		}
	});
    
	var GerejaGrid = new Ext.grid.GridPanel({
		title: 'Gereja<br />Berikut adalah gereja yang terdaftar di SIMETRI JEMAAT MANAGER, silahkan tambahkan gereja sesuai dengan informasinya',
		viewConfig: { forceFit: true }, store: GerejaStore, height: 335, renderTo: 'grid-member',
		features: [{ ftype: 'filters', encode: true, local: false }],
		columns: [ {
					header: 'Nama', dataIndex: 'nama', sortable: true, filter: true, width: 175
			}, {	header: 'Alamat', dataIndex: 'alamat', sortable: true, filter: true, width: 175
			}, {	header: 'Kota', dataIndex: 'kota', sortable: true, filter: true, width: 175
			}, {	header: 'Propinsi', dataIndex: 'propinsi', sortable: true, filter: true, width: 175
			}, {	header: 'Negara', dataIndex: 'negara', sortable: true, filter: true, width: 175
			}, {	header: 'User Gereja', dataIndex: 'name', sortable: true, filter: true, width: 175
			}, {	header: 'Logo', dataIndex: 'logo', sortable: true, filter: { type: 'list', options: [ 'Ada' , 'Tidak Ada'] }, width: 175, renderer: function(Value) { return (Value.length > 0) ? 'Ada' : 'Tidak Ada';  }
		} ],
		tbar: [ {
				text: 'Tambah', iconCls: 'addIcon', tooltip: 'Tambah gereja', id: 'AddTB', handler: function() { CallWindowGereja({ GerejaID: 0 }); }
			}, '-', {
				text: 'Ubah', iconCls: 'editIcon', tooltip: 'Ubah gereja', id: 'UpdateTB', handler: function() { GerejaGrid.Update({ }); }
			}, '-', {
				text: 'API', iconCls: 'editIcon', tooltip: 'Api gereja', id: 'ApiTB', hidden: true, handler: function() { GerejaGrid.Update({ Window: 'WindowApi' }); }
			}, '-', {
				text: 'Hapus', iconCls: 'delIcon', tooltip: 'Hapus gereja', id: 'DeleteTB', handler: function() {
					if (GerejaGrid.getSelectionModel().getSelection().length == 0) {
						Ext.Msg.alert('Informasi', 'Silahkan memilih data.');
						return false;
					}
					
					Ext.MessageBox.confirm('Konfirmasi', 'Apa anda yakin akan menghapus data ini ?', GerejaGrid.Delete);
				}
			}, '->', {
                id: 'SearchPM', xtype: 'textfield', tooltip: 'Cari gereja', emptyText: 'Cari', listeners: {
                    'specialKey': function(field, el) {
                        if (el.getKey() == Ext.EventObject.ENTER) {
                            var value = Ext.getCmp('SearchPM').getValue();
                            if ( value ) {
								GerejaGrid.LoadGrid({ RequestName: 'Gereja', id: GetAccessGerejaID, NameLike: value });
                            }
                        }
                    }
                }
            }, '-', {
				text: 'Reset', tooltip: 'Reset pencarian', iconCls: 'refreshIcon', handler: function() {
					GerejaGrid.LoadGrid({ RequestName: 'Gereja', id: GetAccessGerejaID });
				}
		} ],
		bbar: new Ext.PagingToolbar( {
			store: GerejaStore, displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: 'No topics to display'
		} ),
		listeners: {
			'itemdblclick': function(model, records) {
				GerejaGrid.Update({ });
            }
        },
		LoadGrid: function(Param) {
			GerejaStore.proxy.extraParams = Param;
			GerejaStore.load();
		},
		Update: function(Param) {
			var Data = GerejaGrid.getSelectionModel().getSelection();
			if (Data.length == 0) {
				Ext.Msg.alert('Informasi', 'Silahkan memilih data.');
				return false;
			}
			
			Param.Window = (Param.Window == null) ? 'WindowEdit' : Param.Window;
			Ext.Ajax.request({
				url: Web.HOST + '/administrator/ajax',
				params: { Action: 'GetGerejaByID', GerejaID: Data[0].data.id },
				success: function(Result) {
					eval('var Record = ' + Result.responseText)
					Record.GerejaID = Record.id;
					
					if (Param.Window == 'WindowApi') {
						CallWindowApi(Record);
					} else {
						CallWindowGereja(Record);
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
				params: { Action: 'DeteleGerejaByID', GerejaID: GerejaGrid.getSelectionModel().getSelection()[0].data.id },
				success: function(TempResult) {
					eval('var Result = ' + TempResult.responseText)
					
					Ext.Msg.alert('Informasi', Result.Message);
					if (Result.QueryStatus == '1') {
						GerejaStore.load();
					}
				}
			});
		}
	});
	
	function CallWindowApi(Param) {
		var Win = new Ext.Window({
			layout: 'fit', width: 350, height: 125,
			closeAction: 'hide', plain: true, modal: true, title: 'Window API',
			buttons: [ {
						text: 'Save', id: 'DeleteED', handler: function() { Win.Update(); }
				}, {	text: 'Close', handler: function() {
						Win.hide();
				}
			}],
			listeners: {
				show: function(w) {
					Ext.Ajax.request({
						url: Web.HOST + '/gereja/gereja/view/gereja_api',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							Win.client_id = new Ext.form.TextField({ renderTo: 'client_idED', width: 225, allowBlank: false, blankText: 'Masukkan Client ID', readOnly: true });
							Win.privatekey = new Ext.form.TextField({ renderTo: 'privatekeyED', width: 225, allowBlank: false, blankText: 'Masukkan Private Key', readOnly: true });
							Win.LoadApi(Param.GerejaID);
						}
					});
				},
				hide: function(w) {
					w.destroy();
					w = Win = null;
				}
			},
			LoadApi: function(gereja_id) {
				Ext.Ajax.request({
					url: Web.HOST + '/gereja/gereja/action',
					params: { Action: 'GetGerejaApiByID', gereja_id: gereja_id },
					success: function(Result) {
					eval('var Record = ' + Result.responseText)
						Win.config_id = Record.config_id;
						Win.client_id.setValue(Record.client_id);
						Win.privatekey.setValue(Record.privatekey);
						
						Win.client_id.setReadOnly(false);
						Win.privatekey.setReadOnly(false);
					}
				});
			},
			Update: function() {
				var Param = new Object();
				Param.Action = 'UpdateGerejaApi';
				Param.config_id = Win.config_id;
				Param.client_id = Win.client_id.getValue();
				Param.privatekey = Win.privatekey.getValue();
				
				// Validation
				var Validation = true;
				if (! Win.client_id.validate()) {
					Validation = false;
				}
				if (! Win.privatekey.validate()) {
					Validation = false;
				}
				if (! Validation) {
					return;
				}
				
				Ext.Ajax.request({
					params: Param, url: Web.HOST + '/gereja/gereja/action',
					success: function(RawResult) {
						eval('var Result = ' + RawResult.responseText);
						Ext.Msg.alert('Informasi', Result.Message);
						Win.hide();
					}
				});
			}
		});
		Win.show();
	}
	
	function CallWindowGereja(Gereja) {
		Ext.getStore('AdminGerejaStore').load();
		
		var WinGereja = new Ext.Window({
			layout: 'fit', width: 350, height: 355,
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Save', id: 'DeleteED', handler: function() { WinGereja.SaveGereja(); }
				}, {	text: 'Close', handler: function() {
						WinGereja.hide();
				}
			}],
			listeners: {
				show: function(w) {
					var Title = (Gereja.GerejaID == 0) ? 'Entry Gereja - [New]' : 'Entry Gereja - [Edit]';
					w.setTitle(Title);
					
					Ext.Ajax.request({
						url: Web.HOST + '/administrator/request/entry-gereja-popup',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							WinGereja.GerejaID = Gereja.GerejaID;
							WinGereja.nama = new Ext.form.TextField({ renderTo: 'namaED', width: 225, allowBlank: false, blankText: 'Masukkan Nama' });
							WinGereja.alamat = new Ext.form.TextArea({ renderTo: 'alamatED', width: 225, height: 50 });
							WinGereja.negara = new Ext.form.ComboBox({
								triggerAction: 'all', lazyRender: true, forceSelection: true,
								store: Ext.getStore('NegaraStore'), width: 225, minChars: 1,
								valueField: 'id', displayField: 'negara', typeAhead: true,
								renderTo: 'negaraED', readonly: false, editable: true,
								allowBlank: false, blankText: 'Masukkan Negara',
								listeners: {
									select: function(combo, record) {
										WinGereja.propinsi.reset();
										WinGereja.kota.reset();
									}
								}
							});
							WinGereja.propinsi = new Ext.form.ComboBox({
								triggerAction: 'all', lazyRender: true, forceSelection: true,
								store: Ext.getStore('PropinsiStore'), width: 225, minChars: 1,
								valueField: 'id', displayField: 'propinsi', typeAhead: true,
								renderTo: 'propinsiED', readonly: false, editable: true,
								allowBlank: false, blankText: 'Masukkan Propinsi',
								listeners: {
									select: function(combo, record) {
										WinGereja.kota.reset();
									},
									beforequery: function() {
										var idnegara = WinGereja.negara.getValue();
										if (idnegara == null) {
											return false;
										}
										
										WinGereja.propinsi.store.proxy.extraParams.idnegara = idnegara;
										WinGereja.propinsi.store.load();
									}
								}
							});
							WinGereja.kota = new Ext.form.ComboBox({
								triggerAction: 'all', lazyRender: true, forceSelection: true,
								store: Ext.getStore('KotaStore'), width: 225, minChars: 1,
								valueField: 'id', displayField: 'kota', typeAhead: true,
								renderTo: 'kotaED', readonly: false, editable: true,
								allowBlank: false, blankText: 'Masukkan Kota',
								listeners: {
									beforequery: function() {
										var idpropinsi = WinGereja.propinsi.getValue();
										if (idpropinsi == null) {
											return false;
										}
										
										WinGereja.kota.store.proxy.extraParams.idpropinsi = idpropinsi;
										WinGereja.kota.store.load();
									}
								}
							});
							WinGereja.userid = new Ext.form.ComboBox({
								triggerAction: 'all', lazyRender: true, forceSelection: true,
								queryMode: 'local', store: Ext.getStore('AdminGerejaStore'), width: 225,
								valueField: 'id', displayField: 'name',
								renderTo: 'userED', readonly: true, editable: false
							});
							WinGereja.Logo = Ext.create('Ext.form.field.File', { name: 'Logo', renderTo: 'LogoED', width: 225, hideLabel: true });
							Ext.get('CntLogo').dom.innerHTML = '<img src="' + Web.HOST + '/images/temple.png" />';
							
							// Populate Record
							if (Gereja.GerejaID > 0) {
								WinGereja.nama.setValue(Gereja.nama);
								WinGereja.alamat.setValue(Gereja.alamat);
								
								if (Gereja.idkota != 0) {
									WinGereja.kota.store.add({ "id": Gereja.idkota, "kota": Gereja.kota });
									WinGereja.kota.setValue(Gereja.idkota);
								}
								if (Gereja.idpropinsi != 0) {
									WinGereja.propinsi.store.add({ "id": Gereja.idpropinsi, "propinsi": Gereja.propinsi });
									WinGereja.propinsi.setValue(Gereja.idpropinsi);
								}
								if (Gereja.idnegara != 0) {
									WinGereja.negara.store.add({ "id": Gereja.idnegara, "negara": Gereja.negara });
									WinGereja.negara.setValue(Gereja.idnegara);
								}
								if (Gereja.UserID != 0) {
									WinGereja.userid.store.add({ "id": Gereja.UserID, "name": Gereja.name });
									WinGereja.userid.setValue(Gereja.UserID);
								}
								if (Gereja.logo.length > 0) {
									Ext.get('CntLogo').dom.innerHTML = '<img src="' + Web.HOST + '/images/logo/' + Gereja.logo + '" />';
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
					w = WinGereja = null;
				}
			},
			SaveGereja: function() {
				var Param = new Object();
				Param.Action = 'EditGereja';
				Param.GerejaID = WinGereja.GerejaID;
				Param.nama = WinGereja.nama.getValue();
				Param.alamat = WinGereja.alamat.getValue();
				Param.idkota = WinGereja.kota.getValue();
				Param.idpropinsi = WinGereja.propinsi.getValue();
				Param.idnegara = WinGereja.negara.getValue();
				Param.UserID = (WinGereja.userid.getValue() == null) ? 0 : WinGereja.userid.getValue();
				
				// Validation
				var Validation = true;
				if (! WinGereja.nama.validate()) {
					Validation = false;
				}
				if (! WinGereja.kota.validate()) {
					Validation = false;
				}
				if (! WinGereja.propinsi.validate()) {
					Validation = false;
				}
				if (! WinGereja.negara.validate()) {
					Validation = false;
				}
				
				if (! Validation) {
					return;
				}
				
                var Form = new Ext.FormPanel({
                    renderTo: 'FormPanel', fileUpload: true,
                    width: 500, frame: true, autoHeight: true, labelWidth: 50, 
                    items: [
						{ 	xtype: 'textfield', fieldLabel: 'FormType', name: 'FormType', value: 'Gereja' },
						{ 	xtype: 'textfield', fieldLabel: 'Action', name: 'Action', value: 'EditGereja' },
						{ 	xtype: 'textfield', fieldLabel: 'GerejaID', name: 'GerejaID', value: Param.GerejaID },
						{	xtype: 'textfield', fieldLabel: 'nama', name: 'nama', value: Param.nama },
						{	xtype: 'textfield', fieldLabel: 'alamat', name: 'alamat', value: Param.alamat },
						{	xtype: 'textfield', fieldLabel: 'kota', name: 'idkota', value: Param.idkota },
						{	xtype: 'textfield', fieldLabel: 'propinsi', name: 'idpropinsi', value: Param.idpropinsi },
						{	xtype: 'textfield', fieldLabel: 'negara', name: 'idnegara', value: Param.idnegara },
						{	xtype: 'textfield', fieldLabel: 'UserID', name: 'UserID', value: Param.UserID },
						WinGereja.Logo
                    ],
                    defaults: { anchor: '95%', allowBlank: true, msgTarget: 'side' }
                });
                
                Form.getForm().submit({
                    url: Web.HOST + '/administrator/upload',
                    waitMsg: 'Upload Document ...',
                    success: function(DataForm, Ajax) {
                        Ext.get('FormPanel').dom.innerHTML = '';
                        Ext.Msg.alert('Informasi', Ajax.result.Message);
                        
                        if (Ajax.result.UploadStatus == 1) {
							GerejaStore.load();
							WinGereja.hide();
                        } else {
							WinGereja.Logo = Ext.create('Ext.form.field.File', { name: 'Logo', renderTo: 'LogoED', width: 225, hideLabel: true });
						}
                    }
                });
			}
		});
		WinGereja.show();
	}
	
	if (! Renderer.AllowedWrite()) {
		Ext.getCmp('AddTB').disable();
		Ext.getCmp('UpdateTB').disable();
		Ext.getCmp('DeleteTB').disable();
	} else if (GetAccessGerejaID != 0) {
		Ext.getCmp('AddTB').disable();
		Ext.getCmp('DeleteTB').disable();
	}
	if (GetAccessGerejaID == 0) {
		Ext.getCmp('ApiTB').show();
	}
	
	Renderer.InitWindowSize({ Panel: -1, Grid: GerejaGrid, Toolbar: 70 });
});