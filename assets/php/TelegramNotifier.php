<?php

class TelegramNotifier {
    private $token;
    private $chatId;
    private $photoUrl;
    private $params = [];
    private $staticPhoto = '/assets/img/home/advantage/placeholder.jpg';
    private $staticPhotoPath;

    public function __construct(string $token, string $chatId) {
        if (empty($token) || empty($chatId)) {
            throw new InvalidArgumentException('Invalid parameters');
        } else {
            $this->token = $token;
            $this->chatId = $chatId;
            $this->staticPhotoPath = $_SERVER['DOCUMENT_ROOT'] . $this->staticPhoto;
        }
    }

    public function sendNotification(): void {
        try {
            if (!isset($_POST['message'])) {
                throw new Exception('Invalid parameters');
            }

            $this->sendPhoto();
            $http_code = 200;
            $message = 'Notification sent successfully';

            http_response_code($http_code);
            exit($message);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }

    private function getPhotoUrl(): string {
        $width = 500;
        $height = 300;
        $keywords = 'money';
        $url = "https://source.unsplash.com/random/{$width}x{$height}/?$keywords";
        $site_content = @file_get_contents($url);

        if ($site_content === false) {
            $url = $this->staticPhotoPath;
        }
        
        return $url;
    }
    

    private function downloadPhoto(string $photoUrl): string {
        $temp_file = tempnam(sys_get_temp_dir(), 'telegram');
        $photo_data = file_get_contents($photoUrl);
        if (!$photo_data) {
            throw new Exception('Error sending notification');
        }
        file_put_contents($temp_file, $photo_data);
        return $temp_file;
    }

    private function sendPhotoToTelegram(string $photoPath): void {
        $photo = new CURLFile($photoPath);
        $this->params = [
            'chat_id' => $this->chatId,
            'photo' => $photo,
            'caption' => $this->getMessageText(),
            'parse_mode' => 'HTML',
        ];
        if (isset($_POST['disable_notification']) && $_POST['disable_notification'] === 'true') {
            $this->params['disable_notification'] = true;
        }
        $url = "https://api.telegram.org/bot" . $this->token . "/sendPhoto";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $this->params,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    private function sendPhoto(): void {
        $photoUrl = $this->getPhotoUrl();
        $temp_file = $this->downloadPhoto($photoUrl);
        $this->sendPhotoToTelegram($temp_file);
        unlink($temp_file);
    }

    private function getMessageText(): string {
        $ip = $_SERVER['REMOTE_ADDR'];
        $ip_data = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        $country = $ip_data->geoplugin_countryName ?? 'Unknown';
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $os = $this->getOS($_SERVER['HTTP_USER_AGENT']);
        $time = date("Y-m-d H:i:s");

        $message_text = "<b>Новый пользователь начал загрузку!</b>\n\n" 
                        . "<b>IP:</b> " . $ip 
                        . "\n" 
                        . "<b>Country:</b> " . $country 
                        . "\n" 
                        . "<b>Browser:</b> " . $browser 
                        . "\n" 
                        . "<b>OS:</b> " . $os 
                        . "\n" 
                        . "<b>Local time:</b> " . $time;

        return $message_text;
    }

    private function getOS(string $user_agent): string {
        $os_platform = "Unknown OS Platform";
        $os_array = [
            '/windows nt 10.0/i' => 'Windows 10',
            '/windows nt 10.1/i' => 'Windows 10',
            '/windows nt 10.2/i' => 'Windows 10',
            '/windows nt 11/i' => 'Windows 11',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile',
        ];
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }
        return $os_platform;
    }
}
?>