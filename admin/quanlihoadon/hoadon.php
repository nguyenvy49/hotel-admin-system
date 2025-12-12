<?php

    $keyword = $_GET["keyword"] ?? "";
$from    = $_GET["from"] ?? "";
$to      = $_GET["to"] ?? "";

$where = "WHERE 1";

// Tìm kiếm
if ($keyword !== "") {
    $kw = $conn->real_escape_string($keyword);
    $where .= " AND (ma_hoa_don LIKE '%$kw%' 
                OR ma_dat_phong LIKE '%$kw%' 
                OR tong_tien LIKE '%$kw%')";
}

// Lọc từ ngày
if ($from !== "") {
    $where .= " AND ngay_lap >= '$from'";
}

// Lọc đến ngày
if ($to !== "") {
    $where .= " AND ngay_lap <= '$to'";
}

$q = $conn->query("
    SELECT * FROM hoa_don
    $where
    ORDER BY ma_hoa_don DESC
");
?>
<div class="p-8">

    <!-- TIÊU ĐỀ LỚN -->
    <h1 class="text-4xl font-extrabold text-gray-900 mb-8 tracking-tight flex items-center gap-4">
        🧾 Quản lý hóa đơn
    </h1>

    <!-- FILTER -->
<!-- FILTER -->
        <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 mb-10 
                    grid grid-cols-1 md:grid-cols-4 gap-6">

            <!-- Tìm kiếm -->
            <div class="col-span-2">
                <label class="filter-label">Tìm kiếm</label>
                <input 
                    id="searchInput"
                    value="<?= $keyword ?>"
                    class="filter-input"
                    placeholder="Nhập mã HĐ, mã đặt phòng, tổng tiền..."
                >
            </div>

            <!-- Từ ngày -->
            <div>
                <label class="filter-label">Từ ngày</label>
                <input 
                    type="date"
                    id="filterFrom"
                    value="<?= $from ?>"
                    class="filter-input"
                >
            </div>

            <!-- Đến ngày -->
            <div>
                <label class="filter-label">Đến ngày</label>
                <input 
                    type="date"
                    id="filterTo"
                    value="<?= $to ?>"
                    class="filter-input"
                >
            </div>

        </div>


    <!-- BẢNG HÓA ĐƠN -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        <table class="min-w-full table-auto">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="th">Mã HĐ</th>
                    <th class="th">Mã đặt phòng</th>
                    <th class="th">Tổng tiền</th>
                    <th class="th">Trạng thái</th>
                    <th class="th">Ngày thanh toán</th>
                    <th class="th text-center">Hành động</th>
                </tr>
            </thead>

            <tbody class="text-gray-800">

            <?php
            $q = $conn->query("SELECT * FROM hoa_don ORDER BY ma_hoa_don DESC");
            while ($r = $q->fetch_assoc()):
            ?>

                <tr class="border-b hover:bg-indigo-50 transition">

                    <td class="td font-semibold">#<?= $r['ma_hoa_don'] ?></td>

                    <td class="td font-medium text-gray-700">
                        <?= $r['ma_dat_phong'] ?>
                    </td>

                    <td class="td font-bold text-indigo-600">
                        <?= number_format($r['tong_tien']) ?> đ
                    </td>

                    <td class="td">
                        <?php
                        $isPaid = $r['trang_thai'] === "Đã thanh toán";
                        $color = $isPaid 
                            ? "bg-green-100 text-green-700"
                            : "bg-yellow-100 text-yellow-700";
                        ?>
                        <span class="badge <?= $color ?>">
                            <?= $r['trang_thai'] ?>
                        </span>
                    </td>

                    <td class="td">
                        <?= $r['ngay_thanh_toan'] ?: "<span class='text-gray-400'>—</span>" ?>
                    </td>

                    <td class="td text-center">
                        <div class="flex justify-center gap-5">

                            <!-- Xem -->
                            <button onclick="viewInvoice(<?= $r['ma_hoa_don'] ?>)"
                                class="action-btn text-indigo-600 hover:text-indigo-800"
                                title="Xem hóa đơn">
                                <i class="fa-solid fa-eye text-lg"></i>
                            </button>

                            <!-- In -->
                            <button onclick="printInvoice(<?= $r['ma_hoa_don'] ?>)"
                                class="action-btn text-green-600 hover:text-green-800"
                                title="In hóa đơn">
                                <i class="fa-solid fa-print text-lg"></i>
                            </button>

                        </div>
                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>
    </div>

</div>

<!-- CSS TỐI ƯU GIAO DIỆN -->
<style>
.filter-label {
    font-weight: 600;
    color: #374151;
}
.filter-input {
    width: 100%;
    padding: 11px 14px;
    margin-top: 4px;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    transition: .2s;
    background: #fafafa;
}
.filter-input:focus {
    border-color: #6366f1;
    background: white;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .25);
}

.th {
    padding: 14px 16px;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    color: #6b7280;
}
.td {
    padding: 14px 16px;
    vertical-align: middle;
}

.badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.action-btn {
    padding: 9px;
    border-radius: 12px;
    transition: 0.2s;
}
.action-btn:hover {
    background: rgba(99,102,241,0.08);
}

.filter-label {
    font-weight: 600;
    color: #374151;
}

.filter-input {
    width: 100%;
    padding: 11px 14px;
    margin-top: 4px;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    background: #fafafa;
    transition: .2s;
}

.filter-input:focus {
    border-color: #6366f1;
    background: white;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .25);
}


</style>

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>


      
<script>
    document.getElementById("searchInput").oninput = applyFilter;
document.getElementById("filterFrom").onchange = applyFilter;
document.getElementById("filterTo").onchange = applyFilter;

function applyFilter() {
    let kw = document.getElementById("searchInput").value;
    let f  = document.getElementById("filterFrom").value;
    let t  = document.getElementById("filterTo").value;

    loadPage(`page=hoadon&keyword=${kw}&from=${f}&to=${t}`);
}

</script>