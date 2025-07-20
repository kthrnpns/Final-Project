<?php
// workshops.php - Workshop related functions (WIP)
function get_workshop_by_id($id) {
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM workshops WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_workshop(
    $title,
    $description,
    $short_desc,
    $date,
    $time,
    $end_time,
    $location,
    $capacity,
    $image_url,
    $category,
) {
    global $db;

    $stmt = $db->prepare("
        INSERT INTO workshops (title, description, short_description, date, time, end_time, location, image_url, capacity)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([$title, $description, $short_desc, $date, $time, $end_time, $location, $capacity, $image_url, $category]);
}

// function update_workshop($id, $title, $description, $date, $time, $location, $capacity, $instructor)
// {
//     global $db;

//     $stmt = $db->prepare("
//         UPDATE workshops 
//         SET title = ?, description = ?, date = ?, time = ?, location = ?, capacity = ?, instructor = ?
//         WHERE id = ?
//     ");

//     return $stmt->execute([$title, $description, $date, $time, $location, $capacity, $instructor, $id]);
// }

function delete_workshop($id)
{
    global $db;

    $stmt = $db->prepare("DELETE FROM workshops WHERE id = ?");
    return $stmt->execute([$id]);
}
