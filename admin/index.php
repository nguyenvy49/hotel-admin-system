<?php
include "config.php";
include "helpers.php";
include "controllers/controller.php";

// Lấy page mặc định
$page = $_GET["page"] ?? "home";

// Nếu là fragment → chỉ trả về nội dung HTML của view
if (isset($_GET["fragment"])) {
    load_view($page, $conn);
    exit;
}

// Nếu không phải fragment → render layout đầy đủ
include "layout_header.php";
?>

<style>
/* Animation loading */
.loader-box {
    animation: fadeIn .25s ease-in-out;
}

.skeleton {
    height: 18px;
    background: linear-gradient(90deg, #eee, #f5f5f5, #eee);
    background-size: 200% 100%;
    animation: shimmer 1.2s infinite;
    border-radius: 6px;
}

@keyframes shimmer {
    from { background-position: 200% 0; }
    to { background-position: -200% 0; }
}


/* Hover đẹp hơn */
.sidebar-item {
    transition: .22s ease;
}
.sidebar-item:hover {
    background: #eff6ff;
    color: #1d4ed8;
    transform: translateX(6px);
}
</style>

<div class="flex min-h-screen bg-gray-50">

    <!-- Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Main Content -->
    <main id="mainContent" class="flex-1 p-8 overflow-auto">

        <!-- Loading đẹp -->
        <div class="loader-box space-y-4">
            <div class="skeleton w-1/3"></div>
            <div class="skeleton w-1/2"></div>
            <div class="skeleton w-full h-20"></div>
        </div>

    </main>
</div>

<script>
async function loadPage(page) {

    // Effect loading nhanh
    document.getElementById("mainContent").innerHTML = `
        <div class='loader-box space-y-4'>
            <div class='skeleton w-1/3'></div>
            <div class='skeleton w-1/2'></div>
            <div class='skeleton w-full h-20'></div>
        </div>
    `;

    const res = await fetch(`index.php?fragment=1&page=${page}`);
    const html = await res.text();
    document.getElementById("mainContent").innerHTML = html;

    // Active sidebar button
    document.querySelectorAll(".sidebar-item").forEach(btn =>
        btn.classList.toggle("active", btn.dataset.page === page)
    );
}

// Gán sự kiện click
document.querySelectorAll(".sidebar-item").forEach(btn => {
    btn.onclick = () => loadPage(btn.dataset.page);
});

// Load trang đầu tiên
loadPage("<?= $page ?>");
</script>


</body>
</html>
