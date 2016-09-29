
Ext.onReady(function(){
        var form = Ext.widget({
            xtype: 'form',   
            layout: 'form',         
            id: 'loginForm',

            frame: true,
            title: 'Snackr WebAdmin Login',
            bodyPadding: '5 5 0',
            width: 350, 
            fieldDefaults: {
                msgTarget: 'side',
                labelWidth: 100
            }, 
            defaultType: 'textfield',
            items: [{
                allowBlank: false,
                fieldLabel: 'User Name',
                name: 'username',
                emptyText: 'user name'
                },{
                    allowBlank: false,
                    fieldLabel: 'Password',
                    name: 'password',
                    emptyText: 'password',
                    inputType: 'password'
                }, {
                    xtype:'checkbox',
                    fieldLabel: 'Remember Me',
                    name: 'rememberMe'
            }],
             
            //buttonAlign: 'left',
            buttons: [/*{ 
                xtype: 'displayfield',
                value: '<a href="signup">Sign up</a>'
                },'->',*/{
                    text: 'Log in',
                    handler: function() { 
                        this.up('form').getForm().submit({  
                            method: 'POST',
                            url: 'login',
                            params: {
                                
                            },
                            success: function(form, action) {
                                //location.reload();
                                location.href = '../rest/index';
                            },
                            failure: function(form, action) {
                                Ext.Msg.alert('Failure', "Username or password is not correct. please try again.");
                            }
                        });
                    }
            }]
        });  

        form.render("form-div");
    });