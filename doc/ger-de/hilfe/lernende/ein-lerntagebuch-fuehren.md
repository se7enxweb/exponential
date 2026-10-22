---
title: Ein Lerntagebuch führen
audience: student
category: learning
order: 3
summary: Ein einfaches Lerntagebuch zum Lernen von Exponential - was Sie für jede Sitzung festhalten, eine Vorlage zum Kopieren, wie Sie es in Git neben Ihrer Lernwebsite aufbewahren, was niemals hineingehört und wie das Tagebuch später Teil Ihres Portfolios wird.
keywords: lerntagebuch, lernprotokoll, notizen, vorlage, festhalten, reflexion, portfolio, git, fortschritt, study journal, learning log, notes, template, record, reflection, progress, learning diary
type: help
---
# Ein Lerntagebuch führen

Diese Seite ist für Lernende, die ihren eigenen Fortschritt sehen und sich merken möchten, was sie gelernt haben. Ein
Lerntagebuch ist eine einfache Textdatei mit einem Eintrag pro Sitzung. Es kostet fünf Minuten am Tag und zahlt sich
jedes Mal aus, wenn Sie demselben Problem zum zweiten Mal begegnen.

## Inhalt

1. [Warum ein Tagebuch hilft](#warum-ein-tagebuch-hilft)
2. [Was Sie festhalten](#was-sie-festhalten)
3. [Eine Vorlage zum Kopieren](#eine-vorlage-zum-kopieren)
4. [Wo Sie es aufbewahren](#wo-sie-es-aufbewahren)
5. [Was niemals hineingehört](#was-niemals-hineingehört)
6. [Vom Tagebuch zum Portfolio](#vom-tagebuch-zum-portfolio)

## Warum ein Tagebuch hilft

- **Sie lernen aus Unterschieden.** Die Hilfeseiten sagen, was Sie sehen sollten („Erwartet: …“). Wenn Sie
  aufschreiben, was Sie tatsächlich gesehen haben, merken Sie, wenn es abweicht.
- **Sie lösen nicht mehr zweimal dasselbe Problem.** Ein Cache, der geleert werden musste, ein Siteaccess, den Sie zu
  nennen vergessen haben, eine Rolle ohne Richtlinie: Diese Dinge kommen wieder.
- **Sie können bessere Fragen stellen.** Eine Frage mit den Schritten, dem erwarteten und dem tatsächlichen Ergebnis
  bekommt schneller eine Antwort ([Um Hilfe bitten und Lerngruppen](um-hilfe-bitten-und-lerngruppen.md)).
- **Sie können Ihre Arbeit zeigen.** Eine Lehrkraft, ein Trainer oder ein Arbeitgeber kann nachlesen, was Sie getan
  und wie Sie gedacht haben ([Ein Portfolio aufbauen](ein-portfolio-aufbauen.md)).

## Was Sie festhalten

Für jede Sitzung:

| Feld | Beispiel |
|---|---|
| Datum und Dauer | 2026-10-20, 70 Minuten |
| Release | `console (Exponential) 6.0.15stable`, aus `php bin/php/console --version` |
| Ziel | Ein Override für die vollständige Ansicht eines Artikels anlegen |
| Verwendete Seite | [Templates und Design](../../anleitungen/templates-und-design.md), Abschnitt 4 |
| Was ich getan habe | Die Befehle oder Klicks, in der Reihenfolge |
| Erwartet | Was ich laut der Seite sehen sollte |
| Was passiert ist | Was ich gesehen habe, mit der genauen Meldung, falls es eine gab |
| Warum (falls abweichend) | Was ich herausgefunden habe |
| Nächster Schritt | Was ich beim nächsten Mal tue |

## Eine Vorlage zum Kopieren

```text
## <date> (<minutes> min) - <short goal>

Release:   <output of php bin/php/console --version, first line>
Path:      <learning path and step, or study project number>
Page(s):   <titles of the help pages you used>

Did:
  1. <step>
  2. <step>

Expected:  <what the page said>
Happened:  <what you saw; copy error messages exactly>
Learned:   <one or two sentences in your own words>
New words: <terms to look up in the glossary>
Next:      <the next small step>
```

Halten Sie die Einträge kurz. Eine ehrliche Zeile unter „Learned“ ist mehr wert als eine aus der Hilfe kopierte Seite.

## Wo Sie es aufbewahren

- **Eine Markdown-Datei in einem eigenen Git-Repository**, zum Beispiel `journal.md` in einem Ordner neben Ihrer
  Lernwebsite. Committen Sie nach jeder Sitzung; die Historie zeigt dann Ihren Fortschritt Tag für Tag
  ([Git als Lernende nutzen](git-als-lernende-nutzen.md)).
- **Ein Notizbuch aus Papier** funktioniert ebenso, wenn Sie das lieber mögen. Übertragen Sie die wichtigen Befehle
  später in eine Datei.
- Wenn Ihr Kurs oder Ihre Schule Ihnen eine Lernplattform bereitstellt, folgen Sie deren Regeln, wo Arbeiten
  aufbewahrt werden.

Bewahren Sie das Tagebuch nicht in den eigenen Ordnern der Installation auf (etwa `var/` oder `settings/`): Eine
Neuinstallation kann sie entfernen ([Die Lernwebsite zurücksetzen und neu aufbauen](die-lernwebsite-zuruecksetzen-und-neu-aufbauen.md)).

## Was niemals hineingehört

- **Passwörter, Schlüssel und Tokens.** Der Installer gibt das Administratorpasswort einmal aus und schreibt es nach
  `var/log/initial-admin-password` ([Erste Schritte](../../anleitungen/erste-schritte.md#5-erste-anmeldung)). Bewahren Sie es
  in einem Passwortmanager auf, nicht im Tagebuch. Dasselbe gilt für persönliche API-Schlüssel, die nur einmal
  angezeigt werden ([Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md)).
- **Personenbezogene Daten anderer Menschen.** Verwenden Sie für Testkonten erfundene Namen und Adressen unter
  `example.invalid`.
- **Private Serverangaben** eines Arbeitgebers, einer Schule oder eines Kunden: Hostnamen, IP-Adressen, Pfade.

Wenn Sie jemals versehentlich ein Geheimnis committen, behandeln Sie es als öffentlich: Ändern Sie sofort das Passwort
oder widerrufen Sie den Schlüssel.

## Vom Tagebuch zum Portfolio

Am Ende eines Lernpfads oder eines Lernprojekts:

1. Lesen Sie Ihre Einträge für diesen Zeitraum noch einmal.
2. Schreiben Sie eine kurze Zusammenfassung: was Sie gebaut haben, welche Seiten Sie verwendet haben, die zwei
   schwierigsten Probleme und wie Sie sie gelöst haben.
3. Fügen Sie Screenshots Ihrer Lernwebsite hinzu (keine Passwörter auf dem Bildschirm).
4. Verlinken Sie die Zusammenfassung in Ihrem Portfolio ([Ein Portfolio aufbauen](ein-portfolio-aufbauen.md)).

## Verwandte Seiten

- [Mit der Hilfe lernen](mit-der-hilfe-lernen.md)
- [Die eigene Arbeit bewerten](die-eigene-arbeit-bewerten.md)
- [Checklisten für Lernende](checklisten-fuer-lernende.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
