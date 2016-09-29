Ext.define('app.view.RestViewport', {
    extend: 'Ext.container.Viewport',
    layout: 'border',

    requires: [
        'app.view.RestaurantList',
        'app.view.DishList',
        //'app.view.StationsList',
        //'app.view.RecentlyPlayedScroller',
        //'app.view.SongInfo'
    ],

    initComponent: function() {
        var me = this;
        
        me.items = [{
        region: 'north',
        contentEl: 'header-div',
        border: false,
        height: 50
        },{
            region: 'south',
            contentEl: 'footer-div'
        },{  
            region: 'center',

            layout: 'border',
            items: [{
                region:'west',
                xtype: 'panel', 
                width: 350, 
                layout: 'border',
                split: true, 
                items:[{
                    region: 'center',
                    xtype: 'restaurantlist',
                    selModel: Ext.create('Ext.selection.RowModel', {
                        mode: 'SINGLE',
                        listeners: {
                            select: function(sm, record, index, options) {
                                Ext.getCmp('detail-address-panel').update(record.data);

                                var result = record.get('location');
                                var values = result.split(",");
                                if(values.length == 2 && !isNaN(values[0] && !isNaN(values[1]))){
                                    
                                    var myCenter =  new google.maps.LatLng(values[0], values[1]);
                                    var mapProp = {
                                        center: myCenter,
                                        zoom:7,
                                        mapTypeId: google.maps.MapTypeId.ROADMAP
                                    };
                                    var map = new google.maps.Map(document.getElementById("detail-map-panel"),mapProp);

                                    var marker=new google.maps.Marker({
                                        position:myCenter, 
                                        animation:google.maps.Animation.BOUNCE
                                    });

                                    marker.setMap(map);

                                    map.setZoom(15);
                                }
                                
                                me.down('dishlist').getStore().loadPage(1, {
                                    params: {
                                        rest_id: record.get('id')
                                    },
                                    callback: function(records, operation, success) {
                                        // do something after the load finishes
                                    },
                                    scope: me
                                });
                            }
                        } 
                    })
                    },{
                        region: 'south',
                        title: 'Location',
                        collapsible: true,
                        layout: 'border',                         
                        items: [{
                            id: 'detail-address-panel',
                            region: 'north',
                            bodyPadding: 5,
                            height: 50,
                            tpl: [
                                '<p>{address}</p>',
                                '<p>{location}</p>'
                            ]
                        },{
                            id: 'detail-map-panel',
                            region: 'center'
                        }],
                        height: 350
                }]
                },{
                    
                    region: 'center',     // center region is required, no width/height specified
                    xtype: 'dishlist',
                    layout: 'fit'
            }]
    }] ;
        
        me.callParent();
    }
    /*initComponent: function() {
    this.items = [{html:'asdf'}];

    this.callParent();
    }*/
});