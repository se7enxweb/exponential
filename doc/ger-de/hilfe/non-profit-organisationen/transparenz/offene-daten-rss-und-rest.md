---
title: Offene Daten, RSS und REST
audience: non-profit, government, website-creator, app-creator
category: non-profits
order: 60
summary: Wie eine Non-Profit-Organisation ihre Veröffentlichungen und Register aus Exponential maschinenlesbar teilt - RSS-2.0- und Atom-Feeds mit Neuigkeiten und Veröffentlichungen, Exporte von als Register geführten Inhalten in CSV, JSON und XML, die REST-API (ezprestapi, v1 und v2) mit standardmäßiger Authentifizierung und was vor einer Öffnung für anonyme Leser zu entscheiden ist, sowie die Lizenzierung dessen, was Sie teilen.
keywords: offene Daten, RSS, Atom, Feeds, Export, CSV, JSON, XML, xrowextract, REST-API, ezprestapi, OAuth2, API-Schlüssel, RequireAuthentication, anonym, Lizenz, Weiterverwendung, Geldgeber, Netzwerke, Forschende, open data, feeds, REST API, API keys, anonymous, license, re-use, funders, networks, researchers
type: help
---
# Offene Daten, RSS und REST

Diese Seite richtet sich an die Website-Ersteller und die Verantwortlichen für Kommunikation oder Daten. Geldgeber,
Dachverbände, Partnernetzwerke und Forschende möchten Ihre Veröffentlichungen, Veranstaltungen oder Förderregister
manchmal in einer Form, die ein Computer lesen kann. Exponential bietet dafür drei Kanäle: Feeds, Exporte und eine API.

## Inhalt

1. [Welcher Kanal für welchen Bedarf](#welcher-kanal-für-welchen-bedarf)
2. [RSS- und Atom-Feeds](#rss--und-atom-feeds)
3. [Exporte](#exporte)
4. [Die REST-API](#die-rest-api)
5. [Lizenzierung dessen, was Sie teilen](#lizenzierung-dessen-was-sie-teilen)
6. [Datenschutz zuerst](#datenschutz-zuerst)
7. [Checkliste](#checkliste)

## Welcher Kanal für welchen Bedarf

| Bedarf | Kanal | Aufwand |
|---|---|---|
| Unterstützer und Netzwerke verfolgen Ihre Neuigkeiten und Veröffentlichungen | RSS- oder Atom-Feed | Gering |
| Ein Geldgeber möchte einmal im Jahr eine Liste Ihrer Förderungen oder Projekte | Export nach CSV | Gering |
| Eine Partner-Website zeigt Ihre Veranstaltungen automatisch an | Feed oder die REST-API | Mittel |
| Eine Forscherin möchte regelmäßig strukturierte Daten | Export oder die REST-API | Mittel |

## RSS- und Atom-Feeds

RSS-Exporte veröffentlichen Feeds Ihrer Inhalte als RSS 2.0, Atom, OPML oder Podcast; verlinken Sie sie im
Seitenlayout, damit Browser und Feedreader sie finden ([RSS-Feeds für Ihre Website](../../ersteller/website-ersteller/rss-feeds-fuer-ihre-website.md),
[Leitfaden zu RSS-Feeds](../../../anleitungen/rss-feeds.md)).

Gute Feeds für eine Non-Profit-Organisation:

| Feed | Quellordner |
|---|---|
| Neuigkeiten | Der Ordner mit den Neuigkeiten |
| Veröffentlichungen | Der Ordner für Rechenschaft (neue Jahresveröffentlichungen, Richtlinien) |
| Veranstaltungen | Der Veranstaltungskalender ([Veranstaltungen und Kalender](../kommunikation/veranstaltungen-und-kalender-fuer-non-profit-organisationen.md)) |
| Podcast | Ein Ordner mit Audiodateien ([RSS- und Podcast-Feeds](../../kunden/wachstum/rss-und-podcast-feeds.md)) |

Der Feed für neue Veröffentlichungen im Detail: [Feeds für neue und geänderte Datensätze](../../verwaltung/offene-daten/feeds-fuer-neue-und-geaenderte-datensaetze.md).

## Exporte

| Export | Was | Quelle |
|---|---|---|
| Reiter „Export“ von `xrowextract` | Inhaltsobjekte einer Klasse als CSV und in anderen Formaten | [Inhalte als CSV, JSON, XML exportieren](../../verwaltung/offene-daten/inhalte-als-csv-json-xml-exportieren.md) |
| `exp:ezcsvexport` | Der CSV-Exportbefehl des Kernels | dieselbe Seite |
| CSV der Gesammelten Informationen | Formulareinsendungen (personenbezogene Daten; nie veröffentlichen) | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |

Ein als Inhalt geführtes Register, zum Beispiel „Von uns vergebene Förderungen“, das einmal im Jahr exportiert und als
Datei veröffentlicht wird, ist oft alles, was ein Geldgeber braucht ([Finanzdokumente](finanzdokumente.md#drei-wege-der-veröffentlichung)).

## Die REST-API

Die Erweiterung `ezprestapi` stellt REST v1 und v2 bereit ([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)):

- Lesen über v1 und v2, Schreiben nur über v2; die Antworten sind JSON.
- Aufrufe wie `GET /content/node/<id>` und `GET /content/node/<id>/list/offset/<n>/limit/<n>`.
- **Authentifizierung ist standardmäßig erforderlich** (`rest.ini [Authentication] RequireAuthentication=enabled`):
  ein OAuth2-Token oder ein persönlicher API-Schlüssel. Jeder Lesezugriff prüft die Richtlinie `content/read` des
  Benutzers.

Die Öffnung für anonyme Leser ist eine Sicherheitsentscheidung. Prüfen Sie vorher, dass Anonymous nur öffentliche
Bereiche lesen kann, dass Schreibrouten geschlossen bleiben und dass eine Ratenbegrenzung vorgeschaltet ist, und
halten Sie fest, wer die Öffnung genehmigt hat
([Die REST-API als Datenschnittstelle](../../verwaltung/offene-daten/die-rest-api-als-datenschnittstelle.md#für-anonyme-leser-öffnen)).

Für die meisten Non-Profit-Organisationen ist es besser, wenn ein Partner für seine Integration einen API-Schlüssel
erhält; dann wird nichts für alle geöffnet ([API-Schlüssel](../../../anleitungen/api-schluessel.md),
[REST-API für Betreiber](../../kunden/wachstum/rest-api-fuer-betreiber.md)).

## Lizenzierung dessen, was Sie teilen

Geben Sie an, unter welchen Bedingungen andere Ihre Veröffentlichungen weiterverwenden dürfen: auf der Seite, in der
Feed-Beschreibung und in der Datei. Siehe [Datensätze lizenzieren](../../verwaltung/offene-daten/datensaetze-lizenzieren.md).
Wählen Sie eine Lizenz gemeinsam mit Ihrem Vorstand; diese Hilfe empfiehlt keine bestimmte.

## Datenschutz zuerst

- Legen Sie nie personenbezogene Daten in einen Feed, einen Export oder eine öffentliche API-Antwort: keine Namen von
  Spendern, keine Kontaktdaten von Ehrenamtlichen, keine Begünstigten
  ([Datenschutzprüfungen vor der Veröffentlichung von Daten](../../verwaltung/offene-daten/datenschutzpruefung-vor-dem-veroeffentlichen.md)).
- Prüfen Sie nach der Einrichtung, was ein Feed enthält: Öffnen Sie ihn so, wie ein Besucher es täte.
- Das Audit-Protokoll verzeichnet CSV-Exporte mit ihrer Zeilenzahl ([Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md)).

## Checkliste

- [ ] Feeds für Neuigkeiten, Veröffentlichungen und Veranstaltungen, im Seitenlayout verlinkt.
- [ ] Jeder Feed als Besucher geöffnet und auf personenbezogene Daten geprüft.
- [ ] Register exportiert und als Dateien veröffentlicht, wo Geldgeber danach fragen.
- [ ] REST-API weiterhin mit Authentifizierung; bei Bedarf Schlüssel pro Partner ausgegeben.
- [ ] Bedingungen für die Weiterverwendung angegeben.

## Verwandte Seiten

- [Transparenz für Non-Profit-Organisationen](README.md)
- [Offene Daten in Exponential im Überblick](../../verwaltung/offene-daten/offene-daten-in-exponential-auf-einen-blick.md)
- [Entfernte Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
