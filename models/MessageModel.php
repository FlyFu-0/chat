<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/db.php';

class MessageModel
{
    private $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function fetchPagedMessages()
    {
        $res = mysqli_query($this->db, "SELECT COUNT(*) FROM `messages`");
        $row = mysqli_fetch_row($res);
        $total = $row[0];

        $count = 5;
        $totalPages = ceil($total / $count);

        $page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) ? min((int)$_GET['page'], $totalPages) : 1;

        $start = ($page * $count) - $count;

        $result = mysqli_query(
            $this->db,
            "SELECT username, email, text, m.create_date AS 'created', filePath
            FROM `messages` m JOIN `user` u ON m.user_id = u.id
            ORDER BY 4 DESC
            LIMIT $start, $count;"
        );

        return [
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'result' => mysqli_fetch_all($result, MYSQLI_ASSOC)
        ];
    }

    public function createMessage(string $message, string $savedfilePath = NULl): bool
    {
        $savedfilePath = mysqli_escape_string($this->db, $savedfilePath);
        $message = mysqli_escape_string($this->db, $message);
        return  mysqli_query($this->db, "INSERT INTO messages (text, user_id, filePath) VALUES ('$message', 1, '$savedfilePath');");
    }
}