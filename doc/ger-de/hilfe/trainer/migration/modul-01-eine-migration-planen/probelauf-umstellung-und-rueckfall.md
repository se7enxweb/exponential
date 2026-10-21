---
title: "Lektion 1.4: Probelauf, Umstellung und Rückweg"
audience: trainer, admin, developer, project-manager
category: training
order: 14
summary: Die Teilnehmenden planen Probeläufe, die das Zeitfenster der Umstellung messen, schreiben ein Umstellungs-Runbook mit Einfrieren und Wartungsfenster und definieren einen Rückweg, der Zurückschalten bedeutet statt Rückgängigmachen. 45 Minuten.
keywords: migration zu exponential, probelauf, generalprobe, umstellung, einfrieren, wartungsmodus, rückweg, entscheidungsfenster, migrating to exponential, rehearsal, dress rehearsal, cut-over, freeze, maintenance mode, rollback, DNS, decision window, runbook
type: help
---
# Lektion 1.4: Probelauf, Umstellung und Rückweg

Eine Migration gelingt oder scheitert in ihren letzten Stunden. Diese Lektion vermittelt die drei Gewohnheiten, die
die Dokumentation in jedem Kapitel wiederholt: proben, bis der Lauf langweilig ist, nach einem Runbook umstellen und
durch Zurückschalten zurückkehren, niemals durch das Rückgängigmachen von Anweisungen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 1: Eine Migration planen](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren, Entwickler und Projektleiter |
| Voraussetzungen | [Lektion 1.3](phasen-und-die-checkliste.md); eine Schulungsinstallation mit Shell-Zugang |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, warum eine Migration mindestens zweimal geprobt wird und was jeder Probelauf misst;
- ein Umstellungs-Runbook mit Einfrieren, letztem Import, Prüfungen und dem Umschalten schreiben;
- eine Exponential-Installation in den Wartungsmodus versetzen und wieder herausnehmen;
- Auslöser für den Rückweg, die entscheidende Person und das Entscheidungsfenster festlegen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Checkliste, Abschnitte 6, 8 und 9](../../../../installation/migration/checkliste.md),
[Kapitel 14.2](../../../../installation/14-migration-von-4x.md#142-die-migration-auf-einer-staging-kopie-planen) und
[Kapitel 14.14](../../../../installation/14-migration-von-4x.md#1414-rollback-plan).

1. **Proben** (6 Minuten). Bauen Sie die neue Website neben der alten auf; spielen Sie eine Produktionskopie ein;
   führen Sie das ganze Verfahren durch; notieren Sie jeden Befehl und wie lange er gedauert hat; korrigieren und
   wiederholen Sie, bis es ohne manuelle Reparatur von oben bis unten durchläuft. Die Checkliste verlangt mindestens
   zwei Probeläufe.
2. **Umstellung** (6 Minuten). Abschnitt 8 der Checkliste: das Einfrieren ankündigen, letzter Export und Import,
   Zählungen und Tests der Weiterleitungen, am Vortag die DNS-Lebensdauer (TTL) senken, umschalten, Caches leeren und
   Seiten vorwärmen, Logs und die 404-Liste beobachten, die Sitemap einreichen.
3. **Wartungsmodus** (3 Minuten). Exponential nimmt eine Website mit `php bin/php/maintenance.php` vom Netz; sie
   antwortet dann mit HTTP 503 (Kapitel 14.16). Zeigen Sie die Anleitung [Wartungsmodus](../../../../anleitungen/wartungsmodus.md).
4. **Rückweg** (5 Minuten). Kapitel 14.14: „Eine Migration wird zurückgenommen, indem man **nicht** umschaltet,
   niemals durch Rückgängigmachen der migrierten Datenbank.“ Lassen Sie die alte Website und ihre Datenbank
   unangetastet; das Zurückschalten ist derselbe DNS- oder Proxy-Vorgang in umgekehrter Richtung. Änderungen, die
   nach dem Umschalten auf der neuen Website gemacht wurden, gehen beim Zurückschalten verloren; halten Sie das
   Fenster deshalb kurz und die Bearbeitung eingefroren oder protokolliert.

### Häufige Fragen

**Können wir eine Update-Datei erneut ausführen, um einen halb fertigen Lauf zu reparieren?** Nein. Kapitel 14.14:
Spielen Sie den vor dem Lauf erstellten Dump wieder ein und beginnen Sie das Runbook von vorn.

**Wer entscheidet über den Rückweg?** Legen Sie das vorab fest, zusammen mit den Auslösern (Checkliste, Abschnitt 9),
zum Beispiel ein bestimmter Anteil defekter Schlüsselseiten oder eine nicht funktionierende Anmeldung.

**Wie lange behalten wir die alte Website?** Bis zur Abnahme; Kapitel 16.9 sagt: mindestens einen vollständigen
Redaktionszyklus auf dem neuen System.

### Stolperfallen

- **Auf einer anderen Datenbank-Engine proben.** Der Probelauf misst den echten Lauf nur, wenn er die Engine der
  Produktion und eine realistische Kopie verwendet.
- **Caches vor dem PHP-Reload leeren.** Eine Seite, die zwischen dem Leeren und dem Reload mit altem Code erzeugt
  wird, landet wieder im Cache; Kapitel 14.13 führt „veraltete Seiten nach dem Umschalten“ auf.
- **Keine Dumps aufbewahrt.** Kapitel 14.14 nennt drei, die aufzubewahren sind: den alten Produktions-Dump, den
  migrierten Dump nach den Prüfungen und das Archiv von `var/`.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation (installiert mit `php bin/php/console exp:install`) als „neue
Website“. Sie versetzen sie in den Wartungsmodus und wieder zurück, wie bei einer Umstellung.

1. Zeigen Sie im Wurzelverzeichnis der Installation die Optionen des Wartungsskripts an:

   ```bash
   php bin/php/maintenance.php --help
   ```

2. Öffnen Sie die Startseite Ihrer Schulungswebsite in einem Browser und notieren Sie, was Sie sehen.
3. Schalten Sie den Wartungsmodus mit einer Nachricht für Besucher ein und prüfen Sie dann den Zustand:

   ```bash
   php bin/php/maintenance.php on --message="Training: migration cut-over rehearsal" --until=30m
   php bin/php/console exp:maintenance status
   ```
4. Laden Sie die Startseite neu. Notieren Sie die Seite und in den Entwicklerwerkzeugen des Browsers den HTTP-Status.
5. Schalten Sie den Wartungsmodus mit `php bin/php/maintenance.php off` aus und laden Sie die Startseite neu.
6. Schreiben Sie in Ihren Notizen ein Umstellungs-Runbook mit zehn nummerierten Zeilen für das WordPress-Szenario aus
   Lektion 1.3, anhand von Abschnitt 8 der [Checkliste](../../../../installation/migration/checkliste.md#8-umstellung).
   Setzen Sie den Wartungsschritt an die richtige Stelle, auf der **alten** Website, und nennen Sie, wer jede Zeile
   ausführt.
7. Ergänzen Sie einen Abschnitt zum Rückweg: zwei Auslöser, wer entscheidet, und das Entscheidungsfenster in Tagen.
8. Ergänzen Sie die drei Dumps aus Kapitel 14.14, die Sie aufbewahren werden, jeweils mit ihrem Speicherort.

## Erwartetes Ergebnis

- Schritt 4: Die Website antwortete mit der Wartungsseite und dem HTTP-Status 503.
- Schritt 5: Die Startseite ist wieder da.
- Das Runbook enthält das Einfrieren, den letzten Export und Import, Zählungen und Tests der Weiterleitungen, das
  Umschalten von DNS oder Proxy, das Leeren und Vorwärmen der Caches sowie das Beobachten von Logs und 404-Fehlern.
- Der Abschnitt zum Rückweg nennt konkrete Auslöser, eine entscheidende Person und ein Fenster.
- Der Trainer prüft den Wartungsschritt auf dem Bildschirm einer Person und liest ein Runbook laut vor.

## Quiz

1. Wie viele Probeläufe verlangt die Checkliste mindestens, und was messen Sie darin?
2. Ein letzter Lauf scheitert mitten in den Datenbankschritten. Was tun Sie?
   A. Die fehlgeschlagene Update-Datei erneut ausführen B. Den vor dem Lauf erstellten Dump einspielen und neu
   beginnen C. Die Tabellen von Hand bearbeiten D. Trotzdem DNS umschalten
3. Warum senkt man die DNS-Lebensdauer einen Tag vor dem Umschalten?
4. Richtig oder falsch: Rückweg bedeutet, die migrierte Datenbank rückgängig zu machen.
5. Mit welchem HTTP-Status antwortet der Wartungsmodus von Exponential?

<details>
<summary>Antworten</summary>

1. Zwei; die Zeit jedes Laufs, aus der sich das Zeitfenster der Umstellung ergibt, und die Zählungen im Vergleich zur
   Bestandsaufnahme
   ([Checkliste, Abschnitt 6](../../../../installation/migration/checkliste.md#6-den-import-bauen-und-proben)).
2. **B** ([Kapitel 14.14](../../../../installation/14-migration-von-4x.md#1414-rollback-plan)).
3. Damit das Umschalten, und ein Zurückschalten, die Besucher schnell erreicht
   ([Checkliste, Abschnitt 8](../../../../installation/migration/checkliste.md#8-umstellung)).
4. **Falsch.** Der Rückweg ist das Zurückschalten auf die alte Website, die unangetastet geblieben ist (Kapitel 14.14).
5. 503 ([Kapitel 14.16](../../../../installation/14-migration-von-4x.md#1416-die-upgrade-seiten-des-herstellers-punkt-für-punkt)).

</details>

## Weiterführende Informationen

- [Die gemeinsame Migrations-Checkliste](../../../../installation/migration/checkliste.md): Probelauf, Umstellung und
  Rückweg.
- [Wartungsmodus](../../../../anleitungen/wartungsmodus.md): eine Website vom Netz nehmen und wieder online bringen.
- [Kapitel 16.9: Rückweg](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#169-rollback): der Rückweg
  für die Plattformwege.

## Verwandte Seiten

- [Modul 1: Eine Migration planen](README.md)
- [Migration zu Exponential: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
