# Middle-Out: shared-dictionary compression

Read this page if you run Velocity's response cache and wonder whether to switch on `MiddleOutCompression`. It was
introduced in Exponential 6.0.15, is implemented and tested, and is **off by default**. Most of this page explains why
it is off, and exactly when to turn it on. For a typical site the answer is: leave it off.

The name comes from the compression in the TV series *Silicon Valley*, which is fiction. The technique is real and
older than the show: it is the idea behind Brotli's built-in dictionary and the late SDCH, and zlib has supported it
for longer than either.

## In short

| | |
|---|---|
| What changed | New opt-in setting `MiddleOutCompression` that stores response cache entries compressed against a shared dictionary. |
| Who is affected | Nobody unless you switch it on. |
| How to check | `grep -n "MiddleOutCompression" settings/velocity.ini settings/override/velocity.ini.append.php` |
| How to decide | Switch it on only when the cache is larger than the memory it has, or holds uncompressed bodies (see "When it is worth switching on"). |

## What it does

gzip's window is 32 KB, and it starts empty for every document. A page cache is full of documents that are mostly
identical (the same head, navigation, footer and asset URLs), so the hundredth page pays full price for a header the
ninety-nine before it also contained.

A dictionary is a block of bytes the compressor may reference before it has seen them. Give it the site's common
markup, and each document compresses against the whole site instead of only against itself.

Measured on one installation's own 273 cached pages:

```
plain gzip        1,626,195 bytes
with dictionary   1,290,140 bytes
saved               336,055 bytes   20.7% smaller
round-tripped       273 of 273 exactly
dictionary           32 KB, built in 140 ms
```

On a synthetic corpus with a large shared shell it reaches 80.4%. On documents that share nothing it reports 1.00×
and claims no win.

## Why it is off

Switched on against that cache, it compressed **1 of 272 entries**.

The cache stores bodies in **wire form**: already gzipped, on purpose, so a hit needs no compression work at all. A
dictionary cannot shrink gzip output. The 20.7% above is what it saves on the *plain* HTML.

Capturing it means storing plain HTML and compressing on the way out. Measured:

```
storage saved                      377,991 bytes  (23.5%)
decompress with the dictionary       0.080 ms
re-gzip at level 6                   0.533 ms
total added per hit                  0.612 ms

a cached hit: 0.36 ms  ->  0.97 ms
memory saved across the whole cache: 369 KB
```

That is 369 KB bought with 0.61 ms on every request, on a machine with 24 GB free: trading something abundant for
something scarce, the wrong direction.

Note where the cost is: **decompression takes 0.080 ms, the re-gzip 0.533 ms**, 6.7 times more. The dictionary is not
what makes this expensive.

## When it is worth switching on

### 1. A cache larger than the memory available to it

This is the case it is really for. The choice is then not "wire form or dictionary form" but "dictionary form in
memory, or wire form on disk", and a disk read costs far more than 0.61 ms, while a full render costs 1382 ms.

```
against a disk read:   WORTH IT     0.61 ms added, 1.64 ms saved
against a render:      WORTH IT     by 2,258x
```

How many more pages the same memory holds:

| Memory | Wire form | With dictionary | Extra |
|---|---|---|---|
| 64 MB | 11,302 | 14,772 | +3,470 |
| 256 MB | 45,208 | 59,089 | +13,881 |
| 1 GB | 180,835 | 236,356 | +55,521 |
| 4 GB | 723,340 | 945,427 | +222,087 |
| 16 GB | 2,893,362 | 3,781,711 | +888,349 |

**The rule: it pays exactly when the extra pages it keeps in memory are pages that would otherwise have been evicted.**
The measured installation held 271 pages in 1.5 MB and evicted nothing, so the extra room bought nothing.

### 2. Compression Dictionary Transport: dictionaries on the wire

Since 2024 there is a real standard for sending a dictionary-compressed response to a browser. The server advertises a
dictionary with `Use-As-Dictionary`, the client offers it back with `Available-Dictionary`, and the response carries
`Content-Encoding: dcb` (Brotli) or `dcz` (Zstandard). Chrome has shipped it since version 130.

This would be Middle-Out compressing what goes to the visitor, not what sits on the server, and the gains on a site
with a shared shell are far larger than 20%, because the visitor's second page is compressed against the first.

**It needs Brotli or Zstandard.** The dictionary transport encodings are defined only for those two; there is no gzip
equivalent. On the measured installation both were missing:

```
brotli (dcb): MISSING
zstd   (dcz): MISSING
```

Installing `ext-brotli` or `ext-zstd` is what unlocks this.

### 3. A cache that holds uncompressed bodies

For example, an API cache of JSON served to clients that do not ask for compression. There is no gzip in the way, so
the dictionary applies directly and costs only the 0.080 ms to undo.

## Configuration

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/velocity.ini` | `CacheSettings` | `MiddleOutCompression` | `disabled` | installation |

The key used to live in `[ServerSettings]`; a value there still works, and a value in `[CacheSettings]` wins.

```ini
[CacheSettings]
MiddleOutCompression=enabled
```

**Build the dictionary before you switch it on.** Without one, entries are stored the ordinary way and nothing breaks.
The dictionary is the file `middle-out.dict` in the response cache directory. It is built from documents the cache
holds, with `Q_WebServer_MiddleOut::buildDictionary()` from the Velocity engine
(`vendor/se7enxweb/exponential-velocity/src/Q/WebServer/MiddleOut.php`); Exponential ships no command for it. Install a
dictionary only when it beats plain gzip and reads back everything it wrote; the builder used for the measurements
above refused any dictionary that failed either check.

Rebuilding the dictionary invalidates every entry written against the old one. That is safe and deliberate: every
payload carries a CRC of the dictionary it was built against, and decompression **refuses rather than guesses** when
they disagree. An entry that cannot be read is a cache miss, which is slow; one read against the wrong dictionary
would be a corrupted page.

Clearing the cache (`clear()`) does not delete the dictionary. It lives in the cache directory but is not an entry,
and deleting it would leave every later write uncompressed until somebody noticed.

## Tests

The engine's unit test covers 29 cases:

```bash
php vendor/se7enxweb/exponential-velocity/tests/unit-middle-out.php
```

## Related pages

- [Response cache and navigation](response-cache-and-navigation.md)
- [Velocity response cache](../../features/6.0/velocity-response-cache.md)
- [HTTP caching for anonymous visitors](http-caching.md)
- [Velocity engines](velocity-engines.md)
