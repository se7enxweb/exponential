# Sessions: who is signed in, and signing browsers out

This guide explains sessions in Exponential and the **Setup > Sessions** page (`/setup/session`): what a session is,
the two kinds of session handler and what the page can do with each, the figures, the filter, the search, sorting
and paging, removing sessions safely, and what to do when something does not behave as expected.

It is written for administrators. Every page, field and rule below was checked against the code of this repository
on 6 October 2026; the files are named in [References](#references).

[Guides](README.md) · Related: [Security and audit](security-and-audit.md) ·
[Operating a site](operating-a-site.md) · [List paging](../features/6.0/admin-list-paging.md)

## In short

- A **session** keeps one browser signed in. It is opened at the first request that needs it and ends
  `site.ini [Session] SessionTimeout` seconds after its last use (default 259200, three days).
- Where sessions are kept depends on `site.ini [Session] Handler`. Empty (the default) means
  `ezpSessionHandlerPHP`: PHP keeps them in its own storage (files) and the page **cannot list or remove them one
  by one**. `ezpSessionHandlerDB` keeps them in the table `ezsession`, and the page lists and removes them.
- Without a session table the page shows **who signed in recently** (from `ezuservisit`, which every handler keeps),
  with a time window, a search, sorting and paging, and explains how to switch.
- With a session table the page shows figures, a filter and a search, one card per user (or per session of one
  user) with last activity and idle time, and removes sessions through a **confirmation**. The session you are using
  is never removed from a selection.
- Session keys are never shown: a card carries a short reference and the first four characters of the key.

## 1. The page without a session table (the default)

Open **Setup > Sessions**. With `Handler=` empty the page starts with a blue notice: sessions are kept by
`ezpSessionHandlerPHP` in PHP's session storage (`session.save_handler`, usually `files`), so they cannot be counted
or removed here. PHP removes timed out sessions itself (`session.gc_maxlifetime`, which Exponential sets to
`SessionTimeout`).

Below it:

| Part | What it shows |
|---|---|
| Figures | Users who signed in within the last `ActivityTimeout` (one hour by default), within the last day, and within the session lifetime |
| Signed in within | The time window of the list: the last hour, the last day, the session lifetime |
| Find a user | Part of a name, login or e-mail address; **Update list** applies it, **Clear search** removes it |
| Recently signed in | One card per user: name (a link to the user), login, e-mail, last sign-in and how long ago, the sign-in before, the number of sign-ins; your own card is marked **You** |
| Sort by | Last sign-in (newest first), full name, login, e-mail, sign-ins; selecting the current sort reverses it |
| Per page and pages | 50, 25 or 100 per page (`admininterface.ini [PaginationSettings] ItemsPerPageList_setup_session[]`) and the pager |
| How to administer sessions on this page | The settings for the database handler (section 3) |

A user in this list may still have an open session; one who signed out, or whose browser forgot its cookie, has
none. With the PHP handler the visit record is written at sign-in only, so "last sign-in" is exactly that, not the
last click.

The window and the search are kept in your own session while you page and sort.

## 2. The page with a session table

With `Handler=ezpSessionHandlerDB` the page lists the rows of `ezsession`.

**Figures**: sessions in all, users active within `ActivityTimeout`, sessions of signed in users, anonymous
sessions, and sessions that have timed out but are still in the table (red when there are any).

**Actions on all sessions**:

- **Remove timed out / old sessions** is safe: it removes the expired rows and the baskets they leave, as
  `bin/php/ezsessiongc.php` and the `session_gc` cronjob part do. Nobody is signed out. The message after it says how
  many went.
- **Remove all sessions** signs out everybody, you included. It asks first, on a page that says so.

**Filter**: Users (Everyone, Registered users, Anonymous users), **Find a user** (part of a name, login or e-mail
address, `%` and `_` match themselves) and **Include inactive users** (also users whose last activity is older than
`ActivityTimeout`). **Update list** applies them; they are kept in your session.

**The list** has one card per user: name, login, badges (**You**, Anonymous visitors, the number of sessions,
Timed out), e-mail, last activity, idle time in words and when the session ends, with the buttons **Sessions** (that
user's sessions one by one) and **User** (the user object). Sort by last activity, full name, login, e-mail or count;
the sort is in the address (`/(sortby)/name/(order)/asc`), so paging keeps it.

**One user's sessions** (`/setup/session/<user id>`, or **Sessions** on a card) have one card per browser or device,
named by the first four characters of the key. The session you are using is marked and cannot be ticked.
**Sessions for all users** goes back.

## 3. Switching to session administration

1. In `settings/override/site.ini.append.php`:

   ```ini
   [Session]
   Handler=ezpSessionHandlerDB
   ```

2. Clear the INI cache and reload the PHP workers, or deploy:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

   Everybody signs in again once: sessions kept by PHP are not carried over to the table.
3. Open **Setup > Sessions**: the list of section 2.

`ForceStart=enabled` also opens a session for every anonymous visitor; set it only if anonymous visitors should be
counted, because it writes a row for every visit.

## 4. Removing sessions safely

| Button | What happens |
|---|---|
| Remove selected (all users) | A confirmation lists the ticked users with their session counts; **Remove the sessions** signs their browsers out. If your own user is ticked, its other sessions go and the one you are using stays. |
| Remove selected (one user) | A confirmation for the ticked sessions; the session you are using is left out even if it was ticked. |
| Remove all sessions | A confirmation that says everybody, you included, is signed out. |
| Remove timed out / old sessions | No confirmation: nobody is signed out. |

**Cancel** goes back without changing anything. Removing a session signs out a browser; it does not block the
user. To stop somebody signing in again, disable the user or change their password as well.

The page posts the same buttons as before (`RemoveAllSessionsButton`, `RemoveTimedOutSessionsButton`,
`RemoveSelectedSessionsButton`, `UserIDArray[]`); the confirmation posts them again with `ConfirmSessionRemoval=1`.
One user's sessions are posted as references (`SessionRefArray[]`); an older override that posts `SessionKeyArray[]`
still works, for keys of that user only. Every form carries the form token.

## 5. Problems

| Symptom | Cause and fix |
|---|---|
| "Your current session handler does not support session administration." | `Handler` is empty or `ezpSessionHandlerPHP`. That is normal; see section 3 to switch. |
| The recent sign-in list is empty | Nobody signed in within the window: choose the session lifetime. |
| "Not all timed out sessions were successfully removed." | The run stopped to avoid a timeout. Repeat it, or run `php bin/php/ezsessiongc.php`, or install the `session_gc` cronjob part. |
| "Only the session you are using now was selected" | Your own current session is never removed from the list. Sign out to end it. |
| "Time skew detected" in Last activity | The session's end lies further ahead than the session lifetime: the clock of a web server differs, or `SessionTimeout` was lowered. |

## References

- View: `kernel/private/classes/views/setup/session.php` (`Exponential\View\Kernel\Setup\Session`), module
  definition `kernel/setup/module.php` (`session`, policy `setup/administrate`).
- Templates (design/admin and design/admin4): `setup/session.tpl`, `setup/session_no_db.tpl`,
  `setup/session_confirmremove.tpl`, `setup/session_exp_style.tpl`; `design/standard/templates/setup/session_no_db.tpl`
  for other designs.
- Settings: `site.ini [Session] Handler, SessionTimeout, ActivityTimeout, ForceStart`;
  `admininterface.ini [PaginationSettings] ItemsPerPageList_setup_session[]`.
- Garbage collection: `kernel/private/classes/services/sessiongarbagecollector.php`, `bin/php/ezsessiongc.php`,
  `cronjobs/session_gc.php`.
- Tests: `tests/tests/kernel/classes/setup/SessionPageTest.php` (references, own-session rule, cleaning, durations,
  rows; no database).
- [Changelog 6.0.15](../changelogs/6.0/6.0.15.md)
