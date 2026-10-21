---
title: FAQ für Website-Ersteller
audience: website-creator, admin
category: website-creation
order: 750
summary: Kurze Antworten auf die Fragen, die Website-Ersteller am häufigsten stellen - Änderungen, die nicht erscheinen, Templates, Menüs, Sprachen, Formulare, Mail, Bilder, URLs, Layouts, Geschwindigkeit und wo es Hilfe gibt - jeweils mit der Seite, die es erklärt.
keywords: FAQ, häufige Fragen, Fehlerbehebung, Änderung erscheint nicht, Cache, Template wird nicht verwendet, Menü, Sprache, Mail wird nicht versendet, Bildgröße, URL, Layouts, Hilfe, Website-Ersteller, questions, troubleshooting, change does not show, template not used, mail not sent, image size, help, website creator
type: help
---
# FAQ für Website-Ersteller

Kurze Antworten mit der Seite, die jede einzelne erklärt.

## Meine Template-Änderung erscheint nicht

Leeren Sie den Template-Cache (`--clear-tag=template`); eine neue Template-Datei oder Override-Regel braucht zusätzlich
`--clear-id=template-override`; dann den Inhalts-Cache und, unter Velocity, dessen Antwort-Cache. Siehe
[Leistung und Caching](leistung-und-caching.md#caches-in-der-richtigen-reihenfolge-leeren).

## Welches Template hat diesen Teil der Seite erzeugt?

Schalten Sie die Kommentare zu Template-Pfaden ein, und suchen Sie im Seitenquelltext nach `START`
([Designs und die Fallback-Kette](designs-und-die-fallback-kette.md#die-kette-eines-siteaccess-lesen)).

## Wie ändere ich eine Art von Seite?

Mit einer Override-Regel, die auf den Klassenbezeichner passt ([Template-Overrides](template-overrides.md)).

## Ein Ordner erscheint nicht im Menü

Seine Klasse steht nicht in `TopIdentifierList[]`, er ist versteckt, oder der Cache-Block des Menüs enthält noch das
alte Menü ([Menüs und Navigation](menues-und-navigation.md)).

## Besucher erhalten in einer neuen Sektion "Zugriff verweigert"

Ergänzen Sie `content/read` für die Sektion in der Rolle Anonymous ([Rechte für Besucher](rechte-fuer-besucher.md)).

## Eine Seite in meiner zweiten Sprache fehlt

Das Objekt hat keine Übersetzung in einer der Sprachen in `SiteLanguageList[]` des Siteaccess und ist nicht immer
verfügbar ([Eine mehrsprachige Website bauen](mehrsprachige-websites.md)).

## Mails aus Formularen kommen nicht an

Eine frische Installation schreibt Mails in Dateien; stellen Sie einen echten Transport und einen echten Absender ein
([Formulare mit dem Informationssammler](formulare-mit-dem-informationssammler.md#mail-an-einen-empfänger)).

## Bilder haben die falsche Größe

Definieren Sie eine Bildvariante für diesen Zweck, und leeren Sie die erzeugten Bildvarianten
([Bilder und Bildvarianten](bilder-und-bildvarianten.md)).

## Eine alte Adresse zeigt "nicht gefunden"

Fügen Sie sie als Alias des Knotens oder als Platzhalter hinzu
([Weiterleitungen und verschobene Inhalte](weiterleitungen-und-verschobene-inhalte.md)).

## Mein Layout für die Landingpage wird nicht verwendet

Eine Regel mit höherer Priorität passt zuerst, oder das Layout ist nicht veröffentlicht ([Layout-Regeln](layout-regeln.md)).

## Die Website ist langsam

Messen Sie mit `exp:benchmark`, und folgen Sie dann den Stellschrauben der Reihe nach
([Leistung und Caching](leistung-und-caching.md)).

## Wo melde ich einen Fehler?

Auf der Seite **Einen Fehler melden** der Hilfe und unter [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md).

## Verwandte Seiten

- [Website-Ersteller: hier beginnen](website-ersteller-hier-beginnen.md)
- [Lesepfad für Website-Ersteller](lesepfad-fuer-website-ersteller.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
