Ext.Loader.setConfig({
	enabled: true,
    paths: {
        'Ext': Web.HOST + '/extjs/src',
        'Ext.ux': Web.HOST + '/extjs/examples/ux'
    }
});
Ext.require([ 'Ext.ux.grid.FiltersFeature' ]);

Ext.onReady(function() {
    Ext.QuickTips.init();
    Ext.get('loading_mask').destroy();
	
	var Store = Ext.create('Ext.data.Store', {
		autoLoad: true, pageSize: 25, remoteSort: true,
        sorters: [{ property: 'jenis_biaya', direction: 'ASC' }],
		fields: [ 'jenis_biaya_id', 'jenis_biaya', 'is_income' ],
		proxy: {
			type: 'ajax', extraParams: { },
			url : Web.HOST + '/finance/jenis_biaya/grid', actionMethods: { read: 'POST' },
			reader: { type: 'json', root: 'rows', totalProperty: 'totalCount' }
		}
	});
    
	var Grid = Ext.create('Ext.grid.Panel', {
		viewConfig: { forceFit: true }, store: Store, height: 400, renderTo: Ext.get('grid-member'),
		features: [{ ftype: 'filters', encode: true, local: false }], layout: 'fit', title: 'Jenis Biaya',
		columns: [ {
				header: 'Jenis Biaya', dataIndex: 'jenis_biaya', sortable: true, filter: true, width: 200, flex: 1
		}, {	header: 'Termasuk Pemasukan', dataIndex: 'is_income', sortable: true, width: 150, renderer: Renderer.YesNo
		} ],
		tbar: [ {
				text: 'Tambah', iconCls: 'addIcon', tooltip: 'Tambah Jenis Biaya', id: 'AddTB', handler: function() { CallWin({ jenis_biaya_id: 0 }); }
			}, '-', {
				text: 'Ubah', iconCls: 'editIcon', tooltip: 'Ubah Jenis Biaya', id: 'UpdateTB', handler: function() { Grid.Update({ }); }
			}, '-', {
				text: 'Hapus', iconCls: 'delIcon', tooltip: 'Hapus Jenis Biaya', id: 'DeleteTB', handler: function() {
					if (Grid.getSelectionModel().getSelection().length == 0) {
						Ext.Msg.alert('Information', 'Please choose record.');
						return false;
					}
					
					Ext.MessageBox.confirm('Confirmation', 'Are you sure ?', Grid.Delete);
				}
			}, '->', {
                id: 'SearchPM', xtype: 'textfield', emptyText: 'Search', width: 80, listeners: {
                    'specialKey': function(field, el) {
                        if (el.getKey() == Ext.EventObject.ENTER) {
                            var value = Ext.getCmp('SearchPM').getValue();
                            if ( value ) {
								var Param = Grid.GetParam();
								Grid.LoadGrid(Param);
                            }
                        }
                    }
                }
            }, '-', {
				text: 'Reset', tooltip: 'Reset search', iconCls: 'refreshIcon', handler: function() {
					Grid.LoadGrid({ Reset: 1 });
				}
		} ],
		bbar: new Ext.PagingToolbar( {
			store: Store, displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: 'No topics to display'
		} ),
		listeners: {
			'itemdblclick': function(model, records) {
				Grid.Update({ });
            }
        },
		GetParam: function() {
			var Param = { NameLike: Ext.getCmp('SearchPM').getValue() };
			
			return Param;
		},
		LoadGrid: function(Param) {
			Param.Reset = (Param.Reset == null) ? 0 : Param.Reset;
			
			if (Param.Reset == 1) {
				Store.proxy.extraParams = { }
			} else {
				Store.proxy.extraParams = Param;
			}
			
			Store.load();
		},
		Update: function(Param) {
			var Data = Grid.getSelectionModel().getSelection();
			if (Data.length == 0) {
				Ext.Msg.alert('Information', 'Please choose record.');
				return false;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/finance/jenis_biaya/action',
				params: { Action: 'GetJenisBiayaByID', jenis_biaya_id: Data[0].data.jenis_biaya_id },
				success: function(Result) {
					eval('var Record = ' + Result.responseText)
					CallWin(Record);
				}
			});
		},
		Delete: function(Value) {
			if (Value == 'no') {
				return;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/finance/jenis_biaya/action',
				params: { Action: 'DeteleJenisBiayaByID', jenis_biaya_id: Grid.getSelectionModel().getSelection()[0].data.jenis_biaya_id },
				success: function(TempResult) {
					eval('var Result = ' + TempResult.responseText)
					
					Renderer.FlashMessage(Result.Message);
					if (Result.QueryStatus == '1') {
						Store.load();
					}
				}
			});
		}
	});
	
	function CallWin(Param) {
		var WinBill = new Ext.Window({
			layout: 'fit', width: 370, height: 125,
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Save', handler: function() { WinBill.Save(); }
				}, {	text: 'Close', handler: function() {
						WinBill.hide();
				}
			}],
			listeners: {
				show: function(w) {
					var Title = (Param.jenis_biaya_id == 0) ? 'Entry Metode Pengiriman - [New]' : 'Entry Metode Pengiriman - [Edit]';
					w.setTitle(Title);
					
					Ext.Ajax.request({
						url: Web.HOST + '/finance/jenis_biaya/view/',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							WinBill.jenis_biaya_id = Param.jenis_biaya_id;
							WinBill.jenis_biaya = new Ext.form.TextField({ renderTo: 'jenis_biayaED', width: 215, allowBlank: false, blankText: 'Masukkan Metode Pengiriman' });
							WinBill.is_income = new Ext.form.Checkbox({ renderTo: 'is_incomeED' });
							WinBill.jenis_biaya.focus();
							
							if (WinBill.jenis_biaya_id > 0) {
								WinBill.jenis_biaya.setValue(Param.jenis_biaya);
								WinBill.is_income.setValue(Param.is_income);
							}
						}
					});
				},
				hide: function(w) {
					w.destroy();
					w = WinBill = null;
				}
			},
			Save: function() {
				var Param = new Object();
				Param.Action = 'UpdateJenisBiaya';
				Param.jenis_biaya_id = WinBill.jenis_biaya_id;
				Param.jenis_biaya = WinBill.jenis_biaya.getValue();
				Param.is_income = (WinBill.is_income.getValue()) ? 1 : 0;
				
				// Validation
				var Validation = true;
				if (! WinBill.jenis_biaya.validate()) {
					Validation = false;
				}
				if (! Validation) {
					return;
				}
				
				Ext.Ajax.request({
					params: Param,
					url: Web.HOST + '/finance/jenis_biaya/action',
					success: function(TempResult) {
						eval('var Result = ' + TempResult.responseText);
						Renderer.FlashMessage(Result.Message);
                        
                        if (Result.QueryStatus == '1') {
							Store.load();
							WinBill.hide();
                        }
					}
				});
			}
		});
		WinBill.show();
	}
	
	Renderer.InitWindowSize({ Panel: -1, Grid: Grid, Toolbar: 70 });
    Ext.EventManager.onWindowResize(function() {
		Renderer.InitWindowSize({ Panel: -1, Grid: Grid, Toolbar: 70 });
    }, Grid);
});