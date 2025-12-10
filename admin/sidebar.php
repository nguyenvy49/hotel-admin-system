<div class="w-64 bg-white border-r h-screen p-6 shadow-xl flex flex-col">

    <!-- Logo -->
    <h2 class="text-3xl font-extrabold mb-8 
               bg-gradient-to-r from-blue-600 to-indigo-500 
               bg-clip-text text-transparent tracking-wide">
        Prestige Admin
    </h2>

    <nav class="flex flex-col gap-2">

        <button class="sidebar-item group" data-page="home">
            <span class="text-xl">🏠</span>
            <span class="group-hover:translate-x-1 transition-all">Trang chủ</span>
        </button>

        <button class="sidebar-item group" data-page="customers">
            <span class="text-xl">👤</span>
            <span class="group-hover:translate-x-1 transition-all">Khách hàng</span>
        </button>

        <button class="sidebar-item group" data-page="nhanvien">
            <span class="text-xl">👥</span>
            <span class="group-hover:translate-x-1 transition-all">Nhân viên</span>
        </button>

        <button class="sidebar-item group" data-page="phong">
            <span class="text-xl">🏨</span>
            <span class="group-hover:translate-x-1 transition-all">Phòng</span>
        </button>

        <button class="sidebar-item group" data-page="datphong">
            <span class="text-xl">📌</span>
            <span class="group-hover:translate-x-1 transition-all">Đặt phòng</span>
        </button>

        <button class="sidebar-item group" data-page="dichvu">
            <span class="text-xl">🛎</span>
            <span class="group-hover:translate-x-1 transition-all">Dịch vụ</span>
        </button>

        <button class="sidebar-item group" data-page="hoadon">
            <span class="text-xl">💰</span>
            <span class="group-hover:translate-x-1 transition-all">Hóa đơn</span>
        </button>

        <button class="sidebar-item group" data-page="xuli">
            <span class="text-xl">⚙</span>
            <span class="group-hover:translate-x-1 transition-all">Xử lý</span>
        </button>

    </nav>
</div>
<style>
    .sidebar-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 500;
    color: #4b5563;
    background: transparent;
    cursor: pointer;
    transition: all .25s ease;
}

.sidebar-item:hover {
    background: #eef2ff;
    color: #1d4ed8;
    transform: translateX(4px);
}

.sidebar-item.active {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: white !important;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(59,130,246,0.3);
}

</style>