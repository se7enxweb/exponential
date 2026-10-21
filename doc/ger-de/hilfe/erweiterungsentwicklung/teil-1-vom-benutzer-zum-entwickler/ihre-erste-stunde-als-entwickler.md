---
title: Ihre erste Stunde als Entwickler
audience: user, developer, designer, app-creator
category: extension-development
order: 50
summary: Eine angeleitete Stunde - sehen, wer eine Einstellung setzt, sie ändern und zurücknehmen, das Template hinter einer Seite finden, die Debug-Ausgabe und ein Log lesen und PHP mit geladenem Kernel ausführen.
keywords: erste Stunde, Tutorial, Einsteiger, Debug-Ausgabe, verwendete Templates, Template-Pfadkommentare, Log, rückgängig machen, Sicherung, first hour, tutorial, beginner, exp:ini where, debug output, used templates, template path comments, log, ezexec, undo, backup
type: help
---
# Ihre erste Stunde als Entwickler

Teil 1, Kapitel 5. Dieses Kapitel ist eine angeleitete Stunde auf der Entwicklungsinstallation aus dem vorigen Kapitel.
Sie machen sechs kleine Übungen, jede mit etwas zum Eintippen und etwas zum Ansehen. Hier wird noch kein Code
geschrieben; es geht darum zu lernen, wie Sie das System *ansehen*, und das ist der größte Teil der Arbeit eines
Entwicklers.

> **Hinweis**
> Machen Sie das nur auf Ihrer Entwicklungsinstallation. Jede Übung lässt sich rückgängig machen, und der letzte
> Abschnitt zeigt, wie.

## Übung 1: Woher kommt eine Einstellung? (10 Minuten)

Jedes Verhalten von Exponential kommt aus Einstellungen in Dateien `.ini`, und viele Dateien können denselben Wert
setzen. Die Frage „Welche Datei gewinnt?“ ist die erste, die ein Entwickler zu stellen lernt.

```bash
./console exp:ini where site.ini/SiteSettings/SiteName --allow-root-user
```

Sie sehen jede Datei, die `SiteName` setzt, in der Reihenfolge, in der sie gelesen werden, und am Ende den geltenden
Wert. Der Standard des Kernels in `settings/site.ini` kommt zuerst; Overrides, Siteaccess-Einstellungen und
Erweiterungen kommen später und gewinnen.

Jetzt dasselbe für eine Einstellung, die eine Erweiterung ändert:

```bash
./console exp:ini where design.ini/ExtensionSettings/DesignExtensions --allow-root-user
```

Das ist die Liste der Erweiterungen, deren Ordner `design/` das Template-System durchsucht. Jede Zeile nennt die
Erweiterung, die sich hinzugefügt hat. So stecken sich Erweiterungen ein: indem sie einer Einstellung eine Zeile
hinzufügen.

## Übung 2: eine Einstellung ändern und zurücknehmen (10 Minuten)

```bash
./console exp:ini set site.ini/SiteSettings/SiteName "My dev site" global --dry-run --allow-root-user
```

`--dry-run` gibt die Änderung als Diff aus und schreibt nichts. Führen Sie den Befehl ohne `--dry-run` noch einmal aus.
Er schreibt `settings/override/site.ini.append.php`, behält eine Kopie der alten Datei unter
`var/backup/ini/<timestamp>/` und leert den Einstellungs-Cache. Laden Sie eine Seite Ihrer Website neu: Der Name hat
sich geändert.

Nehmen Sie es zurück:

```bash
./console exp:ini rem site.ini/SiteSettings/SiteName global --allow-root-user
./console exp:ini get site.ini/SiteSettings/SiteName --allow-root-user
```

Der geltende Wert ist wieder der von vorher. Sie haben gerade die häufigste Aufgabe eines Builders erledigt, sicher und
umkehrbar.

## Übung 3: Welches Template hat diese Seite gezeichnet? (10 Minuten)

Schalten Sie die Liste der verwendeten Templates ein (falls Sie es im vorigen Kapitel nicht getan haben):

```bash
./console exp:ini set site.ini/TemplateSettings/ShowUsedTemplates enabled global --allow-root-user
./console exp:ini set site.ini/DebugSettings/DebugOutput enabled global --allow-root-user
```

Öffnen Sie die Startseite Ihrer Website und scrollen Sie nach unten. Die Debug-Ausgabe hat jetzt eine Tabelle mit jedem
verwendeten Template, mit dem Template, das *angefordert*, und der Datei, die *verwendet* wurde. Wenn sich beide
unterscheiden, hat ein Override das Standard-Template ersetzt. Finden Sie in der Liste das Pagelayout (das Template, das
den Rahmen um jede Seite zeichnet).

Schalten Sie dann die Pfadkommentare ein, laden Sie neu und sehen Sie sich den Seitenquelltext an (Strg+U):

```bash
./console exp:ini set site.ini/TemplateSettings/ShowTemplatePathComments enabled global --allow-root-user
```

Jeder Teil des HTML ist jetzt von Kommentaren umschlossen, die seine Template-Datei nennen. Wenn Sie etwas auf einer
Seite ändern möchten, finden Sie so die Datei, die Sie ändern müssen. Mehr dazu in
[Template-Pfadkommentare](../../../../eng-us/features/6.0/template-path-comments.md).

## Übung 4: die Debug-Ausgabe lesen (10 Minuten)

Sehen Sie sich den Rest der Debug-Ausgabe unter der Seite an:

- **Fehler und Warnungen**, jede mit einer Bezeichnung, die den Teil des Systems nennt, der sie ausgelöst hat.
- **Zeitmesspunkte**: wie lange jede Phase der Anfrage gedauert hat.
- **Template-Verwendung** aus Übung 3.
- **SQL**, wenn `[DatabaseSettings] SQLOutput` eingeschaltet ist (probieren Sie es aus und schalten Sie es danach
  wieder aus: Es ist lang).

Öffnen Sie eine Seite, die es nicht gibt, zum Beispiel `/content/view/full/999999999`. Die Debug-Ausgabe sagt Ihnen,
warum Sie eine Fehlerseite sehen. Diese Gewohnheit, zu lesen, was das System sagt, bevor Sie raten, spart mehr Zeit als
jede andere.

## Übung 5: eine Log-Datei lesen (10 Minuten)

Fehler werden auch in Log-Dateien unter `var/log/` geschrieben (bei manchen Installationen unter `var/<site>/log/`):

```bash
ls var/log/
tail -n 20 var/log/error.log
```

Die Administration hat einen Viewer für dieselben Dateien: **Setup > Logs**, erklärt in
[Der Log-Viewer](../../../../eng-us/features/6.0/log-viewer.md). Jede Fehlerzeile trägt eine Referenz, nach der Sie suchen
können; siehe [Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md).

## Übung 6: ein Skript mit geladenem Kernel ausführen (10 Minuten)

Legen Sie in Ihrem eigenen Erweiterungsordner eine Datei an, `extension/mysite_hello/scripts/hello.php`:

```php
<?php
// Runs with the kernel loaded: php bin/php/ezexec.php extension/mysite_hello/scripts/hello.php
$ini = eZINI::instance( 'site.ini' );
$cli = eZCLI::instance();
$cli->output( 'Site name: ' . $ini->variable( 'SiteSettings', 'SiteName' ) );
$cli->output( 'Active extensions: ' . count( $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ) ) );
```

Prüfen Sie sie und führen Sie sie aus:

```bash
php -l extension/mysite_hello/scripts/hello.php
php bin/php/ezexec.php extension/mysite_hello/scripts/hello.php
```

`bin/php/ezexec.php` lädt den Kernel (Einstellungen, Datenbank, Autoloads) und führt dann Ihre Datei aus. Sie haben
gerade zwei Klassen des Kernels verwendet: `eZINI`, das Einstellungen liest, und `eZCLI`, das ins Terminal schreibt.
Beide werden in [Teil 8](../teil-8-referenz/README.md) beschrieben: [eZINI](../teil-8-referenz/ezini.md) und
[eZScript und eZCLI](../teil-8-referenz/ezscript-und-ezcli.md).

## Alles zurücknehmen

```bash
./console exp:ini rem site.ini/TemplateSettings/ShowTemplatePathComments global --allow-root-user
```

Lassen Sie `DebugOutput` und `ShowUsedTemplates` eingeschaltet, während Sie das Buch durcharbeiten; schalten Sie sie auf
dieselbe Weise mit `exp:ini rem` aus, wenn Sie fertig sind. Jeder Schreibvorgang hat eine Sicherung unter
`var/backup/ini/` angelegt, sodass selbst ein Fehler nur eine Kopie entfernt ist.

## Was Sie gelernt haben

| Sie können jetzt | Mit |
|---|---|
| Finden, welche Datei einen Wert setzt | `exp:ini where` |
| Eine Einstellung sicher ändern und zurücknehmen | `exp:ini set`, `--dry-run`, `exp:ini rem` |
| Das Template hinter jedem Teil einer Seite finden | verwendete Templates, Template-Pfadkommentare |
| Lesen, warum eine Seite fehlgeschlagen ist | Debug-Ausgabe, `var/log/error.log`, Setup > Logs |
| Ihr eigenes PHP mit geladenem Kernel ausführen | `bin/php/ezexec.php` |

## Verwandte Seiten

- Zurück: [Eine Entwicklungsinstallation einrichten](eine-entwicklungsinstallation-einrichten.md)
- Weiter: [Den Code lesen, den Sie schon haben](den-vorhandenen-code-lesen.md)
- [Einstellungen mit exp:ini ändern](../teil-2-builder/der-befehl-exp-ini.md)
- [Templates debuggen](../teil-2-builder/templates-debuggen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
