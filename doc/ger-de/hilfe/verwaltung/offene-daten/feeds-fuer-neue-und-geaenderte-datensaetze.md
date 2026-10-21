---
title: Feeds für neue und geänderte Datensätze
audience: government, admin, editor
category: public-sector
order: 100
summary: Unter Setup > RSS einen RSS-2.0- oder Atom-Feed einrichten, der die neuesten Datensätze des Katalogs auflistet, festlegen, welche Felder zu Titel und Beschreibung werden, und prüfen, was Nachnutzer erhalten.
keywords: RSS, Atom, Feed, neue Datensätze, RSS-Export, Setup RSS, rss/feed, Abonnement, CacheTime, offene Daten, Benachrichtigungen, feed, new datasets, rss export, subscription, open data, notifications
type: help
---
# Feeds für neue und geänderte Datensätze

Diese Seite richtet sich an Administratoren, die Nachnutzern und Journalisten einen Feed neuer Datensätze anbieten.
Exponential veröffentlicht unter **Setup > RSS** (`/rss/list`) Feeds im Format RSS 1.0, RSS 2.0, Atom, OPML und Apple
Podcasts; jeder Export listet die neuesten Objekte unterhalb seiner Quellen auf. Die Angaben und Bezeichnungen stammen
aus der [Anleitung zu RSS-Feeds](../../../anleitungen/rss-feeds.md).

## Inhalt

1. [Was der Feed enthält](#was-der-feed-enthält)
2. [Den Export anlegen](#den-export-anlegen)
3. [Den Feed prüfen](#den-feed-prüfen)
4. [Geänderte Datensätze](#geänderte-datensätze)
5. [Andere Wege, Menschen zu informieren](#andere-wege-menschen-zu-informieren)
6. [Probleme](#probleme)

## Was der Feed enthält

Ein Export listet die neuesten Objekte der gewählten Klasse unterhalb jedes Quellorts auf, bis zur eingestellten Anzahl
von Einträgen, mit den Attributen, die Sie als Titel, Beschreibung, Kategorie und Anhang gewählt haben. Seine Adresse
ist die öffentliche Website des Siteaccess des Exports, gefolgt von `/rss/feed/<address>`.

## Den Export anlegen

Sie brauchen die Richtlinie `rss/edit`. Der Siteaccess für die Redaktion bietet das RSS-Modul nicht an; verwenden Sie
die Administration.

1. Öffnen Sie **Setup > RSS** und klicken Sie auf **Neuer Export**.
2. **Der Feed**: Benennen Sie ihn („Neue Datensätze“), schreiben Sie eine Beschreibung, wählen Sie **RSS 2.0** oder
   **Atom 1.0** und geben Sie die Zugriffs-URL an, zum Beispiel `datasets`. Kreuzen Sie **Aktiv** an.
3. **Was der Feed auflistet**: Stellen Sie die Anzahl der Einträge ein, klicken Sie dann in der Quellkarte auf
   **Durchsuchen** und wählen Sie den Katalogordner. Kreuzen Sie **Unterknoten** an, wenn Datensätze in Themenordnern
   darunter liegen. Wählen Sie die Klasse `dataset` und klicken Sie auf **Setzen**; ordnen Sie **Titel** dem Titel und
   **Beschreibung** der Beschreibung zu; ordnen Sie **Kategorie** dem Thema zu, wenn Sie eines haben.
4. Klicken Sie auf **OK**. Die Karte zeigt nun **Aktiv**, die Adresse des Feeds und die Quelle.

Für einen Feed neuer Dateien statt neuer Datensatzseiten fügen Sie eine zweite Quelle mit der Klasse Ihrer
Distributionen hinzu.

## Den Feed prüfen

1. Klicken Sie auf der Karte des Exports auf **Feed öffnen**; der Browser zeigt das XML.
2. Prüfen Sie jeden Eintrag: Titel, Link zur Datensatzseite, Beschreibung, Datum.
3. Prüfen Sie ihn abgemeldet in einem Feedreader, den Sie verwenden, damit Sie sehen, was die Öffentlichkeit sieht.

Feeds werden `site.ini [RSSSettings] CacheTime` Sekunden lang zwischengespeichert; „Zuletzt geschrieben“ auf der Karte
sagt, wann die zwischengespeicherte Kopie erstellt wurde. Ein neuer Datensatz erscheint nach dieser Zeit oder sofort
nach `php bin/php/ezcache.php --clear-id=rss_cache`.

## Geänderte Datensätze

Ein RSS-Export listet die neuesten Objekte unterhalb seiner Quellen auf; er ist ein Feed für „Was ist neu“. Um
Menschen mitzuteilen, dass sich ein bestehender Datensatz geändert hat:

- fügen Sie unter dem Datensatz ein kurzes Objekt „Änderungshinweis“ hinzu (zum Beispiel einen datierten Artikel), wenn
  sich die Daten ändern, damit der Feed es auflistet; oder
- veröffentlichen Sie für wichtige Aktualisierungen eine eigene Nachricht.

Exponential erzeugt kein Änderungsprotokoll der Daten innerhalb einer Datei; es verzeichnet Versionen der Seite und
ihrer Dateien ([Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)).

## Andere Wege, Menschen zu informieren

| Weg | Werkzeug |
|---|---|
| E-Mail, wenn sich ein Teil der Website ändert | Benachrichtigungen: Benutzer folgen einem Teil der Website ([Benachrichtigungen](../../../anleitungen/benachrichtigungen-administrator.md)) |
| Eine Mailingliste für Datenankündigungen | Die Erweiterung `cjw_newsletter` ([Newsletter mit cjw_newsletter](../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md)) |
| Maschinen, die nach Änderungen abfragen | Die REST-API mit einer Liste der Unterknoten, sortiert nach Änderung ([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)) |

## Probleme

| Problem | Lösung |
|---|---|
| Ein Reader erhält einen Fehler | Der Export ist Inaktiv oder hat keine Adresse: bearbeiten, beides setzen, Aktiv ankreuzen |
| Der Feed ist leer | Der Quellort ist weg („missing“), oder die Klasse ist falsch |
| Ein neuer Datensatz fehlt | Warten Sie die CacheTime ab oder leeren Sie den RSS-Cache |

## Verwandte Seiten

- [RSS-Feeds](../../../anleitungen/rss-feeds.md)
- [RSS-Feeds für Ihre Website](../../../../eng-us/help/creators/website-creators/rss-feeds-for-your-site.md)
- [RSS: Feeds für Apple Podcasts, eine seitenweise Feedliste und sicherere Exporte](../../../../eng-us/features/6.0/rss-podcast-and-feed-list.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
