---
title: "Lektion 7.2: Einschalten, Schlüssel, Limits und Budgets"
audience: trainer, admin
category: training
order: 72
summary: Administratoren folgen den Schritten zum Einschalten des Assistenten, halten Schlüssel aus dem Repository heraus, verwenden den Hauptschalter und den Schalter pro Siteaccess, setzen Nutzungslimits und lesen EI: Budgets. 60 Minuten.
keywords: exponential intelligence, administration, aktivieren, einschalten, hauptschalter, enabled, disabled siteaccesses, api-schlüssel, settings override, limits, budgets, ei budgets, cron, auftragswarteschlange, expaischema, activate, switch on, master switch, api key, job queue
type: help
---
# Lektion 7.2: Einschalten, Schlüssel, Limits und Budgets

Der Assistent wird mit jeder Installation mitgeliefert und bleibt ausgeschaltet, bis ein Administrator ihn einschaltet.
Diese Lektion geht durch das sichere Einschalten auf einer Schulungsinstallation, zeigt, wohin die Schlüssel gehören,
wie man ihn wieder ausschaltet und wie man seine Nutzung mit Limits und Budgets begrenzt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Intelligence](../README.md) |
| Modul | [Modul 7: Administration](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren mit Shell-Zugang zu einer Schulungsinstallation |
| Voraussetzungen | [Lektion 7.1](anbieter-und-modelle.md); eine Schulungsinstallation und ein Anbieterschlüssel, der für Schulungen gedacht ist, oder ein lokales Modell |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Schritte zum Einschalten des Assistenten aufzählen: aktivieren, Schlüssel, Tabellen, Richtlinien, Caches, Cron;
- Schlüssel nur in `settings/override/expai.ini.append.php` (Modus 0640) oder an einem nicht versionierten Ort für
  Schlüssel halten;
- den Assistenten für die ganze Website oder einen Siteaccess ausschalten;
- Nutzungslimits in `[Limits]` setzen und auf dem Dashboard lesen;
- **EI: Budgets** lesen: das Monatsbudget, Auftragsgruppen, Spuren und Anbieter-Batches.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [expai: Einschalten](../../../../funktionen/6.0/erweiterungen/expai.md#einschalten) und die Seiten zur
Konfiguration und zu Budgets unter **EI: Help** (`extension/expai/doc/configuration.md`,
`extension/expai/doc/budgets.md`).

1. **Die Schritte** (6 Minuten). Von der Funktionsseite:
   1. `expai` für den oder die Admin-Siteaccesses aktivieren (`ActiveAccessExtensions[]=expai` pro Admin-Siteaccess oder
      `ActiveExtensions[]` für alle);
   2. den Schlüssel nur in `settings/override/expai.ini.append.php` schreiben (Modus 0640, nie committet), mit
      `[AISettings] Enabled=true`;
   3. die Tabellen anlegen: `php extension/expai/bin/php/expaischema.php --install -s <admin siteaccess>`;
   4. Redakteuren `expai/write` und `ezjscore/call` (Funktionsliste `expai`) geben;
   5. Autoloads neu erzeugen, den INI- und den Template-Cache leeren, PHP neu laden;
   6. Cron: den Teil `expqueue` jede Minute und den Teil `expai` alle paar Minuten.
2. **Schlüssel** (3 Minuten). Nie in einer versionierten Datei, nie in einem Template, nie in einem Ticket oder Chat.
   **EI: Models** warnt vor einem Schlüssel in einer anderen Datei oder in einer Datei, die jeder Benutzer des Servers
   lesen kann.
3. **Ausschalten** (3 Minuten). `[AISettings] Enabled=false` schaltet alles aus (kein Tab, keine Bedienelemente im
   Bearbeitungsformular, kein Vector, nichts wird in die Warteschlange gestellt, wartende Aufträge werden angehalten,
   keine Anfrage an irgendeinen Anbieter); `DisabledSiteAccesses[]` schaltet ihn auf einzelnen Siteaccesses aus. Das
   Dashboard sagt dann, dass er ausgeschaltet ist.
4. **Limits** (4 Minuten). Alle optional: Tokens pro Gespräch, pro Sitzung, pro Benutzer und Tag und Monat, Kosten pro
   Benutzer und Tag und Monat, pro Website und Monat, pro Anbieter und Monat, Anfragen pro Durchgang. Sie werden vor
   jeder Anfrage geprüft; dem Redakteur wird gesagt, welches Limit erreicht ist und wann es zurückgesetzt wird.
5. **Budgets** (4 Minuten). Setup > **EI: Budgets**: **Budget this month** (Arbeit in der Warteschlange), **Job
   groups**, **Lanes**, **Provider batches**. Eine Auftragsgruppe kann ein eigenes Budget haben (zum Beispiel das
   optionale Budget des Berichts zu Alternativtexten).

### Häufige Fragen

**Können wir es ohne gehosteten Anbieter ausprobieren?** Ja, mit einem lokalen Modell und der Region `local`; langsam
auf gewöhnlichen Servern.

**Wo kommen die eigenen Limits des Anbieters ins Spiel?** Die Ratenlimits des Anbieters und das Ausgabenlimit der
Organisation gelten ebenfalls; das Dashboard zeigt die Ratenlimits, die der Anbieter zuletzt gemeldet hat.

**Brauche ich die Auftragswarteschlange?** Für Ketten, Alternativtexte in der Warteschlange, den Übersetzungsabgleich
und Läufe im Hintergrund ja.

### Stolperfallen

- **Ein Schlüssel in einer committeten Datei.** Lassen Sie ihn in diesem Fall beim Anbieter erneuern und verschieben Sie
  ihn.
- **Keine Limits auf einer Schulungs-Website.** Setzen Sie vor dem Kurs tägliche Limits pro Benutzer.
- **Die Aktivierung pro Siteaccess in Cron-Zeilen vergessen:** Nennen Sie den Admin-Siteaccess mit `-s`.

## Praktische Übung

Verwenden Sie eine Schulungsinstallation, auf der Sie Shell-Zugang haben (Ihre eigene, installiert mit
`php bin/php/console exp:install --apply`, siehe [7. Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md)).
Nie auf einer Live-Website.

1. Lesen Sie die Schritte „Einschalten“ der Funktionsseite und prüfen Sie, welche auf Ihrer Installation schon erledigt
   sind (ist `expai` für den Admin-Siteaccess aktiv? sind die Tabellen da?).
2. Öffnen Sie `settings/override/expai.ini.append.php` (legen Sie die Datei an, falls sie fehlt, Modus 0640). Fügen Sie
   `[AISettings]`, `Enabled=true` und Ihren Schulungsschlüssel hinzu, oder konfigurieren Sie ein lokales Modell, wie
   die Installationsanleitung es beschreibt. Committen Sie die Datei nicht.
3. Führen Sie die Tabelleninstallation für Ihren Admin-Siteaccess aus:

   ```bash
   php extension/expai/bin/php/expaischema.php --install -s <admin siteaccess>
   ```

4. Fügen Sie ein Limit für die Schulungsbenutzer hinzu, zum Beispiel in derselben Datei:

   ```ini
   [Limits]
   MaxCostPerUserPerDay=1
   ```

5. Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`) und laden Sie PHP neu, wie Ihre Installation es
   verlangt.
6. Öffnen Sie die Dashboard-Seite **Exponential Intelligence**: Die Statuszeile sagt **Ready**, und **Limits** zeigt
   einen Balken für Ihr Limit.
7. Öffnen Sie Setup > **EI: Budgets** und lesen Sie seine vier Abschnitte.
8. Schalten Sie den Assistenten mit `Enabled=false` aus, leeren Sie den INI-Cache und prüfen Sie, dass das Dashboard
   sagt, dass er ausgeschaltet ist. Schalten Sie ihn wieder ein.

## Erwartetes Ergebnis

- Der Assistent ist eingeschaltet, mit dem Schlüssel nur in der Override-Datei, einem auf dem Dashboard sichtbaren
  täglichen Kostenlimit, und die Budgetseite wurde gelesen.
- Aus- und Einschalten hat funktioniert.
- Der Trainer prüft das, indem er jede Person fragt, wo ihr Schlüssel gespeichert ist, und am Beamer `git status`
  zeigt (die Override-Datei darf nicht als vorgemerkt erscheinen).

## Quiz

1. Wo darf ein API-Schlüssel gespeichert werden?
2. Was bewirkt `[AISettings] Enabled=false`?
3. Welche Einstellung schaltet den Assistenten nur für einen Siteaccess aus?
   A. `Enabled` B. `DisabledSiteAccesses[]` C. `AllowedModels[]` D. `Region`
4. Nennen Sie drei Arten von Limits.
5. Welche Cron-Teile braucht der Assistent?

<details>
<summary>Antworten</summary>

1. In `settings/override/expai.ini.append.php` (Modus 0640, nie committet) oder an einem nicht versionierten Ort für
   Schlüssel.
2. Es schaltet alles aus: kein Tab, keine Bedienelemente im Bearbeitungsformular, kein Vector, nichts in der
   Warteschlange, keine Anfrage an irgendeinen Anbieter.
3. **B.**
4. Drei beliebige aus: Tokens pro Gespräch, pro Sitzung, pro Benutzer und Tag oder Monat; Kosten pro Benutzer und Tag
   oder Monat; pro Website und Monat; pro Anbieter und Monat; Anfragen pro Durchgang.
5. Den Teil `expqueue` (jede Minute) und den Teil `expai` (alle paar Minuten).

</details>

## Weiterführende Informationen

- [expai: Einschalten](../../../../funktionen/6.0/erweiterungen/expai.md#einschalten)
- [expqueue](../../../../funktionen/6.0/erweiterungen/expqueue.md): die Auftragswarteschlange.
- Die Installationsanleitung und die Seiten zur Konfiguration und zu Budgets unter **EI: Help**.

## Verwandte Seiten

- [Modul 7: Administration](README.md)
- [Exponential Intelligence: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
