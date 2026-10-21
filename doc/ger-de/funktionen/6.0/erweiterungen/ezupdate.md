# ezupdate: Aktualisierungen und Pakete im Admin

Diese Seite richtet sich an Administratoren, die eine Installation aktualisieren oder ihr Pakete hinzufügen. `ezupdate`
("eZ Update") ist der Paketmanager einer Exponential-Installation, im Admin und auf der Kommandozeile.

Eine Installation besteht aus einigen Dutzend Composer-Paketen, einigen Dutzend Extension-Verzeichnissen und ein paar
Einstellungen, die entscheiden, welche davon laufen. ezupdate bringt all das auf einen Bildschirm, zeigt, wo die Teile
nicht übereinstimmen, und lässt Sie Pakete mit denselben Schutzvorkehrungen aktualisieren und installieren, die Sie auch
in einer Shell verwenden würden: eine Prüfung, die nichts verändert, ein Probelauf, eine Bestätigung der Sicherung und
die Ausgabe von Composer selbst, während er läuft.

Die Extension erschien erstmals am 2. November 2024 (1.0.1) als Bildschirm für Composer-Updates. Die Releases 1.1.3 bis
1.1.10 (28. September bis 2. Oktober 2026) machten daraus einen vollständigen Paketmanager. Sie öffnen sie unter
**Setup > Updates und Pakete** (`/update/dashboard`). Die eigenen Anleitungen der Extension (Benutzerhandbuch,
installierte Pakete, Konfiguration, Kommandozeile, Sicherheit, Architektur, FAQ) liegen in ihrem Ordner `doc/`; diese
Seite ist die Übersicht.

## Was Sie tun können

| Reiter oder Funktion | Was er bzw. sie tut | Seit |
|---|---|---|
| **Übersicht** | Wo Composer liegt und welches PHP ihn ausführt, ob Aktualisieren und Installieren erlaubt sind, eine Suche nach Aktualisierungen (`composer outdated`), die nichts verändert, ein Probelauf des Updates, ein abgesichertes Update, die letzten Läufe sowie Karten, die installierte Pakete, Extensions und offene Punkte zählen | 1.0.1, neu gestaltet 1.1.8 |
| **Installierte Pakete** (`/update/installed`) | `composer.json`, `composer.lock`, `vendor/composer/installed.json` und die Extension-Verzeichnisse nebeneinander, mit den Extensions, die `ActiveExtensions` und das `ActiveAccessExtensions` jedes Siteaccess einschalten. Benennt, was nicht übereinstimmt. Filter, Suche, Sortierung, Zeilendetails, teilbare Adresse (`#issues`), Downloads als JSON und CSV. Liest nur Dateien | 1.1.8 |
| **Pakete finden** | Durchsucht packagist.org (standardmäßig Exponential-Extensions) oder jeden Server in `composer.json`. Eine Paketseite zeigt Beschreibung, Versionen, Abhängigkeiten, Lizenz, Downloads, Git-URL und Commit jeder Version und, wenn installiert, wie das Paket geholt wurde sowie Branch und Commit seiner Git-Arbeitskopie. Von dort aus installieren, zuerst als Probelauf | 1.1.3 |
| **dist oder source** | Installiert pro Lauf aus Release-Archiven (schnell) oder als Git-Klone mit Historie, oder lässt Composer entscheiden | 1.1.3 |
| **Paketserver** | Die Composer-`repositories` aus `composer.json` (packagist.org lässt sich abschalten) und die Exponential-Paketserver für `.ezpkg`, die der Einrichtungsassistent liest; ein `.ezpkg` in das lokale Paket-Repository holen | 1.1.3 |
| **Live-Läufe** | Updates und Installationen laufen im Hintergrund, einer nach dem anderen, mit einer Sperre; die Laufseite folgt der Ausgabe von Composer, während sie geschrieben wird, farbig, mit **Folgen**, **Zeilen umbrechen** und **Kopieren** | 1.1.3 |
| **Erneut ausführen / Wirklich ausführen** | Jeder Lauf lässt sich erneut ausführen; ein Probelauf lässt sich wirklich ausführen, unter denselben Schaltern und Bestätigungen | 1.1.4 |
| **Förderung** | Von wem die installierten Pakete gefördert werden möchten, gruppiert wie bei `composer fund`, aus den Paketmetadaten und der `.github/FUNDING.yml` jedes Pakets; als Seite, JSON und Text | 1.1.6 |
| **Kommandozeile** | Alles oben Genannte über `php extension/ezupdate/bin/php/ezupdate.php` | 1.1.3 |

## Einrichten

```bash
composer require se7enxweb/ezupdate
# settings/override/site.ini.append.php:
#   [ExtensionSettings]
#   ActiveExtensions[]=ezupdate
php bin/php/ezpgenerateautoloads.php --extension
php bin/php/ezcache.php --clear-all
```

Öffnen Sie **Setup > Updates und Pakete**. Administratoren sehen den Eintrag sofort. Anderen Rollen gewähren Sie
`update/ezupdate` (ansehen, prüfen, Probelauf) und, wo sie die Installation ändern dürfen,
`update/manage` (aktualisieren, installieren, holen, Server ändern).

**Aktualisieren und Installieren sind ausgeschaltet, bis Sie sie einschalten**, in
`settings/override/ezupdate.ini.append.php`:

```ini
[UpdateSettings]
AllowUpdate=enabled
AllowInstall=enabled
```

Composer schreibt `vendor/` und jedes von ihm verwaltete Paket neu. Schalten Sie diese Einstellungen deshalb nur dort
ein, wo das gewollt ist, und mit einer Sicherung (siehe [git_manager](git_manager.md) für Sicherungen aus dem Admin).

## Wie ein Update abläuft

1. **Nach Aktualisierungen suchen** (`composer outdated`): verändert nichts.
2. **Vorschau (Probelauf)** des Updates: zeigt, was Composer ändern würde. Die Spalte „Art“ entscheidet
   Composer selbst (ein `composer update --dry-run` über die veralteten Pakete):
   *Kann installiert werden* oder *Blocked by composer.json (constraint)* (das "semver-safe-update" von
   composer outdated berücksichtigt composer.json nicht). Nur installierbare Pakete haben ein Ankreuzfeld.
3. Bestätigen Sie, dass eine **Sicherung vorhanden** ist, dann **Updates installieren** (die angekreuzten Pakete;
   deaktiviert, mit einer Erklärung, solange `AllowUpdate` auf `disabled` steht; **Updates einschalten** /
   **Ausschalten** schreiben `settings/override/ezupdate.ini.append.php` über den INI-Editor des Kernels). Der Lauf
   findet im Hintergrund statt; die Seite folgt der Ausgabe. Danach erzeugen die Befehle in
   `[JobSettings] AfterRunCommands[]` die Autoloads neu und leeren die Caches für INI, Templates und Inhalte.
4. Wenn ein Lauf endet, zeigt die Seite oben einen Hinweis: *Die Installation ist auf dem neuesten Stand!
   Aktualisieren Sie bald wieder, um sicher zu bleiben.*, wenn nichts zu installieren, zu aktualisieren oder zu
   entfernen war (Composers "Nothing to modify in lock file" / "Nothing to install, update or remove" oder null
   veraltete Pakete nach *Nach Aktualisierungen suchen*), andernfalls eine kurze Zusammenfassung der Änderungen und,
   wenn der Lauf fehlschlug, den Exit-Code. Der Hinweis erscheint auch, während Sie den Lauf verfolgen.
5. Kann ein Lauf nicht verfolgt werden, nennt die Seite den Grund: abgemeldet (melden Sie sich wieder an und laden Sie
   neu; der Lauf geht auf dem Server weiter), mit HTTP 403 abgewiesen (die Richtlinie fehlt) oder ein Serverfehler
   (die Seite versucht es erneut, wartet jedes Mal länger und hört nach zehn Fehlern in Folge auf).

## Einstellungen

Alle Schlüssel stehen in `ezupdate.ini`; tragen Sie Ihre Werte in `settings/override/ezupdate.ini.append.php` ein.

| Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|
| `ComposerSettings` | `Path`, `Binary` | leer | Verzeichnis und Dateiname von Composer; leer probiert `SearchPath[]` mal `BinaryNames[]` |
| `ComposerSettings` | `SearchPath[]` | `var/ezupdate/`, `bin/`, `./`, `vendor/bin/`, dann `/usr/local/bin/`, `/usr/bin/`, `/opt/cpanel/composer/bin/` | Wo gesucht wird; relative Einträge liegen unterhalb des Installationsverzeichnisses und werden zuerst durchsucht, weil `open_basedir` (Plesk-Standard) die Systemordner verbirgt. Ein in `Path`/`Binary`/`PHPBinary` gesetzter Pfad wird unter `open_basedir` ungeprüft verwendet. *Composer holen* in der Übersicht lädt ein per SHA-256 geprüftes `composer.phar` nach `var/ezupdate/` |
| `ComposerSettings` | `BinaryNames[]` | `composer`, `composer.phar` | Zu probierende Namen |
| `ComposerSettings` | `PHPBinary` | leer | PHP-CLI, das ein `composer.phar` und die Hintergrundjobs ausführt; leer verwendet das php neben dem laufenden |
| `ComposerSettings` | `Timeout` | `900` | Sekunden, die ein Composer-Lauf dauern darf |
| `ComposerSettings` | `ComposerHome` | `var/ezupdate/composer` | `COMPOSER_HOME`, wenn der Webserver keines übergibt; darunter ein Ordner pro Systemkonto |
| `UpdateSettings` | `AllowUpdate` | `disabled` | Erlaubt `composer update` aus dem Admin |
| `UpdateSettings` | `AllowInstall` | `disabled` | Erlaubt `composer require` aus dem Admin |
| `UpdateSettings` | `PreferredInstall` | `auto` | `dist`, `source` oder `auto` (source für Entwicklungsversionen, dist für Releases) |
| `UpdateSettings` | `UpdateArguments[]`, `RequireArguments[]` | `--ansi`, `--no-progress` | Immer übergebene Argumente |
| `JobSettings` | `AfterRunCommands[]` | `bin/php/ezpgenerateautoloads.php --extension`, `bin/php/ezcache.php --clear-tag=ini,template,content` | Laufen nach einem erfolgreichen Update oder einer erfolgreichen Installation |
| `JobSettings` | `KeepJobs` | `20` | Aufbewahrte abgeschlossene Jobs (Log und Status) |
| `PackagistSettings` | `URL` | `https://packagist.org` | Durchsuchter Paketindex |
| `PackagistSettings` | `PerPage` | `25` | Suchergebnisse pro Seite |
| `PackagistSettings` | `CacheTime` | `900` | Sekunden, die eine Antwort wiederverwendet wird |
| `PackagistSettings` | `Types[]` | extension, kernel, library, any | Im Suchformular angebotene Pakettypen |
| `PackageServerSettings` | `Servers[<name>]` | keine | Im Admin hinzugefügte `.ezpkg`-Server werden hier als `https://`-URLs eingetragen; der Server aus `package.ini [RepositorySettings]` steht immer an erster Stelle |

## Kommandozeile

```bash
php extension/ezupdate/bin/php/ezupdate.php status
php extension/ezupdate/bin/php/ezupdate.php installed --issues
php extension/ezupdate/bin/php/ezupdate.php outdated
php extension/ezupdate/bin/php/ezupdate.php search seven
php extension/ezupdate/bin/php/ezupdate.php fund --direct --json
```

Die Befehle sind `status`, `installed [--issues] [--json]`, `outdated`, `search`, `show`,
`servers`, `server-add`, `server-remove`, `packagist on|off`, `packages`, `fetch`, `require`,
`update`, `jobs` und `fund [--direct] [--json]`. Läufe, die in der einen Oberfläche gestartet wurden, erscheinen auch in
der anderen.

## Sicherheit

Composer wird ohne Shell gestartet (`proc_open` mit einer Argumentliste), im Installationsverzeichnis, mit einem
Zeitlimit, einem eigenen `COMPOSER_HOME`, ohne Rückfragen und nur mit übergebenen Pipes (unter Velocity keiner der
Sockets des Servers). Serveradressen müssen mit `https://` beginnen; jedes Formular trägt das Formular-Token; die Ausgabe
von Composer wird maskiert, bevor ihre Farben zu HTML werden, sodass eine Paketbeschreibung mit Markup Text bleibt.
Nichts wird in statischen Variablen gehalten, sodass ein persistenter Worker immer die aktuellen Einstellungen liest, und
die Ansichten laden ihre Klassen selbst, wenn das Autoload-Array sie noch nicht kennt.

## Verhaltensänderungen

* 1.1.3 entfernte die Ansichten `dump` und `show`, das Commit-Template und den Link *Dump assets*.
  Sie gehörten zu `git_manager` und konnten hier nicht funktionieren (eine las `git_manager.ini`, eine rief
  eine Klasse `GitManager` auf, die diese Extension nicht hat, und das Template sendete an
  `git_manager/dashboard`). Der Link im Setup-Menü auf `git_manager/dump` fiel mit ihnen weg;
  `git_manager` bringt seinen eigenen mit.
* 1.1.12 zeigt nach jedem abgeschlossenen Lauf einen Ergebnishinweis (auf dem neuesten Stand, was sich geändert hat, fehlgeschlagen).
* 1.1.13 fügt „Updates installieren“ hinzu (Ankreuzfelder pro Paket, deaktiviert mit einer Erklärung, solange Updates ausgeschaltet sind, Einschalten / Ausschalten) und kennzeichnet Pakete danach, was Composer installieren würde.
* Die Lizenztexte liegen seit 1.1.8 in `LICENSE.md` und `doc/LICENSE.md`.

## Sprachen

Die Extension bringt Übersetzungsdateien in `translations/<locale>/translation.ts` mit: eng-US und ger-DE. Die deutsche
Datei enthält 280 Meldungen (zählen Sie `<message` in `translations/ger-DE/translation.ts`). Die Texte werden in den
Kontexten `extension/ezupdate`, `kernel/navigationpart` und `design/admin/parts/setup/menu` nachgeschlagen.

Aktualisieren Sie nach dem Bearbeiten einer Übersetzungsdatei den kompilierten Übersetzungs-Cache mit
`./console exp:ezgeneratetranslationcache` und leeren Sie die Template- und Inhalts-Caches.
`./console exp:ezchecktranslation ger-DE` gibt Statistiken zur `share/translations/ger-DE/translation.ts` des Kernels
aus, nicht zur Datei dieser Extension.

## Verwandte Seiten

- [git_manager](git_manager.md): Sicherungen vor dem Aktualisieren
- [Paketlizenzen und -versionen](../paketlizenzen-und-versionen.md)
- [Velocity-Engines](../../../bc/6.0/velocity-engines.md)
- [Abstraktionen für CLI, Cronjobs und Ansichten](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
- [Chronik](../../../geschichte/erweiterungen/ezupdate.md) und [Versionshinweise](../../../../changelogs/extensions/ezupdate.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezupdate.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2024-11](../../../geschichte/erweiterungen/monate/2024-11.md), [2026-06](../../../geschichte/erweiterungen/monate/2026-06.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
