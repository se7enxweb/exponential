# expai: Exponential Intelligence

Lesen Sie diese Seite, um zu entscheiden, ob Sie Exponential Intelligence einschalten, den Schreibassistenten der
Administrationsoberfläche, und um seine eigene Dokumentation zu finden. Die Extension ist das Composer-Paket
`se7enxweb/expai`, das `composer.json` ab 6.0.15 verlangt (`~0.0.1.0`); sie liegt also auf jeder Installation in
`extension/expai` und bleibt inaktiv, bis sie aktiviert wird. Ihre vollständige Dokumentation liegt bei ihr, im
[Repository von expai](https://github.com/se7enxweb/expai).

## Kurz gesagt

| | |
|---|---|
| Was sie tut | Redakteure bitten in einfacher Sprache um neue Inhalte oder Änderungen; der Assistent sucht heraus, was er braucht, und schreibt **Entwürfe**, als der angemeldete Redakteur und mit dessen Berechtigungen. Er veröffentlicht nie von sich aus. |
| Wo | Der Reiter **Intelligence** der Vollansicht eines Knotens, das Bearbeitungsformular (Feldaktionen, Voreinstellungen, Menü Improve, Alternativtext), **Vector** (Threads), Dashboard > Exponential Intelligence, Setup > EI: Summary |
| Anbieter | Anthropic, jeder OpenAI-kompatible Dienst (Melious.ai in der EU, Hugging Face, OpenRouter, Mistral ...), ein lokales Modell mit offenen Gewichten über Ollama |
| Braucht | Exponential 6.0.14 oder neuer, PHP 8.0+ mit `curl`, `dom`, `json`, `mbstring`; ezjscore, ezformtoken; [expqueue](expqueue.md) für eingereihte Arbeit; einen Schlüssel eines Anbieters oder ein lokales Modell |
| Release | [0.0.1.0](https://github.com/se7enxweb/expai/releases/tag/v0.0.1.0) |

## Für Redakteure

- **Der Reiter Intelligence**: eine Unterhaltung pro Benutzer und Knoten; jede Abfrage und jeder Entwurf ist ein
  Schritt; die geschriebenen Entwürfe werden mit Links zum Bearbeiten, Ansehen und Verwerfen aufgeführt. **Save as
  drafts** behält sie, **Send for approval** führt die normale Veröffentlichungsoperation als der Redakteur aus, sodass
  ein Freigabe-Workflow die Version festhält.
- **Das Bearbeitungsformular**: „Fill the fields“ aus einer Vorgabe, Aktionen pro Feld (Write, Rewrite, Shorten,
  Translate, Suggest), Voreinstellungen pro Klasse (Titel, Zusammenfassung, Meta-Beschreibung, Schlüsselwörter), ein Menü
  **Improve** an jedem Textfeld und eine Zeile mit Modell, Region und Kosten eines Klicks. Die Werte kommen zur Prüfung
  ins Formular.
- **Alternativtext** für Bilder aus einem Bildmodell und ein Bericht „Missing alt text“, der Vorschläge für einen
  Teilbaum einreiht.
- **Vector**: Threads mit Modi für Umfang, Planen und Schreiben, Planungsmodus mit Kostenschätzung, schrittweise Prüfung
  jeder Änderung pro Feld und Teil, parallele Läufe eines genehmigten Plans, Checkpoints, Fortschritt live über
  Server-Sent Events und **What changed** mit Zurücknehmen für direkt geschriebene Entwürfe.
- **KI-Ketten** (schreiben, prüfen, überarbeiten, SEO, ein menschlicher Kontrollpunkt, übersetzen) und
  **Übersetzungsabgleich** laufen über die Auftragswarteschlange.

## Für Administratoren

- **Anbieter und Modelle**, mit Rückfall auf den Standard der Website, und eine **Probe**: Nur Modelle, die eine
  Live-Probe des Tool-Callings bestanden haben (oder per Konfiguration als vertrauenswürdig gelten), dürfen Entwürfe
  schreiben.
- **Datenrichtlinie**: eine Region (`us`, `eu`, `local`) und Aufbewahrung pro Anbieter, Regeln pro Sektion und Klasse,
  überall durchgesetzt und pro Anfrage erfasst.
- **Nutzung, Kosten, Grenzen und Budgets**: jede Anfrage in einem Nutzungsprotokoll (nur Benutzer-IDs), die geschätzten
  Kosten aus einer Preistabelle (Preise in einer anderen Währung mit den Referenzkursen der EZB umgerechnet), Grenzen pro
  Benutzer, Tag, Monat, Website und Anbieter, Spuren und Batch-APIs für Massenarbeit, Budgets für eingereihte Arbeit.
- **Hauptschalter** (`[AISettings] Enabled`, auch pro Siteaccess abschaltbar) und ein **Veto-Hook**, der eine Anfrage
  mit Begründung ablehnt.
- **Audit**: Jede Anfrage, jeder Entwurf und jede Änderung des Status eines Entwurfs ist ein Ereignis des Kanals `ai`
  des [Prüfprotokolls](../../../../eng-us/bc/6.0/audit.md), mit eigener Quelle in der Audit-Konsole (Dashboard-Bereich, Links,
  Diagramme, CSV-Spalten). Prompts, Antworten und Schlüssel werden nie erfasst.
- **EI: Summary** (`expai/summary`): eine druckbare Seite für einen Datenschutzbeauftragten mit Region, Aufbewahrung,
  Datum der Bedingungen und Quelle des Schlüssels (nie dem Schlüssel) jedes Anbieters, Rollen, Spuren, Grenzen und
  externen Werkzeugen.

## Für Entwickler

- Werkzeuge, die das Modell aufrufen kann: Lesedienste von expservices, die mit `'ai' => true` freigegeben sind
  ([Backend-Dienste](../../../../eng-us/bc/6.0/backend_ezjscore_services.md)), die Brücke im Prozess zur Extension expmcp, wenn sie
  installiert ist, und externe MCP-Server mit einer Freigabeliste.
- Sie baut auf allgemeinen Kernel-Funktionen auf: [Installation als Entwürfe und das JSON-Paketformat](../../../../eng-us/bc/6.0/package-json-format.md),
  das Ereignis `package/install/drafts`, [Audit-Quellen](../../../../eng-us/bc/6.0/audit.md) und in
  [rad.ini](../../../../eng-us/bc/6.0/rad-extension-surface.md) deklarierte Registries.

## Einschalten

1. Aktivieren Sie sie für den oder die Admin-Siteaccesses: in der `site.ini.append.php` **jedes** Admin-Siteaccess
   `[ExtensionSettings] ActiveAccessExtensions[]=expai` (oder `ActiveExtensions[]=expai` in
   `settings/override/site.ini.append.php` für jeden Siteaccess). Bei der Form pro Siteaccess nennt jede Skript- und
   Cron-Zeile, die expai braucht, diesen Siteaccess mit `-s <admin siteaccess>`.
2. Schlüssel kommen nur nach `settings/override/expai.ini.append.php` (Modus 0640, nie committet):

   ```ini
   [AISettings]
   Enabled=true
   ApiKey=<your provider key>
   ```

3. Legen Sie die Tabellen an, auf jeder Engine des Kernels (idempotent; zugleich die Aktualisierung):

   ```bash
   php extension/expai/bin/php/expaischema.php --install -s <admin siteaccess>
   ```

4. Geben Sie Redakteuren `expai/write` und `ezjscore/call` (Funktionsliste `expai`); weitere Richtlinien öffnen Vector,
   Ketten, Web- und MCP-Werkzeuge.
5. `php bin/php/ezpgenerateautoloads.php -e`, leeren Sie die INI- und Template-Caches, laden Sie PHP-FPM neu oder führen
   Sie `./console exp:velocity deploy --packer` aus.
6. Cron: `php runcronjobs.php -q -s <admin siteaccess> expqueue` jede Minute und
   `php runcronjobs.php -q -s <admin siteaccess> expai` alle paar Minuten.

Die vollständigen Schritte, mit Streaming hinter Apache oder nginx und den Prüfungen: die
[Installationsanleitung](https://github.com/se7enxweb/expai/blob/main/INSTALL.md). Der tägliche Gebrauch: die
[Benutzungsanleitung](https://github.com/se7enxweb/expai/blob/main/USAGE.md). Jedes Thema:
[das Dokumentationsverzeichnis](https://github.com/se7enxweb/expai/blob/main/doc/index.md), auch in der
Administrationsoberfläche unter `expai/help` gezeigt.

## Verwandte Seiten

- [expqueue](expqueue.md), die Auftragswarteschlange
- [Exponential installieren, Abschnitt 10](../../../INSTALL.md#10-nach-der-installation)
- [Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md#12-october-2026-exponential-intelligence-in-the-distribution)
- [Verzeichnis der Extensions](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
