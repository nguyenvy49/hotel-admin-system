<?php
/* ================================
    INPUT FILTER DATE
================================ */
$checkin  = $_GET["checkin"]  ?? date("Y-m-d");
$checkout = $_GET["checkout"] ?? date("Y-m-d");

$isFilter = isset($_GET["checkin"]) && isset($_GET["checkout"]);

/* ================================
   LỌC PHÒNG TRỐNG THEO CTDP
================================ */

$conditionEmpty = "";
if ($isFilter) {
    $conditionEmpty = "
        WHERE NOT EXISTS (
            SELECT 1
            FROM chi_tiet_dat_phong ctdp
            JOIN dat_phong dp ON dp.ma_dat_phong = ctdp.ma_dat_phong
            WHERE ctdp.ma_phong = p.ma_phong
            AND dp.ngay_nhan <= '$checkout'
            AND dp.ngay_tra   >= '$checkin'
            AND dp.trang_thai IN ('Đã đặt','Đang ở')
        )
    ";
}

/* ================================
   LẤY TRẠNG THÁI PHÒNG
================================ */
/* ================================
   LẤY TRẠNG THÁI PHÒNG CHÍNH XÁC
================================ */
$sql = "
SELECT 
    p.*, 
    lp.ten_loai_phong,
    lp.gia_phong,

    (
        SELECT 
            CASE
                /* 1) Khách đã check-in */
                WHEN EXISTS (
                    SELECT 1 
                    FROM chi_tiet_dat_phong ctdp 
                    JOIN dat_phong dp ON dp.ma_dat_phong = ctdp.ma_dat_phong
                    WHERE ctdp.ma_phong = p.ma_phong
                    AND dp.trang_thai = 'Đang ở'
                ) THEN 'Đang ở'

                /* 2) Lễ tân đã xác nhận nhưng khách chưa đến */
                WHEN EXISTS (
                    SELECT 1 
                    FROM chi_tiet_dat_phong ctdp 
                    JOIN dat_phong dp ON dp.ma_dat_phong = ctdp.ma_dat_phong
                    WHERE ctdp.ma_phong = p.ma_phong
                    AND dp.trang_thai = 'Chờ nhận phòng'
                ) THEN 'Chờ nhận'

                /* 3) Booking online – chưa xác nhận */
                WHEN EXISTS (
                    SELECT 1 
                    FROM chi_tiet_dat_phong ctdp 
                    JOIN dat_phong dp ON dp.ma_dat_phong = ctdp.ma_dat_phong
                    WHERE ctdp.ma_phong = p.ma_phong
                    AND dp.trang_thai = 'Đã đặt'
                ) THEN 'Đã đặt'

                ELSE 'Trống'
            END
    ) AS tinh_trang

FROM phong p
JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong
$conditionEmpty
ORDER BY p.so_phong ASC
";


$q = $conn->query($sql);
?>

<div class="p-8">

    <!-- TIÊU ĐỀ -->
    <h1 class="text-4xl font-extrabold text-gray-900 mb-10 tracking-tight flex items-center gap-4">
        🏨 <span>Quản lý phòng</span>
    </h1>

    <!-- FILTER -->
    <div class="bg-white p-7 rounded-3xl shadow-lg mb-10 flex flex-wrap gap-8 items-end border border-gray-100">

        <div>
            <label class="font-semibold text-gray-700">Ngày Check-in</label>
            <input type="date" id="filter_checkin" class="input mt-1"
                   value="<?= $checkin ?>">
        </div>

        <div>
            <label class="font-semibold text-gray-700">Ngày Check-out</label>
            <input type="date" id="filter_checkout" class="input mt-1"
                   value="<?= $checkout ?>">
        </div>

        <button id="btnFilter" 
            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold 
                   rounded-xl shadow-md hover:shadow-xl hover:scale-[1.02] transition">
            🔍 Lọc phòng
        </button>
    </div>

    <!-- DANH SÁCH PHÒNG -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-8">

    <?php while ($r = $q->fetch_assoc()): ?>

        <?php
            $status = $r["tinh_trang"];
            $isEmpty = $status === "Trống";

            $badge = [
                "Trống"   => "bg-green-100 text-green-700",
                "Đang ở"  => "bg-blue-100 text-blue-600",
                "Chờ nhận"   => "bg-purple-100 text-purple-700",
                "Đã đặt"  => "bg-yellow-100 text-yellow-700"
            ][$status];
        ?>

        <!-- CARD PHÒNG -->
        <div 
            class="room-card relative group bg-white border-2 rounded-3xl p-7 shadow-md hover:shadow-2xl 
                   hover:-translate-y-2 transition-all cursor-pointer border-gray-200"
            data-id="<?= $r['ma_phong'] ?>"
            data-empty="<?= $isEmpty ? 1 : 0 ?>"
        >

            <!-- Badge chọn -->
            <?php if ($isEmpty): ?>
            <div class="select-badge hidden absolute top-4 right-4 w-8 h-8 bg-indigo-600 
                        text-white rounded-full flex items-center justify-center shadow-md">
                ✓
            </div>
            <?php endif; ?>

            <div class="mb-5">
                <h2 class="text-2xl font-bold text-gray-900">Phòng <?= $r['so_phong'] ?></h2>
                <p class="text-gray-500"><?= $r['ten_loai_phong'] ?></p>
            </div>

            <p class="text-xl font-extrabold text-indigo-600 mb-4">
                <?= number_format($r['gia_phong']) ?> đ / đêm
            </p>

            <span class="px-4 py-2 rounded-full text-sm font-semibold <?= $badge ?>">
                <?= $status ?>
            </span>
        </div>

    <?php endwhile; ?>
    </div>

</div>

<!-- BUTTON ĐẶT NHIỀU PHÒNG -->
<button id="btnBooking" 
        class="hidden fixed bottom-10 right-10 px-7 py-4 bg-indigo-600 text-white text-lg 
               font-bold rounded-2xl shadow-2xl hover:bg-indigo-700 hover:shadow-3xl 
               hover:scale-[1.03] transition-all">
    Đặt <span id="countSelected">0</span> phòng
</button>

<!-- MODAL XÁC NHẬN -->
<div id="modalMultiBook"
     class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm z-50 flex justify-center items-center">

    <div class="bg-white w-[480px] p-8 rounded-3xl shadow-2xl animate-fadeIn">

        <h2 class="text-3xl font-bold mb-6 text-gray-900">📦 Xác nhận đặt phòng</h2>

        <p class="text-gray-600 mb-3">Các phòng đã chọn:</p>
        <ul id="selectedRoomsList" class="list-disc ml-5 mb-6 text-gray-800"></ul>

        <form id="formMultiBook" class="space-y-5">

            <input type="hidden" name="rooms" id="selectedRoomsInput">

            <div>
                <label class="font-semibold">Họ tên khách</label>
                <input name="ten_khach" class="input mt-1" required>
            </div>

            <div>
                <label class="font-semibold">SĐT</label>
                <input name="sdt" class="input mt-1" required>
            </div>

            <div>
                <label class="font-semibold">Ngày nhận</label>
                <input type="date" name="ngay_nhan" class="input mt-1" required>
            </div>

            <div>
                <label class="font-semibold">Ngày trả</label>
                <input type="date" name="ngay_tra" class="input mt-1" required>
            </div>

            <p id="multiMsg" class="text-red-600"></p>

            <div class="flex justify-end gap-4 pt-4">
                <button type="button" onclick="closeMultiBook()" 
                        class="px-5 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition">
                    Hủy
                </button>

                <button class="px-6 py-2 bg-indigo-600 text-white rounded-xl font-semibold 
                               hover:bg-indigo-700 transition">
                    Xác nhận
                </button>
            </div>

        </form>

    </div>
</div>

<!-- CSS -->
<style>
.input {
    width: 100%;
    padding: 12px 14px;
    border-radius: 14px;
    border: 2px solid #e5e7eb;
    background: #fafafa;
    transition: .2s;
}
.input:focus {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .25);
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn .25s ease-out; }
</style>
