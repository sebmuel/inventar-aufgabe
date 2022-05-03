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


class InventarTypen
{
    public function saveInventarTyp($name)
    {
        $matches = count($this->getInventarTyp($name));
        echo $matches;
        if ($matches > 0) {
            return;
        }
        $pdo = ConnectDb::getDbObject();
        $statement = $pdo->prepare("INSERT INTO Inventartypen (typ_name) VALUES ?");
        $statement->execute(array($name));
        echo $name;
        $pdo = null;
    }

    private function getInventarTyp($name)
    {
        $pdo = ConnectDb::getDbObject();
        $statement = $pdo->prepare("SELECT * FROM Inventartypen WHERE typ_name LIKE ?");
        $statement->execute(array($name));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $pdo = null;
        return $result;
    }
}
