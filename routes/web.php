<?php
require_once __DIR__ . '/../controllers/BillController.php';

$route = $_GET['route'] ?? 'home';
$controller = new BillController();

switch ($route) {
    case 'home':
        require_once __DIR__ . '/../views/bills/index.php';
        break;
    case 'fetch':
        $controller->index();
        break;
    case 'create':
        $controller->create();
        break;
    case 'edit':
        if (isset($_GET['id'])) {
            $controller->edit($_GET['id']);
        } else {
            require_once __DIR__ . '/../views/bills/add.php';
        }
        break;
    case 'update':
        if (isset($_GET['id'])) {
            $controller->update($_GET['id']);
        }
        break;
    case 'delete':
        if (isset($_GET['id'])) {
            $controller->delete($_GET['id']);
        }
        break;
    case 'archive':
        $controller->archive(); // ✅ đảm bảo BillController có hàm archive()
        break;
    case 'archived':
        require_once __DIR__ . '/../views/bills/archived.php';
        break;
    case 'unarchive':
        $controller->unarchive();
        break;
    case 'get_all_ids':
        $controller->getAllIds();
        break;
    case 'get_total_count':
        $controller->getTotalCount();
        break;
    case 'mass_update':
        $controller->massUpdate();
        break;
    case 'inlineUpdate':
        $controller->inlineUpdate();
        break;
    default:
        echo "404 - Route Not Found";
        break;
}
