<?php
session_start();
if (isset($_SESSION['quiz_start_time'])) {
    unset($_SESSION['quiz_start_time']);
}
$score = isset($_GET['score']) ? $_GET['score'] : 0;
$total = isset($_GET['total']) ? $_GET['total'] : 0;

function sortLeaderboard($leaderboard, $criterion) {
    usort($leaderboard, function ($a, $b) use ($criterion) {
        if ($criterion == 'score') {
            return $b['score'] - $a['score'];
        } elseif ($criterion == 'start_time') {
            return strtotime($a['start_time']) - strtotime($b['start_time']);
        } elseif ($criterion == 'finish_time') {
            return strtotime($a['finish_time']) - strtotime($b['finish_time']);
        } elseif ($criterion == 'time_taken') {
            $time_a = strtotime($a['finish_time']) - strtotime($a['start_time']);
            $time_b = strtotime($b['finish_time']) - strtotime($b['start_time']);
            return $time_a - $time_b;
        }
        return 0;
    });
    return $leaderboard;
}

if (isset($_POST['filter'])) {
    $filter = $_POST['filter'];
} else {
    $filter = 'score';
}

if (isset($_SESSION['leaderboard']) && count($_SESSION['leaderboard']) > 0) {
    $_SESSION['leaderboard'] = sortLeaderboard($_SESSION['leaderboard'], $filter);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quiz Result</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f2e9e1;
                color: #4a2c2a;
                margin: 0;
                padding: 20px;
            }
            .container {
                background-color: #fff;
                border-radius: 10px;
                padding: 30px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                max-width: 600px;
                margin: 0 auto;
            }
            h1 {
                text-align: center;
                color: #2c1a18;
                font-size: 2.5em;
                margin-bottom: 20px;
            }
            .score-display {
                text-align: center;
                font-size: 2em;
                color: #2c1a18;
                margin-top: 20px;
            }
            .links {
                text-align: center;
                margin-top: 20px;
            }
            .links .btn {
                background-color: #2c1a18;
                color: #fff;
                padding: 10px 20px;
                text-decoration: none;
                border-radius: 5px;
                display: inline-block;
                font-size: 1.2em;
                margin-top: 10px;
            }
            .links .btn:hover {
                background-color: #4a2c2a;
            }
            .leaderboard {
                margin-top: 30px;
                text-align: center;
            }
            .leaderboard table {
                width: 100%;
                border-collapse: collapse;
            }
            .leaderboard th, .leaderboard td {
                border: 1px solid #2c1a18;
                padding: 10px;
                text-align: center;
            }
            .leaderboard th {
                background-color: #2c1a18;
                color: #fff;
            }
            .filter-buttons {
                text-align: center;
                margin-bottom: 20px;
            }
            .filter-buttons button {
                margin-top: 10px;
                background-color: #d3b683;
                color: #000;
                padding: 5px 10px;
                border: none;
                border-radius: 2px;
                cursor: pointer;
                font-size: 0.6em;
            }
            .filter-buttons button:hover {
                background-color: #4a2c2a;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Quiz Result</h1>
            <div class="score-display">
                <p>Your Score: <?php echo $score; ?> / <?php echo $total; ?></p>
            </div>
            <div class="links">
                <a href="index.php" class="btn">Try Again</a><br>
            </div>
            <?php if (isset($_SESSION['leaderboard']) && count($_SESSION['leaderboard']) > 0): ?>
                <div class="filter-buttons">
                    <form method="POST">
                        <button type="submit" name="filter" value="score">Sort by Score</button>
                        <button type="submit" name="filter" value="start_time">Sort by Start Time</button>
                        <button type="submit" name="filter" value="finish_time">Sort by Finish Time</button>
                        <button type="submit" name="filter" value="time_taken">Sort by Time Taken</button>
                    </form>
                </div>
                <div class="leaderboard">
                    <h2>Leaderboard</h2>
                    <table>
                        <tr>
                            <th>Username</th>
                            <th>Score</th>
                            <th>Start Time</th>
                            <th>Finish Time</th>
                            <th>Time Taken</th>
                        </tr>
                        <?php foreach ($_SESSION['leaderboard'] as $entry): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($entry['username']); ?></td>
                                <td><?php echo $entry['score']; ?></td>
                                <td>
                                    <?php 
                                    $start_time = new DateTime($entry['start_time'], new DateTimeZone('UTC'));
                                    $start_time->setTimezone(new DateTimeZone('Asia/Manila'));
                                    echo $start_time->format('Y-m-d H:i:s'); 
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $finish_time = new DateTime($entry['finish_time'], new DateTimeZone('UTC'));
                                    $finish_time->setTimezone(new DateTimeZone('Asia/Manila'));
                                    echo $finish_time->format('Y-m-d H:i:s'); 
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $start = new DateTime($entry['start_time'], new DateTimeZone('UTC'));
                                    $finish = new DateTime($entry['finish_time'], new DateTimeZone('UTC'));
                                    $start->setTimezone(new DateTimeZone('Asia/Manila'));
                                    $finish->setTimezone(new DateTimeZone('Asia/Manila'));
                                    $interval = $start->diff($finish);
                                    echo $interval->format('%H:%I:%S');
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>
