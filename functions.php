<?php
// Include the database connection
include('db_connect.php');

/**
 * Fetch the nearest upcoming tour from the database.
 *
 * @param mysqli $conn The database connection object.
 * @return mysqli_result|false The result set object or false on failure.
 */
function get_upcoming_tours($conn) {
    $query = "SELECT id, tour_name, start_date, description, price_per_person 
              FROM tour 
              WHERE start_date > CURDATE() 
              ORDER BY start_date ASC 
              LIMIT 1"; // Fetch only the nearest upcoming tour

    $result = $conn->query($query);

    if (!$result) {
        // Log the error if the query fails
        error_log("SQL Error in get_upcoming_tours: " . $conn->error);
        return false; // Return false on failure
    }

    return $result; // Return the result set
}
