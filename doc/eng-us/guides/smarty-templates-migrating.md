# Smarty templates: migrating a design from .tpl to .stpl

This guide is for site builders and template developers who move a whole design to Smarty. It is the plan around the
converter: what to decide before you start, how to run the conversion, how to read its notes, how to prove page by
page that the Smarty design prints what the `.tpl` design printed, how to fix the differences, and how to keep the two
in step while both exist. The port of the media design (516 templates, served by a `smarty` siteaccess) is the worked
example throughout. The commands and every mapping the converter makes are in
[the converter guide](smarty-templates-converter.md); this page links to it instead of repeating it.

## 1. Plan

**Decide what moves.** A design can move whole (every template, as the media port did) or a template at a time: a
`.stpl` and a `.tpl` live side by side in one design, and override rules decide which one a page uses. Move whole when
you want one language for the design; move gradually when only a few templates benefit (the pagelayout, a heavy list).

**Keep the original running.** Port into a design of its own (`design/<name>` or `extension/<ext>/design/<name>`) and
serve it from a siteaccess of its own whose design list keeps the original design as the fallback. The public site is
untouched until you switch, and anything not ported keeps working through the fallback: stylesheets, images, fonts,
JavaScript and templates.

**Take an inventory** of the design:

```bash
find extension/sevenx_themes_media/design/media -name '*.tpl' | wc -l
find extension/sevenx_themes_media/design/media/override/templates -name '*.tpl' | wc -l
grep -c '^\[' settings/siteaccess/site/override.ini.append.php
```

(the template count, the override templates among them, and the override rules in the siteaccess's own
override file; extensions can add rules of their own in their `settings/siteaccess/<name>/`. Replace the paths
with yours). Note the extensions active in the siteaccess: their operators and template functions must be known when
you convert.

**Choose the siteaccess to convert with.** The converter reads the operators, the template functions and the override
rules of the siteaccess given with `-s`. Use the siteaccess that serves the design today (`site` for the media design).

**Make a list of pages to check**: the front page, one page of every class with its own full view, every list with
paging, search, a form with errors, the user pages (login, register, edit), a 404 and an error page, mail templates
(information collection, password mail), and anything with a `{cache-block}`. The media port was checked on these and
on the layout pages of Exponential Layouts.

## 2. Convert

Try it first without writing anything:

```bash
./console exp:template:convert:tpl-to-stpl -s site \
    --from=extension/sevenx_themes_media/design/media \
    --to=extension/sevenx_themes_media/design/smarty_media \
    --dry-run --allow-root-user
```

Every line is `WRITE <file>` (new), `REPLACE <file>` (a generated file that would change), `SKIP <file>` (changed or
written by hand, kept) or a `NOTE`. The last line counts them, for a first run of the media design:

```
PASS tpl->stpl 516 templates: 516 to write, 0 unchanged, 0 skipped, 0 errors; 126 notes in 58 templates
```

Then convert for real and write the override rules for the new siteaccess:

```bash
./console exp:template:convert:tpl-to-stpl -s site \
    --from=extension/sevenx_themes_media/design/media \
    --to=extension/sevenx_themes_media/design/smarty_media \
    --override-ini=var/tmp/smarty-override.ini.append.php --allow-root-user
```

Every result is compiled by Smarty before it is written; a template that does not compile is an `ERROR` line and is
not written, and the command ends with `FAIL` and exit code 1. Every option, output line and exit code is listed in
[Converting templates between template engines](template-conversion.md). Set up the siteaccess as [the converter guide, part 4](smarty-templates-converter.md#4-a-siteaccess-that-serves-the-converted-design)
describes (design list, override rules, `SiteList[]`, `AvailableSiteAccessList[]`, the Anonymous role's login
limitation, cache clear).

## 3. Read the notes

Save the output and sort the notes by kind:

```bash
./console exp:template:convert:tpl-to-stpl -s site --from=<design> --to=<port> --dry-run --allow-root-user \
    > var/tmp/convert.log 2>&1
grep '^NOTE' var/tmp/convert.log | sed 's/^NOTE [^:]*: //' | sort | uniq -c | sort -rn
```

For the media design the list starts with

```
     23 {if} without a condition (a parse error in the .tpl): it prints nothing, as in the .tpl
      1 unknown function {}: it prints nothing, as in the .tpl
      1 the source has an error: parser error @ templates/user/reset_password_done.tpl\[1] No variable name found, this is most likely a bug in the template parser.
```

followed by one `the source has an error` line per broken source.

What each note means and what to do: [Its notes, and what to check after converting](smarty-templates-converter.md#3-its-notes-and-what-to-check-after-converting).
In practice:

- `the source has an error: ...` is the template system's parser complaining about the `.tpl` itself. The `.tpl`
  printed whatever the parser made of it, and so does the `.stpl`; fix the `.tpl` and convert that file again.
- `unknown function {x}` means an extension was not active in the `-s` siteaccess. Convert again with the right one.
- Everything else is rare and names the construct; fix it by hand in the `.stpl`.

## 4. Check the output, page by page

The goal is the same HTML from both siteaccesses. Fetch each page from both, without the debug output (it differs by
nature) and as an anonymous visitor first:

```bash
mkdir -p var/tmp/parity
for p in / /fitness /recipes; do
    n=$(echo "$p" | tr '/' '_')
    curl -s "https://www.example.com$p" > "var/tmp/parity/tpl$n.html"
    curl -s "https://www.example.com/smarty$p" | sed 's#/smarty/#/#g; s#/smarty"#/"#g' > "var/tmp/parity/stpl$n.html"
done
for f in var/tmp/parity/tpl_*.html; do
    diff -q "$f" "${f/tpl_/stpl_}" >/dev/null && echo "SAME ${f#*tpl}" || echo "DIFF ${f#*tpl}"
done
```

The `sed` removes the siteaccess's own path from the links, which is the one difference a URI-matched siteaccess always
has. Other values that change on every request must be taken out the same way before comparing: a CSP nonce
(`s/nonce="[^"]*"/nonce=""/g`), a form token for signed-in visitors (`ezxform_token`), timestamps. Then look at each
`DIFF`:

```bash
diff var/tmp/parity/tpl_fitness.html var/tmp/parity/stpl_fitness.html | head -40
```

Repeat signed in (copy the session cookie of a test user into `curl -b 'eZSESSID...=...'`), and for each language
siteaccess if the design has several. Remove `var/tmp/parity/` when you are done.

## 5. Fix the differences

The differences met in the media port, most frequent first:

| Difference | Cause | Fix |
|---|---|---|
| A newline more or less around a tag | Smarty drops the newline after a tag that prints nothing; a hand edit put a tag on the same line as text, or removed the newline the converter wrote | keep the converter's layout: tags that print nothing on lines of their own |
| A value is the default where the `.tpl` showed the variable | `$a\|first_set('d')` in the `.tpl` ignores `$a`; the converter keeps that (`first_set('d')`) | if the `.tpl` was wrong, fix both to `first_set($a, 'd')` |
| The text `null`, `true` or a word appears | a bare word in the `.tpl` is a string; the converter keeps it (`'null'`) | write the real value in both |
| A whole part is empty | a `.stpl` failed: see the HTML comment `<!-- smarty: ... -->` with debug output on | [Troubleshooting](smarty-templates-troubleshooting.md) |
| A list counts 1 for a word, or prints `Array` | a hand edit used Smarty's `count`, `explode`, `implode` instead of `exp_count`, `exp_explode`, `exp_implode` | use the `exp_` names |
| Escaped `&amp;` twice, or HTML shown as text | a hand edit added Smarty's `\|escape` to a value the `.tpl` washed already | one escaping only: `\|wash` |
| A different template is used | an override rule was missing from `--override-ini`, or a new `.stpl` without the template caches cleared | add the rule; clear the template caches |
| A cache block shows another page's content | a hand-written `{expcacheblock}` without the keys that make it differ, or two blocks with the same `id` in one file | key it; unique ids |

After each fix, fetch and compare again. The media port reached byte-for-byte identical pages for the category, list,
recipe and front pages used in the speed measurement.

## 6. Hand edits and the marker

Every file the converter writes ends with a marker comment:

```
{* exp:template:convert tpl->stpl md5:<checksum> from templates/pagelayout.tpl -- generated; a file changed by hand is not replaced without --force *}
```

The checksum covers everything before it. A later run of the converter replaces a target only while its marker still
matches its content. As soon as you edit a generated `.stpl`, the checksum no longer matches, and every later run
prints `SKIP ... was changed after it was generated` and leaves your edit alone. The media port's `pagelayout.stpl`,
changed by hand to keep the whole page in one cache block, is kept that way:

```
SKIP templates/pagelayout.tpl: extension/sevenx_themes_media/design/smarty_media/templates/pagelayout.stpl was changed after it was generated; --force replaces it
PASS tpl->stpl 516 templates: 0 to write, 515 unchanged, 1 skipped, 0 errors; 126 notes in 58 templates (a skipped file was changed or written by hand: --force replaces it)
``` A `.stpl` you wrote yourself has no
marker and is skipped as `written by hand`. `--force` replaces both; use it with `--file` for one template at a time.

The marker is a comment, so it prints nothing. If you want a file to be yours for good, delete its marker line: it is
then "written by hand" and never replaced without `--force`.

## 7. Keep .tpl and .stpl in step

While both designs exist, decide which one is the source:

**The `.tpl` design stays the source** (both sites in production, the `.stpl` one being proved):

- Change only the `.tpl` files. Run the converter after each change; it rewrites the generated `.stpl` files whose
  source changed (`REPLACE`), leaves the unchanged ones alone, and skips the ones edited by hand.
- A dry run tells you which `.stpl` files are behind their `.tpl`:

  ```bash
  ./console exp:template:convert:tpl-to-stpl -s site --from=<design> --to=<port> --dry-run --allow-root-user | grep '^REPLACE'
  ```

  An empty list means the port is up to date. A `SKIP` of a file you edited by hand means its `.tpl` source may have
  changed under it: compare the two and port the change by hand.

**The `.stpl` design becomes the source** (the switch is done):

- Change only the `.stpl` files, and remove the markers or ignore the `SKIP` lines.
- If a `.tpl` copy is still needed somewhere (another siteaccess, an extension that ships `.tpl`), convert back with
  `./console exp:template:convert:stpl-to-tpl` ([part 6 of the converter guide](smarty-templates-converter.md#6-converting-smarty-templates-back-to-tpl)).
  What has no `.tpl` form is kept as a `{* SMARTY ... *}` comment and reported.

## 8. Switch

Before switching, compare the speed of the two siteaccesses as [Smarty templates: performance (Speed)](smarty-templates-performance.md#how-to-measure)
describes, and decide how the pagelayout is cached ([the page in one cache block](smarty-templates-performance.md#the-page-in-one-cache-block)).

When the pages match:

1. Point the public siteaccess at the port: in its `site.ini.append.php`, the design list of the `.stpl` siteaccess,
   and its `override.ini.append.php` from `--override-ini`. Or make the `.stpl` siteaccess the one your host maps to.
2. Clear the caches or deploy (`./console exp:velocity deploy --allow-root-user`).
3. Keep the `.tpl` design in the fallback list: templates not ported, the static files and any extension template are
   still found there.
4. Watch the error log for `smarty` errors for a while (`var/log/error.log`, or the log viewer).

## Related pages

- [Smarty templates: performance (Speed)](smarty-templates-performance.md)
- [Moving from .tpl to .stpl](../help/template-language/moving-from-tpl-to-stpl.md), the template-language manual's chapter
- [The converter and porting a design](smarty-templates-converter.md)
- [Troubleshooting and FAQ](smarty-templates-troubleshooting.md)
- [Caching and performance](smarty-templates-caching.md)
- [Converting templates between template engines](template-conversion.md): every option, output line and exit code of the commands
- Command reference: [exp:template:convert](../examples/commands/exptemplateconvert.md),
  [exp:template:convert:tpl-to-stpl](../examples/commands/exptemplateconverttpltostpl.md) and
  [exp:template:convert:stpl-to-tpl](../examples/commands/exptemplateconvertstpltotpl.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
