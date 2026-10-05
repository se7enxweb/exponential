# Bookmark folders: upgrade and compatibility

## In short

| | |
|---|---|
| What changed | A table `expbookmark_folder` and two columns `folder_id`, `priority` on `ezcontentbrowsebookmark`. New class `eZContentBrowseBookmarkFolder`, fetch functions `content/bookmark_rows` and `content/bookmark_folders`, the expservices domain `expbookmark`, the folder tree in the bookmark page, the Bookmarks box, the browse dialog and the editor. |
| Who is affected | Every installation: the database must be updated before the code runs, because `eZContentBrowseBookmark` now writes the new columns. |
| How to check | Setup, Upgrade check, database consistency; or `php bin/php/ezexec.php <script that prints the difference> --allow-root-user`. It must report no difference. |
| How to fix | Run the SQL below for your database, then clear all caches. |

## Upgrade steps

1. Update the files.
2. Run the statements of the section "Bookmark folders" at the end of `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`.
3. Clear all caches (`php bin/php/ezcache.php --clear-all --allow-root-user`), reload PHP, and with Velocity run its deploy.

MySQL:

```sql
ALTER TABLE ezcontentbrowsebookmark ADD COLUMN folder_id int(11) NOT NULL DEFAULT '0';
ALTER TABLE ezcontentbrowsebookmark ADD COLUMN priority int(11) NOT NULL DEFAULT '0';
ALTER TABLE ezcontentbrowsebookmark ADD INDEX ezcontentbrowsebookmark_folder (user_id, folder_id);
CREATE TABLE expbookmark_folder (
  created int(11) NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL DEFAULT '',
  parent_id int(11) NOT NULL DEFAULT '0',
  priority int(11) NOT NULL DEFAULT '0',
  user_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY expbookmark_folder_user ( user_id, parent_id )
) ENGINE=InnoDB;
```

PostgreSQL and SQLite: the same section in `update/database/postgresql/6.0/` and `update/database/sqlite/6.0/`.
For SQLite, with a backup first (`cp -p`), the section can be applied with a small script that skips what exists; a second run of the plain SQL stops on the duplicate column and changes nothing.

New installations get the tables from `share/db_schema.dba` and `kernel/sql/<engine>/`.

## Nothing to migrate

Every existing bookmark has `folder_id` 0 (the top level) and `priority` 0. The order of the flat list is unchanged.

## Compatibility

- `eZContentBrowseBookmark::fetchListForUser()` and `fetch( 'content', 'bookmarks' )` return all bookmarks of the user, flat, newest first, offset and limit as before. A bookmark in a folder is in the list.
- `eZContentBrowseBookmark::createNew( $userID, $nodeID, $name )` works as before; a bookmark that exists for the node is replaced and keeps its folder. The new fourth argument chooses the folder.
- The action `ActionAddToBookmarks` takes an optional `BookmarkFolderID`.
- `ezoe::bookmarks` answers as before unless its third argument is `tree`.
- Templates that ignore folders keep working. Overrides of `content/bookmark.tpl`, `content/browse.tpl` and `toolbar/full/admin_bookmarks.tpl` show the flat list until they are updated.
- Deleting a folder moves its bookmarks and subfolders up one level; no bookmark is deleted unless asked.

## Data model

`expbookmark_folder`

| Column | Type | Meaning |
|---|---|---|
| `id` | int, auto increment | Primary key |
| `user_id` | int | Owner (content object id of the user) |
| `parent_id` | int, 0 | Parent folder, 0 is the top level |
| `name` | varchar(255) | Name, tags and control characters removed |
| `priority` | int | Order within the parent, ascending |
| `created` | int | Unix time |

Index `expbookmark_folder_user (user_id, parent_id)`.

`ezcontentbrowsebookmark` (new columns): `folder_id` int NOT NULL DEFAULT 0 (the folder, 0 is the top level) and `priority` int NOT NULL DEFAULT 0 (the order within the folder, then newest first); index `ezcontentbrowsebookmark_folder (user_id, folder_id)`.

Rules: the depth is unlimited; a folder cannot be moved into itself or below itself (checked on every move); a folder or bookmark of another user does not exist for a user (refused in the model and the services); damaged data (a missing parent, a cycle) is shown at the top level and nothing is lost.
