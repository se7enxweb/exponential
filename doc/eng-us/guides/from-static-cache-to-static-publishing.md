# From static cache to static publishing

This guide is for administrators who already use the static cache and wonder whether static publishing replaces it,
complements it, or is something else. It compares the two and shows how to move a site from one to the other.

[Guides](README.md) · Related: [Static cache generator](../features/6.0/static-cache-generator.md),
[Static publishing](../features/6.0/static-publishing.md),
[What static publishing is](../help/static-publishing/what-static-publishing-is.md)

## What you'll have at the end

A clear choice for each site between the static cache, static publishing or both, and a first static copy made with
the crawler you already know.

## Two features, one crawler

Both crawl the site as a visitor and write pages to files. They differ in where the files go and who serves them.

| | Static cache | Static publishing |
|---|---|---|
| Purpose | Speed: the web server answers anonymous visitors from files | A public copy that needs no Exponential at all |
| Where the files are | `static` below the installation, next to the live site | The target's folder, a git repository, pushed to a host |
| Who serves them | Your web server, with rewrite rules, in front of the live site | GitHub Pages or any static host |
| Links | unchanged, the site's own addresses | rewritten: relative, or below a base path |
| Files and images | served by the live site | copied into the copy |
| Search, forms, login | work: requests that miss the cache reach PHP | fall back (search index, form endpoint, hidden) |
| Cleaning | none needed, same server | debug output, form tokens and session ids taken out; pages that cannot be cleaned refused |
| Updates | on content publish, the cached files are rewritten | build and publish, by hand, on publish (debounced) or on a schedule |
| History | none | every publish a commit and tag; rollback |
| Started from | Setup > Cache > Static content cache, `makestaticcache.php` | Setup > Static publishing, `exp:static:*` |

The build of static publishing uses the static cache generator's crawler in an export mode: same link following,
same depth (`MaxDepth` falls back to `[CacheSettings] MaxCacheDepth`), but without a cookie and with the module views
left out.

## Which one

- **Keep the static cache** when the live site stays public and must keep search, forms, logins or a shop. It makes
  that site fast; static publishing would take those features away.
- **Use static publishing** when the public part can live without a server: a brochure, blog, documentation, a
  portfolio. The live installation can then stay internal, and the public site costs nothing to host.
- **Use both** when the live site stays public for members and shoppers, and a free static copy serves a second
  audience (a documentation mirror, an archive, a campaign site).

The settings live in the same file, `staticcache.ini`: `[CacheSettings]` for the cache, `[StaticPublishSettings]` and
`[Target_<id>]` for publishing. They do not affect each other.

## Moving a site over

1. Note the depth and page limit your static cache uses; the target's `MaxDepth` and `MaxPages` take the same role.
2. Make a target for the same siteaccess in Setup > Static publishing > **New target**.
3. Build with `--dry-run` and read the report: pages the static cache served happily may be **refused** here,
   because they start a session or carry a form token. Exclude them (`ExcludePaths[]`) or change them.
4. Preview the copy (`exp:static:serve`) and check the links (`exp:static:diff --check-links`).
5. Publish, point the domain at the host ([custom domains and DNS](static-publishing-custom-domains-and-dns.md)).
6. When the copy serves the public, the static cache of that siteaccess is no longer needed; switch it off when you
   are sure nothing still reaches the live site's public address.

## Next step

[Make a free website in 15 minutes](../help/static-publishing/make-a-free-website-in-15-minutes.md).
