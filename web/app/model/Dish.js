Ext.define('app.model.Dish', {
    extend: 'Ext.data.Model',
    fields: [
        'id', 'title', 'photo', 'email', 'restaurant_id', 'flag', 'is_blocked', 'location', 'address',      
        {name: 'created_time', type: 'date', dateFormat: 'Y-m-d H:i:s'}, 
        'price'
    ]
});