---
title: "Thema: der Audit-Trail"
audience: press
category: press-media
order: 61
summary: Ein Themenbeitrag über den manipulationssicheren Audit-Trail von Exponential - wer was getan hat, eine Hash-Kette, die beweist, dass niemand die Aufzeichnung verändert hat, Alarme, Archive und Regeln zur Privatsphäre - mit druckbaren Fakten, Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, Audit-Trail, Audit-Protokoll, manipulationssicher, Hash-Kette, Rechenschaft, Alarme, Privatsphäre, exp:audit, Presse, story, audit trail, audit log, tamper-evident, hash chain, accountability, alerts, privacy, press
type: help
---
# Thema: der Audit-Trail

Diese Seite ist für Journalisten, die über Rechenschaft, Compliance-Arbeit und Sicherheitsbetrieb berichten. Exponential
zeichnet auf, wer was getan hat, in einem Protokoll, das beweisen kann, dass es nachträglich nicht bearbeitet wurde.
Diese Seite liefert das Thema, die Fakten mit Quellen und die Formulierungen, die Sie vermeiden sollten.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Den Lesern erklären](#den-lesern-erklären)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
7. [Wen Sie fragen](#wen-sie-fragen)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

„Exponential 6.0.15 zeichnet sicherheitsrelevante Ereignisse in einem manipulationssicheren Protokoll auf: Anmeldungen
und Ablehnungen, Inhaltsänderungen, geschriebene Einstellungen, geleerte Caches, Deployments, Bestellungen und
Änderungen der Mehrwertsteuer. Das Protokoll ist standardmäßig eingeschaltet, braucht keine Einrichtung und lässt sich
in der Administration oder auf der Kommandozeile lesen“ ([Der Audit-Trail](../../../eng-us/features/6.0/audit-trail.md)). „Jeder
Eintrag trägt den Hash des Eintrags davor (eine Hash-Kette)“, und ein Befehl `verify` sagt, ob etwas bearbeitet oder
herausgeschnitten wurde. Der Trail geht auch sorgsam mit den Menschen um, die er aufzeichnet: Adressen werden gekürzt,
und Passwörter werden nie aufgeschrieben. (6.0.15 ist in Entwicklung und nicht erschienen; die Funktion wurde am
2. Oktober 2026 hinzugefügt.)

## Was neu ist, und seit wann

| Was | Wann | Quelle |
|---|---|---|
| Der Audit-Trail: hash-verkettet, signiert, durchsuchbar, mit dem Befehl `exp:audit`, dem Reiter **Audit**, Ausgabezielen, Alarmregeln und Archiven; standardmäßig eingeschaltet | 2. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#1-and-2-october-2026) |
| Quellen von Erweiterungen in der Audit-Konsole, gestreamte und im Hintergrund laufende Exporte | 12. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#12-october-2026-the-audit-consoles-extension-sources-a-streamed-and-background-export-repeated-records) |
| Eine Zeitleiste für die Audit-Ereignisse von admin4 | 14. Oktober 2026 | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#14-october-2026-a-timeline-for-admin4s-audit-events) |
| Exponential Intelligence protokolliert seine Anfragen als Audit-Ereignisse | 12. Oktober 2026 | [expai, Für Administratoren](../../../eng-us/features/6.0/extensions/expai.md#for-administrators) |

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Er ist standardmäßig eingeschaltet und braucht keine Einrichtung. | [Der Audit-Trail](../../../eng-us/features/6.0/audit-trail.md) |
| Jeder Eintrag trägt den Hash des vorherigen; Tagesdateien werden abgeschlossen, archiviert und in signierten Manifesten aufgeführt. | [Der Audit-Trail, Wozu Sie ihn brauchen](../../../eng-us/features/6.0/audit-trail.md#why-you-want-it) |
| Alarmregeln achten auf Brute-Force-Anmeldungen, die Vergabe einer Administratorrolle, außerhalb der Arbeitszeit geschriebene Einstellungen, Massenlöschungen, das Abschalten des Audits und eine unterbrochene Kette. | ebenda |
| Adressen werden gekürzt, Sitzungen gehasht, User-Agents verkürzt, Werte aus Abfragen entfernt. | ebenda |
| Passwörter, Tokens, Reparaturschlüssel und Geheimnisse werden nie aufgezeichnet. | ebenda |
| Das Entfernen eines Teilbaums ist ein Elternereignis mit einem Kindereignis für jeden Knoten darunter. | ebenda |
| Zu den Ausgabezielen gehören syslog, journald, ein signierter Webhook und Mail; Archive in fünf Formaten. | [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#1-and-2-october-2026) |
| Anfragen an Exponential Intelligence sind Audit-Ereignisse, aber nie ein Prompt, eine Antwort oder ein Schlüssel. | [README, Safety](https://github.com/se7enxweb/exponential/blob/main/README.md#safety) |

## Was Sie nicht behaupten sollten

- „Der Audit-Trail macht eine Website konform“ mit irgendeiner Vorschrift. Er ist ein Nachweis, den eine Organisation
  verwenden kann; die Konformität hängt von der Organisation ab.
- „Niemand kann das Protokoll ändern.“ Die Quelle sagt, dass Änderungen **erkannt** werden können (`verify` endet mit
  1, wenn eine Kette unterbrochen ist).
- „Er zeichnet alles auf, was Benutzer tun.“ Er zeichnet die Ereignisse auf, die in seinem Katalog aufgeführt sind;
  lesen Sie den [Leitfaden zum Audit](../../../eng-us/bc/6.0/audit.md), bevor Sie mehr sagen.

## Den Lesern erklären

```text
Every entry in the log contains a fingerprint of the entry before it. Change or delete one old entry and the
fingerprints no longer match, so a check finds the gap. The log does not stop someone with access to the server
from editing it - it makes the edit visible.
```

Auf Deutsch: Jeder Eintrag im Protokoll enthält einen Fingerabdruck des Eintrags davor. Wer einen alten Eintrag ändert
oder löscht, bringt die Fingerabdrücke durcheinander, und eine Prüfung findet die Lücke. Das Protokoll hält niemanden
mit Zugang zum Server davon ab, es zu bearbeiten – es macht die Bearbeitung sichtbar.

## Blickwinkel und Ideen für Überschriften

```text
"Who removed this page? An open source CMS that can answer - and prove it"
"A log with fingerprints: Exponential's tamper-evident audit trail"
"Recording the actions, not the people: privacy rules inside an audit log"
```

Auf Deutsch zum Beispiel: „Wer hat diese Seite entfernt? Ein Open-Source-CMS, das antworten – und es beweisen – kann“,
„Ein Protokoll mit Fingerabdrücken: der manipulationssichere Audit-Trail von Exponential“, „Die Handlungen
aufzeichnen, nicht die Menschen: Regeln zur Privatsphäre in einem Audit-Protokoll“.

## Wen Sie fragen

| Frage | Fragen Sie | Wo |
|---|---|---|
| Entwurf und Anwendungsfälle | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Wie Administratoren ihn nutzen | die Community | <https://share.exponential.earth/forums> |

## Verwandte Seiten

- [Thema: Sicherheit](thema-sicherheit.md)
- [Der Audit-Trail (Funktionsseite)](../../../eng-us/features/6.0/audit-trail.md)
- [Thema: ein Assistent, der nur Entwürfe schreibt](thema-ein-assistent-der-nur-entwuerfe-schreibt.md)
- [Funktionsthemen: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
