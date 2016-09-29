Ext.define('app.store.Dishes', {
    extend: 'Ext.data.Store',
    requires: 'app.model.Dish',    
    model: 'app.model.Dish',
    pageSize: 25,  
    
    proxy: {
        type: 'ajax',
        url: '../dish/list',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    autoLoad: true
});