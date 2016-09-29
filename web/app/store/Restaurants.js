Ext.define('app.store.Restaurants', {
    extend: 'Ext.data.Store',
    requires: 'app.model.Restaurant',
    model: 'app.model.Restaurant' , 
    pageSize: 25,  
    proxy: {
        type: 'ajax',
        url: '../rest/list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    remoteSort: true,
    //buffered: true,
    //leadingBufferZone: 50,
    sorters: [{
        property: 'title',
        direction: 'ASC'
    }]
});