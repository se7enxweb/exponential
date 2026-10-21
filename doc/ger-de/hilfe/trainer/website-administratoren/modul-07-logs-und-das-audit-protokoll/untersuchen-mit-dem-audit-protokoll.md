---
title: "Lektion 7.5: Mit dem Audit-Protokoll ermitteln"
audience: trainer, admin
category: training
order: 75
summary: Die Teilnehmenden beantworten in der Audit-Konsole die Fragen „Wer hat das entfernt?“ und „Wer hat diese Einstellung geändert?“, lesen die Alarmregeln, exportieren eine gefilterte Menge für einen Prüfer und geben einer Prüferrolle nur Lesezugriff auf das Audit. 35 Minuten.
keywords: website-administration, audit-protokoll, ermittlung, audit-konsole, alarme, export, prüferrolle, audit/read, exp:audit search, wer hat entfernt, content.node.remove, site administration, audit trail, investigation, audit console, alerts, auditor role, who removed
type: help
---
# Lektion 7.5: Mit dem Audit-Protokoll ermitteln

Lektion 7.4 hat gezeigt, was das Audit-Protokoll aufzeichnet. Diese Lektion verwendet es so, wie es ein Administrator
nach einem Vorfall tut: Etwas ist verschwunden, eine Einstellung hat sich geändert, oder ein Alarm wurde ausgelöst.
Die Teilnehmenden verfolgen ein Ereignis bis zu seiner Ursache, lesen die Alarmregeln, geben einem Prüfer einen
gefilterten Export und legen eine Prüferrolle an, die das Audit lesen darf und sonst nichts.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 7: Logs und das Audit-Protokoll](README.md) |
| Dauer | 35 Minuten (10 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Sicherheitsbeauftragte |
| Voraussetzungen | [Lektion 7.4](das-audit-protokoll.md); Rollen und Richtlinien aus Modul 3 dieses Kurses; ein Administratorkonto |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- in der Audit-Konsole herausfinden, wer einen Inhalt entfernt hat und wann;
- das Audit in einer Shell nach Ereignisname, Ergebnis und Text durchsuchen;
- die Alarmregeln und die Liste der ausgelösten Alarme lesen;
- die Ereignisse eines Filters für einen Prüfer exportieren;
- einer Prüferrolle die Richtlinie `audit/read` ohne weitere Administrationsrechte geben.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Das Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md), die Abschnitte „Wer nachsehen darf“, „Von
der Kommandozeile“, „Senken, Alarme und Archive“ und „Exporte und die Kanäle von Erweiterungen“;
[Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md).

1. **Ein Vorfall in fünf Schritten** (3 Minuten). Was ist passiert (die sichtbare Wirkung), wann (ungefähr), welcher
   Kanal, die Konsole filtern, das Ereignis öffnen und seinem Elternereignis oder seiner Anfrage folgen. Das Entfernen
   eines Teilbaums ist ein Elternereignis mit einem Kindereignis pro Knoten darunter.
2. **Alarme** (2 Minuten). Regeln überwachen Anmeldungen per Brute Force, die Vergabe einer Administratorrolle,
   außerhalb der Arbeitszeit geschriebene Einstellungen, Massenlöschungen, das Abschalten des Audits und eine
   unterbrochene Kette. Empfänger: die der Regel selbst, sonst die der Alarmeinstellungen, sonst die `AdminEmail` der
   Website.
3. **Exporte** (2 Minuten). **Audit-Ereignisse exportieren** bietet **Jetzt herunterladen** (sofort gestreamt) und
   **Im Hintergrund exportieren** (ein Inhaltsauftrag; die Datei wird auf der Exportseite dem Benutzer angeboten, der
   sie angefordert hat).
4. **Die Prüferrolle** (3 Minuten). `audit/read`, optional nach **Kanal** eingeschränkt. Ein Benutzer ohne diese
   Richtlinie sieht keinen Reiter, und eine eingetippte Adresse wird verweigert und aufgezeichnet.

### Häufige Fragen

**Kann der Export personenbezogene Daten enthalten?** Ja: Logins und gekürzte Adressen. Behandeln Sie ihn wie alle
personenbezogenen Daten und löschen Sie ihn, wenn die Anfrage, die er beantwortet, erledigt ist.

**Kann ein Prüfer etwas ändern?** Mit `audit/read` allein nicht. `audit/manage` fügt Archive, Einstellungen und
**Jetzt prüfen** hinzu; geben Sie sie keinem Prüfer, der nur lesen soll.

**Werden Änderungen an Orten aufgezeichnet?** Das Entfernen eines Ortes wird standardmäßig aufgezeichnet. Das
Hinzufügen eines Ortes und das Ändern des Hauptortes werden aufgezeichnet, sobald sie in
`settings/override/audit.ini.append.php` eingeschaltet sind.

### Stolperfallen

- **Alarm-E-Mails im Kurs.** Alarme können E-Mails an `AdminEmail` versenden. Lassen Sie die Schulungsinstallation auf
  dem Datei-Transport (Modul 6) und tragen Sie nie eine echte Adresse als Empfänger ein.
- **In der falschen Zeitzone suchen.** Auf der Kommandozeile sind `--from` und `--to` ohne Versatz UTC; in der Konsole
  gilt die Zeit der Website.
- **Dem Prüfer die Rolle Administrator geben.** Sie enthält jede Richtlinie. Bauen Sie eine eigene Rolle.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation. Jedes Testkonto verwendet eine Adresse unter `example.invalid`.

1. Legen Sie einen Ordner `Audit test` mit einem Artikel darin an, veröffentlichen Sie beide und verschieben Sie den
   Ordner dann in den Papierkorb.
2. Öffnen Sie die **Audit-Konsole**, setzen Sie **Kanal** auf `content` und klicken Sie auf **Filtern**. Suchen Sie
   das Entfernen des Ordners und öffnen Sie das Ereignis. Lesen Sie, wer es getan hat, wann und welcher Knoten
   betroffen war.
3. Suchen Sie in der Shell nach Inhaltsereignissen:

   ```bash
   php bin/php/console exp:audit search --name='content.*' --limit=10
   ```

4. Suchen Sie nach verweigerten Aktionen: `php bin/php/console exp:audit search --name='access.*' --result=refused --limit=5`.
5. Öffnen Sie das **Audit-Dashboard**, dann **Alarme**. Lesen Sie die **Regeln** und, falls vorhanden,
   **Ausgelöste Alarme**.
6. Listen Sie die Regeln in der Shell auf: `php bin/php/console exp:audit alerts list`.
7. Filtern Sie in der **Audit-Konsole** nach **Kanal** `content` und dem heutigen Datum und öffnen Sie dann
   **Export**. Wählen Sie ein **Format** und verwenden Sie **Jetzt herunterladen**. Öffnen Sie die Datei und prüfen
   Sie, dass sie nur die gefilterten Ereignisse enthält.
8. Öffnen Sie **Benutzer > Rollen und Richtlinien** und legen Sie eine Rolle `Training auditor` mit einer Richtlinie
   an: Modul `audit`, Funktion `read`. Weisen Sie sie einer Test-Benutzergruppe zu.
9. Melden Sie sich in einem privaten Fenster als Mitglied dieser Gruppe an. Öffnen Sie die Adresse des
   Audit-Dashboards (`/audit/dashboard` im Siteaccess der Administration) und dann **Setup**.
10. Filtern Sie als Administrator die Konsole nach dem Ereignisnamen `access.permission.refused` und suchen Sie den
    verweigerten Versuch des Testbenutzers, falls einer aufgezeichnet wurde.

## Erwartetes Ergebnis

- Schritt 2 findet ein Ereignis zum Entfernen von Inhalten mit dem Schulungs-Login, der Zeit und dem Knoten.
- Schritt 7 liefert eine Datei im gewählten Format, die nur die gefilterten Ereignisse enthält.
- Schritt 9: Der Prüfer sieht die Audit-Seiten, wird aber überall dort abgewiesen, wo keine andere Richtlinie den
  Zugriff erlaubt.
- Schritt 10 listet die Verweigerungen des Testbenutzers.
- Der Trainer prüft die Rolle einer Teilnehmerin oder eines Teilnehmers: genau eine Richtlinie, `audit/read`.

![Die Audit-Einstellungen: Index, Schlüssel, Senken, Archivformate und Quellen](../../../../../eng-us/help/images/trainers/sa-audit-settings.webp)

## Quiz

1. Ein Artikel ist gestern verschwunden. Welchen Kanal und welche Filter verwenden Sie zuerst?
2. Welche Richtlinie sollte ein externer Prüfer bekommen?
   A. `audit/manage` B. `audit/read` C. `setup/logs` D. Die Rolle Administrator
3. Wer bekommt Alarm-E-Mails, wenn kein Empfänger eingestellt ist?
4. Was ist der Unterschied zwischen **Jetzt herunterladen** und **Im Hintergrund exportieren**?
5. Ein Benutzer ohne `audit/read` tippt die Adresse des Audits ein. Was passiert?

<details>
<summary>Antworten</summary>

1. Den Kanal `content`, mit **Von** und **Bis** um den gestrigen Tag, und den Ereignisnamen eines Entfernens oder eine
   Textsuche nach dem Namen des Artikels.
2. **B.** Optional nach Kanal eingeschränkt ([Das Audit-Protokoll, Wer nachsehen darf](../../../../../eng-us/features/6.0/audit-trail.md#who-may-look)).
3. Die `AdminEmail` der Website.
4. **Jetzt herunterladen** streamt die Datei sofort; **Im Hintergrund exportieren** führt einen Inhaltsauftrag aus und
   bietet die Datei später auf der Exportseite an, nur dem Benutzer, der sie angefordert hat.
5. Die Anfrage wird verweigert und als `access.permission.refused` aufgezeichnet.

</details>

## Weiterführende Literatur

- [Das Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md): Senken, Alarme, Archive und Exporte.
- [Der Leitfaden zum Audit](../../../../bc/6.0/audit.md): der Ereigniskatalog, um den richtigen Ereignisnamen zu
  finden.
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md): die Prüferrolle bauen.
- [Aufbewahrung von Logs und Daten für Hoster](../../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md): wie lange
  Datensätze aufzubewahren sind.

## Verwandte Seiten

- [Modul 7: Logs und das Audit-Protokoll](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
