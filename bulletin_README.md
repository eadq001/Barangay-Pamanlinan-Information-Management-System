Bulletin Board Subsystem
========================

Files added:

- `migrations/create_bulletins_table.php` - run once to create the `bulletins` table and seed a sample post.
- `bulletinBoard.php` - main listing page for announcements, advisories, events and notices.
- `bulletin_create.php` - create new post (requires login).
- `bulletin_edit.php` - edit an existing post (requires login).
- `bulletin_delete.php` - delete a post (requires login).
- `bulletin_view.php` - view single post.

How to run:

1. Ensure your webserver and PHP are running and `connection.php` database credentials are correct.
2. From the project folder, run the migration once by opening the migration file in the browser or running from CLI:

   php migrations/create_bulletins_table.php

3. Open `bulletinBoard.php` in your browser to see the sample post.
4. Login using the existing login system to create/edit/delete posts.

Notes:
- The subsystem uses the existing `connection.php` (mysqli) and session-based authentication. It expects a `user_id` in `$_SESSION` for logged-in users.
