Ext.define( 'Stiki.view.MainPanel', {
    extend: 'Ext.tab.Panel',
    alias: 'widget.mainpanel',

    region: 'center',
    activeTab: 0,
    defaults: {
        padding: '10px'
    },
    
    initComponent: function() {
		function ShowPopup() {
			var GenerateGraph = function() {
				// Chart 1
				var StoreRaw1 = Ext.get('DChart1').dom.innerHTML;
				eval('var StoreTemp1 = ' + StoreRaw1);
				var StorePie1 = Ext.create('Ext.data.JsonStore', {
					fields: ['name', 'jemaat_rekap_total'],
					data: StoreTemp1
				});
				var ChartPie1 = Ext.create('Ext.chart.Chart', {
					width: 370, height: 248, animate: true,
					// renderTo: 'PChart1', 
					store: StorePie1, shadow: true, legend: false, theme: 'Base:gradients',
					axes: [{
						type: 'Numeric', minimum: 0, position: 'left',
						title: 'Jumlah Jemaat', minorTickSteps: 1, fields: ['jemaat_rekap_total'],
						grid: { odd: { opacity: 1, fill: '#ddd', stroke: '#bbb', 'stroke-width': 0.5 } }
					}, {
						type: 'Category', position: 'bottom', fields: ['name'], title: 'Month'
					}],
					series: [{
						xField: 'name', yField: 'jemaat_rekap_total',
						type: 'line', axis: 'left', smooth: true,
						highlight: { size: 7, radius: 7 },
						markerConfig: { type: 'circle', size: 4, radius: 4, 'stroke-width': 0 }
					}]
				});
				var Panel1 = Ext.create('Ext.panel.Panel', {
					height: 250, collapsible: true,
					title: 'Perkembangan Jumlah Jemaat', renderTo: 'PChart1',
					layout: 'fit', items: [ChartPie1]
				});
				
				// Chart 2
				var StoreRaw2 = Ext.get('DChart2').dom.innerHTML;
				eval('var StoreTemp2 = ' + StoreRaw2);
				var StorePie2 = Ext.create('Ext.data.JsonStore', {
					fields: ['sex', 'Total'],
					data: StoreTemp2
				});
				var ChartPie2 = Ext.create('Ext.chart.Chart', {
					width: 385, height: 200, animate: true,
					store: StorePie2, shadow: true, legend: { position: 'right' },
					insetPadding: 40, theme: 'Base:gradients',
					series: [{
						highlight: { segment: { margin: 20 } },
						type: 'pie', field: 'Total', showInLegend: true,
						tips: {
							trackMouse: true, width: 140, height: 28,
							renderer: function(storeItem, item) {
								var total = 0;
								StorePie2.each(function(rec) {
									total += rec.get('Total');
								});
								this.setTitle(storeItem.get('sex') + ': ' + Math.round(storeItem.get('Total') / total * 100) + '%');
							}
						},
						label: {
							field: 'sex', display: 'rotate',
							contrast: true, font: '10px Arial'
						}
					}]
				});
				var Panel2 = Ext.create('Ext.panel.Panel', {
					height: 250, collapsible: true,
					title: 'Jumlah Jemaat berdasarkan Jenis Kelamin', renderTo: 'PChart2',
					layout: 'fit', items: [ChartPie2]
				});
				
				// Chart 3
				var StoreRaw3 = Ext.get('DChart3').dom.innerHTML;
				eval('var StoreTemp3 = ' + StoreRaw3);
				var StorePie3 = Ext.create('Ext.data.JsonStore', {
					fields: ['lengkap', 'Total'],
					data: StoreTemp3
				});
				var ChartPie3 = Ext.create('Ext.chart.Chart', {
					width: 385, height: 200, animate: true,
					store: StorePie3, shadow: true, legend: { position: 'right' },
					insetPadding: 40, theme: 'Base:gradients',
					series: [{
						highlight: { segment: { margin: 20 } },
						type: 'pie', field: 'Total', showInLegend: true,
						tips: {
							trackMouse: true, width: 140, height: 28,
							renderer: function(storeItem, item) {
								var total = 0;
								StorePie3.each(function(rec) {
									total += rec.get('Total');
								});
								this.setTitle(storeItem.get('lengkap') + ': ' + Math.round(storeItem.get('Total') / total * 100) + '%');
							}
						},
						label: {
							field: 'lengkap', display: 'rotate',
							contrast: true, font: '10px Arial'
						}
					}]
				});
				var Panel3 = Ext.create('Ext.panel.Panel', {
					height: 250, collapsible: true,
					title: 'Jumlah Jemaat berdasarkan Data Lengkap', renderTo: 'PChart3',
					layout: 'fit', items: [ChartPie3]
				});
				
				// Chart 4
				var StoreRaw3 = Ext.get('DChart4').dom.innerHTML;
				eval('var StoreTemp4 = ' + StoreRaw3);
				var StorePie4 = Ext.create('Ext.data.JsonStore', {
					fields: ['profesi', 'Total'],
					data: StoreTemp4
				});
				var ChartPie4 = Ext.create('Ext.chart.Chart', {
					width: 385, height: 200, animate: true,
					store: StorePie4, shadow: true, legend: { position: 'right' },
					insetPadding: 40, theme: 'Base:gradients',
					series: [{
						highlight: { segment: { margin: 20 } },
						type: 'pie', field: 'Total', showInLegend: true,
						tips: {
							trackMouse: true, width: 140, height: 28,
							renderer: function(storeItem, item) {
								var total = 0;
								StorePie4.each(function(rec) {
									total += rec.get('Total');
								});
								this.setTitle(storeItem.get('profesi') + ': ' + Math.round(storeItem.get('Total') / total * 100) + '%');
							}
						},
						label: {
							field: 'profesi', display: 'rotate',
							contrast: true, font: '10px Arial'
						}
					}]
				});
				var Panel4 = Ext.create('Ext.panel.Panel', {
					height: 250, collapsible: true,
					title: 'Jumlah Jemaat berdasarkan Pekerjaan', renderTo: 'PChart4',
					layout: 'fit', items: [ChartPie4]
				});
			}
			
			var Win = new Ext.Window({
				layout: 'fit', width: 800, height: 600, title: 'System Statistics',
				closeAction: 'hide', plain: true, modal: true,
				buttons: [ { text: 'Close', handler: function() { Win.hide(); } }],
				listeners: {
					show: function(w) {
						Ext.Ajax.request({
							url: URLS.stiki + '/site/pie_chart',
							success: function(Result) {
								w.body.dom.innerHTML = Result.responseText;
								GenerateGraph();
							}
						});
					},
					hide: function(w) {
						w.destroy();
						w = Win = null;
					}
				}
				
			});
			Win.show();
		}
		
		function ShowGraph() {
			// Panel 1
			var Temp = Ext.get('GridProperty1Store').dom.innerHTML;
			eval('var PropStore1 = ' + Temp);
			var PropGrid1 = Ext.create('Ext.grid.property.Grid', {
				renderTo: 'GridProperty1', height: 175, source: PropStore1,
				propertyNames: { tested: 'QA', borderWidth: 'Border Width' },
				listeners : { beforeedit : function(e) { return false; } }
			});
			Ext.create('Ext.panel.Panel', { title: 'Jumlah Jemaat', renderTo: 'CntBox1', height: 200, collapsible: true, items: [PropGrid1] });
			
			// Panel 2
			var Temp = Ext.get('GridProperty2Store').dom.innerHTML;
			eval('var RawStore2 = ' + Temp);
			var PanelStore2 = Ext.create('Ext.data.Store', {
				fields: ['nama', 'ultah_perkawinan'], data: { items: RawStore2 },
				proxy: { type: 'memory', reader: { type: 'json', root: 'items' } }
			});
			var GridPanel2 = Ext.create('Ext.grid.Panel', {
				height: 200, renderTo: 'CntBox2',
				store: PanelStore2, title: 'Keluarga yang berulang tahun saat ini', collapsible: true,
				columns: [
					{ header: 'Ultah Perkawinan', dataIndex: 'ultah_perkawinan', width: 150 },
					{ header: 'Nama',  dataIndex: 'nama', flex: 1 }
				],
				tools:[{
					type: 'print',
					tooltip: 'Print Report',
					handler: function(event, toolEl, panel) {
						ShowReport({
							title: 'Laporan Ulang Tahun Keluarga', maximizable: false, ReportName: 'keluarga_ultah',
							ArrayToolbar: ['date_start_day', 'date_end_day']
						});
					}
				}]
			});
			
			// Panel 3
			var Temp = Ext.get('GridProperty3Store').dom.innerHTML;
			eval('var RawStore3 = ' + Temp);
			var PanelStore3 = Ext.create('Ext.data.Store', {
				fields: ['nama', 'tgllahir'], data: { items: RawStore3 },
				proxy: { type: 'memory', reader: { type: 'json', root: 'items' } }
			});
			var GridPanel3 = Ext.create('Ext.grid.Panel', {
				height: 200, renderTo: 'CntBox3',
				store: PanelStore3, title: 'Jemaat yang berulang tahun saat ini', collapsible: true,
				columns: [
					{ header: 'Tanggal Lahir', dataIndex: 'tgllahir', width: 150 },
					{ header: 'Nama',  dataIndex: 'nama', flex: 1 }
				],
				tools:[{
					type: 'print',
					tooltip: 'Print Report',
					handler: function(event, toolEl, panel) {
						ShowReport({
							title: 'Laporan Ulang Tahun Jemaat', maximizable: false, ReportName: 'jemaat_ultah',
							ArrayToolbar: ['date_start_day', 'date_end_day']
						});
					}
				}]
			});
			
			// Panel 4
			var Temp = Ext.get('GridProperty4Store').dom.innerHTML;
			eval('var RawStore4 = ' + Temp);
			var PanelStore4 = Ext.create('Ext.data.Store', {
				fields: ['nama', 'UpdateTime'], data: { items: RawStore4 },
				proxy: { type: 'memory', reader: { type: 'json', root: 'items' } }
			});
			var GridPanel4 = Ext.create('Ext.grid.Panel', {
				height: 200, renderTo: 'CntBox4',
				store: PanelStore4, title: 'Data Keluarga Terbaru', collapsible: true,
				columns: [
					{ header: 'Waktu Perubahan', dataIndex: 'UpdateTime', width: 150 },
					{ header: 'Nama',  dataIndex: 'nama', flex: 1 }
				]
			});
			
			// Chart 1
			var StoreRaw1 = Ext.get('Box5Store').dom.innerHTML;
			eval('var StoreTemp1 = ' + StoreRaw1);
			var StorePie1 = Ext.create('Ext.data.JsonStore', {
				fields: ['name', 'jemaat_rekap_total'],
				data: StoreTemp1
			});
			var ChartPie1 = Ext.create('Ext.chart.Chart', {
				width: 370, height: 150, animate: true,
				store: StorePie1, shadow: true, legend: false, theme: 'Base:gradients',
				axes: [{
					type: 'Numeric', minimum: 0, position: 'left',
					title: 'Jumlah Jemaat', minorTickSteps: 1, fields: ['jemaat_rekap_total'],
					grid: { odd: { opacity: 1, fill: '#ddd', stroke: '#bbb', 'stroke-width': 0.5 } }
				}, {
					type: 'Category', position: 'bottom', fields: ['name'], title: 'Month'
				}],
				series: [{
					xField: 'name', yField: 'jemaat_rekap_total',
					type: 'line', axis: 'left', smooth: true,
					highlight: { size: 7, radius: 7 },
					markerConfig: { type: 'circle', size: 4, radius: 4, 'stroke-width': 0 }
				}]
			});
			var Panel1 = Ext.create('Ext.panel.Panel', {
				height: 200, collapsible: true, collapsed: true,
				title: 'Perkembangan Jumlah Jemaat', renderTo: 'CntBox5',
				layout: 'fit', items: [ChartPie1]
			});
			
			// Chart 2
			var StoreRaw2 = Ext.get('Box6Store').dom.innerHTML;
			eval('var StoreTemp2 = ' + StoreRaw2);
			var StorePie2 = Ext.create('Ext.data.JsonStore', {
				fields: ['sex', 'Total'],
				data: StoreTemp2
			});
			var ChartPie2 = Ext.create('Ext.chart.Chart', {
				width: 385, height: 150, animate: true,
				store: StorePie2, shadow: true, legend: { position: 'right' },
				insetPadding: 40, theme: 'Base:gradients',
				series: [{
					highlight: { segment: { margin: 20 } },
					type: 'pie', field: 'Total', showInLegend: true,
					tips: {
						trackMouse: true, width: 140, height: 28,
						renderer: function(storeItem, item) {
							var total = 0;
							StorePie2.each(function(rec) {
								total += rec.get('Total');
							});
							this.setTitle(storeItem.get('sex') + ': ' + Math.round(storeItem.get('Total') / total * 100) + '%');
						}
					},
					label: {
						field: 'sex', display: 'rotate',
						contrast: true, font: '10px Arial'
					}
				}]
			});
			var Panel2 = Ext.create('Ext.panel.Panel', {
				height: 200, collapsible: true, collapsed: true,
				title: 'Jumlah Jemaat berdasarkan Jenis Kelamin', renderTo: 'CntBox6',
				layout: 'fit', items: [ChartPie2]
			});
			
			// Chart 3
			var StoreRaw3 = Ext.get('Box7Store').dom.innerHTML;
			eval('var StoreTemp3 = ' + StoreRaw3);
			var StorePie3 = Ext.create('Ext.data.JsonStore', {
				fields: ['lengkap', 'Total'],
				data: StoreTemp3
			});
			var ChartPie3 = Ext.create('Ext.chart.Chart', {
				width: 385, height: 150, animate: true,
				store: StorePie3, shadow: true, legend: { position: 'right' },
				insetPadding: 40, theme: 'Base:gradients',
				series: [{
					highlight: { segment: { margin: 20 } },
					type: 'pie', field: 'Total', showInLegend: true,
					tips: {
						trackMouse: true, width: 140, height: 28,
						renderer: function(storeItem, item) {
							var total = 0;
							StorePie3.each(function(rec) {
								total += rec.get('Total');
							});
							this.setTitle(storeItem.get('lengkap') + ': ' + Math.round(storeItem.get('Total') / total * 100) + '%');
						}
					},
					label: {
						field: 'lengkap', display: 'rotate',
						contrast: true, font: '10px Arial'
					}
				}]
			});
			var Panel3 = Ext.create('Ext.panel.Panel', {
				height: 200, collapsible: true, collapsed: true,
				title: 'Jumlah Jemaat berdasarkan Data Lengkap', renderTo: 'CntBox7',
				layout: 'fit', items: [ChartPie3]
			});
			
			// Chart 4
			var StoreRaw3 = Ext.get('Box8Store').dom.innerHTML;
			eval('var StoreTemp4 = ' + StoreRaw3);
			var StorePie4 = Ext.create('Ext.data.JsonStore', {
				fields: ['profesi', 'Total'],
				data: StoreTemp4
			});
			var ChartPie4 = Ext.create('Ext.chart.Chart', {
				width: 385, height: 150, animate: true,
				store: StorePie4, shadow: true, legend: { position: 'right' },
				insetPadding: 40, theme: 'Base:gradients',
				series: [{
					highlight: { segment: { margin: 20 } },
					type: 'pie', field: 'Total', showInLegend: true,
					tips: {
						trackMouse: true, width: 140, height: 28,
						renderer: function(storeItem, item) {
							var total = 0;
							StorePie4.each(function(rec) {
								total += rec.get('Total');
							});
							this.setTitle(storeItem.get('profesi') + ': ' + Math.round(storeItem.get('Total') / total * 100) + '%');
						}
					},
					label: {
						field: 'profesi', display: 'rotate',
						contrast: true, font: '10px Arial'
					}
				}]
			});
			var Panel4 = Ext.create('Ext.panel.Panel', {
				height: 200, collapsible: true, collapsed: true,
				title: 'Jumlah Jemaat berdasarkan Pekerjaan', renderTo: 'CntBox8',
				layout: 'fit', items: [ChartPie4]
			});
			
			var MainPanel = Ext.get('MainPanel').getHeight();
			Ext.get('Dashboard').setHeight(MainPanel - 123);
		}
		
        Ext.apply( this, {
            items: [{
                title: 'Home',
				loader: {
					url: 'administrator/welcome', contentType: 'html', autoLoad: true,
					callback: function() {
						ShowGraph();
					} 
				}
            }]
        });
        this.callParent(arguments);
    }
    
});

Ext.define('Stiki.view.ContentTab', {
    extend: 'Ext.container.Container',
    alias: 'widget.contenttab',
    layout: 'fit',
    url: '',
    base: URLS.stiki,

    initComponent: function() {
        this.tpl = new Ext.XTemplate('<iframe style="width: 100%; height: 100%; border: 0; margin:0; padding:0;" src="{base}{url}"></iframe>');
        this.callParent(arguments);
    },

    load: function() {
        this.update(this.tpl.apply(this));
    },

    clear: function() {
        this.update('');
    }
});
