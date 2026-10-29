# Templates zwischen Template-Engines konvertieren

Exponential rendert Templates mehr als einer Template-Engine: Exponential-Templates (`.tpl`, die klassische
Template-Sprache) und Smarty-Templates (`.stpl`, Smarty 5). Jede Konvertierung zwischen ihnen ist ein einziger Bereich
von Konsolenbefehlen, `exp:template:convert`. Diese Anleitung richtet sich an Site-Bauer und Template-Entwickler, die
ein Template oder ein ganzes Design konvertieren: welcher Befehl, jede Option, alles, was die Befehle ausgeben, die
Markierung, die von Hand geänderte Dateien schützt, die Override-Einstellungen, die sie schreiben, und wie Sie dieselbe
Arbeit aus PHP aufrufen.

Die Teile, die zu einer Engine gehören, etwa was jedes Konstrukt in Smarty wird und wie Sie einen Siteaccess für ein
konvertiertes Design einrichten, stehen auf den Smarty-Seiten, die am Ende verlinkt sind. Jedes Beispiel dieser Seite
wurde auf einer Entwicklungsinstallation ausgeführt und zeigt die Ausgabe so, wie der Befehl sie geschrieben hat.

## 1. Die Engines und die Befehle

| Engine | Dateiendung | Wert für `--from-engine` / `--to-engine` | Ebenfalls angenommen |
|---|---|---|---|
| Exponential-Templates | `.tpl` | `tpl` | `exp`, `exponential` |
| Smarty-Templates | `.stpl` | `stpl` | `smarty` |

| Befehl | Konvertiert | Skript |
|---|---|---|
| `./console exp:template:convert --from-engine=<engine> --to-engine=<engine>` | der allgemeine Befehl: jede Richtung, für die die Installation einen Konverter hat | `bin/php/exptemplateconvert.php` |
| `./console exp:template:convert:tpl-to-stpl` | Exponential-Templates in Smarty-Templates | `bin/php/exptemplateconverttpltostpl.php` |
| `./console exp:template:convert:stpl-to-tpl` | Smarty-Templates in Exponential-Templates | `bin/php/exptemplateconvertstpltotpl.php` |

Die benannten Befehle sind der allgemeine Befehl mit festen Engines; sie nehmen dieselben Optionen außer den beiden
Engine-Optionen. Diese beiden Zeilen tun dasselbe:

```bash
./console exp:template:convert:tpl-to-stpl --from=extension/<ext>/design/<design> --to=var/tmp/<design> --allow-root-user
./console exp:template:convert --from-engine=tpl --to-engine=stpl --from=extension/<ext>/design/<design> --to=var/tmp/<design> --allow-root-user
```

Führen Sie die Befehle im Wurzelverzeichnis der Installation aus. `--help` gibt den Hilfetext jedes Befehls aus; die
Befehlsreferenz enthält sie mit ihren Optionen: [exp:template:convert](../beispiele/befehle/exptemplateconvert.md),
[exp:template:convert:tpl-to-stpl](../beispiele/befehle/exptemplateconverttpltostpl.md),
[exp:template:convert:stpl-to-tpl](../beispiele/befehle/exptemplateconvertstpltotpl.md).

## 2. Jede Option

| Option | Bedeutung |
|---|---|
| `--from-engine=<engine>`, `--to-engine=<engine>` | nur der allgemeine Befehl: die Engine der Quellen und die Engine, in die konvertiert wird (Tabelle oben) |
| `-s <siteaccess>` | der Siteaccess, dessen Erweiterungen bestimmen, welche Operatoren und Template-Funktionen es gibt und wie jeder Operator seine Eingabe nimmt, und dessen `override.ini` `--override-ini` übernimmt. Nehmen Sie den Siteaccess, der das Design ausliefert |
| `--from=<verzeichnis>` | das Design-Verzeichnis, das gelesen wird; jedes Template der Quell-Engine darunter wird konvertiert (`templates/` und `override/templates/` gleichermaßen) |
| `--to=<verzeichnis>` | das Design-Verzeichnis, das geschrieben wird; jedes Ziel kommt an dieselbe Stelle darunter. Standard: `--from`, jedes Ziel wird also neben seine Quelle geschrieben |
| `--file=<datei>` | ein Template konvertieren: ein Pfad unterhalb von `--from` (`--file=templates/pagelayout.tpl`), oder ohne `--from` eine einzelne Datei, die daneben geschrieben wird, oder nach `--to`, das dann die Zieldatei nennt |
| `--override-ini=<datei>` | eine `override.ini.append.php` für einen Siteaccess schreiben, der das `--to`-Design verwendet (Teil 5). Braucht `--from` |
| `--dry-run` | zeigen, was geschrieben würde (`WRITE` neu, `REPLACE` geändert, `SKIP` behalten); nichts wird geschrieben |
| `--force` | auch ein Ziel ersetzen, das von Hand geändert oder geschrieben wurde (Teil 4) |
| `--allow-root-user` | als root ausführen (eine allgemeine Option jedes Skripts; geben Sie sie an, wenn Sie als root angemeldet sind) |

## 3. Ausgabe und Exit-Codes

| Zeile | Bedeutung |
|---|---|
| `WRITE <datei>` | ein neues Ziel wurde geschrieben (mit `--dry-run`: würde geschrieben) |
| `REPLACE <datei>` | ein Ziel, das der Befehl früher geschrieben hat, wurde ersetzt, weil sich seine Quelle geändert hat (mit `--dry-run`: würde ersetzt) |
| `SKIP <template>: <datei> was changed after it was generated; --force replaces it` | die Markierung des Ziels passt nicht mehr zu seinem Inhalt |
| `SKIP <template>: <datei> has no conversion marker (written by hand); --force replaces it` | das Ziel wurde nicht vom Befehl geschrieben |
| `NOTE <template>: <hinweis>` | ein Konstrukt, das nicht exakt konvertiert werden konnte, oder ein Fehler, den der Parser des Template-Systems in der Quelle gefunden hat (`the source has an error: ...`) |
| `ERROR <template>: ...` | die Quelle konnte nicht gelesen oder konvertiert werden, oder das Ergebnis kompiliert oder parst nicht; für sie wird nichts geschrieben |
| `PASS <von>-><nach> <n> templates: <w> written, <u> unchanged, <s> skipped, <e> errors; <m> notes in <k> templates` | die Zusammenfassung; mit `--dry-run` steht `to write` statt `written` |
| `PASS wrote <datei>: ...` | `--override-ini` wurde geschrieben |
| `FAIL ...` | Fehler, falsche Optionen oder eine Richtung, die nicht verfügbar ist |

| Exit-Code | Wann |
|---|---|
| 0 | `PASS`, auch wenn Dateien übersprungen oder Hinweise ausgegeben wurden |
| 1 | `FAIL`: ein Template konnte nicht gelesen, konvertiert, geprüft oder geschrieben werden, oder die Override-Datei konnte nicht geschrieben werden |
| 2 | falsche Optionen: `FAIL --from-engine is needed: tpl, stpl`, `FAIL --from must be a design directory: ...`, `FAIL --file must be an existing .tpl file: ...`, `FAIL --override-ini needs --from (the design directory)` ... |
| 3 | die Konvertierung zwischen den beiden Engines ist in dieser Installation nicht verfügbar (kein Konverter für die Richtung, siehe Teil 7) |

Jedes Ergebnis wird geprüft, bevor es geschrieben wird: eine `.stpl` muss mit Smarty 5 kompilieren (wenn Smarty
installiert ist), eine `.tpl` muss im Parser des Template-Systems parsen.

Zwei der Bedienfehler, so wie die Befehle sie ausgeben:

```
$ ./console exp:template:convert --to-engine=stpl --from=extension/sevenx_themes_media/design/media --allow-root-user
FAIL --from-engine is needed: tpl, stpl
$ ./console exp:template:convert:tpl-to-stpl --from=extension/no-such-design --allow-root-user
FAIL --from must be a design directory: extension/no-such-design (or give --file)
```

## 4. Die Markierung: von Hand geänderte Dateien

Jede Datei, die die Befehle schreiben, endet mit einem Markierungskommentar in der Kommentarsyntax der Ziel-Engine,
der die Richtung, die Prüfsumme von allem davor und die Quelle nennt:

```
{* exp:template:convert tpl->stpl md5:1e2129f53813e0f6d5b0f839d1220bf6 from templates/explayouts/block/title/centered.tpl -- generated; a file changed by hand is not replaced without --force *}
```

Ein vorhandenes Ziel wird nur ersetzt, solange seine Markierung da ist und noch zu seinem Inhalt passt, also solange
niemand die Datei geändert hat, nachdem sie geschrieben wurde. Läuft ein Befehl erneut über unveränderte Quellen,
bleibt jede Datei, wie sie ist (`unchanged`); ein von Hand geändertes Ziel oder eines ohne Markierung wird übersprungen
und gemeldet, der Exit-Code bleibt 0. `--force` ersetzt es. Mit `--dry-run --force` sehen Sie, was `--force` ersetzen
würde, bevor es das tut.

Die Markierung ist ein Kommentar ganz am Ende der Datei, gibt also nichts aus, und sie wird aus einer Quelle entfernt,
bevor die Quelle konvertiert wird. Ein Editor, der nach der Markierung einen Zeilenumbruch anfügt, lässt die Datei als
von Hand geschrieben gelten, was sicher ist. Löschen Sie die Markierung einer Datei, um sie endgültig zu Ihrer zu machen.

## 5. Override-Einstellungen für ein konvertiertes Design

`--override-ini=<datei>` schreibt eine `override.ini.append.php` für einen Siteaccess, der das konvertierte Design
ausliefert:

1. jede Override-Regel des Siteaccess von `-s`, in derselben Reihenfolge und mit derselben `Priority`, wobei
   `MatchFile` auf das konvertierte Template zeigt, wo eines konvertiert wurde;
2. von Exponential-Templates in eine andere Engine außerdem eine Regel je konvertiertem Template im Verzeichnis
   `templates/` des Designs, benannt nach der Ziel-Engine (`stpl_pagelayout`), die die konvertierte Datei an die
   Stelle der `.tpl` setzt, nach der das Template-System fragt:

   ```ini
   [stpl_pagelayout]
   Source=pagelayout.tpl
   MatchFile=../../templates/pagelayout.stpl
   Subdir=templates
   ```

   `MatchFile` wird unterhalb von `override/templates/` jedes Designs gesucht, `../../templates/` ist also das eigene
   Verzeichnis `templates/` des Designs.

Die Datei beginnt mit einem Kommentar, der den Befehl nennt. Läuft der Befehl erneut für den Siteaccess, der das
konvertierte Design schon ausliefert, schreibt er dieselben Regeln wieder; die Datei lässt sich also nach einer
Änderung am Design neu erzeugen.

## 6. Beispiele

### Ein Template, in beide Richtungen

```bash
./console exp:template:convert --from-engine=tpl --to-engine=stpl --from=extension/sevenx_themes_media/design/media \
    --file=templates/explayouts/block/title/centered.tpl --to=var/tmp/template_convert_example --allow-root-user
```

```
WRITE var/tmp/template_convert_example/templates/explayouts/block/title/centered.stpl
PASS tpl->stpl 1 templates: 1 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

Dasselbe noch einmal, mit den anderen Namen der Engines, ändert nichts:

```bash
./console exp:template:convert --from-engine=exp --to-engine=smarty --from=extension/sevenx_themes_media/design/media \
    --file=templates/explayouts/block/title/centered.tpl --to=var/tmp/template_convert_example --allow-root-user
```

```
PASS tpl->stpl 1 templates: 0 written, 1 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

Und zurück, neben die `.stpl`:

```bash
./console exp:template:convert:stpl-to-tpl \
    --file=var/tmp/template_convert_example/templates/explayouts/block/title/centered.stpl --allow-root-user
```

```
WRITE var/tmp/template_convert_example/templates/explayouts/block/title/centered.tpl
PASS stpl->tpl 1 templates: 1 written, 0 unchanged, 0 skipped, 0 errors; 0 notes in 0 templates
```

Die `.tpl` endet mit ihrer eigenen Markierung: `{* exp:template:convert stpl->tpl md5:... from centered.stpl -- generated; ... *}`.

### Ein ganzes Design

Das Media-Design (516 Templates) in ein eigenes Smarty-Design konvertiert, mit den Override-Regeln für den Siteaccess
`site`, der das Media-Design ausliefert:

```bash
./console exp:template:convert:tpl-to-stpl -s site \
    --from=extension/sevenx_themes_media/design/media \
    --to=var/tmp/template_convert_trial/smarty_media \
    --override-ini=var/tmp/template_convert_trial/override.ini.append.php --allow-root-user
```

Eine `WRITE`-Zeile je Template und eine `NOTE`-Zeile für jedes Konstrukt, das nicht exakt konvertiert werden konnte,
dann:

```
PASS wrote var/tmp/template_convert_trial/override.ini.append.php: 74 rules pointed at .stpl, 444 rules for the design's templates
PASS tpl->stpl 516 templates: 516 written, 0 unchanged, 0 skipped, 0 errors; 126 notes in 58 templates
```

Die 126 Hinweise sind Fehler, die der Parser des Template-Systems in 58 der eigenen `.tpl`-Quellen des Media-Designs
meldet. Beim zweiten Lauf wird nichts geschrieben:

```
PASS tpl->stpl 516 templates: 0 written, 516 unchanged, 0 skipped, 0 errors; 126 notes in 58 templates
```

Zurück in das ursprüngliche Design, als Probelauf: dessen eigene `.tpl`-Dateien haben keine Markierung, also wird jede
übersprungen, solange `--force` nicht angegeben ist:

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

## 7. Aus PHP, und die Konverter-Registrierung

Die Befehle lesen ihre Optionen und geben sie an `expTemplateDesignConverter`
(`lib/templatebridge/classes/exptemplatedesignconverter.php`) weiter, der das Design durchläuft, die Markierung,
`--force`, `--dry-run`, `--file` und `--override-ini` anwendet und die Zeilen oben ausgibt. Dieselbe Arbeit aus PHP:

```php
$converter = new expTemplateDesignConverter( 'tpl', 'stpl' );
$code = $converter->run( array( 'from' => 'design/mysite', 'to' => 'var/tmp/mysite-stpl', 'dry-run' => true ),
                         function ( $line ) { echo $line, "\n"; },
                         function ( $line ) { fwrite( STDERR, $line . "\n" ); } );
$counts = $converter->counts(); // templates, written, unchanged, skipped, errors, notes, withNotes
```

`convertText( $text, $name )` konvertiert einen Template-Text und gibt das Ergebnis mit seinen Hinweisen zurück;
`expTemplateDesignConverter::splitMarker( $text )` liest die Markierung einer Datei.

Der Konverter eines Template-Texts kommt aus einer Registrierung in `site.ini`:

```ini
[TemplateBridgeSettings]
ConverterList[]
ConverterList[tpl-stpl]=expSmartyConverter
ConverterList[stpl-tpl]=expSmartyReverseConverter
```

`ConverterList[<von>-<nach>]` nennt eine Klasse, deren Konstruktor ein `eZTemplate` nimmt und die
`convert( $text, $name )` und `warnings()` hat. Eine Erweiterung fügt eine Richtung mit einem eigenen Eintrag in ihrer
`settings/site.ini.append.php` hinzu; eine Richtung ohne Eintrag endet mit Exit-Code 3.

## 8. Nach dem Konvertieren

- Lesen Sie die `NOTE`-Zeilen: Jede nennt das Template und was nicht exakt konvertiert wurde. Für Smarty stehen die
  Hinweise und was bei jedem zu tun ist im [converter guide](../../eng-us/guides/smarty-templates-converter.md#3-its-notes-and-what-to-check-after-converting) (Englisch).
- Öffnen Sie die Seiten, die die konvertierten Templates verwenden, und vergleichen Sie sie mit denselben Seiten des
  ursprünglichen Designs.
- Halten Sie eine Seite als Quelle: Ändern Sie nur deren Dateien und konvertieren Sie nach jeder Änderung erneut.
  Unveränderte Ziele bleiben, wie sie sind, geänderte Quellen ersetzen ihre erzeugten Ziele, von Hand geänderte Ziele
  werden übersprungen.

## Verwandte Seiten

- [Von Exponential-Templates (.tpl) zu Exponential-Smarty-Templates (.stpl) umziehen](../hilfe/template-sprache/von-tpl-zu-stpl-umziehen.md): das Kapitel des Template-Sprachen-Handbuchs, mit durchgerechneten Beispielen
- Anleitungen (Englisch): [the converter and porting a design](../../eng-us/guides/smarty-templates-converter.md), [migrating a design](../../eng-us/guides/smarty-templates-migrating.md)
- [Smarty templates (.stpl) next to .tpl](../../eng-us/features/6.0/smarty-templates.md), die Seite zur Funktion (Englisch)
- [Alle Konsolenbefehle](../beispiele/befehle/README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
