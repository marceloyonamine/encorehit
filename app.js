
   

document.addEventListener('DOMContentLoaded', () => {
 

  const applicationServerKey =
    'BKSTVs2g4xGE_Q80mdXd0H_evakOEn7AFwXcQPZA7N1O7Gcztn-qZA_qPgjdTiDUhXZzAQVUtVzWbWXWVv8Wbbb';
  let isPushEnabled = false;
  
   

  const pushButton = document.querySelector('#push-subscription-button');
  if (!pushButton) {
    return;
  }

  pushButton.addEventListener('click', function() {
    if (isPushEnabled) {
      push_unsubscribe();
    } else {
      push_subscribe();
    }
  });

  if (!('serviceWorker' in navigator)) {
    console.warn('Service workers are not supported by this browser');
    changePushButtonState('incompatible');
    return;
  }

  if (!('PushManager' in window)) {
    console.warn('Push notifications are not supported by this browser');
    changePushButtonState('incompatible');
    return;
  }

  if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
    console.warn('Notifications are not supported by this browser');
    changePushButtonState('incompatible');
    return;
  }

  
  if (Notification.permission === 'denied') {
    console.warn('Notifications are denied by the user');
    changePushButtonState('incompatible');
    return;
  }

  navigator.serviceWorker.register('serviceWorker.js').then(
    () => {
      console.log('[SW] Service worker has been registered');
      push_updateSubscription();
    },
    e => {
      console.error('[SW] Service worker registration failed', e);
      changePushButtonState('incompatible');
    }
  );

  function changePushButtonState(state) {
    switch (state) {
      case 'enabled':
        pushButton.disabled = false;
        pushButton.textContent = 'Disable Push Notifications';
        isPushEnabled = true;
        break;
      case 'disabled':
        pushButton.disabled = false;
        pushButton.textContent = 'Enable Push Notifications';
        isPushEnabled = false;
        break;
      case 'computing':
        pushButton.disabled = true;
        pushButton.textContent = 'Loading...';
        break;
      case 'incompatible':
        pushButton.disabled = true;
        pushButton.textContent = 'Push notifications are not compatible with this browser';
        break;
      default:
        console.error('Unhandled push button state', state);
        break;
    }
  }

  function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
  }

  function checkNotificationPermission() {
    return new Promise((resolve, reject) => {
      if (Notification.permission === 'denied') {
        return reject(new Error('Push messages are blocked.'));
      }

      if (Notification.permission === 'granted') {
        return resolve();
      }

      if (Notification.permission === 'default') {
        return Notification.requestPermission().then(result => {
          if (result !== 'granted') {
            reject(new Error('Bad permission result'));
          } else {
            resolve();
          }
        });
      }

      return reject(new Error('Unknown permission'));
    });
  }

  function push_subscribe() {
    changePushButtonState('computing');

    return checkNotificationPermission()
      .then(() => navigator.serviceWorker.ready)
      .then(serviceWorkerRegistration =>
        serviceWorkerRegistration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(applicationServerKey),
        })
      )
      .then(subscription => {
      
      
        
        return push_sendSubscriptionToServer(subscription, 'POST');
      })
      .then(subscription => subscription && changePushButtonState('enabled')) // update your UI
      .catch(e => {
        if (Notification.permission === 'denied') {
        
          console.warn('Notifications are denied by the user.');
          changePushButtonState('incompatible');
        } else {
        
          console.error('Impossible to subscribe to push notifications', e);
          changePushButtonState('disabled');
        }
      });
  }

  function push_updateSubscription() {
    navigator.serviceWorker.ready
      .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
      .then(subscription => {
        changePushButtonState('disabled');

        if (!subscription) {
        
          return;
        }

   
        return push_sendSubscriptionToServer(subscription, 'PUT');
      })
      .then(subscription => subscription && changePushButtonState('enabled')) // Set your UI to show they have subscribed for push messages
      .catch(e => {
        console.error('Error when updating the subscription', e);
      });
  }

  function push_unsubscribe() {
    changePushButtonState('computing');

 
    navigator.serviceWorker.ready
      .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
      .then(subscription => {
      
        if (!subscription) {
        
          changePushButtonState('disabled');
          return;
        }

     
        return push_sendSubscriptionToServer(subscription, 'DELETE');
      })
      .then(subscription => subscription.unsubscribe())
      .then(() => changePushButtonState('disabled'))
      .catch(e => {
       
        console.error('Error when unsubscribing the user', e);
        changePushButtonState('disabled');
      });
  }
  
  


  function generateBrowserID() {
   
    const now = new Date();
    const timestamp = now.getTime();
  
    
    const randomNum = Math.floor(Math.random() * 1000000);
  
   
    const localStorage = window.localStorage;
  
   
    let navegadorID = localStorage.getItem('navegadorID');
  
     const letras = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
    const indiceAleatorio = Math.floor(Math.random() * letras.length);
  
   
    if (!navegadorID) {
      navegadorID = `${timestamp}${letras[indiceAleatorio]}${randomNum}`;
      localStorage.setItem('navegadorID', navegadorID);
    }
  
    return navegadorID;
  }




  function push_sendSubscriptionToServer(subscription, method) {

    const browserID = generateBrowserID();
  
  
    const key = subscription.getKey('p256dh');
    const token = subscription.getKey('auth');
    const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

    return fetch('push_subscription.php', {
      method,
       headers: {
    'Content-Type' : 'application/json',
    'Authorization1': browserID,
    'Authorization2': 'nulo'
      },
      body: JSON.stringify({
        endpoint: subscription.endpoint,
        publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
        authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
        contentEncoding,
      }),
    }).then(() => subscription);
    
    
    
    
  }







  function calcularIntervaloHoras(dataHoraLocal, dataHoraFormulario) {
    
    const dataLocal = new Date(dataHoraLocal);
    const dataFormulario = new Date(dataHoraFormulario);
  
   
    const diferencaMilissegundos = dataFormulario.getTime() - dataLocal.getTime();
  
 
    const diferencaHoras = diferencaMilissegundos / (1000 * 60 * 60);
  
    return Math.abs(diferencaHoras);
     }
     
     
     
     function isDateBeforeCurrent(dateTimeString) {
    const currentDate = new Date();
    const givenDateTime = new Date(dateTimeString);
  
    return givenDateTime.getTime() < currentDate.getTime();
              }



  const sendPushButton = document.querySelector('#send-push-button');
  if (!sendPushButton) {
    return;
  }

  sendPushButton.addEventListener('click', () =>
    navigator.serviceWorker.ready
      .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
      .then(subscription => {
      
         let myvaluemsgstorage = localStorage.getItem("storagemessage");
         let myvaluedatetimestorage = localStorage.getItem("storagedatetime");


         const navegadorID = generateBrowserID();

         
         const dataHoraLocal = new Date(); // Data e hora local atual
         
         const intervaloHoras = calcularIntervaloHoras(dataHoraLocal, myvaluedatetimestorage);


      
        if (!subscription) {
          alert('Please enable push notifications');
          
          return;
        } else {

          


          const numItems = localStorage.length;

        
          if (numItems < 3) {

         alert("Empty Message field or Date Time field, ERROR! Was not sent");

          localStorage.removeItem("storagemessage");
          localStorage.removeItem("storagedatetime");
          

         return;
         
          } else {

            if (isDateBeforeCurrent(myvaluedatetimestorage)) {

              alert('Error: Date Time less than Current Date Time');
                 
              localStorage.removeItem("storagemessage");
              localStorage.removeItem("storagedatetime");
             
    
                return;
    
              } else {


              if (intervaloHoras >= 30) {

           
                localStorage.removeItem("storagemessage");
                localStorage.removeItem("storagedatetime");


              } else {
        
                alert('Error: The interval between dates is less than or equal to 30 hours.');
    
              localStorage.removeItem("storagemessage");
              localStorage.removeItem("storagedatetime");
              
    
               return;
    
               }


              }
        
         
          
          }
          
          
        }
        
        

        const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];
        const jsonSubscription = subscription.toJSON();

          fetch('grava_notification.php', {
          method: 'POST',
           headers: {
          'Content-Type' : 'application/json',
          'Authorization1': myvaluemsgstorage,
          'Authorization2': myvaluedatetimestorage,
          'Authorization3': navegadorID 
          },
          body: JSON.stringify(Object.assign(jsonSubscription, { contentEncoding })),
        }).then(response => response.text()).then(data => {
          
         
            alert(data);
        
        
        });

        

                

      })

      
      

  );
  /**
   * END send_push_notification
   */
});
