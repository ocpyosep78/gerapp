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
	
	var ConfigStore = new Ext.create('Ext.data.Store', {
		fields:[ 'log_id', 'user_id', 'gereja_id', 'log_datetime', 'log_action', 'UserName', 'GerejaName' ],
		autoLoad: true, pageSize: 25, remoteSort: true,
        sorters: [{ property: 'log_datetime', direction: 'DESC' }],
		proxy: {
			type: 'ajax', extraParams: { hidden: 0 },
			url : Web.HOST + '/site/log/grid', actionMethods: { read: 'POST' },
			reader: { type: 'json', root: 'rows', totalProperty: 'totalCount' }
		}
	});
	
	var ConfigGrid = Ext.create('Ext.grid.Panel', {
		viewConfig: { forceFit: true }, store: ConfigStore, height: 400, renderTo: Ext.get('grid-member'),
		features: [{ ftype: 'filters', encode: true, local: false }], layout: 'fit',
		columns: [ {
				header: 'Date Time', dataIndex: 'log_datetime', sortable: true, filter: true, width: 150
		}, {	header: 'Gereja', dataIndex: 'GerejaName', sortable: true, filter: true, width: 150
		}, {	header: 'Admin', dataIndex: 'UserName', sortable: true, filter: true, width: 150
		}, {	header: 'Message', dataIndex: 'log_action', sortable: true, filter: true, width: 150, flex: 1
		} ],
		bbar: new Ext.PagingToolbar( {
			store: ConfigStore, displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: 'No topics to display'
		} )
	});
	
	Renderer.InitWindowSize({ Panel: -1, Grid: ConfigGrid, Toolbar: 70 });
    Ext.EventManager.onWindowResize(function() {
		Renderer.InitWindowSize({ Panel: -1, Grid: ConfigGrid, Toolbar: 70 });
    }, ConfigGrid);
});