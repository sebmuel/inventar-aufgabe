<?php
// load config
require_once 'config.php';

/**
 *  Singleton to connect db
 *  Using singelton here makes sure that whe only have 1 instance of the db object 
 *  no matter how many times we create one
 *  Having multiple db objects can cause performance issues 
 *  (probalby not on this small application) but i wanted to try this out
 *  Singelton Pattern is known for making unittest a nightmare but we dont have unittest so who cares
 */

class ConnectDb
{
    // Hold the class instance.
    private static $instance = null;
    private $conn;
    // db credentials
    private $host = HOSTNAME;
    private $user = DATABASEUSER;
    private $pass = DATABASEPASSWORD;
    private $name = DATABASENAME;

    // The db connection is established in the private constructor.
    private function __construct()
    {
        // test if an instance can be created
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->name}",
                $this->user,
                $this->pass
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $err) {
            $_SESSION["message"] = $err->getMessage();
        }
    }

    // if there is no instance -> create one and return it
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new ConnectDb();
        }

        return self::$instance;
    }

    // get the connection from the instance
    public function getConnection()
    {
        return $this->conn;
    }
}


/**
 * Auth Class
 * $instance = create instance or get instance
 * $conn = get the PDO object from the instance
 */

class Auth
{
    private $instance;
    private $conn;

    public function __construct()
    {
        $this->instance = ConnectDb::getInstance();
        $this->conn = $this->instance->getConnection();
    }

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
        $pdo = $this->conn;
        $statement = $pdo->prepare("UPDATE Users SET login_attepms = $attemps WHERE username Like ?");
        $statement->execute(array($username));
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

        $hash = $this->getHash($password);
        $this->saveUser($username, $hash);
    }

    private function getHash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function getUser($username)
    {
        $pdo = $this->conn;
        $statement = $pdo->prepare("SELECT * FROM Users WHERE username Like ?");
        $statement->execute(array($username));
        $result = $statement->fetch();
        return $result;
    }

    private function saveUser($username, $hash)
    {
        $pdo = $this->conn;
        $statement = $pdo->prepare("INSERT INTO Users (username, passwort, login_attepms) VALUES (?, ?, ?)");
        $statement->execute(array($username, $hash, 0));
        $_SESSION["message"] = "Benutzer $username angelegt!";
    }
}
