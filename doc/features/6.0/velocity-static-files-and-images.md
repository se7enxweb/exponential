# Velocity static files and image processing

This page is for administrators who serve a site with Exponential Velocity and want to know how files and images are
delivered, cached and resized. Velocity answers a request for a file in the document root itself, without PHP: it sets
the type, validates with `ETag` and `Last-Modified`, compresses text, sends ranges, and converts or resizes images on
request. Applies to Exponential Velocity 0.0.4.x. Reference: [Engine settings](../../specifications/6.0/velocity-engine-settings.md),
[HTTP/2 and security](../../specifications/6.0/velocity-http2-and-security.md).

What you gain:

- Static files never wake a worker. They are answered in the server process, from an in-memory copy when hot
  ([worker pool](../../specifications/6.0/velocity-worker-pool.md)).
- Add `?w=400` to an image URL and the server resizes, picks AVIF or WebP for browsers that accept them, honours
  `Save-Data`, and caches the result. No image service, no template change.
- A stray `.env`, `.sql` or backup in the document root is not downloadable: only files with an allowed extension are
  served as they are, and the opt-in path lists in [security](../../specifications/6.0/velocity-http2-and-security.md)
  narrow it further.

## Resize an image in one request

```
/photos/hero.jpg?w=400         400 pixels wide, height in proportion
/photos/hero.jpg?h=300         300 pixels tall
/photos/hero.jpg?w=400&h=300   exactly 400 by 300 (stretches; give one dimension to keep proportions)
/photos/hero.webp              made from hero.png when no hero.webp exists
```

Check that a URL goes through the image pipeline:

```bash
curl -sI 'https://host/photos/hero.jpg?w=200'
```

Expected: `Vary: Accept` and `Cache-Control: public, max-age=31536000, immutable` in the answer.

How a request is handled:

1. The source is the file at the URL or, for a conversion, the first same-named file in the order `.png`, `.jpg`,
   `.jpeg`, `.gif`, `.webp`, `.bmp`.
2. `w` and `h` are held between 1 and 4096 and never above the source's own size (no upscaling).
3. The output format starts from the URL's extension, then becomes AVIF if the browser's `Accept` lists `image/avif`
   and PHP's GD can write it, else WebP if it lists `image/webp`. The response carries `Vary: Accept`.
4. `Save-Data: on` lowers the quality to 50 and caps a conversion without a size at 800 pixels wide; those variants are
   cached apart (`_sd`).
5. A cached result newer than the source is served; otherwise it is generated in the worker, written to the cache and
   served.

Transparency is kept for PNG, WebP, GIF and AVIF; for JPEG it is composited on white. An animated GIF is passed through
only when requested unresized; any resize or conversion yields the first frame.

Results are cached under `files/Q/cached/images/` of the application directory (beside the document root when
standalone), for example `files/Q/cached/images/photos/hero/400x.avif`. Replacing the source regenerates every variant
on its next request.

**Because of `immutable`, a browser that holds an old version keeps it for a year.** When you replace an image in
place, change its URL (`hero.jpg?w=400&v=2`; extra parameters do not change the server's cache key).

Requirements: PHP's GD extension (without it the original is served), WebP support in GD for WebP, and `imageavif()`
(PHP 8.1 with libavif) for AVIF. Check with `php --ri gd`.

| Configuration key | Default | Meaning |
|---|---|---|
| `Q.images.quality` | `82` | Encoding quality, 1 to 100 (PNG maps it to a compression level) |
| `Q.images.cache.maxSize` | `268435456` | Bytes of cached variants before the least recently accessed are removed (down to 75 percent of the limit; checked after every 50 images generated) |

## Serving files

A file whose extension is on the allowed list is sent straight from disk. The list covers text and data (`html`,
`htm`, `txt`, `md`, `json`, `xml`, `yaml`, `yml`, `csv`, `tsv`, `log`), code (`css`, `js`, `mjs`, `map`, `wasm`),
images (`png`, `gif`, `webp`, `jpg`, `jpeg`, `svg`, `bmp`, `ico`, `avif`), fonts (`woff`, `woff2`, `ttf`, `otf`),
media (`mp3`, `wav`, `ogg`, `mp4`, `webm`), `pdf` and `zip`. `Q.webserver.extensions` replaces it.

| Response header | Value |
|---|---|
| `Content-Type` | from the extension |
| `ETag`, `Last-Modified` | from the file's time and size; a matching `If-None-Match` or `If-Modified-Since` gets `304` and no body |
| `Cache-Control` | `public, max-age=0, must-revalidate` unless `Q.web.static.maxAge` is set |
| `Accept-Ranges` | byte ranges are honoured |

### How long a browser may keep a file

By default a browser keeps a file but asks before every reuse (one cheap `304`). To let versioned assets live longer:

```json
{ "Q": { "web": { "static": { "maxAge": 86400 } } } }
```

Set `maxAge` only when asset URLs carry a version or hash; the service worker and manifest stay fresh on their own.

| Configuration key | Default | Meaning |
|---|---|---|
| `Q.web.static.maxAge` | `0` | Seconds sent as `Cache-Control: public, max-age=N`; `0` sends `max-age=0, must-revalidate` |
| `Q.web.static.revalidate` | `sw.js`, `service-worker.js`, `*.webmanifest`, `manifest.json` | `fnmatch` patterns that are **always** revalidated whatever `maxAge` is, because they decide how a client behaves afterwards; `[]` turns the exception off |
| `Q.web.static.paths` | all allowed files | Opt-in patterns a path must match to be sent as it is ([security](../../specifications/6.0/velocity-http2-and-security.md)) |
| `Q.webserver.fileCache.maxSize`, `.maxFile`, `.checkInterval` | `64MB`, `1MB`, `1` | The in-memory copy of hot files: total size, largest file, seconds between modification checks |

## Compression

Text types under 5 MB are compressed with gzip when `Accept-Encoding` allows it (`q=0` counts as refused), with
`Vary: Accept-Encoding`. To compress each file once and share the result between workers, switch on the precompression
cache:

```json
{ "Q": { "webserver": { "precompress": { "enabled": true, "maxFiles": 1000, "minSize": 1024, "level": 6 } } } }
```

| Key (`Q.webserver.precompress`) | Default | Meaning |
|---|---|---|
| `enabled` | `false` | Turn it on |
| `maxFiles` | `1000` | Keep the most recently served this many files |
| `minSize` | `1024` | Do not compress files smaller than this (bytes) |
| `level` | `6` | gzip level |
| `dir` | `qbixserver-precompress` in the system temporary directory | Where the `.gz` files live; set it to a directory of your own for a service that has a private temporary directory |
| `fileMode`, `dirMode` | umask, `0755` | Permissions (since 23 September 2026 the default directory is no longer world-readable by accident) |

## Directory listings and galleries

A directory without an index file is listed in a responsive page; image folders show a gallery with previews. The
document root itself can be listed like any other directory (21 September 2026). If you do not want listings, give the
directory an index file, or serve through the front controller so unlisted paths go to the application.

## Limits

- One request that generates a new size pays the encoding cost (AVIF is slowest). Two simultaneous first requests for
  the same size both generate it.
- No `fit`, `crop`, `q` or `dpr` parameters: other query parameters are ignored.
- Not verified: whether an Exponential installation's image URLs (image aliases under `var/<site>/storage/images/`)
  reach this pipeline depends on its `Q.web.static.paths` list. Check with `curl -sI 'https://host/<image URL>?w=200'`
  and look for `Vary: Accept` in the answer.

## Related pages

- [Velocity web server](velocity-web-server.md), [response cache](velocity-response-cache.md) (pages, not files), [packages and binaries](velocity-packages-and-binaries.md) (the binaries bundle GD), [uwebserver](velocity-uwebserver.md)
- [Static cache generator](static-cache-generator.md)
- [Changelog: Exponential Velocity engine](../../changelogs/extensions/exponential-velocity.md)
- History: [July 2026](../../history/velocity/2026-07.md) (directory listings, galleries, `Save-Data`), [22 September](../../history/velocity/2026-09b.md) (static lifetimes, compressed storage), [23 September](../../history/velocity/2026-09c.md) (cache permissions)
