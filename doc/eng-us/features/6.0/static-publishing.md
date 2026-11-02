# Static publishing

This page is for administrators and developers who want a public copy of an Exponential site that any static host
can serve: GitHub Pages, any other git host's pages, or a plain web server with no PHP at all. Static publishing
turns a front siteaccess into a folder of HTML pages and files, with every link rewritten so the folder works on its
own, at the root of a domain or below a path such as `/my-site/`. Editors keep working in Exponential; the public
site is a copy.

It is new in Exponential 6.0.15. This page describes the build engine, publishing to git, their commands and the
screens under Setup > Static publishing. The GitHub setup is added to it when it arrives.

[Features of 6.0](README.md) · Related: [Static cache generator](static-cache-generator.md),
[Host static sites on GitHub Pages with Exponential 6.0.15+](../../guides/host-static-sites-on-github-pages.md),
[The build format](../../specifications/6.0/static-publishing-build-format.md)

## What you get

- **Targets** in `staticcache.ini`: each `[Target_<id>]` block names one front siteaccess, or several that share one
  host by their URI prefix (`SiteAccessList[]`, one copy with the `DefaultSiteAccess` at its root and the others in
  their folders: [Static publishing for multisite installations](../../guides/static-publishing-for-multisite-installations.md)),
  how its links are written and where the copy is meant to go. An installation can have many targets.
- **`exp:static:build <target>`**: crawls the siteaccess as the anonymous user, takes out what a public copy must
  not carry, rewrites the links, copies the files the pages need and writes the extras a static host expects.
- **`exp:static:serve <target>`**: a local preview of the build, served below the target's base path the way GitHub
  Pages serves a project site.
- **`exp:static:diff <target>`**: what the last build changed, whether the folder still matches it, and a link check
  of every page and file.
- **`exp:static:targets`**: every target with its settings checked.
- **`exp:static:publish <target>`**: commits the build in the target's own git repository, tags it
  `static/<target>/<n>` and pushes it, never forced, signed in with a token or an SSH deploy key that never reaches a
  command line or a log. `exp:static:rollback`, `exp:static:push`, `exp:static:keys` and `exp:static:status` go with
  it.

## How a build works

1. **Crawl as the anonymous user.** The build uses the crawler of the [static cache generator](static-cache-generator.md)
   in an export mode. It sends no cookie, so every page is what a visitor who is not signed in sees. It starts at the
   site's front page, follows only the links the site itself writes, and leaves out module views such as the search,
   the login and the editing screens. A target can fetch from an internal address (`CrawlURL`) instead of the site's
   public `SiteURL`.
2. **Take out what must not be public.** Every request carries a signed `X-Exp-Static-Build` header, so this server
   renders the page without debug output even when its own address is in `DebugIPList[]` (see "Debug output and the
   build" in the troubleshooting guide). Every page loses its form tokens (`ezxform_token` fields and the
   `csrf-token` tags), session ids in addresses and Content Security Policy nonces.
3. **Refuse what cannot be cleaned.** A page whose response set a session cookie, that still comes with debug output
   (it is not stripped: the server did not take the signature), or that still carries a form token, is refused. The build then fails and the target's folder is not changed. Pages that only exist
   with a query string (`/blog?page=2`) and paths that cannot be a file name on every system are skipped and
   reported.
4. **Collect the files.** Every file the pages and their style sheets name is copied: the designs' style sheets,
   scripts, fonts and images, the packed files in `var/<site>/cache/public`, image aliases and downloads. Files the
   web server serves directly are read from the installation (`var/` through the cluster file handler, so a cluster's
   files are found too). Everything else is requested from the site like a visitor would: a download, an image alias
   that has not been made yet (the request makes it), a packed file that has not been written yet. With
   `Assets=referenced-and-storage`, every image in `var/<site>/storage/images` that belongs to the published version
   of content the anonymous user may read is copied as well, linked from a page or not.
5. **Rewrite the links.** See [Links](#links) below.
6. **Write the extras and the manifest.** `.nojekyll`, `CNAME`, `404.html`, `robots.txt` and `sitemap.xml` as the target
   asks, and `.static-publish/manifest.json`, which lists every file with its SHA-256, its size and, for a page, its
   node and when its content was last modified.
7. **Compare, check and replace.** The build is made in a staging folder first. It is compared with the target's
   folder (the diff), and its links are checked. Unless it is a dry run, it then replaces the folder's content: new and
   changed files are copied, files the last manifest listed and this one does not are removed, and nothing else in the
   folder is touched. A `.git` folder or a README you added stays.

A build of the demo site, 560 pages and 3,547 files (485 MB, most of it storage images), took about three minutes on
a copy of its installation; most of it is the crawl, which renders each page once. The link check of that build
found no broken link or file among 18,534, at the root and below `/<repository>/`.

## Links

| Link in the page | In the copy |
|---|---|
| A page of the site (`/about-us`, `/index.php/about-us`, `https://www.example.com/about-us`) | `about-us/` relative to the page (`../about-us/` from a page one level down), or `/my-site/about-us/` with `Links=base-path` |
| The front page | `./` or `../../`, or `/my-site/` |
| A file (`/var/site/storage/images/...`, `/design/...`, `/extension/x/design/...`) | its path in the copy, relative or below the base path; the query of a file (`?x=1`) is kept |
| A fragment (`#top`), `mailto:`, `tel:`, `data:`, another host | unchanged |
| A page or file the copy does not hold (the search, the login, a form's action, a page with a query, a download the site refused) | the live site: `LiveURL`, by default `https://` and the host of the siteaccess's `SiteURL` (not when that host is `localhost` or a loopback address such as `127.0.0.1`, as on a build server or in CI: then the link stays as it is and the build warns) |
| A link (`<a href>`) to a page of the site's host the copy does not hold, with `OutsideLinks=unlink` | a placeholder link: the same `<a>` with its classes and content but without `href`, marked `aria-disabled="true"` and the class `exp-static-unavailable`, greyed out by the build's `exp-static.css` (linked only by the pages that need it; opacity only, so the theme's menu, footer, breadcrumb and button styles still apply). A navigation `<link>` (`alternate`, `search`, `next` ...) there is dropped. Images, style sheets, scripts and forms are never taken out (forms follow `Forms`) |
| A link into an administration siteaccess (`/admin/...`), with `AdminLinks=unlink` | a placeholder link, as above; on the site's own host or `LiveURL`. The administration siteaccesses are the ones the target check knows: a login is required, an administration design, or a name such as `admin` or `editor`; their path is `/<name>`, and the path of their `SiteURL` on one of the site's hosts |
| Canonical, `hreflang` alternates, `og:url`, `og:image`, `twitter:image`, the addresses in structured data (`application/ld+json`) | absolute, below `PublicURL`, when it is set |

Relative links work wherever the folder is served, at `/` and below any path. `Links=base-path` writes every link
below `BasePath`, as a site that is always served at one place wants. `404.html` always links below `BasePath`:
GitHub Pages serves it for every missing address at any depth, where relative links would point to the wrong place.

The siteaccess prefix (`/site/about-us` on a site matched by URI) and `index.php` are taken out of every page
address. Addresses in scripts (other than structured data) and comments are left alone.

## Settings

All settings are in `staticcache.ini`, next to the static cache's `[CacheSettings]`. Put yours in
`settings/override/staticcache.ini.append.php`.

`[StaticPublishSettings]`:

| Setting | Default | Meaning |
|---|---|---|
| `Enabled` | `disabled` | Static publishing as a whole; nothing is built while it is `disabled` |
| `TargetList[]` | empty | The ids of the targets that are built |
| `WorkDir` | `static-publish` | Where builds are kept, below `var/<site>/` unless absolute |
| `MaxSiteBytes` | `1000000000` | The build warns above 90 % of this (GitHub Pages allows 1 GB a site) |
| `MaxFileBytes` | `100000000` | The build warns about larger files (GitHub allows 100 MB a file) |
| `GitHubAPI` | `enabled` | The GitHub integration (repository, Pages, domain, build status); publishing to the branch works without it |
| `GitHubAPIURL` | `https://api.github.com` | GitHub's REST API (`https://<host>/api/v3` for GitHub Enterprise Server) |
| `GitHubAppId`, `GitHubAppKey` | empty | The GitHub App of `Credential=app:<installation>`: its id and the secret holding its private key |
| `GitHubOAuthClientId` | empty | The OAuth App of `exp:static:github signin` (device flow) |

`[Target_<id>]` (the shipped `[Target_example]` block is a template with every setting; it is not built):

| Setting | Default | Meaning |
|---|---|---|
| `Name` | the id | Shown in lists |
| `SiteAccess` | | The front siteaccess to publish; administration and editing siteaccesses are refused |
| `SiteAccessList[]` | empty | Several front siteaccesses in one copy, instead of `SiteAccess`: each but the default in the folder of its URI prefix (`/bold_ger/`); links between them stay in the copy. Each must be selected by a URI prefix (MatchOrder `uri`, or `host_uri` with a URI) |
| `DefaultSiteAccess` | empty | The siteaccess of `SiteAccessList[]` at the root of the copy; empty: each in its folder, and the root a page that links to them |
| `Links` | `relative` | `relative` or `base-path` |
| `BasePath` | empty | The path the site is served below: `/<repository>/` for a GitHub project site, empty for a custom domain |
| `PublicURL` | empty | The public address, for the sitemap, `robots.txt` and absolute canonical and social card links |
| `CustomDomain` | empty | Written to `CNAME`; with it the site is served at the root of the domain |
| `CrawlURL` | empty | Fetch the pages from this address instead of `SiteURL` |
| `LiveURL` | empty | Where links to what the copy does not hold go; empty: `https://` and the host of `SiteURL`, unless that host is `localhost` or a loopback address (then they stay as they are) |
| `OutsideLinks` | `keep` | `keep` (links to pages the copy does not hold go to the live site) or `unlink` (they stay in place as greyed-out placeholder links without an address) |
| `AdminLinks` | `keep` | `keep` or `unlink`, for links into an administration siteaccess |
| `MaxPages` | `10000` | The most pages one build crawls (1 to 20000) |
| `MaxDepth` | empty | How many links away from the front page the crawl goes; empty: `[CacheSettings] MaxCacheDepth` |
| `Assets` | `referenced-and-storage` | `referenced`, or also every storage image of content the anonymous user may see |
| `Extras[]` | `nojekyll`, `404`, `robots`, `sitemap`, `manifest` | The files written besides the pages |
| `PostProcess[]` | `strip` | `strip` always runs |
| `ExcludePaths[]` | empty | Pages left out by path (`/members/*`); links to them go to the live site |
| `ExcludeSections[]` | empty | Pages left out by the section of their content |
| `Remote`, `Branch`, `Folder` | empty, `gh-pages`, empty | The git address the build is pushed to, its branch, and `docs` when the site is in `/docs` of the branch (the build is then written to `<folder>/docs`) |
| `Credential` | `none` | How the push signs in: `token:<secret>`, `token:env:<VARIABLE>`, `sshkey:<secret>`, `app:<installation>` (a GitHub App) or `none` |
| `Kind`, `Delivery` | `github-pages`, `branch` | Read and checked now, used by the GitHub setup |
| `Search`, `Forms`, `Triggers[]`, `Schedule`, `Notify[]`, `Webhook` | `disabled`, `hide`, `manual` and `cli`, empty, `failure`, empty | Read and checked now, used by later parts of static publishing |

The generated reference lists every setting with its comment:
[`staticcache.ini [StaticPublishSettings]`](../../examples/ini-settings/staticcache/StaticPublishSettings.md) and
[`[Target_example]`](../../examples/ini-settings/staticcache/Target_example.md).

`exp:static:targets` checks each target: the siteaccess exists and is a front one, `BasePath` has the form `/name/`,
addresses are http or https, `Remote` holds no password or token, `Credential` names a secret instead of holding one,
the lists hold known values. Mistakes are errors; doubtful combinations (a `PublicURL` whose path is not `BasePath`,
a sitemap without `PublicURL`) are warnings.

## In the administration

**Setup > Static publishing** (the module `staticpublish`) does what the commands do, with screens: a wizard that
makes and changes targets (written through the INI editor into `settings/override/staticcache.ini.append.php`), a
build started in the background with its progress followed live, a preview of the build (what changes with sizes,
the build's report, the size against the host's limits, the copy served behind a signed link) with **Publish** and
**Discard**, the history with the rollback, the page of every job and the deploy key. A dashboard block lists the
targets with their stale pages and last publish, and Setup > Cache links to it. The policies `staticpublish/read`,
`build`, `publish` and `setup` take a Target limitation; publishing, rolling back, saving a target and the deploy key
ask "Verify it's you". The help manual: [Static publishing](../../help/static-publishing/README.md).

The administration starts its jobs as console processes (`exp:static:build`, `exp:static:publish` and
`exp:static:rollback` with `--login`, `--run-id` and `--progress`), so a long build never holds a web request; the
job files are in `<WorkDir>/.jobs` and `[StaticPublishSettings] JobHistory` sets how many are kept.

## Commands

| Command | What it does |
|---|---|
| `exp:static:targets` | Lists the targets with their siteaccess, links, public address, folder and last build; exit 1 when a target has an error |
| `exp:static:build <target>` | Builds; `--dry-run` reports what would change without touching the folder, `--paths=/a,/b` or `--nodes=12,34` builds only those pages and keeps the rest, `--report=<file>` also writes the report, `--crawl-url` and `--max-pages` override the target |
| `exp:static:diff <target>` | What the last build changed; `--verify` compares the folder with its manifest; `--check-links --mount=/ --mount=/my-site/` checks every link and file as a browser would |
| `exp:static:serve <target>` | Serves the folder on `http://127.0.0.1:8090/<base path>` with PHP's built-in web server; `--base-path=/` serves it at the root |
| `exp:static:publish <target>` | Commits the build, tags it `static/<target>/<n>` and pushes it; `--build` builds first, `--message` sets the commit message, `--no-push` commits and tags only |
| `exp:static:rollback <target> <n>` | Lists what going back to publish `n` (or the tag `static/<target>/<n>`) would change; `--apply` makes it a new commit with the next tag and pushes it |
| `exp:static:push [<target>]` | Pushes the targets whose push failed again (also the cronjob part `staticpublish`) |
| `exp:static:keys <target>` | `--generate` makes the target's SSH deploy key and prints its public half; without it, shows the public half and fingerprint |
| `exp:static:status <target>` | The target's repository, credential (readable or not, never the value), the last runs and the pages; `--limit` |
| `exp:static:github setup <target>` | Sets the target up on GitHub: `--create-repo` (`--public`, the default, or `--private`), GitHub Pages from the branch or `--pages=actions`, `--domain`, HTTPS unless `--no-https`; `--plan` only says what it would do |
| `exp:static:github status <target>` | The repository, GitHub Pages (delivery, domain, HTTPS, certificate) and the last Pages build, with what differs from the target |
| `exp:static:github signin --secret=<name>` | Signs in with GitHub (an OAuth App's device flow) and keeps the token as a secret for `Credential=token:<name>` |
| `exp:static:dns <domain>` | Checks the A, AAAA and CNAME records of a custom domain against GitHub Pages and lists the records to set; `--owner` |

Every command takes `--format=json` and then prints one JSON document, schema `exp-static/1`. The exit codes are
those of `exp:upgrade`: 0 done, 1 failed, 2 usage, 3 the database unreachable, 4 internal error. The generated pages:
[exp:static:build](../../examples/commands/expstaticbuild.md), [exp:static:diff](../../examples/commands/expstaticdiff.md),
[exp:static:serve](../../examples/commands/expstaticserve.md), [exp:static:targets](../../examples/commands/expstatictargets.md),
[exp:static:publish](../../examples/commands/expstaticpublish.md), [exp:static:rollback](../../examples/commands/expstaticrollback.md),
[exp:static:push](../../examples/commands/expstaticpush.md), [exp:static:keys](../../examples/commands/expstatickeys.md),
[exp:static:status](../../examples/commands/expstaticstatus.md), [exp:static:github](../../examples/commands/expstaticgithub.md),
[exp:static:dns](../../examples/commands/expstaticdns.md).

## Publishing to git

The target's folder `var/<site>/static-publish/<target>/` is a git repository of its own (never inside the site's
repository). `exp:static:publish` commits only the build's files in it, as one commit per publish with the annotated
tag `static/<target>/<n>`, and pushes the branch and the tags to the target's `Remote`. A push is never forced and a
tag is never moved; a push that fails keeps the commit local with the push state `pending`, and `exp:static:push` or
the cronjob part `staticpublish` sends it again. A rollback is a new commit holding the files of an earlier tag, so
the history only grows. One build, publish, rollback or push of a target runs at a time (a lock per target).

Credentials are names: a token in Setup > Secrets or an environment variable reaches git through `GIT_ASKPASS` and
the environment of the one git process; an SSH deploy key, made per target by `exp:static:keys`, through a file only
the process can read for the time of the push and `GIT_SSH_COMMAND`. Neither is ever on a command line, in the remote
address or in a log.

Every publish and rollback is a row of `exp_static_run` (state, counts, bytes, commit, tag, push state, who, when),
and the pages of the last publish are in `exp_static_page`; the upgrade step `6.0.15-static-publish-tables` makes both
tables on every database. The audit records `system.staticpublish.publish`, `.rollback` and `.credential`.

Guides: [Repositories and credentials](../../guides/static-publishing-targets-and-credentials.md),
[History and rollback](../../guides/static-publishing-history-and-rollback.md),
[SSH deploy keys](../../guides/static-publishing-deploy-keys.md).

## GitHub

For a target with `Kind=github-pages` and a GitHub address, static publishing uses GitHub's REST API, behind a
feature check (`expStaticGitHub::available()`: PHP's curl and openssl, and `GitHubAPI=enabled`). It signs in with
the target's own credential, a token (`token:`) or a GitHub App installation (`app:`), sent in the `Authorization`
header only. A deploy key or `none` only pushes.

- **Setup** (`exp:static:github setup`, and **Set up on GitHub** in step 4 of the wizard): creates the repository,
  public unless asked otherwise and empty, for the account or an organization; switches GitHub Pages on from the
  branch and folder, or from GitHub Actions; sets the custom domain and enforces HTTPS once GitHub has its
  certificate. Audited as `system.staticpublish.target` with the verb `github-setup`.
- **Status** (`exp:static:github status`, and the box **On GitHub** in History and Preview): the repository, GitHub
  Pages and the last Pages build (built, building, queued, failed), with what differs from the target's settings.
- **GitHub Actions**: with `Delivery=actions` (or `Extras[]=workflow`) the build writes
  `.github/workflows/static-publish-pages.yml`, which deploys the pushed files as they are. It needs the build at the
  root of the branch, and a token with the Workflows permission to push it.
- **Limits**: every push of a GitHub Pages target is checked first. A build over `MaxSiteBytes` or with a file over
  `MaxFileBytes` is not pushed (the push stays pending); above 90 % of the site limit, a file over 50 MB and a
  private repository are warnings.
- **GitHub App**: `Credential=app:<installation>` signs in with an installation token of the App set in
  `GitHubAppId` and `GitHubAppKey`, made with a JWT signed by the App's private key and kept for the process until
  it expires; it reaches git through `GIT_ASKPASS` like a token.
- **OAuth**: `exp:static:github signin` signs a user in with an OAuth App's device flow and keeps the token in the
  secret store.
- **DNS**: `exp:static:dns` and **Check the DNS** in step 6 compare a custom domain's records with GitHub Pages'
  addresses (`expStaticDnsCheck`).

Help: [Connect to GitHub](../../help/static-publishing/connect-to-github-token-key-or-app.md),
[Branch or GitHub Actions](../../help/static-publishing/choose-branch-or-github-actions.md),
[Use your own domain](../../help/static-publishing/use-your-own-domain.md),
[GitHub Pages limits and terms](../../help/static-publishing/github-pages-limits-and-terms.md).

## What needs a server

A static host cannot search, take a form or sign anybody in. Each build finds these parts (`expStaticDegrader`) and
gives them a fallback; the report of the build lists them as `degraded`:

- **Search** (`Search=enabled`): the site's search forms go to `search.html`, which searches an index per language in
  the browser (`expStaticSearchIndex`).
- **Information collectors** (`Forms=endpoint`): they post to the form endpoint `staticpublish/form` of the live site,
  which checks the origin, a token signed at build time and a rate limit, then hands the post to the normal
  information collection. With `Forms=hide` they show a short text with a link to the live page.
- **Comments, the basket, the login**: the short text, or left out.
- **Redirect pages** (`Extras[]=redirects`) for old addresses, custom aliases and URL wildcards without `*`.
- **Post-processing**: `minify`, `precompress`, `fingerprint`.

A target can publish itself: `Triggers[]=publish` (pages marked stale on content publish, built after
`PublishDebounce`) and `Triggers[]=schedule` (`Schedule`), both in the cronjob part `staticpublish`, with the e-mail of
`Notify[]` and a signed `Webhook` afterwards. Details: [Dynamic features](../../guides/static-publishing-dynamic-features.md),
[The form endpoint](../../specifications/6.0/static-publishing-form-endpoint.md). The audit records
`system.staticpublish.build` and `system.staticpublish.form` too.

## Privacy

- Builds run as the anonymous user: the crawl sends no cookie, and the database checks (which storage images may be
  copied) are made for the anonymous user.
- The build's requests are signed so the server sends no debug output; a page that still comes with debug output
  stops the build. Form tokens and session ids are cleaned; a page that cannot be cleaned, or whose response set a
  session cookie, stops the build before anything is written to the target's folder.
- Storage images are copied only for the published version of content the anonymous user may read. Images of drafts
  (`storage/images-versioned`) and files in `storage/original` are never copied from the storage; a download goes into
  the copy only when a page links it and the site hands it to an anonymous visitor.
- `ExcludePaths[]` and `ExcludeSections[]` leave pages out.
- Read the diff of a build (`exp:static:build --dry-run`, `exp:static:diff`) before you push it anywhere public.

## Platforms

- PHP 7.4 is usable, 8.0 and newer are fully supported.
- Every database Exponential runs on; the build reads only content and image tables.
- Apache with PHP-FPM, Velocity and any other web server: the build fetches pages over HTTP like a visitor.
- Cluster storage: files below `var/` are read through the cluster file handler.
- Windows: paths are written with `/` in the manifest whatever the system, page paths that cannot be file names on
  Windows (`:`, `*`, `?`, `CON` and the like) are skipped and reported, and the preview server is started without a
  shell.
