<?php
include('../includes/db_connect.php');
$id = intval($_GET['id']);
$sql = "SELECT id, firstname, lastname, email, created_at FROM users WHERE id = $id LIMIT 1";
$res = $conn->query($sql);
if($res && $row = $res->fetch_assoc()):
  $fullname = trim($row['firstname'].' '.$row['lastname']);
?>
<ul class="list-group">
  <li class="list-group-item"><strong>Họ tên:</strong> <?= htmlspecialchars($fullname) ?></li>
  <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></li>
  <li class="list-group-item"><strong>Ngày tạo:</strong> <?= date("d/m/Y H:i", strtotime($row['created_at'])) ?></li>
  <li class="list-group-item"><strong>Số lần đặt (demo):</strong> 0</li>
  <li class="list-group-item"><strong>Tổng chi tiêu (demo):</strong> 0.00</li>
  <li class="list-group-item text-muted">Phản hồi và lịch sử đặt phòng sẽ hiển thị khi có dữ liệu.</li>
</ul>
<?php else: ?>
<p class="text-danger">Không tìm thấy khách hàng.</p>
<?php endif; ?>
