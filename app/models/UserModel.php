<?php
class UserModel
{
    private Database $db;

    public function __construct()
    {
        // $this->db = new Database();
        $this->db = new Database();
    }

    /**
     * Haal gebruiker op op basis van e-mailadres
     */
    public function findByEmail(string $email)
    {
        $this->db->query('SELECT * FROM gebruikers WHERE email = :email AND actief = 1');
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        return $this->db->single(); // object of false
    }

    /**
     * Verifieer login en return gebruiker of false
     */
    public function login(string $email, string $password)
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user->wachtwoord)) {
            // wachtwoord klopt, log login poging
            $this->logLogin($email , 'login');
            return $user;
        }
        return false;
    }

    /**
     * Haal gebruiker op op basis van ID
     */
    public function getuserid(int $id)
    {
        $this->db->query('SELECT * FROM gebruikers WHERE id = :id AND actief = 1');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single(); // object of false
    }

    /**
     * Update een gebruiker (alleen email en id verplicht, rest optioneel)
     * @param array $data
     * @return bool
     */
    public function updateUser(array $data): bool
    {
        if (empty($data['email']) || empty($data['id'])) {
            return false;
        }
        $fields = [];
        $params = [':email' => $data['email'], ':id' => $data['id']];
        $types = [':email' => PDO::PARAM_STR, ':id' => PDO::PARAM_INT];

        // Optionele velden
        $optFields = [
            'voornaam' => PDO::PARAM_STR,
            'tussenvoegsel' => PDO::PARAM_STR,
            'achternaam' => PDO::PARAM_STR,
            'telefoon' => PDO::PARAM_STR,
            'adres' => PDO::PARAM_STR,
            'woonplaats' => PDO::PARAM_STR,
            'geboortedatum' => PDO::PARAM_STR,
            'bsn' => PDO::PARAM_STR
        ];

        foreach ($optFields as $field => $type) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field] !== '' ? $data[$field] : null;
                $types[":$field"] = $type;
            }
        }

        // Email is altijd verplicht
        $fields[] = "email = :email";

        $sql = "UPDATE gebruikers SET " . implode(", ", $fields) . " WHERE id = :id";
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value, $types[$key]);
        }
        return $this->db->execute();
    }

    public function updatePasswordById($id, $hash)
    {
        $this->db->query('UPDATE gebruikers SET wachtwoord = :wachtwoord WHERE id = :id');
        $this->db->bind(':wachtwoord', $hash, PDO::PARAM_STR);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Log login attempt
     */
    public function logLogin($email , $actie )
    {
        $this->db->query('INSERT INTO login_logs (email, actie, logtijd) VALUES (:email, :actie, NOW(6))');
        $this->db->bind(':email', $email, PDO::PARAM_STR);
        $this->db->bind(':actie', $actie, PDO::PARAM_STR);
        $this->db->execute();
    }

}
