const notifyBtn = document.querySelectorAll('.pt-btn_notify');

notifyBtn.forEach(sendNotification);

async function sendNotification(button) {
  button.addEventListener('click', async (event) => {
    event.preventDefault();
    try {
      const ipResponse = await fetch('https://ipapi.co/json/');
      const ipData = await ipResponse.json();
      const userIP = ipData.ip;
      const locationResponse = await fetch(`https://ipapi.co/${userIP}/json/`);
      const locationData = await locationResponse.json();
      const userCountry = locationData.country_name;
      const userAgent = navigator.userAgent;
      const userOS = navigator.platform;
      const userTime = new Date().toLocaleString();
      const userInfo = `IP адрес: ${userIP}\nСтрана: ${userCountry}\nБраузер: ${userAgent}\nОС: ${userOS}\nВремя: ${userTime}`;
      const message = `Нажата кнопка на сайте!\n\n${userInfo}`;
      const xhr = new XMLHttpRequest();
      
      xhr.open('POST', '../assets/php/main.php', true);
      xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      xhr.setRequestHeader('Access-Control-Allow-Origin', '*');

      xhr.onload = function () {
        if (xhr.status === 200) {
          console.log('Notification sent successfully');
        } else {
          console.log('Notification failed with error code ' + xhr.status);
        }
      };
      xhr.send('message=' + encodeURIComponent(message));
    } catch (error) {
      console.error('An error occurred while sending the notification', error);
    }
  });
}