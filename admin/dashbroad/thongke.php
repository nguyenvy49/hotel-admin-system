<div class="p-8">

    <!-- TIÊU ĐỀ -->
    <div class="mb-8">
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Tổng quan hệ thống</h1>
        <p class="text-gray-500 mt-1">Bảng điều khiển tổng hợp hoạt động khách sạn Prestige Manor</p>
    </div>

    <!-- 4 THẺ THỐNG KÊ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

        <!-- KHÁCH HÀNG -->
        <div class="p-7 bg-white shadow-xl rounded-2xl border border-gray-100 hover:shadow-2xl transition duration-200">
            <div class="flex justify-between items-center">
                <p class="text-gray-700 text-lg font-semibold">Khách hàng</p>
                <span class="text-blue-600 text-4xl">👤</span>
            </div>
            <h2 class="text-5xl font-extrabold text-blue-600 mt-3"><?= $stats['kh'] ?></h2>
        </div>

        <!-- PHÒNG ĐÃ ĐẶT -->
        <div class="p-7 bg-white shadow-xl rounded-2xl border border-gray-100 hover:shadow-2xl transition duration-200">
            <div class="flex justify-between items-center">
                <p class="text-gray-700 text-lg font-semibold">Phòng đang sử dụng</p>
                <span class="text-yellow-500 text-4xl">🏨</span>
            </div>
            <h2 class="text-5xl font-extrabold text-yellow-600 mt-3"><?= $stats['phong_dang_dat'] ?></h2>
        </div>

        <!-- DOANH THU -->
        <div class="p-7 bg-white shadow-xl rounded-2xl border border-gray-100 hover:shadow-2xl transition duration-200">
            <div class="flex justify-between items-center">
                <p class="text-gray-700 text-lg font-semibold">Doanh thu tháng</p>
                <span class="text-green-600 text-4xl">💰</span>
            </div>
            <h2 class="text-4xl font-extrabold text-green-600 mt-3">
                <?= number_format($stats['dt_thang'],0,',','.') ?> đ
            </h2>
        </div>

        <!-- DỊCH VỤ -->
        <div class="p-7 bg-white shadow-xl rounded-2xl border border-gray-100 hover:shadow-2xl transition duration-200">
            <div class="flex justify-between items-center">
                <p class="text-gray-700 text-lg font-semibold">Dịch vụ sử dụng</p>
                <span class="text-red-600 text-4xl">🛎️</span>
            </div>
            <h2 class="text-5xl font-extrabold text-red-600 mt-3"><?= $stats['dv'] ?></h2>
        </div>

    </div>

    <!-- KHU VỰC THỐNG KÊ PHỤ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-10">

        <!-- TRẠNG THÁI PHÒNG -->
        <div class="bg-white rounded-2xl shadow-xl p-7 border border-gray-100">
            <h2 class="text-xl font-bold mb-5 text-gray-800">Tình trạng phòng</h2>

            <div class="space-y-4 text-gray-700">

                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-green-500 shadow"></span>
                        Trống
                    </span>
                    <span class="font-bold text-lg"><?= $stats['phong_trong'] ?></span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-yellow-500 shadow"></span>
                        Đã đặt
                    </span>
                    <span class="font-bold text-lg"><?= $stats['phong_dang_dat'] ?></span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-blue-500 shadow"></span>
                        Đang dọn dẹp
                    </span>
                    <span class="font-bold text-lg"><?= $stats['phong_trang_thai']['Đang dọn dẹp'] ?></span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-red-500 shadow"></span>
                        Bảo trì
                    </span>
                    <span class="font-bold text-lg"><?= $stats['phong_bao_tri'] ?></span>
                </div>

            </div>
        </div>

        <!-- BIỂU ĐỒ DOANH THU -->
        <div class="col-span-2 bg-white rounded-2xl shadow-xl p-7 border border-gray-100">
            <h2 class="text-xl font-bold mb-5 text-gray-800">Doanh thu 6 tháng gần nhất</h2>

            <div class="relative h-64">
                <canvas id="revenueChart"></canvas>
            </div>

            <script>
                (function() {
                    const labels = [
                        <?php foreach ($stats['doanh_thu'] as $d) echo "'T".$d['thang']."'," ?>
                    ];
                    const data = [
                        <?php foreach ($stats['doanh_thu'] as $d) echo $d['total'] . "," ?>
                    ];

                    function draw() {
                        if (typeof Chart === "undefined") {
                            setTimeout(draw, 150);
                            return;
                        }

                        const ctx = document.getElementById('revenueChart').getContext('2d');

                        new Chart(ctx, {
                            type: "line",
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: "Doanh thu",
                                    data: data,
                                    borderColor: "#2563eb",
                                    backgroundColor: "rgba(37,99,235,0.20)",
                                    borderWidth: 3,
                                    tension: 0.4,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } }
                            }
                        });
                    }

                    draw();
                })();
            </script>

        </div>

    </div>

</div>
