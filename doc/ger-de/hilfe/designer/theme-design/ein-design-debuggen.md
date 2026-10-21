---
title: Ein Design debuggen
audience: designer, developer
category: theme-design
order: 170
summary: Herausfinden, welches Template, welcher Override und welches Stylesheet einen Teil einer Seite erzeugt hat, warum eine neue Datei ignoriert wird und wie Sie die Template-Syntax prüfen, mit den Debug-Einstellungen, den Kommentaren mit Template-Pfaden, dem Template-Prüfer und den Caches, die zu leeren sind.
keywords: Design debuggen, Templates debuggen, welches Template, Kommentare mit Template-Pfaden, ShowUsedTemplates, DebugOutput, Template-Debug, eztemplatecheck, Prüfung der Template-Syntax, neues Template ignoriert, veraltete Seite, Cache leeren, error.log, debug design, debugging templates, which template, template path comments, template debug, template syntax check, new template ignored, stale page, cache clear
type: help
---
# Ein Design debuggen

Diese Seite ist für Designer, die auf eine Seite schauen und fragen: „Woher kommt das?“ oder „Warum erscheint meine Änderung nicht?“.
Sie listet die Werkzeuge in der Reihenfolge, in der Sie sie meistens brauchen.

## Inhalt

1. [Zuerst: Welches Template hat das gezeichnet?](#zuerst-welches-template-hat-das-gezeichnet)
2. [Welche Templates wurden überhaupt verwendet?](#welche-templates-wurden-überhaupt-verwendet)
3. [Mein neues Template oder mein Override wird ignoriert](#mein-neues-template-oder-mein-override-wird-ignoriert)
4. [Meine Stylesheet-Änderung erscheint nicht](#meine-stylesheet-änderung-erscheint-nicht)
5. [Syntaxfehler in Templates](#syntaxfehler-in-templates)
6. [Fehler und Warnungen](#fehler-und-warnungen)
7. [Die Seite zeigt alte Inhalte](#die-seite-zeigt-alte-inhalte)
8. [Kurzreferenz](#kurzreferenz)

## Zuerst: Welches Template hat das gezeichnet?

Schalten Sie für einen Test-Siteaccess die **Kommentare mit Template-Pfaden** ein:

```ini
# settings/siteaccess/<test siteaccess>/site.ini.append.php
[TemplateSettings]
ShowTemplatePathComments=enabled
```

Die Ausgabe jedes Templates wird dann in Kommentare eingeschlossen wie
`<!-- START design/media/templates/pagelayout/footer.tpl --> ... <!-- STOP ... -->`. Öffnen Sie den Seitenquelltext oder den
Element-Inspektor des Browsers und lesen Sie den Kommentar um das Markup, das Sie interessiert. Es funktioniert mit ein- und
ausgeschaltetem Template-Compiler. Die Einstellung fügt jedem Template Markup hinzu und muss daher in der Produktion aus bleiben,
ebenso auf jedem Siteaccess, der JSON, XML oder RSS ausliefert (Kommentar in `settings/site.ini`). Einzelheiten:
[Kommentare mit Template-Pfaden](../../../../eng-us/features/6.0/template-path-comments.md).

## Welche Templates wurden überhaupt verwendet?

Mit eingeschalteter Debug-Ausgabe wird eine Tabelle aller Templates hinzugefügt, die für die Seite verwendet wurden:

```ini
[DebugSettings]
DebugOutput=enabled
# limit it to your own address
DebugByIP=enabled
DebugIPList[]=203.0.113.10

[TemplateSettings]
ShowUsedTemplates=enabled
```

`ShowUsedTemplates` braucht `DebugOutput` (Kommentar in `settings/site.ini`). Verwenden Sie Ihre eigene Adresse in `DebugIPList[]`;
die obige ist ein Beispiel für die Dokumentation.

## Mein neues Template oder mein Override wird ignoriert

Gehen Sie diese Punkte der Reihe nach durch:

1. **Der Template-Override-Cache.** Eine neue Template-Datei in einem Design oder eine neue Regel in `override.ini` wird erst nach
   `php bin/php/ezcache.php --clear-id=template-override` gesehen. Eine Seite, die direkt nach dem Hinzufügen von Templates plötzlich
   mit leerem Inhaltsbereich rendert, hat dieselbe Ursache.
2. **Der INI-Cache.** Eine neue Zeile in `override.ini` oder `design.ini` braucht `--clear-tag=ini`.
3. **Der Pfad.** Die Datei muss genau an dem Pfad liegen, nach dem der Kernel fragt, innerhalb von `templates/` (oder
   `override/templates/` für die `MatchFile` eines Overrides). Ein Tippfehler in `MatchFile` wird als
   „Custom match file: path '...' not found in any resource“ ins Fehlerprotokoll geschrieben.
4. **Die Kaskade.** Ein weiter oben stehendes Design kann dieselbe Datei haben. Die Kommentare mit Template-Pfaden zeigen, welche Kopie
   gewonnen hat ([Die Design-Kaskade](die-design-kaskade.md)).
5. **Die Reihenfolge der Overrides.** Eine andere Regel für dieselbe `Source` kann zuerst passen. Regeln werden nach `Priority`
   versucht (niedrigste zuerst; ohne `Priority` ganz am Ende), und der erste Treffer gewinnt ([Reihenfolge der Overrides](../vorlagen/override-reihenfolge.md)).
6. **Die Erweiterung.** Ist die Design-Erweiterung für diesen Siteaccess aktiv und in `DesignExtensions[]` aufgeführt?

## Meine Stylesheet-Änderung erscheint nicht

1. Wird die Datei überhaupt geladen? Die gepackte Datei im Netzwerkbereich ist nach einem Hash ihrer Teile benannt; mit
   `[TemplateSettings] DevelopmentMode=enabled` auf einem Test-Siteaccess liefert der Packer jede Datei einzeln aus, sodass Sie Ihre
   am Namen erkennen.
2. Das Admin-Pagelayout speichert den Link auf die gepackte Datei in einem Cache-Block. Leeren Sie nach einer CSS-Änderung in einem
   Admin-Design auch `template-block` (ein Deploy mit `--packer` tut das).
3. Der Browser kann die alte Datei halten: Laden Sie hart neu. Fügen Sie Links kein `?v=` hinzu ([Keine Versionsabfragen an Asset-Links](keine-versionsparameter-an-asset-links.md)).

## Syntaxfehler in Templates

Der Template-Prüfer parst Templates, ohne sie zu rendern:

```bash
# every template of a siteaccess's designs
./console exp:eztemplatecheck -s mysite
# or one design directory
./console exp:eztemplatecheck extension/mytheme/design/mytheme/templates/
```

Er gibt für jede Datei „Template file valid“ oder den Parse-Fehler aus. Führen Sie ihn vor jedem Commit eines Designs aus. Er prüft
nur die Syntax: Ein Operator, der nicht existiert (oder auf dem Siteaccess nicht aktiv ist), lässt sich problemlos parsen und
scheitert erst beim Rendern der Seite; öffnen Sie also auch die Seiten.

## Fehler und Warnungen

Template-Fehler und -Warnungen (nicht registrierte Operatoren, unbekannte Funktionen, fehlende Include-Dateien) erscheinen in der
Debug-Ausgabe; Fehler werden auch ins Fehlerprotokoll geschrieben (`site.ini [DebugSettings] AlwaysLog[]=error`), das Setup >
Logdateien zeigt. Ein unbekannter Operator ist die übliche Folge davon, einen Operator aus einer Erweiterung zu verwenden, die auf
dem Siteaccess nicht aktiv ist.

## Die Seite zeigt alte Inhalte

- **View-Cache.** Vollansichten werden zwischengespeichert (`site.ini [ContentSettings] ViewCaching=enabled`, zwischengespeicherte
  View-Modi `CachedViewModes=full;sitemap;pdf`). Das Veröffentlichen von Inhalten leert die richtigen Seiten; eine Template-Änderung
  nicht. Leeren Sie nach einer Änderung an einem View-Template `--clear-id=content`.
- **Cache-Blöcke.** Teile in `{cache-block}` behalten ihre Ausgabe, bis sich ihre Schlüssel ändern oder sie ablaufen; leeren Sie
  `--clear-id=template-block`.
- **Velocity.** Auf Installationen, die den Anwendungsserver Velocity verwenden, ist sein Antwort-Cache getrennt:
  `./console exp:velocity cache clear`.

## Kurzreferenz

| Symptom | Was Sie zuerst versuchen |
|---|---|
| Welche Datei hat das gezeichnet? | `ShowTemplatePathComments=enabled` auf einem Test-Siteaccess |
| Neues Template ignoriert | `--clear-id=template-override` |
| Neue INI-Zeile ignoriert | `--clear-tag=ini` |
| View-Template geändert, alte Seite | `--clear-id=content` |
| Teil in einem Cache-Block geändert | `--clear-id=template-block` |
| CSS-Änderung fehlt in der Administration | Deploy mit `--packer`, dann hart neu laden |
| „Operator '...' is not registered“ | die Erweiterung dieses Operators ist hier nicht aktiv |
| Template lässt sich nicht parsen | `./console exp:eztemplatecheck` |

## Verwandte Seiten

- [Templates debuggen](../vorlagen/template-debugging.md)
- [Template-Kompilierung und Cache](../vorlagen/template-kompilierung-und-cache.md)
- [Caching für Designer](caching-fuer-designer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
