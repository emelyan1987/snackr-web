Ext.define('app.store.Users', {
    extend: 'Ext.data.Store',
    requires: 'app.model.User',    
    model: 'app.model.User',
    pageSize: 25,  
    
    proxy: {
        type: 'ajax',
        url: '../user/list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    remoteSort: true,
    sorters: [{
        property: 'created_time',
        direction: 'DESC'
    }],
    autoLoad: true
});