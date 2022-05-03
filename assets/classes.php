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
}


/**
 * Auth Class
 * $instance = create instance or get instance
 * $conn = get the PDO object from the instance
 */

class Auth
{
    /**
     * Login
     * $username = string
     * $password = string
     */
    public function login(String $username, String $password)
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

    public function loggedIn()
    {
        if (isset($_SESSION["logged_in"]) and $_SESSION["logged_in"] === true) {
            return true;
        } else {
            header("Location: /");
        }
    }

    private function setLoginAttemps(string $username, string $attemps)
    {
        $pdo = ConnectDb::getDbObject();
        $statement = $pdo->prepare("UPDATE Users SET login_attepms = $attemps WHERE username Like ?");
        $statement->execute(array($username));
        $pdo = null;
    }


    public function register($username, $password)
    {
        $nameAvailable = $this->getUser($username);

        // check if username already exists
        if (!empty($nameAvailable)) {
            $_SESSION["message"] =  "$username ist schon belegt! Wähle einen anderen Namen";
            return false;
        }

        // check if password has minlength
        if (strlen($password) < PWLENGTH) {
            $_SESSION["message"] = "Passwort ist zu kurz min. " . PWLENGTH . " Zeichen";
            return false;
        }

        // check if password has minlength
        if (strlen($username) < USERLENGTH) {
            $_SESSION["message"] = "Username ist zu kurz min. " . PWLENGTH . " Zeichen";
            return false;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->saveUser($username, $hash);
    }


    public function listUsers()
    {
        $pdo = ConnectDb::getDbObject();
        $statement = $pdo->prepare("SELECT * FROM Users");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $pdo = null;
        return $result;
    }

    private function getUser($username)
    {
        $pdo = ConnectDb::getDbObject();
        $statement = $pdo->prepare("SELECT * FROM Users WHERE username Like ?");
        $statement->execute(array($username));
        $result = $statement->fetch();
        $pdo = null;
        return $result;
    }

    public function deleteUser($username)
    {
        try {
            $pdo = ConnectDb::getDbObject();
            $statement = $pdo->prepare("DELETE FROM Users WHERE username LIKE ?");
            $statement->execute(array($username));
            $_SESSION["message"] = "Nutzer $username gelöscht";
        } catch (PDOException $e) {
            $_SESSION["message"] = $e->getMessage();
        }
    }

    private function saveUser($username, $hash)
    {
        $pdo = ConnectDb::getDbObject();
        $statement = $pdo->prepare("INSERT INTO Users (username, passwort, login_attepms) VALUES (?, ?, ?)");
        $statement->execute(array($username, $hash, 0));
        $_SESSION["message"] = "Benutzer $username angelegt!";
        $pdo = null;
    }
}
