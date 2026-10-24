<?php
session_start();
include_once __DIR__ . '/../config.php';

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    echo "<p style='color:red;'>ไม่ได้รับสิทธิ์ในการเข้าถึงหน้านี้</p>";
    exit;
}

// ดึงข้อมูลผู้ใช้
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">👤 จัดการผู้ใช้งาน</h5>
        </div>
        <div class="card-body">
            <form id="add-user-form" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="username" class="form-control" placeholder="ชื่อผู้ใช้" required>
                </div>
                <div class="col-md-3">
                    <input type="email" name="email" class="form-control" placeholder="อีเมล" required>
                </div>
                <div class="col-md-3">
                    <input type="password" name="password" class="form-control" placeholder="รหัสผ่าน" required>
                </div>
                <div class="col-md-2">
                    <select name="role" class="form-select" required>
                        <option value="">บทบาท</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-success">➕ เพิ่ม</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white">
            <strong>📋 รายชื่อผู้ใช้งาน</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ชื่อผู้ใช้</th>
                        <th>อีเมล</th>
                        <th>บทบาท</th>
                        <th>สมัครเมื่อ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody id="user-list">
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u["username"]) ?></td>
                        <td><?= htmlspecialchars($u["email"]) ?></td>
                        <td><?= $u["role"] === "admin" ? "<span class='badge bg-danger'>admin</span>" : "<span class='badge bg-secondary'>user</span>" ?></td>
                        <td><?= date("d/m/Y H:i", strtotime($u["created_at"])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-btn"
                                    data-id="<?= $u["id"] ?>"
                                    data-username="<?= htmlspecialchars($u["username"]) ?>"
                                    data-email="<?= htmlspecialchars($u["email"]) ?>"
                                    data-role="<?= $u["role"] ?>">แก้ไข</button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="<?= $u["id"] ?>">ลบ</button>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$('#add-user-form').on('submit', function (e) {
    e.preventDefault();
    $.post('includes/ajax_add_user.php', $(this).serialize(), function (res) {
        if (res.status === 'success') {
            Swal.fire('เพิ่มผู้ใช้แล้ว', '', 'success');
            $.get('includes/manage_users.php', function (data) {
                $('#content-area').html(data);
            });
        } else {
            Swal.fire('ผิดพลาด', res.message || 'เกิดข้อผิดพลาด', 'error');
        }
    }, 'json');
});

$('.delete-btn').on('click', function () {
    const id = $(this).data('id');
    Swal.fire({
        title: 'ลบผู้ใช้นี้?',
        text: 'การลบนี้ไม่สามารถย้อนกลับได้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
    }).then(result => {
        if (result.isConfirmed) {
            $.post('includes/ajax_delete_user.php', { id }, function () {
                $.get('includes/manage_users.php', function (data) {
                    $('#content-area').html(data);
                });
            });
        }
    });
});

$('.edit-btn').on('click', function () {
    const id = $(this).data('id');
    const username = $(this).data('username');
    const email = $(this).data('email');
    const role = $(this).data('role');

    Swal.fire({
        title: 'แก้ไขผู้ใช้',
        html: `
            <input type="text" id="sw-username" class="swal2-input" placeholder="ชื่อผู้ใช้" value="${username}">
            <input type="email" id="sw-email" class="swal2-input" placeholder="อีเมล" value="${email}">
            <select id="sw-role" class="swal2-input">
                <option value="user" ${role === 'user' ? 'selected' : ''}>User</option>
                <option value="admin" ${role === 'admin' ? 'selected' : ''}>Admin</option>
            </select>
        `,
        confirmButtonText: 'บันทึก',
        preConfirm: () => {
            return {
                id,
                username: $('#sw-username').val(),
                email: $('#sw-email').val(),
                role: $('#sw-role').val()
            };
        }
    }).then(result => {
        if (result.value) {
            $.post('includes/ajax_update_user.php', result.value, function () {
                $.get('includes/manage_users.php', function (data) {
                    $('#content-area').html(data);
                });
            });
        }
    });
});
</script>
