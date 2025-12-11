<?php

/* ==========================
    INPUT FILTER DATE
========================== */
$checkin  = $_GET["checkin"]  ?? date("Y-m-d");
$checkout = $_GET["checkout"] ?? date("Y-m-d");

/* ==========================
    BUILD WHERE FOR FILTER
========================== */
$whereEmpty = "";
$isFilter = isset($_GET["checkin"]) && isset($_GET["checkout"]);

/* Nếu user bấm Lọc → chỉ hiện phòng TRỐNG */
if ($isFilter) {
    $whereEmpty = "
        WHERE NOT EXISTS (
            SELECT 1 FROM dat_phong dp
            WHERE dp.ma_phong = p.ma_phong
            AND (
                dp.ngay_nhan <= '$checkout'
                AND dp.ngay_tra   >= '$checkin'
            )
        )
    ";
}

/* ==========================
    SQL LẤY TẤT CẢ PHÒNG + TRẠNG THÁI THEO NGÀY
========================== */

$sql = "
SELECT 
    p.*, 
    lp.ten_loai_phong,
    lp.gia_phong,

    (
        SELECT 
            CASE
                WHEN EXISTS (
                    SELECT 1 FROM dat_phong dp
                    WHERE dp.ma_phong = p.ma_phong
                    AND '$checkin' BETWEEN dp.ngay_nhan AND dp.ngay_tra
                    AND dp.trang_thai = 'Đang ở'
                ) THEN 'Đang ở'

                WHEN EXISTS (
                    SELECT 1 FROM dat_phong dp
                    WHERE dp.ma_phong = p.ma_phong
                    AND dp.ngay_nhan >= '$checkin'
                    AND dp.ngay_nhan <= '$checkout'
                    AND dp.trang_thai = 'Đã đặt'
                ) THEN 'Đã đặt'

                ELSE 'Trống'
            END
    ) AS tinh_trang

FROM phong p
JOIN loai_phong lp ON p.ma_loai_phong = lp.ma_loai_phong

$whereEmpty

ORDER BY p.so_phong ASC
";

$q = $conn->query($sql);
?>


<div class="p-8">

    <h1 class="text-4xl font-extrabold text-gray-800 mb-10 flex items-center gap-4">
        🏨 <span class="tracking-wide">Quản lý phòng</span>
    </h1>

    <!-- ==========================
            BỘ LỌC CHECKIN / OUT
    =========================== -->
    <div class="bg-white p-6 rounded-2xl shadow mb-10 flex flex-wrap gap-6 items-end">

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
            class="px-5 py-3 bg-indigo-600 text-white font-semibold rounded-xl 
                   hover:bg-indigo-700 active:scale-95 transition shadow">
            🔍 Lọc phòng
        </button>
    </div>


    <!-- ==========================
            DANH SÁCH PHÒNG
    =========================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

    <?php while ($r = $q->fetch_assoc()): ?>

        <?php
            $status = $r['tinh_trang'];

            $isEmpty     = ($status === "Trống");
            $isStaying   = ($status === "Đang ở");
            $isReserved  = ($status === "Đã đặt");

            if ($isEmpty) {
                $badge = "bg-green-100 text-green-700 border-green-300";
                $icon  = "🟢";
            } elseif ($isStaying) {
                $badge = "bg-blue-100 text-blue-700 border-blue-300";
                $icon  = "🔵";
            } else {
                $badge = "bg-yellow-100 text-yellow-700 border-yellow-300";
                $icon  = "🟠";
            }
        ?>

        <!-- HỘP PHÒNG -->
        <div class="group bg-white rounded-3xl border border-gray-200 shadow-md 
                    hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 p-7">

            <!-- HEADER -->
            <div class="flex justify-between items-start mb-5">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        Phòng <?= $r['so_phong'] ?>
                    </h2>
                    <p class="text-gray-500 mt-1 text-lg">
                        <?= $r['ten_loai_phong'] ?>
                    </p>
                </div>

                <div class="text-5xl opacity-80 group-hover:opacity-100 transition-all">
                    🛏️
                </div>
            </div>

            <!-- PRICE -->
            <p class="text-xl font-medium text-gray-600 mb-3">Giá phòng:</p>
            <p class="text-3xl font-extrabold text-indigo-600 tracking-tight">
                <?= number_format($r['gia_phong']) ?> đ
            </p>

            <!-- STATUS -->
            <div class="mt-5">
                <span class="px-5 py-2.5 rounded-full text-sm font-semibold border <?= $badge ?>">
                    <?= $icon ?> <?= $status ?>
                </span>
            </div>

            <!-- BUTTONS -->
            <div class="flex gap-4 mt-7">

                <?php if ($isStaying): ?>
                    <button onclick="openAddServiceForRoom(<?= $r['ma_phong'] ?>)"
                        class="px-5 py-3 bg-blue-500 text-white font-semibold rounded-xl 
                               hover:bg-blue-600 active:scale-95 transition-all shadow-sm">
                        ➕ Thêm dịch vụ
                    </button>

                    <button onclick="openCheckout(<?= $r['ma_phong'] ?>)"
                        class="px-5 py-3 bg-green-600 text-white font-semibold rounded-xl 
                               hover:bg-green-700 active:scale-95 transition-all shadow-sm">
                        💸 Trả phòng
                    </button>

                <?php elseif ($isEmpty): ?>

                    <button onclick="openBookRoom(<?= $r['ma_phong'] ?>)"
                        class="px-5 py-3 bg-indigo-600 text-white font-semibold rounded-xl 
                               hover:bg-indigo-700 active:scale-95 transition-all shadow-sm">
                        📝 Đặt phòng
                    </button>

                <?php elseif ($isReserved): ?>

                    <button class="px-5 py-3 bg-gray-300 text-gray-600 rounded-xl cursor-not-allowed">
                        ⏳ Phòng đã được đặt
                    </button>

                <?php endif; ?>

            </div>

        </div>

    <?php endwhile; ?>
    </div>

</div>


<!-- ================================================
                MODAL ĐẶT PHÒNG
================================================ -->
<div id="modalBookRoom"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden z-50 flex justify-center items-center">

    <div class="bg-white p-8 rounded-3xl w-[500px] shadow-2xl animate-fadeIn">

        <h2 class="text-3xl font-bold text-gray-800 mb-8">📝 Đặt phòng trực tiếp</h2>

        <form id="formBookRoom" class="space-y-6">

            <input type="hidden" name="ma_phong" id="book_room_id">

            <div>
                <label class="font-semibold text-gray-700">Họ tên khách</label>
                <input name="ten_khach" class="input mt-1" required>
            </div>

            <div>
                <label class="font-semibold text-gray-700">Số điện thoại</label>
                <input name="sdt" class="input mt-1" required>
            </div>

            <div>
                <label class="font-semibold text-gray-700">Ngày nhận phòng</label>
                <input type="date" name="ngay_nhan" class="input mt-1" required>
            </div>

            <div>
                <label class="font-semibold text-gray-700">Ngày trả dự kiến</label>
                <input type="date" name="ngay_tra" class="input mt-1" required>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button onclick="closeBookRoom()" type="button" class="btn-gray">Hủy</button>
                <button type="submit" class="btn-blue">Xác nhận</button>
            </div>

            <p id="bookRoomMsg" class="text-red-600 text-sm"></p>
        </form>

    </div>
</div>


<!-- ================================================
                MODAL THÊM DỊCH VỤ
================================================ -->
<div id="modalAddServiceRoom"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden z-50 flex justify-center items-center">

    <div class="bg-white p-8 rounded-3xl w-[500px] shadow-2xl animate-fadeIn">

        <h2 class="text-2xl font-bold text-gray-800 mb-5">➕ Thêm dịch vụ phòng</h2>

        <form id="formAddServiceRoom">

            <input type="hidden" name="ma_phong" id="service_room_id">

            <div id="serviceList" class="space-y-4">
                <div class="service-row flex items-center gap-3">
                    <select name="ma_dich_vu[]" class="input service-select"></select>
                    <input type="number" name="so_luong[]" class="input w-24" min="1" value="1">
                    <button type="button" class="btn-red px-3 remove-row">✖</button>
                </div>
            </div>

            <button type="button" onclick="addServiceRow()"
                class="mt-4 px-4 py-2 bg-blue-100 text-blue-700 font-semibold rounded-xl hover:bg-blue-200">
                + Thêm dịch vụ
            </button>

            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeAddServiceRoom()" type="button" class="btn-gray">Hủy</button>
                <button type="submit" class="btn-blue">Lưu</button>
            </div>

            <p id="serviceRoomMsg" class="text-red-600 text-sm mt-2"></p>

        </form>

    </div>
</div>


<!-- ================================================
                MODAL CHECKOUT
================================================ -->
<div id="modalCheckout"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden z-50 flex justify-center items-center">

    <div class="bg-white p-8 rounded-3xl w-[520px] shadow-2xl animate-fadeIn">

        <h2 class="text-3xl font-bold text-gray-800 mb-6">💸 Xác nhận trả phòng</h2>

        <div id="checkoutContent"></div>

        <div class="flex justify-end gap-3 mt-8">
            <button onclick="closeCheckout()" class="btn-gray">Hủy</button>
            <button onclick="confirmCheckout()" class="btn-green">Xác nhận</button>
        </div>

    </div>
</div>


<!-- STYLE -->
<style>
.input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    background: #fafafa;
    border-radius: 14px;
    font-size: 15px;
    transition: .2s;
}
.input:focus {
    background: white;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .25);
}

.btn-gray, .btn-blue, .btn-green, .btn-red {
    padding: 10px 18px;
    border-radius: 12px;
    font-weight: 600;
    transition: .2s;
}
.btn-gray { background:#e5e7eb; }
.btn-gray:hover { background:#d6d6d6; }

.btn-blue { background:#2563eb; color:white; }
.btn-blue:hover { background:#1e4ed8; }

.btn-green { background:#10b981; color:white; }
.btn-green:hover { background:#059669; }

.btn-red { background:#ef4444; color:white; }
.btn-red:hover { background:#dc2626; }

@keyframes fadeIn { 
    from { opacity:0; transform:translateY(10px); }
    to { opacity:1; transform:translateY(0); }
}
.animate-fadeIn { animation: fadeIn .25s ease-out; }
</style>



