<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Prestige Manor Admin</title>

<!-- Tailwind CDN -->
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<style>
    body {
        background: #f1f4f9;
        font-family: "Inter", sans-serif;
        color: #1f2937;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
        width: 260px;
        background: #ffffff;
        border-right: 1px solid rgba(0,0,0,0.06);
        box-shadow: 4px 0 20px rgba(0,0,0,0.05);
        padding: 20px;
        position: sticky;
        top: 0;
        height: 100vh;
    }

    .sidebar-title {
        font-size: 24px;
        font-weight: 800;
        margin-bottom: 24px;
        background: linear-gradient(90deg, #1d4ed8, #2563eb);
        -webkit-background-clip: text;
        color: transparent;
    }

    .sidebar-item {
        padding: 12px 16px;
        border-radius: 12px;
        display: flex;
        gap: 12px;
        align-items: center;
        font-weight: 500;
        color: #4b5563;
        cursor: pointer;
        transition: 0.25s ease;
        margin-bottom: 6px;
    }

    .sidebar-item.active {
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        box-shadow: 0 4px 10px rgba(30,64,175,0.25);
        color: white !important;
        font-weight: 600;
    }

    .sidebar-item:hover {
        background: rgba(59,130,246,0.12);
        color: #1d4ed8;
        transform: translateX(4px);
    }

    /* ===== TOPBAR ===== */
    .topbar {
        background: white;
        padding: 16px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(0,0,0,0.06);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .title-main {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
    }

    .sub-title {
        font-size: 14px;
        color: #6b7280;
    }

    /* ===== CARDS ===== */
    .card {
        border-radius: 18px;
        padding: 24px;
        background: white;
        box-shadow: 0 6px 16px rgba(0,0,0,0.05);
        transition: 0.25s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 22px rgba(0,0,0,0.10);
    }

    /* ===== TABLE ===== */
    .table-beauty th {
        text-transform: uppercase;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.6px;
        color: #6b7280;
        background: #f3f4f6;
        padding: 12px;
    }

    .table-beauty tbody td {
        padding: 12px;
        font-size: 15px;
    }

    .table-beauty tbody tr {
        transition: 0.15s ease;
    }

    .table-beauty tbody tr:hover {
        background: #eef2ff;
    }

    /* BADGES */
    .badge {
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-success { background: #dcfce7; color: #16a34a; }
    .badge-warning { background: #fef9c3; color: #ca8a04; }
    .badge-danger  { background: #fee2e2; color: #dc2626; }
    .badge-info    { background: #dbeafe; color: #1d4ed8; }
</style>

</head>

<!-- TOAST POPUP Ở GIỮA MÀN HÌNH -->
<div id="toast"
     class="fixed inset-0 flex justify-center items-center pointer-events-none opacity-0 
            transition-all duration-300 z-[9999]">
    
    <div id="toastBox"
         class="bg-green-600 text-white text-xl font-semibold px-8 py-5 rounded-2xl shadow-2xl 
                scale-90 transition-all duration-300">
        Thông báo
    </div>

</div>

<script>
function showToast(message, type = "success") {
    const toast = document.getElementById("toast");
    const box = document.getElementById("toastBox");

    const colors = {
        success: "bg-green-600",
        error: "bg-red-600",
        warn: "bg-yellow-500"
    };

    // Set màu + nội dung
    box.className = `text-white text-xl font-semibold px-8 py-5 rounded-2xl shadow-2xl 
                     scale-90 transition-all duration-300 ${colors[type]}`;
    box.innerText = message;

    // Hiện popup
    toast.style.opacity = "1";
    box.style.transform = "scale(1)";

    // Ẩn sau 1.3 giây
    setTimeout(() => {
        toast.style.opacity = "0";
        box.style.transform = "scale(0.9)";
    }, 1300);
}
</script>

<body>
