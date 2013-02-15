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
    
	Ext.define('GroupModel', {
		extend: 'Ext.data.Model',
		fields: [
			{ name: 'group_id', type: 'int' },
			{ name: 'group_name', type: 'string' }
		]
	});
	var GroupStore = Ext.create('Ext.data.Store', {
		model: 'GroupModel', autoLoad: true, pageSize: 25, remoteSort: true,
        sorters: [{ property: 'group_name', direction: 'ASC' }],
		proxy: {
			type: 'ajax', extraParams: { RequestName: 'Group' },
			url : Web.HOST + '/administrator/grid',
			reader: { root: 'GroupData', totalProperty: 'GroupCount' }
		}
	});
    
	var GroupGrid = new Ext.grid.GridPanel({
		viewConfig: { forceFit: true }, store: GroupStore, height: 335, renderTo: 'grid-member',
		features: [{ ftype: 'filters', encode: true, local: false }],
		columns: [ {
					header: 'Group Name', dataIndex: 'group_name', sortable: true, filter: true, width: 400, flex: 1
		} ],
		tbar: [ {
				text: 'Ubah', width: 100, iconCls: 'editIcon', tooltip: 'Ubah user', id: 'UpdateTB', handler: function() { GroupGrid.Update({ }); }
			}, '->', {
                id: 'SearchPM', xtype: 'textfield', tooltip: 'Cari user', emptyText: 'Cari', width: 100, listeners: {
                    'specialKey': function(field, el) {
                        if (el.getKey() == Ext.EventObject.ENTER) {
                            var value = Ext.getCmp('SearchPM').getValue();
                            if ( value ) {
								GroupGrid.LoadGrid({ RequestName: 'Group', NameLike: value });
                            }
                        }
                    }
                }
            }, '-', {
				text: 'Reset', tooltip: 'Reset pencarian', iconCls: 'refreshIcon', width: 100, handler: function() {
					GroupGrid.LoadGrid({ RequestName: 'Group' });
				}
		} ],
		bbar: new Ext.PagingToolbar( {
			store: GroupStore, displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: 'No topics to display'
		} ),
		listeners: {
			'itemdblclick': function(model, records) {
				if (! Renderer.AllowedWrite()) {
					return;
				}
				
				GroupGrid.Update({ });
            }
        },
		LoadGrid: function(Param) {
			GroupStore.proxy.extraParams = Param;
			GroupStore.load();
		},
		Update: function(Param) {
			var Data = GroupGrid.getSelectionModel().getSelection();
			if (Data.length == 0) {
				Ext.Msg.alert('Informasi', 'Silahkan memilih data.');
				return false;
			}
			
			CallWindowGroup({ GroupID: Data[0].data.group_id });
		}
	});
	
	function CallWindowGroup(Group) {
		var WinGroup = new Ext.Window({
			layout: 'fit', width: 600, height: 400, title: 'Entry Group - [Edit]',
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Close', handler: function() {
						WinGroup.hide();
				}
			}],
			listeners: {
				show: function(w) {
					w.body.dom.innerHTML = '<div id="GridPermission"></div>';
					
					Ext.define('PermissionModel', {
						extend: 'Ext.data.Model',
						fields: [
							{ name: 'ModulName', type: 'string' },
							{ name: 'Title', type: 'string' },
							{ name: 'Read', type: 'string' },
							{ name: 'ReadID', type: 'int' },
							{ name: 'Write', type: 'string' },
							{ name: 'WriteID', type: 'int' }
						]
					});
					WinGroup.Store = Ext.create('Ext.data.Store', {
						model: 'PermissionModel', autoLoad: true, pageSize: 25, remoteSort: true,
						sorters: [{ property: 'ModulName', direction: 'ASC' }],
						proxy: {
							type: 'ajax', extraParams: { RequestName: 'Permission', GroupID: Group.GroupID },
							url : Web.HOST + '/administrator/grid',
							reader: { root: 'PermissionData', totalProperty: 'PermissionCount' }
						}
					});
					
					WinGroup.Grid = new Ext.grid.GridPanel({
						viewConfig: { forceFit: true }, store: WinGroup.Store, height: 335, renderTo: 'GridPermission',
						columns: [ {
									header: 'Permission', dataIndex: 'Title', width: 400, flex: 1
							}, {	xtype: 'actioncolumn', header: 'Read', width: 80, align: 'center',
									items: [ {
										getClass: function(v, meta, rec) {
											if (rec.get('Read') == 0) {
												this.items[0].tooltip = 'Tidak Memiliki Hak Akses';
												return 'delIcon';
											} else {
												this.items[0].tooltip = 'Memiliki Hak Akses';
												return 'acceptIcon';
											}
										},
										handler: function(grid, rowIndex, colIndex) {
											var rec = WinGroup.Store.getAt(rowIndex);
											
											Ext.Ajax.request({
												params: {
													Action: 'UpdatePermission', group_id: Group.GroupID,
													perm_id: rec.data.ReadID, IsInsert: (rec.data.Read == 0) ? 1 : 0
												},
												url: Web.HOST + '/administrator/ajax',
												success: function(TempResult) {
													eval('var Result = ' + TempResult.responseText);
													WinGroup.Store.load();
												}
											});
										}
									} ]
							}, {	xtype: 'actioncolumn', header: 'Write', width: 80, align: 'center',
									items: [ {
										getClass: function(v, meta, rec) {
											if (rec.get('Write') == 0) {
												this.items[0].tooltip = 'Tidak Memiliki Hak Akses';
												return 'delIcon';
											} else {
												this.items[0].tooltip = 'Memiliki Hak Akses';
												return 'acceptIcon';
											}
										},
										handler: function(grid, rowIndex, colIndex) {
											var rec = WinGroup.Store.getAt(rowIndex);
											
											Ext.Ajax.request({
												params: {
													Action: 'UpdatePermission', group_id: Group.GroupID,
													perm_id: rec.data.WriteID, IsInsert: (rec.data.Write == 0) ? 1 : 0
												},
												url: Web.HOST + '/administrator/ajax',
												success: function(TempResult) {
													eval('var Result = ' + TempResult.responseText);
													WinGroup.Store.load();
												}
											});
										}
									} ]
						} ],
						bbar: new Ext.PagingToolbar( {
							store: WinGroup.Store, displayInfo: true,
							displayMsg: 'Displaying topics {0} - {1} of {2}',
							emptyMsg: 'No topics to display'
						} )
					});
				},
				hide: function(w) {
					w.destroy();
					w = WinGroup = null;
				}
			}
		});
		WinGroup.show();
	}
	
	if (! Renderer.AllowedWrite()) {
		Ext.getCmp('UpdateTB').disable();
	}
	
	Renderer.InitWindowSize({ Panel: -1, Grid: GroupGrid, Toolbar: 70 });
});