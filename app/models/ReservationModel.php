<?php
class ReservationModel
{
    private $db;

    public function __construct()
    {
        // assumes je Database-wrapper staat in app/libraries/Database.php
        $this->db = new Database();
    }

    // Haal alle lespakketten op
    public function getPackages()
    {
        $this->db->query("SELECT id, name FROM packages");
        return $this->db->resultSet();
    }

    // Haal alle locaties op
    public function getLocations()
    {
        $this->db->query("SELECT id, name FROM locations");
        return $this->db->resultSet();
    }

    // Haal alle instructeurs op
    public function getInstructors()
    {
        $this->db->query("SELECT id, voornaam, tussenvoegsel, achternaam 
                          FROM gebruikers 
                          WHERE rol = 'instructeur' or rol = 'beheerder'");
        return $this->db->resultSet();
    }

    // Maak een nieuwe reservation aan
    public function createReservation(array $data)
    {
        $this->db->query(
            "INSERT INTO reservations 
             (user_id, package_id, location_id,  duo_voornaam, duo_tussenvoegsel, duo_achternaam, duo_geboortedatum )
             VALUES 
             (:user_id, :package_id, :location_id,   :duo_voornaam, :duo_tussenvoegsel, :duo_achternaam, :duo_geboortedatum)"
        );
        $this->db->bind(":user_id", $data['user_id'], PDO::PARAM_INT);
        $this->db->bind(":package_id", $data['package_id'], PDO::PARAM_INT);
        $this->db->bind(":location_id", $data['location_id'], PDO::PARAM_INT);
        $this->db->bind(":duo_voornaam", $data['duo_voornaam'] ?: null, PDO::PARAM_STR);
        $this->db->bind(":duo_tussenvoegsel", $data['duo_tussenvoegsel'] ?: null, PDO::PARAM_STR);
        $this->db->bind(":duo_achternaam", $data['duo_achternaam'] ?: null, PDO::PARAM_STR);
        $this->db->bind(":duo_geboortedatum", $data['duo_geboortedatum'] ?: null, PDO::PARAM_STR);

        if ($this->db->execute()) {
            // Haal het ID van de nieuwe reservering op via een query
            $this->db->query("SELECT LAST_INSERT_ID() AS id");
            $row = $this->db->single();
            $reservationId = $row ? $row->id : null;
            if ($reservationId && !empty($data['time_slots']) && is_array($data['time_slots'])) {
                foreach ($data['time_slots'] as $slotId) {

                    $this->db->query("UPDATE instructor_availability 
                                      SET beschikbaar = 0 ,
                                       reservationid = :reservationId
                                      WHERE id = :slotId AND beschikbaar = 1");
                    $this->db->bind(':reservationId', $reservationId, PDO::PARAM_INT);
                    $this->db->bind(':slotId', $slotId, PDO::PARAM_INT);
                    $this->db->execute();
                }
                return $reservationId; // Return the new reservation ID
            }

        }
    }

    public function getAllReservations()
    {
        $sql = "
            SELECT 
                r.id,
                u.voornaam,
                u.tussenvoegsel,
                u.achternaam,
                p.name      AS pakket,
                l.name      AS locatie,
                i.status,
                r.duo_voornaam,
                r.duo_tussenvoegsel,
                r.duo_achternaam,
                r.duo_geboortedatum,
                i.cancel_reason,
                i.start_time,
                i.available_date ,
                i.id AS instructor_availability_id,
                i.end_time 


            FROM reservations r
            JOIN gebruikers u   ON r.user_id = u.id
            JOIN packages p     ON r.package_id = p.id
            JOIN locations l    ON r.location_id = l.id
            JOIN instructor_availability i ON r.id = i.reservationid
            WHERE i.beschikbaar = 0 AND i.available_date >= CURDATE() 
            ORDER BY i.available_date, i.start_time
            ;

        ";
        //WHERE r.status != 'canceled'
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // Haal alle reserveringen inclusief instructeur-naam op
    public function getAllReservationsWithInstructor()
    {
        $sql = "
            SELECT 
                r.id,
                u.voornaam,
                u.tussenvoegsel,
                u.achternaam,
                r.definitief,
                r.betaald,
                p.name      AS pakket,
                l.name      AS locatie,
                i.status,
                r.duo_voornaam,
                r.duo_tussenvoegsel,
                r.duo_achternaam,
                r.duo_geboortedatum,
                i.cancel_reason,
                i.start_time,
                i.available_date ,
                i.id AS instructor_availability_id,
                i.end_time,
                g.voornaam AS instructeur_voornaam,
                g.tussenvoegsel AS instructeur_tussenvoegsel,
                g.achternaam AS instructeur_achternaam
            FROM reservations r
            JOIN gebruikers u   ON r.user_id = u.id
            JOIN packages p     ON r.package_id = p.id
            JOIN locations l    ON r.location_id = l.id
            JOIN instructor_availability i ON r.id = i.reservationid
            JOIN gebruikers g ON i.instructor_id = g.id
            ORDER BY i.available_date, i.start_time
        ";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Update de status van een geselecteerde reservering
     */
    public function updateStatus(int $reservationId, string $newStatus)
    {
        $this->db->query("
            UPDATE instructor_availability
            SET status = :status
            WHERE id = :id
        ");
        $this->db->bind(':status', $newStatus, PDO::PARAM_STR);
        $this->db->bind(':id', $reservationId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    // Annuleer reservering in database, zet status op 'pending_cancel' en sla reden op
    public function cancelReservation($reservationId, $reason)
    {
        $this->db->query("UPDATE instructor_availability SET cancel_reason = :reason, status = 'annulering_in_afwachting' WHERE id = :id");
        $this->db->bind(':reason', $reason, PDO::PARAM_STR);
        $this->db->bind(':id', $reservationId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    // Zet status op 'canceled' in instructor_availability voor een reservering
    public function cancelInstructorAvailability($reservationId, $reason = null)
    {
        // Zet status op 'geannuleerd' voor bestaande instructor_availability
        $sql = "UPDATE instructor_availability SET status = 'geannuleerd' WHERE id = :reservationId";
        $this->db->query($sql);
        $this->db->bind(':reservationId', $reservationId, PDO::PARAM_INT);
        return $this->db->execute();

        // Alleen als er een reden is, plan een nieuwe beschikbaarheid in
       
    }

    // Haal reserveringen op van een specifieke gebruiker
    public function getReservationsByUser($userId)
    {
        $sql = "
            SELECT 
                r.id,
                p.name      AS pakket,
                l.name      AS locatie,
                i.status,
                r.duo_voornaam,
                r.duo_tussenvoegsel,
                r.duo_achternaam,
                r.duo_geboortedatum,
                r.betaald,
                r.definitief,
                i.start_time,
                i.available_date ,
                i.id AS instructor_availability_id
            FROM reservations r
            JOIN packages p     ON r.package_id = p.id
            JOIN locations l    ON r.location_id = l.id
            JOIN instructor_availability i ON r.id = i.reservationid
            WHERE r.user_id = :user_id
            ;
        ";
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    public function getReservationWithPackage($reservationId)
    {
        $this->db->query("
        SELECT r.* , p.sessions_count 
        FROM reservations r 
        JOIN packages p ON r.package_id = p.id 
        WHERE r.id = :reservationId
    ");
        $this->db->bind(':reservationId', $reservationId, PDO::PARAM_INT);

        return $this->db->resultSet();
        ;
    }

    public function getPackageById($packageId)
    {
        $this->db->query("SELECT sessions_count FROM packages WHERE id = :packageId");
        $this->db->bind(':packageId', $packageId, PDO::PARAM_INT);

        $result = $this->db->single();
        return $result ? (int) $result->sessions_count : 0; // Return 0 if not found



    }

    public function getTimeSlots()
    {
        $this->db->query("  SELECT  instructor_id, reservationid , id, beschikbaar, status, cancel_reason,
available_date, start_time, end_time FROM instructor_availability WHERE available_date > CURDATE() AND beschikbaar = 1 ORDER BY available_date, start_time
 ");
        return $this->db->resultSet();
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Retrieve available time slots for a specific instructor.
     *
     * @param int $instructorId The ID of the instructor.
     * @return array An array of available time slots for the given instructor.
     */
        public function getTimeSlotsByInstructor($instructorId)
    {
        $this->db->query("SELECT * FROM instructor_availability WHERE instructor_id = :instructorId AND beschikbaar = 1");
        $this->db->bind(':instructorId', $instructorId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function inplannen($data)
    {
        $this->db->query("insert into instructor_availability (instructor_id, available_date, start_time, end_time, beschikbaar)
        values (:instructor_id, :available_date, :start_time, :end_time, 1)");
        $this->db->bind(':instructor_id', $data['instructor_id'], PDO::PARAM_INT);
        $this->db->bind(':available_date', $data['available_date'], PDO::PARAM_STR);
        $this->db->bind(':start_time', $data['start_time'], PDO::PARAM_STR);
        $this->db->bind(':end_time', $data['end_time'], PDO::PARAM_STR);
        return $this->db->execute();
    }

    public function getavailabilityByInstructor($instructorId)
    {
        $this->db->query("SELECT * FROM instructor_availability WHERE instructor_id = :instructorId AND beschikbaar = 1");
        $this->db->bind(':instructorId', $instructorId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }


    public function getPlannings()
    {
        $this->db->query("SELECT i.*, g.voornaam, g.tussenvoegsel, g.achternaam FROM instructor_availability i JOIN gebruikers g ON i.instructor_id = g.id WHERE i.available_date > CURDATE() ORDER BY i.available_date, i.start_time");
        return $this->db->resultSet();
    }

    // Haal pakketnaam, locatie en prijs op
    public function getPackageAndLocationInfo($packageId, $locationId)
    {
        $this->db->query("SELECT p.name AS pakketnaam, p.price, l.name AS locatie FROM packages p JOIN locations l ON l.id = :location_id WHERE p.id = :package_id");
        $this->db->bind(':package_id', $packageId, PDO::PARAM_INT);
        $this->db->bind(':location_id', $locationId, PDO::PARAM_INT);
        return $this->db->single();
    }

    // Haal reserveringen op voor een instructeur (medewerker)
    public function getReservationsByInstructor($instructorId)
    {
        $sql = "
            SELECT 
                r.id,
                u.voornaam,
                u.tussenvoegsel,
                u.achternaam,
                p.name      AS pakket,
                l.name      AS locatie,
                r.duo_voornaam,
                r.duo_tussenvoegsel,
                r.duo_achternaam,
                r.duo_geboortedatum,
                r.betaald,
                r.definitief,
                i.start_time,
                i.available_date,
                i.status,
                i.id AS instructor_availability_id
            FROM reservations r
            JOIN gebruikers u   ON r.user_id = u.id
            JOIN packages p     ON r.package_id = p.id
            JOIN locations l    ON r.location_id = l.id
            JOIN instructor_availability i ON r.id = i.reservationid
            WHERE i.instructor_id = :instructor_id
            ORDER BY i.available_date, i.start_time
        ";
        $this->db->query($sql);
        $this->db->bind(':instructor_id', $instructorId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Haal lessen van instructeur op binnen een periode (dag, week, maand)
    public function getInstructorLessonsByPeriod($instructorId, $startDate, $endDate)
    {
        $sql = "
            SELECT 
                r.id,
                u.voornaam,
                u.tussenvoegsel,
                u.achternaam,
                p.name      AS pakket,
                l.name      AS locatie,
                r.duo_voornaam,
                r.duo_tussenvoegsel,
                r.duo_achternaam,
                r.duo_geboortedatum,
                r.betaald,
                r.definitief,
                i.start_time,
                i.available_date,
                i.status,
                i.id AS instructor_availability_id,
                i.instructor_id AS instructor_id,
                g.voornaam AS instructeur_voornaam,
                g.tussenvoegsel AS instructeur_tussenvoegsel,
                g.achternaam AS instructeur_achternaam
            FROM reservations r
            JOIN gebruikers u   ON r.user_id = u.id
            JOIN packages p     ON r.package_id = p.id
            JOIN locations l    ON r.location_id = l.id
            JOIN instructor_availability i ON r.id = i.reservationid
            JOIN gebruikers g ON i.instructor_id = g.id
            WHERE i.instructor_id = :instructor_id
              AND i.available_date BETWEEN :start AND :end
            ORDER BY i.available_date, i.start_time
        ";
        $this->db->query($sql);
        $this->db->bind(':instructor_id', $instructorId, PDO::PARAM_INT);
        $this->db->bind(':start', $startDate, PDO::PARAM_STR);
        $this->db->bind(':end', $endDate, PDO::PARAM_STR);
        return $this->db->resultSet();
    }

    // Haal alle reserveringen voor alle instructeurs in een periode (voor beheerder)
    public function getAllReservationsByPeriod($startDate, $endDate)
    {
        $sql = "
            SELECT 
                r.id,
                u.voornaam,
                u.tussenvoegsel,
                u.achternaam,
                p.name      AS pakket,
                l.name      AS locatie,
                r.duo_voornaam,
                r.duo_tussenvoegsel,
                r.duo_achternaam,
                r.duo_geboortedatum,
                r.betaald,
                r.definitief,
                i.start_time,
                i.available_date,
                i.status,
                i.id AS instructor_availability_id,
                i.instructor_id AS instructor_id,
                g.voornaam AS instructeur_voornaam,
                g.tussenvoegsel AS instructeur_tussenvoegsel,
                g.achternaam AS instructeur_achternaam
            FROM reservations r
            JOIN gebruikers u   ON r.user_id = u.id
            JOIN packages p     ON r.package_id = p.id
            JOIN locations l    ON r.location_id = l.id
            JOIN instructor_availability i ON r.id = i.reservationid
            JOIN gebruikers g ON i.instructor_id = g.id
            WHERE i.available_date BETWEEN :start AND :end
            ORDER BY i.available_date, i.start_time
        ";
        $this->db->query($sql);
        $this->db->bind(':start', $startDate, PDO::PARAM_STR);
        $this->db->bind(':end', $endDate, PDO::PARAM_STR);
        return $this->db->resultSet();
    }

    // Haal alle niet-ingeroosterde beschikbaarheid op (beschikbaar = 1, geen reservering)
    public function getUnplannedAvailability()
    {
        $sql = "
            SELECT ia.*, g.voornaam, g.tussenvoegsel, g.achternaam
            FROM instructor_availability ia
            JOIN gebruikers g ON ia.instructor_id = g.id
            WHERE ia.beschikbaar = 1
              AND (ia.reservationid IS NULL OR ia.reservationid = 0)
              AND ia.available_date >= CURDATE()
            ORDER BY ia.available_date, ia.start_time
        ";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // Haal reservering + klantgegevens voor annulering
    public function getReservationWithUser($reservationId)
    {
        $sql = "
            SELECT r.*, u.email, u.voornaam, u.tussenvoegsel, u.achternaam, p.name AS pakket, l.name AS locatie, i.available_date
            FROM reservations r
            JOIN gebruikers u ON r.user_id = u.id
            JOIN packages p ON r.package_id = p.id
            JOIN locations l ON r.location_id = l.id
            JOIN instructor_availability i ON r.id = i.reservationid
            WHERE i.id = :reservationId
            LIMIT 1
        ";
        $this->db->query($sql);
        $this->db->bind(':reservationId', $reservationId, PDO::PARAM_INT);
        return $this->db->single();
    }

    // Zet status van instructor_availability handmatig
    public function setInstructorAvailabilityStatus($availabilityId, $status)
    {
        $this->db->query("UPDATE instructor_availability SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status, PDO::PARAM_STR);
        $this->db->bind(':id', $availabilityId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    // Herplan reservering: koppel nieuw tijdslot aan bestaande reservering
    public function replanReservation($reservationId, $timeSlotId)
    {
        // Zet oude tijdslot op niet-beschikbaar en status op 'herpland'
        $this->db->query("UPDATE instructor_availability SET beschikbaar = 0, status = 'herpland' WHERE reservationid = :reservationId");
        $this->db->bind(':reservationId', $reservationId, PDO::PARAM_INT);
        $this->db->execute();
        // Koppel nieuwe tijdslot aan reservering
        $this->db->query("UPDATE instructor_availability SET reservationid = :reservationId, status = 'gepland', beschikbaar = 0 WHERE id = :timeSlotId");
        $this->db->bind(':reservationId', $reservationId, PDO::PARAM_INT);
        $this->db->bind(':timeSlotId', $timeSlotId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function setCancelStatus($reservationId, $status)
    {
        $oudeStatus = $status; // Bewaar de oude status voor eventuele logging of andere doeleinden
        $oudereservering = $reservationId ; // Bewaar de oude reservering ID voor eventuele logging of andere doeleinden
        $this->db->query("UPDATE instructor_availability SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status, PDO::PARAM_STR);
        $this->db->bind(':id', $reservationId, PDO::PARAM_INT);
        $this->db->execute();

        // Alleen als status 'geannuleerd' is en er een reden is opgegeven
        if ($oudeStatus === 'geannuleerd' ) {
            // Haal de oude beschikbaarheid op
            $this->db->query("SELECT * FROM instructor_availability WHERE id = :reservationId LIMIT 1");
            $this->db->bind(':reservationId', $oudereservering, PDO::PARAM_INT);
            $old = $this->db->single();
            if ($old) {

                // Insert nieuwe beschikbaarheid met dezelfde data, maar zonder reservering
                $this->db->query("INSERT INTO instructor_availability (instructor_id, available_date, start_time, end_time, status) VALUES (:instructor_id, :available_date, :start_time, :end_time,  'beschikbaar')");
                $this->db->bind(':instructor_id', $old->instructor_id, PDO::PARAM_INT);
                $this->db->bind(':available_date', $old->available_date, PDO::PARAM_STR);
                $this->db->bind(':start_time', $old->start_time, PDO::PARAM_STR);
                $this->db->bind(':end_time', $old->end_time, PDO::PARAM_STR);
                $this->db->execute();
            }
        }
    }
    

    public function getReservationById($reservationId)
    {
        $this->db->query("
            SELECT r.*, u.email
            FROM reservations r
            JOIN gebruikers u ON r.user_id = u.id
            WHERE r.id = :id
            LIMIT 1
        ");
        $this->db->bind(':id', $reservationId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function updateReservation($id, $data)
    {
        $this->db->query("UPDATE reservations SET 
                user_id = :user_id,
                package_id = :package_id,
                location_id = :location_id,
                aantal = :aantal,
                duo_voornaam = :duo_voornaam,
                duo_tussenvoegsel = :duo_tussenvoegsel,
                duo_achternaam = :duo_achternaam,
                duo_geboortedatum = :duo_geboortedatum,
                betaald = :betaald,
                definitief = :definitief
            WHERE id = :id");


        $this->db->bind(':user_id', $data['user_id'], PDO::PARAM_INT);
        $this->db->bind(':package_id', $data['package_id'], PDO::PARAM_INT);
        $this->db->bind(':location_id', $data['location_id'], PDO::PARAM_INT);
        $this->db->bind(':aantal', $data['aantal'], PDO::PARAM_INT);
        $this->db->bind(':duo_voornaam', $data['duo_voornaam'] !== '' ? $data['duo_voornaam'] : null, PDO::PARAM_STR);
        $this->db->bind(':duo_tussenvoegsel', $data['duo_tussenvoegsel'] !== '' ? $data['duo_tussenvoegsel'] : null, PDO::PARAM_STR);
        $this->db->bind(':duo_achternaam', $data['duo_achternaam'] !== '' ? $data['duo_achternaam'] : null, PDO::PARAM_STR);
        $this->db->bind(':duo_geboortedatum', $data['duo_geboortedatum'] !== '' ? $data['duo_geboortedatum'] : null, PDO::PARAM_STR);
        $this->db->bind(':betaald', $data['betaald'], PDO::PARAM_STR);
        $this->db->bind(':definitief', $data['definitief'], PDO::PARAM_STR);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();

    }

    public function getInstructorAvailabilityById($id)
    {
        $this->db->query("SELECT * FROM instructor_availability WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function updateInstructorAvailability($id, $data)
    {
        $this->db->query("UPDATE instructor_availability SET available_date = :available_date, start_time = :start_time, end_time = :end_time, status = :status, instructor_id = :instructor_id WHERE id = :id");
        $this->db->bind(':available_date', $data['available_date'], PDO::PARAM_STR);
        $this->db->bind(':start_time', $data['start_time'], PDO::PARAM_STR);
        $this->db->bind(':end_time', $data['end_time'], PDO::PARAM_STR);
        $this->db->bind(':status', $data['status'], PDO::PARAM_STR);
        $this->db->bind(':instructor_id', $data['instructor_id'], PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function getInstructorById($instructorId)
    {
        $this->db->query("SELECT * FROM gebruikers WHERE id = :id");
        $this->db->bind(':id', $instructorId, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function maakeDefinitive($reservationId)
    {
        $this->db->query("UPDATE reservations SET definitief = 1 WHERE id = :reservationId");
        $this->db->bind(':reservationId', $reservationId, PDO::PARAM_INT);
        return $this->db->execute();
    }



    public function getInstructorsinreservation($reservationId)
    {
        $this->db->query("SELECT 
    g.id AS instructeur_id,
    g.voornaam,
    g.tussenvoegsel,
    g.achternaam,
    g.email,
    r.id AS reservatie_id,
    ia.available_date,
    ia.start_time,
    ia.end_time
FROM 
    gebruikers g
JOIN 
    instructor_availability ia ON g.id = ia.instructor_id
JOIN 
    reservations r ON ia.reservationid = r.id
WHERE 
    r.id = :id
");
        $this->db->bind(':id', $reservationId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // Haal tijdslots op basis van een lijst met ids
    public function getTimeSlotsByIds($ids)
    {
        if (empty($ids) || !is_array($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id, available_date, start_time, end_time FROM instructor_availability WHERE id IN ($placeholders) ORDER BY available_date, start_time";
        $this->db->query($sql);
        foreach ($ids as $idx => $id) {
            $this->db->bind(($idx+1), $id, PDO::PARAM_INT);
        }
        return $this->db->resultSet();
    }

}