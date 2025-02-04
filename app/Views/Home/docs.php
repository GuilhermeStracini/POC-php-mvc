<div class="container mt-5">
    <h1 class="display-4">API Documentation</h1>
    <p class="lead">REST API for managing users.</p>
    <hr class="my-4">

    <h2>API Base URL</h2>
    <p><code>https://<?php echo $_SERVER['HTTP_HOST']; ?>/api/v1/users/</code></p>

    <h2>Endpoints</h2>

    <!-- GET All Users -->
    <div class="mt-4">
        <h3>GET /api/v1/users</h3>
        <p>Retrieve a list of all users.</p>
        <p><strong>Response Example:</strong></p>
        <pre><code>{
    "status": "success",
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john.doe@example.com"
        },
        {
            "id": 2,
            "name": "Jane Doe",
            "email": "jane.doe@example.com"
        }
    ]
}</code></pre>
        <p><strong>Status Codes:</strong></p>
        <ul>
            <li>200 OK - Request was successful.</li>
            <li>500 Internal Server Error - Something went wrong on the server.</li>
        </ul>
    </div>

    <!-- GET Single User -->
    <div class="mt-4">
        <h3>GET /api/v1/users/{id}</h3>
        <p>Retrieve a specific user by their ID.</p>
        <p><strong>URL Parameters:</strong></p>
        <ul>
            <li><code>id</code> (required) - The ID of the user to retrieve.</li>
        </ul>
        <p><strong>Response Example:</strong></p>
        <pre><code>{
    "status": "success",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john.doe@example.com"
    }
}</code></pre>
        <p><strong>Status Codes:</strong></p>
        <ul>
            <li>200 OK - Request was successful.</li>
            <li>404 Not Found - User not found.</li>
            <li>500 Internal Server Error - Something went wrong on the server.</li>
        </ul>
    </div>

    <!-- POST Create User -->
    <div class="mt-4">
        <h3>POST /api/v1/users</h3>
        <p>Create a new user.</p>
        <p><strong>Request Body:</strong></p>
        <pre><code>{
    "name": "New User",
    "email": "new.user@example.com"
}</code></pre>
        <p><strong>Response Example:</strong></p>
        <pre><code>{
    "status": "success",
    "data": {
        "id": 3,
        "name": "New User",
        "email": "new.user@example.com"
    }
}</code></pre>
        <p><strong>Status Codes:</strong></p>
        <ul>
            <li>201 Created - User was successfully created.</li>
            <li>400 Bad Request - Invalid data was provided.</li>
            <li>500 Internal Server Error - Something went wrong on the server.</li>
        </ul>
    </div>

    <!-- PUT Update User -->
    <div class="mt-4">
        <h3>PUT /api/v1/users/{id}</h3>
        <p>Update an existing user.</p>
        <p><strong>URL Parameters:</strong></p>
        <ul>
            <li><code>id</code> (required) - The ID of the user to update.</li>
        </ul>
        <p><strong>Request Body:</strong></p>
        <pre><code>{
    "name": "Updated Name",
    "email": "updated.email@example.com"
}</code></pre>
        <p><strong>Response Example:</strong></p>
        <pre><code>{
    "status": "success",
    "data": {
        "id": 1,
        "name": "Updated Name",
        "email": "updated.email@example.com"
    }
}</code></pre>
        <p><strong>Status Codes:</strong></p>
        <ul>
            <li>200 OK - User was successfully updated.</li>
            <li>400 Bad Request - Invalid data was provided.</li>
            <li>404 Not Found - User not found.</li>
            <li>500 Internal Server Error - Something went wrong on the server.</li>
        </ul>
    </div>

    <!-- DELETE Delete User -->
    <div class="mt-4">
        <h3>DELETE /api/v1/users/{id}</h3>
        <p>Delete a specific user by their ID.</p>
        <p><strong>URL Parameters:</strong></p>
        <ul>
            <li><code>id</code> (required) - The ID of the user to delete.</li>
        </ul>
        <p><strong>Response Example:</strong></p>
        <pre><code>{
    "status": "success",
    "message": "User deleted successfully."
}</code></pre>
        <p><strong>Status Codes:</strong></p>
        <ul>
            <li>200 OK - User was successfully deleted.</li>
            <li>404 Not Found - User not found.</li>
            <li>500 Internal Server Error - Something went wrong on the server.</li>
        </ul>
    </div>
</div>
