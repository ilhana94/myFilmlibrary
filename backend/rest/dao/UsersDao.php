<?php
require_once __DIR__ . "/BaseDao.php";

class UsersDao extends BaseDao {
    public function __construct() {
        parent::__construct('users');
    }

    // CREATE
    public function createUser($username, $email, $password, $role) {
        return $this->insert([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role
        ]);
    }

    // READ - po emailu
    public function getByEmail($email) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    // READ - svi
    public function getAll() {
        $stmt = $this->connection->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // UPDATE
    public function updateUser($id, $data) {
        return $this->update($id, $data);
    }

    // DELETE
    public function deleteUser($id) {
        return $this->delete($id);
    }
}
?>
