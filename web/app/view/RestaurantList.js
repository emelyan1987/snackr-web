
Ext.define('app.view.RestaurantList', {
    extend: 'Ext.grid.Panel',
    xtype: 'restaurantlist',

    //store: 'Restaurants',
    //title: 'RestaurantList',
    hideHeaders: true,
    autoScroll: true,

    initComponent: function() { 
        var me = this;
        
        var store = Ext.create('app.store.Restaurants');
        me.store = store;
        me.tbar = [{
            iconCls: 'icon-new',
            tooltip: 'Add Restaurant',
            handler: function(btn){
                var win = Ext.create('app.view.RestaurantForm', {
                    listeners: {
                        saved: function(){
                            win.close();
                            me.getStore().reload();
                        }
                    }
                });
                win.show(btn.id);
            }
            },{
                iconCls: 'icon-remove',
                tooltip: 'Delete Restaurant',
                handler: function(){
                    var sel = me.getSelectionModel().getSelection()[0]; 
                    if(sel!=null){
                        Ext.MessageBox.confirm("Confirm", "Are you delete " + sel.get('title') + "?", function(btn){
                            
                            if(btn == 'yes') {
                                
                                Ext.Ajax.request({
                                    url: '../rest/delete',
                                    method: 'POST', 
                                    timeout: 20000,
                                    params: {
                                        id: sel.get("id")
                                    },
                                    success: function(response){
                                        me.getStore().reload();
                                    }
                                });    
                            }
                        });
                    }
                }
        },{
            iconCls: 'icon-refresh',
            tooltip: 'refresh',
            handler: function() {
                me.getStore().reload();
            }
        },'->',{      
            iconCls: 'icon-warning',
            tooltip: 'not published',
            enableToggle: true,
            toggleHandler: function(btn,state) {
                me.getStore().load({
                    params: {
                        is_published: state?0:1
                    }
                });
            }
        }]; 
        me.columns = [{
            text: 'title',
            dataIndex: 'title',
            flex: 1
        }];
        
        me.listeners = {
            itemdblclick: function(view, record, item) {
                var win = Ext.create('app.view.RestaurantForm', {
                    listeners: {
                        saved: function(){
                            win.close();
                            me.getStore().reload();
                        }
                    }
                });
                win.show(item.id);
                win.down('form').loadRecord(record);
                win.showMap(record.get('location'));
            }  
        },
        
        me.bbar = Ext.create('Ext.toolbar.Paging',{
            store: store
        });
        
        /*me.selModel = {
            pruneRemoved: false
        }*/
        
        me.callParent();
        
        
        
        store.load({
            params: {
                is_published: 1
            }
        });
    }
});