Ext.define('app.view.RestaurantForm', {
    extend: 'Ext.Window',
    xtype: 'restaurantform',
    title: 'Restaurant Form',
    width: 700, 
    height: 450,
    modal: true,
    layout: 'border',

    initComponent: function(){
        var me = this;
        me.items = [{
            region: 'west',
            xtype: 'form',
            //layout: 'form',
            bodyPadding: '5 5 0',
            fieldDefaults: {
                //msgTarget: 'side',
                labelWidth: 75
            },
            defaults: {
                anchor: '100%'
            },
            width: 300,
            items:[{
                xtype: 'hidden',
                name: 'id'
                },{
                    xtype: 'textfield',
                    name: 'title',
                    fieldLabel: 'Title',
                    allowBlank: false
                },{
                    xtype: 'textfield',
                    name: 'tel',
                    fieldLabel: 'Tel',
                    allowBlank: false
                },{
                    xtype: 'textfield',
                    name: 'address',
                    fieldLabel: 'Address',
                    allowBlank: false,
                    listeners: {
                        blur: function(field,event,options) {
                            var address = field.getValue();
                            if(address.length>0){

                                Ext.Ajax.request({
                                    url: '../rest/location', 
                                    timeout: 20000,
                                    params: {
                                        address: address
                                    },
                                    success: function(response){
                                        var result = response.responseText;
                                        me.showMap(result);
                                    }
                                });    
                            }
                        }
                    }
                },{
                    xtype: 'textfield',
                    name: 'location',
                    fieldLabel: 'Location',
                    allowBlank: false
                },{
                    xtype: 'textfield',
                    name: 'zip_code',
                    fieldLabel: 'Zip Code',
                    allowBlank: false
                },{
                    xtype: 'textarea',
                    name: 'description',
                    fieldLabel: 'Description',
                    labelAlign: 'top',
                    height: 200
            }]
            }, {
                region: 'center',
                bodyPadding: 5,
                id: 'map-panel',
                html: 'map panel'
        }];

        me.buttons = [{
            text: 'Save',
            handler: function(){
                me.down('form').getForm().submit({  
                    method: 'POST',
                    url: '../rest/save',
                    params: {

                    },
                    success: function(form, action) {
                        Ext.Msg.alert('Success', "Saved successfully!");
                        me.fireEvent('saved');
                    },
                    failure: function(form, action) {
                        switch (action.failureType) {
                            case Ext.form.action.Action.CLIENT_INVALID:
                                Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
                                break;
                            case Ext.form.action.Action.CONNECT_FAILURE:
                                Ext.Msg.alert('Failure', 'Ajax communication failed');
                                break;
                            case Ext.form.action.Action.SERVER_INVALID:
                                Ext.Msg.alert('Failure', action.result.msg);
                        }
                    }
                });
            }
            }, {
                text: 'Cancel',
                handler: function(){
                    me.close();
                }
        }];
        me.callParent();

        me.addEvents('saved');
    },

    showMap: function(location) {
        var me = this;
        var values = location.split(",");
        if(values.length == 2 && !isNaN(values[0] && !isNaN(values[1]))){
            me.down('textfield[name=location]').setValue(location) ;
            var myCenter =  new google.maps.LatLng(values[0], values[1]);
            var mapProp = {
                center: myCenter,
                zoom:7,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(document.getElementById("map-panel"),mapProp);

            var marker=new google.maps.Marker({
                position:myCenter, 
                animation:google.maps.Animation.BOUNCE
            });

            marker.setMap(map);

            map.setZoom(17);
        }
    }

});