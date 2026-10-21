# sevenx_dse: der Database Source Editor

Diese Seite ist für Administratoren, die die Live-Datenbank ansehen und mit Vorsicht ändern müssen, ohne die
Administrationsoberfläche zu verlassen. `sevenx_dse` („7x Database Source Editor“) bettet das Datenbankwerkzeug
**AdminNeo** (ein nachgelagerter Fork von Adminer) als natives Modul der Administrationsoberfläche von Exponential ein,
unter `/dse/dashboard`. Es ist die einzige freigegebene Extension für einen Datenbankeditor in Exponential.

## Was sie Ihnen gibt

- **Ausführen von rohem SQL** mit Syntaxhervorhebung.
- **Durchsehen von Tabellen** mit Filter, Sortierung und Blättern; **Untersuchen und Bearbeiten des Schemas** (Tabellen,
  Spalten, Indizes, Fremdschlüssel).
- **Import** (SQL, CSV, TSV) und **Export** (SQL-Dump, CSV, TSV) als direkte Datei-Downloads, die die Template-Hülle
  umgehen.
- **Mehrere Datenbanktreiber**: MySQL 8.0+ und MariaDB 10.3+, PostgreSQL 14+, SQLite 3.35+.
- **Sicherheitsschranke**: ein verpflichtender Haftungshinweis und ein Bildschirm zur **Bestätigung der Sicherung** bei
  jedem direkten Zugriff.
- **Gegen CSRF geschützter Wechsel der Datenbank**: Der Link „Change database“ trägt ein Token `dse_switch` pro
  Sitzung; „return to last database“ funktioniert, ohne URLs zu speichern; die Sitzungen mit Serverpasswort von
  AdminNeo bleiben über die Schranke hinweg erhalten.
- **Rollenrichtlinie**: Der Zugriff ist die Richtlinie `dse/dashboard`.
- **CSS-Isolierung**: Die globalen Stile von AdminNeo können das eigene Layout der Administrationsoberfläche nicht
  beschädigen.

Benötigt PHP 8.3 oder neuer (8.3 und 8.5 empfohlen), Exponential 6 und das Design admin3.

## Einrichten

1. Installieren Sie das Paket:

```bash
composer require se7enxweb/sevenx_dse
```

2. Aktivieren Sie es in `settings/override/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveExtensions[]=sevenx_dse
```

3. Erzeugen Sie die Autoloads neu und leeren Sie die Caches:

```bash
php bin/php/ezpgenerateautoloads.php
php bin/php/ezcache.php --clear-all
```

4. Weisen Sie die Richtlinie `dse/dashboard` nur den Rollen zu, die sie haben müssen.
5. Erstellen Sie zuerst eine Sicherung (zum Beispiel mit [git_manager](git_manager.md)), öffnen Sie dann
   `/dse/dashboard` und bestätigen Sie die Sicherung.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `dse.ini` | `DSESettings` | `InsecureUse` | `disabled` | Bei `enabled` wird das Datenbankpasswort im Anmeldeformular vorausgefüllt. Nur in vertrauenswürdigen, geschlossenen Umgebungen verwenden |

## Was sich geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.0.0 | 23. April 2026 | Erstes stabiles Release; README mit Bildschirmfotos. |
| 1.1.0 | 22. September 2026 | Modulansichten sind auf einem dauerhaften Worker (Velocity) sicher. |
| 1.1.1 | 24. September 2026 | Zwei Fehler auf einem dauerhaften Worker, siehe unten. |
| 1.1.2 bis 1.1.4 | 27. bis 30. September 2026 | `ezinfo.php` und `extension.xml` melden die Release-Version und nennen die Lizenz vollständig; der **Navigationsteil hat einen eigenen Bezeichner** (`dsenavigationpart`: `menu.ini` verwendete `ezupdatenavigationpart`, den Bezeichner von ezupdate, und benannte so den Teil dieser Extension überall dort in „DSE“ um, wo beide aktiv waren); Menütexte übersetzt (Englisch und Deutsch); Metadaten zur Finanzierung; die Beschreibung nennt Exponential. |

Zu 1.1.1:

- Die Nonce der Content Security Policy von AdminNeo **blieb für jede Anfrage gleich**, die ein Worker bediente
  (`get_nonce()` hielt sie in einer statischen Funktionsvariablen), sodass die Richtlinie ihren Sinn verlor. Sie liegt
  jetzt in `$GLOBALS`, frisch pro Anfrage.
- Eine **zweite Dashboard-Anfrage im selben Worker brach ab**, weil AdminNeo pro Anfrage Konstanten und Treiberfunktionen
  deklariert. Das Dashboard bittet den Worker jetzt, nach seiner Antwort ersetzt zu werden, sodass die nächste Anfrage
  einen frischen erhält (ein abgesicherter Aufruf, der unter jedem anderen Server nichts tut).

## Verwandte Seiten

- [Datenbankverwaltung AdminNeo](../adminneo-datenbankverwaltung.md)
- [Übersicht über das Ökosystem](../../../../eng-us/history/ecosystem.md): das Upstream-Werkzeug
- [Velocity-Engines](../../../../eng-us/bc/6.0/velocity-engines.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../../eng-us/history/extensions/sevenx_dse.md) und [Versionshinweise](../../../../changelogs/extensions/sevenx_dse.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/sevenx_dse.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- Monate: [2026-04](../../../../eng-us/history/extensions/months/2026-04.md), [2026-09](../../../../eng-us/history/extensions/months/2026-09.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
