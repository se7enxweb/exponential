# In der Administration gespeicherte Einstellungsdateien sind nicht mehr für alle beschreibbar

Lesen Sie diese Seite, wenn Einstellungen in der Administration geändert werden (Editor für Einstellungen,
Debug-Leiste, Setup > Extensions, der Setup-Assistent), mit `exp:ini` oder durch ein Skript, das `eZINI::save()`
aufruft, und besonders, wenn Velocity und PHP-FPM unter verschiedenen Benutzern laufen. Seit dem 5. Oktober 2026
(6.0.15) behält ein Speichern Modus, Besitzer und Gruppe der Datei, die es ersetzt, und gibt einer neuen Datei `0640`.
Bisher wurde jede gespeicherte Datei auf `0666` gesetzt.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../../hilfe/hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | `eZINI::save()` führt kein `chmod( $file, 0666 )` mehr aus. Eine vorhandene Datei behält Modus, Besitzer und Gruppe; eine neue erhält `0640` sowie Besitzer und Gruppe ihres Verzeichnisses. Neue Einstellungsverzeichnisse sind `0755`, nicht `0777`. INI-Cache-Dateien sind `0644`, nicht `0666`. |
| Wer betroffen ist | Alle. Jede Einstellungsdatei, die die Administration je gespeichert hat, ist auf der Festplatte `0666`, bis sie einmal korrigiert wird (siehe unten). |
| Wie Sie es prüfen | `find settings extension/*/settings -name '*.ini*' -perm -o+w` listet die für alle beschreibbaren auf; es darf nichts ausgeben. |
| Wie Sie es beheben | Einmal: `chmod 0640` für die Dateien, die der Befehl auflistet (Geheimnisse in `settings/override`), oder `0644`, wo andere Benutzer sie lesen müssen. Am Code ist nichts zu ändern. |

## Was falsch war

`eZINI` hatte einen Modus für zwei verschiedene Arten von Dateien: die Einstellungsdateien, die `save()` schreibt, und
die INI-Cache-Dateien in `var/cache/ini`. Beide wurden mit `chmod()` auf `EZP_INI_FILE_PERMISSION` gesetzt und, wenn
diese Konstante in `config.php` nicht definiert ist (der ausgelieferte Fall), auf `0666`. Ein einziges Speichern von
`settings/override/site.ini.append.php`, das das Datenbankpasswort enthält, machte sie so für jeden Benutzer der
Maschine lesbar **und beschreibbar**. Ein lokaler Benutzer oder jede andere Website auf einem gemeinsamen Host konnte
das Passwort lesen oder eine Einstellung hinzufügen (`[DebugSettings]`, eine Erweiterung, einen Datenbank-Host), der
die nächste Anfrage folgte.

Die Cache-Dateien waren ebenfalls `0666`. Es sind PHP-Dateien, die `eZINI` einbindet, und sie enthalten jede
zusammengeführte Einstellung, Passwörter eingeschlossen: Jeder lokale Benutzer konnte eine davon ändern und Code in der
Website ausführen lassen.

Einstellungsverzeichnisse, die `save()` anlegen musste, wurden mit `0777` angelegt (bei ausgeschalteter umask).

## Was jetzt geschieht

### Eine bereits vorhandene Einstellungsdatei

Sie behält ihren eigenen Modus, Besitzer und ihre Gruppe: Eine Datei, die Sie auf `0600` gesetzt haben, bleibt `0600`,
eine `0644`-Datei bleibt `0644`. Entfernt wird nur das Bit, mit dem andere Benutzer sie schreiben dürfen (`0666` wird
zu `0664`), ebenso setuid-, setgid- und Sticky-Bits; eine Datei, die ein früheres Speichern für alle beschreibbar
hinterlassen hat, wird beim nächsten Speichern korrigiert.

Der Besitzer wird nur dann auf den Besitzer der alten Datei zurückgesetzt, wenn das Speichern als root läuft (die
Worker von Velocity auf einem Host, auf dem sie als root laufen): root schreibt die neue Datei und übergibt sie dem
Benutzer der Website, sodass PHP-FPM sie weiterhin lesen und speichern kann. Ein Speichern durch einen anderen Benutzer
setzt die Gruppe, wenn dieser Benutzer das darf (er gehört der Gruppe an), und sonst gehört die Datei wie bisher am
Ende diesem Benutzer.

Die Sicherung, die das Speichern behält (`<file>~`), ist die vorherige, beiseitegeschobene Datei und behält daher den
vorherigen Modus.

### Eine neue Einstellungsdatei

- Modus `0640` (Besitzer lesen/schreiben, Gruppe lesen) oder `EZP_INI_SAVE_FILE_PERMISSION`, wenn `config.php` es
  definiert. Auch aus diesem Wert wird das Schreibrecht für andere Benutzer entfernt, sodass `0666` dort `0664`
  ergibt.
- Besitzer und Gruppe des Verzeichnisses, in dem sie angelegt wird. Gehört dieses Verzeichnis root (von einem
  root-Prozess angelegt, etwa `settings/siteaccess/<name>`, das Velocity angelegt hat), Besitzer und Gruppe des
  nächsten Verzeichnisses darüber innerhalb der Installation, das nicht root gehört, normalerweise `settings/` des
  Website-Benutzers. Ohne das wäre eine Datei, die root mit `0640` geschrieben hat, für PHP-FPM nicht lesbar, und ihre
  Einstellungen würden dort stillschweigend fehlen.
- Die temporäre Datei, die das Speichern zuerst schreibt, ist `0600`, bis sie ihren endgültigen Modus erhält, sodass
  die Einstellungen zwischendurch nie für andere lesbar sind.

Verzeichnisse, die `save()` anlegt, sind `0755`, mit derselben Regel für Besitzer und Gruppe.

### Dasselbe für expIniEditor

`expIniEditor` (hinter `exp:ini`, den Einstellungen der Debug-Leiste und den E-Mail-Einstellungen) behielt Modus,
Besitzer und Gruppe einer vorhandenen Datei bereits. Eine neue Datei, die er anlegt, erhält jetzt dasselbe `0640`
(oder `EZP_INI_SAVE_FILE_PERMISSION`) und dieselbe Regel für den Besitzer wie bei `eZINI::save()` statt `0644`, und
seine temporäre Datei ist `0600`, bis sie an ihren Platz verschoben wird.

### INI-Cache-Dateien

`var/cache/ini/*.php` verwenden weiterhin `EZP_INI_FILE_PERMISSION`, das jetzt nur noch für sie (und für erzeugte
Autoload-Dateien) gilt. Sein Standard ist `0644` statt `0666`. Eine Cache-Datei wird ersetzt, indem eine neue Datei
geschrieben und umbenannt wird, daher braucht ein zweiter Benutzer (Velocity als root, PHP-FPM als Benutzer der
Website) Schreibrecht auf das Verzeichnis, nicht auf die Datei; `0644` hält sie für jeden Benutzer lesbar. Setzen Sie
`EZP_INI_FILE_PERMISSION` in `config.php`, wenn Ihre Benutzer etwas anderes brauchen, zum Beispiel `0640`, wenn alle
die Gruppe der Datei teilen.

## Einstellungen

| Konstante (`config.php`) | Gilt für | Standard |
|---|---|---|
| `EZP_INI_SAVE_FILE_PERMISSION` | neue Einstellungsdateien, geschrieben von `eZINI::save()` und `expIniEditor` | `0640` |
| `EZP_INI_FILE_PERMISSION` | INI-Cache-Dateien, erzeugte Autoload-Dateien | `0644` (war `0666`) |

Beide sind in `config.php-RECOMMENDED` auskommentiert, mit diesen Standardwerten. Definiert Ihre `config.php`
`EZP_INI_FILE_PERMISSION` als `0666`, ändern Sie es auf `0644`: Es wirkt sich nicht mehr auf Einstellungsdateien aus,
macht aber die Cache-Dateien weiterhin für alle beschreibbar.

Beide lassen sich von der Konsole aus lesen und setzen; `exp:config:set` zeigt die Änderung zuerst und schreibt sie
erst mit `--apply`, nach einer Sicherung:

```bash
./console exp:config:get EZP_INI_FILE_PERMISSION
./console exp:config:set EZP_INI_FILE_PERMISSION 0644 --apply
./console exp:config:set EZP_INI_SAVE_FILE_PERMISSION 0640 --apply
```

Siehe [.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md).

## Eine Installation einmal korrigieren

```bash
# list world-writable settings files and directories
find settings extension/*/settings -perm -o+w \( -type f -o -type d \)
# secrets: owner and group only
chmod 0640 settings/override/*.ini.append.php
# cache files: regenerated with the new mode
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Prüfen Sie, dass der Benutzer von PHP-FPM jede geänderte Datei weiterhin lesen kann
(`sudo -u <site user> cat <file> > /dev/null`), laden Sie dann PHP-FPM neu und starten Sie Velocity neu, sodass
beide den neuen Code verwenden.

## Code

- `lib/ezutils/classes/ezini.php`: `eZINI::SAVE_FILE_PERMISSION`, `eZINI::SAVE_DIRECTORY_PERMISSION`,
  `eZINI::newSaveFileMode()`, `eZINI::saveFileMode()`, `eZINI::saveFileOwnership()`; `save()` verwendet sie.
- `kernel/classes/ini/expinieditor.php`: `writeFile()` verwendet sie für neue Dateien.
- Tests: `eZINISaveFilePermissionsTest` (Suite `lib`): Eine neue Datei ist `0640`; Dateien mit `0600`, `0640`, `0644`
  und `0664` behalten ihren Modus, `0666` wird zu `0664`; Besitzer und Gruppe bleiben als root erhalten; eine neue
  Datei in einem Verzeichnis, das root gehört, erhält den Besitzer des Verzeichnisses darüber; neue Verzeichnisse
  `0755`; `EZP_INI_FILE_PERMISSION` erreicht ein Speichern nicht mehr; ein konfiguriertes
  `EZP_INI_SAVE_FILE_PERMISSION` wird beachtet, ist aber nie für alle beschreibbar. `expIniEditorTest` INI-06 prüft den
  Modus einer neuen Datei.

## Verwandte Seiten

- [Installationsanleitung, 13.11 Dateirechte und Besitz](../../installation/13-sicherheitshaertung.md#1311-dateiberechtigungen-und-eigentümer)
- [eZINI behält Kommentare beim Speichern](ezini-behaelt-kommentare.md)
- [exp:ini](console-exp-ini.md)
- [.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md): `exp:config:*` für die Konstanten in `config.php`
