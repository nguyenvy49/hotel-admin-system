<div class="p-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
                🛎️ Quản lý dịch vụ
            </h1>
            <p class="text-gray-500 mt-1">Danh sách các dịch vụ khách sạn đang cung cấp</p>
        </div>

        <a href="quanlidichvu/add_dichvu.php"
           class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl shadow-lg 
                  font-semibold hover:opacity-90 transition hover:shadow-xl">
            + Thêm dịch vụ
        </a>
    </div>

    <!-- SUB HEADER -->
    <h2 class="text-xl font-bold text-gray-700 mb-4">Danh sách dịch vụ</h2>

    <!-- SERVICE GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">

        <?php
        $q = $conn->query("SELECT * FROM dich_vu ORDER BY ma_dich_vu ASC");
        while ($r = $q->fetch_assoc()):
        ?>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-md p-6 
                    hover:shadow-xl hover:-translate-y-1 transition transform">

            <!-- TITLE -->
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-xl text-gray-800"><?= $r['ten_dich_vu'] ?></h3>
                    <p class="text-gray-500 mt-1">Mã DV: <?= $r['ma_dich_vu'] ?></p>
                </div>

                <div class="text-3xl">✨</div>
            </div>

            <!-- PRICE -->
            <p class="mt-4 text-2xl font-semibold text-indigo-600">
                <?= number_format($r['don_gia']) ?> đ
            </p>

            <!-- ACTIONS -->
            <div class="flex gap-4 mt-6">

                <a href="quanlidichvu/edit_dichvu.php?id=<?= $r['ma_dich_vu'] ?>"
                   class="flex items-center gap-1 px-4 py-2 bg-blue-500 text-white rounded-lg 
                          hover:bg-blue-600 transition shadow">
                    ✏️ <span>Sửa</span>
                </a>

                <a href="quanlidichvu/delete_dichvu.php?id=<?= $r['ma_dich_vu'] ?>"
                   onclick="return confirm('Bạn chắc chắn muốn xóa dịch vụ này?')"
                   class="flex items-center gap-1 px-4 py-2 bg-red-500 text-white rounded-lg 
                          hover:bg-red-600 transition shadow">
                    🗑️ <span>Xóa</span>
                </a>

            </div>

        </div>

        <?php endwhile; ?>

    </div>

</div>
