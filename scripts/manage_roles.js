$(document).ready(function() {
    $('#usersTable').DataTable();
});

$('#editUserModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var userId = button.data('user-id');
    var username = button.data('username');
    var email = button.data('email');
    var fullName = button.data('full-name');
    var contactNumber = button.data('contact-number');
    var role = button.data('role');
    var status = button.data('status');

    var modal = $(this);
    modal.find('#user_id').val(userId);
    modal.find('#edit_username').val(username);
    modal.find('#edit_email').val(email);
    modal.find('#edit_full_name').val(fullName);
    modal.find('#edit_contact_number').val(contactNumber);
    modal.find('#edit_role').val(role);
    modal.find('#edit_status').val(status);
});
