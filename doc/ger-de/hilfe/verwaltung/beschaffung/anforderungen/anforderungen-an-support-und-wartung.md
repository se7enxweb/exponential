---
title: Anforderungen an Support und Wartung
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 120
summary: Formulierungen für Support und Wartung eines Open-Source-CMS - wer was unterstützt, Schweregrade und Reaktionszeiten als Platzhalter, Sicherheitsupdates, Upgrades, die eigenen Zusagen des vorgelagerten Projekts und wie Sie das Angebot eines Lieferanten damit vergleichen.
keywords: Support, Wartung, SLA, Reaktionszeit, Schweregrad, Sicherheitsupdates, Upgrades, unterstützte Versionen, Partner, 7x, Support der Community, Eskalation, Ausschreibung, Anforderungen, support, maintenance, response time, severity, security updates, supported versions, community support, escalation, tender, requirements
type: help
---
# Anforderungen an Support und Wartung

Diese Seite richtet sich an Beschaffungsteams und Projektleitungen, die den Support für eine Exponential-Website
festlegen. Bei Open-Source-Software gibt es drei Ebenen: den **Lieferanten**, den Sie beauftragen, das **vorgelagerte
Projekt**, das die Software veröffentlicht, und die **Community**. Nur die erste ist an Ihren Vertrag gebunden. Die
Seite gibt Formulierungen für Anforderungen und gibt an, wozu sich das Projekt selbst verpflichtet, sodass Sie sehen,
welche Zusagen ein Bieter allein machen kann und welche von anderen abhängen.

## Inhalt
1. [Die drei Ebenen des Supports](#die-drei-ebenen-des-supports)
2. [Formulierung der Anforderungen](#formulierung-der-anforderungen)
3. [Wozu sich das Projekt verpflichtet](#wozu-sich-das-projekt-verpflichtet)
4. [Bieter vergleichen](#bieter-vergleichen)
5. [Fallstricke](#fallstricke)

## Die drei Ebenen des Supports

| Ebene | Wer | An Ihren Vertrag gebunden? | Quelle |
|---|---|---|---|
| Lieferant | das Unternehmen, das Sie beauftragen: ein Partner des Projekts, ein Hosting-Unternehmen, 7x oder ein anderer Integrator | Ja | Ihr Vertrag |
| Vorgelagertes Projekt | 7x und die Exponential Foundation, die 6.0.x veröffentlichen | Nein, sofern Sie 7x nicht direkt beauftragen | [Support erhalten](../../../projekt/unterstuetzung-erhalten.md), [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Community | Foren und Chats | Nein; „seine Entwickler sind nicht verpflichtet, kostenlosen Support zu leisten“ | [Support erhalten](../../../projekt/unterstuetzung-erhalten.md) |

Partner des Projekts haben vom Projekt eigene veröffentlichte Support-Stufen
([Support-Stufen und Reaktionszeiten](../../../partner/support-stufen-und-reaktionszeiten.md)); ein Bieter, der Partner
ist, kann seine Stufe als Teil seines Eskalationswegs nennen.

## Formulierung der Anforderungen

| Id | Anforderung |
|---|---|
| SUP-01 | Der Lieferant leistet Support für die Software, das gelieferte Design, Erweiterungen und Integrationen, während <Zeiten, Zeitzone>. |
| SUP-02 | Schweregrade sind definiert als <Definitionen>; Zeiten bis zur ersten Reaktion sind <Zeiten>; Ziele für Lösung oder Umgehung sind <Zeiten>. |
| SUP-03 | Veröffentlichte Sicherheitskorrekturen von Exponential werden innerhalb von <Zeit> nach dem Release in die Produktion eingespielt. |
| SUP-04 | Die Website wird während des Vertrags auf einer unterstützten Version von Exponential, PHP und der Datenbank gehalten; Upgrades werden <Zeitraum> im Voraus geplant und angekündigt. |
| SUP-05 | Der Lieferant nennt seinen Eskalationsweg zum vorgelagerten Projekt (Partnerstufe, bezahlter Support, Issue-Tracker) und gibt an, welche Probleme er selbst behebt. |
| SUP-06 | Mängel an eigens entwickelter Arbeit, die unter diesem Vertrag geliefert wurde, werden <Zeitraum> lang kostenlos behoben. |
| SUP-07 | Support-Anfragen und ihre Bearbeitung werden protokolliert und monatlich berichtet. |
| SUP-08 | Der Lieferant gibt Korrekturen von allgemeinem Interesse an das Projekt zurück oder begründet, warum nicht. |

Füllen Sie die Lücken anhand Ihrer eigenen Risikobewertung aus. Diese Hilfe gibt keine Werte vor.

## Wozu sich das Projekt verpflichtet

Aus [Wer ist die Exponential Foundation? Die Unterstützungszusage](../../../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung)
und `SECURITY.md`:

- **Gepflegte Linie**: Exponential 6.0. Sicherheitskorrekturen werden auf `main` vorgenommen und mit dem nächsten
  Release 6.0.x veröffentlicht. 5.x, 4.x, 3.x und älter werden nicht unterstützt.
- **Veröffentlichte Versionen ändern sich nie**: Eine Korrektur kommt als nächste Version, nicht als korrigierte Kopie
  einer alten ([Quellcodeverwaltung und Releases](../../../../standards/versionsverwaltung-und-releases.md)).
- **Vertrauliche Meldungen von Schwachstellen**: eine erste Antwort innerhalb eines Werktags.
- **PHP**: 8.0 bis 8.5 unterstützt; die CI führt die Tests unter 8.1 bis 8.5 aus und prüft, dass 8.0 startet.
- **Kein SLA** wird vom Projekt für den allgemeinen Support veröffentlicht ([SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)).

SUP-03 ist also eine Zusage, die der Lieferant machen kann (Zeit bis zum Einspielen eines Release); eine Zusage zur Zeit
bis zur *Behebung* einer Schwachstelle in der Software hängt vom Projekt ab.

## Bieter vergleichen

| Frage | Eine gute Antwort zeigt |
|---|---|
| Wer in Ihrem Personal kennt Exponential? | Benannte Rollen, abgeschlossene Schulungen ([Kurskatalog](../../../trainer/kurskatalog.md)) |
| Wie erfahren Sie von einem neuen Release? | Verfolgen der Releases des Repositorys und des Changelogs ([Changelog 6.0.15](../../../../../changelogs/6.0/6.0.15.md)) |
| Wie testen Sie ein Update vor der Produktion? | Eine Staging-Kopie und ein schriftliches Verfahren ([Kundenwebsites aktualisieren](../../../hosting/betrieb/kunden-websites-aktualisieren.md)) |
| Was tun Sie, wenn die Korrektur im vorgelagerten Projekt liegt? | Ein Issue im öffentlichen Tracker, ein Pull Request oder der Ticketweg für Partner ([Fehler als Partner melden](../../../partner/fehler-gut-melden.md)) |

## Fallstricke

- Eine Reaktionszeit kaufen, die kürzer ist, als der Lieferant personell abdecken kann.
- Erweiterungen aus dem Umfang lassen: Das meiste eigene Verhalten steckt in ihnen.
- Kein Budget für Upgrades: Planen Sie sie je Jahr (siehe die Seiten zum Lebenszyklus dieses Abschnitts zur
  Beschaffung).

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Ausschreibungsvorlage: Klausel zu Support und Wartung](../ausschreibungsvorlagen/klausel-support-und-wartung.md)
- [Ausschreibungsvorlage: Klausel zu Service-Levels](../ausschreibungsvorlagen/klausel-service-level.md)
- [Support-Stufen](../../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
