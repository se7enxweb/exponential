# Converting templates between template engines

Exponential renders templates of three template engines: Exponential templates (`.tpl`, the classic template
language), Smarty templates (`.stpl`, Smarty 5) and Twig templates (`.twig`, Twig 3). Every conversion between them is
one console command space, `exp:template:convert`, with a named command for each of the six directions. This guide is for site builders and template developers who convert one template or a whole
design: which command to run, every option, everything the commands print, the marker that protects files changed by
hand, the override settings they write, and how to call the same work from PHP.

The engine-specific parts, such as what each construct becomes in Smarty and how to set up a siteaccess for a
converted design, are in the Smarty pages linked at the end. Every example on this page was run on a development
installation and shows the output as the command printed it.

## 1. The engines and the commands

| Engine | File ending | `--from-engine` / `--to-engine` value | Also accepted |
|---|---|---|---|
| Exponential templates | `.tpl` | `tpl` | `exp`, `exponential` |
| Smarty templates | `.stpl` | `stpl` | `smarty` |

| Command | Converts | Script |
|---|---|---|
| `./console exp:template:convert --from-engine=<engine> --to-engine=<engine>` | the general command: any direction the installation has a converter for | `bin/php/exptemplateconvert.php` |
| `./console exp:template:convert:tpl-to-stpl` | Exponential templates to Smarty templates | `bin/php/exptemplateconverttpltostpl.php` |
| `./console exp:template:convert:stpl-to-tpl` | Smarty templates to Exponential templates | `bin/php/exptemplateconvertstpltotpl.php` |

The named commands are the general command with the engines fixed; they take the same options apart from the two
engine options. These two lines do the same:

```bash
./console exp:template:convert:tpl-to-stpl --from=extension/<ext>/design/<design> --to=var/tmp/<design> --allow-root-user
./console exp:template:convert --from-engine=tpl --to-engine=stpl --from=extension/<ext>/design/<design> --to=var/tmp/<design> --allow-root-user
```

Run the commands from the installation root. `--help` prints the help text of each; the command reference has them
with their options: [exp:template:convert](../examples/commands/exptemplateconvert.md),
[exp:template:convert:tpl-to-stpl](../examples/commands/exptemplateconverttpltostpl.md),
[exp:template:convert:stpl-to-tpl](../examples/commands/exptemplateconvertstpltotpl.md).

## 2. Every option

| Option | Meaning |
|---|---|
| `--from-engine=<engine>`, `--to-engine=<engine>` | the general command only: the engine of the sources and the engine to convert them to (table above) |
| `-s <siteaccess>` | the siteaccess whose extensions decide which operators and template functions exist and how each operator takes its input, and whose `override.ini` `--override-ini` copies. Use the siteaccess that serves the design |
| `--from=<dir>` | the design directory to read; every template of the source engine below it is converted (`templates/` and `override/templates/` alike) |
| `--to=<dir>` | the design directory to write; each target goes to the same place below it. Default: `--from`, so each target is written next to its source |
| `--file=<file>` | convert one template: a path below `--from` (`--file=templates/pagelayout.tpl`), or, without `--from`, a file of its own, written next to it, or to `--to`, which then names the target file |
| `--override-ini=<file>` | write an `override.ini.append.php` for a siteaccess that uses the `--to` design (part 5). Needs `--from` |
| `--dry-run` | show what would be written (`WRITE` new, `REPLACE` changed, `SKIP` kept); nothing is written |
| `--force` | also replace a target that was changed or written by hand (part 4) |
| `--allow-root-user` | run as root (a general option of every script; add it when you are logged in as root) |

## 3. Output and exit codes

| Line | Means |
|---|---|
| `WRITE <file>` | a new target was written (with `--dry-run`: would be written) |
| `REPLACE <file>` | a target the command wrote before was replaced, because its source changed (with `--dry-run`: would be replaced) |
| `SKIP <template>: <file> was changed after it was generated; --force replaces it` | the target's marker no longer matches its content |
| `SKIP <template>: <file> has no conversion marker (written by hand); --force replaces it` | the target was not written by the command |
| `NOTE <template>: <note>` | a construct that could not be converted exactly, or an error the template system's parser found in the source (`the source has an error: ...`) |
| `ERROR <template>: ...` | the source could not be read or converted, or the result does not compile or parse; nothing is written for it |
| `PASS <from>-><to> <n> templates: <w> written, <u> unchanged, <s> skipped, <e> errors; <m> notes in <k> templates` | the summary; `to write` instead of `written` with `--dry-run` |
| `PASS wrote <file>: ...` | `--override-ini` was written |
| `FAIL ...` | errors, wrong options, or a direction that is not available |

| Exit code | When |
|---|---|
| 0 | `PASS`, also when files were skipped or notes were printed |
| 1 | `FAIL`: a template could not be read, converted, checked or written, or the override file could not be written |
| 2 | wrong options: `FAIL --from-engine is needed: tpl, stpl`, `FAIL --from must be a design directory: ...`, `FAIL --file must be an existing .tpl file: ...`, `FAIL --override-ini needs --from (the design directory)` ... |
| 3 | the conversion between the two engines is not available in this installation (no converter for the direction, see part 7) |

Every result is checked before it is written: a `.stpl` must compile with Smarty 5 (when Smarty is installed), a
`.tpl` must parse in the template system's parser.

Two of the usage errors, as the commands print them:

```
$ ./console exp:template:convert --to-engine=stpl --from=extension/sevenx_themes_media/design/media --allow-root-user
FAIL --from-engine is needed: tpl, stpl
$ ./console exp:template:convert:tpl-to-stpl --from=extension/no-such-design --allow-root-user
FAIL --from must be a design directory: extension/no-such-design (or give --file)
```

## 4. The marker: files changed by hand

Every file the commands write ends with a marker comment, in the comment syntax of the target engine, that names the
direction, the checksum of everything before it and the source:

```
{* exp:template:convert tpl->stpl md5:1e2129f53813e0f6d5b0f839d1220bf6 from templates/explayouts/block/title/centered.tpl -- generated; a file changed by hand is not replaced without --force *}
```

A target that exists is replaced only while its marker is there and still matches its content, that is when nobody
changed the file after it was written. Running a command again on unchanged sources leaves every file alone
(`unchanged`); a target changed by hand, or one without a marker, is skipped and reported, and the exit code stays 0.
`--force` replaces it. With `--dry-run --force` you see what `--force` would replace before it does.

The marker is a comment at the very end of the file, so it prints nothing, and it is removed from a source before the
source is converted. An editor that adds a newline after the marker makes the file count as written by hand, which is
safe. Delete the marker of a file to make it yours for good.

## 5. Override settings for a converted design

`--override-ini=<file>` writes an `override.ini.append.php` for a siteaccess that serves the converted design:

1. every override rule of the `-s` siteaccess, in the same order and with the same `Priority`, with `MatchFile`
   pointing at the converted template where one was converted;
2. from Exponential templates to another engine, also one rule per converted template of the design's `templates/`
   directory, named after the target engine (`stpl_pagelayout`), that puts the converted file in the place of the
   `.tpl` the template system asks for:

   ```ini
   [stpl_pagelayout]
   Source=pagelayout.tpl
   MatchFile=../../templates/pagelayout.stpl
   Subdir=templates
   ```

   `MatchFile` is looked up below `override/templates/` of each design, so `../../templates/` is the design's own
   `templates/` directory.

The file starts with a comment that names the command. Run again for the siteaccess that already serves the converted
design, the command writes the same rules again, so the file can be regenerated after a change to the design.

## 6. Examples

### One template, both ways

```bash
./console exp:template:convert --from-engine=tpl --to-engine=stpl --from=extension/sevenx_themes_media/design/media \
    --file=templates/explayouts/block/title/centered.tpl --to=var/tmp/template_convert_example --allow-root-user
```

```
WRITE var/tmp/template_convert_example/templates/explayouts/block/title/centered.stpl
PASS tpl->stpl 1 templates: 1 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

The same again, with the other names of the engines, changes nothing:

```bash
./console exp:template:convert --from-engine=exp --to-engine=smarty --from=extension/sevenx_themes_media/design/media \
    --file=templates/explayouts/block/title/centered.tpl --to=var/tmp/template_convert_example --allow-root-user
```

```
PASS tpl->stpl 1 templates: 0 written, 1 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

And back, next to the `.stpl`:

```bash
./console exp:template:convert:stpl-to-tpl \
    --file=var/tmp/template_convert_example/templates/explayouts/block/title/centered.stpl --allow-root-user
```

```
WRITE var/tmp/template_convert_example/templates/explayouts/block/title/centered.tpl
PASS stpl->tpl 1 templates: 1 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

The `.tpl` ends with its own marker: `{* exp:template:convert stpl->tpl md5:... from centered.stpl -- generated; ... *}`.

### A whole design

The media design (516 templates) converted to a Smarty design of its own, with the override rules for the siteaccess
`site` that serves the media design:

```bash
./console exp:template:convert:tpl-to-stpl -s site \
    --from=extension/sevenx_themes_media/design/media \
    --to=var/tmp/template_convert_trial/smarty_media \
    --override-ini=var/tmp/template_convert_trial/override.ini.append.php --allow-root-user
```

A `WRITE` line per template and a `NOTE` line for each construct that could not be converted exactly, then:

```
PASS wrote var/tmp/template_convert_trial/override.ini.append.php: 74 rules pointed at .stpl, 444 rules for the design's templates
PASS tpl->stpl 516 templates: 516 written, 0 unchanged, 0 skipped, 0 errors; 126 notes in 58 templates
```

The 126 notes are errors the template system's parser reports in 58 of the media design's own `.tpl` sources. Run
again, nothing is written:

```
PASS tpl->stpl 516 templates: 0 written, 516 unchanged, 0 skipped, 0 errors; 126 notes in 58 templates
```

Back into the original design, as a dry run: its own `.tpl` files have no marker, so every one is skipped unless
`--force` is given:

```bash
./console exp:template:convert:stpl-to-tpl -s smarty --from=var/tmp/template_convert_trial/smarty_media \
    --to=extension/sevenx_themes_media/design/media --dry-run --allow-root-user
```

```
Dry run: nothing is written.
SKIP override/templates/content/view/embed/embed_file.stpl: extension/sevenx_themes_media/design/media/override/templates/content/view/embed/embed_file.tpl has no conversion marker (written by hand); --force replaces it
...
PASS stpl->tpl 516 templates: 0 to write, 0 unchanged, 516 skipped, 0 errors; 8 notes in 4 templates (a skipped file was changed or written by hand: --force replaces it)
```

## 7. From PHP, and the converter registry

The commands parse their options and hand them to `expTemplateDesignConverter`
(`lib/templatebridge/classes/exptemplatedesignconverter.php`), which walks the design, applies the marker, `--force`,
`--dry-run`, `--file` and `--override-ini`, and prints the lines above. The same work from PHP:

```php
$converter = new expTemplateDesignConverter( 'tpl', 'stpl' );
$code = $converter->run( array( 'from' => 'design/mysite', 'to' => 'var/tmp/mysite-stpl', 'dry-run' => true ),
                         function ( $line ) { echo $line, "\n"; },
                         function ( $line ) { fwrite( STDERR, $line . "\n" ); } );
$counts = $converter->counts(); // templates, written, unchanged, skipped, errors, notes, withNotes
```

`convertText( $text, $name )` converts one template text and returns the result with its notes;
`expTemplateDesignConverter::splitMarker( $text )` reads the marker of a file.

The converter of one template text comes from a registry in `site.ini`:

```ini
[TemplateBridgeSettings]
ConverterList[]
ConverterList[tpl-stpl]=expSmartyConverter
ConverterList[stpl-tpl]=expSmartyReverseConverter
```

`ConverterList[<from>-<to>]` names a class whose constructor takes an `eZTemplate` and that has
`convert( $text, $name )` and `warnings()`. An extension adds a direction with an entry of its own in its
`settings/site.ini.append.php`; a direction without an entry ends with exit code 3.

## 8. After converting

- Read the `NOTE` lines: each names the template and what was not converted exactly. For Smarty, the notes and what to
  do about each are listed in [the converter guide](smarty-templates-converter.md#3-its-notes-and-what-to-check-after-converting).
- Open the pages that use the converted templates and compare them with the same pages of the original design.
- Keep one side the source: change only that side's files and convert again after each change. Unchanged targets
  stay as they are, changed sources replace their generated targets, hand-edited targets are skipped.

## Related pages

- [Smarty templates: the converter and porting a design](smarty-templates-converter.md): what each `.tpl` construct becomes in `.stpl` and back, the notes, a siteaccess for a converted design
- [Smarty templates: migrating a design](smarty-templates-migrating.md): a whole design moved to `.stpl`, step by step, with the checks
- [Moving from .tpl to .stpl](../help/template-language/moving-from-tpl-to-stpl.md): the template-language manual's chapter, with worked examples
- [Smarty templates (.stpl) next to .tpl](../features/6.0/smarty-templates.md), the feature page
- [Twig templates: the converter and porting a design](twig-templates-converter.md): what each `.tpl` construct becomes in `.twig` and back, with real output
- [Converting templates between template engines](../help/template-language/converting-between-template-engines.md): the manual's chapter, through all three engines
- [The three template engines compared](template-engines.md)
- [All console commands](../examples/commands/README.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
