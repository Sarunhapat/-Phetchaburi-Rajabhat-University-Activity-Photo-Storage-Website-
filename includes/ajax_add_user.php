<script>
$('#add-user-form').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
        url: 'includes/ajax_add_user.php',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                Swal.fire('เพิ่มผู้ใช้สำเร็จ!', '', 'success');
                // โหลดหน้าใหม่
                $.get('includes/manage_users.php', function (html) {
                    $('#content-area').html(html);
                });
            } else {
                Swal.fire('ผิดพลาด', res.message, 'error');
            }
        }
    });
});
</script>
