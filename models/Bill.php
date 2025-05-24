<?php
require_once 'Database.php';

class Bill {
    private $conn;
    private $table = "bills";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getAll($search = "", $limit = 5, $offset = 0, $archived = 0) {
        $query = "SELECT * FROM {$this->table} WHERE is_archived = :archived";
        $params = [':archived' => $archived];

        if (!empty($search)) {
            $query .= " AND (
                bill_id LIKE :search OR
                account_number LIKE :search OR
                service LIKE :search OR
                payment_status LIKE :search OR
                category LIKE :search OR
                CAST(amount AS CHAR) LIKE :search
            )";
            $params[':search'] = '%' . $search . '%';
        }

        $query .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount($search = "", $archived = 0) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE is_archived = :archived";
        $params = [':archived' => $archived];

        if (!empty($search)) {
            $query .= " AND (
                bill_id LIKE :search OR
                account_number LIKE :search OR
                service LIKE :search OR
                payment_status LIKE :search OR
                category LIKE :search OR
                CAST(amount AS CHAR) LIKE :search
            )";
            $params[':search'] = '%' . $search . '%';
        }

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
            (account_number, bill_id, service, amount, payment_status, category, created_at, is_archived)
            VALUES (:account_number, :bill_id, :service, :amount, :payment_status, :category, NOW(), 0)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = :$key, ";
        }
        $fields = rtrim($fields, ", ");
        $data['id'] = $id;

        $sql = "UPDATE {$this->table} SET $fields WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        foreach ($data as $key => $val) {
            $stmt->bindValue(":$key", $val);
        }
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function archive($ids) {
        // Kiểm tra rỗng hoặc không phải mảng
        if (empty($ids) || !is_array($ids)) return false;

        // Ép kiểu từng phần tử thành số nguyên
        $ids = array_map('intval', $ids);

        // Nếu sau khi ép kiểu mà vẫn rỗng thì dừng
        if (empty($ids)) return false;

        $in = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET is_archived = 1 WHERE id IN ($in)");
        return $stmt->execute($ids);
    }


    public function unarchive(array $ids): bool {
        if (empty($ids)) return false;

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE bills SET is_archived = 0 WHERE id IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);  // ✅ Dùng đúng biến $this->conn
        return $stmt->execute($ids);
    }



    public function getAllIds() {
        $stmt = $this->conn->query("SELECT id FROM {$this->table} WHERE is_archived = 0");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function massUpdate($ids, $data) {
        $fields = [];
        foreach ($data as $key => $val) {
            $fields[] = "$key = :$key";
        }
        $setClause = implode(', ', $fields);

        $placeholders = [];
        foreach ($ids as $i => $id) {
            $placeholders[] = ":id$i";
        }
        $whereClause = implode(',', $placeholders);

        $sql = "UPDATE {$this->table} SET $setClause WHERE id IN ($whereClause)";
        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $val) {
            $stmt->bindValue(":$key", $val);
        }

        foreach ($ids as $i => $id) {
            $stmt->bindValue(":id$i", $id, PDO::PARAM_INT);
        }
        return $stmt->execute();
    }
}
