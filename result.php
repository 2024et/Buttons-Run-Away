<?php
//データベース情報
$servername = "secret";
$username = "secret";
$password = "secret";
$dbname = "secret";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("接続失敗: " . $conn->connect_error); }

//ランキングへの登録
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $time = $_POST["time"];

    if (!empty($user) && !empty($time)) {
        $stmt = $conn->prepare("INSERT INTO ranking (username, time) VALUES (?, ?)");
        $stmt->bind_param("sd", $user, $time);
        $stmt->execute();
        header("Location: index.php");
    }
}

// ランキング取得
$sql = "SELECT username, time, created_at FROM ranking ORDER BY time ASC LIMIT 20";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Noの計測結果</title>
    <link rel="stylesheet" href="result.css">
    <link rel="icon" href="icon.ico">
</head>
<body>

<div class="section">
    <div class="card">
        <h1>"そうは思わない"を押すまでにかかった時間</h1>
        <p id="result"></p>
        <p>この画面をスクリーンショットしてみんなと共有しよう！</p>
        <a href="index.php">トップページへ戻る</a>
    </div>
</div>

<div class="section">
    <h2>ランキング一覧（上位20）</h2>
    <table>
        <tr>
            <th>順位</th>
            <th>ユーザー名</th>
            <th>タイム（秒）</th>
            <th>記録日時</th>
        </tr>
        <?php
        $rank = 1;
        if ($result->num_rows > 0):
            while($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?= $rank ?></td>
                <td><?= htmlspecialchars($row["username"]) ?></td>
                <td><?= $row["time"] ?></td>
                <td><?= $row["created_at"] ?></td>
            </tr>
        <?php
            $rank++;
            endwhile;
            endif;
        ?>
    </table>
</div>

<div class="section">
    <h2>結果をランキングに登録</h2>

    <form action="" method="post">
        <label>ユーザー名：</label><br>
        <input type="text" name="username" required><br>

        <input type="hidden" name="time" id="timeField">

        <button type="submit">ランキングに登録</button>
    </form>
</div>

<script>
    //localStrageから計測時間の結果を取得しhtmlへ表示
    const elapsed = localStorage.getItem("elapsedTime");

    document.getElementById("result").textContent =elapsed ? `${elapsed} 秒でした！` : "計測データがありません。";

    if (elapsed) {
        document.getElementById("timeField").value = elapsed;
    }

    // 開発者ツール禁止
    document.addEventListener("keydown", function (e) {
      if (
        e.key === "F12" ||
        (e.ctrlKey && e.shiftKey && (e.key === "I" || e.key === "J")) ||
        (e.ctrlKey && e.key === "U")
      ) {
        e.preventDefault();
        alert("開発者ツールは禁止されています。");
      }
    });

    //右クリックの使用禁止
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });
</script>

</body>
</html>