---
title: Rundgang durch die Funktionen
audience: customer, businesses, editor
category: customer-success
order: 50
summary: Die wichtigsten Funktionen von Exponential in einem Rundgang für Website-Betreiber - Inhalte, Seiten, Sprachen und Websites, Handel, Kommunikation, Integration, der Schreibassistent, Betrieb und Sicherheit - mit der Angabe, wer in Ihrem Team welche nutzt und wo Sie mehr lesen.
keywords: Rundgang durch die Funktionen, Funktionen, was Exponential kann, Inhalte, Layouts, mehrsprachig, Multisite, Shop, Newsletter, REST-API, Exponential Intelligence, Prüfprotokoll, Sicherheit, feature tour, features, what Exponential does, content, layouts, multilingual, multisite, shop, newsletters, REST API, audit trail, security
type: help
---
# Rundgang durch die Funktionen

Diese Seite ist für Betreiber, die alles wissen möchten, was ihre Website kann, nicht nur das, wofür sie ursprünglich
gebaut wurde. Jeder Bereich listet die Funktionen auf, die mit Exponential geliefert werden, wer in einem typischen Team
sie nutzt und wo Sie mehr lesen. Die Listen stammen aus der `README.md` des Repositorys („Main Exponential features“ und
„Recent improvements“); welche davon Ihre Website nutzt, hängt davon ab, wie sie gebaut wurde und welche Erweiterungen
aktiv sind.

## Inhalte

| Funktion | Wer sie nutzt | Lesen Sie |
|---|---|---|
| Inhaltsklassen, die Sie selbst definieren (Artikel, Produkte, Veranstaltungen ...) | Partner definiert, Redaktion füllt | [Inhaltsklassen für Websites](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md) |
| Versionskontrolle: Jede Veröffentlichung bewahrt die frühere Version | Redaktion | [Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md) |
| Freigabe von Inhalten und zeitgesteuerte Veröffentlichung | Redaktion, freigebende Personen | [Workflows](../../../anleitungen/workflows.md) |
| Multimedia mit automatischer Umwandlung und Skalierung von Bildern | Redaktion | [Medien und Uploads](../../medien-und-uploads.md) |
| Eingebaute Suchmaschine | Besucher, Redaktion | [Suchen](../../suchen.md) |
| Fein abgestufte, rollenbasierte Berechtigungen | Betreiber entscheidet, Administrator richtet ein | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) |

## Seiten

| Funktion | Wer sie nutzt | Lesen Sie |
|---|---|---|
| Exponential Layouts: Layouts, Zonen und Blöcke, von der Redaktion angeordnet, und Regeln, die entscheiden, welche Seiten sie erhalten | Redaktion, Marketing | [Der Layouts-Editor](../../layout-editor.md) |
| Gemeinsame Layouts: ein Header oder Footer, einmal gebaut und in andere Layouts verknüpft | Partner richtet ein, Redaktion pflegt | `README.md`, „Exponential Layouts“ |
| Trennung von Inhalt und Darstellung, eine erweiterbare Template-Engine | Partner, Designer | [Template-Overrides](../../ersteller/website-ersteller/template-overrides.md) |

## Sprachen und Websites

| Funktion | Wer sie nutzt | Lesen Sie |
|---|---|---|
| Mehrsprachige Inhalte, mit 219 Locales | Übersetzer, Redaktion | [Sprachen](../../sprachen.md), [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md) |
| Sprachen mit Schreibrichtung von rechts nach links | Redaktion | [Von rechts nach links](../../../funktionen/6.0/rechts-nach-links.md) |
| Mehrere Websites aus einer Installation | Betreiber, Administrator | [Multisite-Einrichtung](../../ersteller/website-ersteller/multisite-einrichten.md) |

## Handel

| Funktion | Wer sie nutzt | Lesen Sie |
|---|---|---|
| Der eingebaute Shop: Produkte, Warenkorb, Kasse, Bestellungen, Mehrwertsteuer, Währungen, Rabatte, Wunschzettel | Shop-Team | [Ihren Shop betreiben](../handel/README.md) |
| Das Shop-Dashboard mit anstehender Arbeit und nächsten Schritten | Shop-Leitung | [Shop-Berichte](../handel/shop-berichte.md) |
| Dauerhafte Bestellbelege | Kunden, Shop-Leitung | [Rechnungen und Belege](../handel/rechnungen-und-belege.md) |

## Kommunikation

| Funktion | Wer sie nutzt | Lesen Sie |
|---|---|---|
| Kontaktformulare (der Information Collector) | Besucher; Mitarbeitende lesen die Einsendungen | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |
| Feeds als RSS, ATOM, iTunes-RSS und OPML | Leser, Partner, Podcast-Hörer | [RSS-Feeds](../../../anleitungen/rss-feeds.md) |
| E-Mail-Einstellungen: eine Seite, auf der Menschen wählen, welche E-Mails sie erhalten | Besucher, Mitglieder | [E-Mail-Einstellungen](../../../funktionen/6.0/e-mail-einstellungen.md) |
| Benachrichtigungen, wenn sich verfolgte Inhalte ändern | Redaktion, Mitglieder | [Benachrichtigungen](../../../funktionen/6.0/benachrichtigungen.md) |
| Newsletter mit der Erweiterung `cjw_newsletter` | Marketing | [cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md) |

## Integration

| Funktion | Wer sie nutzt | Lesen Sie |
|---|---|---|
| Eine Headless-CRUD-REST-API | Entwickler, Apps | [REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md) |
| Remote-Dienste für Apps und andere Systeme | Entwickler | [Remote-Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md) |
| Tausende Open-Source-Erweiterungen über eine Plugin-API | Partner | `README.md`, „Main Exponential features“ |

## Der Schreibassistent

Exponential Intelligence schreibt Entwürfe in der Administration und veröffentlicht nie von sich aus; jedes Nachschlagen
und jeder Entwurf läuft mit den eigenen Berechtigungen des Redakteurs, Kosten lassen sich deckeln, und eine einzige
Einstellung schaltet ihn überall ab (`README.md`, „Exponential Intelligence“). Wer ihn nutzt: die Redaktion, wobei der
Betreiber entscheidet, ob und mit welchem Anbieter ([expai](../../../funktionen/6.0/erweiterungen/expai.md)).

## Betrieb

| Funktion | Wer sie nutzt | Lesen Sie |
|---|---|---|
| Ein manipulationssicheres Prüfprotokoll: wer was getan hat | Betreiber, Administrator | [Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md) |
| Der Inhaltsfinder: Inhalte anhand von ID, Adresse oder Dateipfad finden | Administrator, erfahrene Redakteure | [Der Inhaltsfinder](../../../funktionen/6.0/inhalts-finder.md) |
| Inhaltsaufträge: große Entfernungen, Kopien und Verschiebungen im Hintergrund | Redaktion, Administrator | [Inhaltsaufträge](../../../funktionen/6.0/inhaltsauftraege.md) |
| Der Log-Viewer | Administrator, Hoster | [Der Log-Viewer](../../../funktionen/6.0/log-betrachter.md) |
| Wartungsmodus mit einer freundlichen Seite | Administrator | [Wartungsmodus](../../../anleitungen/wartungsmodus.md) |
| Die Upgrade-Prüfung von Dateien und Datenbank | Administrator, Partner | [Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md) |
| Exponential Velocity, der mitgelieferte Anwendungsserver | Hoster | [Velocity für Unternehmen](../../unternehmen/zuverlaessigkeit/velocity-fuer-unternehmen.md) |
| Das Hilfesystem in der Administration (`/exphelp/dashboard`) | Mitarbeitende, deren Rolle `exphelp/read` hat | [Das Hilfesystem](../../../funktionen/6.0/hilfesystem.md) |

## Sicherheit

| Funktion | Wer sie nutzt | Lesen Sie |
|---|---|---|
| Zweistufige Anmeldung | Alle mit einem Konto | [Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md) |
| Formular-Tokens gegen Cross-Site Request Forgery | Automatisch | [„This form has expired“](../../../funktionen/6.0/seite-abgelaufenes-formular.md) |
| Content Security Policy | Hoster, Administrator | [Content Security Policy](../../../funktionen/6.0/content-security-policy.md) |
| Ratschläge zur Härtung von Produktiv-Websites | Hoster, Partner | [Sicherheitshärtung](../../sicherheitshaertung.md) |

## Den Rundgang nutzen

Gehen Sie ihn einmal im Jahr mit Ihrem Partner durch, in der [jährlichen Überprüfung des Nutzens](jaehrliche-ueberpruefung-des-nutzens.md),
und markieren Sie jede Funktion: genutzt, nicht gebraucht oder einen Versuch wert. Die Liste „einen Versuch wert“ ist
oft die günstigste Verbesserung, die Sie vornehmen können.

## Verwandte Seiten

- [Funktionen, die Kunden oft übersehen](oft-uebersehene-funktionen.md)
- [Was der eingebaute Shop leistet](../../unternehmen/shops/was-der-eingebaute-shop-kann.md)
- [Wofür Sie nicht bezahlen](../../unternehmen/kosten/wofuer-sie-nicht-bezahlen.md)
- [Jährliche Überprüfung des Nutzens](jaehrliche-ueberpruefung-des-nutzens.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
