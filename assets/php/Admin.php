<?php
class Admin {
    private $username;
    private $password;
  
    public function __construct(string $username, string $password) {
        $this->username = $username;
        $this->password = $password;
    }
  
    public function login(string $username, string $password): bool {
        return ($this->username === $username && $this->password === $password);
    }
  
    public function changeHref(string $newHref): bool {
        if (file_exists('index.html')) {
            $html = file_get_contents('index.html');
            $doc = new DOMDocument();
            $doc->loadHTML($html);
    
            $aTags = $doc->getElementsByTagName('a');
    
            foreach ($aTags as $a) {
                $a->setAttribute('href', $newHref);
            }
    
            $html = $doc->saveHTML();
    
            file_put_contents('index.html', $html);
            return true;
        } else {
            return false;
        }
    }

    public function renderLoginForm(): void {
        echo '<form method="post">';
        echo '<label for="username">Username:</label>';
        echo '<input type="text" id="username" name="username">';
        echo '<label for="password">Password:</label>';
        echo '<input type="password" id="password" name="password">';
        echo '<button type="submit">Login</button>';
        echo '</form>';
    }

    public function renderChangeHrefForm(): void {
        echo '<form method="post">';
        echo '<label for="newHref">New Href:</label>';
        echo '<input type="text" id="newHref" name="newHref">';
        echo '<button type="submit">Change Href</button>';
        echo '</form>';
    }

    public function handleRequest(): void {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $loggedIn = $this->login($_POST['username'], $_POST['password']);

            if ($loggedIn) {
                $this->renderChangeHrefForm();
            } else {
                echo 'Invalid username or password';
                $this->renderLoginForm();
            }
        } else {
            $this->renderLoginForm();
        }

        if (isset($_POST['newHref'])) {
            $success = $this->changeHref($_POST['newHref']) ? 'Href has been changed' : 'Error: index.html not found';
            echo $success;
        }
    }
}
?>