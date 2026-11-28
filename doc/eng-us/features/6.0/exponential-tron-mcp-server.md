# Exponential Tron: the MCP server layer

This page is for administrators who want to let an AI agent work inside an Exponential installation through the Model
Context Protocol (MCP), and for extension developers who want to offer their own data as MCP tools. It says what
Exponential Tron is, how it reaches an installation, how to switch it on, how agents connect, what keeps them in check
and where the full documentation lives.

**Status.** Exponential Tron is a public preview. From 25 November 2026 the root `composer.json` of Exponential
requires `se7enxweb/tron` (`~0.0.0.16` today), so every installation made with Composer from the 6.0.15 line has it in
`extension/tron`. It is **not activated by default**: nothing listens and no tables exist until an administrator turns
it on. Its interfaces may still change between preview releases; the version rule is the project's own (only the last
position moves: 0.0.0.9, 0.0.0.10, ...).

## Contents

1. [What it is](#what-it-is)
2. [How it reaches an installation](#how-it-reaches-an-installation)
3. [Switching it on](#switching-it-on)
4. [Connecting an agent](#connecting-an-agent)
5. [The HTTP endpoint](#the-http-endpoint)
6. [Programs, tools and skills](#programs-tools-and-skills)
7. [What keeps an agent in check](#what-keeps-an-agent-in-check)
8. [Setup > tron in the administration](#setup--tron-in-the-administration)
9. [Settings](#settings)
10. [The command line](#the-command-line)
11. [Clusters, several sites and hosting](#clusters-several-sites-and-hosting)
12. [Compatibility and limits](#compatibility-and-limits)
13. [Troubleshooting](#troubleshooting)
14. [Related pages](#related-pages)

## What it is

MCP is an open protocol through which an AI client (Claude Code, Claude Desktop, Cursor, VS Code and others) calls
tools a server offers. Exponential Tron is that server for Exponential. What sets it apart from a generic connector:

| | A typical MCP connector | Exponential Tron |
|---|---|---|
| Identity | one shared service account | every call runs as a real user of the installation |
| Permissions | all tools for everyone | the installation's roles and policies decide, through `eZUser::hasAccessTo`; `tools/list` shows only what the user may call |
| Credentials | a long-lived key in a file | per-agent tokens: stored only as a SHA-256 hash, shown once, expiring, scoped to programs, rotated or revoked in one step |
| What is offered | fixed at build time | programs found in the active extensions; each program and tool can be switched off per installation |
| Accountability | the client's own logs | every call audited with user, token, outcome and time; a write runs only after its audit entry is written |
| Ways in | usually one | stdio (on the server or over SSH; nothing listens) and stateless streamable HTTP at `/tron/mcp` |

It speaks MCP 2025-11-25 and the earlier versions 2025-06-18, 2025-03-26 and 2024-11-05: tools, resources, resource
templates, prompts, pagination and structured content.

## How it reaches an installation

- **Composer.** The root `composer.json` requires `se7enxweb/tron`. The package is of type
  `ezpublish-legacy-extension` and is installed into `extension/tron`, like the other required extensions.
- **The protocol layer.** On PHP 8.2 and later, when the package `mcp/sdk` (the official MCP PHP SDK, Apache 2.0) and
  a PSR-7 implementation such as `nyholm/psr7` are installed, Tron hands JSON-RPC, version negotiation, the lifecycle and
  both transports to the SDK. `se7enxweb/exponential-php-floor` 1.3.14, the line for PHP 8.4 and later, brings
  `mcp/sdk ^0.10` and `nyholm/psr7 ^1.8` with it, so on PHP 8.4 and 8.5 the SDK is there without a further step. On PHP
  7.4 to 8.1, where the SDK cannot run, and wherever it is missing, Tron's own frozen protocol layer speaks the same
  versions. On PHP 8.2 and 8.3 run `composer require mcp/sdk nyholm/psr7` if you want the SDK.
- **The root's `config.allow-plugins`** lists `php-http/discovery`, the SDK's Composer plugin; it is harmless where the
  SDK is not installed.
- **Licenses.** Tron is GPL-2.0-or-later; the SDK is Apache 2.0. Apache 2.0 code combines with the GPL version 3, and
  Tron's "or later" lets the two run together.

## Switching it on

From the installation root, as the user that owns the files (as root add `--allow-root-user` to every command).
`<sa>` is an administration siteaccess.

1. Activate the extension in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=tron
   ```

2. Regenerate the autoloads and clear the INI cache. On a site served by Velocity, deploy instead:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-tag=ini
   # or, where Velocity serves the site:
   ./console exp:velocity deploy
   ```

3. Create Tron's tables (seven `tron_*` tables; idempotent; `--dry-run` prints the SQL) and check:

   ```bash
   php extension/tron/bin/php/tron.php install --siteaccess=<sa>
   php extension/tron/bin/php/tron.php status --siteaccess=<sa>      # PASS, or exit 1 with the reason
   ```

4. **Make one user per agent** (never a person's account, never the shared administrator) and give it a role with
   the policies it needs, without limitations: `tron/use` always, then for example `tron/centre_read` and
   `tron/centre_write`. A policy with a limitation counts as not granted.

5. **Make a token.** The first one, your own, comes from **Setup > tron > Tokens**. Further ones come from the
   command line with a token of a user who holds `tron/admin`:

   ```bash
   php extension/tron/bin/php/tron.php token:create --siteaccess=<sa> --token-file=<your token file> \
       --user=agent-claude --name="claude@laptop" --days=30 --scopes=centre \
       --out=$HOME/.config/tron/agent-claude.token
   ```

   The token (`tron_<prefix>_<secret>`, 256 random bits) is written with mode 0600 and never printed. It expires after
   `--days` (90 by default, at most 365).

## Connecting an agent

| Way in | When | Identity |
|---|---|---|
| stdio on the server | the agent runs on the machine of the installation | `--token-file=<file>` or the environment variable `TRON_TOKEN` |
| stdio over SSH | the agent runs elsewhere and may SSH to the server | a token file that stays on the server |
| HTTP | anywhere else | `Authorization: Bearer <token>` |

Claude Code, stdio on the server:

```bash
claude mcp add tron -- php /path/to/site/extension/tron/bin/php/tron.php serve \
    --siteaccess=<sa> --token-file=$HOME/.config/tron/agent-claude.token
```

Claude Desktop (`claude_desktop_config.json`) and Cursor (`.cursor/mcp.json`) take the same command as JSON:

```json
{
  "mcpServers": {
    "tron": {
      "command": "php",
      "args": ["/path/to/site/extension/tron/bin/php/tron.php", "serve", "--siteaccess=<sa>",
               "--token-file=/home/<you>/.config/tron/agent-claude.token"]
    }
  }
}
```

Over SSH the client starts `ssh -o BatchMode=yes <user>@<server> "php .../tron.php serve ..."`; over HTTP it is
`claude mcp add --transport http tron https://<host>/tron/mcp --header "Authorization: Bearer $TRON_TOKEN"`.

**stdio needs a token.** From tron 0.0.0.14 on, `serve --user=<login>` without a token is refused unless an override
sets `[StdioSettings] AllowLoginOption=enabled`: without a token, whoever could start the process could act as any
user. From 0.0.0.15 on the same applies to `tron.php call`, `token:create` and the `tron-skills.php` commands that act
as a user.

A good first request to an agent: "Call `tron_whoami` and `tron_status` and tell me what you can do here." The answer
names the user, the token, its scopes, the transport and the protocol version.

## The HTTP endpoint

Off until it is opened. It is stateless (one POST in, one JSON answer out), so any server of a cluster can answer any
request and a load balancer needs no stickiness.

```ini
# settings/override/tron.ini.append.php
[HTTPSettings]
Enabled=true
SiteAccesses[]
SiteAccesses[]=<a user-facing siteaccess with RequireUserLogin=false>
AllowedHosts[]
AllowedHosts[]=<host>
```

Then:

- a global URL alias `mcp` to `tron/mcp` gives the short address `/mcp`;
- behind Apache with PHP-FPM, pass the header on: `SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1`;
- behind the [request shield](request-shield.md), include the rules Tron ships in `doc/request-shield/tron.rules`, or
  the shield answers the MCP requests with 405.

| Status | Meaning |
|---|---|
| 200 | the JSON-RPC answer |
| 202 | a notification, accepted |
| 400 | an unsupported `MCP-Protocol-Version` |
| 401 | no token, or a malformed, unknown, expired or revoked one, or a disabled user |
| 403 | a foreign `Origin`, or a user without `tron/use` |
| 404 | the endpoint is off for this siteaccess |
| 405 | not a POST (Tron offers no event stream) |
| 413, 415, 421 | the body is too large, not `application/json`, or the Host is not in `AllowedHosts[]` |

A token is never accepted in a URL.

## Programs, tools and skills

What an agent can do comes from **programs**. Each program registers tools, resources and prompts under its own name
prefix, loads in isolation (a broken program fails alone and says why) and can be switched off.

| Program | Tools (examples) | Policies |
|---|---|---|
| `tron` (the core, always on) | `tron_whoami`, `tron_status`; resources `tron://status`, `tron://programs` | `tron/use` |
| `centre` | tickets, cards and decisions: `centre_ticket_create`, `_update`, `_comment`, `_move`, `_close`, `_link`, `_list`, `_get`, `centre_board_view`, `centre_decision_review`, `centre_decision_record`; prompts `centre_weekly_review`, `centre_triage` | `tron/centre_read`, `tron/centre_write`, `tron/centre_decide` |
| `search` | `search_query` (the installation's own search engine, with subtree, class, section and language filters, sort, paging and facets), `search_index` | `content/read`: only what the user may read is found or counted |
| further programs | content, classes, nodes, roles, INI, templates, layouts, validation, diffs, the REST and GraphQL APIs, web fetches under an SSRF guard | the policies each one names |

**Your own program.** Put `tron/program.json` into any extension and implement `tronProgram`:

```
extension/myext/tron/program.json          {"name": "myext", "class": "myextTronProgram", "version": "1.0.0.0", "requires": "0.0.0.1"}
extension/myext/classes/myexttronprogram.php
```

The program's `register()` adds tools with `tronTool::define()`: a name under the program's prefix, a description, a
JSON schema for the input (enforced before the handler runs), the policy (`array( 'myext', 'read' )`) and a handler.
`'write' => true` makes Tron write the audit entry before the handler runs. After `ezpgenerateautoloads.php -e`,
`tron.php status` lists `myext ... loaded`.

**Skills** are reviewed chains of tool calls written as data (a manifest with typed parameters, the units it runs,
declarative steps between them and the schema of the answer). They hold no code and run as their caller. Tron ships 16
starter packs (content inventory, classes, media, SEO, languages, INI and settings, site audit, Exponential Layouts,
templates, users and roles and others); an extension can ship its own under `extension/<ext>/tron/skills/<pack>/`. A
skill is offered only after its harness passed and a person approved it in **Setup > tron > Skills**.

## What keeps an agent in check

| Layer | Protection |
|---|---|
| Users | an agent is a real, dedicated user of the installation |
| Roles and policies | module `tron`: `use`, `admin`, `tokens`, `audit`, `centre_read`, `centre_write`, `centre_decide` and the functions programs name; Tron has no permission system of its own |
| Tokens | hash-only storage, constant-time comparison, expiry, scopes that only narrow, revocation effective on the next request; disabling a user stops all of the user's tokens |
| Transports | HTTP off until enabled per siteaccess; Origin allowlist (empty: every browser request refused), Host allowlist, JSON only, a body limit, the protocol version check |
| Audit | every `tools/call`, `resources/read`, `prompts/get`, refused sign-in and token action, in the table `tron_audit` and/or the JSON-lines file `var/log/tron-audit.log`; secrets masked; a write is refused when its entry cannot be written |
| Answers | declared to the agent as data, not instructions; internal errors say only "internal error"; answers above `ResponseMaxBytes` are cut with a note |

Tron never stores a token in clear, never accepts one in a URL, never shows a tool the user may not call, never runs
a write without its audit entry and never changes a kernel table.

## Setup > tron in the administration

| Page | Policy | Does |
|---|---|---|
| Dashboard | `tron/admin` | version, programs and their state, tools, active tokens, the last day's calls by outcome, the ways in |
| Tokens | `tron/tokens` | make (shown once), rotate, revoke; list by user and state |
| Programs and tools | `tron/admin` | every program and tool with its policy and the roles that grant it, and a switch for each |
| Audit | `tron/audit` | newest first, filters by outcome, transport, program and user, a live tail |
| Settings | `tron/admin` | `tron.ini` as the siteaccess reads it, with links into the INI editor |

Token and switch actions ask "Verify it's you" first. The pages have a light and a dark mode and are translated into
English and German.

## Settings

`extension/tron/settings/tron.ini.append.php` holds the defaults; override in `settings/override/tron.ini.append.php`
or per siteaccess, or with [`exp:ini`](exp-ini-command.md).

| Group | Key | Default | Meaning |
|---|---|---|---|
| `ServerSettings` | `Title` | Exponential tron | the server title in `initialize` |
| | `Instructions` | empty | text appended to the instructions the agent receives |
| | `PageSize` | 100 | items per list page |
| | `ResponseMaxBytes` | 200000 | longer answers are cut with a note |
| `ProgramSettings` | `ExtensionScan` | enabled | look for programs in the active extensions |
| | `Disabled[]` | none | programs to leave out (never the core) |
| `AuditSettings` | `Sinks[]` | database | `database` and/or `file` |
| | `FilePath` | `var/log/tron-audit.log` | the file sink |
| `TokenSettings` | `DefaultLifetimeDays`, `MaxLifetimeDays` | 90, 365 | token lifetimes |
| `StdioSettings` | `AllowLoginOption` | disabled | `enabled` (in an override) allows `serve --user=<login>` without a token |
| `HTTPSettings` | `Enabled` | false | the endpoint answers 404 until it is `true` |
| | `SiteAccesses[]` | none | the siteaccesses that serve the endpoint |
| | `AllowedOrigins[]` | none | browser origins; empty refuses every request with an `Origin` |
| | `AllowedHosts[]` | none | the Host names served; set it on a public endpoint |
| | `MaxBodyBytes` | 1048576 | larger bodies get 413 |
| `CentreSettings` | `Backend` | auto | `auto`, `expcentre` or `tron` |
| | `Lanes[]`, `Boards[]` | inbox, next, doing, waiting, done; any | the board layout of the centre program |

## The command line

```bash
php extension/tron/bin/php/tron.php <command> --siteaccess=<sa> [options]
```

| Command | Does |
|---|---|
| `serve --token-file=<f>` | the stdio MCP server (`TRON_TOKEN` works too) |
| `install [--dry-run]` | creates the missing tables (idempotent) |
| `status` | programs, tools and tables; exit 1 when something is wrong |
| `token:create --token-file=<issuer> --user=<login> --name=<what for> [--days=<n>] [--scopes=a,b] [--out=<file>]` | a token, shown once or written 0600 |
| `token:list [--user=<login>]` | tokens with state, expiry and last use, never the secret |
| `token:revoke <id or prefix>` | revokes now |
| `audit:tail [--limit=50] [--user=<login>]` | the newest audit entries |
| `call <tool> --token-file=<f> [--json='{...}']` | one tool call as the token's user, through the full permission, schema and audit path |

Exit codes: 0 PASS, 1 FAIL, 2 usage. `bin/php/tron-schema.php` prints the SQL per database engine;
`bin/php/tron-skills.php` lints, tries, runs and approves skills.

## Clusters, several sites and hosting

- **Several siteaccesses** of one installation share Tron's tables (tokens, audit) when they share a database; the
  settings can differ per siteaccess.
- **Several installations** each run their own Tron; an agent gets one MCP server entry per installation (`tron-shop`,
  `tron-docs`), and the client keeps their tools apart by server name.
- **Clusters.** HTTP is stateless: no stickiness. Install and activate Tron on every serving node before traffic
  reaches it (`tron.php install` once, on the shared database).
- **Hosting.** Either stdio over SSH on a private network (nothing listens, the token stays on the node) or public
  HTTPS with tokens behind a load balancer. Through Velocity an HTTP request takes about 120 ms (median); building the
  server takes about 10 to 45 ms, read tools 20 to 40 ms, writes 50 to 100 ms with their audit writes.

## Compatibility and limits

| | |
|---|---|
| Exponential | the 6.x kernel; proven on 6.0.15 |
| PHP | 7.4 to 8.5, each tested |
| Databases | MariaDB / MySQL, PostgreSQL and SQLite for Tron's own tables; Oracle and MongoDB are planned, the other databases of 6.0.15 are not yet supported for Tron's tables |
| Runtimes | Apache with PHP-FPM and Velocity proven; nginx with PHP-FPM expected to work |
| MCP | 2025-11-25, 2025-06-18, 2025-03-26, 2024-11-05 |

Not yet: OAuth 2.1 sign-in, self-service tokens, rate limits, streaming with progress and cancellation, change
notifications and activation by default; they are on Tron's roadmap, with no date.

## Troubleshooting

Start every diagnosis with `tron.php status` and `tron.php audit:tail --limit=20`.

| Symptom | Fix |
|---|---|
| "Running scripts as root may be dangerous" | add `--allow-root-user`, also in the client's `serve` command |
| `serve` exits "does not hold the policy tron/use" | grant `tron/use` to the user's role, without a limitation |
| `serve` exits "missing tables" | `tron.php install` |
| `serve --user=...` refused | use a token (`--token-file`, `TRON_TOKEN`); `--user` needs `[StdioSettings] AllowLoginOption=enabled` |
| HTTP 404 | `[HTTPSettings] Enabled` and `SiteAccesses[]`, or this node lacks Tron |
| HTTP 405 with a `reference` | the request shield: include `tron.rules` |
| HTTP 401 although the token works on stdio | Apache drops `Authorization`: pass it on |
| A tool is missing | `tron_status` lists the tools left out and why: roles, scopes, switches |
| A role change is not seen | restart the client: stdio keeps its answers for the process |
| A write refused "audit log is unavailable" | the audit table or file is not writable by this process |

## Related pages

- Tron's own documentation, in the package: `extension/tron/README.md` and `extension/tron/doc/index.md` (quick start,
  clients, tokens, permissions, audit, HTTP transport, hosting, programs, skills, security, upgrading), and
  [github.com/se7enxweb/tron](https://github.com/se7enxweb/tron).
- [AI providers, the job queue and MCP](../../help/integrators/ai-job-queue-and-mcp.md) (for integrators),
  [Choosing an interface for an agent](../../help/ai-agents/agentic-workflows/choosing-an-interface.md).
- [The request shield](request-shield.md), [Velocity web server](velocity-web-server.md),
  [Change settings from the command line: exp:ini](exp-ini-command.md).
