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
	
	Ext.define('ProfesiModel', {
		extend: 'Ext.data.Model',
		fields: [
			{ name: 'id', type: 'int' },
			{ name: 'profesi', type: 'string' }
		]
	});
	var ProfesiStore = Ext.create('Ext.data.Store', {
		model: 'ProfesiModel', autoLoad: true, pageSize: 25, remoteSort: true,
        sorters: [{ property: 'profesi', direction: 'ASC' }],
		proxy: {
			type: 'ajax', extraParams: { RequestName: 'Profesi' },
			url : Web.HOST + '/administrator/grid',
			reader: { root: 'ProfesiData', totalProperty: 'ProfesiCount' }
		}
	});
    
	var ProfesiGrid = new Ext.grid.GridPanel({
		viewConfig: { forceFit: true }, store: ProfesiStore, height: 335, renderTo: 'grid-member',
		features: [{ ftype: 'filters', encode: true, local: false }],
		columns: [ {
					header: 'Profesi', dataIndex: 'profesi', sortable: true, filter: true, width: 600, flex: 1
		} ],
		tbar: [ {
				text: 'Tambah', width: 100, iconCls: 'addIcon', tooltip: 'Tambah Profesi', id: 'AddTB', handler: function() { CallWindowProfesi({ ProfesiID: 0 }); }
			}, '-', {
				text: 'Ubah', width: 100, iconCls: 'editIcon', tooltip: 'Ubah Profesi', id: 'UpdateTB', handler: function() { ProfesiGrid.Update({ }); }
			}, '-', {
				text: 'Hapus', width: 100, iconCls: 'delIcon', tooltip: 'Hapus Profesi', id: 'DeleteTB', handler: function() {
					if (ProfesiGrid.getSelectionModel().getSelection().length == 0) {
						Ext.Msg.alert('Informasi', 'Silahkan memilih data.');
						return false;
					}
					
					Ext.MessageBox.confirm('Konfirmasi', 'Apa anda yakin akan menghapus data ini ?', ProfesiGrid.Delete);
				}
			}, '->', {
                id: 'SearchPM', xtype: 'textfield', tooltip: 'Cari Profesi', emptyText: 'Cari', width: 100, listeners: {
                    'specialKey': function(field, el) {
                        if (el.getKey() == Ext.EventObject.ENTER) {
                            var value = Ext.getCmp('SearchPM').getValue();
                            if ( value ) {
								ProfesiGrid.LoadGrid({ RequestName: 'Profesi', NameLike: value });
                            }
                        }
                    }
                }
            }, '-', {
				text: 'Reset', tooltip: 'Reset pencarian', iconCls: 'refreshIcon', width: 100, handler: function() {
					ProfesiGrid.LoadGrid({ RequestName: 'Profesi' });
				}
		} ],
		bbar: new Ext.PagingToolbar( {
			store: ProfesiStore, displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: 'No topics to display'
		} ),
		listeners: {
			'itemdblclick': function(model, records) {
				ProfesiGrid.Update({ });
            }
        },
		LoadGrid: function(Param) {
			ProfesiStore.proxy.extraParams = Param;
			ProfesiStore.load();
		},
		Update: function(Param) {
			var Data = ProfesiGrid.getSelectionModel().getSelection();
			if (Data.length == 0) {
				Ext.Msg.alert('Informasi', 'Silahkan memilih data.');
				return false;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/administrator/ajax',
				params: { Action: 'GetProfesiByID', ProfesiID: Data[0].data.id },
				success: function(Result) {
					eval('var Record = ' + Result.responseText)
					Record.ProfesiID = Record.id;
					CallWindowProfesi(Record);
				}
			});
		},
		Delete: function(Value) {
			if (Value == 'no') {
				return;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/administrator/ajax',
				params: { Action: 'DeteleProfesiByID', ProfesiID: ProfesiGrid.getSelectionModel().getSelection()[0].data.id },
				success: function(TempResult) {
					eval('var Result = ' + TempResult.responseText)
					
					Ext.Msg.alert('Informasi', Result.Message);
					if (Result.QueryStatus == '1') {
						ProfesiStore.load();
					}
				}
			});
		}
	});
	
	function CallWindowProfesi(Profesi) {
		var WinProfesi = new Ext.Window({
			layout: 'fit', width: 325, height: 100,
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Save', id: 'DeleteED', handler: function() { WinProfesi.SaveProfesi(); }
				}, {	text: 'Close', handler: function() {
						WinProfesi.hide();
				}
			}],
			listeners: {
				show: function(w) {
					var Title = (Profesi.ProfesiID == 0) ? 'Entry Profesi - [New]' : 'Entry Profesi - [Edit]';
					w.setTitle(Title);
					
					Ext.Ajax.request({
						url: Web.HOST + '/administrator/request/entry-profesi-popup',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							WinProfesi.ProfesiID = Profesi.ProfesiID;
							WinProfesi.profesi = new Ext.form.TextField({ renderTo: 'profesiED', width: 225, allowBlank: false, blankText: 'Masukkan Profesi' });
							
							// Populate Record
							if (Profesi.ProfesiID > 0) {
								WinProfesi.profesi.setValue(Profesi.profesi);
							}
						}
					});
					
					if (! Renderer.AllowedWrite()) {
						Ext.getCmp('DeleteED').hide();
					}
				},
				hide: function(w) {
					w.destroy();
					w = WinProfesi = null;
				}
			},
			SaveProfesi: function() {
				var Param = new Object();
				Param.Action = 'EditProfesi';
				Param.ProfesiID = WinProfesi.ProfesiID;
				Param.profesi = WinProfesi.profesi.getValue().toUpperCase();
				
				// Validation
				var Validation = true;
				if (! WinProfesi.profesi.validate()) {
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
							ProfesiStore.load();
							WinProfesi.hide();
                        }
					}
				});
			}
		});
		WinProfesi.show();
	}
	
	if (! Renderer.AllowedWrite()) {
		Ext.getCmp('AddTB').disable();
		Ext.getCmp('UpdateTB').disable();
		Ext.getCmp('DeleteTB').disable();
	}
	
	Renderer.InitWindowSize({ Panel: -1, Grid: ProfesiGrid, Toolbar: 70 });
});