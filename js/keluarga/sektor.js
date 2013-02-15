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
	
    var SektorStore = Ext.create('Ext.data.TreeStore', {
		fields:[ 'sektor_id', 'sektor' ], folderSort: true,
        proxy: { type: 'ajax', url: Web.HOST + '/keluarga/sektor/grid', actionMethods: { read: 'POST' } }
    });
	
    var SektorGrid = Ext.create('Ext.tree.Panel', {
		title: 'Sector',
        height: 400, renderTo: 'grid-member', rootVisible: false, store: SektorStore,
        columns: [ { xtype: 'treecolumn', text: 'Sektor', sortable: true, dataIndex: 'sektor', flex: 1 } ],
		tbar: [ {
				text: 'Tambah', iconCls: 'addIcon', tooltip: 'Tambah Sektor', id: 'AddTB', handler: function() {
					var val = Ext.getCmp('GerejaTB').getValue();
					if (val == null) {
						Ext.getCmp('GerejaTB').markInvalid('Masukkan Gereja');
						return;
					}
					
					CallWin({ sektor_id: 0 });
				}
			}, '-', {
				text: 'Ubah', iconCls: 'editIcon', tooltip: 'Ubah Sektor', id: 'UpdateTB', handler: function() { SektorGrid.Update({ }); }
			}, '-', {
				text: 'Hapus', iconCls: 'delIcon', tooltip: 'Hapus Sektor', id: 'DeleteTB', handler: function() {
					if (SektorGrid.getSelectionModel().getSelection().length == 0) {
						Ext.Msg.alert('Information', 'Please choose record.');
						return false;
					}
					
					Ext.MessageBox.confirm('Confirmation', 'Are you sure ?', SektorGrid.Delete);
				}
			}, '->', {
				xtype: 'label', text: 'Gereja', id: 'GerejaTextTB'
			}, '-', Combo.Param.Gereja({ id: 'GerejaTB', listeners: {
				select: function(combo, record, eOpts) {
					SektorStore.proxy.extraParams.gereja_id = record[0].data.id;
					SektorStore.load();
				}
			} })
		],
		Update: function(Param) {
			var Data = SektorGrid.getSelectionModel().getSelection();
			if (Data.length == 0) {
				Ext.Msg.alert('Information', 'Please choose record.');
				return false;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/keluarga/sektor/action',
				params: { Action: 'GetSektorByID', sektor_id: Data[0].data.sektor_id },
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
				url: Web.HOST + '/keluarga/sektor/action',
				params: { Action: 'DeteleSektorByID', sektor_id: SektorGrid.getSelectionModel().getSelection()[0].data.sektor_id },
				success: function(TempResult) {
					eval('var Result = ' + TempResult.responseText)
					
					Renderer.FlashMessage(Result.Message);
					if (Result.QueryStatus == '1') {
						SektorStore.load();
					}
				}
			});
		}
    });
	
	function CallWin(Param) {
		var treeStore = Ext.create('Ext.data.TreeStore', {
			folderSort: false, root: {
				text: 'Root', id: 'root',
				expanded: true, children: [ {
						id: '1', text: 'First node', expanded: true,
						children: [ {
								id: '3', text: 'First child node', leaf: true
						}, {	id: '4', text: 'Second child node', leaf: true
						} ]
				}, {	id: '2', text: 'Second node', leaf: true
				} ]
			}
		});
		
		var WinPopup = new Ext.Window({
			layout: 'fit', width: 370, height: 130,
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Save', handler: function() { WinPopup.Save(); }
				}, {	text: 'Close', handler: function() {
						WinPopup.hide();
				}
			}],
			listeners: {
				show: function(w) {
					var Title = (Param.sektor_id == 0) ? 'Entry Sektor - [New]' : 'Entry Sektor - [Edit]';
					w.setTitle(Title);
					
					Ext.Ajax.request({
						url: Web.HOST + '/keluarga/sektor/view/',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							WinPopup.sektor_id = Param.sektor_id;
							WinPopup.sektor = new Ext.form.TextField({ renderTo: 'sektorED', width: 245, allowBlank: false, blankText: 'Name cannot be empty' });
							WinPopup.parent = Combo.Class.Sektor({ renderTo: 'parentED' });
							
							// Populate Record
							if (Param.sektor_id > 0) {
								WinPopup.sektor.setValue(Param.sektor);
								WinPopup.parent.setValue(Param.parent_id);
							}
						}
					});
				},
				hide: function(w) {
					w.destroy();
					w = WinPopup = null;
				}
			},
			Save: function() {
				var Param = new Object();
				Param.Action = 'UpdateSektor';
				Param.sektor_id = WinPopup.sektor_id;
				Param.sektor = WinPopup.sektor.getValue();
				Param.parent_id = WinPopup.parent.getValue();
				Param.gereja_id = Ext.getCmp('GerejaTB').getValue();
				
				// Validation
				var Validation = true;
				if (! WinPopup.sektor.validate()) {
					Validation = false;
				}
				if (Param.gereja_id == 0) {
					Ext.getCmp('GerejaTB').markInvalid('Masukkan Gereja');
					Validation = false;
				}
				if (Param.parent_id == 0) {
					WinPopup.parent.markInvalid('Masukkan Parent');
					Validation = false;
				}
				if (! Validation) {
					return;
				}
				
				Ext.Ajax.request({
					params: Param,
					url: Web.HOST + '/keluarga/sektor/action',
					success: function(TempResult) {
						eval('var Result = ' + TempResult.responseText);
						Renderer.FlashMessage(Result.Message);
                        
                        if (Result.QueryStatus == '1') {
							SektorStore.load();
							WinPopup.hide();
                        }
					}
				});
			}
		});
		WinPopup.show();
	}
	
	if (AccessGerejaID != 0) {
		Ext.getCmp('GerejaTB').hide();
		Ext.getCmp('GerejaTextTB').hide();
		Ext.getCmp('GerejaTB').setValue(AccessGerejaID);
	}
	
	Renderer.InitWindowSize({ Panel: -1, Grid: SektorGrid, Toolbar: 70 });
    Ext.EventManager.onWindowResize(function() {
		Renderer.InitWindowSize({ Panel: -1, Grid: SektorGrid, Toolbar: 70 });
    }, SektorGrid);
});