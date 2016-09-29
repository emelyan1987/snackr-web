
Ext.define('app.view.UserList', {
    extend: 'Ext.grid.Panel',
    xtype: 'userlist',

    title: 'UserList',
    hideHeaders: false,
    autoScroll: true,

    initComponent: function() { 
        var me = this; 
        var store = Ext.create('app.store.Users');
        
        me.store = store;
        /*me.tbar = [{
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
        }];*/ 
        me.columns = [{
            xtype: 'rownumberer'
        },{
            text: 'Email',
            dataIndex: 'email',
            flex: 1
        },{
            text: 'Username',
            dataIndex: 'username',
            flex: 1
        },{
            text: 'Class',
            dataIndex: 'class',
            width: 200,
            //align: 'center',
            renderer: function(v,p,r) {
                if(v=='N') return "Normal Customer";
                else if(v=='NF') return "Normal Facebook Customer";
                else if(v=='NT') return "Normal Twitter Customer";
                else if(v=='NE') return "Normal Email Customer";
                else if(v=='RF') return "Restaurant Facebook Customer";
                else if(v=='RT') return "Restaurant Twitter Customer";
                else if(v=='RE') return "Restaurant Email Customer";
            }
        },{
            text: 'ZipCode',
            dataIndex: 'zip_code',
            width: 75,
            align: 'center'
        },{
            text     : 'Created Date',
            width    : 125,
            sortable : true,
            align: 'center',
            renderer : Ext.util.Format.dateRenderer('m/d/Y'),
            dataIndex: 'created_time'
        },{   
            text: 'Posts',
            dataIndex: 'posts',
            width: 80,
            tdCls: 'blue'
        },{   
            text: 'Point',
            dataIndex: 'point',
            width: 80,
            tdCls: 'red'
        },{
            text: 'Reward',
            dataIndex: 'reward',
            width: 80,
            tdCls: 'green'
        },{
            text: 'Likes',
            dataIndex: 'likes',
            width: 80,
            tdCls: 'blue'
        },{
            text: 'Dislikes',
            dataIndex: 'dislikes',
            width: 80,
            tdCls: 'blue'
        },{
            text: 'Discards',
            dataIndex: 'discards', 
            width: 80,
            tdCls: 'blue'
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
        
        me.selModel = Ext.create('Ext.selection.CheckboxModel', {
            injectCheckbox: 'last'
        });
        me.dockedItems = [{
            xtype: 'pagingtoolbar',
            store: store,   // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            dock: 'top',
            items: ['->', {
                text: 'Delete User',
                handler: function(){
                    var sels = me.getSelectionModel().getSelection();
                    
                    var ids = new Array();
                    Ext.Array.each(sels, function(item, index) {
                        ids.push(item.get('id'));
                    }); 
                    
                    
                    if(ids.length>0){
                        Ext.MessageBox.confirm("Confirm", "Are you delete " + ids.join(",") + "?", function(btn){
                            
                            if(btn == 'yes') {
                                
                                Ext.Ajax.request({
                                    url: '../user/delete',
                                    method: 'POST', 
                                    timeout: 20000,
                                    params: {
                                        ids: ids.join(",")
                                    },
                                    success: function(response){
                                        me.getStore().reload();
                                    }
                                });    
                            }
                        });
                    }
                }
                
            }]
        }];
        
        me.callParent();
        
        me.getStore().load();
    }
});