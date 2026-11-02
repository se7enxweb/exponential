# Specification: the static publishing build format

This page is the normative description of what a static publishing build of Exponential 6.0.15 writes: the folder,
where each page and file goes, how links are rewritten, what is taken out of a page and what makes a page refused,
the manifest `exp-static-manifest/1` and the machine output `exp-static/1` of the `exp:static` commands. Later parts
of static publishing (git, the administration, GitHub) read these files; they may add keys, never rename or remove
one.

[Specifications](README.md) · Related: [Static publishing](../../features/6.0/static-publishing.md),
[Host static sites on GitHub Pages](../../guides/host-static-sites-on-github-pages.md)

## The folders

| Path | What it is |
|---|---|
| `var/<site>/<WorkDir>/` | The work directory of all targets (`[StaticPublishSettings] WorkDir`, default `static-publish`; absolute when it starts with `/` or a drive letter) |
| `var/<site>/<WorkDir>/<target>/` | The target's folder: the site as a static host serves it, plus `.static-publish/` |
| `var/<site>/<WorkDir>/.staging/<target>-<run id>/` | A build in progress; removed when the build ends (kept with `--keep-staging`) |

A target id matches `^[a-z0-9][a-z0-9_-]{0,49}$`. A run id is `<YYYYMMDD>-<HHMMSS>-<6 hex digits>` (UTC).

A build replaces the target folder's content only when it succeeded and is not a dry run: it copies the files that
are new or changed, copies again a listed file that is missing, removes the files the previous manifest listed and
this one does not (and the folders that leaves empty), and writes the manifests. A file that no manifest lists (a
`.git` folder, a README added by hand) is never touched. A partial build (`--paths`, `--nodes`) removes nothing.

## Pages

A page is a response of the crawl with status 2xx and type `text/html`, at an address below the site path (the
siteaccess prefix and the folder of the installation, which are not part of the page path). Its page path is the
address's path without the site path, without `/index.php` and without a trailing slash; the front page is `/`.

| Page path | File |
|---|---|
| `/` | `index.html` |
| `/about-us`, `/about-us/` | `about-us/index.html` |
| `/old/page.html` (ends in `.html` or `.htm`) | `old/page.html` |
| `/%C3%BCber%20uns` | `über uns/index.html` (decoded) |

A page is **skipped** (not written, reported under `skipped`) when its address has a query string, or when a segment
of its path cannot be a file name on every system: empty, `.` or `..`, holding `< > : " \ | ? *` or a control
character, ending in a dot or a space, or a reserved device name (`CON`, `PRN`, `AUX`, `NUL`, `COM1` to `COM9`, `LPT1`
to `LPT9`). A page is **excluded** by `ExcludePaths[]` (shell patterns on the page path, `*` matches any part, a
pattern ending in `/*` matches the page itself too) or `ExcludeSections[]` (the section of the page's node).

## What is taken out

Every page, and the site's 404 page, is stripped of:

| Removed | Recognised by |
|---|---|
| The debug report | from `<div id="debug">` to `<a id="debug-end"></a>`, with a `<script>` right after it that names `debug` |
| Template debug comments | `<!-- START: including template: ... -->`, `<!-- STOP: including template: ... -->` |
| Form tokens | `<input>` named `ezxform_token`; `<meta name="csrf-param">`, `csrf-token`, `csrf-token-x`; the hidden `<span id="ezxform_token_js">` |
| Session ids in addresses | `eZSESSID…=` and `PHPSESSID=` parameters (and the `?` or `&` left at the end) |
| Content Security Policy nonces | `nonce="…"` attributes; a CSP `<meta http-equiv>` that names a `nonce-` |

## What makes a page refused

After stripping, a page is **refused** when its response set a cookie whose name starts with `eZSESSID` or
`PHPSESSID`, or the page still holds `<div id="debug">`, `id="debug-end"`, a template debug comment, `id="exp-debug-summary"`,
an `ezxform_token` input, a `csrf-token` `<meta>` or a session id in an address. A refused page is listed under
`refused` with its reasons, the build exits 1 and the target's folder is not changed.

## Links

Addresses are rewritten in the attributes `href`, `src`, `srcset`, `imagesrcset`, `data-srcset`, `poster`, `action`,
`formaction`, `data-src`, `data-href`, `data-background`, `background`, `cite`, `longdesc`, `data`, `manifest`; in
`url()` of `style` attributes and `<style>` blocks; in `content` of `<meta>` tags whose `property` or `name` is
`og:url`, `og:image`, `og:image:url`, `og:image:secure_url`, `og:audio`, `og:video`, `og:video:url`, `twitter:image`,
`twitter:image:src`, `twitter:url`, `msapplication-TileImage` or `thumbnail`, or whose `itemprop` is `image`, `url`
or `thumbnailUrl`; in the absolute addresses (JSON strings starting with `http://` or `https://`, `\/` escaped or not)
of structured data, `<script type="application/ld+json">`, which are written absolute; and in style sheets, in `url()`
and `@import`. The content of any other `<script>` and of comments is never changed. Attribute values are decoded before and encoded after rewriting; a tag with nothing to rewrite keeps its
bytes.

An address belongs to the site when it is root-relative, relative (resolved against the document's own address), or
absolute on one of the build's origins: the address the build fetched from, the siteaccess's `SiteURL` and `LiveURL`.
On the host of `SiteURL` (when it is not the address the build fetched from) the site is also below the path of
`SiteURL` there: a site fetched below `/site` but served at the root of `www.example.com` owns
`https://www.example.com/about` as well as `https://www.example.com/site/about`.
Dot segments are resolved and `/index.php` is removed. Then:

| Kind | Rule | Written as |
|---|---|---|
| File of the host | the path starts with `var/`, `design/`, `extension/<name>/design/`, `share/`, `lib/ezjscore/`, or is `favicon.ico` or `apple-touch-icon*` | the path in the copy; the query and fragment kept |
| File of the site | below the site path, with a file extension of a known file type (images, style sheets, scripts, fonts, media, documents, archives, `json`, `xml`, `rss`, `ics`, `vcf`) | the path below the site path |
| Page | below the site path, anything else | the page's address in the copy (`about-us/`, `''` for the front page, the file for `*.html`), the fragment kept |
| Outside | not below the site path | the live site |

A page the build does not hold (not crawled, skipped, excluded, refused, or with a query string) and a file that could
not be collected are written as the live site's address: `LiveURL` (default `https://` and the host of `SiteURL`)
followed by the original path, query and fragment. Without a live address they stay unchanged. Addresses on other
hosts, fragments of the same page (`#top`), a lone query (`?x`), and `mailto:`, `tel:`, `javascript:`, `data:` and
other schemes are never changed.

With `OutsideLinks=unlink` a link (`<a href>`) to such a page, and with `AdminLinks=unlink` a link whose path starts
with the path of an administration siteaccess, is taken out instead: the `<a>` and its closing tag become a `<span>`
with the link's `class` attribute, or nothing when it had none, so only its content stays. A `<link>` whose `rel` only
names navigation (`alternate`, `search`, `home`, `next`, `prev` ...) is dropped. Files, images, style sheets, scripts,
form actions and other hosts are never taken out. An administration path is never held as a page of the site. The
report counts both (`links_removed_outside`, `links_removed_admin`).

**Relative links** (`Links=relative`) are relative to the folder of the file that holds them: `about-us/` from
`index.html`, `../` from `about-us/index.html` to the front page, `./` to the folder's own page. **Base-path links**
(`Links=base-path`) are `BasePath` followed by the address in the copy: `/my-site/about-us/`. With a `PublicURL`,
`<link rel="canonical|alternate|shortlink">` and the social card `<meta>` tags are absolute: `PublicURL` followed by
the address in the copy. `404.html` is always written with base-path links.

## Files

A file the pages or style sheets name is read from the installation when its path is below a folder the web server
serves as files:

- `design/<design>/(stylesheets|images|javascript|javascripts|fonts|lib|icons)/`
- `extension/<name>/design/<design>/(stylesheets|images|javascript|javascripts|fonts|lib|flash|icons)/`
- `share/icons/`
- `var/[<site>/]storage/images/`
- `var/[<site>/]cache/(public|texttoimage)/`
- `favicon.ico`, `apple-touch-icon*`

Paths below `var/` are read through the cluster file handler. Any other file is requested from the site as the
anonymous user and kept when the answer is 2xx and is not an HTML page (unless the file is one). A path that ends
in `.php`, `.phtml`, `.phar`, `.ini`, `.inc`, `.tpl`, `.sql`, `.sh`, `.env`, `.lock`, `.htaccess`, `.htpasswd`,
`.bak`, `.orig` or `.swp`, or holds a `.git`, `.svn` or `.env` segment, is never copied. Style sheets are rewritten
after the files they name are collected, up to 5 style sheets deep.

With `Assets=referenced-and-storage`, the build also copies every file of the table `ezimagefile` below
`var/<site>/storage/images/` whose content object attribute belongs to the current version of a published object
(`status = 1`) the anonymous user may read. Its kind in the manifest is `storage`; a file a page also names is of kind
`file`.

## Extras

| File | Written when | Content |
|---|---|---|
| `.nojekyll` | `Extras[]=nojekyll` | empty |
| `CNAME` | `CustomDomain` is set | the domain and a newline |
| `404.html` | `Extras[]=404` | the site's answer to a missing address (`<site path>/static-publish-not-found-<run id>`) when it is a 404 HTML page that passes the checks, stripped and rewritten with base-path links; else a plain page linking the base path |
| `robots.txt` | `Extras[]=robots` | `User-agent: *`, `Allow: /`, and `Sitemap: <PublicURL>sitemap.xml` when a sitemap is written |
| `sitemap.xml` | `Extras[]=sitemap` and a `PublicURL` | every page's absolute address, sorted, with `<lastmod>` (UTC date) when its content's modification time is known |

An extra replaces a page or file of the same name, with a warning.

## The manifest: `exp-static-manifest/1`

`.static-publish/manifest.json` in the target's folder; the one before it is kept as
`.static-publish/manifest.previous.json`. JSON, keys sorted, `/` separators, `\n` line ends, a final newline: the
same build gives the same bytes on every system.

```json
{
    "format": "exp-static-manifest/1",
    "meta": {
        "base_path": "/my-site/",
        "built_at": "2026-11-01T09:30:00+00:00",
        "links": "relative",
        "public_url": "https://owner.github.io/my-site/",
        "run_id": "20261101-093000-4f2a1c",
        "scope": "full",
        "siteaccess": "site",
        "target": "github-public"
    },
    "files": {
        "about-us/index.html": {
            "kind": "page",
            "sha256": "…",
            "size": 18234,
            "url": "/about-us",
            "node": 59,
            "modified": 1761984000
        }
    }
}
```

| Key of a file | Meaning |
|---|---|
| `kind` | `page`, `file` (named by a page or style sheet), `storage` (a storage image no page names), `extra` |
| `sha256` | of the bytes written |
| `size` | in bytes |
| `url` | the host path it came from (empty for extras) |
| `node` | the node of a page, when known, else `null` |
| `modified` | the modification time of a page's content object (Unix time), when known, else `null` |

## The machine output: `exp-static/1`

With `--format=json` every `exp:static` command prints one JSON object with `schema` (`exp-static/1`), `command`,
`mode`, `errors`, `warnings` and `exit`, and the keys of its mode. `exp:static:build --report=<file>` writes the same
object to a file.

| Key of a build | Meaning |
|---|---|
| `mode` | `build` or `dry-run` |
| `scope` | `full` or `partial` |
| `target`, `siteaccess`, `run_id`, `started_at`, `duration_ms`, `directory` | the build |
| `links` | `mode`, `base_path`, `public_url` |
| `counts` | `pages`, `pages_refused`, `pages_skipped`, `pages_excluded`, `files`, `storage`, `files_failed`, `extras`, `links_rewritten`, `links_live`, `links_external`, `links_removed_outside`, `links_removed_admin`, `site_broken_links`, `bytes`, `stripped` (by what was taken out) |
| `diff` | `added`, `changed`, `removed`, `unchanged` (counts), `bytes_added`, `bytes_removed`, `files` (the paths, at most 500 of each) |
| `pages` | each page: `path`, `file`, `bytes`, `node`, `stripped` |
| `refused` | each refused page: `path`, `url`, `reasons` |
| `skipped` | each skipped page: `path`, `reason` |
| `files_failed` | each file not collected: `file`, `url`, `error` |
| `link_check` | `mount`, `files`, `links`, `broken` (at most 200: `file`, `link`, `resolved`), `broken_count`, `skipped` |
| `limits` | `bytes`, `max_site_bytes`, `max_file_bytes`, `too_large` |
| `applied` | whether the target's folder was changed |

`exp:static:targets` reports `enabled` and `targets` (each target's settings with `id`, `name`, `directory`,
`built_at`, `pages`, `bytes`, `errors`, `warnings`); `exp:static:diff` reports `diff` (the paths of `added`,
`changed`, `removed`, `unchanged`, `first_build`), `verify` (`missing`, `changed`, `unlisted`) and `link_checks`.

| Exit | Meaning |
|---|---|
| 0 | done |
| 1 | failed: an error in a target, a refused page, nothing exported, a problem found by `--verify` or `--check-links` |
| 2 | usage: a wrong option or argument |
| 3 | the database is not reachable |
| 4 | internal error |

## The link check

Every `*.html` and `*.css` file of the folder (outside `.git` and `.static-publish`) is read; each address in
`href`, `src`, `poster`, `data-src`, `srcset`, `data-srcset`, `style` and `<style>`, or in `url()` and `@import`, is
resolved against the address the file is served at when the folder is mounted at a path, without query and fragment.
It must name a file of the folder; an address ending in `/` names its `index.html`, a folder without its slash is
found when it has an `index.html`. Addresses on other hosts, protocol-relative addresses, fragments, lone queries and
schemes are not checked. `404.html` is resolved against the mount and is read only below the base path it was built
for.
