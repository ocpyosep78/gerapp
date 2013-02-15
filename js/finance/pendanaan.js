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
        sorters: [{ property: 'pendanaan', direction: 'ASC' }],
		fields: [ 'pendanaan_id', 'sektor', 'jenis_biaya', 'metode_kirim', 'is_income', 'pendanaan', 'pendanaan_jumlah', 'pendanaan_tanggal' ],
		proxy: {
			type: 'ajax', extraParams: { },
			url : Web.HOST + '/finance/pendanaan/grid', actionMethods: { read: 'POST' },
			reader: { type: 'json', root: 'rows', totalProperty: 'totalCount' }
		},
		PendanaanJumlah: function(val, style, record) {
			var ClassColor = (record.data.is_income == 1) ? '#0000FF' : '#FF0000';
			var pendanaan_jumlah = Renderer.Money(record.data.pendanaan_jumlah);
			
			return '<span style="color: ' + ClassColor + ';">' + pendanaan_jumlah + '</span>';
		}
	});
    
	var Grid = Ext.create('Ext.grid.Panel', {
		viewConfig: { forceFit: true }, store: Store, height: 400, renderTo: Ext.get('grid-member'),
		features: [{ ftype: 'filters', encode: true, local: false }], layout: 'fit', title: 'Pendanaan',
		columns: [ {
				header: 'Pendanaan', dataIndex: 'pendanaan', sortable: true, filter: true, width: 200, flex: 1
		}, {	header: 'Sektor', dataIndex: 'sektor', sortable: true, filter: true, width: 100
		}, {	header: 'Jenis Biaya', dataIndex: 'jenis_biaya', sortable: true, filter: true, width: 150
		}, {	header: 'Metode Kirim', dataIndex: 'metode_kirim', sortable: true, filter: true, width: 150
		}, {	header: 'Jumlah', dataIndex: 'pendanaan_jumlah', sortable: true, filter: { type: 'numeric' }, width: 100, align: 'right', renderer: Store.PendanaanJumlah
		}, {	header: 'Tanggal', dataIndex: 'pendanaan_tanggal', sortable: true, filter: true, width: 100
		} ],
		tbar: [ {
				text: 'Tambah', iconCls: 'addIcon', tooltip: 'Tambah Pendanaan', id: 'AddTB', handler: function() { CallWin({ pendanaan_id: 0 }); }
			}, '-', {
				text: 'Ubah', iconCls: 'editIcon', tooltip: 'Ubah Pendanaan', id: 'UpdateTB', handler: function() { Grid.Update({ }); }
			}, '-', {
				text: 'Hapus', iconCls: 'delIcon', tooltip: 'Hapus Pendanaan', id: 'DeleteTB', handler: function() {
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
				url: Web.HOST + '/finance/pendanaan/action',
				params: { Action: 'GetPendanaanByID', pendanaan_id: Data[0].data.pendanaan_id },
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
				url: Web.HOST + '/finance/pendanaan/action',
				params: { Action: 'DetelePendanaanByID', pendanaan_id: Grid.getSelectionModel().getSelection()[0].data.pendanaan_id },
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
		var Win = new Ext.Window({
			layout: 'fit', width: 395, height: 245,
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Save', handler: function() { Win.Save(); }
				}, {	text: 'Close', handler: function() {
						Win.hide();
				}
			}],
			listeners: {
				show: function(w) {
					var Title = (Param.pendanaan_id == 0) ? 'Entry Pendanaan - [New]' : 'Entry Pendanaan - [Edit]';
					w.setTitle(Title);
					
					Ext.Ajax.request({
						url: Web.HOST + '/finance/pendanaan/view/',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							Win.pendanaan_id = Param.pendanaan_id;
							Win.sektor = Combo.Class.Sektor({ renderTo: 'sektorED' });
							Win.jenis_biaya = Combo.Class.JenisBiaya({ renderTo: 'jenis_biayaED', width: 245 });
							Win.metode_kirim = Combo.Class.MetodeKirim({ renderTo: 'metode_kirimED', width: 245 });
							Win.pendanaan = new Ext.form.TextField({ renderTo: 'pendanaanED', width: 245, allowBlank: false, blankText: 'Masukkan Keterangan Pendanaan' });
							Win.pendanaan_jumlah = new Ext.form.TextField({ renderTo: 'pendanaan_jumlahED', width: 245, allowBlank: false, blankText: 'Masukkan Jumlah Pendanaan' });
							Win.pendanaan_tanggal = new Ext.form.DateField({ renderTo: 'pendanaan_tanggalED', width: 125, format: DATE_FORMAT, allowBlank: false, blankText: 'Masukkan Tanggal Pendanaan' });
							Win.pendanaan.focus();
							
							if (Win.pendanaan_id > 0) {
								Win.sektor.setValue(Param.sektor_id);
								Win.jenis_biaya.setValue(Param.jenis_biaya_id);
								Win.metode_kirim.setValue(Param.metode_kirim_id);
								Win.pendanaan.setValue(Param.pendanaan);
								Win.pendanaan_jumlah.setValue(Param.pendanaan_jumlah);
								Win.pendanaan_tanggal.setValue(Param.pendanaan_tanggal);
							}
						}
					});
				},
				hide: function(w) {
					w.destroy();
					w = Win = null;
				}
			},
			Save: function() {
				var Param = new Object();
				Param.Action = 'UpdatePendanaan';
				Param.pendanaan_id = Win.pendanaan_id;
				Param.sektor_id = Win.sektor.getValue();
				Param.jenis_biaya_id = Win.jenis_biaya.getValue();
				Param.metode_kirim_id = Win.metode_kirim.getValue();
				Param.pendanaan = Win.pendanaan.getValue();
				Param.pendanaan_jumlah = Win.pendanaan_jumlah.getValue();
				Param.pendanaan_tanggal = Win.pendanaan_tanggal.getValue();
				
				// Validation
				var Validation = true;
				if (! Win.pendanaan.validate()) {
					Validation = false;
				}
				if (! Validation) {
					return;
				}
				
				Ext.Ajax.request({
					params: Param,
					url: Web.HOST + '/finance/pendanaan/action',
					success: function(TempResult) {
						eval('var Result = ' + TempResult.responseText);
						Renderer.FlashMessage(Result.Message);
                        
                        if (Result.QueryStatus == '1') {
							Store.load();
							Win.hide();
                        }
					}
				});
			}
		});
		Win.show();
	}
	
	Renderer.InitWindowSize({ Panel: -1, Grid: Grid, Toolbar: 70 });
    Ext.EventManager.onWindowResize(function() {
		Renderer.InitWindowSize({ Panel: -1, Grid: Grid, Toolbar: 70 });
    }, Grid);
});