<?php
// load config
require_once 'config.php';

/**
 *  Conncetion Class
 */

class ConnectDb
{
    // db credentials
    private static $host = HOSTNAME;
    private static $user = DATABASEUSER;
    private static $pass = DATABASEPASSWORD;
    private static $name = DATABASENAME;

    /**
     * getDbObject
     * create PDO Object and hand it to the load and save methods
     *
     * @return PDO
     */
    public static function getDbObject()
    {
        return new PDO(
            "mysql:dbname=" . ConnectDb::$name . ";host=" . ConnectDb::$host,
            ConnectDb::$user,
            ConnectDb::$pass
        );
    }

    /**
     * store
     * handle the update requests to he database
     *
     * @param  string $statement A valid SQL STATEMENT
     * @param  array $prepare  An array with parameters for the PDO->prepare method
     * @return void
     */
    public static function store($statement, $prepare = array())
    {

        try {
            $pdo = self::getDbObject();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = $statement;
            $query = $pdo->prepare($sql);
            $query->execute($prepare);
        } catch (PDOException $e) {
            $_SESSION["message"] = $e->getMessage();
        } finally {
            $pdo = null;
        }
    }

    /**
     * load
     *
     * @param  string $statement A valid SQL STATEMENT
     * @param  array $prepare  An array with parameters for the PDO->prepare method
     * @param  boolean $singleRecord True will use fetch() instead of fetchAll() default= false
     * @return void
     */
    public static function load($statement, $prepare = array(), $singleRecord = false)
    {
        try {
            $pdo = self::getDbObject();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = $statement;
            $query = $pdo->prepare($sql);
            $query->execute($prepare);
            if ($singleRecord) {
                $result = $query->fetch();
            } else {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
            }
            return $result;
        } catch (PDOException $e) {
            $_SESSION["message"] = $e->getMessage();
        } finally {
            $pdo = null;
        }
    }
}

/**
 * Auth
 */
class Auth
{

    /**
     * login
     * Method to log the user in
     *
     * @param  string $username
     * @param  string $password
     * @return bool
     */
    public function login(string $username, string $password)
    {
        // get user data from db
        $result = $this->getUser($username);
        // check if user exists
        if (!empty($result)) {
            if ($result["login_attepms"] >= 3) {
                $_SESSION['message'] = "Account ist gesperrt Bitte kontaktieren sie den Page Admin";
                return false;
            }
            // user exists -> validate password
            if (password_verify($password, $result["passwort"])) {
                // setLoginAttemps to 0
                $this->setLoginAttemps($username, 0);
                // set session Variables
                $_SESSION["username"] = $username;
                $_SESSION["logged_in"] = true;

                return true;
            }
            // name was right but password was wrong so -> increase login attemps
            $this->setLoginAttemps($username, $result["login_attepms"] + 1);
            $_SESSION["message"] = "Kombination aus Passwort und Username ist falsch!";
            return false;
        } else {
            $_SESSION["message"] = "Unter dem Namen $username existiert kein Account";
        }
    }

    /**
     * loggedIn
     * Checks if the user is logged and taking action if not
     *
     */
    public function loggedIn()
    {
        if (isset($_SESSION["logged_in"]) and $_SESSION["logged_in"] === true) {
            return true;
        } else {
            header("Location: /");
        }
    }

    /**
     * setLoginAttemps
     *
     * @param  string $username
     * @param  string $attemps
     * @return void
     */
    public function setLoginAttemps(string $username, string $attemps)
    {
        if ($username === $_SESSION["username"]) {
            $_SESSION["message"] = "Sie können den Account nicht 
            Sperren/Entsperren wenn sie eingeloggt sind";
            return;
        }
        $statement = "UPDATE Users SET login_attepms = $attemps WHERE username Like ?";
        $prepare = array($username);
        ConnectDb::store($statement, $prepare);
    }

    /**
     * register
     *
     * @param  string $username
     * @param  string $password
     * @return void
     */
    public function register($username, $password)
    {
        $nameAvailable = $this->getUser($username);

        // check if username already exists
        if (!empty($nameAvailable)) {
            $_SESSION["message"] = "$username ist schon belegt! Wähle einen anderen Namen";
            return;
        }

        // check if password has minlength
        if (strlen($password) < PWLENGTH) {
            $_SESSION["message"] = "Passwort ist zu kurz min. " . PWLENGTH . " Zeichen";
            return;
        }

        // check if password has minlength
        if (strlen($username) < USERLENGTH) {
            $_SESSION["message"] = "Username ist zu kurz min. " . USERLENGTH . " Zeichen";
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->saveUser($username, $hash);
    }

    /**
     * listUsers
     * retrive all users
     *
     * @return array
     */
    public function listUsers()
    {
        $statement = "SELECT * FROM Users";
        $result = ConnectDb::load($statement);
        return $result;
    }

    /**
     * getUser
     * get one specific user
     *
     * @param  string $username
     * @return array
     */
    private function getUser($username)
    {

        $statement = "SELECT * FROM Users WHERE username Like ?";
        $prepare = array($username);
        $result = ConnectDb::load($statement, $prepare, true);
        return $result;
    }

    /**
     * deleteUser
     * delete selected user
     *
     * @param  string $username
     * @return void
     */
    public function deleteUser($username)
    {
        // check if the user is trying to delete the account he is logged in with
        if ($username === $_SESSION["username"]) {
            $_SESSION["message"] = "Sie können den Account nicht 
            löschen wenn sie eingeloggt sind";
            return;
        }
        $statement = "DELETE FROM Users WHERE username LIKE ?";
        $prepare = array($username);
        ConnectDb::store($statement, $prepare);
        $_SESSION["message"] = "Nutzer $username gelöscht";
    }

    /**
     * saveUser
     * save a single user
     *
     * @param  string $username
     * @param  string $hash
     * @return void
     */
    private function saveUser($username, $hash)
    {
        $statement = "INSERT INTO Users (username, passwort, login_attepms) VALUES (?, ?, ?)";
        $prepare = array($username, $hash, 0);
        ConnectDb::store($statement, $prepare);
        $_SESSION["message"] = "Benutzer $username angelegt!";
    }
}

/**
 * InventarTypen
 */
class InventarTypen
{
    public function saveInventarTyp($name)
    {
        if (strlen($name) <= MINLENGTH) {
            $_SESSION["message"] = "Eingabe zu Kurz mind. " . MINLENGTH . " Zeichen";
            return;
        }
        $matches = $this->getInventarTyp($name);
        if ($matches) {
            $_SESSION["message"] = "Typ $name existiert bereits";
            return;
        }
        $statement = "INSERT INTO Inventartypen (typ_name) VALUES (?)";
        $prepare = array($name);
        ConnectDb::store($statement, $prepare);
        $_SESSION["message"] = "Typ: $name angelegt !";
    }

    private function getInventarTyp($name)
    {

        $statement = "SELECT * FROM Inventartypen WHERE typ_name LIKE ?";
        $prepare = array($name);
        $result = ConnectDb::load($statement, $prepare, true);
        return $result;
    }

    /**
     * getAll
     *
     * @return array
     */
    public function getAll()
    {
        $statement = "SELECT * FROM Inventartypen";
        $result = ConnectDb::load($statement);
        return $result;
    }

    public function deleteType($name)
    {
        $statement = "DELETE FROM Inventartypen WHERE typ_name LIKE ?";
        $prepare = array($name);
        ConnectDb::store($statement, $prepare);
        $_SESSION["message"] = "Typ $name gelöscht!";
    }
}

/**
 * Filiale
 */
class Filiale
{
    public function saveFiliale($filiale)
    {
        if (strlen($filiale) <= MINLENGTH) {
            $_SESSION["message"] = "Eingabe zu Kurz mind. " . MINLENGTH . " Zeichen";
            return;
        }
        $matches = $this->getFiliale($filiale);

        if ($matches) {
            $_SESSION["message"] = "Typ $filiale existiert bereits";
            return;
        }
        $statement = "INSERT INTO Filialen (filiale) VALUES (?)";
        $prepare = array($filiale);
        ConnectDb::store($statement, $prepare);
        $_SESSION["message"] = "Typ: $filiale angelegt !";
    }

    private function getFiliale($filiale)
    {

        $statement = "SELECT * FROM Filialen WHERE filiale LIKE ?";
        $prepare = array($filiale);
        $result = ConnectDb::load($statement, $prepare, true);
        return $result;
    }

    public function getAll()
    {
        $statement = "SELECT * FROM Filialen";
        $result = ConnectDb::load($statement);
        return $result;
    }

    public function deleteFiliale($filiale)
    {
        $statement = "DELETE FROM Filialen WHERE filiale LIKE ?";
        $prepare = array($filiale);
        ConnectDb::store($statement, $prepare);
        $_SESSION["message"] = "Filiale $filiale gelöscht!";
    }
}

/**
 * Abteilung
 */
class Abteilung
{
    public function saveAbteilung($abteilung)
    {
        if (strlen($abteilung) <= MINLENGTH) {
            $_SESSION["message"] = "Eingabe zu Kurz mind. " . MINLENGTH . " Zeichen";
            return;
        }
        $matches = $this->getAbteilung($abteilung);

        if ($matches) {
            $_SESSION["message"] = "Abteilung $abteilung existiert bereits";
            return;
        }
        $statement = "INSERT INTO Abteilungen (abteilung) VALUES (?)";
        $prepare = array($abteilung);
        ConnectDb::store($statement, $prepare);
        $_SESSION["message"] = "Abteilung: $abteilung angelegt !";
    }

    /**
     * getAbteilung
     *
     * @param  mixed $abteilung
     * @return mixed
     */
    private function getAbteilung($abteilung)
    {

        $statement = "SELECT * FROM Abteilungen WHERE abteilung LIKE ?";
        $prepare = array($abteilung);
        $result = ConnectDb::load($statement, $prepare, true);
        return $result;
    }

    /**
     * getAll
     *
     * @return array
     */
    public function getAll()
    {
        $statement = "SELECT * FROM Abteilungen";
        $result = ConnectDb::load($statement);
        return $result;
    }

    public function deleteAbteilung($abteilung)
    {
        $statement = "DELETE FROM Abteilungen WHERE abteilung LIKE ?";
        $prepare = array($abteilung);
        ConnectDb::store($statement, $prepare);
        $_SESSION["message"] = "Abteilung $abteilung gelöscht!";
    }
}

/**
 * Inventar
 */
class Inventar
{
    private InventarTypen $typen;
    private Abteilung $abteilungen;
    private Filiale $filialen;

    public array $entries = [];
    public float $summeRestwerte = 0;

    public function __construct()
    {
        $this->typen = new InventarTypen();
        $this->abteilungen = new Abteilung();
        $this->filialen = new Filiale();
    }

    /**
     * getInventarTyp
     *
     * @return array
     */
    public function getInventarTyp()
    {
        return $this->typen->getAll();
    }

    /**
     * getAbteilung
     *
     * @return array
     */
    public function getAbteilung()
    {
        return $this->abteilungen->getAll();
    }

    /**
     * getFiliale
     *
     * @return array
     */
    public function getFiliale()
    {
        return $this->filialen->getAll();
    }

    public function saveInventar(array $values)
    {
        $values["preis"] = number_format((float) $values["preis"], 2);
        $statement = "INSERT INTO Inventar (name, typ, buy_date, buy_price, dauer, abteilung, filiale) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
        // transform formated nubmer to float
        $values["preis"] = (float) filter_var(
            $values["preis"],
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        );
        $prepare = array(
            $values["name"], $values["typ"], $values["datum"],
            $values["preis"], $values["dauer"], $values["abteilung"], $values["filiale"]
        );
        ConnectDb::store($statement, $prepare);
        $_SESSION["message"] = "Inventar " . $values['name'] . " angelegt!";
    }

    /**
     * getRecords
     * Returns a list of Records based on the Selected Options
     *
     * @param  string $typ
     * @param  string $abteilung
     * @param  string $filiale
     * @return array
     */
    public function getRecords($typ = "%%", $abteilung = "%%", $filiale = "%%")
    {
        $statement = "SELECT * FROM Inventar WHERE typ LIKE ? AND abteilung LIKE ? AND filiale LIKE ?";
        $prepare = array($typ, $abteilung, $filiale);
        $result = ConnectDb::load($statement, $prepare);
        return $result;
    }

    public function deleteRecord($id)
    {
        $statement = "DELETE FROM Inventar WHERE i_id = ?";
        $prepare = array($id);
        ConnectDb::store($statement, $prepare);
        $_SESSION["message"] = "Inventar mit der Id: $id gelöscht!";
    }

    /**
     * entries
     * builds InventarEntry objects
     * @return array
     */
    public function entries()
    {
        $entries = $this->getRecords();
        foreach ($entries as $entry) {
            $id = $entry["i_id"];
            $name = $entry["name"];
            $typ = $entry["typ"];
            $datum = $entry["buy_date"];
            $preis = $entry["buy_price"];
            $dauer = $entry["dauer"];
            $filiale = $entry["filiale"];
            $abteilung = $entry["abteilung"];
            $entry = new InventarEntry($id, $name, $preis, $datum, $dauer, $typ, $filiale, $abteilung);
            array_push($this->entries, $entry);
        }
        return $entries;
    }

    /**
     * calcTotal
     * calculate the total "Restwert"
     * @return float
     */
    public function calcTotal()
    {
        if (!empty($this->entries)) {
            foreach ($this->entries as $entry) {
                $this->summeRestwerte += $entry->restwert;
            }
            return $this->summeRestwerte;
        }
    }

    /**
     * filter
     * filters the array with records based on the input from user 
     * 
     * @param  array $filters
     * @param  array $records
     * @return array
     */
    public function filter(array $filters, array $records)
    {
        $min = floatval(htmlspecialchars($filters["min"]));
        $max = floatval(htmlspecialchars($filters["max"]));
        $abteilung = htmlspecialchars($filters["abteilung"]);
        $filiale = htmlspecialchars($filters["filiale"]);
        $typ = htmlspecialchars($filters["typ"]);

        $filtered = array();

        foreach ($records as $record) {
            $restwert = $record->restwert;
            $valid = true;

            if ($restwert < $min or $restwert > $max) {
                $valid = false;
            }
            if ($record->abteilung !== $abteilung and $abteilung !== "%%") {
                $valid = false;
            }
            if ($record->filiale !== $filiale and $filiale !== "%%") {
                $valid = false;
            }
            if ($record->typ !== $typ and $typ !== "%%") {
                $valid = false;
            }
            if ($valid === true) {
                array_push($filtered, $record);
            } else {
                $this->summeRestwerte -= $restwert;
            }
        }
        return $filtered;
    }
}

class InventarEntry
{
    public $id;
    public $name;
    public $anschaffungsPreis;
    public float $restwert;
    public $anschaffungsDatum;
    public $dauer;
    public $typ;
    public $filiale;
    public $abteilung;

    public function __construct($id, $name, $anschaffungsPreis, $anschaffungsDatum, $dauer, $typ, $filiale, $abteilung)
    {
        $this->id = $id;
        $this->name = $name;
        $this->anschaffungsPreis = $anschaffungsPreis;
        $this->anschaffungsDatum = $anschaffungsDatum;
        $this->dauer = $dauer;
        $this->typ = $typ;
        $this->filiale = $filiale;
        $this->abteilung = $abteilung;
        $this->restwert = $this->residualValue($anschaffungsDatum, $anschaffungsPreis, $dauer);
    }

    /**
     * residualValue
     * method to calculate the "Aktuelle Restwert"
     *
     * @param  mixed $anschaffungsDatum
     * @param  mixed $preis
     * @param  mixed $dauer
     * @return float
     */
    public function residualValue($anschaffungsDatum, $preis, $dauer)
    {
        $heute = new DateTime();
        $preis = floatval($preis);
        $anschaffungsDatum = new DateTime($anschaffungsDatum);
        $jahreVergangen = $heute->diff($anschaffungsDatum);
        $jahreVergangen = $jahreVergangen->y;
        $abschreibungsBetrag = $preis / $dauer;
        $rest = $preis;
        for ($i = 0; $i < $jahreVergangen; ++$i) {
            $rest = $rest - $abschreibungsBetrag;
        }

        $rest = ($rest < 1) ? 1 : doubleval($rest);
        $rest = floatval($rest);
        return $rest;
    }
}
