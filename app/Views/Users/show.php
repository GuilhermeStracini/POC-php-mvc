<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>User Details</h2>
            <form id="userDetailsForm">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Name</th>
                        <td>
                            <span id="userName"><?php echo htmlspecialchars($user['name']); ?></span>
                            <input type="text" class="form-control d-none" id="editName" value="<?php echo htmlspecialchars($user['name']); ?>">
                            <button type="button" class="btn btn-sm btn-warning" id="editNameBtn">Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>
                            <span id="userEmail"><?php echo htmlspecialchars($user['email']); ?></span>
                            <input type="email" class="form-control d-none" id="editEmail" value="<?php echo htmlspecialchars($user['email']); ?>">
                            <button type="button" class="btn btn-sm btn-warning" id="editEmailBtn">Edit</button>
                        </td>
                    </tr>
                </table>
                <button type="button" class="btn btn-success d-none" id="saveChangesBtn">Save Changes</button>
            </form>
            <a href="/users" class="btn btn-primary">Back to List</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Edit Name button
        $('#editNameBtn').click(function() {
            $('#userName').toggleClass('d-none');
            $('#editName').toggleClass('d-none');
            $('#saveChangesBtn').removeClass('d-none');
        });

        // Edit Email button
        $('#editEmailBtn').click(function() {
            $('#userEmail').toggleClass('d-none');
            $('#editEmail').toggleClass('d-none');
            $('#saveChangesBtn').removeClass('d-none');
        });

        // Save Changes button
        $('#saveChangesBtn').click(function() {
            const userId = <?php echo $user['id']; ?>;
            const updatedName = $('#editName').val();
            const updatedEmail = $('#editEmail').val();

            $.ajax({
                url: '/users/' + userId,
                method: 'PUT',
                data: {
                    name: updatedName,
                    email: updatedEmail
                },
                success: function(response) {
                    // Update displayed values
                    $('#userName').text(updatedName);
                    $('#userEmail').text(updatedEmail);

                    // Toggle input fields
                    $('#userName').toggleClass('d-none');
                    $('#editName').toggleClass('d-none');
                    $('#userEmail').toggleClass('d-none');
                    $('#editEmail').toggleClass('d-none');
                    $('#saveChangesBtn').addClass('d-none');

                    alert('User updated successfully.');
                },
                error: function(xhr, status, error) {
                    alert('Failed to update user: ' + error);
                }
            });
        });
    });
</script>
