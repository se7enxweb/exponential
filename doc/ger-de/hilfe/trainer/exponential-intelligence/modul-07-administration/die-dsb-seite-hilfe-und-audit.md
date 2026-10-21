---
title: "Lektion 7.3: EI: Summary, die Hilfeseiten und das Audit-Protokoll"
audience: trainer, admin
category: training
order: 73
summary: Administratoren erstellen die druckbare Seite EI: Summary für einen Datenschutzbeauftragten, finden jedes Thema in EI: Help und wissen, was das Audit-Protokoll über den Assistenten festhält und was nie. 30 Minuten.
keywords: exponential intelligence, administration, ei summary, datenschutzbeauftragter, drucken, pdf, ei help, dokumentation, audit-protokoll, kanal ai, aufzeichnungen, datenschutz, data protection officer, print, documentation, audit trail, channel ai, records, privacy
type: help
---
# Lektion 7.3: EI: Summary, die Hilfeseiten und das Audit-Protokoll

Früher oder später fragt jemand: „Was schickt die KI-Funktion wohin, und wer darf sie nutzen?“ Die Seite **EI:
Summary** beantwortet das auf einer druckbaren Seite. Diese Lektion zeigt sie, die Hilfeseiten, die mit der
Erweiterung kommen, und was das Audit-Protokoll aufbewahrt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Intelligence](../README.md) |
| Modul | [Modul 7: Administration](README.md) |
| Dauer | 30 Minuten (10 Präsentation, 20 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren, Datenschutzbeauftragte |
| Voraussetzungen | [Lektion 7.2](einschalten-einstellungen-und-limits.md); die Richtlinie `expai/summary` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- **EI: Summary** öffnen und ihre Abschnitte nennen;
- sie für einen Datenschutzbeauftragten drucken oder als PDF speichern;
- einem Datenschutzbeauftragten eine Rolle nur mit `expai/summary` geben;
- ein Thema in **EI: Help** finden;
- sagen, was das Audit-Protokoll über den Assistenten festhält und was nie.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: die Seiten zu EI: Summary und zum Audit-Protokoll unter **EI: Help** (`extension/expai/doc/summary.md`,
`extension/expai/doc/audit-trail.md`) und [expai: Für Administratoren](../../../../../eng-us/features/6.0/extensions/expai.md#for-administrators).

1. **EI: Summary** (4 Minuten). Setup > **EI: Summary** (oder **Details** auf dem Dashboard). Nur lesend; jede Angabe
   wird in dem Moment gelesen, in dem die Seite sich öffnet. Abschnitte: **Master switch and surfaces**, **Providers**
   (Host, Region, Aufbewahrung, Datum der Bedingungen, woher der Schlüssel kommt, aber nie der Schlüssel, wer ihn nutzen
   darf, die Nutzung in diesem Monat), **Lanes and their retention**, **Data policy**, **Limits, budget and usage this
   month**, **Tools that reach outside the site**, **What this site keeps**, **Roles with Exponential Intelligence
   policies**. **Print or save as PDF** öffnet den Druckdialog des Browsers.
2. **Eine Rolle für den DSB** (2 Minuten). `expai/summary` braucht keine andere expai-Richtlinie und keinen
   Inhaltszugriff.
3. **EI: Help** (2 Minuten). Die Dokumentation der Erweiterung, aus den installierten Dateien erzeugt, sodass sie zur
   installierten Version passt; jede expai-Seite hat einen Link „?“ zu ihrer Seite.
4. **Audit** (2 Minuten). Jede Anfrage, jeder Entwurf und jede Zustandsänderung eines Entwurfs ist ein Ereignis des
   Kanals `ai` des Audit-Protokolls. Prompts, Antworten und Schlüssel werden dort nie festgehalten.

### Häufige Fragen

**Ist die Seite ein rechtliches Dokument?** Sie listet die Fakten auf, die die Einstellungen und Logs der Website
enthalten. Das Verzeichnis der Verarbeitungstätigkeiten Ihrer Organisation ist ein eigenes Dokument; die Seite kann
darauf verweisen (`[SummarySettings] Note`).

**Kann der DSB Inhalte sehen?** Nicht mit `expai/summary` allein.

**Wo ist die vollständige Dokumentation außerhalb der Administration?** Im Ordner `doc/` der Erweiterung und in ihrem
Repository.

### Stolperfallen

- **Eine veraltete Seite drucken.** Sie spiegelt den Moment wider, in dem sie geöffnet wurde; drucken Sie sie nach
  Änderungen erneut.
- **Leere Daten bei „terms checked“.** Die Seite zeigt sie; beheben Sie das, bevor Sie sie verschicken.
- **Das PDF frei herumschicken.** Es nennt Rollen und Einstellungen; behandeln Sie es als intern.

## Praktische Übung

Verwenden Sie die Schulungsinstallation mit einem Administratorkonto.

1. Öffnen Sie Setup > **EI: Summary**. Lesen Sie jede Abschnittsüberschrift.
2. Prüfen Sie für jeden Anbieter die Region, die Aufbewahrung und das Datum der Bedingungen. Notieren Sie jedes „not
   checked“.
3. Suchen Sie, woher der Schlüssel kommt (die Einstellungsdatei und ihre Ablage). Bestätigen Sie, dass auf der Seite
   kein Schlüssel steht.
4. Lesen Sie **Roles with Exponential Intelligence policies**: Passt das zu den Rollen, die Sie in Lektion 1.3
   entworfen haben?
5. Klicken Sie auf **Print or save as PDF** und speichern Sie ein PDF auf Ihrem Rechner.
6. Öffnen Sie **EI: Help** und suchen Sie die Seiten zur Datenrichtlinie und zu Prompt Injection (die
   Sicherheitsseite).
7. Wenn Ihre Rolle die Audit-Konsole öffnen darf, suchen Sie die Ereignisse des Assistenten (Kanal `ai`) und prüfen
   Sie, dass kein Prompt-Text erscheint.

## Erwartetes Ergebnis

- Die Person hat ein PDF der Seite und eine Liste offener Punkte (zum Beispiel zu setzende Daten der Bedingungen).
- Die Person hat die Seiten zur Datenrichtlinie und zur Sicherheit in **EI: Help** gefunden.
- Der Trainer prüft das, indem er eine Person fragt, welcher Abschnitt „Was schickt die KI-Funktion wohin?“
  beantwortet.

![Die Hilfe von Exponential Intelligence in der Administration](../../../../../eng-us/help/images/trainers/ei-help.webp)

## Quiz

1. Nennen Sie vier Abschnitte von **EI: Summary**.
2. Zeigt **EI: Summary** API-Schlüssel?
3. Welche Richtlinie allein genügt einem Datenschutzbeauftragten, um die Seite zu lesen?
   A. `expai/write` B. `expai/summary` C. `expai/log` D. `expai/settings`
4. Warum passen die Hilfeseiten immer zur installierten Version?
5. Was hält das Audit-Protokoll über den Assistenten nie fest?

<details>
<summary>Antworten</summary>

1. Vier beliebige aus: Master switch and surfaces; Providers; Lanes and their retention; Data policy; Limits, budget
   and usage this month; Tools that reach outside the site; What this site keeps; Roles with Exponential Intelligence
   policies.
2. Nein; sie nennt, woher ein Schlüssel kommt, nie den Schlüssel.
3. **B.**
4. Sie werden aus den installierten Dateien erzeugt.
5. Prompts, Antworten und Schlüssel.

</details>

## Weiterführende Informationen

- [expai: Für Administratoren](../../../../../eng-us/features/6.0/extensions/expai.md#for-administrators)
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md): das Audit-Protokoll.
- Die Seiten zu EI: Summary und zum Audit-Protokoll unter **EI: Help**.

## Verwandte Seiten

- [Modul 7: Administration](README.md)
- [Exponential Intelligence: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
