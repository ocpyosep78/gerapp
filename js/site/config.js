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
	
	var BackgroundLoginID = 8;
	var AccessGerejaID = Ext.get('AccessGerejaID').getValue();
	
	if (AccessGerejaID == 0) {
		var StoreImage = [['0', 'Logo Gereja'], [BackgroundLoginID, 'Background Login']];
	} else {
		var StoreImage = [['0', 'Logo Gereja']];
	}
	
	Ext.define('Config', {
		extend: 'Ext.data.Model',
		fields: [
			{ name: 'config_id', type: 'int' },
			{ name: 'config_name', type: 'string' },
			{ name: 'gereja', type: 'string' },
			{ name: 'config', type: 'string' }
		]
	});
	
	var ConfigStore = Ext.create('Ext.data.Store', {
		model: 'Config', autoLoad: true, pageSize: 25, remoteSort: true,
        sorters: [{ property: 'config_name', direction: 'ASC' }],
		proxy: {
			type: 'ajax', extraParams: { hidden: 0 },
			url : Web.HOST + '/site/config/grid', actionMethods: { read: 'POST' },
			reader: { type: 'json', root: 'rows', totalProperty: 'totalCount' }
		}
	});
    
	var ConfigGrid = Ext.create('Ext.grid.Panel', {
		viewConfig: { forceFit: true }, store: ConfigStore, height: 400, renderTo: Ext.get('grid-member'),
		features: [{ ftype: 'filters', encode: true, local: false }], layout: 'fit',
		columns: [ {
				header: 'Content Name', dataIndex: 'config_name', sortable: true, filter: true, width: 200
		}, {	header: 'Gereja', dataIndex: 'gereja', sortable: true, filter: true, width: 150
		}, {	header: 'Content', dataIndex: 'config', sortable: true, filter: true, width: 100, flex: 1
		} ],
		tbar: [ {
				text: 'Tambah', iconCls: 'addIcon', tooltip: 'Tambah Config', id: 'AddTB', handler: function() { CallWindowConfig({ config_id: 0 }); }
			}, '-', {
				text: 'Ubah', iconCls: 'editIcon', tooltip: 'Ubah Config', id: 'UpdateTB', handler: function() { ConfigGrid.Update({ }); }
			}, '-', {
				text: 'Hapus', iconCls: 'delIcon', tooltip: 'Hapus Config', id: 'DeleteTB', handler: function() {
					if (ConfigGrid.getSelectionModel().getSelection().length == 0) {
						Ext.Msg.alert('Information', 'Please choose record.');
						return false;
					}
					
					Ext.MessageBox.confirm('Confirmation', 'Are you sure ?', ConfigGrid.Delete);
				}
			}, '-', {
				text: 'Upload Logo / Background', iconCls: 'editIcon', tooltip: 'Upload Logo', id: 'UploadTB', handler: function() { CallWinLogo(); }
			}, '->', {
                id: 'SearchPM', xtype: 'textfield', emptyText: 'Search', width: 80, listeners: {
                    'specialKey': function(field, el) {
                        if (el.getKey() == Ext.EventObject.ENTER) {
                            var value = Ext.getCmp('SearchPM').getValue();
                            if ( value ) {
								var Param = ConfigGrid.GetParam();
								ConfigGrid.LoadGrid(Param);
                            }
                        }
                    }
                }
            }, '-', {
				text: 'Reset', tooltip: 'Reset search', iconCls: 'refreshIcon', handler: function() {
					ConfigGrid.LoadGrid({ Reset: 1 });
				}
		} ],
		bbar: new Ext.PagingToolbar( {
			store: ConfigStore, displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: 'No topics to display'
		} ),
		listeners: {
			'itemdblclick': function(model, records) {
				ConfigGrid.Update({ });
            }
        },
		GetParam: function() {
			var Param = { NameLike: Ext.getCmp('SearchPM').getValue() };
			
			return Param;
		},
		LoadGrid: function(Param) {
			Param.Reset = (Param.Reset == null) ? 0 : Param.Reset;
			
			if (Param.Reset == 1) {
				ConfigStore.proxy.extraParams = { hidden: 0 }
			} else {
				ConfigStore.proxy.extraParams = Param;
			}
			
			ConfigStore.load();
		},
		Update: function(Param) {
			var Data = ConfigGrid.getSelectionModel().getSelection();
			if (Data.length == 0) {
				Ext.Msg.alert('Information', 'Please choose record.');
				return false;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/site/config/action',
				params: { Action: 'GetConfigByID', config_id: Data[0].data.config_id },
				success: function(Result) {
					eval('var Record = ' + Result.responseText)
					CallWindowConfig(Record);
				}
			});
		},
		Delete: function(Value) {
			if (Value == 'no') {
				return;
			}
			
			Ext.Ajax.request({
				url: Web.HOST + '/site/config/action',
				params: { Action: 'DeteleConfigByID', config_id: ConfigGrid.getSelectionModel().getSelection()[0].data.config_id },
				success: function(TempResult) {
					eval('var Result = ' + TempResult.responseText)
					
					Renderer.FlashMessage(Result.Message);
					if (Result.QueryStatus == '1') {
						ConfigStore.load();
					}
				}
			});
		}
	});
	
	function CallWindowConfig(Config) {
		var WinConfig = new Ext.Window({
			layout: 'fit', width: 675, height: 430,
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Save', handler: function() { WinConfig.Save(); }
				}, {	text: 'Close', handler: function() {
						WinConfig.hide();
				}
			}],
			listeners: {
				show: function(w) {
					var Title = (Config.config_id == 0) ? 'Entry Config - [New]' : 'Entry Config - [Edit]';
					w.setTitle(Title);
					
					Ext.Ajax.request({
						url: Web.HOST + '/site/config/view/',
						success: function(Result) {
							w.body.dom.innerHTML = Result.responseText;
							
							WinConfig.config_id = Config.config_id;
							WinConfig.config_name = new Ext.form.TextField({ renderTo: 'config_nameED', width: 575, allowBlank: false, blankText: 'Name cannot be empty' });
							WinConfig.gereja = Combo.Class.Gereja({ renderTo: 'gerejaED', width: 300 });
							WinConfig.config = new Ext.form.HtmlEditor({ renderTo: 'configED', width: 575, height: 300, enableFont: false })
							
							// Populate Record
							if (Config.config_id > 0) {
								WinConfig.config_name.setValue(Config.config_name);
								WinConfig.config.setValue(Config.config);
								
								Ext.Ajax.request({
									url: Web.HOST + '/administrator/combo',
									params: { Action : 'Gereja', ForceDisplayID: Config.gereja_id },
									success: function(Result) {
										WinConfig.gereja.store.loadData(eval(Result.responseText));
										WinConfig.gereja.setValue(Config.gereja_id);
									}
								});
							}
						}
					});
				},
				hide: function(w) {
					w.destroy();
					w = WinConfig = null;
				}
			},
			Save: function() {
				var Param = new Object();
				Param.Action = 'UpdateConfig';
				Param.config_id = WinConfig.config_id;
				Param.config_name = WinConfig.config_name.getValue();
				Param.gereja_id = WinConfig.gereja.getValue();
				Param.config = WinConfig.config.getValue();
				
				// Validation
				var Validation = true;
				if (! WinConfig.config_name.validate()) {
					Validation = false;
				}
				if (! Validation) {
					return;
				}
				
				Ext.Ajax.request({
					params: Param,
					url: Web.HOST + '/site/config/action',
					success: function(TempResult) {
						eval('var Result = ' + TempResult.responseText);
						Renderer.FlashMessage(Result.Message);
                        
                        if (Result.QueryStatus == '1') {
							ConfigStore.load();
							WinConfig.hide();
                        }
					}
				});
			}
		});
		WinConfig.show();
	}
	
	function CallWinLogo() {
		var Win = new Ext.Window({
			layout: 'fit', width: 350, height: 240, title: 'Upload Logo / Background',
			closeAction: 'hide', plain: true, modal: true,
			buttons: [ {
						text: 'Upload', handler: function() { Win.Upload(); }
				}, {	text: 'Close', handler: function() {
						Win.hide();
				}
			}],
			listeners: {
				show: function(w) {
					var Content = '<div>';
					Content += '<div style="padding: 5px; background: #FFFFFF; width: 400px;">';
					Content += '<div style="float: left; width: 100px; padding: 3px 5px 12px 0; text-align: right;">Gereja :</div>';
					Content += '<div style="float: left; width: 230px;"><div id="gerejaED"></div></div>';
					Content += '<div class="clear"></div>';
					Content += '<div style="float: left; width: 100px; padding: 3px 5px 12px 0; text-align: right;">Image Type :</div>';
					Content += '<div style="float: left; width: 230px;"><div id="image_typeED"></div></div>';
					Content += '<div class="clear"></div>';
					Content += '<div style="float: left; width: 100px; padding: 3px 5px 12px 0; text-align: right;">Upload Photo :</div>';
					Content += '<div style="float: left; width: 230px;"><div id="photo_uploadED"></div></div>';
					Content += '<div class="clear"></div>';
					Content += '<div style="float: left; width: 100px; padding: 3px 5px 12px 0; text-align: right;">Current Photo :</div>';
					Content += '<div style="float: left; width: 230px; height: 85px">';
					Content += '<div class="AvatarImage" id="current_logo"><img src="' + Web.HOST + '/images/default-image.png" /></div>';
					Content += '</div>';
					Content += '<div class="clear"></div>';
					Content += '</div>';
					w.body.dom.innerHTML = Content;
					
					Win.image_type = new Ext.form.ComboBox({
						store: StoreImage, width: 225,
						typeAhead: false, renderTo: 'image_typeED', readonly: true, editable: false,
						allowBlank: false, blankText: 'Image Type cannot be empty', listeners: {
							select: function(combo, records) {
								var AjaxParam = {
									Action: 'GetConfigByID',
									config_id: combo.getValue()
								}
								if (AjaxParam.config_id == 0) {
									AjaxParam.ImageTypeText = 'Logo Gereja';
								}
								
								Ext.Ajax.request({
									url: Web.HOST + '/site/config/action', params: AjaxParam,
									success: function(Result) {
										eval('var Record = ' + Result.responseText);
										var LinkLogo = Web.HOST + '/images/logo/' + Record.config;
										Ext.get('current_logo').dom.innerHTML = '<img src="' + LinkLogo + '" />';
									}
								});
							}
						}
					});
					Win.gereja = Combo.Class.Gereja({ renderTo: 'gerejaED', width: 225 });
					Win.photo_upload = Ext.create('Ext.form.field.File', { name: 'photo_upload', renderTo: 'photo_uploadED', width: 225, hideLabel: true });
					
					if (AccessGerejaID != 0) {
						Ext.Ajax.request({
							url: Web.HOST + '/administrator/combo',
							params: { Action : 'Gereja', ForceDisplayID: AccessGerejaID },
							success: function(Result) {
								Win.gereja.store.loadData(eval(Result.responseText));
								Win.gereja.setValue(AccessGerejaID);
							}
						});
					}
				},
				hide: function(w) {
					w.destroy();
					w = Win = null;
				}
			},
			Upload: function() {
				var Param = new Object();
				Param.Action = 'UpdateConfig';
				Param.config_id = Win.image_type.getValue();
				Param.gereja_id = Win.gereja.getValue();
				
				if (Param.config_id == 0) {
					Param.ImageTypeText = Win.image_type.getRawValue();
				}
				
				// Validation
				var Validation = true;
				if (! Win.image_type.validate()) {
					Validation = false;
				}
				if (! Validation) {
					return;
				}
				
				var Form = new Ext.FormPanel({
                    renderTo: 'FormUpload', fileUpload: true,
                    width: 500, frame: true, autoHeight: true, labelWidth: 50, 
                    items: [
						{ 	xtype: 'textfield', fieldLabel: 'Action', name: 'Action', value: 'UploadImage' },
						Win.photo_upload
                    ],
                    defaults: { anchor: '95%', allowBlank: false, msgTarget: 'side' }
                });
                
                Form.getForm().submit({
                    url: Web.HOST + '/site/upload/',
                    waitMsg: 'Upload Document ...',
                    success: function(DataForm, Ajax) {
                        Ext.get('FormUpload').dom.innerHTML = '';
						Param.config = (Ajax.result.UploadPhoto != null && Ajax.result.UploadPhoto.length > 0) ? Ajax.result.UploadPhoto : null;
                        
						// Validation
						if (Param.config_id == BackgroundLoginID) {
							if (Ajax.result.width > 640 || Ajax.result.height > 480) {
								Ajax.result.UploadStatus = 0;
								Ajax.result.Message = 'Ukuran gambar yang diijinkan maksimal 640 x 480';
							}
						}
						
                        if (Ajax.result.UploadStatus == 1 && Param.config != null) {
							Ext.Ajax.request({
								params: Param,
								url: Web.HOST + '/site/config/action',
								success: function(TempResult) {
									eval('var Result = ' + TempResult.responseText);
									Renderer.FlashMessage(Result.Message);
									
									if (Result.QueryStatus == '1') {
										Win.hide();
									}
								}
							});
                        } else {
							Renderer.FlashMessage(Ajax.result.Message);
							Win.photo_upload = Ext.create('Ext.form.field.File', { name: 'photo_upload', renderTo: 'photo_uploadED', width: 225, hideLabel: true });
						}
                    }
                });
			}
		});
		Win.show();
	}
	
	Renderer.InitWindowSize({ Panel: -1, Grid: ConfigGrid, Toolbar: 70 });
    Ext.EventManager.onWindowResize(function() {
		Renderer.InitWindowSize({ Panel: -1, Grid: ConfigGrid, Toolbar: 70 });
    }, ConfigGrid);
});