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
	
	// Default Record;
	var DefaultRecord = function() {
		var Data = {
			id: 0, JemaatID: 0, nama: '', nomor: 'xx-xx-xxxxx-xx', idkeluarga: '',
			tgllahir: '', tempatlahir: '', golongandarah: '', tanggaldaftar: Renderer.DateFormat(new Date()),
			alamat: '', rtrw: '', kelurahan: '', kecamatan: '',
			kodepos: '', telpon: '', hp: '', catatan: '',
			status: '', meninggal: '', firstname: '', lastname: '',
			email: '', sex: '', profesi: '', institusi: '',
			jabatan: '', statusbaptis: '', tanggalbaptis: '', statussidi: '',
			tanggalsidi: '', statusnikah: '', tanggalnikah: '', tempatpemberkatan: '',
			pendidikan: '', gelar: '', jurusan: '', hubungankeluarga: '',
			gerejaED: '', lengkap: '', kota: '', propinsi: '', negara: '',
			foto: '', FotoLink: Web.HOST + '/images/default-image.png'
		}
		return Data;
	}
	var GetAccessGerejaID = Ext.get('AccessGerejaID').getValue();
	
	// Load Store
	Ext.getStore('SexStore').load();
	Ext.getStore('StatusStore').load();
	Ext.getStore('GolonganDarahStore').load();
	Ext.getStore('HubunganKeluargaStore').load();
	
	Ext.define('JemaatModel', {
		extend: 'Ext.data.Model',
		fields: [
			{ name: 'id', type: 'int' },
			{ name: 'nama', type: 'string' },
			{ name: 'nomor', type: 'string' },
			{ name: 'idkeluarga', type: 'int' },
			{ name: 'tgllahir', type: 'date' },
			{ name: 'tempatlahir', type: 'string' },
			{ name: 'golongandarah', type: 'string' },
			{ name: 'tanggaldaftar', type: 'date' },
			{ name: 'alamat', type: 'string' },
			{ name: 'rtrw', type: 'string' },
			{ name: 'kelurahan', type: 'string' },
			{ name: 'kecamatan', type: 'string' },
			{ name: 'kota', type: 'string' },
			{ name: 'propinsi', type: 'string' },
			{ name: 'negara', type: 'string' },
			{ name: 'kodepos', type: 'int' },
			{ name: 'telpon', type: 'string' },
			{ name: 'hp', type: 'string' },
			{ name: 'catatan', type: 'string' },
			{ name: 'status', type: 'string' },
			{ name: 'meninggal', type: 'string' },
			{ name: 'firstname', type: 'string' },
			{ name: 'lastname', type: 'string' },
			{ name: 'email', type: 'string' },
			{ name: 'sex', type: 'string' },
			{ name: 'sex_desc', type: 'string' },
			{ name: 'persekutuan', type: 'string' },
			{ name: 'profesi', type: 'string' },
			{ name: 'institusi', type: 'string' },
			{ name: 'jabatan', type: 'string' },
			{ name: 'statusbaptis', type: 'string' },
			{ name: 'tanggalbaptis', type: 'date' },
			{ name: 'statussidi', type: 'string' },
			{ name: 'tanggalsidi', type: 'date' },
			{ name: 'statusnikah', type: 'string' },
			{ name: 'tanggalnikah', type: 'date' },
			{ name: 'tempatpemberkatan', type: 'string' },
			{ name: 'pendidikan', type: 'string' },
			{ name: 'gelar', type: 'string' },
			{ name: 'jurusan', type: 'string' },
			{ name: 'hubungankeluarga', type: 'string' },
			{ name: 'gereja', type: 'string' },
			{ name: 'idgereja', type: 'int' },
			{ name: 'lengkap', type: 'int' },
			{ name: 'sektor', type: 'string' },
			{ name: 'GerejaNama', type: 'string' },
			{ name: 'tgl_meninggal', type: 'date' },
			{ name: 'tempat_makam', type: 'string' },
			{ name: 'tempat_baptis', type: 'string' },
			{ name: 'tempat_sidi', type: 'string' },
			{ name: 'atestesi_dari', type: 'string' },
			{ name: 'atestesi_ke', type: 'string' }
		]
	});
	var JemaatStore = Ext.create('Ext.data.Store', {
		model: 'JemaatModel', autoLoad: true, pageSize: 25, remoteSort: true,
        sorters: [{ property: 'TanggalDaftar', direction: 'DESC' }],
		proxy: {
			type: 'ajax', extraParams: { RequestName: 'Jemaat', idgereja: GetAccessGerejaID },
			url : Web.HOST + '/administrator/grid',
			reader: { root: 'JemaatData', totalProperty: 'JemaatCount' }
		}
	});
    
	var JemaatGrid = new Ext.grid.GridPanel({
		title: 'Jemaat<br />Silahkan mengedit data jemaat dengan memilih record yang ada',
		viewConfig: { forceFit: true }, store: JemaatStore, height: 350,
		Record: DefaultRecord(), renderTo: 'grid-member',
		features: [{ ftype: 'filters', encode: true, local: false }],
		columns: [ {
					header: 'Nama', dataIndex: 'nama', sortable: true, filter: true, width: 150
			}, {	header: 'Nomor', dataIndex: 'nomor', sortable: true, filter: true, width: 150
			}, {	header: 'Tempat Lahir', dataIndex: 'tempatlahir', sortable: true, filter: true, width: 90
			}, {	header: 'Tanggal Lahir', dataIndex: 'tgllahir', sortable: true, filter: true, width: 80, renderer: Ext.util.Format.dateRenderer(DATE_FORMAT)
			}, {	header: 'Gol Darah', dataIndex: 'golongandarah', sortable: true, filter: true, width: 80
			}, {	header: 'Tanggal Daftar', dataIndex: 'tanggaldaftar', sortable: true, filter: true, width: 80, renderer: Ext.util.Format.dateRenderer(DATE_FORMAT)
			}, {	header: 'Kelurahan', dataIndex: 'kelurahan', sortable: true, filter: true, width: 100
			}, {	header: 'Kecamatan', dataIndex: 'kecamatan', sortable: true, filter: true, width: 100
			}, {	header: 'Jenis Kelamin', dataIndex: 'sex_desc', sortable: true, filter: { type: 'list', options: ['Laki Laki', 'Perempuan'] }, width: 100
			}, {	header: 'Sektor', dataIndex: 'sektor', sortable: false, filter: true, width: 175
			}, {	header: 'Pelayanan Kategorial', dataIndex: 'persekutuan', sortable: false, filter: { type: 'list', options: [] }, width: 175
			}, {	header: 'Ulang Tahun', dataIndex: 'tgllahir', sortable: true, filter: true, width: 80, renderer: Ext.util.Format.dateRenderer('j F')
//			}, {	header: 'Gereja', dataIndex: 'GerejaNama', sortable: true, filter: true, width: 100
			}, {	header: 'Member Card', xtype: 'actioncolumn', width: 75, align: 'center', items: [ {
					iconCls: 'printIcon spaceIcon', tooltip: 'Cetak', handler: function(grid, rowIndex, colIndex) {
						var rec = JemaatStore.getAt(rowIndex);
						window.open(Web.HOST + '/jemaat/member_card/?JemaatID=' + rec.data.id);
					}
			} ]
			}, {	header: 'Kota', dataIndex: 'kota', sortable: true, filter: true, width: 100, hidden: true
			}, {	header: 'Propinsi', dataIndex: 'propinsi', sortable: true, filter: true, width: 100, hidden: true
			}, {	header: 'Negara', dataIndex: 'negara', sortable: true, filter: true, width: 100, hidden: true
			}, {	header: 'Kodepos', dataIndex: 'kodepos', sortable: true, filter: true, width: 60, hidden: true
			}, {	header: 'Telepon', dataIndex: 'telpon', sortable: true, filter: true, width: 100, hidden: true
			}, {	header: 'HP', dataIndex: 'hp', sortable: true, filter: true, width: 100, hidden: true
			}, {	header: 'Status', dataIndex: 'status', sortable: true, filter: true, width: 80, hidden: true
			}, {	header: 'Lengkap', dataIndex: 'lengkap', sortable: true, filter: true, width: 80, hidden: true
			}, {	header: 'Tempat Baptis', dataIndex: 'tempat_baptis', sortable: true, filter: true, width: 120
			}, {	header: 'Tanggal Baptis', dataIndex: 'tanggalbaptis', sortable: true, filter: true, width: 120, renderer: Ext.util.Format.dateRenderer(DATE_FORMAT)
			}, {	header: 'Tempat Sidi', dataIndex: 'tempat_sidi', sortable: true, filter: true, width: 120
			}, {	header: 'Tanggal Sidi', dataIndex: 'tanggalsidi', sortable: true, filter: true, width: 120, renderer: Ext.util.Format.dateRenderer(DATE_FORMAT)
			}, {	header: 'Tempat Pemberkatan', dataIndex: 'tempatpemberkatan', sortable: true, filter: true, width: 120
			}, {	header: 'Tanggal Nikah', dataIndex: 'tanggalnikah', sortable: true, filter: true, width: 120, renderer: Ext.util.Format.dateRenderer(DATE_FORMAT)
			}, {	header: 'Tgl Meninggal', dataIndex: 'tgl_meninggal', sortable: true, filter: true, width: 120, renderer: Ext.util.Format.dateRenderer(DATE_FORMAT)
			}, {	header: 'Dimakamkan di', dataIndex: 'tempat_makam', sortable: true, filter: true, width: 120
			}, {	header: 'Atestesi Dari', dataIndex: 'atestesi_dari', sortable: true, filter: true, width: 120
			}, {	header: 'Atestesi Ke', dataIndex: 'atestesi_ke', sortable: true, filter: true, width: 120
		} ],
		tbar: [ {
				text: 'Tambah', iconCls: 'addIcon', tooltip: 'Tambah jemaat', id: 'AddTB', handler: function() {
					JemaatGrid.Record = DefaultRecord();
					CallWindowJemaat(JemaatGrid.Record);
				}
			}, '-', {
				text: 'Ubah', iconCls: 'editIcon', tooltip: 'Ubah jemaat', id: 'UpdateTB', handler: function() { JemaatGrid.UpdateJemaat({ }); }
			}, '-', {
				text: 'Hapus', iconCls: 'delIcon', tooltip: 'Hapus jemaat', id: 'DeleteTB', handler: function() {
					if (JemaatGrid.getSelectionModel().getSelection().length == 0) {
						Ext.Msg.alert('Informasi', 'Silahkan memilih data jemaat.');
						return false;
					}
					
					Ext.MessageBox.confirm('Konfirmasi', 'Apa anda yakin akan menghapus data ini ?', JemaatGrid.DeleteJemaat);
				}
			}, '-', {
				text: 'Import', iconCls: 'editIcon', tooltip: 'Import jemaat', id: 'ImportTB', handler: function() { CallWindowUpload(); }
			}, '->', {
                id: 'SearchPM', xtype: 'textfield', tooltip: 'Cari jemaat', emptyText: 'Cari', width: 100, listeners: {
                    'specialKey': function(field, el) {
                        if (el.getKey() == Ext.EventObject.ENTER) {
                            var value = Ext.getCmp('SearchPM').getValue();
                            if ( value ) {
								JemaatGrid.LoadGrid({ RequestName: 'Jemaat', idgereja: GetAccessGerejaID, NameLike: value });
                            }
                        }
                    }
                }
            }, '-', {
				text: 'Reset', tooltip: 'Reset pencarian', iconCls: 'refreshIcon', handler: function() {
					JemaatGrid.LoadGrid({ RequestName: 'Jemaat', idgereja: GetAccessGerejaID });
				}
            }, '-', {
				text: 'Cetak', tooltip: 'Cetak Jemaat', iconCls: 'printIcon', handler: function() {
					JemaatStore.proxy.extraParams.SetSession = 1;
					JemaatStore.load({
						callback: function(r, options, success) {
							var RawDataStore = options.response.responseText;
							eval('var DataStore = ' + RawDataStore);
							window.open(Web.HOST + '/jemaat/jemaat_list/?SessionID=' + DataStore.SessionName);
						}
					});
				}
			}, '-', {
				text: 'CSV', tooltip: 'Download CSV', iconCls: 'printIcon', handler: function() {
					JemaatStore.proxy.extraParams.SetSession = 1;
					JemaatStore.load({
						callback: function(r, options, success) {
							var RawDataStore = options.response.responseText;
							eval('var DataStore = ' + RawDataStore);
							window.open(Web.HOST + '/jemaat/jemaat_csv/?SessionID=' + DataStore.SessionName);
						}
					});
				}
			}, '-', {
				text: 'Excel', tooltip: 'Download Excel', iconCls: 'printIcon', handler: function() {
					JemaatStore.proxy.extraParams.SetSession = 1;
					JemaatStore.load({
						callback: function(r, options, success) {
							var RawDataStore = options.response.responseText;
							eval('var DataStore = ' + RawDataStore);
							window.open(Web.HOST + '/jemaat/jemaat_excel/?SessionID=' + DataStore.SessionName);
						}
					});
				}
		} ],
		bbar: new Ext.PagingToolbar( {
			store: JemaatStore, displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: 'No topics to display'
		} ),
		listeners: {
			'itemdblclick': function(model, records) {
				JemaatGrid.UpdateJemaat({ });
            }
        },
		LoadGrid: function(Param) {
			JemaatStore.proxy.extraParams = Param;
			JemaatStore.load();
		},
		UpdateJemaat: function(Param) {
			var Data = JemaatGrid.getSelectionModel().getSelection();
			if (Data.length == 0) {
				Ext.Msg.alert('Informasi', 'Silahkan memilih data jemaat.');
				return false;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/administrator/ajax',
				params: { Action: 'GetJemaatByID', JemaatID: Data[0].data.id },
				success: function(Result) {
					eval('var Record = ' + Result.responseText)
					JemaatGrid.Record = Record;
					CallWindowJemaat(Record);
				}
			});
		},
		DeleteJemaat: function(Value) {
			if (Value == 'no') {
				return;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/administrator/ajax',
				params: { Action: 'DeteleJemaatByID', JemaatID: JemaatGrid.getSelectionModel().getSelection()[0].data.id },
				success: function(TempResult) {
					eval('var Result = ' + TempResult.responseText)
					
					Ext.Msg.alert('Informasi', Result.Message);
					if (Result.QueryStatus == '1') {
						JemaatStore.load();
					}
				}
			});
		}
	});
	
	function CallWindowJemaat(Jemaat) {
		var WinJemaat = new Ext.Window({
			layout: 'fit', width: 675, height: 520,
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Keluarga', id: 'KeluargaED', tooltip: 'Tambah keluarga', hidden: true, handler: function() { CallWindowKeluarga({ KeluargaID: 0 }); }
//				}, 		'<div style="width: 410px;">&nbsp;</div>', {
				}, {	text: 'Save', id: 'DeleteED', handler: function() { WinJemaat.SaveJemaat({  }); }
				}, {	text: 'Close', handler: function() {
						WinJemaat.hide();
				}
			}],
			listeners: {
				show: function(w) {
					var Title = (Jemaat.id == 0) ? 'Jemaat - [New]' : 'Jemaat - [Edit]';
					w.setTitle(Title);
					
					Ext.Ajax.request({
						url: Web.HOST + '/administrator/request/entry-jemaat-popup',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							WinJemaat.nama = new Ext.form.TextField({ renderTo: 'namaED', width: 175, allowBlank: false, blankText: 'Masukkan Nama', value: JemaatGrid.Record.nama });
							WinJemaat.nomor = new Ext.form.TextField({ renderTo: 'nomorED', width: 175, allowBlank: false, blankText: 'Masukkan Nomor', readOnly: true, disabled: true, value: JemaatGrid.Record.nomor });
							
							WinJemaat.Tab = Ext.createWidget('tabpanel', {
								renderTo: 'TabJemaat', activeTab: 0, height: 390,
								defaults: { bodyPadding: 10 },
								items: [
									{ 	contentEl: 'UmumEL', title: 'Umum', id: 'Tab1', listeners: {
										activate: function(tab) {
											if (typeof(WinJemaat.keluarga) != 'undefined') {
												return;
											}
                                            
											JemaatGrid.Record.tgllahir = (JemaatGrid.Record.tgllahir == '0000-00-00') ? '' : JemaatGrid.Record.tgllahir;
											JemaatGrid.Record.tanggaldaftar = (JemaatGrid.Record.tanggaldaftar == '0000-00-00') ? '' : JemaatGrid.Record.tanggaldaftar;
											
											// Column 1
											WinJemaat.keluarga = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: true,
												store: Ext.getStore('KeluargaStore'), width: 175, minChars: 1,
												valueField: 'id', displayField: 'nama', typeAhead: true,
												renderTo: 'keluargaED', readonly: false, editable: true,
												allowBlank: false, blankText: 'Masukkan Nama Keluarga',
												listeners: {
													select: function(combo, record) { WinJemaat.GenerateNomor(); },
													beforequery: function() { WinJemaat.keluarga.store.proxy.extraParams.idgereja = GetAccessGerejaID; }
												}
											});
											WinJemaat.hubungankeluarga = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: true,
												queryMode: 'local', store: Ext.getStore('HubunganKeluargaStore'), width: 175,
												valueField: 'id', displayField: 'value',
												renderTo: 'hubungankeluargaED', readonly: true, editable: false,
												allowBlank: false, blankText: 'Masukkan Hubungan Keluarga',
												listeners: { select: function(combo, record) { WinJemaat.GenerateNomor(); } }
											});
											WinJemaat.tgllahir = new Ext.form.DateField({ renderTo: 'tgllahirED', width: 125, format: DATE_FORMAT, allowBlank: false, blankText: 'Masukkan Tanggal Lahir', value: JemaatGrid.Record.tgllahir });
											WinJemaat.tempatlahir = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: false,
												store: Ext.getStore('KotaStore'), width: 175, minChars: 1,
												valueField: 'kota', displayField: 'kota', typeAhead: true,
												renderTo: 'tempatlahirED', readonly: false, editable: true,
												allowBlank: false, blankText: 'Masukkan Tempat Lahir',
                                                value: JemaatGrid.Record.tempatlahir
											});
											WinJemaat.gereja = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: true,
												store: Ext.getStore('GerejaStore'), width: 175, minChars: 1,
												valueField: 'id', displayField: 'nama', typeAhead: true,
												renderTo: 'gerejaED', readonly: false, editable: true,
												allowBlank: false, blankText: 'Masukkan Gereja', listeners: {
													beforequery: function() { WinJemaat.gereja.store.proxy.extraParams.id = GetAccessGerejaID; }
												}
											});
											WinJemaat.tanggaldaftar = new Ext.form.DateField({ renderTo: 'tanggaldaftarED', width: 125, format: DATE_FORMAT, value: JemaatGrid.Record.tanggaldaftar });
											WinJemaat.rtrw = new Ext.form.TextField({ renderTo: 'rtrwED', width: 175, value: JemaatGrid.Record.rtrw });
											WinJemaat.alamat = new Ext.form.TextArea({ renderTo: 'alamatED', width: 175, height: 50, value: JemaatGrid.Record.alamat });
											
											// Column 2
											WinJemaat.kelurahan = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: false,
												store: Ext.getStore('KelurahanStore'), width: 175, minChars: 1,
												valueField: 'kelurahan', displayField: 'kelurahan', typeAhead: true,
												renderTo: 'kelurahanED', readonly: false, editable: true,
                                                value: JemaatGrid.Record.kelurahan
											});
											WinJemaat.kecamatan = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: false,
												store: Ext.getStore('KecamatanStore'), width: 175, minChars: 1,
												valueField: 'kecamatan', displayField: 'kecamatan', typeAhead: true,
												renderTo: 'kecamatanED', readonly: false, editable: true,
                                                value: JemaatGrid.Record.kecamatan
											});
											WinJemaat.kota = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: false,
												store: Ext.getStore('KotaStore'), width: 175, minChars: 1,
												valueField: 'kota', displayField: 'kota', typeAhead: true,
												renderTo: 'kotaED', readonly: false, editable: true,
                                                value: JemaatGrid.Record.kota
											});
											WinJemaat.propinsi = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: false,
												store: Ext.getStore('PropinsiStore'), width: 175, minChars: 1,
												valueField: 'propinsi', displayField: 'propinsi', typeAhead: true,
												renderTo: 'propinsiED', readonly: false, editable: true,
                                                value: JemaatGrid.Record.propinsi
											});
											WinJemaat.negara = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: false,
												store: Ext.getStore('NegaraStore'), width: 175, minChars: 1,
												valueField: 'negara', displayField: 'negara', typeAhead: true,
												renderTo: 'negaraED', readonly: false, editable: true,
                                                value: JemaatGrid.Record.negara
											});
											WinJemaat.kodepos = new Ext.form.TextField({ renderTo: 'kodeposED', width: 175, value: JemaatGrid.Record.kodepos });
											WinJemaat.telpon = new Ext.form.TextField({ renderTo: 'telponED', width: 175, value: JemaatGrid.Record.telpon });
											WinJemaat.hp = new Ext.form.TextField({ renderTo: 'hpED', width: 175, value: JemaatGrid.Record.hp });
											WinJemaat.status = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: true,
												queryMode: 'local', store: Ext.getStore('StatusStore'), width: 175,
												valueField: 'id', displayField: 'value', typeAhead: true,
												renderTo: 'statusED', readonly: true, editable: false,
												value: JemaatGrid.Record.status
											});
											WinJemaat.meninggal = new Ext.form.Checkbox({ renderTo: 'meninggalED', checked: (JemaatGrid.Record.meninggal == 1) ? true : false });
											WinJemaat.tgl_meninggal = new Ext.form.DateField({ renderTo: 'tgl_meninggalED', width: 125, format: DATE_FORMAT, value: JemaatGrid.Record.tgl_meninggal });
											WinJemaat.tempat_makam = new Ext.form.TextField({ renderTo: 'tempat_makamED', width: 175, value: JemaatGrid.Record.tempat_makam });
											WinJemaat.sektor = Combo.Class.Sektor({ renderTo: 'sektorED', width: 175 });
											WinJemaat.sektor.setValue(JemaatGrid.Record.sektor_id);
											WinJemaat.atestesi_dari = new Ext.form.TextField({ renderTo: 'atestesi_dariED', width: 175, value: JemaatGrid.Record.atestesi_dari });
											WinJemaat.atestesi_ke = new Ext.form.TextField({ renderTo: 'atestesi_keED', width: 175, value: JemaatGrid.Record.atestesi_ke });
											
											if (JemaatGrid.Record.hubungankeluarga != '') {
												WinJemaat.hubungankeluarga.setValue(JemaatGrid.Record.hubungankeluarga);
											}
											
											Ext.Ajax.request({
												url: Web.HOST + '/administrator/combo',
												params: { Action : 'Keluarga', idkeluarga: JemaatGrid.Record.idkeluarga },
												success: function(Result) {
													Ext.getStore('KeluargaStore').loadData(eval(Result.responseText));
													WinJemaat.keluarga.setValue(JemaatGrid.Record.idkeluarga);
												}
											});
											Ext.Ajax.request({
												url: Web.HOST + '/administrator/combo',
												params: { Action : 'Gereja', query: JemaatGrid.Record.gereja },
												success: function(Result) {
													Ext.getStore('GerejaStore').loadData(eval(Result.responseText));
													WinJemaat.gereja.setValue(JemaatGrid.Record.idgereja);
												}
											});
										},
										beforedeactivate: function(tab) {
											WinJemaat.Tab.CollectEntry('Tab1');
										}
									} },
									{	contentEl: 'DetailEL',  title: 'Detail', id: 'Tab2', listeners: {
										activate: function(tab) {
											if (typeof(WinJemaat.firstname) != 'undefined') {
												return;
											}
											
											JemaatGrid.Record.tanggalsidi = (JemaatGrid.Record.tanggalsidi == '0000-00-00') ? '' : JemaatGrid.Record.tanggalsidi;
											JemaatGrid.Record.tanggalnikah = (JemaatGrid.Record.tanggalnikah == '0000-00-00') ? '' : JemaatGrid.Record.tanggalnikah;
											JemaatGrid.Record.tanggalbaptis = (JemaatGrid.Record.tanggalbaptis == '0000-00-00') ? '' : JemaatGrid.Record.tanggalbaptis;
											
											// Column 3
											WinJemaat.firstname = new Ext.form.TextField({ renderTo: 'firstnameED', width: 175, value: JemaatGrid.Record.firstname });
											WinJemaat.lastname = new Ext.form.TextField({ renderTo: 'lastnameED', width: 175, value: JemaatGrid.Record.lastname });
											WinJemaat.email = new Ext.form.TextField({ renderTo: 'emailED', width: 175, value: JemaatGrid.Record.email });
											WinJemaat.sex = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: true,
												queryMode: 'local', store: Ext.getStore('SexStore'), width: 175,
												valueField: 'id', displayField: 'value',
												renderTo: 'sexED', readonly: true, editable: false,
												value: JemaatGrid.Record.sex
											});
											WinJemaat.profesi = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: false,
												store: Ext.getStore('ProfesiStore'), width: 175, minChars: 1,
												valueField: 'profesi', displayField: 'profesi', typeAhead: true,
												renderTo: 'profesiED', readonly: false, editable: true,
                                                value: JemaatGrid.Record.profesi
											});
											WinJemaat.institusi = new Ext.form.TextField({ renderTo: 'institusiED', width: 175, value: JemaatGrid.Record.institusi });
											WinJemaat.jabatan = new Ext.form.TextField({ renderTo: 'jabatanED', width: 175, value: JemaatGrid.Record.jabatan });
                                            WinJemaat.catatan = new Ext.form.TextArea({ renderTo: 'catatanED', width: 175, height: 50, value: JemaatGrid.Record.catatan });
											
											// Column 4
											WinJemaat.statusbaptis = new Ext.form.Checkbox({ renderTo: 'statusbaptisED', checked: (JemaatGrid.Record.statusbaptis == 1) ? true : false });
											WinJemaat.tanggalbaptis = new Ext.form.DateField({ renderTo: 'tanggalbaptisED', width: 125, format: DATE_FORMAT, value: JemaatGrid.Record.tanggalbaptis });
											WinJemaat.tempat_baptis = new Ext.form.TextField({ renderTo: 'tempat_baptisED', width: 175, value: JemaatGrid.Record.tempat_baptis });
											WinJemaat.statussidi = new Ext.form.Checkbox({ renderTo: 'statussidiED', checked: (JemaatGrid.Record.statussidi == 1) ? true : false });
											WinJemaat.tanggalsidi = new Ext.form.DateField({ renderTo: 'tanggalsidiED', width: 125, format: DATE_FORMAT, value: JemaatGrid.Record.tanggalsidi });
											WinJemaat.tempat_sidi = new Ext.form.TextField({ renderTo: 'tempat_sidiED', width: 175, value: JemaatGrid.Record.tempat_sidi });
											WinJemaat.statusnikah = new Ext.form.Checkbox({ renderTo: 'statusnikahED', checked: (JemaatGrid.Record.statusnikah == 1) ? true : false });
											WinJemaat.tanggalnikah = new Ext.form.DateField({ renderTo: 'tanggalnikahED', width: 125, format: DATE_FORMAT, value: JemaatGrid.Record.tanggalnikah });
											WinJemaat.tempatpemberkatan = new Ext.form.TextField({ renderTo: 'tempatpemberkatanED', width: 175, value: JemaatGrid.Record.tempatpemberkatan });
											WinJemaat.pendidikan = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: false,
												store: Ext.getStore('PendidikanStore'), width: 175, minChars: 1,
												valueField: 'pendidikan', displayField: 'pendidikan', typeAhead: true,
												renderTo: 'pendidikanED', readonly: false, editable: true,
                                                value: JemaatGrid.Record.pendidikan
											});
											WinJemaat.gelar = new Ext.form.TextField({ renderTo: 'gelarED', width: 175, value: JemaatGrid.Record.gelar });
											WinJemaat.jurusan = new Ext.form.TextField({ renderTo: 'jurusanED', width: 175, value: JemaatGrid.Record.jurusan });
											WinJemaat.golongandarah = new Ext.form.ComboBox({
												triggerAction: 'all', lazyRender: true, forceSelection: true,
												queryMode: 'local', store: Ext.getStore('GolonganDarahStore'), width: 175,
												valueField: 'id', displayField: 'value', typeAhead: true,
												renderTo: 'golongandarahED', readonly: true, editable: false,
												value: JemaatGrid.Record.golongandarah
											});
											WinJemaat.lengkap = new Ext.form.Checkbox({ renderTo: 'LengkapED', checked: (JemaatGrid.Record.lengkap == 1) ? true : false });
										},
										beforedeactivate: function(tab) {
											WinJemaat.Tab.CollectEntry('Tab2');
										}
									} },
									{ 	contentEl: 'FotoEL', title: 'Foto', id: 'Tab3', listeners: {
										activate: function(tab) {
											if (typeof(WinJemaat.foto) != 'undefined') {
												return;
											}
                                            
											WinJemaat.foto = Ext.create('Ext.form.field.File', {
												name: 'foto', renderTo: 'fotoED', width: 225, hideLabel: true, listeners: {
													change: function() { WinJemaat.Tab.Upload(); }
												}
											});
											Ext.get('CntFoto').dom.innerHTML = '<img src="' + JemaatGrid.Record.FotoLink + '" />';
										}
									} }
								],
								CollectEntry: function(TabActive) {
									if (TabActive == 'Tab1') {
										JemaatGrid.Record.nama = WinJemaat.nama.getValue();
										JemaatGrid.Record.nomor = WinJemaat.nomor.getValue();
										JemaatGrid.Record.idkeluarga = WinJemaat.keluarga.getValue();
										JemaatGrid.Record.idgereja = WinJemaat.gereja.getValue();
										JemaatGrid.Record.tgllahir = WinJemaat.tgllahir.getValue();
										JemaatGrid.Record.tempatlahir = WinJemaat.tempatlahir.getValue();
										JemaatGrid.Record.hubungankeluarga = WinJemaat.hubungankeluarga.getValue();
										JemaatGrid.Record.tanggaldaftar = WinJemaat.tanggaldaftar.getValue();
										JemaatGrid.Record.rtrw = WinJemaat.rtrw.getValue();
										JemaatGrid.Record.alamat = WinJemaat.alamat.getValue();
										JemaatGrid.Record.kelurahan = WinJemaat.kelurahan.getValue();
										JemaatGrid.Record.kecamatan = WinJemaat.kecamatan.getValue();
										JemaatGrid.Record.kota = WinJemaat.kota.getValue();
										JemaatGrid.Record.propinsi = WinJemaat.propinsi.getValue();
										JemaatGrid.Record.negara = WinJemaat.negara.getValue();
										JemaatGrid.Record.kodepos = WinJemaat.kodepos.getValue();
										JemaatGrid.Record.telpon = WinJemaat.telpon.getValue();
										JemaatGrid.Record.hp = WinJemaat.hp.getValue();
										JemaatGrid.Record.status = WinJemaat.status.getValue();
										JemaatGrid.Record.meninggal = (WinJemaat.meninggal.getValue()) ? 1 : 0;
										JemaatGrid.Record.tgl_meninggal = WinJemaat.tgl_meninggal.getValue();
										JemaatGrid.Record.tempat_makam = WinJemaat.tempat_makam.getValue();
										JemaatGrid.Record.sektor_id = (WinJemaat.sektor.getValue() == null) ? 0 : Func.GetSektor(WinJemaat.sektor.getValue());
										JemaatGrid.Record.atestesi_dari = WinJemaat.atestesi_dari.getValue();
										JemaatGrid.Record.atestesi_ke = WinJemaat.atestesi_ke.getValue();
									} else if (TabActive == 'Tab2') {
										JemaatGrid.Record.firstname = WinJemaat.firstname.getValue();
										JemaatGrid.Record.lastname = WinJemaat.lastname.getValue();
										JemaatGrid.Record.email = WinJemaat.email.getValue();
										JemaatGrid.Record.sex = WinJemaat.sex.getValue();
										JemaatGrid.Record.profesi = WinJemaat.profesi.getValue();
										JemaatGrid.Record.institusi = WinJemaat.institusi.getValue();
										JemaatGrid.Record.jabatan = WinJemaat.jabatan.getValue();
										JemaatGrid.Record.catatan = WinJemaat.catatan.getValue();
										JemaatGrid.Record.statusbaptis = (WinJemaat.statusbaptis.getValue()) ? 1 : 0;
										JemaatGrid.Record.tanggalbaptis = WinJemaat.tanggalbaptis.getValue();
										JemaatGrid.Record.tempat_baptis = WinJemaat.tempat_baptis.getValue();
										JemaatGrid.Record.statussidi = (WinJemaat.statussidi.getValue()) ? 1 : 0;
										JemaatGrid.Record.tanggalsidi = WinJemaat.tanggalsidi.getValue();
										JemaatGrid.Record.tempat_sidi = WinJemaat.tempat_sidi.getValue();
										JemaatGrid.Record.statusnikah = (WinJemaat.statusnikah.getValue()) ? 1 : 0;
										JemaatGrid.Record.tanggalnikah = WinJemaat.tanggalnikah.getValue();
										JemaatGrid.Record.tempatpemberkatan = WinJemaat.tempatpemberkatan.getValue();
										JemaatGrid.Record.pendidikan = WinJemaat.pendidikan.getValue();
										JemaatGrid.Record.gelar = WinJemaat.gelar.getValue();
										JemaatGrid.Record.jurusan = WinJemaat.jurusan.getValue();
										JemaatGrid.Record.golongandarah = WinJemaat.golongandarah.getValue();
										JemaatGrid.Record.lengkap = (WinJemaat.lengkap.getValue()) ? 1 : 0;
									}
								},
								Upload: function() {
									if (! Renderer.AllowedWrite()) {
										return;
									}
									
									var Form = new Ext.FormPanel({
										renderTo: 'FormUpload', fileUpload: true,
										width: 500, frame: true, autoHeight: true, labelWidth: 50, 
										items: [
											{ 	xtype: 'textfield', fieldLabel: 'FormType', name: 'FormType', value: 'Jemaat' },
											{ 	xtype: 'textfield', fieldLabel: 'JemaatID', name: 'JemaatID', value: JemaatGrid.Record.JemaatID },
											WinJemaat.foto
										],
										defaults: { anchor: '95%', allowBlank: false, msgTarget: 'side' }
									});
									
									Form.getForm().submit({
										url: Web.HOST + '/administrator/upload',
										waitMsg: 'Upload Document ...',
										success: function(DataForm, Ajax) {
											Ext.get('FormUpload').dom.innerHTML = '';
											Ext.get('CntMessage').dom.innerHTML = Ajax.result.Message;
											Ext.get('CntFoto').dom.innerHTML = '<img src="' + Ajax.result.PhotoLink + '" />';
											JemaatGrid.Record.foto = Ajax.result.PhotoLink.replace(Web.HOST + '/images/jemaat/', '');
											
											WinJemaat.foto = Ext.create('Ext.form.field.File', {
												name: 'foto', renderTo: 'fotoED', width: 225, hideLabel: true, listeners: {
													change: function() { WinJemaat.Tab.Upload(); }
												}
											});
										}
									});
								}
							} );
							
							Ext.get('ButtonFamily').on('click',function() {
								CallWindowKeluarga({ KeluargaID: 0 });
							});
						}
					});
					
					if (! Renderer.AllowedWrite()) {
						Ext.getCmp('DeleteED').hide();
					}
					if (! Renderer.AllowedAccess('KeluargaWrite')) {
						Ext.getCmp('KeluargaED').hide();
					}
				},
				hide: function(w) {
					w.destroy();
					w = WinJemaat = null;
				}
			},
			SaveJemaat: function(Window) {
				var TabActiveID = WinJemaat.Tab.getActiveTab().el.id;
				WinJemaat.Tab.CollectEntry(TabActiveID);
				
				var Param = JemaatGrid.Record;
				Param.Action = 'EditJemaat';
				Param.RequestApi = 1;
				Param.JemaatID = Param.id;
				
				// Validation
				var Message = '';
				var Validation = true;
				if (! WinJemaat.nama.validate()) {
					Validation = false;
				}
				if (! WinJemaat.keluarga.validate()) {
					Validation = false;
				}
				if (! WinJemaat.hubungankeluarga.validate()) {
					Validation = false;
				}
				if (! WinJemaat.nomor.validate()) {
					Validation = false;
				}
				if (! WinJemaat.tgllahir.validate()) {
					Validation = false;
				}
				if (! WinJemaat.tempatlahir.validate()) {
					Validation = false;
				}
				if (! WinJemaat.gereja.validate()) {
					Validation = false;
				}
				
				if (! Validation) {
					WinJemaat.Tab.setActiveTab('Tab1');
					return;
				}
				
				Ext.Ajax.request({
					params: Param,
					url: Web.HOST + '/administrator/ajax',
					success: function(TempResult) {
						eval('var Result = ' + TempResult.responseText)
						Ext.Msg.alert('Informasi', Result.Message);
                        
						if (Result.QueryStatus == '1') {
							JemaatStore.load();
							WinJemaat.hide();
						}
					}
				});
			},
			GenerateNomor: function() {
				var KeluargaID = WinJemaat.keluarga.getValue();
				var KeluargaNomor = (KeluargaID == null) ? '' : Ext.getStore('KeluargaStore').getById(KeluargaID).data.nomor;
				var HubunganKeluargaID = (WinJemaat.hubungankeluarga.getValue() == null) ? '' : WinJemaat.hubungankeluarga.getValue();
				var Nomor = KeluargaNomor + '-' + HubunganKeluargaID;
				
				if (Nomor.substr(Nomor.length - 1, 1) == 'n') {
					var Param = { Action: 'GerNomorJemaat', Nomor: Nomor }
					Ext.Ajax.request({
						params: Param,
						url: Web.HOST + '/administrator/ajax',
						success: function(TempResult) {
							eval('var Result = ' + TempResult.responseText);
							WinJemaat.nomor.setValue(Result.Nomor);
						}
					});
				} else {
					WinJemaat.nomor.setValue(Nomor);
				}
			}
		});
		WinJemaat.show();
	}
	
	function CallWindowUpload() {
		var WinUpload = new Ext.Window({
			title: 'Upload Hasil Transfer',
			layout: 'fit', width: 325, height: 125,
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Upload', handler: function() { WinUpload.DoUpload(); }
				}, {	text: 'Close', handler: function() {
						WinUpload.hide();
				}
			}],
			listeners: {
				show: function(w) {
					Ext.Ajax.request({
						url: Web.HOST + '/jemaat/main/import',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							WinUpload.FileImport = Ext.create('Ext.form.field.File', { name: 'FileImport', renderTo: 'FileImportED', width: 225, hideLabel: true });
						}
					});
				},
				hide: function(w) {
					w.destroy();
					w = WinUpload = null;
				}
			},
			DoUpload: function() {
                var Form = new Ext.FormPanel({
                    renderTo: 'FormUpload', fileUpload: true,
                    width: 500, frame: true, autoHeight: true,
                    labelWidth: 50,
                    items: [
						{ 	xtype: 'textfield', fieldLabel: 'FormType', name: 'FormType', value: 'ImportJemaat' },
						WinUpload.FileImport
                    ],
                    defaults: { anchor: '95%', allowBlank: false, msgTarget: 'side' }
                });
                
                Form.getForm().submit({
                    url: Web.HOST + '/administrator/upload',
                    waitMsg: 'Upload Document ...',
                    success: function(DataForm, Ajax) {
                        Ext.get('FormUpload').dom.innerHTML = '';
						WinUpload.FileImport = Ext.create('Ext.form.field.File', { name: 'FileImport', renderTo: 'FileImportED', width: 225, hideLabel: true });
                        
                        if (Ajax.result.UploadStatus == 1) {
                            WinUpload.hide();
							CallWindowGrid({ 'FileUpload': Ajax.result.PhotoLink });
                        } else {
							Ext.Msg.alert('Informasi', 'Mohon hanya mengupload file dengan extensi csv.');
						}
                    }
                });
			}
		});
		WinUpload.show();
	}
	
	function CallWindowGrid(Param) {
		var WinUpload = new Ext.Window({
			title: 'Upload Hasil Transfer',
			layout: 'fit', width: 775, height: 435,
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Save', handler: function() { WinUpload.Save(); }
				}, {	text: 'Close', handler: function() {
						WinUpload.hide();
				}
			}],
			listeners: {
				show: function(w) {
					Ext.Ajax.request({
						url: Web.HOST + '/administrator/ajax',
						params: { Action: 'ShowTranferUpload', FileUpload: Param.FileUpload }, success: function(Result) {
							eval('var Data = ' + Result.responseText)
							w.body.dom.innerHTML = Data.Content;
							
							var grid = Ext.create('Ext.ux.grid.TransformGrid', "ConvertionGrid", { stripeRows: true, height: 350 });
							grid.render();
						}
					});
				},
				hide: function(w) {
					w.destroy();
					w = WinUpload = null;
				}
			},
			Save: function() {
				var IsValid = Ext.get('IsValid').getValue();
				if (IsValid == 0) {
					Ext.Msg.alert('Informasi', '<div style="width: 500px">Mohon memastikan semua data benar-benar valid untuk melakukan proses penyimpanan.</div>');
					return false;
				}
				
				Ext.Ajax.request({
					url: Web.HOST + '/administrator/ajax',
					params: { Action: 'SaveTranferUpload', FileUpload: Param.FileUpload }, success: function(TempResult) {
						eval('var Result = ' + TempResult.responseText);
						Ext.Msg.alert('Informasi', Result.Message);
                        
                        if (Result.QueryStatus == '1') {
							JemaatStore.load();
							WinUpload.hide();
                        }
					}
				});
			}
		});
		WinUpload.show();
	}
	
	if (! Renderer.AllowedWrite()) {
		Ext.getCmp('AddTB').disable();
		Ext.getCmp('UpdateTB').disable();
		Ext.getCmp('ImportTB').disable();
		Ext.getCmp('DeleteTB').disable();
	}
	
	Renderer.InitWindowSize({ Panel: -1, Grid: JemaatGrid, Toolbar: 70 });
});