---
title: "Lesson 2.2: Users, roles and passwords"
audience: trainer, admin, developer
category: training
order: 22
summary: Learners plan how user accounts, groups and roles move to Exponential, decide per source whether password hashes can carry over or need a reset, and handle personal data with a legal basis. 45 minutes.
keywords: migrating to exponential, users, user groups, roles, policies, password hash, password_hash_type, php_default, bcrypt, password reset, personal data, data protection
type: help
---
# Lesson 2.2: Users, roles and passwords

Users are the part of a migration that the most people notice when it goes wrong: nobody can sign in. This lesson
teaches how accounts, groups and roles map to Exponential, which password hashes Exponential can verify, and when a
reset is the only honest option.

> **Note**
> Test accounts in every exercise use addresses at `example.invalid`. Never import real users into a training
> installation.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 2: Mapping content to Exponential](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Administrators and developers; a data protection contact is welcome |
| Prerequisites | [Lesson 2.1](classes-datatypes-and-the-tree.md); a training installation with shell access |

## Learning objectives

After this lesson, learners can:

- map users, groups and roles of a source to user objects, user groups and roles with policies;
- read `ezuser.password_hash_type` and name the hash types Exponential knows;
- decide, for a given source, whether hashes can be imported as type 7 or a reset is needed;
- explain why permissions are designed for the new site rather than copied one to one.

## Trainer's notes

### How to present it

Sources: [Mapping content to Exponential, section 6](../../../../install/migration/content-model-mapping.md#6-users-groups-roles-and-passwords),
[chapter 14.10](../../../../install/14-migrating-from-4x.md#1410-users-passwords-and-sign-in) and
[chapter 17.7](../../../../install/17-migration-reference.md#177-passwords-url-aliases-and-image-storage).

1. **Accounts are objects** (4 minutes). Users are objects of the `user` class in user groups; members and editors
   usually go into separate groups.
2. **Roles** (4 minutes). Roles are rebuilt as Exponential roles with policies, assigned to users or groups. Do not
   copy permissions one to one; design them for the new site. Show [Roles and policies](../../../../guides/roles-and-policies.md).
3. **Hash types** (6 minutes). Show the table of chapter 14.10: types 0 to 7, from `empty` to `php_default`. With
   `[UserSettings] UpdateHash=true` (the default) a sign-in with an old hash stores a new one.
4. **Other systems** (6 minutes). Standard PHP `password_hash()` hashes (starting `$2y$`, `$argon2i$` or `$argon2id$`)
   can be imported as type 7 and are re-hashed at the next sign-in. Any other scheme cannot be verified: import the
   accounts without a usable password and send password reset mails, or build a custom login handler (development
   work).

### Common questions

**Can WordPress or Drupal users keep their passwords?** The WordPress and Drupal guides say both store passwords in
their own formats, which Exponential does not read: plan resets. For Joomla, the guide says to test whether the
hashes are standard PHP `password_hash()` values.

**What about old Exponential sites?** Old hash types keep working and are upgraded at sign-in, with the exceptions in
chapter 14.10: `md5_site` hashes need the old `[UserSettings] SiteName`, and `mysql` hashes do not work on MySQL 8.

**When do we tell users?** Before the cut-over. "Users locked out" is one of the recurring risks of the checklist,
and its counter-measure is to plan resets and tell users in advance.

### Pitfalls

- **Exporting hashes casually.** Password hashes are personal data. The xrowextract site archive never exports them
  ([Migrating away from Exponential](../../../../install/migration/leaving-exponential.md#5-users-and-personal-data)).
- **Moving every account.** Import only what the new site needs, with a legal basis.
- **Testing sign-in with the shared admin.** Use a test account per learner.

## Hands-on exercise

Use your own training installation. The database is SQLite in `var/storage/sqlite3/exponential.db`; open it
read-only.

1. Open the database:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db
   ```

2. Show the hash types in use:

   ```sql
   SELECT password_hash_type, COUNT(*) FROM ezuser GROUP BY password_hash_type;
   ```

3. Show the accounts without a settings row (the preflight query of
   [chapter 14.5.3](../../../../install/14-migrating-from-4x.md#1453-preflight-queries)); on a fresh installation it
   should return nothing:

   ```sql
   SELECT u.contentobject_id, u.login FROM ezuser u
     LEFT JOIN ezuser_setting s ON s.user_id = u.contentobject_id
    WHERE s.user_id IS NULL;
   ```

4. Leave with `.quit`.
5. Read the setting that decides the hash type of new passwords:

   ```bash
   php bin/php/console exp:ini get site.ini/UserSettings/HashType
   ```

6. For each of these sources, write in your notes "import as type 7" or "reset" and the reason, using the mapping
   page and the path guides: a WordPress site; a Drupal site; a custom PHP application that stores `password_hash()`
   values starting with `$2y$`; an old Exponential 4.x site on MySQL 8 with type 4 hashes.
7. Write a short reset announcement for editors (four sentences): what changes, when, what they do, whom they ask.
   Use only `example.invalid` addresses in it.

## Expected result

- Step 2 shows type 7 (`php_default`) on a fresh installation.
- Step 3 returns no rows.
- Step 5 prints `php_default` unless the installation changed it.
- Step 6: WordPress and Drupal reset; the custom PHP application type 7; the 4.x site with `mysql` hashes on MySQL 8
  reset (chapter 14.10).
- The trainer checks step 6 and reads one announcement aloud.

## Quiz

1. Which hash type does Exponential 6 use for new passwords by default?
2. A source stores hashes beginning with `$argon2id$`. What can you do?
   A. Nothing, always reset B. Import them as type 7 C. Convert them to MD5 D. Store them as plaintext
3. What does `[UserSettings] UpdateHash=true` do?
4. Why would accounts of an old site with `md5_site` hashes fail to sign in after the migration?
5. True or false: roles should be copied one to one from the source system.

<details>
<summary>Answers</summary>

1. Type 7, `php_default` ([chapter 14.10](../../../../install/14-migrating-from-4x.md#1410-users-passwords-and-sign-in)).
2. **B.** The kernel checks it with `password_verify()` and re-hashes it at the next sign-in
   ([mapping page, section 6](../../../../install/migration/content-model-mapping.md#6-users-groups-roles-and-passwords)).
3. A successful sign-in with an old hash stores a new hash of `HashType` (chapter 14.10).
4. The hash contains `[UserSettings] SiteName`; if the old site changed it, the same value must be set (chapter 14.10).
5. **False.** Design them for the new site (mapping page, section 6).

</details>

## Further reading

- [Mapping content to Exponential, section 6](../../../../install/migration/content-model-mapping.md#6-users-groups-roles-and-passwords):
  users, groups, roles and passwords.
- [Chapter 17.7: Passwords, URL aliases and image storage](../../../../install/17-migration-reference.md#177-passwords-url-aliases-and-image-storage):
  the hash types for reference.
- [Roles and policies](../../../../guides/roles-and-policies.md): designing roles on the new site.

## Related pages

- [Module 2: Mapping content to Exponential](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
