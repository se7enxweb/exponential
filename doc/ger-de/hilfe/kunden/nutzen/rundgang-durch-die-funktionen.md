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
| Inhaltsklassen, die Sie selbst definieren (Artikel, Produkte, Veranstaltungen ...) | Partner definiert, Redaktion füllt | [Inhaltsklassen für Websites](../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md) |
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
| Trennung von Inhalt und Darstellung, eine erweiterbare Template-Engine | Partner, Designer | [Template-Overrides](../../../../eng-us/help/creators/website-creators/template-overrides.md) |

## Sprachen und Websites

| Funktion | Wer sie nutzt | Lesen Sie |
|---|---|---|
| Mehrsprachige Inhalte, mit 219 Locales | Übersetzer, Redaktion | [Sprachen](../../sprachen.md), [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md) |
| Sprachen mit Schreibrichtung von rechts nach links | Redaktion | [Von rechts nach links](../../../../eng-us/features/6.0/right-to-left.md) |
| Mehrere Websites aus einer Installation | Betreiber, Administrator | [Multisite-Einrichtung](../../../../eng-us/help/creators/website-creators/multisite-setup.md) |

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
| E-Mail-Einstellungen: eine Seite, auf der Menschen wählen, welche E-Mails sie erhalten | Besucher, Mitglieder | [E-Mail-Einstellungen](../../../../eng-us/features/6.0/mail-preferences.md) |
| Benachrichtigungen, wenn sich verfolgte Inhalte ändern | Redaktion, Mitglieder | [Benachrichtigungen](../../../../eng-us/features/6.0/notifications.md) |
| Newsletter mit der Erweiterung `cjw_newsletter` | Marketing | [cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md) |

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
Betreiber entscheidet, ob und mit welchem Anbieter ([expai](../../../../eng-us/features/6.0/extensions/expai.md)).

## Betrieb

| Funktion | Wer sie nutzt | Lesen Sie |
|---|---|---|
| Ein manipulationssicheres Prüfprotokoll: wer was getan hat | Betreiber, Administrator | [Das Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md) |
| Der Inhaltsfinder: Inhalte anhand von ID, Adresse oder Dateipfad finden | Administrator, erfahrene Redakteure | [Der Inhaltsfinder](../../../../eng-us/features/6.0/content-finder.md) |
| Inhaltsaufträge: große Entfernungen, Kopien und Verschiebungen im Hintergrund | Redaktion, Administrator | [Inhaltsaufträge](../../../../eng-us/features/6.0/content-jobs.md) |
| Der Log-Viewer | Administrator, Hoster | [Der Log-Viewer](../../../../eng-us/features/6.0/log-viewer.md) |
| Wartungsmodus mit einer freundlichen Seite | Administrator | [Wartungsmodus](../../../anleitungen/wartungsmodus.md) |
| Die Upgrade-Prüfung von Dateien und Datenbank | Administrator, Partner | [Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md) |
| Exponential Velocity, der mitgelieferte Anwendungsserver | Hoster | [Velocity für Unternehmen](../../../../eng-us/help/businesses/reliability/velocity-for-businesses.md) |
| Das Hilfesystem in der Administration (`/exphelp/dashboard`) | Mitarbeitende, deren Rolle `exphelp/read` hat | [Das Hilfesystem](../../../../eng-us/features/6.0/help-system.md) |

## Sicherheit

| Funktion | Wer sie nutzt | Lesen Sie |
|---|---|---|
| Zweistufige Anmeldung | Alle mit einem Konto | [Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md) |
| Formular-Tokens gegen Cross-Site Request Forgery | Automatisch | [„This form has expired“](../../../../eng-us/features/6.0/form-expired-page.md) |
| Content Security Policy | Hoster, Administrator | [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md) |
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
