<?php
// Include the database connection
include('db_connect.php');

/**
 * Fetch upcoming tours from the database along with the total number of bookings.
 *
 * @param mysqli $conn The database connection object.
 * @return mysqli_result|false The result set object or false on failure.
 */
function get_upcoming_tours($conn) {
    // SQL query to fetch upcoming tours with booking count
    $sql = "
        SELECT 
            t.tour_id,
            t.tour_name,
            t.start_date,
            t.description,
            t.price_per_person,
            COUNT(b.booking_id) AS total_booked
        FROM tour t
        LEFT JOIN booking b ON t.tour_id = b.tour_id  -- Joining bookings table to get the count
        WHERE t.start_date > NOW()  -- Filter to only get upcoming tours
        GROUP BY t.tour_id  -- Group by tour_id to get total bookings per tour
        ORDER BY t.start_date ASC;  -- Order tours by start date
    ";

    // Execute the query
    $result = $conn->query($sql);

    // Return the result set or false if the query fails
    return $result;
}
?>
