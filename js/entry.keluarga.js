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
	if (! Renderer.AllowedAccess('KeluargaRead')) {
		return;
	}
	
	var GetAccessGerejaID = Ext.get('AccessGerejaID').getValue();
	
	var KeluargaStore = Ext.create('Ext.data.Store', {
		autoLoad: true, pageSize: 25, remoteSort: true,
        sorters: [{ property: 'nama', direction: 'ASC' }],
		fields: [ 'id', 'nama', 'alamat', 'nomor', 'no_kk', 'no_hp', 'gereja', 'sektor', 'ultah_perkawinan', 'meninggal' ],
		storeId: 'KeluargaStore',
		proxy: {
			type: 'ajax', extraParams: { RequestName: 'Keluarga', idgereja: GetAccessGerejaID },
			url : Web.HOST + '/administrator/grid',
			reader: { root: 'KeluargaData', totalProperty: 'KeluargaCount' }
		}
	});
	
	var KeluargaGrid = new Ext.grid.GridPanel({
		title: 'Manajemen Keluarga Jemaat<br />Daftar keluarga dari jemaat gereja',
		viewConfig: { forceFit: true }, store: KeluargaStore, height: 335, renderTo: 'grid-member',
		features: [{ ftype: 'filters', encode: true, local: false }],
		columns: [ {
					header: 'No KK', dataIndex: 'no_kk', sortable: true, filter: true, width: 150
			}, {	header: 'No HP', dataIndex: 'no_hp', sortable: true, filter: true, width: 150
			}, {	header: 'Nama', dataIndex: 'nama', sortable: true, filter: true, width: 150, flex: 1
			}, {	header: 'Alamat', dataIndex: 'alamat', sortable: true, filter: true, width: 150
			}, {	header: 'Nomor Anggota', dataIndex: 'nomor', sortable: true, filter: true, width: 100
			}, {	header: 'Sektor', dataIndex: 'sektor', sortable: true, filter: true, width: 100
			}, {	header: 'Gereja', dataIndex: 'gereja', sortable: true, filter: true, width: 100
			}, {	header: 'Ultah Perkawinan', dataIndex: 'ultah_perkawinan', sortable: true, filter: true, width: 125, renderer: Ext.util.Format.dateRenderer(DATE_FORMAT)
			}, {	header: 'Meninggal', dataIndex: 'meninggal', sortable: true, filter: { type: 'list', options: [] }, width: 125, renderer: Renderer.RendererSudahBelum
			}, {	header: 'Cetak', xtype: 'actioncolumn', width: 75, align: 'center', items: [ {
					iconCls: 'printIcon spaceIcon', tooltip: 'Cetak', handler: function(grid, rowIndex, colIndex) {
						var rec = KeluargaStore.getAt(rowIndex);
						window.open(Web.HOST + '/keluarga/member/?KeluargaID=' + rec.data.id);
					}
			} ]
		} ],
		tbar: [ {
				text: 'Tambah', width: 100, iconCls: 'addIcon', tooltip: 'Tambah keluarga', id: 'AddTB', handler: function() { CallWindowKeluarga({ KeluargaID: 0, CallBack: function() { Ext.getStore('KeluargaStore').load(); } }); }
			}, '-', {
				text: 'Ubah', width: 100, iconCls: 'editIcon', tooltip: 'Ubah keluarga', id: 'UpdateTB', handler: function() { KeluargaGrid.Update({ }); }
			}, '-', {
				text: 'Hapus', width: 100, iconCls: 'delIcon', tooltip: 'Hapus keluarga', id: 'DeleteTB', handler: function() {
					if (KeluargaGrid.getSelectionModel().getSelection().length == 0) {
						Ext.Msg.alert('Informasi', 'Silahkan memilih data.');
						return false;
					}
					
					Ext.MessageBox.confirm('Konfirmasi', 'Apa anda yakin akan menghapus data ini ?', KeluargaGrid.Delete);
				}
			}, '->', {
                id: 'SearchPM', xtype: 'textfield', tooltip: 'Cari keluarga', emptyText: 'Cari', width: 100, listeners: {
                    'specialKey': function(field, el) {
                        if (el.getKey() == Ext.EventObject.ENTER) {
                            var value = Ext.getCmp('SearchPM').getValue();
                            if ( value ) {
								KeluargaGrid.LoadGrid({ RequestName: 'Keluarga', idgereja: GetAccessGerejaID, NameLike: value });
                            }
                        }
                    }
                }
            }, '-', {
				text: 'Reset', tooltip: 'Reset pencarian', iconCls: 'refreshIcon', width: 100, handler: function() {
					KeluargaGrid.LoadGrid({ RequestName: 'Keluarga', idgereja: GetAccessGerejaID });
				}
		} ],
		bbar: new Ext.PagingToolbar( {
			store: KeluargaStore, displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: 'No topics to display'
		} ),
		listeners: {
			'itemdblclick': function(model, records) {
				KeluargaGrid.Update({ });
            }
        },
		LoadGrid: function(Param) {
			KeluargaStore.proxy.extraParams = Param;
			KeluargaStore.load();
		},
		Update: function(Param) {
			var Data = KeluargaGrid.getSelectionModel().getSelection();
			if (Data.length == 0) {
				Ext.Msg.alert('Informasi', 'Silahkan memilih data.');
				return false;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/administrator/ajax',
				params: { Action: 'GetKeluargaByID', KeluargaID: Data[0].data.id },
				success: function(Result) {
					eval('var Record = ' + Result.responseText)
					Record.KeluargaID = Record.id;
					Record.CallBack = function() { Ext.getStore('KeluargaStore').load(); }
					CallWindowKeluarga(Record);
				}
			});
		},
		Delete: function(Value) {
			if (Value == 'no') {
				return;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/administrator/ajax',
				params: { Action: 'DeteleKeluargaByID', KeluargaID: KeluargaGrid.getSelectionModel().getSelection()[0].data.id },
				success: function(TempResult) {
					eval('var Result = ' + TempResult.responseText)
					
					Ext.Msg.alert('Informasi', Result.Message);
					if (Result.QueryStatus == '1') {
						KeluargaStore.load();
					}
				}
			});
		}
	});
	
	if (! Renderer.AllowedAccess('KeluargaWrite')) {
		Ext.getCmp('AddTB').disable();
		Ext.getCmp('UpdateTB').disable();
		Ext.getCmp('DeleteTB').disable();
	}
	
	Renderer.InitWindowSize({ Panel: -1, Grid: KeluargaGrid, Toolbar: 70 });
});