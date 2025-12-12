/* ========================================
    MODAL KHỞI TẠO
======================================== */
function showModal(html) {
    let old = document.getElementById("modal");
    if (old) old.remove();

    let div = document.createElement("div");
    div.id = "modal";
    div.className =
        "fixed inset-0 bg-black/40 flex justify-center items-center z-[9999]";

    div.innerHTML = `
        <div class="bg-white rounded-3xl p-8 w-[700px] max-h-[85vh] overflow-auto shadow-xl">
            ${html}
            <div class="text-right mt-6">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Đóng</button>
            </div>
        </div>
    `;

    document.body.appendChild(div);
}

function closeModal() {
    let m = document.getElementById("modal");
    if (m) m.remove();
}

/* ========================================
    XEM CHI TIẾT HÓA ĐƠN
======================================== */
async function viewInvoice(id) {
    let res = await fetch(`quanlihoadon/view_hoadon.php?id=${id}`);
    let js  = await res.json();

    if (!js.status) return alert(js.msg);
    showModal(js.html);
}

/* ========================================
    IN HÓA ĐƠN
======================================== */
async function printInvoice(id) {
    let res = await fetch(`quanlihoadon/view_hoadon.php?id=${id}`);
    let js  = await res.json();

    if (!js.status) return alert(js.msg);

    // mở modal print
    let printWindow = window.open("", "_blank", "width=800,height=900");

    printWindow.document.write(`
        <html>
        <head>
            <title>In hóa đơn</title>
            <style>
                body { font-family: Arial; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                th, td { border-bottom: 1px solid #ddd; padding: 8px; }
                th { background: #eee; }
            </style>
        </head>
        <body>
            ${js.print}
            <script>window.onload = () => window.print();<\/script>
        </body>
        </html>
    `);

    printWindow.document.close();
}

