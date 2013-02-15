var DATE_FORMAT = 'Y-m-d';

Ext.create('Ext.data.Store', {
    fields: ['id', 'nama', 'alamat', 'nomor'],
    storeId: 'KeluargaStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'Keluarga' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'nama', 'alamat', 'kota'],
    storeId: 'GerejaStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'Gereja' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'value', 'alamat'],
    storeId: 'GolonganDarahStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'GolonganDarah' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'value'],
    storeId: 'StatusStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'Status' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'value'],
    storeId: 'SexStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'Sex' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'value'],
    storeId: 'HubunganKeluargaStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'HubunganKeluarga' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'name'],
    storeId: 'AdminGerejaStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'AdminGereja' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'kelurahan'],
    storeId: 'KelurahanStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'Kelurahan' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'kecamatan'],
    storeId: 'KecamatanStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'Kecamatan' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'negara'],
    storeId: 'NegaraStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'Negara' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'propinsi'],
    storeId: 'PropinsiStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'Propinsi' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'kota'],
    storeId: 'KotaStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'Kota' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'profesi'],
    storeId: 'ProfesiStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'Profesi' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['id', 'pendidikan'],
    storeId: 'PendidikanStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'Pendidikan' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Ext.create('Ext.data.Store', {
    fields: ['group_id', 'group_name'],
    storeId: 'GroupStore', autoLoad: false, proxy: {
        type: 'ajax', extraParams: { Action : 'Group' },
        url: Web.HOST + '/administrator/combo',
        reader: { type: 'json', root: 'res' },
        actionMethods: { read: 'POST' }
    }
});

Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

var Renderer = {
	DateFormat: function(Value) {
		var Month = Value.getMonth() + 1;
		var MonthText = (Month.toString().length == 1) ? '0' + Month : Month;
		var Date = Value.getFullYear() + '-' + MonthText + '-' + Value.getDate();
		return Date;
	},
	TimeFormat: function(Value) {
		var Hour = Value.getHours();
		var HourText = (Hour.toString().length == 1) ? '0' + Hour : Hour;
		var Minute = Value.getMinutes();
		var MinuteText = (Minute.toString().length == 1) ? '0' + Minute : Minute;
		var Time = HourText + ':' + MinuteText;
		return Time;
	},
	AllowedRead: function() {
		return (Ext.get('PermissionRead').getValue() == 0) ? false : true;
	},
	AllowedWrite: function() {
		return (Ext.get('PermissionWrite').getValue() == 0) ? false : true;
	},
	AllowedAccess: function(ElementID) {
		return (Ext.get(ElementID).getValue() == 0) ? false : true;
	},
	InitWindowSize: function(Param) {
		Renderer.AutoWindowSize({ Panel: Param.Panel, Grid: Param.Grid, Toolbar: Param.Toolbar });
		
		// garai hang ra usah on resize
		return;
		window.onresize = function() {
			Renderer.AutoWindowSize({ Panel: Param.Panel, Grid: Param.Grid, Toolbar: Param.Toolbar });
		};
	},
	AutoWindowSize: function(Param) {
		if (typeof window.innerWidth != 'undefined') {
			WindowWidth = window.innerWidth;
			WindowHeight = window.innerHeight;
		} else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0) {
			WindowWidth = document.documentElement.clientWidth,
			WindowHeight = document.documentElement.clientHeight
		} else {
			WindowWidth = document.getElementsByTagName('body')[0].clientWidth;
			WindowHeight = document.getElementsByTagName('body')[0].clientHeight;
		}
		
		if (Param.Panel == -1) {
			Param.Grid.setHeight(WindowHeight);
		} else {
			Param.Panel.setHeight(WindowHeight);
			Param.Grid.setHeight(WindowHeight - Param.Toolbar);
		}
	},
	RendererSudahBelum: function(Value) {
		return (Value == 1) ? 'Sudah' : 'Belum';
	},
	Money: function(value) {
		value = parseInt(value, 10);
        return value.formatMoney();
    },
	YesNo: function(value) {
		return (value == 1) ? 'Yes' : 'No';
    },
	FlashMessage: function(Message) {
		Ext.Msg.alert('Informasi', Message);
	}
}

function CallWindowKeluarga(Keluarga) {
	var GetAccessGerejaID = Ext.get('AccessGerejaID').getValue();
	
	var WinKeluarga = new Ext.Window({
		layout: 'fit', width: 370, height: 533,
		closeAction: 'hide', plain: true, modal: true,
		buttons: [ {
					text: 'Save', id: 'DeleteKeluargaED', handler: function() { WinKeluarga.SaveKeluarga(); }
			}, {	text: 'Close', handler: function() {
					WinKeluarga.hide();
			}
		}],
		listeners: {
			show: function(w) {
				var Title = (Keluarga.KeluargaID == 0) ? 'Entry Keluarga - [New]' : 'Entry Keluarga - [Edit]';
				w.setTitle(Title);
				
				Ext.Ajax.request({
					url: Web.HOST + '/administrator/request/entry-keluarga-popup',
					success: function(Result) {
						w.body.dom.innerHTML = Result.responseText;
						
						WinKeluarga.KeluargaID = Keluarga.KeluargaID;
						WinKeluarga.nama = new Ext.form.TextField({ renderTo: 'namaKeluargaED', width: 225, allowBlank: false, blankText: 'Masukkan Nama' });
						WinKeluarga.alamat = new Ext.form.TextArea({ renderTo: 'alamatKeluargaED', width: 225, height: 50 });
						WinKeluarga.nomor = new Ext.form.TextField({ renderTo: 'nomorKeluargaED', width: 225, allowBlank: false, blankText: 'Masukkan Nomor' });
						WinKeluarga.no_kk = new Ext.form.TextField({ renderTo: 'no_kkED', width: 225 });
						WinKeluarga.no_hp = new Ext.form.TextField({ renderTo: 'no_hpED', width: 225 });
						WinKeluarga.gereja = Combo.Class.Gereja({ renderTo: 'gerejaKeluargaED', width: 225, allowBlank: false, blankText: 'Masukkan Gereja' });
						WinKeluarga.sektor = Combo.Class.Sektor({ renderTo: 'sektorED', width: 225 });
						WinKeluarga.ultah_perkawinan = new Ext.form.DateField({ renderTo: 'ultah_perkawinanKeluargaED', width: 125, format: DATE_FORMAT });
						WinKeluarga.meninggal = new Ext.form.Checkbox({ renderTo: 'meninggalKeluargaED' });
						
						// Populate Record
						if (Keluarga.KeluargaID > 0) {
							Keluarga.ultah_perkawinan = (Keluarga.ultah_perkawinan == '0000-00-00') ? '' : Keluarga.ultah_perkawinan;
							
							WinKeluarga.nama.setValue(Keluarga.nama);
							WinKeluarga.alamat.setValue(Keluarga.alamat);
							WinKeluarga.nomor.setValue(Keluarga.nomor);
							WinKeluarga.no_kk.setValue(Keluarga.no_kk);
							WinKeluarga.no_hp.setValue(Keluarga.no_hp);
							WinKeluarga.sektor.setValue(Keluarga.sektor_id);
							WinKeluarga.ultah_perkawinan.setValue(Keluarga.ultah_perkawinan);
							WinKeluarga.meninggal.setValue(Keluarga.meninggal);
							
							Ext.Ajax.request({
								url: Web.HOST + '/administrator/combo',
								params: { Action : 'Gereja', ForceDisplayID: Keluarga.idgereja },
								success: function(Result) {
									WinKeluarga.gereja.store.loadData(eval(Result.responseText));
									WinKeluarga.gereja.setValue(Keluarga.idgereja);
								}
							});
							
							// Grid Keluarga
							Ext.get('CntGridKeluarga').dom.innerHTML = '';
							WinKeluarga.Jemaat = {
								Store: new Ext.create('Ext.data.Store', {
									fields:['id', 'nama', 'nomor', 'tgllahir', 'tempatlahir'],
									autoLoad: true, pageSize: 25, remoteSort: true,
									sorters: [{ property: 'nama', direction: 'ASC' }],
									proxy: {
										type: 'ajax', extraParams: { RequestName: 'Jemaat', idkeluarga: Keluarga.KeluargaID },
										url : Web.HOST + '/administrator/grid',
										reader: { root: 'JemaatData', totalProperty: 'JemaatCount' }
									}
								})
							};
							WinKeluarga.Jemaat.Grid = new Ext.grid.GridPanel({
								viewConfig: { forceFit: true }, store: WinKeluarga.Jemaat.Store, height: 175,
								renderTo: 'CntGridKeluarga', features: [{ ftype: 'filters', encode: true, local: false }],
								columns: [ {
											header: 'Nomor', dataIndex: 'nomor', sortable: true, filter: true, width: 75
									}, {	header: 'Nama', dataIndex: 'nama', sortable: true, filter: true, width: 150, flex: 1
									}, {	header: 'Tanggal Lahir', dataIndex: 'tgllahir', sortable: true, filter: true, hidden: true, width: 80, renderer: Ext.util.Format.dateRenderer(DATE_FORMAT)
									}, {	header: 'Tempat Lahir', dataIndex: 'tempatlahir', sortable: true, filter: true, hidden: true, width: 90
								} ]
							});
						}
					}
				});
				
				if (! Renderer.AllowedAccess('KeluargaWrite')) {
					Ext.getCmp('DeleteKeluargaED').hide();
				}
			},
			hide: function(w) {
				w.destroy();
				w = WinKeluarga = null;
			}
		},
		SaveKeluarga: function() {
			var Param = new Object();
			Param.Action = 'EditKeluarga';
			Param.KeluargaID = WinKeluarga.KeluargaID;
			Param.nama = WinKeluarga.nama.getValue();
			Param.alamat = WinKeluarga.alamat.getValue();
			Param.nomor = WinKeluarga.nomor.getValue();
			Param.no_kk = WinKeluarga.no_kk.getValue();
			Param.no_hp = WinKeluarga.no_hp.getValue();
			Param.idgereja = WinKeluarga.gereja.getValue();
			Param.sektor_id = WinKeluarga.sektor.getValue();
			Param.ultah_perkawinan = WinKeluarga.ultah_perkawinan.getValue();
			Param.meninggal = (WinKeluarga.meninggal.getValue() == true) ? 1 : 0;
			
			// Validation
			var Validation = true;
			if (! WinKeluarga.nama.validate()) {
				Validation = false;
			}
			if (! WinKeluarga.nomor.validate()) {
				Validation = false;
			}
			if (! WinKeluarga.gereja.validate()) {
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
						if (Keluarga.CallBack != null) {
							Keluarga.CallBack();
						}
						
						WinKeluarga.hide();
					}
				}
			});
		}
	});
	WinKeluarga.show();
}

function ShowReport(Param) {
    if (Param.ReportName == null) {
        alert('Please enter Report Name');
        return;
    }
    
    var GenerateLink = function(Toolbar, ReportName) {
		var Validation = true;
        var ReportLink = Web.HOST + '/index.php/report/index/' + ReportName + '/?';
        for (var j = 0; j < Toolbar.length; j++) {
            if (Toolbar[j].name != null) {
                var name = Toolbar[j].name;
                var value = Toolbar[j].getValue();
                if (Toolbar[j].xtype == 'datefield') {
                    value = Renderer.DateFormat(Toolbar[j].getValue());
                }
				
				// Validation Form
				if (! Toolbar[j].validate()) {
					Validation = false;
				}
                
                // Validation Data
                value = (value == null) ? '' : value;
                
                // Generate Link
                ReportLink += '&' + name + '=' + value;
            }
        }
        return Validation ? ReportLink : '';
    }
    
	Param.title = (Param.title == null) ? 'Report' : Param.title;
	Param.close = (Param.close == null) ? true : Param.close;
	Param.maximizable = (Param.maximizable == null) ? true : Param.maximizable;
	
	Param.listeners = (Param.listeners == null) ? { } : Param.listeners;
	Param.listeners.hide = function(w) { w.destroy(); w = WinReport = null; }
    if (Param.listeners.show == null && Param.ArrayToolbar == null) {
        Param.listeners.show = function(w) {
            var iframe = '<iframe src="' + URLS.php + '/report/index/' + Param.ReportName + '/" style="width: 100%; height: 100%;"></iframe>';
            w.body.dom.innerHTML = iframe;
        }
    }
	
	Param.buttons = (Param.buttons == null) ? [] : Param.buttons;
	if (Param.close) {
		Param.buttons.push({ text: 'Close', handler: function(w) { WinReport.hide(); } });
	}
	
	var ArrayToolbar = {
		'date_end': {
			FieldName: 'Tanggal Selesai', 
			Config: {
				xtype: 'datefield', format: DATE_FORMAT, width: 100, name: 'date_end', margin: '0 10px 0 0;',
				allowBlank: false, blankText: 'Tanggal Selesai tidak boleh kosong'
			}
		},
		'date_end_day': {
			FieldName: 'Tanggal Selesai', 
			Config: {
				xtype: 'datefield', format: 'd F', width: 100, name: 'date_end', margin: '0 10px 0 0;',
				allowBlank: false, blankText: 'Tanggal Selesai tidak boleh kosong'
			}
		},
		'date_select': {
			FieldName: 'Tanggal', 
			Config: {
				xtype: 'datefield', format: DATE_FORMAT, width: 100, name: 'date_select'
			}
		},
		'date_start': {
			FieldName: 'Tanggal Mulai', 
			Config: {
				xtype: 'datefield', format: DATE_FORMAT, width: 100, name: 'date_start', margin: '0 10px 0 0;',
				allowBlank: false, blankText: 'Tanggal Mulai tidak boleh kosong'
			}
		},
		'date_start_day': {
			FieldName: 'Tanggal Mulai', 
			Config: {
				xtype: 'datefield', format: 'd F', width: 100, name: 'date_start', margin: '0 10px 0 0;',
				allowBlank: false, blankText: 'Tanggal Mulai tidak boleh kosong'
			}
		}
	}
	
	var Tbar = [];
	Param.ArrayToolbar = (Param.ArrayToolbar == null) ? [] : Param.ArrayToolbar;
	for (var i = 0; i < Param.ArrayToolbar.length; i++) {
		var InputName = Param.ArrayToolbar[i];
		if (ArrayToolbar[InputName] != null) {
			// Add Label
			Tbar.push({ xtype: 'label', text: ArrayToolbar[InputName].FieldName + ' : ' });
			
			// Add Form
			Tbar.push(ArrayToolbar[InputName].Config);
		}
	}
	if (Tbar.length > 0) {
		Tbar.push({
			text: 'Generate', iconCls: 'reportIcon', handler: function() {
				for (var i = 0; i < Param.win.dockedItems.items.length; i++) {
					if (Param.win.dockedItems.items[i].dock == 'top' && Param.win.dockedItems.items[i].xtype == 'toolbar') {
                        var ReportLink = GenerateLink(Param.win.dockedItems.items[i].items.items, Param.ReportName);
						if (ReportLink == '') {
							return;
						}
						
						Param.win.body.dom.innerHTML = '<iframe src="' + ReportLink + '" style="width: 100%; height: 100%;"></iframe>';
						break;
					}
				}
			}
		});
	}
	
	var WinReport = new Ext.Window({
		layout: 'fit', width: 800, height: 600, closeAction: 'hide', plain: true,
		maximizable: Param.maximizable, title: Param.title, tbar: Tbar,
		closable: Param.close, resizable: false,
		buttons: Param.buttons, listeners: Param.listeners
	});
	WinReport.show();
	
	Param.win = WinReport;
}

var Func = {
	ArrayToJson: function(Data) {
		var Temp = '';
		for (var i = 0; i < Data.length; i++) {
			Temp = (Temp.length == 0) ? Func.ObjectToJson(Data[i]) : Temp + ',' + Func.ObjectToJson(Data[i]);
		}
		return '[' + Temp + ']';
	},
	ObjectToJson: function(obj) {
		var str = '';
		for (var p in obj) {
			if (obj.hasOwnProperty(p)) {
				if (obj[p] != null) {
					str += (str.length == 0) ? str : ',';
					str += '"' + p + '":"' + obj[p] + '"';
				}
			}
		}
		str = '{' + str + '}';
		return str;
	},
	InArray: function(Value, Array) {
		var Result = false;
		for (var i = 0; i < Array.length; i++) {
			if (Value == Array[i]) {
				Result = true;
				break
			}
		}
		return Result;
	},
	IsEmpty: function(value) {
		var Result = false;
		if (value == null || value == 0) {
			Result = true;
		} else if (typeof(value) == 'string') {
			value = Func.Trim(value);
			if (value.length == 0) {
				Result = true;
			}
		}
		
		return Result;
	},
	ObjectToJson: function(obj) {
		var str = '';
		for (var p in obj) {
			if (obj.hasOwnProperty(p)) {
				if (obj[p] != null) {
					str += (str.length == 0) ? str : ',';
					str += p + ":'" + obj[p] + "'";
				}
			}
		}
		str = '{' + str + '}';
		return str;
	},
	SyncComboParam: function(c, Param) {
		var ArrayConfig = ['renderTo', 'name', 'fieldLabel', 'anchor', 'id', 'allowBlank', 'blankText', 'tooltip', 'iconCls', 'width', 'listeners', 'value'];
		for (var i = 0; i < ArrayConfig.length; i++) {
			if (Param[ArrayConfig[i]] != null) {
				c[ArrayConfig[i]] = Param[ArrayConfig[i]];
			}
		}
		return c;
	},
	Trim: function(value) {
		return value.replace(/^\s+|\s+$/g,'');
	},
	GetSektor: function(Value) {
		var Result = null;
		var ArrayValue = Value.split(',');
		if (ArrayValue.length > 1) {
			var Result = ArrayValue[0]
		} else {
			Result = Value;
		}
		
		return Result;
	}
}

var Store = {
	Gereja: function() {
		var Store = new Ext.create('Ext.data.Store', {
			fields: ['id', 'nama', 'alamat', 'kota'],
			autoLoad: false, proxy: {
				type: 'ajax', extraParams: { Action: 'Gereja' },
				url: Web.HOST + '/administrator/combo',
				reader: { type: 'json', root: 'res' },
				actionMethods: { read: 'POST' }
			}
		});
		
		return Store;
	},
	Jemaat: function() {
		var Store = new Ext.create('Ext.data.Store', {
			fields: ['id', 'nama', 'alamat', 'kota'],
			autoLoad: false, proxy: {
				type: 'ajax', extraParams: { Action: 'Jemaat' },
				url: Web.HOST + '/administrator/combo',
				reader: { type: 'json', root: 'res' },
				actionMethods: { read: 'POST' }
			}
		});
		
		return Store;
	},
	JenisBiaya: function() {
		var Store = new Ext.create('Ext.data.Store', {
			fields: ['jenis_biaya_id', 'jenis_biaya', 'is_income'],
			autoLoad: true, proxy: {
				type: 'ajax', extraParams: { Action: 'JenisBiaya' },
				url: Web.HOST + '/administrator/combo',
				reader: { type: 'json', root: 'res' },
				actionMethods: { read: 'POST' }
			}
		});
		
		return Store;
	},
	MetodeKirim: function() {
		var Store = new Ext.create('Ext.data.Store', {
			fields: ['metode_kirim_id', 'metode_kirim'],
			autoLoad: true, proxy: {
				type: 'ajax', extraParams: { Action: 'MetodeKirim' },
				url: Web.HOST + '/administrator/combo',
				reader: { type: 'json', root: 'res' },
				actionMethods: { read: 'POST' }
			}
		});
		
		return Store;
	},
	SasaranJemaat: function() {
		return [['1', 'Semua Jemaat'], ['2', 'Anggota']];
	},
	Sektor: function() {
		var Store = Ext.create('Ext.data.TreeStore', {
			folderSort: true,
			sorters: [{ property: 'text', direction: 'ASC' }],
			root: { text: 'Sektor', id: '0', expanded: true },
			proxy: { type: 'ajax', url: Web.HOST + '/keluarga/sektor/combo' }
		});
		
		return Store;
	},
	TagihanType: function() {
		var Store = new Ext.create('Ext.data.Store', {
			fields: ['tagihan_type_id', 'tagihan_type', 'tagihan_config'],
			autoLoad: true, proxy: {
				type: 'ajax', extraParams: { Action: 'TagihanType' },
				url: Web.HOST + '/administrator/combo',
				reader: { type: 'json', root: 'res' },
				actionMethods: { read: 'POST' }
			}
		});
		
		return Store;
	}
}

var Combo = {
	Param: {
		Gereja: function(Param) {
			var width = (Param.width == null) ? 145 : Param.width;
			
			var p = {
				xtype: 'combo', store: Store.Gereja(), width: width, minChars: 1, selectOnFocus: true,
				valueField: 'id', displayField: 'nama',
				readonly: true, editable: false
			}
			p = Func.SyncComboParam(p, Param);
			
			return p;
		},
		Jemaat: function(Param) {
			var width = (Param.width == null) ? 145 : Param.width;
			
			var p = {
				xtype: 'combo', store: Store.Jemaat(), width: width, minChars: 1, selectOnFocus: true,
				valueField: 'id', displayField: 'nama',
				readonly: false, editable: true
			}
			p = Func.SyncComboParam(p, Param);
			
			return p;
		},
		JenisBiaya: function(Param) {
			var width = (Param.width == null) ? 145 : Param.width;
			
			var p = {
				xtype: 'combo', store: Store.JenisBiaya(), width: width, minChars: 1, selectOnFocus: true,
				valueField: 'jenis_biaya_id', displayField: 'jenis_biaya',
				readonly: true, editable: false
			}
			p = Func.SyncComboParam(p, Param);
			
			return p;
		},
		MetodeKirim: function(Param) {
			var width = (Param.width == null) ? 145 : Param.width;
			
			var p = {
				xtype: 'combo', store: Store.MetodeKirim(), width: width, minChars: 1, selectOnFocus: true,
				valueField: 'metode_kirim_id', displayField: 'metode_kirim',
				readonly: true, editable: false
			}
			p = Func.SyncComboParam(p, Param);
			
			return p;
		},
		SasaranJemaat: function(Param) {
			var width = (Param.width == null) ? 145 : Param.width;
			
			var p = {
				xtype: 'combo', store: Store.SasaranJemaat(), width: width, minChars: 1, selectOnFocus: true,
				readonly: true, editable: false
			}
			p = Func.SyncComboParam(p, Param);
			
			return p;
		},
		Sektor: function(Param) {
			var width = (Param.width == null) ? 245 : Param.width;
			
			var p = {
				store: Store.Sektor(), width: width, selectChildren: false, canSelectFolders: true
			}
			p = Func.SyncComboParam(p, Param);
			
			return p;
		},
		TagihanType: function(Param) {
			var width = (Param.width == null) ? 145 : Param.width;
			
			var p = {
				xtype: 'combo', store: Store.TagihanType(), width: width, minChars: 1, selectOnFocus: true,
				valueField: 'tagihan_type_id', displayField: 'tagihan_type', readonly: true, editable: false
			}
			p = Func.SyncComboParam(p, Param);
			
			return p;
		}
	}
}

Combo.Class = {
	Gereja: function(Param) {
		var c = new Ext.form.ComboBox(Combo.Param.Gereja(Param));
		return c;
	},
	Jemaat: function(Param) {
		var c = new Ext.form.ComboBox(Combo.Param.Jemaat(Param));
		return c;
	},
	JenisBiaya: function(Param) {
		var c = new Ext.form.ComboBox(Combo.Param.JenisBiaya(Param));
		return c;
	},
	MetodeKirim: function(Param) {
		var c = new Ext.form.ComboBox(Combo.Param.MetodeKirim(Param));
		return c;
	},
	SasaranJemaat: function(Param) {
		var c = new Ext.form.ComboBox(Combo.Param.SasaranJemaat(Param));
		return c;
	},
	Sektor: function(Param) {
		var c = Ext.create('Ext.ux.TreeCombo', Combo.Param.Sektor(Param) );
		return c;
	},
	TagihanType: function(Param) {
		var c = new Ext.form.ComboBox(Combo.Param.TagihanType(Param));
		return c;
	}
}