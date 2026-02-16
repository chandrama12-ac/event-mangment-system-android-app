<?php
require_once 'db_config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Helper to generate a unique slug
function createSlug($title) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    return $slug . '-' . time();
}

switch($method) {
    case 'GET':
        if ($action === 'list') {
            $userId = $_GET['user_id'] ?? 0;
            $search = $_GET['search'] ?? '';
            
            $where = "e.is_active = 1 AND e.status = 'published'";
            $params = [$userId];
            
            if (!empty($search)) {
                $where .= " AND (e.title LIKE ? OR e.category LIKE ? OR e.college_name LIKE ? OR e.location LIKE ? OR e.speaker_name LIKE ?)";
                $searchParam = "%$search%";
                array_push($params, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
            }
            
            $query = "SELECT e.*, a.full_name as creator_name,
                      (SELECT COUNT(*) FROM registrations WHERE event_id = e.id AND user_id = ? AND status != 'cancelled') as is_registered 
                      FROM events e 
                      LEFT JOIN admins a ON e.created_by = a.id 
                      WHERE $where
                      ORDER BY e.event_date ASC";
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            sendResponse("success", "Events list retrieved", $stmt->fetchAll());
        }

        if ($action === 'trending') {
            $userId = $_GET['user_id'] ?? 0;
            $query = "SELECT e.*, a.full_name as creator_name,
                      (SELECT COUNT(*) FROM registrations WHERE event_id = e.id AND user_id = ? AND status != 'cancelled') as is_registered 
                      FROM events e 
                      LEFT JOIN admins a ON e.created_by = a.id 
                      WHERE e.is_active = 1 AND e.status = 'published' AND e.is_trending = 1
                      ORDER BY e.rating DESC, e.event_date ASC
                      LIMIT 10";
            $stmt = $conn->prepare($query);
            $stmt->execute([$userId]);
            sendResponse("success", "Trending events retrieved", $stmt->fetchAll());
        }

        if ($action === 'admin_list') {
            $query = "SELECT e.*, (SELECT COUNT(*) FROM registrations WHERE event_id = e.id) as attendee_count 
                      FROM events e ORDER BY e.created_at DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            sendResponse("success", "Admin events list retrieved", $stmt->fetchAll());
        }

        if ($action === 'details' && isset($_GET['id'])) {
            $query = "SELECT e.*, a.full_name as creator_name FROM events e LEFT JOIN admins a ON e.created_by = a.id WHERE e.id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$_GET['id']]);
            $event = $stmt->fetch();
            if ($event) {
                sendResponse("success", "Event found", $event);
            } else {
                sendResponse("error", "Event not found", null, 404);
            }
        }
        break;

    case 'POST':
        // Support both JSON (for legacy/simple updates) and Multipart (for image uploads)
        $is_multipart = strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false;
        
        if ($is_multipart) {
            $data = (object)$_POST;
            $file = $_FILES['image'] ?? null;
        } else {
            $data = json_decode(file_get_contents("php://input"));
            $file = null;
        }

        if ($action === 'create') {
            if (!empty($data->title) && !empty($data->event_date) && !empty($data->created_by)) {
                $image_url = $data->image_url ?? '';
                
                // Handle Image Upload
                if ($file && $file['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../../uploads/events/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    
                    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $newFileName = md5(time() . $file['name']) . '.' . $fileExt;
                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
                        $image_url = 'uploads/events/' . $newFileName;
                    }
                }

                $slug = createSlug($data->title);
                $query = "INSERT INTO events (title, slug, description, event_date, location, latitude, longitude, venue_address, image_url, capacity, event_type, category, speaker_name, is_trending, college_name, rating, created_by) 
                          VALUES (:title, :slug, :description, :event_date, :location, :latitude, :longitude, :venue_address, :image_url, :capacity, :event_type, :category, :speaker_name, :is_trending, :college_name, :rating, :created_by)";
                $stmt = $conn->prepare($query);
                
                $params = [
                    ':title' => $data->title,
                    ':slug' => $slug,
                    ':description' => $data->description ?? '',
                    ':event_date' => $data->event_date,
                    ':location' => $data->location ?? '',
                    ':latitude' => $data->latitude ?? null,
                    ':longitude' => $data->longitude ?? null,
                    ':venue_address' => $data->venue_address ?? null,
                    ':image_url' => $image_url,
                    ':capacity' => $data->capacity ?? null,
                    ':event_type' => $data->event_type ?? 'other',
                    ':category' => $data->category ?? 'General',
                    ':speaker_name' => $data->speaker_name ?? null,
                    ':is_trending' => $data->is_trending ?? 0,
                    ':college_name' => $data->college_name ?? null,
                    ':rating' => $data->rating ?? 4.5,
                    ':created_by' => $data->created_by
                ];

                if ($stmt->execute($params)) {
                    sendResponse("success", "Event created successfully", ["id" => $conn->lastInsertId(), "slug" => $slug], 201);
                } else {
                    sendResponse("error", "Unable to create event", null, 500);
                }
            } else {
                sendResponse("error", "Incomplete data", null, 400);
            }
        }

        if ($action === 'update' && !empty($data->id)) {
            $image_url = $data->image_url ?? '';
            
            // Handle Image Upload if provided
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../uploads/events/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $newFileName = md5(time() . $file['name']) . '.' . $fileExt;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
                    $image_url = 'uploads/events/' . $newFileName;
                }
            }

            $query = "UPDATE events SET title=:title, description=:description, event_date=:event_date, 
                       location=:location, latitude=:latitude, longitude=:longitude, venue_address=:venue_address,
                       image_url=IF(:image_url != '', :image_url, image_url), capacity=:capacity, 
                       event_type=:event_type, category=:category, speaker_name=:speaker_name, 
                       is_trending=:is_trending, college_name=:college_name, rating=:rating, 
                       status=:status WHERE id=:id";
            $stmt = $conn->prepare($query);
            $params = [
                ':id' => $data->id,
                ':title' => $data->title,
                ':description' => $data->description,
                ':event_date' => $data->event_date,
                ':location' => $data->location,
                ':latitude' => $data->latitude,
                ':longitude' => $data->longitude,
                ':venue_address' => $data->venue_address,
                ':image_url' => $image_url,
                ':capacity' => $data->capacity,
                ':event_type' => $data->event_type,
                ':category' => $data->category,
                ':speaker_name' => $data->speaker_name,
                ':is_trending' => $data->is_trending,
                ':college_name' => $data->college_name,
                ':rating' => $data->rating,
                ':status' => $data->status ?? 'published'
            ];
            if ($stmt->execute($params)) {
                sendResponse("success", "Event updated successfully");
            } else {
                sendResponse("error", "Unable to update event");
            }
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $query = "UPDATE events SET is_active = 0 WHERE id = ?";
            $stmt = $conn->prepare($query);
            if ($stmt->execute([$_GET['id']])) {
                sendResponse("success", "Event deleted (archived) successfully");
            } else {
                sendResponse("error", "Unable to delete event");
            }
        }
        break;
}

sendResponse("error", "Request not handled or invalid method", ["method" => $method, "action" => $action], 404);
?>
