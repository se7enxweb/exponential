# Remote services: the whole admin as an HTTP API (expservices)

`extension/expservices` turns ezjscore server functions into a library of remote services, 1,122 of them as
counted by Setup > RAD when this was written (re-count there, or with the catalogue call `expservices::catalog` shown below), so remote admin apps (desktop, iOS, Android, shell scripts) and JavaScript front ends can
do what the admin interface does: read settings, clear caches, manage content, users, shop, feeds, media, tags,
layouts and the audit. Added 2026-10-02 (extension version 0.1.0, first draft).

Full catalogue and per-domain tables: [doc/bc/6.0/backend_ezjscore_services.md](../../bc/6.0/backend_ezjscore_services.md).

## Call one in 30 seconds

```bash
# sign in once, keep the cookie
curl -c jar -d 'username=<user>' --data-urlencode 'password=<password>' \
  'https://<site>/ezjscore/call/expsession::login?ContentType=json'
curl -b jar 'https://<site>/ezjscore/call/expsession::whoami?ContentType=json'
# discover everything
curl -b jar 'https://<site>/ezjscore/call/expservices::catalog::system?ContentType=json'
```

The URL form is `<root>/ezjscore/call/exp<domain>::<method>[::<arg>...]`. Answers use an envelope:
`{"ok":true,"data":...,"meta":{...}}` or `{"ok":false,"error":{"code":403,"message":"..."}}`; lists are paged with
`total`, `offset`, `limit`, `count`, `has_more`.

## Personal API tokens (for scripts and apps)

A signed-in user creates a token with `expsession::tokenCreate` (POST `name`, `expires_in_days` 1 to 365, default
90). The token (`expt_` and 48 hex characters) is shown once; only its SHA-256 hash is stored. Send it as
`Authorization: Bearer <token>` or, where a proxy does not pass that header to PHP, as `X-Exp-Token: <token>`.
Requests sign in as the token's user for that request only: no cookie, same policies as the user, writes still
need POST but no form token. Expired, revoked or disabled-user tokens answer 401; a token cannot create tokens.
`expsession::tokenList` and `expsession::tokenRevoke` manage them. Events `access.expservices.token.create`,
`.revoke` and `.failed` go to the [audit trail](audit-trail.md). The table `expservices_token` is created from
`extension/expservices/sql/<engine>/schema.sql` (MySQL, PostgreSQL, SQLite).

## Domains

| Group | Domains and services |
|---|---|
| Core | catalogue 5, session 11, system 16, settings read 10, caches 14, cronjobs 7, extensions 9, packages 7, workflows 10, Velocity 5, extension point survey 7, debug summary 6 |
| Content (350) | nodes, objects, classes, class groups, attributes, versions, translations, relations, locations, trash, sections, states, URL aliases and wildcards, search, content jobs |
| Users and access (195) | users, groups, roles, policies, sessions, preferences, notifications, collaboration, account flows |
| Commerce, community, feeds (255) | products, basket and checkout, orders, VAT, currencies, discounts, wish lists, shipping, payments, collected forms, polls, forums, comments, reviews, RSS exports and imports, RSS/Atom/JSON Feed output of any subtree |
| Media and other (218) | images and aliases, files and signed download links, audio and video, tags, layouts, audit (read only), subitems columns, newsletters, sitemaps, statistics, designs, languages, external links, PDF links |
| Editor | `expeditor::engines`, `get`, `set`, `uploadExtensions`, `config` (see [the TinyMCE 8 editor](online-editor-tinymce8.md)) |

## Write a service of your own

A domain is one class that extends `expServiceBase` (`extension/expservices/classes/expservicebase.php`), registered
as `[ezjscServer_exp<domain>]` in an `ezjscore.ini.append.php` (the shipped ones are in
`extension/expservices/settings/ezjscore.ini.append.php`). Each service is one `public static` method declared in
the class's `$services` array with `summary`, `access` (`public`, `user` or `[module, function]`), `write` (true =
POST, form token, audited), `args` (`int`, `string`, `bool`, `json`, `list`) and `returns`. A service answers through
`ok()` or `page()`; throwing `expServiceException` with 400, 401, 403, 404, 409 or 422 becomes the error envelope. The
new service shows up in the catalogue and in Setup > RAD (`/setup/rad`) with no further step.

## Rules every service follows

- `access` is `public`, `user`, or a policy (module, function); 401 without login, 403 without the policy.
- Writes need POST, the form token (field `ezxform_token` or header `X-CSRF-Token`, from `expsession::token`),
  and the policy; every successful write records `service.<domain>.<method>` in the audit.
- Large operations are routed to [content jobs](content-jobs.md).
- Poll votes, form submissions and the VAT country choice need `content/read` (anonymous visitors have it on a
  public site); no write is open without a policy.
- `expfeed::runImport` runs one RSS import with the cronjob's code: POST, policy `rss/edit`, `dry_run` to only
  report, `max_items` to limit.

## Settings (extension/expservices/settings/expservices.ini)

| Block | Key | Default | Scope |
|---|---|---|---|
| `Services` | `Enabled` | `enabled` (when `disabled`, every service answers 403) | installation |
| `Paging` | `DefaultLimit` / `MaxLimit` | 25 / 200 | installation |
| `Writes` | `RequireToken` | `enabled` (switch off for tests only) | installation |
| `Writes` | `Audit` | `enabled` | installation |
| `Community` | `ForumClass`, `ForumContainerClass`, `TopicClass`, `ReplyClass`, `CommentClass`, `PollClass`, `ReviewClass`, `TopicStickyAttribute` | `forum`, `forums`, `forum_topic`, `forum_reply`, `comment`, `poll`, `review`, `sticky` | installation |

## Portals built on it

Sample front ends (four designs under `extension/expservices/design/`: `expportal_jquery`, `expportal_react`,
`expportal_reactive` and `expportal_wireframe`; their shared settings are in `extension/expservices/settings/expportal.ini`)
render news, shop, forums, media, feeds, search and login in the browser without a build
step: `expportal_jquery` (jQuery 4 reference) and `expportal_reactive` (React with React Bootstrap, components, a
store and one-way data flow). They call the services by catalogue name. The guide chapter "Clients" has shell and
Python examples.

Related: [the audit trail](audit-trail.md), [the bc guide](../../bc/6.0/backend_ezjscore_services.md), [specification](../../specifications/6.0/expservices.md), [October 2026 chronicle](../../history/2026/2026-10.md).

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [audit event model](../../specifications/6.0/audit-event-model.md).
