# How the work was done, and what comes next

The LoveStack features came into Exponential in nine phases, in this order. All of them are done
and part of Exponential 6.0.15.

| Phase | Name | Delivered |
|---|---|---|
| 0 | Baseline and the LoveStack check | a LoveStack-shaped test database (Exponential's schema plus LoveStack's schema steps 1 to 4) on which the same content operations were run as on an Exponential database, with the same results; `./console exp:lovestack check` |
| 1 | Security and sessions | L01–L06 and LoveStack's `SameSite` setting name: the policy for node data calls, the registration policy, logout that ends the session, unique logins with a check command, image uploads checked by content |
| 2 | Diagnostics | L07–L12: the deprecated debug level and log, the log size setting, the template operator warning, the template edit URL, database error numbers, the clear-all warning |
| 3 | Content and admin | L13–L21: audit of location changes, site data in templates and the administration, the Copy button, required alternative text, trimmed text lines, name pattern resolver classes, the class filter default, two template fixes |
| 4 | Rich text editor | L22–L24: hidden custom tags, custom tag controls for TinyMCE 3, TinyMCE 8 and Tiptap, the sub/sup dialog |
| 5 | Developer APIs and settings | L25–L31: `eZURI::build()`, the per-message mail transport, `rawurlencode`, settings includes, error page headers, the ezjscore search order, the `layout` design key |
| 6 | URLs and search | L32–L36: decode once, the double quote in IRI aliases, the search integer clamp, relation indexing, the hide cronjob and the search index |
| 7 | PHP 8 review | LoveStack's PHP 7.2–8.3 fixes reproduced on PHP 8.5 and 8.1, fixed where Exponential still failed ([PHP8-REVIEW.md](PHP8-REVIEW.md)) |
| 8 | Migration, tests and documentation | `./console exp:lovestack migrate`; LoveStack's own test cases brought into the suite ([TESTS-MAPPING.md](TESTS-MAPPING.md)); a test of every feature and of the defaults an upgraded site keeps; these pages |

## How the order was chosen

- Phase 0 first: it showed whether a LoveStack database needs any change (it does not, apart
  from the version row that `exp:lovestack migrate` records).
- Phase 1 next: security.
- Phases 2, 3 and 5 did not depend on each other.
- Phase 4 built on the rich text editor work of the YUI replacement, so the editor plugin needs
  no YUI.
- Phase 6 came last among the features: it is the only one that can change URLs and search
  results, so its two URL options stay off unless you switch them on.

## How behaviour is protected

- A feature that could change behaviour on an existing site is off after the upgrade, or takes
  effect on new installations only; the overview says how to switch it on.
- Every feature has tests that run on their own, and a test checks the defaults an upgraded site
  keeps ([TESTING.md](TESTING.md)).
- Every feature is built to work the same under PHP-FPM and under the Velocity engine, whose workers stay
  alive between requests: nothing is kept from one request to the next.

## What comes next

- Features Mugo Web adds to LoveStack after the commit used for this port
  ([source reference](../lovestack.md#source-reference-where-the-features-come-from-and-when))
  can be considered the same way: each one compared, rebuilt, tested and credited.
- Two small points are left for a later release: whether an author without an e-mail address
  should be accepted (LoveStack accepts it, Exponential refuses it, see
  [PHP8-REVIEW.md](PHP8-REVIEW.md)), and whether the old test data files should lose the
  executable flag LoveStack removed from them (a file mode change only).
- The [changelog](CHANGELOG.md) names the release that carries these features once it is cut.
