<?php
require_once 'dbCredentials.php';

/**
 * Class AuthorisationModel class for managing the authorization for the web app.
 */
class AuthorisationModel 
{
    protected $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
            DB_USER, DB_PWD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }


    /**
     * Returns if a token is valid
     * @param string $token refers to the token in the database.
     * @return bool true if the database token is verified.
     */
    public function isValid(string $token): bool {

        $query = 'SELECT COUNT(*) FROM auth_token WHERE token = :token';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':token', $token);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_NUM);
        if ($row[0] == 0) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * Verifies whether the user attempting to use the endpoint is authorized to access it.
     * @param string $token represents the cookie sent through http header.
     * @param string $permission relates to the type of permission being checked for.
     * @return bool true if the user is authorized.
     */
    public function checkTokenAndPermission(string $token, string $permission): bool {

        $query = 'SELECT COUNT(*) FROM auth_token WHERE token = :token AND permission = :permission';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':permission', $permission);
        $stmt->execute();


        $row = $stmt->fetch(PDO::FETCH_NUM);
        if ($row[0] == 0) {
            return false;
        } else {
            return true;
        }
    }

}
