Ext.define('app.model.User', {
    extend: 'Ext.data.Model',
    fields: [
        'id', 'email', 'username', 'class', 'zip_code', 'point', 'reward', 
        {name: 'created_time', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        'posts', 'likes', 'dislikes', 'discards'
    ]
});