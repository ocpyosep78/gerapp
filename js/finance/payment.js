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
	
	var PaymentStore = Ext.create('Ext.data.Store', {
		autoLoad: true, pageSize: 25, remoteSort: true,
        sorters: [{ property: 'jemaat_nama', direction: 'ASC' }],
		fields: [ 'tagihan_id', 'jemaat_id', 'tagihan_type_id', 'jemaat_nama', 'tagihan_type', 'tagihan_tanggal', 'tagihan_note', 'tagihan_nilai', 'tagihan_bayar' ],
		proxy: {
			type: 'ajax', extraParams: { },
			url : Web.HOST + '/finance/payment/grid', actionMethods: { read: 'POST' },
			reader: { type: 'json', root: 'rows', totalProperty: 'totalCount' }
		}
	});
    
	var PaymentGrid = Ext.create('Ext.grid.Panel', {
		viewConfig: { forceFit: true }, store: PaymentStore, height: 400, renderTo: Ext.get('grid-member'),
		features: [{ ftype: 'filters', encode: true, local: false }], layout: 'fit', title: 'Pembayaran',
		columns: [ {
				header: 'Nama Jemaat', dataIndex: 'jemaat_nama', sortable: true, filter: true, width: 200, flex: 1
		}, {	header: 'Jenis Tagihan', dataIndex: 'tagihan_type', sortable: true, filter: true, width: 200
		}, {	header: 'Tanggal', dataIndex: 'tagihan_tanggal', sortable: true, filter: true, width: 200, renderer: Ext.util.Format.dateRenderer(DATE_FORMAT)
		}, {	header: 'Catatan', dataIndex: 'tagihan_note', sortable: true, filter: true, width: 200
		}, {	header: 'Nilai', dataIndex: 'tagihan_nilai', sortable: true, filter: true, width: 200, align: 'right', renderer: Renderer.Money
		}, {	header: 'Terbayar', dataIndex: 'tagihan_bayar', sortable: true, filter: true, width: 200, align: 'right', renderer: Renderer.Money
		} ],
		tbar: [
			'->', {
                id: 'SearchPM', xtype: 'textfield', emptyText: 'Search', width: 80, listeners: {
                    'specialKey': function(field, el) {
                        if (el.getKey() == Ext.EventObject.ENTER) {
                            var value = Ext.getCmp('SearchPM').getValue();
                            if ( value ) {
								var Param = PaymentGrid.GetParam();
								PaymentGrid.LoadGrid(Param);
                            }
                        }
                    }
                }
            }, '-', {
				text: 'Reset', tooltip: 'Reset search', iconCls: 'refreshIcon', handler: function() {
					PaymentGrid.LoadGrid({ Reset: 1 });
				}
		} ],
		bbar: new Ext.PagingToolbar( {
			store: PaymentStore, displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: 'No topics to display'
		} ),
		GetParam: function() {
			var Param = { NameLike: Ext.getCmp('SearchPM').getValue() };
			
			return Param;
		},
		LoadGrid: function(Param) {
			Param.Reset = (Param.Reset == null) ? 0 : Param.Reset;
			
			if (Param.Reset == 1) {
				PaymentStore.proxy.extraParams = { }
			} else {
				PaymentStore.proxy.extraParams = Param;
			}
			
			PaymentStore.load();
		}
	});
	
	Renderer.InitWindowSize({ Panel: -1, Grid: PaymentGrid, Toolbar: 70 });
    Ext.EventManager.onWindowResize(function() {
		Renderer.InitWindowSize({ Panel: -1, Grid: PaymentGrid, Toolbar: 70 });
    }, PaymentGrid);
});