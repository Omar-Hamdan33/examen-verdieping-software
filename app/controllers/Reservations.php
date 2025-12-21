<?php
class Reservations extends BaseController
{
    private $reservationModel;
    

    public function __construct()
    {
        $this->reservationModel = $this->model('ReservationModel');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Vereist ingelogd en profiel compleet
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/Users/login');
            exit;
        }
        // Controleer of profiel compleet is
        require_once APPROOT . '/models/UserModel.php';
        $userModel = new UserModel();
        $user = $userModel->getuserid($_SESSION['user_id']);
        if (
            !$user ||
            empty($user->voornaam) ||
            empty($user->achternaam) ||
            empty($user->telefoon) ||
            empty($user->adres) ||
            empty($user->woonplaats) ||
            empty($user->geboortedatum)
        ) {
            $_SESSION['flash_error'] = 'Vul eerst al je gegevens in (voornaam, achternaam, telefoon, adres, woonplaats, geboortedatum) voordat je deze functionaliteit gebruikt.';
            header('Location: ' . URLROOT . '/users/userbeheer');
            exit;
        }
    }


    // GET = toon formulier, POST = verwerk reservering
    public function book()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {


                $userId = $_SESSION['user_id'] ?? null;
                if (!$userId) {
                    header('Location: ' . URLROOT . '/Users/login');
                    exit;
                }
                $data = [
                    'user_id' => $userId,               // neem aan dat je sessie hebt
                    'package_id' => filter_input(INPUT_POST, 'package_id'),
                    'location_id' => filter_input(INPUT_POST, 'location_id'),
                    'instructor_id' => filter_input(INPUT_POST, 'instructor_id'),
                    'duo_voornaam' => filter_input(INPUT_POST, 'duo_voornaam'),
                    'duo_tussenvoegsel' => filter_input(INPUT_POST, 'duo_tussenvoegsel'),
                    'duo_achternaam' => filter_input(INPUT_POST, 'duo_achternaam'),
                    'duo_geboortedatum' => filter_input(INPUT_POST, 'duo_geboortedatum'),
                    'errors' => []
                ];

                // eenvoudige validatie
                if (!$data['package_id'])
                    $data['errors'][] = 'Kies een pakket.';
                if (!$data['location_id'])
                    $data['errors'][] = 'Kies een locatie.';


                // sessions_count ophalen van het gekozen pakket

                $data['time_slots'] = $this->reservationModel->getTimeSlots();




                $data['sessions_count'] = $this->reservationModel->getPackageById($data['package_id']);

                if ($data['sessions_count'] > count($data['time_slots'])) {
                    $data['errors'][] = 'Het aantal gekozen tijdslots is groter dan het aantal sessies in het pakket.';
   $data = [
                    'packages' => $this->reservationModel->getPackages(),
                    'locations' => $this->reservationModel->getLocations(),
                    'errors' => $data['errors'],
                ];                    $this->view('reservations/book', $data);

                } else {

                    // bij fouten: opnieuw formulier tonen met foutmeldingen
                    $this->store($data);
                    // $this->view('reservations/calender', $data);

                    //  if (empty($data['errors'])) 
                    // {
                    //     $newId = $this->reservationModel->createReservation($data);
                    //     // if ($newId) {
                    //         header('Location: ' . URLROOT . '/Homepages/index');
                    //         exit;
                    //     // } else {
                    //     //     $data['errors'] = 'Er ging iets mis bij het reserveren.';
                    //     // }
                    // }
                }
            } else // Dat is een GET-aanroep
            {
                // eerste keer laden: alleen formdata ophalen
                $data = [
                    'packages' => $this->reservationModel->getPackages(),
                    'locations' => $this->reservationModel->getLocations(),
                    'errors' => []
                ];



                $this->view('reservations/book', $data);
            }
        } catch (Exception $e) {
            $data = ['errors' => ['Er is een fout opgetreden: ' . $e->getMessage()]];
            $this->view('reservations/book', $data);
        }
    }

    // simpele bevestigingspagina
    public function success()
    {
        $this->view('reservations/success');
    }

    // Wijzig de status van een reservering

    // Annuleer een reservering (stap 1: reden opgeven)
    public function cancelReservation()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
                if ($reservationId) {
                    $data = [
                        'reservation_id' => $reservationId,
                        'error' => ''
                    ];
                    $this->view('reservations/cancel', $data);
                    return;
                }
            }
            header('Location: ' . URLROOT . '/Reservations/myreservations');
            exit;
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Er is een fout opgetreden: ' . $e->getMessage();
            header('Location: ' . URLROOT . '/Reservations/myreservations');
            exit;
        }
    }

    // Annuleer een reservering (stap 2: reden verwerken)
    public function doCancelReservation()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
                $reason = trim($_POST['cancel_reason'] ?? '');
                if ($reservationId && $reason !== '') {
                    $result = $this->reservationModel->cancelReservation($reservationId, $reason);
                    if ($result) {
                        $_SESSION['flash_success'] = 'Reservering geannuleerd.';
                    } else {
                        $_SESSION['flash_error'] = 'Annuleren mislukt.';
                    }
                } else {
                    $_SESSION['flash_error'] = 'Geef een reden op.';
                }
            }
            header('Location: ' . URLROOT . '/Reservations/myreservations');
            exit;
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Er is een fout opgetreden: ' . $e->getMessage();
            header('Location: ' . URLROOT . '/Reservations/myreservations');
            exit;
        }
    }

    // Toon alleen reserveringen van de ingelogde klant
    public function myreservations()
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                header('Location: ' . URLROOT . '/Users/login');
                exit;
            }
            $reservations = $this->reservationModel->getReservationsByUser($userId);
            $data = [
                'reservations' => $reservations
            ];
            $this->view('Reservations/myreservations', $data);
        } catch (Exception $e) {
            $data = ['reservations' => [], 'errors' => ['Er is een fout opgetreden: ' . $e->getMessage()]];
            $this->view('Reservations/myreservations', $data);
        }
    }

    public function store($data = null)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (empty($_POST['time_slots'])) {
                    $this->view('Reservations/calender', $data);
                } else {
                    if (empty($_POST['package_id']) || empty($_POST['location_id'])) {
                        $data['errors'][] = 'Kies een pakket en een locatie.';
                        $this->view('reservations/book', $data);
                        return;
                    }
                    // Voeg de gekozen timeslots toe aan $data vóór het opslaan
                    $data['time_slots'] = $_POST['time_slots'];
                    $data = [
                        'user_id' => trim($_POST['user_id']),
                        'package_id' => trim($_POST['package_id']),
                        'location_id' => trim($_POST['location_id']),
                        'duo_voornaam' => trim($_POST['duo_voornaam']),
                        'duo_tussenvoegsel' => trim($_POST['duo_tussenvoegsel']),
                        'duo_achternaam' => trim($_POST['duo_achternaam']),
                        'duo_geboortedatum' => trim($_POST['duo_geboortedatum']),
                        'time_slots' => $_POST['time_slots'],
                        'errors' => []
                    ];
                    // Haal e-mail van gebruiker op
                    require_once APPROOT . '/models/UserModel.php';
                    $userModel = new UserModel();
                    $user = $userModel->getuserid(id: $data['user_id']);
                    $userEmail = $user ? $user->email : null;
                    // Haal pakket- en locatiegegevens op
                    $pakketData = $this->reservationModel->getPackageAndLocationInfo($data['package_id'], $data['location_id']);
                    $pakketNaam = $pakketData ? $pakketData->pakketnaam : '';
                    $locatieNaam = $pakketData ? $pakketData->locatie : '';
                    $bedrag = $pakketData ? $pakketData->price : '';
                    $newId = $this->reservationModel->createReservation($data);
                    if (!empty($newId)) {
                        // Factuur e-mail sturen
                        require_once APPROOT . '/libraries/MailHelper.php';
                        $subject = 'Bevestiging & Factuur van uw reservering';
                        $bodyHtml = '<h2>Bedankt voor uw reservering!</h2>' .
                            '<p>Hierbij ontvangt u de factuur van uw reservering.</p>' .
                            '<ul>' .
                            '<li>Pakket: ' . htmlspecialchars($pakketNaam) . '</li>' .
                            '<li>Locatie: ' . htmlspecialchars($locatieNaam) . '</li>' .
                            // Datum en starttijd tonen
                            '<li>Datum(s) & tijd(en):<ul>';
                        if (!empty($data['time_slots'])) {
                            $slots = $this->reservationModel->getTimeSlotsByIds($data['time_slots']);
                            foreach ($slots as $slot) {
                                $bodyHtml .= '<li>' . htmlspecialchars($slot->available_date . ' ' . $slot->start_time . ' - ' . $slot->end_time) . '</li>';
                            }
                        }
                        $bodyHtml .= '</ul></li>' .
                            '</ul>' .
                            '<p>Bedrag: &euro; ' . htmlspecialchars($bedrag) . '</p>';
                        if ($userEmail) {
                            $mailResult = sendInvoiceMail($userEmail, $subject, $bodyHtml);
                            if (!$mailResult) {
                                $data['errors'][] = 'Er is een fout opgetreden bij het versturen van de e-mail. Controleer de SMTP-instellingen in app/libraries/MailHelper.php.';
                            } else {
                                $data['success'] = 'Reservering succesvol aangemaakt! Factuur is per e-mail verzonden.';
                            }
                            $data['new_reservation_id'] = $newId;

                            $this->myreservations();
                        }
                        return;
                    } else {
                        $data['errors'][] = 'Er ging iets mis bij het reserveren.';
                        $this->view('reservations/book', $data);
                    }
                }
            } else {
                $this->view('Reservations/calender', $data);
            }
        } catch (Exception $e) {
            $data['errors'][] = 'Er is een fout opgetreden: ' . $e->getMessage();
            $this->view('reservations/book', $data);
        }
    }



    // Toon reserveringen van klanten voor medewerkers (instructeurs) en beheerders, met dag/week/maand/maand-selectie
    public function klantreserveringen()
    {
        try {
            // Controleer of medewerker of beheerder is ingelogd
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['instructeur', 'beheerder', 'eigenaar'])) {
                header('Location: ' . URLROOT . '/Users/login');
                exit;
            }
            $instructorId = $_SESSION['user_id'];
            $periode = isset($_GET['periode']) ? $_GET['periode'] : null;
            $maand = isset($_GET['maand']) ? $_GET['maand'] : null;
            $selectedInstructor = isset($_GET['instructeur_id']) ? (int) $_GET['instructeur_id'] : null;
            $today = date('Y-m-d');
            $start = $end = null;
            if ($periode === 'dag') {
                $start = $end = $maand ? $maand : $today;
            } elseif ($periode === 'week') {
                $weekStart = $maand ? date('Y-m-d', strtotime('monday this week', strtotime($maand))) : date('Y-m-d', strtotime('monday this week'));
                $weekEnd = $maand ? date('Y-m-d', strtotime('sunday this week', strtotime($maand))) : date('Y-m-d', strtotime('sunday this week'));
                $start = $weekStart;
                $end = $weekEnd;
            } elseif ($periode === 'maand' && $maand) {
                $start = $maand . '-01';
                $end = date('Y-m-t', strtotime($start));
            } elseif ($periode === 'maand') {
                $start = date('Y-m-01');
                $end = date('Y-m-t');
            }
            // Eigenaar kan instructeur kiezen

            if ($_SESSION['user_role'] === 'beheerder' && $selectedInstructor) {
                $instructorId = $selectedInstructor;
            }
            if ($_SESSION['user_role'] === 'beheerder' || $_SESSION['user_role'] === 'eigenaar') {
                if ($start && $end) {
                    if (($selectedInstructor && $_SESSION['user_role'] === 'beheerder') || ($selectedInstructor && $_SESSION['user_role'] === 'eigenaar')) {
                        $reservations = $this->reservationModel->getInstructorLessonsByPeriod($instructorId, $start, $end);
                    } else {
                        $reservations = $this->reservationModel->getAllReservationsByPeriod($start, $end);
                    }
                } else {
                    $reservations = $this->reservationModel->getAllReservations();
                }
            } else {
                if ($start && $end) {
                    $reservations = $this->reservationModel->getInstructorLessonsByPeriod($instructorId, $start, $end);
                } else {
                    $reservations = $this->reservationModel->getReservationsByInstructor($instructorId);
                }
            }
            // Instructeurs ophalen voor dropdown
            $instructors = [];
            if ($_SESSION['user_role'] === 'beheerder') {
                $instructors = $this->reservationModel->getInstructors();
            }
            $unplannedAvailability = $this->reservationModel->getUnplannedAvailability();
            $data = [
                'reservations' => $reservations,
                'periode' => $periode,
                'maand' => $maand,
                'instructors' => $instructors,
                'selected_instructor' => $selectedInstructor,
                'unplanned_availability' => $unplannedAvailability
            ];
            $this->view('reservations/klantreserveringen', $data);
        } catch (Exception $e) {
            $data = ['reservations' => [], 'errors' => ['Er is een fout opgetreden: ' . $e->getMessage()]];
            $this->view('reservations/klantreserveringen', $data);
        }
    }

    // Overzicht van lessen voor instructeur: dag, week, maand
    public function sendCancelMail()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
                $reason = $_POST['reason'] ?? '';
                if (!$reservationId || !in_array($reason, ['ziekte', 'weer'])) {
                    $_SESSION['flash_error'] = 'Ongeldige aanvraag.';
                    header('Location: ' . URLROOT . '/reservations/klantreserveringen');
                    exit;
                }
                // Haal reservering en klantgegevens op
                $reservation = $this->reservationModel->getReservationWithUser($reservationId);
                if (!$reservation) {
                    $_SESSION['flash_error'] = 'Reservering niet gevonden.';
                    header('Location: ' . URLROOT . '/reservations/klantreserveringen');
                    exit;
                }
                $to = $reservation->email;
                $name = $reservation->voornaam . ' ' . $reservation->tussenvoegsel . ' ' . $reservation->achternaam;
                $date = $reservation->available_date;
                $pakket = $reservation->pakket;
                $locatie = $reservation->locatie;
                $instructeur = isset($reservation->instructeur_naam) ? $reservation->instructeur_naam : (isset($reservation->instructeur) ? $reservation->instructeur : '');
                if ($reason === 'ziekte') {
                    $subject = 'Annulering les wegens ziekte instructeur';
                    $body = "Beste $name,<br><br>Helaas moeten wij uw les op $date ($pakket, $locatie) met instructeur $instructeur annuleren wegens ziekte van de instructeur. We nemen contact op voor het inhalen van de les.<br><br>Met vriendelijke groet,<br>Windkracht-12";
                } else {
                    $subject = 'Annulering les wegens slechte weersomstandigheden';
                    $body = "Beste $name,<br><br>Helaas moeten wij uw les op $date ($pakket, $locatie) met instructeur $instructeur annuleren wegens slechte weersomstandigheden (windkracht > 10). We nemen contact op voor het inhalen van de les.<br><br>Met vriendelijke groet,<br>Windkracht-12";
                }
                require_once APPROOT . '/libraries/MailHelper.php';
                $mailResult = sendInvoiceMail($to, $subject, $body);
                // Zet status op 'canceled' in instructor_availability
                $this->reservationModel->cancelInstructorAvailability($reservationId, $reason);
                if ($mailResult) {
                    $_SESSION['flash_success'] = 'Annulingsmail is verstuurd naar de klant.';
                } else {
                    $_SESSION['flash_error'] = 'Fout bij het versturen van de annuleringsmail.';
                }
                header('Location: ' . URLROOT . '/reservations/klantreserveringen');
                exit;
            }
            header('Location: ' . URLROOT . '/reservations/klantreserveringen');
            exit;
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Er is een fout opgetreden: ' . $e->getMessage();
            header('Location: ' . URLROOT . '/reservations/klantreserveringen');
            exit;
        }
    }

    // Herplan reservering na goedkeuring annulering
    public function herplan()
    {
        try {
            $reservation_id = $_POST['reservation_id'] ;
            $reservation = $this->reservationModel->getReservationWithPackage($reservation_id);
            if (!$reservation) {
                $_SESSION['flash_error'] = 'Reservering niet gevonden. ';
                header('Location: ' . URLROOT . '/Reservations/myreservations');
                exit;
            }
            $timeSlots = $this->reservationModel->getTimeSlots();
            $data = [
                'reservation' => $reservation,
                'timeSlots' => $timeSlots,
                // Voeg eventueel andere benodigde data toe
            ];
            // Toon de herplan-view in plaats van de kalender-view
            $this->view('reservations/herplan', $data);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Er is een fout opgetreden: ' . $e->getMessage();
            header('Location: ' . URLROOT . '/Reservations/myreservations');
            exit;
        }
    }

    // Goedkeuren van annulering door beheerder/instructeur
    public function approveCancelation()
    {
        try {
            if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['beheerder', 'instructeur'])) {
                $_SESSION['flash_error'] = 'Geen rechten om te keuren.';
                header('Location: ' . URLROOT . '/reservations/klantreserveringen');
                exit;
            }
            $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
            if (!$reservationId) {
                $_SESSION['flash_error'] = 'Geen reservering geselecteerd.';
                header('Location: ' . URLROOT . '/reservations/klantreserveringen');
                exit;
            }
            // Zet status op 'canceled' in instructor_availability
            if ($this->reservationModel->setInstructorAvailabilityStatus($reservationId, 'geannuleerd')) {
                 $_SESSION['flash_success'] = 'Annulering goedgekeurd. Klant kan nu een nieuwe datum kiezen. ';
            header('Location: ' . URLROOT . '/reservations/klantreserveringen');
            exit;
            } else {
                $_SESSION['flash_error'] = 'Fout bij het goedkeuren van de annulering.';
                header('Location: ' . URLROOT . '/reservations/klantreserveringen');
                exit;
            }
            

        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Er is een fout opgetreden: ' . $e->getMessage();
            header('Location: ' . URLROOT . '/reservations/klantreserveringen');
            exit;
        }
    }

    // Verwerk herplannen van een reservering
    public function doHerplan()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . URLROOT . '/Users/login');
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
                $timeSlotId = filter_input(INPUT_POST, 'time_slot_id', FILTER_VALIDATE_INT);
                if (!$reservationId || !$timeSlotId) {
                    $_SESSION['flash_error'] = 'Kies een tijdslot.';
                    header('Location: ' . URLROOT . '/Reservations/herplan?reservation_id=' . $reservationId);
                    exit;
                }
                // Koppel het nieuwe tijdslot aan de reservering
                $result = $this->reservationModel->replanReservation($reservationId, $timeSlotId);
                if ($result) {
                    $_SESSION['flash_success'] = 'Nieuwe lesdatum is gekozen!';
                    header('Location: ' . URLROOT . '/Reservations/myreservations');
                    exit;
                } else {
                    $_SESSION['flash_error'] = 'Herplannen mislukt.';
                    header('Location: ' . URLROOT . '/Reservations/herplan?reservation_id=' . $reservationId);
                    exit;
                }
            }
            header('Location: ' . URLROOT . '/Reservations/myreservations');
            exit;
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Er is een fout opgetreden: ' . $e->getMessage();
            header('Location: ' . URLROOT . '/Reservations/myreservations');
            exit;
        }
    }


    /**
     * Toon overzicht van alle reserveringen met status-dropdown
     */
    public function index()
    {
        try {
            // Haal reserveringen inclusief instructeur op
            if ($_SESSION['user_role'] === 'instructeur') {
                // Instructeur ziet alleen zijn eigen lessen
                $result = $this->reservationModel->getReservationsByInstructor($_SESSION['user_id']);
            } else {
                // Beheerder ziet alle lessen
                $result = $this->reservationModel->getAllReservationsWithInstructor();
            }
            $data = [
                'reservations' => $result,
                'statuses' => ['gepland', 'voltooid', 'geannuleerd', 'annulering_in_afwachting', 'beschikbaar', 'herpland']
            ];
            $this->view('reservations/index', $data);
        } catch (Exception $e) {
            $data = ['reservations' => [], 'statuses' => ['gepland', 'voltooid', 'geannuleerd', 'annulering_in_afwachting', 'beschikbaar', 'herpland'], 'errors' => ['Er is een fout opgetreden: ' . $e->getMessage()]];
            $this->view('reservations/index', $data);
        }
    }

    public function inplannen()
    {
        try {
            // Alleen beheerder mag deze functie gebruiken
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'beheerder') {
                header('HTTP/1.1 403 Forbidden');
                exit('Toegang geweigerd.');
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $instructor_id = $_POST['instructor_id'];
                $date = $_POST['available_date'];
                $start_time = $_POST['start_time'];
                $end_time = $_POST['end_time'];
                $data = [
                    'instructor_id' => $instructor_id,
                    'available_date' => $date,
                    'start_time' => $start_time,
                    'end_time' => $end_time
                ];
                $this->reservationModel->inplannen($data);
                $_SESSION['flash_success'] = 'Beschikbaarheid toegevoegd.';
                header('Location: ' . URLROOT . '/homepages/index');
                exit;
            } else {
                // Toon formulier voor beschikbaarheid toevoegen
                $instructors = $this->reservationModel->getInstructors();
                $data = [
                    'instructors' => $instructors
                ];
                $this->view('reservations/inplannen', $data);
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Er is een fout opgetreden: ' . $e->getMessage();
            header('Location: ' . URLROOT . '/homepages/index');
            exit;
        }
    }

    /**
     * Verwerk status-wijziging (POST)
     */
    public function changeStatus()
    {

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . URLROOT . '/reservations/index');
                return;
            }

            $id = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

            // simpele validatie
            $validStatuses = ['gepland', 'voltooid', 'geannuleerd', 'annulering_in_afwachting', 'beschikbaar', 'herpland'];
            if (!$id || !in_array($status, $validStatuses, true)) {
                $_SESSION['flash_error'] = 'Ongeldige statuswijziging.';
                header('Location: ' . URLROOT . '/reservations/index');
                exit;
            }


            $succes = $this->reservationModel->updateStatus($id, $status);
            if ($succes) {
                $_SESSION['flash_success'] = 'Status bijgewerkt.';
            } else {
                $_SESSION['flash_error'] = 'Bijwerken is mislukt.';
            }

            header('Location: ' . URLROOT . '/reservations/index');
            exit;
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Er is een fout opgetreden: ' . $e->getMessage();
            header('Location: ' . URLROOT . '/reservations/index');
            exit;
        }
    }

    public function handleCancelReason()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once APPROOT . '/libraries/MailHelper.php';

                $reservationId = $_POST['reservation_id'];
                $Insructor_availibilityId = $_POST['instructor_availability_id'];
                $action = $_POST['action']; // 'accept' of 'deny'
                // Haal klant-email op via reservationId

                $reservationArr = $this->reservationModel->getReservationById($reservationId);
                $reservation = is_array($reservationArr) ? reset($reservationArr) : $reservationArr;
                $userEmail = $reservation->email;

                if ($action === 'accept') {
                    // Update status in DB, stuur mail naar klant
                    $this->reservationModel->setCancelStatus($Insructor_availibilityId, 'geannuleerd');
                    sendInvoiceMail($userEmail, "Annulering geaccepteerd", "Je annulering is geaccepteerd.");
                } else {
                    $this->reservationModel->setCancelStatus($Insructor_availibilityId, 'gepland');
                    sendInvoiceMail($userEmail, "Annulering geweigerd", "Je annulering is geweigerd.");
                }
                $_SESSION['flash_success'] = 'Actie verwerkt en klant is gemaild.';
                header('Location: ' . URLROOT . '/Reservations/index');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Er is een fout opgetreden: ' . $e->getMessage();
            header('Location: ' . URLROOT . '/Reservations/index');
            exit;
        }
    }

    public function update($id)
{
    $reservation = $this->reservationModel->getReservationById($id); // eventueel tonen voorformulier

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'user_id' => $_POST['user_id'],
            'package_id' => $_POST['package_id'],
            'location_id' => $_POST['location_id'],
            'aantal' => $_POST['aantal'],
            'duo_voornaam' => $_POST['duo_voornaam'],
            'duo_tussenvoegsel' => $_POST['duo_tussenvoegsel'],
            'duo_achternaam' => $_POST['duo_achternaam'],
            'duo_geboortedatum' => $_POST['duo_geboortedatum'],
            'betaald' => isset($_POST['betaald']) ? 1 : 0,
            'definitief' => isset($_POST['definitief']) ? 1 : 0
        ];

        $this->reservationModel->updateReservation($id, $data);

        header("Location: /reservations/index");
        exit;
    }

    require 'views/reservations/updatepage.php'; // Je view voor het bewerken
}

    public function editInstructorAvailability($id)
    {
        require_once APPROOT . '/models/ReservationModel.php';
        $model = new ReservationModel();
        $availability = $model->getInstructorAvailabilityById($id);
        $instructors = $model->getInstructors();
        if (!$availability) {
            $_SESSION['flash_error'] = 'Beschikbaarheid niet gevonden.';
            header('Location: ' . URLROOT . '/reservations/index');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'available_date' => $_POST['available_date'],
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'],
                'status' => $_POST['status'],
                'instructor_id' => $_POST['instructor_id']
            ];
            $model->updateInstructorAvailability($id, $data);
            $_SESSION['flash_success'] = 'Beschikbaarheid bijgewerkt!';
            header('Location: ' . URLROOT . '/reservations/index');
            exit;
        }
        $this->view('reservations/edit_instructor_availability', ['availability' => (array)$availability, 'instructors' => $instructors]);
    }

    public function edit($id)
    {
        $reservation = $this->reservationModel->getReservationById($id);
        if (!$reservation) {
            $_SESSION['flash_error'] = 'Reservering niet gevonden.';
            header('Location: ' . URLROOT . '/reservations/index');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id' => $_POST['user_id'],
                'package_id' => $_POST['package_id'],
                'location_id' => $_POST['location_id'],
                'aantal' => $_POST['aantal'],
                'duo_voornaam' => $_POST['duo_voornaam'],
                'duo_tussenvoegsel' => $_POST['duo_tussenvoegsel'],
                'duo_achternaam' => $_POST['duo_achternaam'],
                'duo_geboortedatum' => $_POST['duo_geboortedatum'],
                'betaald' => isset($_POST['betaald']) ? 1 : 0,
                'definitief' => isset($_POST['definitief']) ? 1 : 0
            ];
            $this->reservationModel->updateReservation($id, $data);
            $_SESSION['flash_success'] = 'Reservering bijgewerkt!';
            header('Location: ' . URLROOT . '/reservations/index');
            exit;
        }
        // Haal extra data op voor het formulier indien nodig (pakketten, locaties, etc.)
        $packages = $this->reservationModel->getPackages();
        $locations = $this->reservationModel->getLocations();
        $this->view('reservations/edit', [
            'reservation' => $reservation,
            'packages' => $packages,
            'locations' => $locations
        ]);
    }

    public function makeDefinitive()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
            if (!$reservationId) {
                $_SESSION['flash_error'] = 'Geen geldige reservering geselecteerd.';
                header('Location: ' . URLROOT . '/reservations/index');
                exit;
            }
            // Zet reservering op definitief
            $this->reservationModel->maakeDefinitive($reservationId);
            // Haal reservering, klant en instructeur(s) op
            $instructors = $this->reservationModel->getInstructorsinreservation($reservationId);


            $reservation = $this->reservationModel->getReservationWithUser($reservationId);
            // Mail naar klant
            
            
            require_once APPROOT . '/libraries/MailHelper.php';
            if ($reservation && !empty($reservation->email)) {
                $subject = 'Reservering definitief';
                $body = 'Beste ' . htmlspecialchars($reservation->voornaam) . ' ' . htmlspecialchars($reservation->tussenvoegsel) . ' ' . htmlspecialchars($reservation->achternaam) . ',<br>Uw reservering is nu definitief.';
                sendInvoiceMail($reservation->email, $subject, $body);
            }
            // Mail naar instructeur(s)
            if ($instructors) {
                foreach ($instructors as $instructor) {
                    if (!empty($instructor->email)) {
                        $subject = 'Nieuwe definitieve reservering';
                        $body = 'Beste ' . htmlspecialchars($instructor->voornaam ?? '') . ' ' . htmlspecialchars($instructor->tussenvoegsel ?? '') . ' ' . htmlspecialchars($instructor->achternaam ?? '') . 
                        ',<br>Er is een reservering definitief gemaakt. Details:<br>' .
                        'Datum: ' . htmlspecialchars($instructor->available_date ?? '') . '<br>' .
                        'Tijd: ' . htmlspecialchars($instructor->start_time ?? '') . ' - ' . htmlspecialchars($instructor->end_time ?? '') . '<br>' .
                        'Locatie: ' . htmlspecialchars($reservation->locatie ?? '') . '<br>' .
                        'Pakket: ' . htmlspecialchars($reservation->pakket ?? '') . '<br>' .
                        'naam: ' . htmlspecialchars($reservation->voornaam ?? '') . ' ' .
                          htmlspecialchars($reservation->tussenvoegsel ?? '') . ' ' .
                         htmlspecialchars($reservation->achternaam ?? '') ;
                        sendInvoiceMail($instructor->email, $subject, $body);
                    } else {
                        $_SESSION['flash_error'] = 'Instructeur zonder e-mail gevonden, kan geen notificatie sturen.';
                    }
                }
            }
            $_SESSION['flash_success'] = 'Reservering is definitief gemaakt en e-mails zijn verzonden.';
            header('Location: ' . URLROOT . '/reservations/index');
                exit;
            }
        header('Location: ' . URLROOT . '/reservations/index');
        exit;
    }
}



