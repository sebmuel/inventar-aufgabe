<?php
// load config
require_once 'config.php';

/**
 *  Singleton to connect db
 *  Using singelton here makes sure that whe only have 1 instance of the db object no matter how many times we create one
 *  Having multiple db objects can cause performance issues (probalby not on this small application) but i wanted to try this out
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
                "mysql:host={$this->host};
                dbname={$this->name}",
                $this->user,
                $this->pass
            );
        } catch (PDOException $Exception) {
            return $Exception;
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
 */

class Auth
{
    private $instance;
    private $conn;
    private $accountStatus;


    public function __construct()
    {
        $this->instance = ConnectDb::getInstance();
        $this->conn = $this->instance->getConnection();
    }

    public function login(String $username, String $password)
    {
        $pdo = $this->conn;
        $statement = $pdo->prepare("SELECT * FROM Users WHERE username Like ?");
        $statement->execute(array($username));
        $result = $statement->fetch();

        if (!empty($result)) {
            if (password_verify($password, $result["passwort"])) {
                if ($result["login_attepms"] >= 3) {
                    return false;
                }
                $_SESSION["username"] = $username;
                $_SESSION["logged_in"] = true;
                $this->setLoginAttemps($username, "0");
                return true;
            }

            $this->setLoginAttemps($username, $result["login_attepms"] + 1);
            return false;
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
}
