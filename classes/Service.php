<?php
// classes/Service.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../classes/Database.php';

class Service
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function create($title, $description, $image = 'images/food-default.jpg')
    {
        $sql = "INSERT INTO services (title, description, image) VALUES (?, ?, ?)";
        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$title, $description, $image]);
    }

    public function readAll($search = '')
    {
        $sql = "SELECT * FROM services";
        if ($search) {
            $sql .= " WHERE title LIKE ? OR description LIKE ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute(["%$search%", "%$search%"]);
        } else {
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $title, $description, $image = null)
    {
        if ($image) {
            $sql = "UPDATE services SET title = ?, description = ?, image = ? WHERE id = ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            return $stmt->execute([$title, $description, $image, $id]);
        } else {
            $sql = "UPDATE services SET title = ?, description = ? WHERE id = ?";
            $stmt = $this->db->getConnection()->prepare($sql);
            return $stmt->execute([$title, $description, $id]);
        }
    }

    public function delete($id)
    {
        $stmt = $this->db->getConnection()->prepare("DELETE FROM services WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>