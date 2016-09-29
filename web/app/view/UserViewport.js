Ext.define('app.view.UserViewport', {
    extend: 'Ext.container.Viewport',
    layout: 'border',

    requires: [
        'app.view.UserList',
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
                region: 'center',     // center region is required, no width/height specified
                xtype: 'userlist',
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