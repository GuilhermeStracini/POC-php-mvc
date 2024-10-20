<div class="container mt-5">
    <h1 class="display-4">API Sandbox</h1>
    <p class="lead">Test the API by sending live requests.</p>
    <hr class="my-4">

    <!-- API Method Selector -->
    <div class="form-group">
        <label for="apiMethod">HTTP Method</label>
        <select class="form-control" id="apiMethod">
            <option value="GET">GET</option>
            <option value="POST">POST</option>
            <option value="PUT">PUT</option>
            <option value="DELETE">DELETE</option>
        </select>
    </div>

    <!-- Endpoint URL Input -->
    <div class="form-group mt-3">
        <label for="apiUrl">API Endpoint</label>
        <input type="text" class="form-control" id="apiUrl" placeholder="e.g. /api/v1/users/1">
    </div>

    <!-- Request Body Input (For POST/PUT methods) -->
    <div class="form-group mt-3" id="requestBodyContainer" style="display: none;">
        <label for="apiRequestBody">Request Body (JSON)</label>
        <textarea class="form-control" id="apiRequestBody" rows="5" placeholder='e.g. {"name": "John", "email": "john@example.com"}'></textarea>
    </div>

    <!-- Submit Button -->
    <button class="btn btn-primary mt-3" id="submitRequest">Send Request</button>

    <!-- Response Output -->
    <div class="mt-5">
        <h4>Response</h4>
        <pre id="apiResponse" style="background-color: #f8f9fa; padding: 15px; border: 1px solid #ddd;"></pre>
    </div>
</div>

<?php $this->startSection("scripts"); ?>
<script>
    // Show/hide request body input based on selected method
    $('#apiMethod').on('change', function() {
        if ($(this).val() === 'POST' || $(this).val() === 'PUT') {
            $('#requestBodyContainer').show();
        } else {
            $('#requestBodyContainer').hide();
        }
    });

    // Handle form submission
    $('#submitRequest').on('click', function() {
        var method = $('#apiMethod').val();
        var url = $('#apiUrl').val();
        var requestBody = $('#apiRequestBody').val();

        // Send AJAX request
        $.ajax({
            url: url,
            method: method,
            contentType: 'application/json',
            data: (method === 'POST' || method === 'PUT') ? requestBody : null,
            success: function(response) {
                $('#apiResponse').text(JSON.stringify(response, null, 4));
            },
            error: function(xhr, status, error) {
                $('#apiResponse').text('Error: ' + error + '\nResponse: ' + xhr.responseText);
            }
        });
    });
</script>
<?php $this->endSection(); ?>