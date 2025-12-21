<?php

class AccountoverzichtModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }


    public function getAllAccountoverzicht()
    {
        $sql = 'SELECT  ZGRS.id
                       ,CONCAT(ZGRS.voornaam, " ", IFNULL(ZGRS.tussenvoegsel, ""), " ", ZGRS.achternaam) as naam
                       ,ZGRS.email
                       ,ZGRS.telefoon
                       ,ZGRS.rol
                       ,ZGRS.actief
                       ,ZGRS.adres
                       ,ZGRS.woonplaats
                       ,ZGRS.geboortedatum
                       ,ZGRS.bsn
                FROM   gebruikers as ZGRS
                ORDER BY ZGRS.id DESC';

        $this->db->query($sql);

        return $this->db->resultSet();
    }

    public function deleteAccountoverzicht($id)
    {
        $sql = 'UPDATE gebruikers SET actief = 0  WHERE Id = :id';

        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);

        return $this->db->execute();
    }

    public function getidAccountoverzicht($id)
    {
        $sql = 'SELECT  ZGRS.id
,zgrs.voornaam
                       ,ZGRS.tussenvoegsel
                       ,ZGRS.achternaam
                       ,ZGRS.email
                       ,ZGRS.telefoon
                       ,ZGRS.rol
                       ,ZGRS.adres
                       ,ZGRS.woonplaats
                       ,ZGRS.geboortedatum
                       ,ZGRS.bsn
                FROM   gebruikers as ZGRS
                WHERE ZGRS.id = :id';

        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_STR);

        return $this->db->resultSet();
    }


    public function updateAccount(array $data): bool
    {
        // Verplichte velden checken
        if (empty($data['email']) || empty($data['rol']) || empty($data['id'])) {
            return false; // Of gooi een Exception
        }

        $sql = '
        UPDATE gebruikers SET 
            voornaam = :voornaam,
            tussenvoegsel = :tussenvoegsel,
            achternaam = :achternaam,
            email = :email,
            telefoon = :telefoon,
            rol = :rol,
            adres = :adres,
            woonplaats = :woonplaats,
            geboortedatum = :geboortedatum,
            bsn = :bsn
        WHERE id = :id
    ';

        $this->db->query($sql);

        // Bind alle velden
        $this->db->bind(':voornaam', $data['voornaam'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':tussenvoegsel', $data['tussenvoegsel'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':achternaam', $data['achternaam'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':email', $data['email'], PDO::PARAM_STR); // verplicht
        $this->db->bind(':telefoon', $data['telefoon'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':rol', $data['rol'], PDO::PARAM_STR); // verplicht
        $this->db->bind(':adres', $data['adres'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':woonplaats', $data['woonplaats'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':geboortedatum', $data['geboortedatum'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':bsn', $data['bsn'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':id', $data['id'], PDO::PARAM_INT);

        return $this->db->execute();
    }

    public function addAccount(array $data): bool
    {
        $sql = 'INSERT INTO gebruikers (voornaam, tussenvoegsel, achternaam, email, telefoon, rol, adres, woonplaats, geboortedatum, actief)
                VALUES (:voornaam, :tussenvoegsel, :achternaam, :email, :telefoon, :rol, :adres, :woonplaats, :geboortedatum, :actief)';
        $this->db->query($sql);
        $this->db->bind(':voornaam', $data['voornaam'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':tussenvoegsel', $data['tussenvoegsel'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':achternaam', $data['achternaam'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':email', $data['email'], PDO::PARAM_STR);
        $this->db->bind(':telefoon', $data['telefoon'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':rol', $data['rol'], PDO::PARAM_STR);
        $this->db->bind(':adres', $data['adres'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':woonplaats', $data['woonplaats'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':geboortedatum', $data['geboortedatum'] ?: null, PDO::PARAM_STR);
        $this->db->bind(':actief', $data['actief'], PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Vind een gebruiker op e-mail (enkel actieve gebruikers)
     */
    public function findByEmail(string $email)
    {
        $sql = 'SELECT * FROM gebruikers WHERE email = :email ';
        $this->db->query($sql);
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        return $this->db->single();
    }

    /**
     * Sla een HASHEDId op bij een gebruiker
     */
    public function saveHashedId(int $userId, string $hashedId): bool
    {
        $sql = 'UPDATE gebruikers SET HASHEDId = :hashedId WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':hashedId', $hashedId, PDO::PARAM_STR);
        $this->db->bind(':id', $userId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Wijzig het e-mailadres van een gebruiker
     */
    public function changeEmail(int $id, string $newEmail): bool
    {
        $sql = 'UPDATE gebruikers SET email = :email WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':email', $newEmail, PDO::PARAM_STR);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function InstructeurAccountoverzicht(int $id)
    {
        $sql = 'select klant.id
                       ,CONCAT(klant.voornaam, " ", IFNULL(klant.tussenvoegsel, ""), " ", klant.achternaam) as naam
                       ,klant.email
                       ,klant.telefoon
                       ,klant.rol
                       ,klant.actief
                       ,klant.adres
                       ,klant.woonplaats
                       ,klant.geboortedatum
                FROM   gebruikers klant
                JOIN reservations r   ON r.user_id = klant.id 
                join instructor_availability ia on ia.reservationid = r.id
                WHERE klant.rol = "klant" AND ia.instructor_id = :id and available_date >= CURDATE()
                ORDER BY available_date DESC';
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

}