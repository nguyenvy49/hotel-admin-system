<?php
include('../includes/db_connect.php');
include('../includes/header.php');
?>

<div class="card p-4">
  <h3 class="mb-3 fw-bold">👥 Quản lý khách hàng</h3>

  <div class="d-flex justify-content-between mb-3">
    <div>
      <button id="filterRecent" class="btn btn-outline-primary">Khách mới (7 ngày)</button>
      <button id="resetFilter" class="btn btn-outline-secondary ms-2">Hiển thị tất cả</button>
    </div>
    <div style="width:320px;">
      <input type="text" id="searchBox" class="form-control" placeholder="Tìm theo tên hoặc email...">
    </div>
  </div>

  <div class="table-responsive">
    <table id="customerTable" class="table table-hover align-middle">
      <thead>
        <tr class="text-center">
          <th>ID</th>
          <th>Họ tên</th>
          <th>Email</th>
          <th>Ngày tạo</th>
          <th>Trạng thái</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT id, firstname, lastname, email, created_at FROM users ORDER BY created_at DESC";
        $res = $conn->query($sql);
        while($r = $res->fetch_assoc()):
          $fullname = trim($r['firstname'].' '.$r['lastname']);
          // Nếu chưa có cột status trong table users, mặc định Active
          $status = isset($r['status']) ? $r['status'] : 'Active';
        ?>
        <tr>
          <td class="text-center"><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($fullname) ?></td>
          <td><?= htmlspecialchars($r['email']) ?></td>
          <td class="text-center"><?= date("d/m/Y H:i", strtotime($r['created_at'])) ?></td>
          <td class="text-center">
            <span class="badge <?= $status == 'Active' ? 'badge-active' : 'badge-locked' ?>"><?= $status ?></span>
          </td>
          <td class="text-center">
            <button class="btn btn-sm btn-info view-btn" data-id="<?= $r['id'] ?>">Xem</button>
            <button class="btn btn-sm btn-warning reset-btn" data-id="<?= $r['id'] ?>">Reset MK</button>
            <button class="btn btn-sm <?= $status=='Active' ? 'btn-danger' : 'btn-success' ?> lock-btn" data-id="<?= $r['id'] ?>" data-status="<?= $status ?>">
              <?= $status=='Active' ? 'Khóa' : 'Mở khóa' ?>
            </button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal detail -->
<div class="modal fade" id="customerDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Chi tiết khách hàng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="customerDetail">
        <p class="text-center text-muted">Đang tải...</p>
      </div>
    </div>
  </div>
</div>

<?php include('../includes/footer.php'); ?>

<script>
$(document).ready(function(){
  const table = $('#customerTable').DataTable({
    pageLength: 8,
    language: { url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/vi.json' }
  });

  $('#searchBox').on('keyup', function(){
    table.search(this.value).draw();
  });

  $('#filterRecent').click(function(){
    const sevenDaysAgo = new Date();
    sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
    table.rows().every(function(){
      const dateText = $(this.node()).find('td:eq(3)').text();
      // dd/mm/yyyy hh:mm -> yyyy-mm-dd
      const parts = dateText.split(' ')[0].split('/');
      const d = new Date(parts[2] + '-' + parts[1] + '-' + parts[0]);
      if (d >= sevenDaysAgo) $(this.node()).show(); else $(this.node()).hide();
    });
  });

  $('#resetFilter').click(function(){ table.rows().every(function(){ $(this.node()).show(); }); });

  // View detail
  $('.view-btn').click(function(){
    const id = $(this).data('id');
    $('#customerDetail').html('<p class="text-center text-muted">Đang tải...</p>');
    $('#customerDetailModal').modal('show');
    $.get('customer_detail.php', { id: id }, function(res){ $('#customerDetail').html(res); });
  });

  // Toggle status
  $('.lock-btn').click(function(){
    const id = $(this).data('id');
    const status = $(this).data('status');
    const action = status == 'Active' ? 'Khóa' : 'Mở khóa';
    Swal.fire({
      title: action + ' tài khoản?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Xác nhận'
    }).then(result=>{
      if(result.isConfirmed){
        $.post('toggle_status.php', { id: id, status: status }, function(res){
          Swal.fire('Thành công', res, 'success').then(()=> location.reload());
        });
      }
    });
  });

  // Reset password
  $('.reset-btn').click(function(){
    const id = $(this).data('id');
    Swal.fire({
      title: 'Reset mật khẩu?',
      text: 'Mật khẩu mới sẽ được tạo và hiển thị (demo).',
      showCancelButton: true
    }).then(result=>{
      if(result.isConfirmed){
        $.post('reset_password.php', { id: id }, function(res){
          Swal.fire('Mật khẩu mới', res, 'success');
        });
      }
    });
  });
});
</script>
