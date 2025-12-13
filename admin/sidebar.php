<div class="w-72 bg-white/80 backdrop-blur-xl border-r min-h-screen p-7 shadow-2xl flex flex-col">

    <!-- LOGO -->
    <div class="flex items-center gap-3 mb-10">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 
                    shadow-lg flex items-center justify-center text-white text-2xl font-bold">
            P
        </div>
        <h2 class="text-3xl font-extrabold bg-gradient-to-r from-blue-600 to-indigo-500 
                   bg-clip-text text-transparent tracking-wide">
            Prestige
        </h2>
    </div>

    <!-- NAVIGATION -->
    <nav class="flex flex-col gap-2">

        <button class="sidebar-item" data-page="home">
            <i class="ri-home-5-line text-2xl"></i>
            <span>Trang chủ</span>
        </button>

        <button class="sidebar-item" data-page="customers">
            <i class="ri-user-line text-2xl"></i>
            <span>Khách hàng</span>
        </button>

        <button class="sidebar-item" data-page="nhanvien">
            <i class="ri-team-line text-2xl"></i>
            <span>Nhân viên</span>
        </button>

        <button class="sidebar-item" data-page="phong">
            <i class="ri-hotel-line text-2xl"></i>
            <span>Phòng</span>
        </button>

        <button class="sidebar-item" data-page="datphong">
            <i class="ri-calendar-check-line text-2xl"></i>
            <span>Đặt phòng</span>
        </button>

        <button class="sidebar-item" data-page="dichvu">
            <i class="ri-service-line text-2xl"></i>
            <span>Dịch vụ</span>
        </button>

        <button class="sidebar-item" data-page="hoadon">
            <i class="ri-bill-line text-2xl"></i>
            <span>Hóa đơn</span>
        </button>

        <button class="sidebar-item" data-page="xuli">
            <i class="ri-settings-3-line text-2xl"></i>
            <span>Xử lý</span>
        </button>

                <button class="sidebar-item" data-page="thongke">
            <i class="ri-settings-3-line text-2xl"></i>
            <span>Thống kê</span>
        </button>

    </nav>
</div>

<!-- ICON LIBRARY -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

<style>
/* ========== SIDEBAR STYLES ========== */

.sidebar-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    border-radius: 14px;
    font-size: 16px;
    font-weight: 500;
    color: #4b5563;
    cursor: pointer;
    transition: all .25s ease;
    letter-spacing: 0.2px;
}

.sidebar-item:hover {
    background: #eef2ff;
    color: #1e40af;
    transform: translateX(6px);
}

.sidebar-item i {
    transition: all .25s ease;
}

.sidebar-item:hover i {
    transform: scale(1.15);
}

/* ACTIVE ITEM */
.sidebar-item.active {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: #fff !important;
    font-weight: 600;
    box-shadow: 0 6px 16px rgba(59,130,246,0.35);
    transform: translateX(6px);
}

.sidebar-item.active i {
    transform: scale(1.2);
}

/* Smooth shadow for sidebar */
.w-72 {
    box-shadow: 6px 0 20px rgba(0,0,0,0.05);
}
</style>
