---
title: RSS-, Atom- und OPML-Feeds für Integrationen
audience: integrator, developer, admin
category: integration
order: 100
summary: Feeds als einfachste Pull-Integration - Inhalte als RSS 1.0, RSS 2.0, Atom, OPML oder Podcast-Feeds exportieren, die RSS-Feeds anderer Systeme per Cronjob importieren, wie importierte Einträge geschlüsselt werden, Caching, Aufräumen und die Grenzen (kein Atom-Import).
keywords: RSS, Atom, OPML, Podcast, Feed, Export, Import, Remote-ID, Pull-Integration, Syndizierung per Feed, RSS 2.0, podcast, feed, export, import, rssimport, rss/feed, RSSImport_, remote id, CacheTime, cleanuprss, syndication by feed, pull integration
type: help
---
# RSS-, Atom- und OPML-Feeds für Integrationen

Diese Seite richtet sich an Integratoren, die einen einfachen, standardisierten Weg brauchen, um Listen von Inhalten
zwischen Systemen zu bewegen. Ein Feed ist eine Pull-Integration ohne Code: Exponential veröffentlicht einen Feed, den
jeder Reader, Aggregator oder nachgelagerte System abrufen kann, und es kann die RSS-Feeds anderer Systeme lesen und
deren Einträge zu Inhaltsobjekten machen. Die Seite der Administration beschreibt
[RSS-Feeds](../../anleitungen/rss-feeds.md).

## Inhalt

1. [Wann ein Feed genügt](#wann-ein-feed-genügt)
2. [Exportieren](#exportieren)
3. [Importieren](#importieren)
4. [Wie importierte Einträge geschlüsselt werden](#wie-importierte-einträge-geschlüsselt-werden)
5. [Caching und Aktualität](#caching-und-aktualität)
6. [Grenzen](#grenzen)
7. [Checkliste](#checkliste)

## Wann ein Feed genügt

| Passt gut | Passt schlecht |
|---|---|
| Eine Partner-Website zeigt Ihre neuesten Nachrichten | Das andere System braucht jedes Feld jedes Objekts |
| Ein Aggregator, ein Newsletter-Werkzeug oder ein Planer für soziale Medien übernimmt neue Artikel | Austausch in beide Richtungen, Bearbeitungen und Löschungen müssen mitreisen |
| Sie übernehmen den RSS-Feed einer Presseagentur oder eines Konzernunternehmens | Die Quelle bietet nur Atom an (kein Atom-Import) |
| Ein Podcast-Verzeichnis liest Ihre Episoden | Zustellung in Echtzeit innerhalb von Sekunden |

## Exportieren

Ein **Export** listet die neuesten Objekte unterhalb einer oder mehrerer Quellen (ein Ort, eine Klasse und wahlweise
die Unterelemente) unter `/rss/feed/<address>` auf, auf der öffentlichen Website des Siteaccess des Exports. Formate
(`kernel/classes/ezrssexport.php`):

| Format | Gespeicherter Wert | Hinweise |
|---|---|---|
| RSS 1.0 | `1.0` | Mit der Feed-Bibliothek der Zeta Components geschrieben |
| RSS 2.0 | `2.0` | Die übliche Wahl; ein Bild lässt sich festlegen |
| Atom 1.0 | `ATOM` | RFC 4287 |
| OPML 2.0 | `OPML` | Eine Liste anderer Feeds; `site.ini [RSSSettings] OPMLMaxOutlines` (Standard 5000) |
| Apple Podcasts | `ITUNES` | RSS 2.0 mit den Podcast-Elementen |

Je Quelle wählen Sie, welche Klassenattribute zu Titel, Beschreibung, Kategorie und Anhang des Eintrags werden. Exporte
legen Sie unter **Setup > RSS** an und bearbeiten sie dort (`/rss/list`, Richtlinie `rss/edit`). Exporte werden in der
Datenbank gespeichert (`ezrss_export` und zugehörige Tabellen), richten Sie sie also in jeder Umgebung ein oder nehmen
Sie sie in die Datenbank auf, die Sie kopieren.

Testen Sie einen Export von der empfangenden Seite aus:

```bash
curl -s https://example.com/rss/feed/<address> | head -20
```

## Importieren

Ein **Import** liest einen RSS-Feed (RSS 0.91, 0.92, 1.0 und 2.0; `kernel/classes/ezrssimport.php`) und legt für jeden
Eintrag, den er noch nicht gesehen hat, ein Objekt unterhalb seines Ziels an. Das Importformular hat vier Teile: der
Feed und seine URL, wohin die Einträge gehen (Ziel und Besitzer), was jeder Eintrag wird (die Klasse und die Zuordnung
der Feed-Felder zu Attributen) und ob er läuft.

Nichts wird gelesen, bevor der Cronjob läuft: `rssimport.php` steht im **globalen** Teil von `settings/cronjob.ini`
(`[CronjobSettings] Scripts[]`), der läuft, wenn `runcronjobs.php` ohne Teilnamen aufgerufen wird:

```bash
php runcronjobs.php -s <siteaccess> --script=rssimport.php   # one run, by hand
php runcronjobs.php -s <siteaccess>                          # the global part, as the crontab runs it
```

Der Import lehnt externe Entitäten ab und schaltet während des Parsens das Netzwerk ab (`LIBXML_NONET`), und er ruft
nur `http`- und `https`-Adressen ab.

## Wie importierte Einträge geschlüsselt werden

Jedes von einem Import angelegte Objekt erhält eine Remote-ID, die mit `RSSImport_<import id>_` beginnt; die
RSS-Übersicht zählt importierte Objekte anhand dieses Präfixes. Das ist dieselbe Idee, die jede Integration verwenden
sollte: ein stabiler Schlüssel mit Präfix je Quelldatensatz
([Idempotente Importe, geschlüsselt nach remote_id](idempotente-importe-ueber-die-remote-id.md)).

Ein Import fügt Objekte hinzu und entfernt oder aktualisiert sie nie. Damit ein lebhafter Feed nicht grenzenlos wächst,
schalten Sie das Aufräumen ein (`content.ini [RSSImportCleanupSettings]`, Befehl `php bin/php/cleanuprss.php`,
Cronjob-Teil `cleanuprss`), das je aktivem Import die neuesten *N* Einträge behält
([Aufräumen von RSS-Importen](../../bc/6.0/cleanuprss.md)).

## Caching und Aktualität

| Einstellung | Wirkung |
|---|---|
| `site.ini [RSSSettings] CacheTime` | Sekunden, die ein geschriebener Feed aus dem Cache ausgeliefert wird; `0` schreibt ihn bei jeder Anfrage neu |
| Cache-ID `rss_cache` | Leeren Sie ihn nach einer Änderung, die sofort sichtbar sein muss: `php bin/php/ezcache.php --clear-id=rss_cache --allow-root-user` |

Sagen Sie den Abnehmern, wie oft sie abrufen sollen; viel häufigeres Abrufen als `CacheTime` bringt nichts.

## Grenzen

- Kein Atom-Import: Der Importer liest nur das Attribut `version` eines RSS-Wurzelelements.
- `GET /api/ezp/<version>/content/node/<id>/listAtom` der REST-API antwortet mit `500`; verwenden Sie stattdessen einen
  RSS-Export.
- Ein Export listet die neuesten Einträge, kein vollständiges Archiv: Er ersetzt keinen Datenexport.
- Importe folgen keinen Aktualisierungen oder Löschungen von Einträgen an der Quelle.

## Checkliste

- [ ] Export: Format, Adresse, Quellen, Anzahl der Einträge und Attributzuordnung mit dem Abnehmer abgestimmt.
- [ ] Export: `CacheTime` und das Abrufintervall in der Schnittstellenspezifikation festgehalten.
- [ ] Import: Ziel, Besitzer (ein Dienstkonto) und Klassenzuordnung gesetzt; die Feed-URL verwendet `https`.
- [ ] Import: Der Cronjob, der `rssimport.php` ausführt, steht in der Crontab und läuft für den richtigen Siteaccess.
- [ ] Import: Aufräumen konfiguriert, wenn der Feed lebhaft ist.

## Verwandte Seiten

- [RSS-Feeds](../../anleitungen/rss-feeds.md)
- [RSS: Apple-Podcasts-Feeds, eine seitenweise Feed-Liste und sicherere Exporte](../../funktionen/6.0/rss-podcast-und-feed-liste.md)
- [OPML-Exporte](../../bc/6.0/opml.md)
- [Syndizierung zwischen Installationen](syndizierung-zwischen-installationen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
