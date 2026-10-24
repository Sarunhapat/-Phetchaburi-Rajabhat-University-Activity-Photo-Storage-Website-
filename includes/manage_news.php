<?php
session_start();
include_once __DIR__ . '/../config.php';

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    echo "<p style='color:red;'>ไม่ได้รับสิทธิ์ในการเข้าถึงหน้านี้</p>";
    exit;
}

$response = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["title"])) {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $news_id = $_POST["news_id"] ?? null;
    $imgPath = null;

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {
        $imgName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetPath = __DIR__ . '/../uploads/news_images/' . $imgName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath);
        $imgPath = 'uploads/news_images/' . $imgName;
    }

    if ($news_id) {
        if ($imgPath) {
            $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ?, image_path = ? WHERE id = ?");
            $stmt->execute([$title, $content, $imgPath, $news_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
            $stmt->execute([$title, $content, $news_id]);
        }
        $status = "updated";
    } else {
        $stmt = $pdo->prepare("INSERT INTO news (title, content, image_path, created_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $imgPath, $_SESSION["user"]["id"]]);
        $status = "added";
    }
    echo "<script>
        $.get('includes/manage_news.php?status={$status}', function(data) {
            $('#content-area').html(data);
        });
    </script>";
    exit;
}

if (isset($_GET["delete_id"])) {
    $id = $_GET["delete_id"];
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    $stmt->execute([$id]);
    echo "<script>
        $.get('includes/manage_news.php?status=deleted', function(data) {
            $('#content-area').html(data);
        });
    </script>";
    exit;
}

$stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
$status = $_GET["status"] ?? null;
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($status): ?>
<script>
Swal.fire({
    icon: 'success',
    title: {
        added: 'เพิ่มข่าวสำเร็จ',
        updated: 'แก้ไขข่าวสำเร็จ',
        deleted: 'ลบข่าวสำเร็จ'
    }["<?= $status ?>"],
    toast: true,
    position: 'top-end',
    timer: 2000,
    showConfirmButton: false
});
</script>
<?php endif; ?>

<style>

body {
    font-family: 'Noto Sans Thai', sans-serif;
    background: #f1f5f9;
    margin: 0;
    padding: 0;
}
.news-form {
    max-width: 1000px;
    margin: 20px auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}
.news-form input, .news-form textarea {
    width: 100%;
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-bottom: 15px;
    font-size: 16px;
}
.news-form button {
    background: #003366;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    border: none;
    font-size: 16px;
    cursor: pointer;
}
.news-form button:hover {
    background: #0055a5;
}
.news-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
}
.news-table th, .news-table td {
    padding: 10px;
    border-bottom: 1px solid #ccc;
}
.news-table th {
    background: #003366;
    color: white;
}
.news-table img {
    max-height: 60px;
}
a.action-btn {
    margin-right: 10px;
    text-decoration: none;
    color: #0066cc;
}
a.del {
    color: red;
    cursor: pointer;
}
</style>

<h2>➕ เพิ่ม/แก้ไข ข่าว</h2>
<form method="POST" id="news-form" class="news-form" enctype="multipart/form-data">
    <input type="hidden" name="news_id" id="news_id">
    <input type="text" name="title" placeholder="หัวข้อข่าว" required id="news_title">
    <textarea name="content" placeholder="เนื้อหาข่าว" rows="4" required id="news_content"></textarea>
    <input type="file" name="image" accept="image/*">
    <button type="submit">บันทึกข่าว</button>
</form>

<h2>📰 ข่าวทั้งหมด</h2>
<table class="news-table">
    <tr>
        <th>หัวข้อ</th>
        <th>ภาพ</th>
        <th>วันที่</th>
        <th>จัดการ</th>
    </tr>
    <?php foreach ($news as $n): ?>
    <tr>
        <td><?= htmlspecialchars($n["title"]) ?></td>
        <td>
            <?php if ($n["image_path"]): ?>
                <img src="<?= $n["image_path"] ?>" alt="image">
            <?php else: ?> - <?php endif; ?>
        </td>
        <td><?= date("d/m/Y H:i", strtotime($n["created_at"])) ?></td>
        <td>
            <a href="#" class="action-btn detail" data-title="<?= htmlspecialchars($n['title']) ?>"
               data-content="<?= htmlspecialchars($n['content']) ?>"
               data-image="<?= $n['image_path'] ?>">ดูข้อมูลข่าว</a>
            <a href="#" class="action-btn edit"
               data-id="<?= $n['id'] ?>"
               data-title="<?= htmlspecialchars($n['title']) ?>"
               data-content="<?= htmlspecialchars($n['content']) ?>">เเก้ไขข่าว</a>
            <a href="#" class="del" data-id="<?= $n["id"] ?>">ลบข่าว</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<script>
$('#news-form').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
        url: 'includes/manage_news.php',
        type: 'POST',
        data: formData,
        success: function (data) {
            $('#content-area').html(data);
        },
        cache: false,
        contentType: false,
        processData: false
    });
});

$('.detail').on('click', function (e) {
    e.preventDefault();
    Swal.fire({
        title: $(this).data('title'),
        html: `<p>${$(this).data('content')}</p>` +
              ($(this).data('image') ? `<img src="${$(this).data('image')}" style='max-width:100%;margin-top:10px;'>` : ''),
        width: 600
    });
});

$('.edit').on('click', function (e) {
    e.preventDefault();
    $('#news_id').val($(this).data('id'));
    $('#news_title').val($(this).data('title'));
    $('#news_content').val($(this).data('content'));
    $('html, body').animate({ scrollTop: 0 }, 'slow');
});

$('.del').on('click', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    Swal.fire({
        title: 'ยืนยันการลบข่าวนี้?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.get('includes/manage_news.php?delete_id=' + id, function(data) {
                $('#content-area').html(data);
            });
        }
    });
});
</script>

