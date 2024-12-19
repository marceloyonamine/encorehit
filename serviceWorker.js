



self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

   
   
   
   const sendNotification = body => {
        
        const title = "ENCOREHIT REMINDER 🔔";

        return self.registration.showNotification(title, {
            body,
        });
    };
   
   
    
   

    if (event.data) {
    
       
          setTimeout(function() {
          
        const payload = event.data.json();
        event.waitUntil(sendNotification(payload.message));
     
          }, 10000)
        
        
        
    }
    
    
    
    
  


});






