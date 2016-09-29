//Ext.Loader.setPath('app', '../../app');
Ext.application({
    name: 'app',
    appFolder: '../app',
    
    
    models: ['User', 'Dish'],    
    stores: ['Users', 'Dishes'/*, 'SearchResults'*/],
    //controllers: ['Station', 'Song']
    launch: function() {
        Ext.create('app.view.UserViewport');
    }
});