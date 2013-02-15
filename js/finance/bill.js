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
	var AccessGerejaID = Ext.get('AccessGerejaID').getValue();
	
	var BillStore = Ext.create('Ext.data.Store', {
		autoLoad: true, pageSize: 25, remoteSort: true,
        sorters: [{ property: 'jemaat_nama', direction: 'ASC' }],
		fields: [ 'jemaat_id', 'tagihan_type_id', 'tagihan_type', 'jemaat_nama', '', 'tagihan_nilai', 'tagihan_bayar', 'record_count' ],
		proxy: {
			type: 'ajax', extraParams: { },
			url : Web.HOST + '/finance/bill/grid', actionMethods: { read: 'POST' },
			reader: { type: 'json', root: 'rows', totalProperty: 'totalCount' }
		},
		TagihanNilai: function(value, cls, record) {
			var value = record.data.tagihan_nilai - record.data.tagihan_bayar;
			return Renderer.Money(value);
		}
	});
    
	var BillGrid = Ext.create('Ext.grid.Panel', {
		viewConfig: { forceFit: true }, store: BillStore, height: 400, renderTo: Ext.get('grid-member'),
		features: [{ ftype: 'filters', encode: true, local: false }], layout: 'fit', title: 'Tagihan',
		columns: [ {
				header: 'Nama Jemaat', dataIndex: 'jemaat_nama', sortable: true, filter: true, width: 200, flex: 1
		}, {	header: 'Jenis Tagihan', dataIndex: 'tagihan_type', sortable: true, filter: true, width: 200
		}, {	header: 'Nilai', dataIndex: 'tagihan_nilai', sortable: true, filter: true, width: 200, align: 'right', renderer: BillStore.TagihanNilai
		} ],
		tbar: [ {
				text: 'Tambah', iconCls: 'addIcon', tooltip: 'Tambah Tagihan', id: 'AddTB', handler: function() { CallWinTagihan({ tagihan_id: 0 }); }
			}, '-', {
				text: 'Bayar', iconCls: 'editIcon', tooltip: 'Bayar Tagihan', id: 'UpdateTB', handler: function() { BillGrid.Update({ }); }
			}, '->', {
                id: 'SearchPM', xtype: 'textfield', emptyText: 'Search', width: 80, listeners: {
                    'specialKey': function(field, el) {
                        if (el.getKey() == Ext.EventObject.ENTER) {
                            var value = Ext.getCmp('SearchPM').getValue();
                            if ( value ) {
								var Param = BillGrid.GetParam();
								BillGrid.LoadGrid(Param);
                            }
                        }
                    }
                }
            }, '-', {
				text: 'Reset', tooltip: 'Reset search', iconCls: 'refreshIcon', handler: function() {
					BillGrid.LoadGrid({ Reset: 1 });
				}
		} ],
		bbar: new Ext.PagingToolbar( {
			store: BillStore, displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: 'No topics to display'
		} ),
		listeners: {
			'itemdblclick': function(model, records) {
				BillGrid.Update({ });
            }
        },
		GetParam: function() {
			var Param = { NameLike: Ext.getCmp('SearchPM').getValue() };
			
			return Param;
		},
		LoadGrid: function(Param) {
			Param.Reset = (Param.Reset == null) ? 0 : Param.Reset;
			
			if (Param.Reset == 1) {
				BillStore.proxy.extraParams = { }
			} else {
				BillStore.proxy.extraParams = Param;
			}
			
			BillStore.load();
		},
		Update: function(Param) {
			var Data = BillGrid.getSelectionModel().getSelection();
			if (Data.length == 0) {
				Ext.Msg.alert('Information', 'Please choose record.');
				return false;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/finance/bill/action',
				params: { Action: 'GetBillByID', jemaat_id: Data[0].data.jemaat_id, tagihan_type_id: Data[0].data.tagihan_type_id },
				success: function(Result) {
					eval('var Record = ' + Result.responseText)
					CallWinPayment(Record);
				}
			});
		}
	});
	
	function CallWinTagihan(Param) {
		var WinBill = new Ext.Window({
			layout: 'fit', width: 370, height: 275,
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Save', handler: function() { WinBill.Save(); }
				}, {	text: 'Close', handler: function() {
						WinBill.hide();
				}
			}],
			listeners: {
				show: function(w) {
					var Title = (Param.tagihan_id == 0) ? 'Entry Tagihan - [New]' : 'Entry Tagihan - [Edit]';
					w.setTitle(Title);
					
					Ext.Ajax.request({
						url: Web.HOST + '/finance/bill/view/',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							WinBill.tagihan_id = Param.tagihan_id;
							WinBill.gereja = Combo.Class.Gereja({ renderTo: 'gerejaED', width: 240, allowBlank: false, blankText: 'Masukkan Gereja' });
							WinBill.sasaran_jemaat = Combo.Class.SasaranJemaat({
								renderTo: 'sasaran_jemaatED', width: 240,
								allowBlank: false, blankText: 'Masukkan Sasaran Jemaat',
								listeners: { select: function() {
									WinBill.jemaat.reset();
									var val = WinBill.sasaran_jemaat.getValue();
									if (val == 1) {
										WinBill.jemaat.setDisabled(true);
									} else {
										WinBill.jemaat.setDisabled(false);
									}
								} }
							});
							WinBill.jemaat = Combo.Class.Jemaat({
								renderTo: 'jemaatED', width: 240, listeners: {
									beforequery: function(queryEvent, eOpts) {
										var gereja_id = WinBill.gereja.getValue();
										if (gereja_id == null) {
											WinBill.gereja.validate();
											return false;
										}
										
										queryEvent.combo.store.proxy.extraParams.gereja_id = gereja_id;
										queryEvent.combo.store.load();
									}
								}
							});
							WinBill.tagihan_tanggal = new Ext.form.DateField({ renderTo: 'tagihan_tanggalED', width: 125, format: DATE_FORMAT, allowBlank: false, blankText: 'Masukkan Tanggal', value: new Date() });
							WinBill.tagihan_type = Combo.Class.TagihanType({
								renderTo: 'tagihan_type_ED', width: 240, allowBlank: false, blankText: 'Masukkan Jenis Tagihan',
								listeners: { select: function(combo, record, eOpts) {
									var val = record[0].data.tagihan_config;
									if (val == 1) {
										WinBill.tagihan_nilai.setDisabled(true);
									} else {
										WinBill.tagihan_nilai.setDisabled(false);
									}
								} }
							});
							WinBill.tagihan_note = new Ext.form.TextField({ renderTo: 'tagihan_noteED', width: 240 });
							WinBill.tagihan_nilai = new Ext.form.TextField({ renderTo: 'tagihan_nilaiED', width: 240 });
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
				Param.Action = 'UpdateBill';
				Param.tagihan_id = WinBill.tagihan_id;
				Param.gereja_id = WinBill.gereja.getValue();
				Param.sasaran_jemaat = WinBill.sasaran_jemaat.getValue();
				Param.jemaat_id = WinBill.jemaat.getValue();
				Param.tagihan_type_id = WinBill.tagihan_type.getValue();
				Param.tagihan_tanggal = WinBill.tagihan_tanggal.getValue();
				Param.tagihan_note = WinBill.tagihan_note.getValue();
				Param.tagihan_nilai = Func.IsEmpty(WinBill.tagihan_nilai.getValue()) ? 0 : WinBill.tagihan_nilai.getValue();
				
				var tagihan_config = 0;
				if (Param.tagihan_type_id != null) {
					var Record = WinBill.tagihan_type.store.findRecord('tagihan_type_id', Param.tagihan_type_id).data;
					tagihan_config = Record.tagihan_type_id;
				}
				
				// Validation
				var Validation = true;
				if (! WinBill.gereja.validate()) {
					Validation = false;
				}
				if (! WinBill.sasaran_jemaat.validate()) {
					Validation = false;
				}
				if (! WinBill.tagihan_type.validate()) {
					Validation = false;
				}
				if (! WinBill.tagihan_tanggal.validate()) {
					Validation = false;
				}
				if (tagihan_config == 0 && Param.tagihan_nilai == 0) {
					Validation = false;
					WinBill.jemaat.markInvalid('Masukkan Nilai Tagihan');
				}
				if (Param.sasaran_jemaat == 2 && Param.jemaat_id == null) {
					Validation = false;
					WinBill.jemaat.markInvalid('Masukkan Nama Jemaat');
				}
				if (! Validation) {
					return;
				}
				
				Param.tagihan_tanggal = Renderer.DateFormat(Param.tagihan_tanggal);
				
				Ext.Ajax.request({
					params: Param,
					url: Web.HOST + '/finance/bill/action',
					success: function(TempResult) {
						eval('var Result = ' + TempResult.responseText);
						Renderer.FlashMessage(Result.Message);
                        
                        if (Result.QueryStatus == '1') {
							BillStore.load();
							WinBill.hide();
                        }
					}
				});
			}
		});
		WinBill.show();
	}
	
	function CallWinPayment(Param) {
		var WinBill = new Ext.Window({
			layout: 'fit', width: 370, height: 215, title: 'Pembayaran',
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Bayar', handler: function() { WinBill.Save(); }
				}, {	text: 'Close', handler: function() {
						WinBill.hide();
				}
			}],
			listeners: {
				show: function(w) {
					Ext.Ajax.request({
						url: Web.HOST + '/finance/bill/view/payment',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							WinBill.jemaat_id = Param.jemaat_id;
							WinBill.tagihan_type_id = Param.tagihan_type_id;
							WinBill.tagihan_type = new Ext.form.TextField({ renderTo: 'tagihan_typeED', width: 240, readOnly: true, value: Param.tagihan_type });
							WinBill.tagihan_count = new Ext.form.TextField({ renderTo: 'tagihan_countED', width: 240, readOnly: true, value: Param.tagihan_count + ' kali' });
							WinBill.jemaat_nama = new Ext.form.TextField({ renderTo: 'jemaat_namaED', width: 240, readOnly: true, value: Param.jemaat_nama });
							WinBill.tagihan_nilai = new Ext.form.TextField({ renderTo: 'tagihan_nilaiED', width: 240, readOnly: true, value: Param.tagihan_nilai });
							WinBill.tagihan_bayar = new Ext.form.TextField({ renderTo: 'tagihan_bayarED', width: 240, allowBlank: false, blankText: 'Masukkan Nilai Bayar' });
							WinBill.tagihan_bayar.focus();
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
				Param.Action = 'UpdatePayment';
				Param.jemaat_id = WinBill.jemaat_id;
				Param.tagihan_type_id = WinBill.tagihan_type_id;
				Param.tagihan_bayar = WinBill.tagihan_bayar.getValue();
				
				// Validation
				var Validation = true;
				if (! WinBill.tagihan_bayar.validate()) {
					Validation = false;
				}
				if (! Validation) {
					return;
				}
				
				Ext.Ajax.request({
					params: Param,
					url: Web.HOST + '/finance/bill/action',
					success: function(TempResult) {
						eval('var Result = ' + TempResult.responseText);
						Renderer.FlashMessage(Result.Message);
                        
                        if (Result.QueryStatus == '1') {
							BillStore.load();
							WinBill.hide();
                        }
					}
				});
			}
		});
		WinBill.show();
	}
	
	Renderer.InitWindowSize({ Panel: -1, Grid: BillGrid, Toolbar: 70 });
    Ext.EventManager.onWindowResize(function() {
		Renderer.InitWindowSize({ Panel: -1, Grid: BillGrid, Toolbar: 70 });
    }, BillGrid);
});