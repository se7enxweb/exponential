---
title: Templates debuggen
audience: developer, designer, app-creator
category: extension-development
order: 150
summary: Herausfinden, warum ein Template, ein Override oder eine Variable nicht tut, was Sie erwarten - verwendete Templates, Pfadkommentare, Template-Debug, der Operator attribute, debug-log und Zeitmesspunkte sowie die Fehlermeldungen der Engine.
keywords: Templates debuggen, Fehlersuche, verwendete Templates, ShowUsedTemplates, ShowTemplatePathComments, Template-Debug, attribute show, debug-log, debug-timing-point, debug-accumulator, unknown template variable, operator not registered, debug templates
type: help
---
# Templates debuggen

Teil 2, Kapitel 15. Probleme mit Templates fallen in drei Gruppen: Es wird das falsche Template verwendet, eine Variable
ist nicht das, was Sie denken, oder das Template enthält einen Fehler. Dieses Kapitel gibt Ihnen für jede Gruppe das
Werkzeug. Schalten Sie zuerst die Debug-Ausgabe ein
([Debug-Ausgabe und Logs für Entwickler](../teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md)).

## Welches Template wird verwendet?

| Einstellung (`site.ini [TemplateSettings]`) | Wirkung |
|---|---|
| `ShowUsedTemplates=enabled` | die Debug-Ausgabe listet jedes Template der Seite auf: angefordert, verwendet und die Override-Regel, die es ausgewählt hat |
| `ShowTemplatePathComments=enabled` | jedes dargestellte Template wird im Seitenquelltext in Kommentare wie `<!-- START design/media/templates/pagelayout/footer.tpl -->` und `<!-- STOP ... -->` eingeschlossen |
| `Debug=enabled` (mit `ShowXHTMLCode=enabled`) | das ältere Inline-Template-Debug: Kommentare mit den Template-Namen im HTML |

Nur für die Entwicklung: Die Kommentare landen in jeder Ausgabe, auch in Mails, JSON, XML und RSS. Lassen Sie sie im
Produktivbetrieb nie eingeschaltet.

Greift ein Override nicht, zeigt Ihnen die Tabelle der verwendeten Templates, was der Kernel angefordert hat (`Source`)
und was er ausgewählt hat; vergleichen Sie das mit Ihrem Block in `override.ini`
([Template-Overrides: die Grundlagen](grundlagen-der-template-overrides.md)).

## Was steckt in dieser Variablen?

```text
{$node|attribute( 'show', 1 )}
```

gibt eine Tabelle der Attribute der Variablen aus, eine Ebene tief (`2` für zwei Ebenen). Bei einem Knoten listet sie
`name`, `node_id`, `object`, `data_map`, `url_alias` und den Rest auf; so finden Sie den Namen des Attributs, das Sie
brauchen. Entfernen Sie die Zeile vor dem Commit: Sie ist nur für Ihre Augen bestimmt. Beachten Sie, dass sie jedes
Attribut liest, auch die teuren.

Werte, die Sie nicht in der Seite haben möchten, schreiben Sie in die Debug-Ausgabe:

```text
{debug-log var=$node.name msg='the node name'}
{debug-log msg='reached the teaser branch'}
```

Die Engine gibt im Debug-Bericht `Debug: (the node name)` gefolgt vom Wert aus.

## Wie lange dauert es?

```text
{debug-timing-point id='menu'}
    ... the expensive part ...
{/debug-timing-point}
```

setzt einen Zeitmesspunkt um einen Teil des Templates; `debug-accumulator` summiert wiederholte Blöcke. Die Zeiten
erscheinen in der Zeittabelle der Debug-Ausgabe. Kombinieren Sie das mit `[DatabaseSettings] SQLOutput=enabled`, um zu
sehen, welcher Fetch langsam ist.

## Die Meldungen der Engine

| Meldung in der Debug-Ausgabe | Bedeutung | Lösung |
|---|---|---|
| `Unknown template variable 'x' in namespace ''` | die Variable wurde hier nie definiert | definieren Sie sie mit `{def}`, übergeben Sie sie an das Include oder verwenden Sie `first_set( $x, default )` |
| `Operator 'x' is not registered` | es gibt keinen Operator dieses Namens (Tippfehler, oder seine Erweiterung ist nicht aktiv) | prüfen Sie den Namen in [Eingebaute Template-Operatoren](../teil-8-referenz/referenz-der-template-operatoren.md); aktivieren Sie die Erweiterung |
| `Object problem with operator 'x'` | die Klasse des Operators konnte nicht eingerichtet werden | ein PHP-Problem in der Erweiterung; siehe [Konstruktoren alten Stils und statische Aufrufe](../teil-4-aktualisieren/alte-konstruktoren-und-statische-aufrufe.md) |
| `No template could be loaded for "x.tpl" using resource "design"` | der Pfad liegt in keiner Design-Basis | prüfen Sie den Pfad und die [Design-Kette](die-design-kette.md) |
| `parser error` mit einer Zeilennummer | ein Syntaxfehler, zum Beispiel `max=3` statt `max 3` in `foreach` | lesen Sie die Zeile; vergleichen Sie mit [Templates für Builder](templates-fuer-builder.md) |

Jede Meldung nennt die Template-Datei sowie Zeile und Spalte, zum Beispiel `full.tpl:12[5]`.

## Veraltete Ausgabe

Wenn Ihre Änderung nicht erscheint:

1. Ist das Template aus einer alten Version kompiliert? Leeren Sie den Template-Cache: `php bin/php/ezcache.php --clear-tag=template`.
2. Liegt die Seite im View-Cache? Leeren Sie ihn: `php bin/php/ezcache.php --clear-tag=content`.
3. Ist es eine **neue** Datei? Leeren Sie den Template-Override-Cache: `--clear-id=template-override`.
4. Liegt der Teil in einem `{cache-block}`? Leeren Sie `--clear-id=template-block`.
5. Velocity hält einen Response-Cache: `./console exp:velocity cache clear --apply`.

Während Sie arbeiten, erspart Ihnen `TemplateCompile=disabled` Schritt 1 ganz.

## Ein Vorgehen für eine kaputte Seite

1. Lesen Sie die Fehler in der Debug-Ausgabe; der erste ist meist die Ursache.
2. Finden Sie das Template mit den Pfadkommentaren.
3. Geben Sie die Variablen, auf die Sie sich verlassen, mit `attribute( 'show' )` aus.
4. Kürzen Sie das Template, bis der Fehler verschwindet; das Letzte, was Sie entfernt haben, ist das Problem.
5. Nehmen Sie die Debug-Zeilen wieder heraus, leeren Sie die Caches und prüfen Sie erneut.

eZpedias [„Learning: Template Debugging“](https://ezpedia.exponential.earth/en/learning/learning_template_debugging)
beschreibt dieselben Werkzeuge aus den früheren Jahren.

## Verwandte Seiten

- Zurück: [Die Template-Operatoren, die Builder am häufigsten nutzen](die-wichtigsten-template-operatoren-fuer-builder.md)
- Weiter: [Caching und Templates](caching-und-templates.md)
- [Template-Pfadkommentare](../../../funktionen/6.0/template-pfad-kommentare.md)
- [Die Exp Debug-Leiste](../../../bc/6.0/debug-leiste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
