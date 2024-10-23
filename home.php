<?php 
session_start();

if (isset($_SESSION['username'])) {
    include 'app/db.conn.php';
    include 'app/helpers/user.php';
    include 'app/helpers/conversations.php';
    include 'app/helpers/timeAgo.php';
    include 'app/helpers/last_chat.php';

    $user = getUser($_SESSION['username'], $conn);
    $conversations = getConversation($user['user_id'], $conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/download.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background: #f0f2f5;
			margin-top: 60px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            position: relative;
        }
        .header img {
            width: 50px;
            border-radius: 50%;
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
        }
        .search-group {
            padding: 10px;
        }
        .search-group input {
            border-radius: 20px;
        }
        .chat-list {
            max-height: 60vh;
            overflow-y: auto;
            padding: 10px;
        }
        .list-group-item {
            border: none;
            border-bottom: 1px solid #e0e0e0;
            transition: background 0.3s;
        }
        .list-group-item:hover {
            background: #f7f7f7;
        }
        .online {
            width: 10px;
            height: 10px;
            background: green;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="uploads/<?=$user['p_p']?>" alt="<?=$user['username']?>">
            <h3 class="fs-xs m-2"><?=$user['username']?></h3>
            <a href="logout.php" class="btn btn-dark btn-sm position-absolute" style="right: 15px; top: 15px;">Logout</a>
        </div>
        <div class="search-group">
            <div class="input-group">
                <input type="text" placeholder="Search..." id="searchText" class="form-control">
                <button class="btn btn-primary" id="searchBtn"><i class="fa fa-search"></i></button>
            </div>
        </div>
        <ul id="chatList" class="list-group chat-list">
            <?php if (!empty($conversations)) { ?>
                <?php foreach ($conversations as $conversation){ ?>
                    <li class="list-group-item">
                        <a href="chat.php?user=<?=$conversation['username']?>" class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="uploads/<?=$conversation['p_p']?>" class="w-10 rounded-circle">
                                <div class="ms-2">
                                    <h5 class="m-0"><?=$conversation['name']?></h5>
                                    <small>
                                        <?php 
                                            echo lastChat($_SESSION['user_id'], $conversation['user_id'], $conn);
                                        ?>
                                    </small>
                                </div>
                            </div>
                            <?php if (last_seen($conversation['last_seen']) == "Active") { ?>
                                <div title="online" class="online"></div>
                            <?php } ?>
                        </a>
                    </li>
                <?php } ?>
            <?php } else { ?>
                <div class="alert alert-info text-center">
                    <i class="fa fa-comments d-block fs-big"></i>
                    No messages yet, Start the conversation
                </div>
            <?php } ?>
        </ul>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#searchText").on("input", function(){
                var searchText = $(this).val();
                if(searchText == "") return;
                $.post('app/ajax/search.php', { key: searchText }, function(data, status){
                    $("#chatList").html(data);
                });
            });

            $("#searchBtn").on("click", function(){
                var searchText = $("#searchText").val();
                if(searchText == "") return;
                $.post('app/ajax/search.php', { key: searchText }, function(data, status){
                    $("#chatList").html(data);
                });
            });

            let lastSeenUpdate = function(){
                $.get("app/ajax/update_last_seen.php");
            }
            lastSeenUpdate();
            setInterval(lastSeenUpdate, 10000);
        });
    </script>
</body>
</html>
<?php
} else {
    header("Location: index.php");
    exit;
}
?>
