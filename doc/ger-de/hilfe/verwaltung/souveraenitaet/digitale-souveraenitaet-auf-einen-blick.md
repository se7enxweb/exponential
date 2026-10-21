---
title: Digitale Souveränität auf einen Blick
audience: government, project-manager, admin
category: public-sector
order: 20
summary: Was digitale Souveränität für eine öffentliche Stelle bedeutet, die ein Content-Management-System auswählt, und wie Exponential bei jedem Punkt abschneidet (Lizenz, Quellcode, Hosting, Datenstandort, externe Dienste, Formate, Datenbanken, Kompetenzen), mit Links zu den Nachweisen.
keywords: digitale Souveränität, Selbstbetrieb, Open Source, GPL, Anbieterabhängigkeit, Datenstandort, eigenes Rechenzentrum, externe Dienste, Kontrolle, Unabhängigkeit, öffentlicher Sektor, digital sovereignty, self-hosting, vendor lock-in, data location, on-premise, public sector
type: help
---
# Digitale Souveränität auf einen Blick

Diese Seite richtet sich an Führungskräfte, Architekten und Administratoren einer öffentlichen Stelle, die zeigen
müssen, dass ihre Webplattform unter ihrer Kontrolle bleibt: dass sie sie betreiben können, wo sie wollen, sie ändern,
sich von ihr lösen und ihre Daten behalten können. Jede Zeile unten nennt einen Punkt und was Exponential dazu
mitbringt, mit der Seite, die es belegt.

## Inhalt

1. [Die Punkte, auf die es ankommt](#die-punkte-auf-die-es-ankommt)
2. [Wo die Arbeit bei Ihnen bleibt](#wo-die-arbeit-bei-ihnen-bleibt)
3. [Seiten dieses Bereichs nach Frage](#seiten-dieses-bereichs-nach-frage)

## Die Punkte, auf die es ankommt

| Punkt | Was Exponential mitbringt | Nachweis |
|---|---|---|
| Lizenz | Freie Software, GNU GPL Version 2 oder jede spätere Version; keine Lizenzgebühren | [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md) |
| Quellcode | Öffentlich auf GitHub, installiert über Composer-Pakete | [Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md) |
| Hosting | Läuft auf Ihren eigenen Servern, in einer Verwaltungscloud oder bei jedem Hoster, der die Anforderungen erfüllt | [Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md) |
| Erforderliche externe Dienste | Für den Kern keine; die Administration lädt keine Ressourcen von Drittanbietern | [Datenschutzstandards](../../../../eng-us/standards/privacy.md) |
| Optionale externe Dienste | Ausgeschaltet, bis sie aktiviert werden: Webanalyse, CAPTCHA, Karten, Social Login, KI-Anbieter | [Datenschutzstandards](../../../../eng-us/standards/privacy.md#third-party-services-in-shipped-extensions) |
| Datenbanken | Sechs unterstützte, in einer offiziellen Reihenfolge, von SQLite bis MySQL | [Datenbankunterstützung](../../datenbankunterstuetzung.md) |
| Datenformate | Inhaltsexporte (CSV, JSON, XML), Pakete (`.ezpkg`), REST-API | [xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md), [Pakete](../../../anleitungen/pakete.md) |
| Dokumentation | Freie Dokumentation unter der GNU FDL 1.3 oder später | [Die Lizenz dieser Dokumentation](../../../../eng-us/README-LICENSE.md) |
| Wer dahintersteht | Eine gemeinnützige Stiftung und ein Unternehmen, beide namentlich genannt, mit belegten Aussagen | [Wer ist 7x?](../../wer-ist-7x.md) |
| Support | Community, Partner oder eigene Kompetenzen; kein einzelner Pflichtanbieter | [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) |

## Wo die Arbeit bei Ihnen bleibt

Souveränität ist nicht allein eine Produkteigenschaft. Diese Punkte bleiben bei der öffentlichen Stelle:

- das Hosting auswählen und vertraglich regeln, und wissen, wo die Daten und die Datensicherungen liegen;
- entscheiden, welche optionalen Erweiterungen aktiviert werden, und die beteiligten Drittanbieter prüfen;
- die Installation aktuell halten oder jemanden damit beauftragen;
- genug Wissen im Haus behalten, um Dienstleister zu steuern und zu wechseln;
- ein Ausstiegsplan, der getestet wurde.

## Seiten dieses Bereichs nach Frage

| Frage | Seite |
|---|---|
| Können wir es selbst betreiben? | „Selbst betreiben im eigenen Haus“, „Verwaltungscloud und Hostingmodelle“ |
| Meldet es sich beim Hersteller oder braucht es fremde Dienste? | „Keine Pflicht zu externen Diensten“, „Zu prüfende Dienste von Drittanbietern“ |
| Was erlaubt uns die Lizenz? | „Die Open-Source-Lizenz (GPL)“, „Was die GPL für eine öffentliche Stelle bedeutet“ |
| Wie beschaffen und betreuen wir es? | „Open-Source-Beschaffung“, „Einen Supportanbieter wählen“ |
| Können wir wieder aussteigen? | „Anbieterabhängigkeit vermeiden“, „Inhalte und Pakete exportieren“, „Ausstiegsplanung“ |
| Wo sind unsere Daten? | „Datenstandort“, „Wahl der Datenbank“ |
| Wer steht dahinter? | „Die Exponential Foundation und 7x“ |
| Können wir etwas zurückgeben? | „Beiträge als öffentliche Stelle“ |

## Verwandte Seiten

- [Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md)
- [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
