<?php
require_once __DIR__ . '/../models/Bill.php';

class BillController {
    private $billModel;

    public function __construct() {
        $this->billModel = new Bill();
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        $page = $_GET['page'] ?? 1;
        $itemsPerPage = $_GET['itemsPerPage'] ?? 5;
        $archived = isset($_GET['archived']) ? 1 : 0;

        $offset = ($page - 1) * $itemsPerPage;
        $total = $this->billModel->getTotalCount($search, $archived);
        $totalPages = $itemsPerPage > 0 ? ceil($total / $itemsPerPage) : 1;

        $data = $this->billModel->getAll($search, $itemsPerPage, $offset, $archived);

        echo json_encode([
            'data' => $data,
            'totalPages' => $totalPages,
            'currentPage' => (int)$page
        ]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'account_number' => $_POST['account_number'],
                    'bill_id' => $_POST['bill_id'],
                    'service' => $_POST['service'],
                    'amount' => $_POST['amount'],
                    'payment_status' => $_POST['payment_status'],
                    'category' => $_POST['category']
                ];
                $this->billModel->create($data);
                header('Location: /index.php');
                exit;
            } catch (PDOException $e) {
                $error = $e->getCode() === "23000"
                    ? "Duplicate entry: Bill ID already exists."
                    : "Add failed: " . $e->getMessage();
                require_once __DIR__ . '/../views/bills/add.php';
            }
        } else {
            require_once __DIR__ . '/../views/bills/add.php';
        }
    }

    public function edit($id) {
        $bill = $this->billModel->getById($id);
        require_once __DIR__ . '/../views/bills/edit.php';
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'account_number' => $_POST['account_number'],
                    'bill_id' => $_POST['bill_id'],
                    'service' => $_POST['service'],
                    'amount' => $_POST['amount'],
                    'payment_status' => $_POST['payment_status'],
                    'category' => $_POST['category']
                ];
                $this->billModel->update($id, $data);
                header('Location: /index.php');
            } catch (PDOException $e) {
                echo "Update failed: " . $e->getMessage();
            }
        }
    }

    public function delete($id) {
        $this->billModel->delete($id);
        header('Location: /index.php');
    }

    public function archive() {
        $ids = $_POST['ids'] ?? [];

        if ($ids === 'ALL') {
            $ids = $this->billModel->getAllIds();
        } elseif (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            echo "fail";
            return;
        }

        $result = $this->billModel->archive($ids);
        echo $result ? "success" : "fail";
    }

    public function unarchive() {
        $ids = $_POST['ids'] ?? [];

        if ($ids === 'ALL') {
            $ids = $this->billModel->getAllIds(archived: true); // chỉ lấy ID của các bill archived
        } elseif (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            echo "fail";
            return;
        }

        echo $this->billModel->unarchive($ids) ? "success" : "fail";
    }



    public function getAllIds() {
        echo json_encode($this->billModel->getAllIds());
    }

    public function getTotalCount() {
        echo $this->billModel->getTotalCount();
    }

    public function massUpdate() {
        $ids = $_POST['ids'] ?? [];
        if ($ids === 'ALL') {
            $ids = $this->billModel->getAllIds();
        } elseif (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $data = array_filter([
            'payment_status' => $_POST['payment_status'] ?? null,
            'service' => $_POST['service'] ?? null,
            'category' => $_POST['category'] ?? null,
            'bill_id' => $_POST['bill_id'] ?? null,
            'amount' => $_POST['amount'] ?? null,
            'account_number' => $_POST['account_number'] ?? null
        ], fn($v) => $v !== null && $v !== '');

        if (empty($data) || empty($ids)) {
            echo "Missing data";
            return;
        }

        echo $this->billModel->massUpdate($ids, $data) ? "success" : "fail";
    }

    public function inlineUpdate() {
        $id = $_POST['id'] ?? null;
        $field = $_POST['field'] ?? null;
        $value = $_POST['value'] ?? null;

        if ($id && $field && $value !== null) {
            $result = $this->billModel->update($id, [$field => $value]);
            echo $result ? "success" : "fail";
        } else {


            echo "invalid";
        }
    }
}
