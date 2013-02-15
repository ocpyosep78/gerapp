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
	
	Ext.define('PendidikanModel', {
		extend: 'Ext.data.Model',
		fields: [
			{ name: 'id', type: 'int' },
			{ name: 'pendidikan', type: 'string' }
		]
	});
	var PendidikanStore = Ext.create('Ext.data.Store', {
		model: 'PendidikanModel', autoLoad: true, pageSize: 25, remoteSort: true,
        sorters: [{ property: 'pendidikan', direction: 'ASC' }],
		proxy: {
			type: 'ajax', extraParams: { RequestName: 'Pendidikan' },
			url : Web.HOST + '/administrator/grid',
			reader: { root: 'PendidikanData', totalProperty: 'PendidikanCount' }
		}
	});
    
	var PendidikanGrid = new Ext.grid.GridPanel({
		viewConfig: { forceFit: true }, store: PendidikanStore, height: 335, renderTo: 'grid-member',
		features: [{ ftype: 'filters', encode: true, local: false }],
		columns: [ {
					header: 'Pendidikan', dataIndex: 'pendidikan', sortable: true, filter: true, width: 600, flex: 1
		} ],
		tbar: [ {
				text: 'Tambah', width: 100, iconCls: 'addIcon', tooltip: 'Tambah Pendidikan', id: 'AddTB', handler: function() { CallWindowPendidikan({ PendidikanID: 0 }); }
			}, '-', {
				text: 'Ubah', width: 100, iconCls: 'editIcon', tooltip: 'Ubah Pendidikan', id: 'UpdateTB', handler: function() { PendidikanGrid.Update({ }); }
			}, '-', {
				text: 'Hapus', width: 100, iconCls: 'delIcon', tooltip: 'Hapus Pendidikan', id: 'DeleteTB', handler: function() {
					if (PendidikanGrid.getSelectionModel().getSelection().length == 0) {
						Ext.Msg.alert('Informasi', 'Silahkan memilih data.');
						return false;
					}
					
					Ext.MessageBox.confirm('Konfirmasi', 'Apa anda yakin akan menghapus data ini ?', PendidikanGrid.Delete);
				}
			}, '->', {
                id: 'SearchPM', xtype: 'textfield', tooltip: 'Cari Pendidikan', emptyText: 'Cari', width: 100, listeners: {
                    'specialKey': function(field, el) {
                        if (el.getKey() == Ext.EventObject.ENTER) {
                            var value = Ext.getCmp('SearchPM').getValue();
                            if ( value ) {
								PendidikanGrid.LoadGrid({ RequestName: 'Pendidikan', NameLike: value });
                            }
                        }
                    }
                }
            }, '-', {
				text: 'Reset', tooltip: 'Reset pencarian', iconCls: 'refreshIcon', width: 100, handler: function() {
					PendidikanGrid.LoadGrid({ RequestName: 'Pendidikan' });
				}
		} ],
		bbar: new Ext.PagingToolbar( {
			store: PendidikanStore, displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: 'No topics to display'
		} ),
		listeners: {
			'itemdblclick': function(model, records) {
				PendidikanGrid.Update({ });
            }
        },
		LoadGrid: function(Param) {
			PendidikanStore.proxy.extraParams = Param;
			PendidikanStore.load();
		},
		Update: function(Param) {
			var Data = PendidikanGrid.getSelectionModel().getSelection();
			if (Data.length == 0) {
				Ext.Msg.alert('Informasi', 'Silahkan memilih data.');
				return false;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/administrator/ajax',
				params: { Action: 'GetPendidikanByID', PendidikanID: Data[0].data.id },
				success: function(Result) {
					eval('var Record = ' + Result.responseText)
					Record.PendidikanID = Record.id;
					CallWindowPendidikan(Record);
				}
			});
		},
		Delete: function(Value) {
			if (Value == 'no') {
				return;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/administrator/ajax',
				params: { Action: 'DetelePendidikanByID', PendidikanID: PendidikanGrid.getSelectionModel().getSelection()[0].data.id },
				success: function(TempResult) {
					eval('var Result = ' + TempResult.responseText)
					
					Ext.Msg.alert('Informasi', Result.Message);
					if (Result.QueryStatus == '1') {
						PendidikanStore.load();
					}
				}
			});
		}
	});
	
	function CallWindowPendidikan(Pendidikan) {
		var WinPendidikan = new Ext.Window({
			layout: 'fit', width: 325, height: 100,
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Save', id: 'DeleteED', handler: function() { WinPendidikan.SavePendidikan(); }
				}, {	text: 'Close', handler: function() {
						WinPendidikan.hide();
				}
			}],
			listeners: {
				show: function(w) {
					var Title = (Pendidikan.PendidikanID == 0) ? 'Entry Pendidikan - [New]' : 'Entry Pendidikan - [Edit]';
					w.setTitle(Title);
					
					Ext.Ajax.request({
						url: Web.HOST + '/administrator/request/entry-pendidikan-popup',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							WinPendidikan.PendidikanID = Pendidikan.PendidikanID;
							WinPendidikan.pendidikan = new Ext.form.TextField({ renderTo: 'pendidikanED', width: 225, allowBlank: false, blankText: 'Masukkan Pendidikan' });
							
							// Populate Record
							if (Pendidikan.PendidikanID > 0) {
								WinPendidikan.pendidikan.setValue(Pendidikan.pendidikan);
							}
						}
					});
					
					if (! Renderer.AllowedWrite()) {
						Ext.getCmp('DeleteED').hide();
					}
				},
				hide: function(w) {
					w.destroy();
					w = WinPendidikan = null;
				}
			},
			SavePendidikan: function() {
				var Param = new Object();
				Param.Action = 'EditPendidikan';
				Param.PendidikanID = WinPendidikan.PendidikanID;
				Param.pendidikan = WinPendidikan.pendidikan.getValue().toUpperCase();
				
				// Validation
				var Validation = true;
				if (! WinPendidikan.pendidikan.validate()) {
					Validation = false;
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
							PendidikanStore.load();
							WinPendidikan.hide();
                        }
					}
				});
			}
		});
		WinPendidikan.show();
	}
	
	if (! Renderer.AllowedWrite()) {
		Ext.getCmp('AddTB').disable();
		Ext.getCmp('UpdateTB').disable();
		Ext.getCmp('DeleteTB').disable();
	}
	
	Renderer.InitWindowSize({ Panel: -1, Grid: PendidikanGrid, Toolbar: 70 });
});