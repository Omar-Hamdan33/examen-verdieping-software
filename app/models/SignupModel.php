<?php
// app/models/SignupModel.php

class SignupModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

     public function findByEmail(string $email)
    {
        $this->db->query('SELECT * FROM gebruikers WHERE email = :email AND actief = 1');
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        return $this->db->single(); // object of false
    }

    /**
     * Creates a new user without password and returns the new user ID.
     *
     * @param array $data Associative array with keys: voornaam, tussenvoegsel, achternaam, email, telefoon
     * @return int|false New user ID or false on failure
     */
    public function Createaccount(array $data)
    {
        // Insert new user record
        $this->db->query(
            "INSERT INTO gebruikers
                (email )
             VALUES
                (:email)"
        );

        $this->db->bind(':email',        $data['email'], PDO::PARAM_STR);
        $this->db->execute();

        // Retrieve the newly generated ID
        $this->db->query("SELECT LAST_INSERT_ID() AS id");
        $row = $this->db->single();
        return $row ? (int)$row->id : false;
    }

    /**
     * Updates the user's details (adres, woonplaats, geboortedatum, bsn) by HASHEDId.
     *
     * @param array $data
     * @return bool True on success, false on failure
     */
    public function updatePassword(array $data): bool
    {  
        $this->db->query(
            "UPDATE gebruikers
             SET wachtwoord = :pwd
             WHERE HASHEDId = :id"
        );
     $this->db->bind(':id', $data['HASHEDId'], PDO::PARAM_STR);
        $this->db->bind(':pwd', $data['wachtwoord'], PDO::PARAM_STR);
        
        return $this->db->execute();
    }
public function updateUserDetails(array $data): bool
    {
        $this->db->query(
            "UPDATE gebruikers
             SET 
                    telefoon = :telefoon,
                    voornaam = :voornaam,
                    tussenvoegsel = :tussenvoegsel,
                    achternaam = :achternaam,
             adres = :adres,
                 woonplaats = :woonplaats,
                 geboortedatum = :geboortedatum,
                 bsn = :bsn
             WHERE id = :id"
        );
        $this->db->bind(':telefoon', $data['telefoon'], PDO::PARAM_STR);
        $this->db->bind(':voornaam', $data['voornaam'], PDO::PARAM_STR);
        $this->db->bind(':tussenvoegsel', $data['tussenvoegsel'], PDO::PARAM_STR);
        $this->db->bind(':achternaam', $data['achternaam'], PDO::PARAM_STR);
        $this->db->bind(':id', $data['id'], PDO::PARAM_STR);
        $this->db->bind(':adres', $data['adres'], PDO::PARAM_STR);
        $this->db->bind(':woonplaats', $data['woonplaats'], PDO::PARAM_STR);
        $this->db->bind(':geboortedatum', $data['geboortedatum'], PDO::PARAM_STR);
        $this->db->bind(':bsn', $data['bsn'], PDO::PARAM_STR);
        return $this->db->execute();
    }

    public function storeHashedId(int $userId, string $hashedId): bool
    {
        $this->db->query(
            "UPDATE gebruikers
             SET HASHEDId = :hashed_id
             WHERE id = :id"
        );
        $this->db->bind(':hashed_id', $hashedId, PDO::PARAM_STR);
        $this->db->bind(':id', $userId, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Vind gebruiker op basis van HASHEDId
     */
    public function findByHashedId(string $hashedId)
    {
        $this->db->query('SELECT * FROM gebruikers WHERE HASHEDId = :hashedId AND actief = 1');
        $this->db->bind(':hashedId', $hashedId, PDO::PARAM_STR);
        return $this->db->single(); // object of false
    }

    /**
     * Verwijder HASHEDId na gebruik
     */
    public function removeHashedId(int $userId): bool
    {
        $this->db->query(
            "UPDATE gebruikers SET HASHEDId = NULL WHERE id = :id"
        );
        $this->db->bind(':id', $userId, PDO::PARAM_INT);
        return $this->db->execute();
    }

     public function logLogin($email , $actie )
    {
        $this->db->query('INSERT INTO login_logs (email, actie, logtijd) VALUES (:email, :actie, NOW(6))');
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        $this->db->bind(':actie', $actie, PDO::PARAM_STR);
        $this->db->execute();
    }
}
