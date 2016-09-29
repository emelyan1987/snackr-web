Ext.define('app.view.DishList', {
    extend: 'Ext.Panel',
    xtype: 'dishlist',
    frame: false,  
    //title: 'Dish List',
    id: 'images-view',
    autoScroll: true,
    initComponent: function() {
        var me = this;
        var selected = null;
        
        var store = Ext.create('app.store.Dishes');
        me.items = Ext.create('Ext.view.View', {
            store: store,
            tpl: [
                '<tpl for=".">',
                '<div class="thumb-wrap" id="{name:stripTags}">',
                '<div class="thumb"><img src="../uploads/{photo}" title="Posted by {email} at {created_time}"></div>',
                '<span>{title:htmlEncode}</span>',
                '<span style="height:50px"><img src="../../images/location.png">&nbsp;&nbsp;{location:htmlEncode}</span>',
                '</div>',
                '</tpl>',
                '<div class="x-clear"></div>'
            ],
            multiSelect: true,
            //height: 310,
            trackOver: true,
            overItemCls: 'x-item-over',
            itemSelector: 'div.thumb-wrap',
            emptyText: 'No images to display', 
            prepareData: function(data) {
                Ext.apply(data, {
                    shortName: Ext.util.Format.ellipsis(data.name, 15),
                    sizeString: Ext.util.Format.fileSize(data.size),
                    dateString: Ext.util.Format.date(data.lastmod, "m/d/Y g:i a")
                });
                return data;
            },
            listeners: {
                selectionchange: function(dv, nodes ){
                    var l = nodes.length;
                    //s = l !== 1 ? 's' : '';
                    //this.up('panel').setTitle('Simple DataView (' + l + ' item' + s + ' selected)');
                    
                    selected = nodes;
                }
            }
        });
                
        me.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: 'Delete Food',
                itemId: 'delete-food-btn',
                handler: function() {
                     if(selected.length > 0) {
                         var ids = new Array();
                        Ext.Array.each(selected, function(item, index) {
                            ids.push(item.get('id'));
                        }); 
                        Ext.MessageBox.confirm("Confirm", "Do you delete selected foods?", function(btn){
                            
                            if(btn == 'yes') {
                                
                                Ext.Ajax.request({
                                    url: '../dish/delete',
                                    method: 'POST', 
                                    timeout: 20000,
                                    params: {
                                        ids: ids.join(",")
                                    },
                                    success: function(response){
                                        me.getStore().reload();
                                    },
                                    failure: function(response) {
                                        alert(Ext.Object.toQueryString());
                                    }
                                });    
                            }
                        });
                     }
                }
            },'->',{
                text: 'Flaged Photos',
                handler: function() { 
                    me.getStore().loadPage(1,{
                        params: {
                            flag: 1
                        }
                    });
                }
            }]
        },{
            xtype: 'pagingtoolbar',
            store: store,   // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        }];

        me.callParent();  
    },

    getStore: function() {
        return this.getComponent(0).getStore();
    }
});