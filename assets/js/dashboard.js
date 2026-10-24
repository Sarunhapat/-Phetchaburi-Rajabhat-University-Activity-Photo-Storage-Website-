$(document).ready(function () {
    function loadPage(page) {
        $("#content-area").html('<p>กำลังโหลด...</p>');
        $.get("includes/" + page, function (data) {
            $("#content-area").html(data);
        });
    }

    // เริ่มต้นโหลดผู้ใช้
    loadPage("manage_users.php");

    // เมื่อคลิกเมนู
    $(".menu-link").click(function (e) {
        e.preventDefault();
        $(".menu-link").removeClass("active");
        $(this).addClass("active");

        const page = $(this).data("page");
        loadPage(page);
    });
});
