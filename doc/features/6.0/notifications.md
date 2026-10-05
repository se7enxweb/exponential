# Notifications

Exponential tells users by e-mail when content they follow changes and about the collaboration items they take part in. This
page describes the feature from the user's and the administrator's side; the technical contract is in
`doc/specifications/6.0/notifications.md`.

## For users: My notification settings

`/notification/settings` (admin menu: My notification settings) is an overview:

- **Items I follow**: the subtree subscriptions, with the path, the class and the date of the last change below each item. The list
  is paged (the page size of the other admin lists: 10, 25, 50) and can be filtered by name and by class. An item whose content was
  deleted is shown as such and can be removed.
- **Add items** opens the content browser; **Remove selected** first shows what will be removed and asks.
- **E-mail digest**: by default every change is mailed at once. A digest holds the messages back and sends one e-mail: daily at an
  hour, weekly on a weekday, or monthly on a day (day 31 means the last day of a short month).
- **Collaboration notification**: which kinds of collaboration items (for example approvals) send e-mail.

Every change shows an inline notice. From the content structure, **Notify me** opens `/notification/addtonotification/<node>`, which
asks before it subscribes (or, for an item that is already followed, offers to stop). Opening the address changes nothing; the
button is a POST with the form token.

Works in admin4 and admin4l (admin4l uses the admin4 templates); the other designs keep their own templates and fall back to the
standard ones for the confirmation pages.

## For administrators: Notification status

`/notification/status` (Setup, Notification; policy `notification/administrate`) shows pending events, the messages kept for a digest,
subscriptions, mail sent in the last 24 hours, the last runs of the notification cronjob and a list of problems (events waiting
while no run is recorded, overdue digests, subscriptions to deleted content or users, no sender address, the file transport).
**Run now** starts `exp:notification:run` in the background and shows its output; **Preview (dry run)** lists what would be sent
and changes nothing. Handled events that nothing waits for, and events older than 30, 90, 180 or 365 days, can be removed;
subscriptions to deleted content can be removed in one step.

`/notification/runfilter` keeps working (Run notification filter, Spawn time event) and now goes through the same service: locked
against the cronjob, recorded and audited.

## Cronjob and console

The cronjob part `notification` (in the group `frequent`, or alone with `php runcronjobs.php notification`) makes a time event and
handles every pending event. Console commands (aliases `exp:notify:*`):

| Command | What it does |
| --- | --- |
| `exp:notification:status [--json]` | pending events, digest items, subscriptions, last runs, problems; ends in PASS, or FAIL when a problem of level error exists |
| `exp:notification:run [--dry-run] [--at=<time>] [--event=<ids>] [--no-time-event] [--mail-file-dir=<dir>]` | one pass; `--dry-run` lists subject, number of recipients and masked addresses (`--addresses` shows them) and changes nothing |
| `exp:notification:events [list\|cleanup] [--status=] [--older-than=30d] [--include-unknown] [--dry-run]` | list events, remove handled orphans and old events |
| `exp:notification:subscriptions [list\|remove-missing] [--user=] [--q=] [--class=] [--missing] [--addresses]` | subscriptions per user and subtree |

`--mail-file-dir=<dir>` forces the file transport for that process only: the mail is written to files, whatever site.ini says. Use it
for every trial run.
