# Static publishing: troubleshooting

This guide is for administrators whose build, publish or GitHub Pages site does not do what they expect. It lists
the symptoms by stage, with the cause and the fix, and the commands that show what happened.

[Guides](README.md) · Related: [When a publish fails](../help/static-publishing/when-a-publish-fails.md),
[Static publishing](../features/6.0/static-publishing.md)

## What you'll have at the end

The cause of a failed build, push or page found from the report and the status, and fixed without guessing.

## First, look

```bash
./console exp:static:targets                       # every target, its settings checked
./console exp:static:status github-public          # repository, credential readable or not, the last runs
./console exp:static:diff github-public --verify --check-links --mount=/my-site/
./console exp:static:github status github-public   # GitHub Pages, the domain, HTTPS, the last Pages build
```

In the administration, every job has its page (`/staticpublish/log/<run-id>`) with the report, and History lists
every publish with its push state.

## Settings

| Symptom | Cause and fix |
|---|---|
| `Static publishing is switched off` | Set `[StaticPublishSettings] Enabled=enabled` |
| `TargetList[] names no target` | Add the id to `TargetList[]`; the `[Target_<id>]` block alone is not built |
| The siteaccess is refused | It is an administration or editing siteaccess; name a front one |
| `Remote` is refused | It holds a password or token; put the secret in Setup > Secrets and name it in `Credential` |

## The build

| Symptom | Cause and fix |
|---|---|
| `No page was exported` | The server cannot fetch the site at `SiteURL`; set `CrawlURL` to an address it reaches |
| `refused: the response set the session cookie` | The page starts a session for anonymous visitors (a form, a basket, a registration); exclude it with `ExcludePaths[]` or change it |
| `came with debug output`, `refused: debug output from the server` | The server that answered did not take the build's signed `X-Exp-Static-Build` header (see below): fetch from this installation's own server, and reload PHP-FPM and restart Velocity after an update |
| Pages `skipped` | They only exist with a query string, or their path cannot be a file name on every system |
| An address shows the page it redirects to, or a page "has moved" | An address that redirects (`/authors` to `/`) becomes a small stub that sends on, never a copy of the target (`redirects_followed` in `--format=json`); a loop or a chain of more than 5 redirects gets no stub and is listed under `skipped`. Fix the redirect on the site, or exclude the address with `ExcludePaths[]` |
| Files `could not be collected` | The site does not hand them to an anonymous visitor; their links go to the live site (`files_failed` in `--format=json`) |
| `another process works on the target` | A build, publish, rollback or push of that target is running; wait for it |
| A build is very large | `Assets=referenced` copies only what pages name; exclude sections that need not be public |

**Debug output and the build.** A build fetches the site over HTTP from the server it runs on, and that server's own
addresses (`127.0.0.1`, `::1`, its public address) are often in `site.ini [DebugSettings] DebugIPList[]`. So every
request of a build carries a signed `X-Exp-Static-Build` header: an HMAC with a key derived from the installation's
secret (the one the form tokens of static copies are signed with), over a fresh nonce, good for two minutes and for
at most four requests (the request and its redirects). A request with a valid signature that comes from the server
itself (its `REMOTE_ADDR` is a loopback address or one of the server's own addresses, never a forwarded header) gets
no debug output: no debug report, no debug bar, no template debug comments, no DebugByUser. The page is exactly what a
visitor outside the list sees, on Apache with PHP-FPM and on Velocity alike, and nobody's `site.ini` changes. The
header can only switch debug output off, never on; from any other address, expired, forged or used up it changes
nothing. A page that still comes with debug output (or with a response header named after debugging) is not cleaned
but refused, and the build fails with `came with debug output`: the server did not take the signature, because the
build fetched from another machine (`CrawlURL`), or the web server still runs older code or another secret.

## The publish

| Symptom | Cause and fix |
|---|---|
| Push state `pending`, `authentication failed` | The credential's secret is missing, expired or lacks write access; renew it, then `exp:static:push` |
| `pending` with a size reason | The build is over `MaxSiteBytes` or has a file over `MaxFileBytes`; make it smaller |
| `pending`, `rejected (fetch first)` | The branch has commits that were not published from here; static publishing never forces. Do not commit to the publishing branch by hand ([Repositories and credentials](static-publishing-targets-and-credentials.md)) |
| `the secret ... is not set` | Add it in Setup > Secrets, or use `token:env:VARIABLE` |
| The folder or `.git` is refused | It belongs to another system user; run the commands as the owner of the site's files |
| A token is refused for the workflow file | `Delivery=actions` pushes `.github/workflows/`; the token needs the Workflows permission |

## On GitHub Pages

| Symptom | Cause and fix |
|---|---|
| 404 on `github.io` | Pages is not set to the branch (or to GitHub Actions for `Delivery=actions`), or the Pages build is still running |
| CSS and images missing below `/<repository>/` | `Links=base-path` with a wrong `BasePath`; use `Links=relative` or correct it |
| CSS missing at a custom domain | `BasePath` is not empty; empty it, build and publish |
| The custom domain is lost after a push | `CustomDomain` is not set, so no `CNAME` is written |
| No HTTPS yet | GitHub issues the certificate after it sees the DNS; wait, then `exp:static:github setup` again |
| A private repository serves nothing | GitHub Pages serves private repositories only on paid plans |

## Next step

[Static publishing: custom domains and DNS](static-publishing-custom-domains-and-dns.md).
