<?php
require_once __DIR__ . '/../../vendor/autoload.php'; // alleen nodig als je autoloader hier nog niet hebt ingeladen

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Signup extends BaseController
{
    private $SignupModel;

    public function __construct()
    {
         $this->SignupModel = $this->model('SignupModel');

         if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Toon het registratieformulier
     */
    public function index()
    {
        $data = [
            'title' => 'Top 5 rijkste Signup ter wereld',
        ];
        $this->view('Signup/index', $data);
    }

    /**
     * Verwerk registratie (alleen e-mail verplicht, MVC/OOP)
     */
    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = [
                    'email' => trim($_POST['email'] ?? ''),
                ];
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $data['error'] = 'Ongeldig e-mailadres.';
                    return $this->view('Signup/index', $data);
                }
                // Controleer of e-mail al bestaat
                $existing = $this->SignupModel->findByEmail($data['email']);
                if ($existing) {
                    $data['error'] = 'Dit e-mailadres is al in gebruik. Probeer een ander e-mailadres of reset je wachtwoord.';
                    return $this->view('Signup/index', $data);
                }
                $newId = $this->SignupModel->Createaccount($data);
                if ($newId) {
                    $this->sendWelcomeEmail($newId, $data['email']);
                    $this->redirect('/Signup/thankyou');
                } else {
                    $data['error'] = 'Er is een fout opgetreden bij het aanmaken van uw account.';
                    return $this->view('Signup/index', $data);
                }
            }
            $this->view('Signup/index');
        } catch (Exception $e) {
            $data = ['error' => 'Er is een fout opgetreden: ' . $e->getMessage()];
            return $this->view('Signup/index', $data);
        }
    }

    /**
     * Thank you page after registration.
     */
    public function thankyou()
    {
        $this->view('signup/thankyou');
    }

    /**
     * Displays and processes the setuserdata form (alleen id verplicht)
     */
    public function setuserdata(string $id = null)
    {
        $error = '';
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = $_POST['id'] ?? 0;
                $data = [
                    'id' => $id,
                    'email' => trim($_POST['email'] ?? ''),
                    'voornaam' => trim($_POST['voornaam'] ?? ''),
                    'tussenvoegsel' => trim($_POST['tussenvoegsel'] ?? ''),
                    'achternaam' => trim($_POST['achternaam'] ?? ''),
                    'telefoon' => trim($_POST['telefoon'] ?? ''),
                    'adres' => trim($_POST['adres'] ?? ''),
                    'woonplaats' => trim($_POST['woonplaats'] ?? ''),
                    'geboortedatum' => trim($_POST['geboortedatum'] ?? ''),
                    'bsn' => trim($_POST['bsn'] ?? ''),
                ];
                // Alleen id en email verplicht
                if (empty($data['id']) || empty($data['email'])) {
                    $error = 'ID en e-mail zijn verplicht.';
                    return $this->view('signup/setuserdata', compact('id','error'));
                }
                $result = $this->SignupModel->updateUserDetails($data);
                if ($result) {
                    header('Location: ' . URLROOT . '/Login/index');
                    exit;
                } else {
                    $error = 'Kon de gegevens niet opslaan. Probeer nog eens.';
                    return $this->view('signup/setuserdata', compact('id','error'));
                }
            }
            $data = [
                'id' => $id,
                'error' => $error
            ];
            return $this->view('signup/setuserdata', $data);
        } catch (Exception $e) {
            $data = [
                'id' => $id,
                'error' => 'Er is een fout opgetreden: ' . $e->getMessage()
            ];
            return $this->view('signup/setuserdata', $data);
        }
    }

    public function setPassword(string $HASHEDId = null )
    {
        $error = '';
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $HASHEDId =  ($_POST['id'] ?? 0);
                $pwd    = trim($_POST['wachtwoord'] ?? '');
                $pwd2   = trim($_POST['wachtwoord2'] ?? '');
                $valid = true;
                $error = '';
                if ($pwd === '' || $pwd !== $pwd2) {
                    $error = 'Wachtwoorden komen niet overeen of zijn leeg.';
                    $valid = false;
                } elseif (strlen($pwd) < 12) {
                    $error = 'Het wachtwoord moet minimaal 12 tekens lang zijn.';
                    $valid = false;
                } elseif (!preg_match('/[A-Z]/', $pwd)) {
                    $error = 'Het wachtwoord moet minimaal één hoofdletter bevatten.';
                    $valid = false;
                } elseif (!preg_match('/[0-9]/', $pwd)) {
                    $error = 'Het wachtwoord moet minimaal één cijfer bevatten.';
                    $valid = false;
                } elseif (!preg_match('/[@#\$!%*?&.,;:]/', $pwd)) {
                    $error = 'Het wachtwoord moet minimaal één leesteken bevatten (zoals @, #, !, %, *, ?, & of . , ; : ).';
                    $valid = false;
                }
                if (!$valid) {
                    return $this->view('signup/setPassword', compact('HASHEDId','error'));
                }
                $hash = password_hash($pwd, PASSWORD_DEFAULT);
                $data = [
                    'HASHEDId' => $HASHEDId,
                    'wachtwoord' => $hash
                ];
                $updateResult = $this->SignupModel->updatePassword($data);
                if (!empty($updateResult)) {
                    // Automatisch inloggen na succesvol wachtwoord instellen
                    $user = $this->SignupModel->findByHashedId($HASHEDId);
                    if (!empty($user)) {
                        // Verwijder HASHEDId na gebruik
                        $this->SignupModel->removeHashedId($user->id);
                        // Log login
                        $this->SignupModel->logLogin($user->email , 'login');
                        $_SESSION['user_id'] = $user->id;
                        $_SESSION['user_email'] = $user->email;
                        $_SESSION['user_role'] = $user->rol ?? 'klant';
                        $_SESSION['flash_success'] = 'Registratie voltooid! U bent nu ingelogd.';
                        header('Location: ' . URLROOT . '/homepages/index');
                        exit;
                    } else {
                        $error = 'Er is iets misgegaan bij het automatisch inloggen.';
                        return $this->view('signup/setPassword', compact('HASHEDId','error'));
                    }
                } else {
                    $error = 'Kon het wachtwoord niet opslaan. Probeer nog eens.';
                    return $this->view('signup/setPassword', compact('HASHEDId','error'));
                }
            }
            $data = [
                'HASHEDId' => $HASHEDId,
                'error'  => $error
            ];
            return $this->view('signup/setPassword', $data);
        } catch (Exception $e) {
            $data = [
                'HASHEDId' => $HASHEDId,
                'error' => 'Er is een fout opgetreden: ' . $e->getMessage()
            ];
            return $this->view('signup/setPassword', $data);
        }
    }

    public function restPassword()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $email = trim($_POST['email']);
                $user = $this->SignupModel->findByEmail($email);
                if (!$user) {
                    $data = [
                        'error' => 'Dit e-mailadres is niet bekend. Controleer je invoer.',
                        'email' => $email
                    ];
                    return $this->view('Signup/sendpasswordreset', $data);
                }
                $id = $user->id;
                $HASHEDId = hash('sha256', $id);
                require_once APPROOT . '/libraries/MailHelper.php';
                $link = URLROOT . "/Signup/setPassword/{$HASHEDId}";
                $subject = 'Welkom bij windkracht-12 stel je wachtwoord in';
                $body = "<p>Hallo,</p>"
                      . "<p>Reset je wachtwoord <a href='{$link}'>Klik hier</a>.</p>"
                      . "<p>Groet,<br>Team JouwApp</p>";
                $this->Addhashedpassword($id, $HASHEDId);
                sendInvoiceMail($email, $subject, $body);
                return $this->view('Signup/sendpasswordreset', ['success' => 'Er is een resetmail verstuurd naar het opgegeven e-mailadres.']);
            } else {
                $this->view('Signup/sendpasswordreset');
            }
        } catch (Exception $e) {
            $data = ['error' => 'Er is een fout opgetreden: ' . $e->getMessage()];
            return $this->view('Signup/sendpasswordreset', $data);
        }
    }

    /**
     * Redirect helper (MVC/OOP)
     */
    protected function redirect($url)
    {
        header('Location: ' . URLROOT . $url);
        exit;
    }

    /**
     * Stuur welkomstmail via MailHelper (MVC/OOP)
     */
    private function sendWelcomeEmail(int $id, string $email)
    {
        try {
            require_once APPROOT . '/libraries/MailHelper.php';
            $HASHEDId = hash('sha256', $id);
            $link = URLROOT . "/Signup/setPassword/{$HASHEDId}";
            $subject = 'Welkom bij JouwApp – stel je wachtwoord in';
            $body = "<p>Hallo,</p>"
                  . "<p>Bedankt voor je registratie! <a href='{$link}'>Klik hier</a> om je wachtwoord aan te maken.</p>"
                  . "<p>Groet,<br>Team JouwApp</p>";
            $this->Addhashedpassword($id, $HASHEDId);
            sendInvoiceMail($email, $subject, $body);
        } catch (Exception $e) {
            // Log eventueel de fout of geef feedback
            error_log('Fout bij versturen welkomstmail: ' . $e->getMessage());
        }
    }

    /**
     * Adds the hashed ID to the database for later verification.
     *
     * @param int $id User ID
     * @param string $hashedId Hashed ID
     */
    private function Addhashedpassword(int $id, string $hashedId)
    {
        try {
            $this->SignupModel->storeHashedId($id, $hashedId);
        } catch (Exception $e) {
            error_log('Fout bij opslaan hashedId: ' . $e->getMessage());
        }
    }


}
