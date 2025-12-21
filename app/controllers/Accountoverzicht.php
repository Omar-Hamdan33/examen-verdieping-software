<?php

class Accountoverzicht extends BaseController
{
    private $AccountoverzichtModel;

    public function __construct()
    {
        $this->AccountoverzichtModel = $this->model('AccountoverzichtModel');
        $this->authorizeBeheerder();
    }

    // Autorisatie via aparte methode
    protected function authorizeBeheerder()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!$this->isBeheerderOfInstructeur()) {
            $this->redirect('/homepages/index');
        }
    }

    protected function isBeheerderOfInstructeur(): bool
    {
        return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['beheerder', 'instructeur']);
    }

    protected function isBeheerder(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'beheerder';
    }

    protected function redirect($url)
    {
        header('Location: ' . URLROOT . $url);
        exit;
    }

    public function index()
    {
        try {
            if ($_SESSION['user_role'] === 'instructeur') {
                $result =$this->AccountoverzichtModel->InstructeurAccountoverzicht($_SESSION['user_id']);
            } else {
            $result = $this->AccountoverzichtModel->getAllAccountoverzicht();
            }
            // if ($_SESSION['user_role'] === 'instructeur') {
            //     $result = array_filter($result, function($user) {
            //         return $user->rol === 'klant';
            //     });
            // }
            // Verwijder dubbele gebruikers op basis van id
            $uniqueResult = [];
            $seenIds = [];
            foreach ($result as $user) {
                if (!in_array($user->id, $seenIds)) {
                    $uniqueResult[] = $user;
                    $seenIds[] = $user->id;
                }
            }
            $data = [
                'title' => 'Accountoverzicht',
                'Accountoverzicht' => $uniqueResult
            ];
            $this->view('Accountoverzicht/index', $data);
        } catch (Exception $e) {
            $data = [
                'title' => 'Accountoverzicht',
                'Accountoverzicht' => [],
                'error' => 'Er is een fout opgetreden: ' . $e->getMessage()
            ];
            $this->view('Accountoverzicht/index', $data);
        }
    }

    public function delete($id)
    {
        try {
            $result = $this->AccountoverzichtModel->deleteAccountoverzicht($id);
            $data = [
                'title' => $result ? 'Het record is verwijderd' : 'Het record kan niet worden verwijderd'
            ];
            $this->view('Accountoverzicht/delete', $data);
            header('Refresh:3; URL=' . URLROOT . '/Accountoverzicht/index');
        } catch (Exception $e) {
            $data = [
                'title' => 'Fout bij verwijderen',
                'error' => 'Er is een fout opgetreden: ' . $e->getMessage()
            ];
            $this->view('Accountoverzicht/delete', $data);
        }
    }

    public function getid($id)
    {
        try {
            $result = $this->AccountoverzichtModel->getidAccountoverzicht($id);
            if (!$result) {
                $data = ['error' => 'Account niet gevonden.'];
                $this->view('Accountoverzicht/index', $data);
                return;
            }
            $data = [
                'Account' => $result
            ];
            $this->view('Accountoverzicht/updatepage', $data);
        } catch (Exception $e) {
            $data = [
                'error' => 'Er is een fout opgetreden: ' . $e->getMessage()
            ];
            $this->view('Accountoverzicht/index', $data);
        }
    }

    public function update($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $data = $this->collectAccountData($id);
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $data['error'] = 'Ongeldig e-mailadres.';
                }
                if (empty($data['error']) && $this->AccountoverzichtModel->updateAccount($data)) {
                    $this->redirect('/Accountoverzicht/index');
                } else {
                    if (empty($data['error'])) {
                        $data['error'] = 'Fout bij het updaten van de gegevens.';
                    }
                    // Vul altijd de bestaande accountdata opnieuw aan voor de view
                    $account = $this->AccountoverzichtModel->getidAccountoverzicht($id);
                    if ($account) {
                        $data['Account'] = $account;
                    }
                    $this->view('Accountoverzicht/updatepage', $data);
                }
            } else {
                $this->getid($id);
            }
        } catch (Exception $e) {
            $data = [
                'error' => 'Er is een fout opgetreden: ' . $e->getMessage()
            ];
            $this->view('Accountoverzicht/updatepage', $data);
        }
    }

    public function add()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $this->collectAccountData();
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $data['error'] = 'Ongeldig e-mailadres.';
                }
                if (empty($data['rol'])) {
                    $data['error'] = 'Rol is verplicht.';
                }
                if (empty($data['error'])) {
                    $existing = $this->AccountoverzichtModel->findByEmail($data['email']);
                    if ($existing && !isset($_POST['force_email'])) {
                        $hashedId = hash('sha256', $existing->id);
                        $this->AccountoverzichtModel->saveHashedId($existing->id, $hashedId);
                        $data['error'] = 'Dit e-mailadres bestaat al. Weet je zeker dat je deze wilt aanpassen?';
                        $data['show_force'] = true;
                        $this->view('Accountoverzicht/add', $data);
                        return;
                    }
                    if ($existing && isset($_POST['force_email'])) {
                        $newOldEmail = '_deleted_' . $existing->email . '_deleted_' . date('Ymd_His');
                        $this->AccountoverzichtModel->changeEmail($existing->id, $newOldEmail);
                        $this->AccountoverzichtModel->deleteAccountoverzicht($existing->id);
                        $result = $this->AccountoverzichtModel->addAccount($data);
                        if ($result) {
                            $newUser = $this->AccountoverzichtModel->findByEmail($data['email']);
                            if ($newUser) {
                                $hashedId = hash('sha256', $newUser->id);
                                $this->AccountoverzichtModel->saveHashedId($newUser->id, $hashedId);
                                $this->sendEmailToOwner($existing->email, array_merge($data, ['HASHEDId' => $hashedId]));
                            }
                            $this->redirect('/Accountoverzicht/index');
                        } else {
                            $data['error'] = 'Fout bij het toevoegen van de klant.';
                            $this->view('Accountoverzicht/add', $data);
                            return;
                        }
                    }
                    $result = $this->AccountoverzichtModel->addAccount($data);
                    if ($result) {
                        $this->redirect('/Accountoverzicht/index');
                    } else {
                        $data['error'] = 'Fout bij het toevoegen van de klant.';
                    }
                }
                $this->view('Accountoverzicht/add', $data);
            } else {
                $data = $this->getEmptyAccountData();
                $this->view('Accountoverzicht/add', $data);
            }
        } catch (Exception $e) {
            $data = [
                'error' => 'Er is een fout opgetreden: ' . $e->getMessage()
            ];
            $this->view('Accountoverzicht/add', $data);
        }
    }

    // Verzamel accountdata uit POST
    private function collectAccountData($id = null): array
    {
        return [
            'id' => $id,
            'voornaam' => trim($_POST['voornaam'] ?? ''),
            'tussenvoegsel' => trim($_POST['tussenvoegsel'] ?? ''),
            'achternaam' => trim($_POST['achternaam'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefoon' => trim($_POST['telefoon'] ?? ''),
            'rol' => trim($_POST['rol'] ?? 'klant'),
            'adres' => trim($_POST['adres'] ?? ''),
            'woonplaats' => trim($_POST['woonplaats'] ?? ''),
            'geboortedatum' => trim($_POST['geboortedatum'] ?? ''),
            'bsn' => trim($_POST['bsn'] ?? ''),
            'actief' => 1,
            'error' => ''
        ];
    }

    // Lege data voor add-form
    private function getEmptyAccountData(): array
    {
        return [
            'voornaam' => '',
            'tussenvoegsel' => '',
            'achternaam' => '',
            'email' => '',
            'telefoon' => '',
            'rol' => 'klant',
            'adres' => '',
            'woonplaats' => '',
            'geboortedatum' => '',
            'actief' => 1,
            'error' => ''
        ];
    }

    /**
     * Stuur een e-mail naar de eigenaar van het bestaande account
     */
    private function sendEmailToOwner($toEmail, $newData)
    {
        require_once APPROOT . '/libraries/MailHelper.php';
        $link = URLROOT . "/Signup/setPassword/" . ($newData['HASHEDId'] ?? hash('sha256', $newData['email']));
        $subject = 'Waarschuwing: Uw e-mailadres wordt gewijzigd door de beheerder';
        $body = '<p>Beste gebruiker,</p>' .
            '<p>Een beheerder heeft geprobeerd uw e-mailadres te wijzigen of een nieuw account aan te maken met uw e-mailadres.</p>' .
            '<p>Indien u dit niet zelf was, neem dan contact op met de klantenservice.</p>' .
            '<p>Klik <a href="' . $link . '">hier</a> om een wachtwoord aan te maken of te wijzigen.</p>' .
            '<p>Met vriendelijke groet,<br>Team JouwApp</p>';
        // sendInvoiceMail verwacht: $to, $subject, $bodyHtml
        sendInvoiceMail($toEmail, $subject, $body);
    }
}
