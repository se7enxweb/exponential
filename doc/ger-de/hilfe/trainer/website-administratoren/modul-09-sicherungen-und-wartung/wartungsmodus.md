---
title: "Lektion 9.2: Wartungsmodus"
audience: trainer, admin
category: training
order: 92
summary: Die Teilnehmenden nehmen eine Schulungs-Website unter Setup > Wartung mit einer Nachricht, einer voraussichtlichen Dauer und ihrer eigenen zugelassenen Adresse offline, prüfen, was Besucher bekommen, schalten den Modus aus, tun dasselbe in einer Shell und kommen wieder hinein, wenn sie ausgesperrt sind. 40 Minuten.
keywords: website-administration, wartungsmodus, setup wartung, website offline nehmen, website wieder online bringen, 503, maintenance.php, exp:maintenance, allow-admin, var/maintenance.json, site administration, maintenance mode, take the site offline, bring the site back online
type: help
---
# Lektion 9.2: Wartungsmodus

Vor einem Upgrade, einem Import oder einer Wiederherstellung sollte die öffentliche Website keine Änderungen mehr
annehmen und den Besuchern eine ehrliche Seite „Bald wieder da“ zeigen. Genau das leistet der Wartungsmodus: Jede
Seitenanfrage bekommt eine Wartungsseite mit Status 503, während die Administration erreichbar bleibt, um die Website
wieder online zu bringen. Diese Lektion behandelt die Seite, die Kommandozeile und den Weg zurück hinein, wenn etwas
schiefgeht.

> **Hinweis**
> Der Wartungsmodus nimmt die ganze öffentliche Website offline. Verwenden Sie nur Ihre eigene Schulungsinstallation.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 9: Sicherungen und Wartung](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Support-Mitarbeiter |
| Voraussetzungen | [Lektion 9.1](was-gesichert-werden-muss.md); ein Administratorkonto und eine Shell auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die öffentliche Website unter **Setup > Wartung** mit einer Nachricht, einer Dauer und zugelassenen Adressen offline
  nehmen;
- mit `curl` prüfen, was ein Besucher bekommt (Status 503 und die Wartungsseite);
- die Website wieder online bringen und **Letzte Änderungen** lesen;
- dasselbe in einer Shell tun und erklären, was `--allow-admin` ändert;
- wieder hineinkommen, indem sie `var/maintenance.json` entfernen, wenn nichts anderes funktioniert.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Wartungsmodus: die Website offline nehmen und wieder online bringen](../../../../anleitungen/wartungsmodus.md),
die Abschnitte 1 bis 7 und 15. Die beschriebene Seite ist die des Designs admin4.

1. **Was er tut** (3 Minuten). Jede Seitenanfrage bekommt die Wartungsseite mit `503 Service Unavailable`,
   `Retry-After` und `no-store`. Bilder, Stylesheets und Skripte werden weiterhin ausgeliefert. Die Administration unter
   `/admin` bleibt erreichbar.
2. **Die Seite** (5 Minuten). **Setup**, dann **Wartung**. Zeigen Sie **Wartungsfenster planen**:
   **Nachricht für Besucher (optional)**, **Voraussichtliche Dauer in Minuten (optional)** mit den Schaltflächen
   **15 Min.** bis **4 Std.**, **Meine eigene Adresse ... sieht die Website weiterhin**,
   **Weitere Adressen, die die Website weiterhin sehen (optional)**, das Bestätigungshäkchen und
   **Website offline nehmen**. Zeigen Sie **Vorschau: Was Besucher sehen werden**.
3. **Während der Wartung** (2 Minuten). Ein gelber Hinweisbalken sagt, warum, durch wen, seit wann, bis wann und ob
   **Sie** die Website sehen. Die Wartung endet nie von selbst, wenn die voraussichtliche Zeit verstrichen ist: Erst
   **Website wieder online bringen** beendet sie.
4. **Die Shell** (3 Minuten). `php bin/php/maintenance.php on|off|status` oder `php bin/php/console exp:maintenance`.
   Ohne `--allow-admin` ist auch die Administration offline.
5. **Der Weg zurück hinein** (2 Minuten). Der gesamte Zustand steht in einer Datei, `var/maintenance.json`. Wer sie
   löscht, beendet die Wartung.

### Häufige Fragen

**Halten Cronjobs während der Wartung an?** Nicht von selbst. Stoppen Sie die Crontab für das Wartungsfenster oder
pausieren Sie die Teile (Modul 6) und führen Sie sie danach aus.

**Ich habe den Modus eingeschaltet, sehe die Website aber noch.** Ihre Adresse ist zugelassen, oder ein CDN hält die
Seite noch. Sehen Sie im Hinweisbalken unter **Sie** nach und prüfen Sie mit `curl -sI` von einer anderen Adresse aus.

**Wird jedes Umschalten aufgezeichnet?** Ja: als `system.maintenance.change` im Audit; die Seite listet die letzten
acht auf.

### Stolperfallen

- **Eine Administration auf einem eigenen Host.** Liegt die Administration nicht unter `/admin`, kann das Einschalten
  über die Seite sie aussperren. Kennen Sie den Shell-Befehl, bevor Sie beginnen.
- **Alte Seiten nach dem Wartungsfenster.** Ein Cache vor PHP hat Seiten von vorher behalten. Leeren Sie den
  Antwort-Cache von Velocity und leeren Sie das CDN.
- **Ein während der Wartung gesichertes `var/` wiederherstellen.** Die wiederhergestellte Website kommt im
  Wartungsmodus zurück; schalten Sie ihn aus.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, ausgeliefert zum Beispiel unter `http://127.0.0.1:8087`; ersetzen
Sie die Adresse durch Ihre eigene.

1. Öffnen Sie als Administrator **Setup > Wartung**. Lesen Sie **Die Website ist online** und **So funktioniert es**.
2. Geben Sie eine **Nachricht für Besucher (optional)** ein: `Training: back in 15 minutes`. Klicken Sie auf
   **15 Min.**
3. Lassen Sie **Meine eigene Adresse ... sieht die Website weiterhin** ohne Häkchen. Lesen Sie
   **Vorschau: Was Besucher sehen werden**.
4. Setzen Sie das Bestätigungshäkchen und klicken Sie auf **Website offline nehmen**. Lesen Sie den Hinweisbalken.
5. Prüfen Sie in einem Terminal, was ein Besucher bekommt:

   ```bash
   curl -sI http://127.0.0.1:8087/ | head -5
   ```

6. Öffnen Sie in einem anderen Tab das Dashboard der Administration: Es funktioniert weiterhin.
7. Klicken Sie auf **Website wieder online bringen**. Führen Sie den `curl`-Befehl erneut aus.
8. Jetzt in der Shell, wobei die Administration erreichbar bleibt:

   ```bash
   php bin/php/maintenance.php on --message="Training from the shell" --until=30m --allow-admin
   php bin/php/maintenance.php status
   ```

9. Laden Sie **Setup > Wartung** neu. Der Hinweisbalken zeigt das Umschalten aus der Shell; **Letzte Änderungen**
   listet beide Wartungsfenster auf.
10. Üben Sie den Weg zurück hinein: `ls -l var/maintenance.json`, dann beenden Sie die Wartung mit
    `php bin/php/maintenance.php off`. Ihr Trainer zeigt den letzten Ausweg, das Löschen der Datei, am Projektor.

## Erwartetes Ergebnis

- Schritt 5 zeigt `HTTP/1.1 503 Service Unavailable` (oder HTTP/2 503) und einen `Retry-After`-Header.
- Schritt 6: Die Administration antwortet normal.
- Nach Schritt 7 zeigt `curl` wieder `200`.
- In Schritt 8 meldet `status` die Wartung als eingeschaltet, mit der Nachricht und dem voraussichtlichen Ende.
- Nach Schritt 10 existiert `var/maintenance.json` nicht mehr, und die Website antwortet mit `200`.
- Der Trainer prüft **Letzte Änderungen** auf der Seite einer Person: zwei Wartungsfenster, jedes ein- und
  ausgeschaltet.

![Setup, Wartung: ein Wartungsfenster planen und starten](../../../../../eng-us/help/images/trainers/sa-maintenance.webp)

## Quiz

1. Welchen HTTP-Status bekommt ein Besucher während der Wartung?
   A. 200 B. 404 C. 500 D. 503
2. Richtig oder falsch: Die Wartung endet von selbst, wenn die voraussichtliche Dauer verstrichen ist.
3. Was ändert `--allow-admin` auf der Kommandozeile?
4. Sie sind aus der Administration ausgesperrt, und die Website zeigt die Wartungsseite. Was tun Sie?
5. Warum sollten Cronjobs für ein Wartungsfenster pausiert werden?

<details>
<summary>Antworten</summary>

1. **D.** Mit `Retry-After` und `no-store`
   ([Wartungsmodus, Kurz gesagt](../../../../anleitungen/wartungsmodus.md#kurz-gesagt)).
2. **Falsch.** Sie endet erst, wenn jemand sie ausschaltet.
3. Die Administration unter `/admin` bleibt erreichbar; ohne die Option ist auch die Administration offline.
4. In einer Shell: `php bin/php/maintenance.php off`; kann PHP nicht laufen, löschen Sie `var/maintenance.json`
   ([Fehlerbehebung](../../../../anleitungen/wartungsmodus.md#15-fehlerbehebung)).
5. Sie laufen weiter gegen eine Website, die gerade geändert wird, und können fehlschlagen oder halbfertige Daten
   schreiben.

</details>

## Weiterführende Literatur

- [Wartungsmodus](../../../../anleitungen/wartungsmodus.md): die vollständige Anleitung, einschließlich Caches und
  Deploys.
- [Funktionsreferenz Wartungsmodus](../../../../funktionen/6.0/wartungsmodus.md).
- [Wartungsmodus für Hoster](../../../hosting/betrieb/wartungsmodus.md).

## Verwandte Seiten

- [Modul 9: Sicherungen und Wartung](README.md)
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
