<form action="auth/register.php" method="POST">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <select name="role_id">
        <option value="1">Admin</option>
        <option value="2">Editor</option>
        <option value="3">Viewer</option>
    </select>
    <button type="submit">Register</button>
</form>
