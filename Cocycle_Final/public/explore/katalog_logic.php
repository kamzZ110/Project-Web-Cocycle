<?php
session_set_cookie_params(0, '/');
session_start();
include '../backend/db_connect.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: ../backend/login.php");
    exit;
}

// Default Variables
$where_clause = "1=1";
$params = [];
$types = "";
$filter_title = "All Products";
$search_keyword = "";

// Variabel untuk menentukan mana Sidebar yang "Nyala"
// Jika tidak ada ?category=... di URL, maka defaultnya 'All'
$current_category = isset($_GET['category']) ? $_GET['category'] : 'All'; 

// 1. LOGIKA PENCARIAN
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_keyword = $_GET['search'];
    $search_query = "%" . $search_keyword . "%";
    $where_clause .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = $search_query;
    $params[] = $search_query;
    $types .= "ss";
    $filter_title = "Search: \"" . htmlspecialchars($search_keyword) . "\"";
    $current_category = ''; // Kalau lagi search, matikan highlight kategori
}

// 2. LOGIKA FILTER RESTO (SKIP - belum ada store_id di database)
if (isset($_GET['resto']) && !empty($_GET['resto'])) {
    $resto_name = $_GET['resto'];
    // Skip filter resto karena belum ada relasi ke stores
    $filter_title = "Resto: " . htmlspecialchars($resto_name);
    $current_category = ''; 
}

// 3. LOGIKA FILTER KATEGORI
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $cat_name = $_GET['category'];
    $where_clause .= " AND category = ?";
    $params[] = $cat_name;
    $types .= "s";
    $filter_title = "Category: " . htmlspecialchars($cat_name);
}

// TEMPORARY: Query tanpa JOIN karena store_id belum ada
// Nanti setelah migration baru bisa pake JOIN
$sql = "SELECT * FROM products WHERE $where_clause";
$stmt = $conn->prepare($sql);

if (!$stmt) die("Error DB: " . $conn->error);
if (!empty($params)) $stmt->bind_param($types, ...$params);
if (!$stmt->execute()) die("Error Execute: " . $stmt->error);

$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    // Add default store_name jika belum ada
    $row['store_name'] = 'CoCycle Store';
    $products[] = $row;
}
?>