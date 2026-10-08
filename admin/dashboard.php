
<?php

session_start();

require_once "../backend/config.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$totalStmt = $pdo->query("SELECT COUNT(*) FROM messages");
$totalMessages = $totalStmt->fetchColumn();

$unreadStmt = $pdo->query(
    "SELECT COUNT(*) FROM messages WHERE status = 'unread'"
);
$unreadMessages = $unreadStmt->fetchColumn();

$stmt = $pdo->query(
    "SELECT * FROM messages ORDER BY created_at DESC"
);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | Saugat Chapagain</title>

    <link
        href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
        rel="stylesheet"
    >

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f6ff;
            color: #4f4b4b;
            min-height: 100vh;
        }

        header {
            width: 100%;
            background: #ffffff;
            border-bottom: 1px solid #e6eaf2;
            padding: 17px 7%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            color: #111111;
            font-size: 25px;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .logo span {
            color: #3771c8;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .admin-user {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #3771c8;
            font-size: 14px;
            font-weight: 600;
            padding: 7px 12px;
            background: #f3f6ff;
            border: 1px solid #e2eaf8;
            border-radius: 20px;
        }

        .admin-user i {
            font-size: 19px;
            color: #3771c8;
        }

        .logout {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #4f4b4b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            padding: 7px 10px;
            border-radius: 6px;
            transition: color 0.2s ease, background 0.2s ease;
        }

        .logout i {
            font-size: 17px;
        }

        .logout:hover {
            color: #3771c8;
            background: #f3f6ff;
        }

        .container {
            width: 86%;
            max-width: 1200px;
            margin: 45px auto;
        }

        .page-title {
            margin-bottom: 30px;
        }

        .page-title p {
            color: #3771c8;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.7px;
            margin-bottom: 8px;
        }

        .page-title h1 {
            color: #111111;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 38px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e6eaf2;
            border-radius: 12px;
            padding: 24px 25px;
            box-shadow: 0 8px 25px rgba(55, 113, 200, 0.05);
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .stat-card p {
            color: #777777;
            font-size: 14px;
            font-weight: 500;
        }

        .stat-icon {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f6ff;
            color: #3771c8;
            border-radius: 8px;
            font-size: 20px;
        }

        .stat-card h2 {
            color: #3771c8;
            font-size: 30px;
            font-weight: 700;
        }

        .messages-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 18px;
        }

        .messages-header h2 {
            color: #111111;
            font-size: 22px;
            font-weight: 700;
        }

        .message-count {
            color: #777777;
            font-size: 13px;
        }

        .message-tools {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-box {
            position: relative;
            flex: 1;
        }

        .search-box i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #888888;
            font-size: 19px;
        }

        .search-box input {
            width: 100%;
            height: 42px;
            padding: 0 14px 0 40px;
            border: 1px solid #dfe5ef;
            border-radius: 8px;
            background: #ffffff;
            color: #333333;
            font-size: 14px;
            outline: none;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        .search-box input:focus {
            border-color: #3771c8;
            box-shadow: 0 0 0 3px rgba(55, 113, 200, 0.08);
        }

        .filters {
            display: flex;
            gap: 7px;
        }

        .filter-btn {
            border: 1px solid #dfe5ef;
            background: #ffffff;
            color: #666666;
            padding: 9px 13px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-btn:hover {
            color: #3771c8;
            border-color: #cbd9ef;
        }

        .filter-btn.active {
            background: #3771c8;
            border-color: #3771c8;
            color: #ffffff;
        }

        .message-card {
            background: #ffffff;
            border: 1px solid #e6eaf2;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 15px;
            box-shadow: 0 6px 20px rgba(55, 113, 200, 0.04);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .message-card:hover {
            box-shadow: 0 10px 28px rgba(55, 113, 200, 0.08);
            transform: translateY(-1px);
        }

        .message-card.unread {
            border-left: 4px solid #3771c8;
        }

        .message-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 16px;
        }

        .sender h3 {
            color: #222222;
            font-size: 17px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .sender a {
            color: #3771c8;
            font-size: 14px;
            text-decoration: none;
        }

        .sender a:hover {
            text-decoration: underline;
        }

        .date {
            color: #888888;
            font-size: 12px;
            white-space: nowrap;
            padding-top: 3px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 9px;
            padding: 4px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-badge.unread {
            background: #eef4ff;
            color: #3771c8;
        }

        .status-badge.read {
            background: #f1f3f5;
            color: #777777;
        }

        .message-text {
            color: #555555;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 20px;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .message-actions {
            display: flex;
            align-items: center;
            gap: 9px;
            padding-top: 14px;
            border-top: 1px solid #f0f2f6;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .read-btn {
            background: #eef4ff;
            color: #3771c8;
        }

        .read-btn:hover {
            background: #e1ebff;
        }

        .delete-btn {
            background: #fff1f1;
            color: #c62828;
        }

        .delete-btn:hover {
            background: #ffe2e2;
        }

        .no-results {
            display: none;
            background: #ffffff;
            border: 1px solid #e6eaf2;
            border-radius: 12px;
            padding: 45px 30px;
            text-align: center;
            color: #777777;
        }

        .no-results i {
            display: block;
            color: #3771c8;
            font-size: 40px;
            margin-bottom: 12px;
        }

        .no-results p {
            font-size: 14px;
        }

        .empty {
            background: #ffffff;
            border: 1px solid #e6eaf2;
            border-radius: 12px;
            padding: 55px 30px;
            text-align: center;
            color: #777777;
        }

        .empty i {
            display: block;
            color: #3771c8;
            font-size: 42px;
            margin-bottom: 12px;
        }

        .empty p {
            font-size: 14px;
        }

        @media (max-width: 700px) {

            header {
                padding: 16px 5%;
            }

            .container {
                width: 90%;
                margin: 32px auto;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .message-tools {
                flex-direction: column;
                align-items: stretch;
            }

            .filters {
                width: 100%;
            }

            .filter-btn {
                flex: 1;
            }

            .messages-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 5px;
            }

            .message-top {
                flex-direction: column;
                gap: 8px;
            }

            .date {
                white-space: normal;
            }

            .admin-user {
                display: none;
            }

            .logout span {
                display: none;
            }

            .logout {
                padding: 7px;
                font-size: 19px;
            }

            .page-title h1 {
                font-size: 28px;
            }
        }

    </style>

</head>

<body>

<header>

    <a href="../index.html" class="logo">
        SC<span>.</span>
    </a>

    <div class="header-right">

        <div class="admin-user">
            <i class="bx bx-user-circle"></i>

            <span>
                <?php
                echo htmlspecialchars(
                    $_SESSION["admin_username"]
                );
                ?>
            </span>
        </div>

        <a href="logout.php" class="logout">
            <i class="bx bx-log-out"></i>

            <span>
                Logout
            </span>
        </a>

    </div>

</header>

<main class="container">

    <div class="page-title">
        <h1>Dashboard</h1>
    </div>

    <div class="stats">

        <div class="stat-card">

            <div class="stat-top">

                <p>
                    Total Messages
                </p>

                <div class="stat-icon">
                    <i class="bx bx-envelope"></i>
                </div>

            </div>

            <h2>
                <?php echo $totalMessages; ?>
            </h2>

        </div>

        <div class="stat-card">

            <div class="stat-top">

                <p>
                    Unread Messages
                </p>

                <div class="stat-icon">
                    <i class="bx bx-envelope-open"></i>
                </div>

            </div>

            <h2>
                <?php echo $unreadMessages; ?>
            </h2>

        </div>

    </div>

    <div class="messages-header">

        <h2>
            Messages
        </h2>

        <span class="message-count" id="message-count">
            <?php echo $totalMessages; ?> message(s)
        </span>

    </div>

    <?php if (empty($messages)): ?>

        <div class="empty">

            <i class="bx bx-envelope"></i>

            <p>
                No messages received yet.
            </p>

        </div>

    <?php else: ?>

        <div class="message-tools">

            <div class="search-box">

                <i class="bx bx-search"></i>

                <input
                    type="text"
                    id="message-search"
                    placeholder="Search messages..."
                    autocomplete="off"
                >

            </div>

            <div class="filters">

                <button
                    type="button"
                    class="filter-btn active"
                    data-filter="all"
                >
                    All
                </button>

                <button
                    type="button"
                    class="filter-btn"
                    data-filter="unread"
                >
                    Unread
                </button>

                <button
                    type="button"
                    class="filter-btn"
                    data-filter="read"
                >
                    Read
                </button>

            </div>

        </div>

        <div id="message-list">

            <?php foreach ($messages as $message): ?>

                <div
                    class="message-card <?php echo $message["status"] === "unread" ? "unread" : ""; ?>"
                    data-status="<?php echo htmlspecialchars($message["status"]); ?>"
                    data-search="<?php
                        echo htmlspecialchars(
                            strtolower(
                                $message["name"] . " " .
                                $message["email"] . " " .
                                $message["message"]
                            )
                        );
                    ?>"
                >

                    <div class="message-top">

                        <div class="sender">

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $message["name"]
                                );
                                ?>
                            </h3>

                            <a href="mailto:<?php
                                echo htmlspecialchars(
                                    $message["email"]
                                );
                            ?>">
                                <?php
                                echo htmlspecialchars(
                                    $message["email"]
                                );
                                ?>
                            </a>

                            <div
                                class="status-badge <?php echo $message["status"] === "unread" ? "unread" : "read"; ?>"
                            >

                                <i class="bx <?php
                                    echo $message["status"] === "unread"
                                        ? "bx-envelope"
                                        : "bx-check-circle";
                                ?>"></i>

                                <?php
                                echo ucfirst(
                                    htmlspecialchars(
                                        $message["status"]
                                    )
                                );
                                ?>

                            </div>

                        </div>

                        <div class="date">

                            <?php
                            echo date(
                                "M d, Y • h:i A",
                                strtotime(
                                    $message["created_at"]
                                )
                            );
                            ?>

                        </div>

                    </div>

                    <div class="message-text">

                        <?php
                        echo htmlspecialchars(
                            $message["message"]
                        );
                        ?>

                    </div>

                    <div class="message-actions">

                        <?php if ($message["status"] === "unread"): ?>

                            <a
                                href="mark-read.php?id=<?php echo $message["id"]; ?>"
                                class="action-btn read-btn"
                            >
                                <i class="bx bx-check"></i>
                                Mark as Read
                            </a>

                        <?php endif; ?>

                        <a
                            href="delete-message.php?id=<?php echo $message["id"]; ?>"
                            class="action-btn delete-btn"
                            onclick="return confirm('Are you sure you want to delete this message?');"
                        >
                            <i class="bx bx-trash"></i>
                            Delete
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="no-results" id="no-results">

            <i class="bx bx-search-alt"></i>

            <p>
                No messages match your search or filter.
            </p>

        </div>

    <?php endif; ?>

</main>

<script>

    const searchInput = document.getElementById("message-search");
    const filterButtons = document.querySelectorAll(".filter-btn");
    const messageCards = document.querySelectorAll(".message-card");
    const messageCount = document.getElementById("message-count");
    const noResults = document.getElementById("no-results");

    let currentFilter = "all";

    function filterMessages() {

        if (!searchInput) {
            return;
        }

        const searchValue =
            searchInput.value.trim().toLowerCase();

        let visibleCount = 0;

        messageCards.forEach((card) => {

            const status =
                card.dataset.status;

            const searchText =
                card.dataset.search;

            const matchesSearch =
                searchText.includes(searchValue);

            const matchesFilter =
                currentFilter === "all" ||
                status === currentFilter;

            if (matchesSearch && matchesFilter) {

                card.style.display = "block";
                visibleCount++;

            } else {

                card.style.display = "none";

            }

        });

        if (messageCount) {

            messageCount.textContent =
                visibleCount +
                (visibleCount === 1
                    ? " message"
                    : " messages");

        }

        if (noResults) {

            noResults.style.display =
                visibleCount === 0
                    ? "block"
                    : "none";

        }

    }

    if (searchInput) {

        searchInput.addEventListener(
            "input",
            filterMessages
        );

    }

    filterButtons.forEach((button) => {

        button.addEventListener("click", () => {

            filterButtons.forEach((item) => {
                item.classList.remove("active");
            });

            button.classList.add("active");

            currentFilter =
                button.dataset.filter;

            filterMessages();

        });

    });

</script>

</body>
</html>
