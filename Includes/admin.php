<?php 
require_once("config.php");

function retrieve_bills_generated($id, $offset, $rowsperpage) {
    global $con;
    $query = "SELECT user.name AS user, bill.bdate AS bdate, bill.units AS units, bill.amount AS amount, bill.id as bid, bill.ddate AS ddate, bill.status AS status
              FROM user JOIN bill ON user.id = bill.uid
              WHERE bill.aid = ?
              ORDER BY bill.id DESC
              LIMIT ?, ?";

    $stmt = $con->prepare($query);
    $stmt->bind_param("iii", $id, $offset, $rowsperpage);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === FALSE) {
        die("Error: " . $con->error);
    }

    return $result; 
}

function retrieve_bill_data($offset, $rowsperpage) {
    global $con;
    $query = "SELECT CURDATE() AS bdate, ADDDATE(CURDATE(), INTERVAL 30 DAY) AS ddate, user.id AS uid, user.name AS uname
              FROM user
              LIMIT ?, ?";

    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $offset, $rowsperpage);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === FALSE) {
        die("Error: " . $con->error);
    }

    return $result;
}

function retrieve_users_detail($id, $offset, $rowsperpage) {
    global $con;
    $query = "SELECT * FROM user LIMIT ?, ?";

    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $offset, $rowsperpage);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === FALSE) {
        die("Error: " . $con->error);
    }

    return $result;
}

function retrieve_admin_stats($id) {
    global $con;
    $queries = [
        "SELECT COUNT(id) AS unprocessed_bills FROM bill WHERE status = 'PENDING' AND aid = ?",
        "SELECT COUNT(id) AS generated_bills FROM bill WHERE aid = ?"
    ];

    $results = [];
    foreach ($queries as $query) {
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result === FALSE) {
            die("Error: " . $con->error);
        }
        $results[] = $result;
    }

    return $results;
}

function retrieve_users_defaulting($id) {
    global $con;

    $queries = [
        "SELECT COUNT(*) FROM bill JOIN transaction ON bill.id = transaction.bid
         WHERE CURDATE() > bill.ddate AND CURDATE() < ADDDATE(bill.ddate, INTERVAL 25 DAY)
         AND bill.aid = ? AND bill.status = 'PENDING' AND bill.amount = transaction.payable",
        "SELECT COUNT(*) FROM bill
         WHERE CURDATE() > ADDDATE(bill.ddate, INTERVAL 25 DAY)
         AND bill.aid = ? AND bill.status = 'PENDING'"
    ];

    $results = [];
    foreach ($queries as $query) {
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result === FALSE) {
            die("Error: " . $con->error);
        }
        $results[] = $result;
    }

    return $results;
}

function insert_into_transaction($id, $amount) {
    global $con;
    $query = "INSERT INTO transaction (bid, payable, pdate, status) VALUES (?, ?, NULL, 'PENDING')";

    $stmt = $con->prepare($query);
    $stmt->bind_param("id", $id, $amount);
    if (!$stmt->execute()) {
        die("Error: " . $con->error);
    }
}
?>
