//Ext.Loader.setPath('app', '../../app');
Ext.application({
    name: 'app',
    appFolder: '../app',
    
    
    models: ['Restaurant', 'Dish'],    
    stores: ['Restaurants', 'Dishes'/*, 'SearchResults'*/],
    //controllers: ['Station', 'Song']
    launch: function() {
        Ext.create('app.view.RestViewport');
    }
});