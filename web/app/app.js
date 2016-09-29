//Ext.Loader.setPath('app', '../../app');
Ext.application({
    name: 'app',
    appFolder: '../app',
    autoCreateViewport: true,
    
    models: ['Station', 'Song'],    
    stores: ['Stations', 'RecentSongs', 'SearchResults'],
    controllers: ['Station', 'Song']
});