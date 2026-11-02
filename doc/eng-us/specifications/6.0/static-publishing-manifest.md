# Specification: the static publishing manifest

This page is the normative description of the manifest `exp-static-manifest/1` that every static publishing build of
Exponential 6.0.15 writes, and of how the build, the diff, the preview, the publish and the rollback use it. The
format of the rest of a build is in [The build format](static-publishing-build-format.md); this page goes deeper into
the one file every other part relies on.

[Specifications](README.md) · Related: [Static publishing](../../features/6.0/static-publishing.md),
[The build format](static-publishing-build-format.md), [The form endpoint](static-publishing-form-endpoint.md)

## Files

| File | Written by | Meaning |
|---|---|---|
| `.static-publish/manifest.json` | every build that is not a dry run | the files of the target's folder as this build left them |
| `.static-publish/manifest.previous.json` | the same build, before it writes the new one | the manifest of the build before |

`manifest.json` is committed and pushed with every publish, so the repository holds the manifest of each publish
next to its files. `manifest.previous.json` stays in the local folder only (publishing adds it to `.gitignore`).

## Encoding

- JSON in UTF-8, pretty-printed, slashes and Unicode not escaped.
- `\n` line ends and one final newline, on every system.
- The keys of `meta` are sorted. The same build gives the same bytes on Linux, macOS and Windows.

## Structure

```json
{
    "format": "exp-static-manifest/1",
    "meta": { "base_path": "/my-site/", "built_at": "2026-11-01T09:30:00+00:00", "links": "relative",
              "public_url": "https://owner.github.io/my-site/", "run_id": "20261101-093000-4f2a1c",
              "scope": "full", "siteaccess": "site", "target": "github-public" },
    "files": {
        "about-us/index.html": { "kind": "page", "sha256": "…", "size": 18234, "url": "/about-us",
                                 "node": 59, "modified": 1761984000 }
    }
}
```

| Key | Type | Meaning |
|---|---|---|
| `format` | string | always `exp-static-manifest/1`; a reader refuses any other value |
| `meta.target`, `meta.siteaccess` | string | the target id and the siteaccess it built |
| `meta.run_id` | string | the run of the build (the job page and `exp_static_run`) |
| `meta.built_at` | string | ISO 8601 in UTC |
| `meta.links`, `meta.base_path`, `meta.public_url` | string | the link settings the build used |
| `meta.scope` | string | `full`, or `partial` for a build of chosen pages (`--paths`, `--nodes`, the sidebar's Publish) |
| `files` | object | one entry per file, keyed by its path |

## Paths

A path is relative to the target's folder (to `<folder>/docs` with `Folder=docs`), with `/` separators, no leading
slash, no drive letter, no empty, `.` or `..` segment. A path of any other form is rejected when the manifest is read
or written. The manifest never lists itself, `.git` or files the build did not write (a README you added).

## A file entry

| Key | Type | Meaning |
|---|---|---|
| `kind` | string | `page`, `file` (named by a page or style sheet), `storage` (a storage image of published content that no page names), `extra` (`.nojekyll`, `CNAME`, `404.html`, `robots.txt`, `sitemap.xml`, search, redirects, workflow, README, LICENSE) |
| `sha256` | string | the SHA-256 of the bytes written, lower-case hex |
| `size` | integer | bytes |
| `url` | string | the path on the site it was fetched from; empty for extras |
| `node` | integer or null | the node of a page, when known |
| `modified` | integer or null | the last modification of the page's content object, Unix time, when known |

All six keys are always present. A reader requires `kind`, `sha256` and `size` and treats a missing `url`, `node` or
`modified` as empty or null.

## How the manifest is used

- **Diff.** Two manifests are compared by path and `sha256`: a path only in the new one is added, one in both with a
  different hash is changed, one only in the old one is removed. Sizes give the bytes added and removed. This is
  what Preview, `exp:static:diff` and the report show.
- **Applying a build.** New and changed files are copied, a listed file missing from the folder is copied again, files
  the previous manifest listed and this one does not are removed, and folders left empty go too. Files no manifest
  lists are never touched.
- **Partial builds.** A `partial` build starts from the entries of the manifest before it and replaces only the pages
  it built and the files they name, so the result still describes the whole folder.
- **Verify.** `exp:static:diff --verify` hashes the folder and compares it with the manifest.
- **Pages of a publish.** After a commit, the `page` entries (path, `url`, `node`) are written to the table
  `exp_static_page`; a content publish marks its node's rows stale there, which the dashboard and the sidebar panel show.
- **Rollback.** The manifests before and after give the counts of what a rollback changes.

## Compatibility

Later versions may add keys to `meta` and to a file entry; they never rename or remove one, and never change the
meaning of a value. A change that would do so takes a new `format` value.
