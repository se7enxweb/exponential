# Velocity Q shell

This page is for administrators who manage a Velocity server and would rather type than click. The Q shell is a
drop-down console on every server view (`/Q/dashboard`, `/Q/panel`, documentation, PHP Info, error pages). Its
commands administer the server: status, workers, cache, sites, modules, certificates, logs. It speaks a zsh-like
language with a real parser (nothing is handed to a shell as a string). Applies to Exponential Velocity 0.0.4.28 and
later (`vc-qshell` command from 0.0.4.42).

## Open it and run a command

1. Sign in to the [control panel](velocity-control-panel.md). The shell needs a signed-in panel session; it is on by
   default.
2. Press the backtick key (or `~`), or click **Shell** in the toolbar.
3. Type a command:

   ```
   qsh> uptime
   qsh> workers -o pid,state,mem -H | sort -k3
   qsh> get Q.shell.timeout
   qsh> for n in 2 4 8; do workers resize $n -f; sleep 5; health; done
   qsh> man jobs
   ```

What it is good for:

- Administer the server from the browser without a login on the machine: `server status`, `cache clear`,
  `site enable`, `ssl`, `conf`, `mod`, `logs`, `workers`, `ext`.
- Script it: loops, pipes, conditions, aliases, `source` scripts from a directory.
- In an Exponential installation (a document root that is an Exponential tree), the shell adds the installation's own
  tools as `exp` commands (release 0.0.4.28).
- It is safe by design: tiers, confirmation, password re-entry for dangerous commands, an audit log.

## Use it at a terminal

`php bin/qshell.php` starts the same shell without a server; `php bin/qshell.php -c 'health'` runs one line.

The deb and rpm packages install it as `/usr/bin/vc-qshell` (the container image as `/usr/local/bin/vc-qshell`), a
link into the installed tree, so this works from any directory for any user:

```bash
vc-qshell -c 'health'
```

The name is not `qshell` because `/usr/bin/qshell` belongs to another program. Before 0.0.4.42 the packages did not
install the shell as a command at all.

The shell works from the phar, the packages, the container image and the static binaries (since 0.0.4.28; before it,
it answered that its runner was missing, as a `429`). A missing runner now answers `503` with the reason.

## The terminal

- **Window controls**: `+` starts or shows the shell; `-` hides it (session and jobs keep running; `Esc` does the
  same); `m` maximises; `x` closes it and ends the session and its jobs after asking.
- **Tabs and splits**: `Ctrl-Shift-T` opens a tab, `Ctrl-Shift-D` splits; each pane is its own session with its own
  jobs.
- **Line editing**: Emacs keys (`Ctrl-A/E/K/U/W`, `Alt-B/F`), arrows for history, `Ctrl-R` reverse search, `Tab`
  completion, `Ctrl-C` interrupt, `Ctrl-L` clear.
- **Themes**: `theme list`, `theme quake`; themes are JSON files in a design's `shell/` directory.
- **Phones**: soft keys for Tab, Ctrl, arrows and `|`.
- **Transport**: a WebSocket at `/Q/ws/shell`, falling back to HTTP polling.
- **Settings**: `get` shows every setting with its source; `set workers.count 8` applies to the running server;
  `set -p workers.count 8` also writes it to the config file (a `.bak` is kept). The shell's own security settings
  cannot be changed from the shell.

## The language

| Feature | Syntax |
|---|---|
| Lists | `a; b`, `a && b`, `a \|\| b`, `a \| b`, `a &` |
| Quoting | `'literal'`, `"with $vars"`, `\x` |
| Expansion | `$x`, `${x:-default}`, `$(command)`, `$((1 + 2))`, `a{1,2,3}` |
| Control | `if ... then ... elif ... else ... fi`, `for`, `while`, `until`, `[ ... ]` |
| History | `!!`, `!n`, `!-n`, `!prefix`, `^old^new`; `history`, `history N`, `history -a`, `history -c` |
| Scripts | `source name`, `exec name` from the shell's directory or `Q.shell.scriptsDir` |

Loops stop after 10,000 rounds. Listings follow zfs conventions: `-o a,b,c` columns, `-H` no header and tab-separated,
`-p` exact numbers, `-j` JSON. Every command answers `--help`; `man <command>`, `man <topic>` and `man -k <word>` cover
the rest.

## Jobs

`command &` runs in the background; `jobs`, `fg %n`, `bg %n`, `kill %n`, `wait` manage them. A job keeps running while
the console is hidden.

## History

100,000 entries by default in `<state dir>/shell/history` (zsh extended format), appended under a lock so several
sessions never lose a line. The console loads the newest 1,000; `Up` past the oldest fetches the previous 1,000;
`Ctrl-R` searches the loaded entries, then the whole file on the server. Measured with 150,000 entries: newest page
about 1.5 ms, a search back to the oldest entry about 30 ms.

## Tiers and settings

| Tier | May run |
|---|---|
| `basic` | read-only: status, health, logs, `get` |
| `expanded` | plus changes to the running server: cache, workers, sites, `set` |
| `advanced` | plus server control and, when allowed, OS commands |

| Configuration key | Default | Meaning |
|---|---|---|
| `Q.shell.enabled` | `true` | The console and the API |
| `Q.shell.tier` | `advanced` | Highest tier a session may use |
| `Q.shell.allowSystem` | `false` | Raw OS commands (`! command`, `sys command`) |
| `Q.shell.user` | document root owner | Who commands run as |
| `Q.shell.allowRoot` | `false` | `sudo <command>` as root |
| `Q.shell.elevateMinutes` | `5` | How long a password check lasts |
| `Q.shell.timeout` | `120` | Seconds per command (TERM, then KILL) |
| `Q.shell.maxOutput` | `8388608` | Bytes of output per command |
| `Q.shell.historySize` | `100000` | History entries kept; `0` keeps every one |
| `Q.shell.maxJobs` | `8` | Jobs per session |
| `Q.shell.scriptsDir` | none | Directory of scripts the shell can run |
| `Q.shell.toggleKey` | backtick | The key that opens the console |
| `Q.shell.allowedOrigins` | `[]` | More origins allowed to open the WebSocket (a proxy's public URL) |

## Safety

- **Confirmation.** Commands that change the running server ask `proceed? [y/N]`; `-f` answers yes anywhere on the
  line. In scripts and through the API, `-f` is required.
- **OS commands are off** unless `Q.shell.allowSystem` is `true`, and only in the advanced tier.
- **Dangerous commands ask for the panel password again**, as `sudo` does: `rm`, `dd`, `mkfs`, `shred`, `shutdown`,
  `kill`, `chmod -R`, a download piped into a shell, writing into `/etc`, and others. Stopping or restarting the server
  and changing the panel password ask the same way. Wrong answers feed the panel's lockout.
- **Never root for your code.** Each command line runs in its own process as `Q.shell.user`; it refuses to run if that
  user is missing or root. The server's own console commands (`server`, `ssl`, `conf`, `site`, `mod`, `cache clear`,
  `panel`, `ext`, `logs tail`) are run by the server itself, re-checked server-side against tier and password state.
- **Nothing of the server's.** A command gets none of the server's open sockets or files, and only path, locale and
  `QBIX_*`/`VC_*` variables (release 0.0.4.28 fixed a leak of sockets and environment).
- **Same origin only** for the WebSocket. Behind a proxy, list the public origin in `Q.shell.allowedOrigins`.
- **Audit.** Every command (who, from where, tier, exit status, duration) is appended to `shell-audit.log` beside the
  panel data; the dashboard has a **Shell activity** card.

## REST API

Under `/Q/api/shell/`, authenticated with the panel session token in `Authorization: Bearer <token>` or
`X-Panel-Token` (the cookie alone is refused):

| Method and path | What it does |
|---|---|
| `GET session` | who, tier, user, whether OS commands and sudo are allowed |
| `DELETE session?session=<name>` | end the session and its jobs |
| `POST exec` | `{"command": "...", "force": false, "timeout": 30}` returns `202` with a job id |
| `GET poll?since=<seq>` | messages since a sequence number |
| `POST input`, `POST signal` | send input or a signal to a job |
| `GET jobs`, `GET jobs/<id>`, `DELETE jobs/<id>` | list, read, stop |
| `POST elevate` | the password check before a dangerous command |
| `GET complete?line=...`, `GET history`, `GET history/search`, `GET audit` | completion, history pages, search, audit |

```bash
curl -s -H "Authorization: Bearer $TOKEN" -d '{"command":"uptime"}' https://host/Q/api/shell/exec
```

## Limits

- Needs a signed-in panel session; signed out, the toolbar item is disabled.
- The command check reads the line as text. It stops habitual mistakes; it is not a sandbox. Leave
  `Q.shell.allowSystem` off unless you need it.
- `server reload` and `server restart` take the session with the server; the shell reconnects.

## Related pages

- [Control panel and dashboard](velocity-control-panel.md) (the sign-in the shell needs), [packages and binaries](velocity-packages-and-binaries.md) (`vc-qshell`), [Velocity web server](velocity-web-server.md)
- Specifications: [engine settings](../../specifications/6.0/velocity-engine-settings.md) (programs, `qbixconsole`), [HTTP/2 and security](../../specifications/6.0/velocity-http2-and-security.md) (the admin surface)
- Upgrade: [Velocity engine upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md)
- [Changelog: Exponential Velocity engine](../../changelogs/extensions/exponential-velocity.md)
- History: [24 September](../../history/velocity/2026-09d.md), [25 to 30 September](../../history/velocity/2026-09e.md)
