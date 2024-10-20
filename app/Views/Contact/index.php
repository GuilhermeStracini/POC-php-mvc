<div class="container">
    <h2>Contact Us</h2>
    <p>Have questions? Feel free to reach out using the form below:</p>

    <!-- Display success message -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <!-- Display form errors -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $field => $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Contact Form -->
    <form action="/contact/submit" method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name"
                name="name" value="<?= htmlspecialchars($name ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email"
                name="email" value="<?= htmlspecialchars($email ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>" id="message"
                name="message" rows="5"><?= htmlspecialchars($message ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>