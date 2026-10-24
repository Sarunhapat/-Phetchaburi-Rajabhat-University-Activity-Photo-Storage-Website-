<?php
session_start();
include_once __DIR__ . '/../config.php';

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    echo "<p style='color:red;'>ไม่ได้รับสิทธิ์ในการเข้าถึงหน้านี้</p>";
    exit;
}

$edit_activity = null;
$status = $_GET["status"] ?? null;

if (isset($_GET["edit_id"])) {
    $stmt = $pdo->prepare("SELECT * FROM activities WHERE id = ?");
    $stmt->execute([$_GET["edit_id"]]);
    $edit_activity = $stmt->fetch();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["title"])) {
    $title = $_POST["title"];
    $desc = $_POST["description"];
    $date = $_POST["activity_date"];
    if (isset($_POST["update_id"])) {
        $stmt = $pdo->prepare("UPDATE activities SET title = ?, description = ?, activity_date = ? WHERE id = ?");
        $stmt->execute([$title, $desc, $date, $_POST["update_id"]]);
        $status = "updated";
    } else {
        $stmt = $pdo->prepare("INSERT INTO activities (title, description, activity_date, user_id, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$title, $desc, $date, $_SESSION["user"]["id"]]);
        $status = "added";
    }
    echo "<script>
        $.get('includes/manage_activities.php?status={$status}', function(data) {
            $('#content-area').html(data);
        });
    </script>";
    exit;
}

if (isset($_GET["delete_id"])) {
    $stmt = $pdo->prepare("DELETE FROM activities WHERE id = ?");
    $stmt->execute([$_GET["delete_id"]]);
    echo "<script>
        $.get('includes/manage_activities.php?status=deleted', function(data) {
            $('#content-area').html(data);
        });
    </script>";
    exit;
}

$stmt = $pdo->query("SELECT a.*, (SELECT COUNT(*) FROM activity_images WHERE activity_id = a.id) AS image_count FROM activities a ORDER BY a.activity_date DESC");
$activities = $stmt->fetchAll();
?>

<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body {
    font-family: 'Noto Sans Thai', sans-serif;
    background: #f1f5f9;
    margin: 0;
    padding: 0;
}
.section {
    max-width: 1000px;
    margin: 20px auto;
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}
.section h2 {
    margin-top: 0;
    color: #003366;
}
.form-group {
    margin-bottom: 15px;
}
.form-group input, .form-group textarea {
    width: 100%;
    padding: 12px;
    font-size: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
}
button, .btn-cancel {
    background: #003366;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
    margin-right: 10px;
}
.btn-cancel {
    background: #888;
}
button:hover {
    background: #0055a5;
}
.btn-cancel:hover {
    background: #666;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
}
table th, table td {
    padding: 12px 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}
table th {
    background: #003366;
    color: white;
}
.badge {
    background: #0066cc;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 13px;
}
.action a {
    text-decoration: none;
    margin-right: 10px;
}
.action a.edit { color: #0066cc; }
.action a.del { color: red; }
</style>

<?php if ($status): ?>
<script>
Swal.fire({
    toast: true,
    icon: 'success',
    title: {
        added: 'เพิ่มกิจกรรมสำเร็จ',
        updated: 'แก้ไขกิจกรรมสำเร็จ',
        deleted: 'ลบกิจกรรมสำเร็จ'
    }["<?= $status ?>"],
    position: 'top-end',
    showConfirmButton: false,
    timer: 2500
});
</script>
<?php endif; ?>

<div class="section">
    <h2><?= $edit_activity ? '✏️ แก้ไขกิจกรรม' : '➕ เพิ่มกิจกรรมใหม่' ?></h2>
    <form method="POST" id="activity-form">
        <?php if ($edit_activity): ?>
            <input type="hidden" name="update_id" value="<?= $edit_activity['id'] ?>">
        <?php endif; ?>
        <div class="form-group">
            <input type="text" name="title" placeholder="ชื่อกิจกรรม" required value="<?= htmlspecialchars($edit_activity['title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <textarea name="description" placeholder="รายละเอียดกิจกรรม" rows="3" required><?= htmlspecialchars($edit_activity['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <input type="date" name="activity_date" required value="<?= $edit_activity['activity_date'] ?? '' ?>">
        </div>
        <button type="submit"><?= $edit_activity ? 'บันทึกการแก้ไข' : 'เพิ่มกิจกรรม' ?></button>
        <?php if ($edit_activity): ?>
        <button class="btn-cancel" id="cancel-edit">ยกเลิก</button>
        <?php endif; ?>
    </form>
</div>

<div class="section">
    <h2>📅 รายการกิจกรรมทั้งหมด</h2>
    <table>
        <tr>
            <th>ชื่อกิจกรรม</th>
            <th>วันที่จัด</th>
            <th>เพิ่มเมื่อ</th>
            <th>รูปภาพ</th>
            <th>จัดการ</th>
        </tr>
        <?php foreach ($activities as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a["title"]) ?></td>
            <td><?= $a["activity_date"] ?></td>
            <td><?= date("d/m/Y H:i", strtotime($a["created_at"])) ?></td>
            <td><span class="badge"><?= $a["image_count"] ?> รูป</span></td>
            <td class="action">
                <a href="#" class="edit"
                   data-id="<?= $a["id"] ?>"
                   data-title="<?= htmlspecialchars($a["title"]) ?>"
                   data-description="<?= htmlspecialchars($a["description"]) ?>"
                   data-date="<?= $a["activity_date"] ?>">เเก้ไข</a>
                <a href="#" class="del" data-id="<?= $a["id"] ?>">ลบ</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<script>
$('#activity-form').on('submit', function (e) {
    e.preventDefault();
    $.post('includes/manage_activities.php', $(this).serialize(), function (data) {
        $('#content-area').html(data);
    });
});

$('.edit').on('click', function (e) {
    e.preventDefault();
    $('#activity-form input[name=update_id]').val($(this).data('id'));
    $('#activity-form input[name=title]').val($(this).data('title'));
    $('#activity-form textarea[name=description]').val($(this).data('description'));
    $('#activity-form input[name=activity_date]').val($(this).data('date'));
    $('html, body').animate({ scrollTop: 0 }, 'smooth');
});

$('.del').on('click', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: 'กิจกรรมนี้จะถูกลบอย่างถาวร',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.get('includes/manage_activities.php?delete_id=' + id, function (data) {
                $('#content-area').html(data);
            });
        }
    });
});

$('#cancel-edit').on('click', function (e) {
    e.preventDefault();
    $.get('includes/manage_activities.php', function (data) {
        $('#content-area').html(data);
    });
});
</script>
