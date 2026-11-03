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
- **Die eigene Datenbank der Website, vorausgewählt**: „Die Datenbank dieser Website öffnen“ auf dem Dashboard öffnet
  mit einem Klick die Hauptdatenbank der Installation, gelesen aus `site.ini [DatabaseSettings]` des Siteaccess so,
  wie der Kernel sie liest, als konfigurierten Server `main` von AdminNeo. Die Anmeldung kommt aus der INI: Sie wird
  nie eingegeben, in kein Formular geschrieben und nicht angezeigt, und in der Adresse steht nur der Serverschlüssel
  `main`. Eine SQLite-Datenbank ohne Pfad ist wie für den Kernel die Datei in `var/storage/sqlite3/`; ihre
  Tabellenliste lässt die eigenen Tabellen von SQLite (`sqlite_sequence` ...) weg.
- **Jede andere Datenbank, die Datenbankauswahl**: „Mit einer anderen Datenbank verbinden“ auf dem Dashboard, der Link
  „Datenbank wechseln“ im Editor und „Logout“ von AdminNeo öffnen die Datenbankauswahl
  (`/dse/dashboard/(dse_choose)/1`): „Datenbank dieser Website öffnen“ mit einem Klick, die in dieser Sitzung gemerkten
  Anmeldungen (die aktuelle Verbindung ist mit „Jetzt verbunden“ markiert, ein Klick wechselt zu einer anderen) und
  das eigene Anmeldeformular von AdminNeo mit jedem Treiber, den es hier hat (MySQL/MariaDB, PostgreSQL, MS SQL,
  SQLite, Oracle, MongoDB ..., soweit ihre PHP-Erweiterungen geladen sind), und den freien Feldern für Server,
  Benutzername, Passwort und Datenbank; eine SQLite-Datei wird mit ihrem Pfad relativ zur Installation angegeben (nur
  innerhalb davon). Die Liste zeigt nur Treiber, Server, Benutzername und Datenbank, nie ein Passwort. `main` ist nur
  für Anfragen zu ihr der konfigurierte Server, das freie Formular steht also immer zur Verfügung. „Logout“ vergisst
  die Anmeldung, von der es abmeldet; die anderen bleiben in der Liste.
- **Sicherheitsschranke**: ein verpflichtender Haftungshinweis und ein Bildschirm zur **Bestätigung der Sicherung** bei
  jedem Aufruf aus dem Menü. Ist er in einer Sitzung bestätigt, führt „Datenbank wechseln“ direkt zur
  Datenbankauswahl; ohne diese Bestätigung (eine neue Sitzung) zeigt die Adresse der Auswahl zuerst die Warnung.
- **Gegen CSRF geschützter Wechsel der Datenbank**: Der Link „Datenbank wechseln“ trägt ein Token `dse_switch` pro
  Sitzung; „return to last database“ funktioniert, ohne URLs zu speichern; die Sitzungen mit Serverpasswort von
  AdminNeo bleiben über die Schranke hinweg erhalten.
- **Ansichtsparameter statt Query-Strings**: Der Zustand des Editors steht als Ansichtsparameter in der Adresse, zum
  Beispiel `/dse/dashboard/(sqlite)/main/(username)/~/(db)/~dmFy.../(select)/ezsection`. Einfache Werte bleiben
  lesbar, `~` ist ein leerer Wert, andere Werte (Pfade mit Schrägstrichen) sind `~` und ihre base64url-Form, und
  Filter und Spaltenlisten reisen gemeinsam in `(qs)`. AdminNeo arbeitet intern weiter mit Query-Strings; die Ansicht
  füllt sie aus den Ansichtsparametern und schreibt jeden Link, jede Formularadresse, jede Skriptadresse und jede
  Weiterleitung um, die AdminNeo ausgibt. Die GET-Formulare von AdminNeo (Suche, Filter, Datenbankauswahl) werden
  gesendet und an ihre Adresse mit Ansichtsparametern weitergeleitet, und eine alte Adresse mit `?sqlite=...` oder
  `?dse_switch=...` wird ebenso weitergeleitet. Hintergrundanfragen der Skripte von AdminNeo hängen `&page=2` oder
  `&script=db` an den Pfad an, was die Ansicht liest. Das Formular für SQL-Befehle wird nur gesendet; es schreibt die
  Anweisung nicht mehr in die Adresse. Eine Anmeldung ist nie ein Ansichtsparameter.
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
| `dse.ini` | `DSESettings` | `InsecureUse` | `disabled` | Bei `enabled` wird das Datenbankpasswort im Anmeldeformular vorausgefüllt. Nur das freie Anmeldeformular von AdminNeo („Mit einer anderen Datenbank verbinden“) verwendet es; die eigene Datenbank der Website zeigt nie ein Anmeldefeld. Nur in vertrauenswürdigen, geschlossenen Umgebungen verwenden |

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
- [Übersicht über das Ökosystem](../../../geschichte/oekosystem.md): das Upstream-Werkzeug
- [Velocity-Engines](../../../bc/6.0/velocity-engines.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/sevenx_dse.md) und [Versionshinweise](../../../../changelogs/extensions/sevenx_dse.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/sevenx_dse.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-04](../../../geschichte/erweiterungen/monate/2026-04.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
