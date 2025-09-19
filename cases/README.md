Cases Subsystem

Files:
- `cases/index.php` - list cases
- `cases/create.php` - create new case (requires login)
- `cases/view.php` - view case and add mediations (requires login to add)
- `cases/edit.php` - edit case (requires login)
- `cases/delete.php` - delete case (requires login)
- `cases/_header.php` - header fragment reused in pages

Database migration:
Run the migration to create tables and sample data:

```bash
php migrations/create_cases_tables.php
```

Access:
- Open `bulletinBoard.php` and click `Cases` in the header, or go to `cases/index.php`.

Notes:
- The system uses session `user_id` to determine a logged-in user. Use existing `login.php` to authenticate.
- Mediations update `cases` status counts in `case_status_counts` table.
