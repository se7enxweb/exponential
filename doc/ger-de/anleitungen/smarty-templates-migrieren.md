# Smarty-Templates: ein Design von .tpl zu .stpl migrieren

Diese Anleitung ist für Site-Builder und Template-Entwickler, die ein ganzes Design zu Smarty umziehen. Sie ist der Plan
rund um den Konverter: was Sie vor dem Start entscheiden, wie Sie die Konvertierung ausführen, wie Sie ihre Hinweise
lesen, wie Sie Seite für Seite nachweisen, dass das Smarty-Design druckt, was das `.tpl`-Design gedruckt hat, wie Sie
die Unterschiede beheben und wie Sie beide im Gleichschritt halten, solange beide bestehen. Die Portierung des
Media-Designs (516 Templates, ausgeliefert von einem Siteaccess `smarty`) ist durchgehend das Beispiel. Die Befehle und
jede Zuordnung, die der Konverter vornimmt, stehen in [der Anleitung zum Konverter](smarty-templates-konverter.md);
diese Seite verweist dorthin, statt sie zu wiederholen.

## 1. Planen

**Entscheiden Sie, was umzieht.** Ein Design kann ganz umziehen (jedes Template, wie bei der Media-Portierung) oder
Template für Template: Ein `.stpl` und ein `.tpl` stehen in einem Design nebeneinander, und Override-Regeln
entscheiden, welches eine Seite verwendet. Ziehen Sie ganz um, wenn Sie eine Sprache für das Design wollen; ziehen Sie
schrittweise um, wenn nur wenige Templates davon profitieren (das Pagelayout, eine aufwendige Liste).

**Lassen Sie das Original weiterlaufen.** Portieren Sie in ein eigenes Design (`design/<name>` oder
`extension/<ext>/design/<name>`) und liefern Sie es über einen eigenen Siteaccess aus, dessen Design-Liste das
Original als Fallback behält. Die öffentliche Website bleibt unberührt, bis Sie umschalten, und alles, was nicht
portiert ist, funktioniert über den Fallback weiter: Stylesheets, Bilder, Schriften, JavaScript und Templates.

**Machen Sie eine Bestandsaufnahme** des Designs:

```bash
find extension/sevenx_themes_media/design/media -name '*.tpl' | wc -l
find extension/sevenx_themes_media/design/media/override/templates -name '*.tpl' | wc -l
grep -c '^\[' settings/siteaccess/site/override.ini.append.php
```

(die Zahl der Templates, die Override-Templates darunter und die Override-Regeln in der eigenen Override-Datei des
Siteaccess; Erweiterungen können in ihrem `settings/siteaccess/<name>/` eigene Regeln hinzufügen. Ersetzen Sie die
Pfade durch Ihre.) Notieren Sie die im Siteaccess aktiven Erweiterungen: Ihre Operatoren und Template-Funktionen müssen
beim Konvertieren bekannt sein.

**Wählen Sie den Siteaccess, mit dem Sie konvertieren.** Der Konverter liest die Operatoren, die Template-Funktionen
und die Override-Regeln des Siteaccess, der mit `-s` angegeben ist. Nehmen Sie den Siteaccess, der das Design heute
ausliefert (`site` für das Media-Design).

**Stellen Sie eine Liste der zu prüfenden Seiten auf**: die Startseite, eine Seite jeder Klasse mit eigener
Vollansicht, jede Liste mit Blättern, die Suche, ein Formular mit Fehlern, die Benutzerseiten (Anmelden, Registrieren,
Bearbeiten), eine 404- und eine Fehlerseite, Mail-Templates (Informationssammlung, Passwort-Mail) und alles mit einem
`{cache-block}`. Die Media-Portierung wurde an diesen Seiten und an den Layout-Seiten von Exponential Layouts geprüft.

## 2. Konvertieren

Probieren Sie es zuerst aus, ohne etwas zu schreiben:

```bash
./console exp:template:convert:tpl-to-stpl -s site \
    --from=extension/sevenx_themes_media/design/media \
    --to=extension/sevenx_themes_media/design/smarty_media \
    --dry-run --allow-root-user
```

Jede Zeile ist `WRITE <file>` (neu), `REPLACE <file>` (eine erzeugte Datei, die sich ändern würde), `SKIP <file>`
(geändert oder von Hand geschrieben, wird behalten) oder ein `NOTE`. Die letzte Zeile zählt sie, bei einem ersten
Lauf über das Media-Design:

```
PASS tpl->stpl 516 templates: 516 to write, 0 unchanged, 0 skipped, 0 errors; 126 notes in 58 templates
```

Dann konvertieren Sie wirklich und schreiben die Override-Regeln für den neuen Siteaccess:

```bash
./console exp:template:convert:tpl-to-stpl -s site \
    --from=extension/sevenx_themes_media/design/media \
    --to=extension/sevenx_themes_media/design/smarty_media \
    --override-ini=var/tmp/smarty-override.ini.append.php --allow-root-user
```

Jedes Ergebnis wird von Smarty kompiliert, bevor es geschrieben wird; ein Template, das nicht kompiliert, ist eine
`ERROR`-Zeile und wird nicht geschrieben, und der Befehl endet mit `FAIL` und Exit-Code 1. Jede Option, jede
Ausgabezeile und jeder Exit-Code steht in [Templates zwischen Template-Engines konvertieren](template-konvertierung.md).
Richten Sie den Siteaccess so ein, wie es [Teil 4 der Anleitung zum Konverter](smarty-templates-konverter.md#4-ein-siteaccess-der-das-konvertierte-design-ausliefert)
beschreibt (Design-Liste, Override-Regeln, `SiteList[]`, `AvailableSiteAccessList[]`, die Login-Einschränkung der
Rolle Anonymous, Caches leeren).

## 3. Die Hinweise lesen

Speichern Sie die Ausgabe und sortieren Sie die Hinweise nach Art:

```bash
./console exp:template:convert:tpl-to-stpl -s site --from=<design> --to=<port> --dry-run --allow-root-user \
    > var/tmp/convert.log 2>&1
grep '^NOTE' var/tmp/convert.log | sed 's/^NOTE [^:]*: //' | sort | uniq -c | sort -rn
```

Beim Media-Design beginnt die Liste mit

```
     23 {if} without a condition (a parse error in the .tpl): it prints nothing, as in the .tpl
      1 unknown function {}: it prints nothing, as in the .tpl
      1 the source has an error: parser error @ templates/user/reset_password_done.tpl\[1] No variable name found, this is most likely a bug in the template parser.
```

gefolgt von einer Zeile `the source has an error` je fehlerhafter Quelle.

Was jeder Hinweis bedeutet und was zu tun ist: [Seine Hinweise und was nach dem Konvertieren zu prüfen ist](smarty-templates-konverter.md#3-seine-hinweise-und-was-nach-dem-konvertieren-zu-prüfen-ist).
In der Praxis:

- `the source has an error: ...` ist der Parser des Template-Systems, der sich über das `.tpl` selbst beschwert. Das
  `.tpl` druckte, was der Parser daraus machte, und das `.stpl` tut dasselbe; korrigieren Sie das `.tpl` und
  konvertieren Sie diese Datei erneut.
- `unknown function {x}` bedeutet, dass eine Erweiterung im Siteaccess von `-s` nicht aktiv war. Konvertieren Sie mit
  dem richtigen erneut.
- Alles andere ist selten und nennt das Konstrukt; beheben Sie es von Hand im `.stpl`.

## 4. Die Ausgabe prüfen, Seite für Seite

Das Ziel ist dasselbe HTML aus beiden Siteaccesses. Rufen Sie jede Seite aus beiden ab, ohne die Debug-Ausgabe (sie
unterscheidet sich naturgemäß) und zuerst als anonymer Besucher:

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

Das `sed` entfernt den eigenen Pfad des Siteaccess aus den Links; das ist der eine Unterschied, den ein über die URI
zugeordneter Siteaccess immer hat. Andere Werte, die sich bei jeder Anfrage ändern, müssen vor dem Vergleich auf
dieselbe Weise entfernt werden: eine CSP-Nonce (`s/nonce="[^"]*"/nonce=""/g`), ein Formular-Token für angemeldete
Besucher (`ezxform_token`), Zeitstempel. Sehen Sie sich dann jedes `DIFF` an:

```bash
diff var/tmp/parity/tpl_fitness.html var/tmp/parity/stpl_fitness.html | head -40
```

Wiederholen Sie das angemeldet (kopieren Sie das Session-Cookie eines Testbenutzers in `curl -b 'eZSESSID...=...'`)
und für jeden Sprach-Siteaccess, wenn das Design mehrere hat. Entfernen Sie `var/tmp/parity/`, wenn Sie fertig sind.

## 5. Die Unterschiede beheben

Die Unterschiede, die bei der Media-Portierung auftraten, die häufigsten zuerst:

| Unterschied | Ursache | Behebung |
|---|---|---|
| Ein Zeilenumbruch mehr oder weniger um ein Tag | Smarty lässt den Zeilenumbruch nach einem Tag weg, das nichts druckt; eine Handänderung setzte ein Tag in dieselbe Zeile wie Text oder entfernte den Zeilenumbruch, den der Konverter geschrieben hatte | das Layout des Konverters beibehalten: Tags, die nichts drucken, in eigenen Zeilen |
| Ein Wert ist der Standardwert, wo das `.tpl` die Variable zeigte | `$a\|first_set('d')` im `.tpl` übergeht `$a`; der Konverter behält das bei (`first_set('d')`) | wenn das `.tpl` falsch war, beide zu `first_set($a, 'd')` korrigieren |
| Der Text `null`, `true` oder ein Wort erscheint | ein nacktes Wort im `.tpl` ist eine Zeichenkette; der Konverter behält das bei (`'null'`) | in beiden den echten Wert schreiben |
| Ein ganzer Teil ist leer | ein `.stpl` ist gescheitert: siehe den HTML-Kommentar `<!-- smarty: ... -->` bei eingeschalteter Debug-Ausgabe | [Problembehebung](smarty-templates-problembehebung.md) |
| Eine Liste zählt 1 für ein Wort oder druckt `Array` | eine Handänderung verwendete Smartys `count`, `explode`, `implode` statt `exp_count`, `exp_explode`, `exp_implode` | die `exp_`-Namen verwenden |
| Doppelt maskiertes `&amp;` oder HTML als Text angezeigt | eine Handänderung fügte Smartys `\|escape` zu einem Wert hinzu, den das `.tpl` schon gewaschen hatte | nur einmal maskieren: `\|wash` |
| Ein anderes Template wird verwendet | in `--override-ini` fehlte eine Override-Regel, oder ein neues `.stpl` ohne geleerte Template-Caches | die Regel ergänzen; die Template-Caches leeren |
| Ein Cache-Block zeigt den Inhalt einer anderen Seite | ein von Hand geschriebener `{expcacheblock}` ohne die Schlüssel, die ihn unterscheiden, oder zwei Blöcke mit derselben `id` in einer Datei | Schlüssel angeben; eindeutige ids |

Rufen Sie nach jeder Behebung erneut ab und vergleichen Sie. Die Media-Portierung erreichte byte-genau gleiche Seiten
für die Kategorie-, Listen-, Rezept- und Startseiten, die bei der Geschwindigkeitsmessung verwendet wurden.

## 6. Handänderungen und die Markierung

Jede Datei, die der Konverter schreibt, endet mit einem Markierungskommentar:

```
{* exp:template:convert tpl->stpl md5:<checksum> from templates/pagelayout.tpl -- generated; a file changed by hand is not replaced without --force *}
```

Die Prüfsumme deckt alles davor ab. Ein späterer Lauf des Konverters ersetzt ein Ziel nur, solange seine Markierung
noch zu seinem Inhalt passt. Sobald Sie ein erzeugtes `.stpl` bearbeiten, passt die Prüfsumme nicht mehr, und jeder
spätere Lauf druckt `SKIP ... was changed after it was generated` und lässt Ihre Änderung in Ruhe. Das
`pagelayout.stpl` der Media-Portierung, von Hand geändert, um die ganze Seite in einem Cache-Block zu halten, bleibt so
erhalten:

```
SKIP templates/pagelayout.tpl: extension/sevenx_themes_media/design/smarty_media/templates/pagelayout.stpl was changed after it was generated; --force replaces it
PASS tpl->stpl 516 templates: 0 to write, 515 unchanged, 1 skipped, 0 errors; 126 notes in 58 templates (a skipped file was changed or written by hand: --force replaces it)
```

Ein `.stpl`, das Sie selbst geschrieben haben, hat keine Markierung und wird als `written by hand` übersprungen.
`--force` ersetzt beide; verwenden Sie es mit `--file` für jeweils ein Template.

Die Markierung ist ein Kommentar und druckt daher nichts. Soll eine Datei dauerhaft Ihnen gehören, löschen Sie ihre
Markierungszeile: Sie gilt dann als „von Hand geschrieben“ und wird ohne `--force` nie ersetzt.

## 7. .tpl und .stpl im Gleichschritt halten

Solange beide Designs bestehen, entscheiden Sie, welches die Quelle ist:

**Das `.tpl`-Design bleibt die Quelle** (beide Websites in Produktion, die `.stpl`-Website wird erprobt):

- Ändern Sie nur die `.tpl`-Dateien. Führen Sie nach jeder Änderung den Konverter aus; er schreibt die erzeugten
  `.stpl`-Dateien neu, deren Quelle sich geändert hat (`REPLACE`), lässt die unveränderten in Ruhe und überspringt die
  von Hand bearbeiteten.
- Ein Probelauf sagt Ihnen, welche `.stpl`-Dateien hinter ihrem `.tpl` zurückliegen:

  ```bash
  ./console exp:template:convert:tpl-to-stpl -s site --from=<design> --to=<port> --dry-run --allow-root-user | grep '^REPLACE'
  ```

  Eine leere Liste bedeutet, dass die Portierung aktuell ist. Ein `SKIP` einer Datei, die Sie von Hand bearbeitet
  haben, bedeutet, dass sich ihre `.tpl`-Quelle darunter geändert haben kann: Vergleichen Sie die beiden und
  übertragen Sie die Änderung von Hand.

**Das `.stpl`-Design wird die Quelle** (die Umstellung ist erfolgt):

- Ändern Sie nur die `.stpl`-Dateien, und entfernen Sie die Markierungen oder übergehen Sie die `SKIP`-Zeilen.
- Wird irgendwo noch eine `.tpl`-Kopie gebraucht (ein anderer Siteaccess, eine Erweiterung, die `.tpl` ausliefert),
  konvertieren Sie mit `./console exp:template:convert:stpl-to-tpl` zurück
  ([Teil 6 der Anleitung zum Konverter](smarty-templates-konverter.md#6-smarty-templates-zurück-in-tpl-konvertieren)).
  Was keine `.tpl`-Form hat, bleibt als Kommentar `{* SMARTY ... *}` erhalten und wird gemeldet.

## 8. Umschalten

Vergleichen Sie vor dem Umschalten die Geschwindigkeit der beiden Siteaccesses, wie es
[Smarty-Templates: Leistung (Geschwindigkeit)](smarty-templates-leistung.md#wie-man-misst) beschreibt, und
entscheiden Sie, wie das Pagelayout gecacht wird ([die Seite in einem Cache-Block](smarty-templates-leistung.md#die-seite-in-einem-cache-block)).

Wenn die Seiten übereinstimmen:

1. Richten Sie den öffentlichen Siteaccess auf die Portierung aus: in seiner `site.ini.append.php` die Design-Liste des
   `.stpl`-Siteaccess und seine `override.ini.append.php` aus `--override-ini`. Oder machen Sie den `.stpl`-Siteaccess
   zu dem, auf den Ihr Host zeigt.
2. Leeren Sie die Caches oder stellen Sie bereit (`./console exp:velocity deploy --allow-root-user`).
3. Behalten Sie das `.tpl`-Design in der Fallback-Liste: Nicht portierte Templates, die statischen Dateien und jedes
   Template einer Erweiterung werden weiterhin dort gefunden.
4. Beobachten Sie eine Weile das Fehlerlog auf `smarty`-Fehler (`var/log/error.log` oder der Log-Viewer).

## Verwandte Seiten

- [Smarty-Templates: Leistung (Geschwindigkeit)](smarty-templates-leistung.md)
- [Von .tpl zu .stpl umziehen](../hilfe/template-sprache/von-tpl-zu-stpl-umziehen.md), das Kapitel des Handbuchs zur
  Template-Sprache
- [Der Konverter und das Portieren eines Designs](smarty-templates-konverter.md)
- [Problembehebung und FAQ](smarty-templates-problembehebung.md)
- [Caching und Leistung](smarty-templates-caching.md)
- [Templates zwischen Template-Engines konvertieren](template-konvertierung.md): jede Option, jede Ausgabezeile und
  jeder Exit-Code der Befehle
- Befehlsreferenz: [exp:template:convert](../beispiele/befehle/exptemplateconvert.md),
  [exp:template:convert:tpl-to-stpl](../beispiele/befehle/exptemplateconverttpltostpl.md) und
  [exp:template:convert:stpl-to-tpl](../beispiele/befehle/exptemplateconvertstpltotpl.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
