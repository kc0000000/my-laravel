<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List with DataTable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Create User</h2>

    <form id="userForm" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="role_id" class="form-label">Role</label>
            <select class="form-control" id="role_id" name="role_id" required>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="profile_image" class="form-label">Profile Image</label>
            <input type="file" class="form-control" id="profile_image" name="profile_image">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<div class="container mt-5">
    <h2>User List</h2>
    <table class="table table-bordered" id="userTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Description</th>
                <th>Role</th>
                <th>Profile Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be loaded here -->
        </tbody>
    </table>
</div>

<!-- Optional: Edit modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    @csrf
                    <input type="hidden" id="editUserId" name="id">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editName" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="editPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-control"  id="editRole" name="role_id" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editProfileImage" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="editProfileImage" name="profile_image">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#userTable').DataTable({
        

        processing: true,
                serverSide: true,
                ajax: "{{ route('users.index') }}", // Route to get users data
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'description', name: 'description' },
                    { data: 'role.name', name: 'role.name' }, // Assuming 'role' is a relationship
                    { data: 'profile_image', name: 'profile_image', render: function(data) {
                        return '<img src="/' + data + '" width="50" height="50">';
                    }},
                    {
                    "data": null,
                    "render": function(data) {
                        return `
                            <button class="btn btn-warning editBtn" data-id="${data.id}">Edit</button>
                        `;
                        }
                    }
                ]
    });

    // Edit button click handler
    $(document).on('click', '.editBtn', function() {
        var userId = $(this).data('id');
        $.get('/user/' + userId, function(user) {
            // Populate form with user data
            $('#editUserId').val(user.id);
            $('#editName').val(user.name);
            $('#editEmail').val(user.email);
            $('#editPhone').val(user.phone);
            $('#editDescription').val(user.description);
            $('#editRole').val(user.role_id);  // Assuming 'role_id' is passed

            $('#editModal').modal('show');  // Open the edit modal
        });
    });

    // Submit the edit form via AJAX
    $('#editUserForm').on('submit', function(e) {

        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: '/user/' + $('#editUserId').val(),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response);
                if (response.errors) {
                    alert('Validation errors: ' + JSON.stringify(response.errors));
                } else {
                    alert('User updated successfully!');
                    table.ajax.reload();  // Reload the DataTable
                    $('#editModal').modal('hide');  // Close the modal
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                if (errors.profile_image) {
                    alert(errors.profile_image[0]); // Display the first error message for 'profile_image'
                }
            }
        });
    });


    $('#userForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        
        $.ajax({
            url: '/user',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.errors) {
                    alert('Validation errors: ' + JSON.stringify(response.errors));
                } else {
                    alert(response.success);
                    loadUsers(); // Reload users
                }
            },
            error: function() {
                alert('Error submitting form');
            }
        });
    });

    // Fetch and display users
    function loadUsers() {
        table.ajax.reload();
    }
});
</script>

<script>
$(document).ready(function() {
    // Submit the form via AJAX
    

});
</script>
</body>
</html>
