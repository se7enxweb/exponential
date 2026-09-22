# Middle-Out: shared-dictionary compression

**Introduced:** Exponential CMS 6.0.15
**Status:** implemented, tested, **off by default** — and this document is
mostly about why, and exactly when to turn it on.

Named after the compression in HBO's *Silicon Valley*, which is fiction. The
technique is real and predates the show: it is the idea behind Brotli's built-in
dictionary and the late SDCH, and zlib has supported it since before either.

---

## What it does

gzip's window is 32 KB and it starts empty for every document. A page cache is
full of documents that are mostly identical — the same head, navigation, footer
and asset URLs — so the hundredth page pays full price for a header the
ninety-nine before it also contained.

A dictionary is a block of bytes the compressor may reference before it has seen
them. Give it the site's common markup and each document compresses against the
whole site instead of only against itself.

Measured on this installation's own 273 cached pages:

```
plain gzip        1,626,195 bytes
with dictionary   1,290,140 bytes
saved               336,055 bytes   20.7% smaller
round-tripped       273 of 273 exactly
dictionary           32 KB, built in 140 ms
```

On a synthetic corpus with a large shared shell it reaches 80.4%. On documents
that share nothing it reports 1.00× and claims no win.

---

## Why it is off

Switched on against this cache, it compressed **1 of 272 entries**.

The cache stores bodies in **wire form** — already gzipped, deliberately, so a
hit needs no compression work at all — and a dictionary cannot shrink gzip
output. The 20.7% above is what it saves on the *plain* HTML.

Capturing it means storing plain HTML and compressing on the way out. Measured:

```
storage saved                      377,991 bytes  (23.5%)
decompress with the dictionary       0.080 ms
re-gzip at level 6                   0.533 ms
total added per hit                  0.612 ms

a cached hit: 0.36 ms  ->  0.97 ms
memory saved across the whole cache: 369 KB
```

369 KB bought with 0.61 ms on every request, on a machine with 24 GB free. That
is trading something abundant for something scarce, which is the wrong
direction.

Note where the cost actually is: **decompression is 0.080 ms; the re-gzip is
0.533 ms**, 6.7× more. The dictionary is not what makes this expensive.

---

## When it is worth switching on

### 1. A cache larger than the memory available to it

This is the case it is really for. The choice then is not "wire form or
dictionary form" but "dictionary form in memory, or wire form on disk" — and a
disk read costs far more than 0.61 ms, while a full render costs 1382 ms.

```
against a disk read:   WORTH IT     0.61 ms added, 1.64 ms saved
against a render:      WORTH IT     by 2,258x
```

How many more pages the same memory holds:

| memory | wire form | with dictionary | extra |
|---|---|---|---|
| 64 MB | 11,302 | 14,772 | +3,470 |
| 256 MB | 45,208 | 59,089 | +13,881 |
| 1 GB | 180,835 | 236,356 | +55,521 |
| 4 GB | 723,340 | 945,427 | +222,087 |
| 16 GB | 2,893,362 | 3,781,711 | +888,349 |

**The rule: it pays exactly when the extra pages it keeps in memory are pages
that would otherwise have been evicted.** This installation holds 271 pages in
1.5 MB and evicts nothing, so the extra room buys nothing.

### 2. Compression Dictionary Transport — dictionaries on the wire

Since 2024 there has been a real standard for sending a dictionary-compressed
response to a browser: the server advertises a dictionary with
`Use-As-Dictionary`, the client offers it back with `Available-Dictionary`, and
the response carries `Content-Encoding: dcb` (Brotli) or `dcz` (Zstandard).
Chrome has shipped it since version 130.

This is Middle-Out doing what it was always meant to do — compressing what goes
to the visitor, not what sits on the server — and the gains on a site with a
shared shell are far larger than 20%, because the visitor's second page is
compressed against their first.

**It needs Brotli or Zstandard. The dictionary transport encodings are defined
only for those two; there is no gzip equivalent and there will not be one.**
Checked on this installation:

```
brotli (dcb): MISSING
zstd   (dcz): MISSING
```

Installing `ext-brotli` or `ext-zstd` is what unlocks this, and it is the single
change that would move Middle-Out from a curiosity to the most valuable thing
in this document.

### 3. A cache that holds uncompressed bodies

An API cache of JSON served to clients that do not ask for compression, for
instance. There is no gzip in the way, so the dictionary applies directly and
costs only the 0.080 ms to undo.

---

## Configuration

```ini
[ServerSettings]
MiddleOutCompression=disabled
```

Build the dictionary before switching it on. Without one, entries are stored the
ordinary way and nothing breaks.

```bash
php ai/bin/one/build_middle_out_dictionary_from_cache.php --dry-run
php ai/bin/one/build_middle_out_dictionary_from_cache.php
```

The builder refuses to install a dictionary that does not beat plain gzip, or
one that cannot read back everything it wrote.

Rebuilding invalidates every entry written against the old dictionary. That is
safe and deliberate: every payload carries a CRC of the dictionary it was built
against, and decompression **refuses rather than guessing** when they disagree.
An entry that cannot be read is a cache miss, which is slow; one read against
the wrong dictionary would be a corrupted page.

`clear()` deliberately does not delete the dictionary. It lives in the cache
directory but is not an entry, and deleting it would leave every later write
uncompressed until somebody noticed — the kind of silent regression that
survives for months.

---

## Measuring it yourself

```bash
php ai/bin/one/measure_middle_out_on_live_cache.php      # what it saves here
php ai/bin/one/measure_middle_out_viability_tradeoff.php # what it would cost
php ai/bin/one/measure_middle_out_crossover_point.php    # when it starts paying
php tests/unit-middle-out.php                            # 29 cases
```
