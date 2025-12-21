<?php
class Users extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = $this->model('UserModel');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Helper voor redirect (MVC/OOP)
     */
    protected function redirect($url)
    {
        header('Location: ' . URLROOT . $url);
        exit;
    }

    /**
     * Stuur een e-mail via MailHelper (MVC/OOP)
     */
    protected function sendMail($to, $subject, $bodyHtml)
    {
        require_once APPROOT . '/libraries/MailHelper.php';
        return sendInvoiceMail($to, $subject, $bodyHtml);
    }

    /**
     * Toon loginformulier en verwerk POST
     */
    public function login()
    {
        try {
            // Als al ingelogd: doorsturen
            if (isset($_SESSION['user_id'])) {
                $this->redirect('/homepages/index');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // sanitizen
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $password = trim($_POST['password'] ?? '');

                $errors = [];
                if (!$email)
                    $errors['email'] = 'Vul een geldig e-mailadres in.';
                if (empty($password))
                    $errors['password'] = 'Vul je wachtwoord in.';

                if (empty($errors)) {
                    $user = $this->userModel->login($email, $password);
                    if ($user) {
                        // sessie starten
                        session_start();
                        $_SESSION['logged_in'] = true;
                        $_SESSION['user_id'] = $user->id;
                        $_SESSION['user_name'] = $user->voornaam . ' ' . ($user->tussenvoegsel ?? '') . ' ' . $user->achternaam;
                        $_SESSION['user_email'] = $user->email;
                        // eventueel rol: 
                        $_SESSION['user_role'] = $user->rol;
                        if (isset($user->bsn) || isset($user->adres) || isset($user->woonplaats) || isset($user->geboortedatum)) {

                            $this->redirect('/homepages/index');
                        } else {
                            // doorsturen naar gegevens aanvullen
                            $this->redirect('/Users/userbeheer');
                        }
                    } else {
                        $errors['credentials'] = 'E-mail of wachtwoord is onjuist.';
                    }
                }

                // bij fouten opnieuw formulier tonen
                $data = [
                    'email' => $email,
                    'errors' => $errors
                ];
                return $this->view('users/login', $data);

            } else {
                // eerste keer GET
                $data = [
                    'email' => '',
                    'errors' => []
                ];
                return $this->view('users/login', $data);
            }
        } catch (Exception $e) {
            $data = [
                'email' => '',
                'errors' => ['Er is een fout opgetreden: ' . $e->getMessage()]
            ];
            return $this->view('users/login', $data);
        }
    }


    /**
     * Uitloggen: sessie vernietigen
     */
    public function logout()
    {
        try {
            $this->userModel->logLogin($_SESSION['user_email'] , 'logout');
            unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role'] , $_SESSION['user_email'] , $_SESSION['logged_in']);
            session_destroy();
            $this->redirect('/users/login');
        } catch (Exception $e) {
            // Fallback: probeer sessie te vernietigen en toon foutmelding
            session_destroy();
            $_SESSION['flash_error'] = 'Fout bij uitloggen: ' . $e->getMessage();
            $this->redirect('/users/login');
        }
    }

    public function userbeheer()
    {
        try {
            if (!isset($_SESSION['user_id']) || !$_SESSION['user_role']) {
                header('Location: ' . URLROOT . '/users/login');
                exit;
            }
            $user = $this->userModel->getuserid($_SESSION['user_id']);
            $errors = [];
            $success = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Verwerk de POST-aanroep voor het bewerken van gebruikers
                $userId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $changePassword = !empty($_POST['new_password']);
                if ($userId && $email) {
                    $data = [
                        'id' => $_POST['id'],
                        'voornaam' => $_POST['voornaam'] ?? null,
                        'tussenvoegsel' => $_POST['tussenvoegsel'] ?? null,
                        'achternaam' => $_POST['achternaam'] ?? null,
                        'email' => $_POST['email'],
                        'telefoon' => $_POST['telefoon'] ?? null,
                        'adres' => $_POST['adres'] ?? null,
                        'woonplaats' => $_POST['woonplaats'] ?? null,
                        'geboortedatum' => $_POST['geboortedatum'] ?? null,
                        'bsn' => $_POST['bsn'] ?? null
                    ];
                    $this->userModel->updateUser($data);
                    // Wachtwoord wijzigen indien ingevuld
                    if ($changePassword) {
                        $new = trim($_POST['new_password']);
                        $new2 = trim($_POST['new_password2']);
                        if ($new !== $new2) {
                            $errors['password'] = 'Nieuwe wachtwoorden komen niet overeen.';
                        } elseif (strlen($new) < 12 || !preg_match('/[A-Z]/', $new) || !preg_match('/[0-9]/', $new) || !preg_match('/[@#\$!%*?&.,;:]/', $new)) {
                            $errors['password'] = 'Het nieuwe wachtwoord moet minimaal 12 tekens, een hoofdletter, een cijfer en een leesteken bevatten.';
                        } else {
                            $hash = password_hash($new, PASSWORD_DEFAULT);
                            $this->userModel->updatePasswordById($userId, $hash);
                            $success = 'Wachtwoord succesvol gewijzigd.';
                        }
                    }
                    if (empty($errors)) {
                        $user = $this->userModel->getuserid($_SESSION['user_id']);
                    }
                } else {
                    $errors['form'] = 'Ongeldige toegangsmethode.';
                }
            } 
            if (empty($errors)) {
                $success = 'Gebruiker succesvol bijgewerkt.';
            } else {
                $success = '';
            }
            $data = [
                'user' => $user,
                'errors' => $errors,
                'success' => $success
            ];
            $this->view('users/userbeheer', $data);
        } catch (Exception $e) {
            $data = [
                'user' => null,
                'errors' => ['Er is een fout opgetreden: ' . $e->getMessage()],
                'success' => ''
            ];
            $this->view('users/userbeheer', $data);
        }
    }

    
}



