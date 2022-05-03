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

    public static function getDbObject()
    {
        try {
            return ConnectDb::createDataBaseInstance();
        } catch (PDOException $e) {
            $_SESSION["message"] = $e->getMessage();
        }
    }

    private static function createDataBaseInstance()
    {
        return new PDO("mysql:dbname=" . ConnectDb::$name . ";host=" . ConnectDb::$host, ConnectDb::$user, ConnectDb::$pass);
    }

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
     * @return void
     */
    public function login(string $username, string $password)
    {
        // get user data from db
        $result = $this->getUser($username);
        // check if user exists
        if (!empty($result)) {
            // user exists -> validate password
            if (password_verify($password, $result["passwort"])) {
                // check if account is locked
                if ($result["login_attepms"] >= 3) {
                    $_SESSION['message'] = "Account ist gesperrt Bitte kontaktieren sie den Page Admin";
                    return false;
                }
                // set session Variables
                $_SESSION["username"] = $username;
                $_SESSION["logged_in"] = true;
                // setLoginAttemps to 0
                $this->setLoginAttemps($username, "0");
                return true;
            }
            // user does not exist -> increase login attemps +1 
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
     * @return void
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
            $_SESSION["message"] = "Sie können den Account nicht Sperren wenn sie eingeloggt sind";
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
            $_SESSION["message"] =  "$username ist schon belegt! Wähle einen anderen Namen";
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
            $_SESSION["message"] = "Sie können den Account nicht löschen wenn sie eingeloggt sind";
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


class Filiale
{
    public function save($filiale)
    {
        if (strlen($filiale) <= MINLENGTH) {
            $_SESSION["message"] = "Eingabe zu Kurz mind. " . MINLENGTH . " Zeichen";
            return;
        }
        $matches = $this->get($filiale);
        if ($matches) {
            $_SESSION["message"] = "Typ $filiale existiert bereits";
            return;
        }
        $statement = "INSERT INTO Filialen (filiale) VALUES (?)";
        $prepare = array($filiale);
        ConnectDb::store($statement, $prepare);
        $_SESSION["message"] = "Typ: $filiale angelegt !";
    }

    private function get($filiale)
    {

        $statement = "SELECT * FROM Inventartypen WHERE typ_name LIKE ?";
        $prepare = array($filiale);
        $result = ConnectDb::load($statement, $prepare, true);
        return $result;
    }

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
