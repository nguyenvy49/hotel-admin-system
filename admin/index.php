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
async function loadPage(pageQuery) {

    // Hiệu ứng loading
    document.getElementById("mainContent").innerHTML = `
        <div class='loader-box space-y-4'>
            <div class='skeleton w-1/3'></div>
            <div class='skeleton w-1/2'></div>
            <div class='skeleton w-full h-20'></div>
        </div>
    `;

    // Render fragment
    const res = await fetch(`index.php?fragment=1&${pageQuery}`);
    const html = await res.text();
    document.getElementById("mainContent").innerHTML = html;

    // Lấy tên trang → lấy sau "page="
    const pageName = pageQuery.split("&")[0].replace("page=", "");

    // Active sidebar
    document.querySelectorAll(".sidebar-item").forEach(btn =>
        btn.classList.toggle("active", btn.dataset.page === pageName)
    );

    // Bắn event cho module (home.js, phong.js,…)
    document.dispatchEvent(new CustomEvent("pageLoaded", { 
        detail: { page: pageName } 
    }));
}

/* ============================================================
   GÁN CLICK SIDEBAR (ĐÃ FIX)
============================================================ */
document.querySelectorAll(".sidebar-item").forEach(btn => {
    btn.onclick = () => loadPage(`page=${btn.dataset.page}`);
});

/* ============================================================
   LOAD TRANG ĐẦU TIÊN (ĐÃ FIX)
============================================================ */
loadPage(`page=<?= $page ?>`);
</script>


<script src="../assets/js/home.js"></script>
<script src="../assets/js/khachhang.js"></script>
<script src="../assets/js/nhanvien.js"></script>
<script src="../assets/js/phong.js"></script>
<script src="../assets/js/loai_phong.js"></script>
<script src="../assets/js/dichvu.js"></script>
<script src="../assets/js/datphong.js"></script>
<script src="../assets/js/hoadon.js"></script>

</body>
</html>
