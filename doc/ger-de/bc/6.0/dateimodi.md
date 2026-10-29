# Grenzen für die Modi neuer Dateien und Verzeichnisse (6.0.15)

Lesen Sie diese Seite, wenn Sie Exponential auf einem Server betreiben, auf dem andere Konten nicht lesen oder ändern
dürfen, was die Website schreibt (ein Shared Host, ein gehärteter Live-Server), oder wenn Sie Code schreiben, der
Dateien oder Verzeichnisse anlegt.

## Kurz gesagt

- Zwei Konstanten in `config.php` legen den weitesten Modus fest, den eine von der Installation angelegte Datei und
  ein angelegtes Verzeichnis erhalten dürfen: `define( 'EZP_DIR_MODE_MAX', 0750 );` und
  `define( 'EZP_FILE_MODE_MAX', 0640 );`.
- Ein Modus, den der Code verlangt, wird durch sie nur eingeengt, nie erweitert: Aus 0777 wird 0750, ein bewusstes
  0600 bleibt 0600.
- Sie gelten für Logs, Caches (INI-Cache, PHP-Caches, Autoload-Arrays, statischer Cache), den Speicher (Uploads,
  Bildvarianten, die Datei-Handler des Clusters) und alles, was ohne eigenen Modus geschrieben wird: Die umask des
  Prozesses wird beim Start aus ihnen gesetzt. Jede Stelle im Kernel, in den Bibliotheken, den Skripten und den
  Erweiterungen des Repositorys, die einer Datei oder einem Verzeichnis einen Modus gibt, geht über sie; ein
  Sicherheitstest sorgt dafür, dass es so bleibt.
- Ohne die Konstanten ändert sich nichts. Niemand muss handeln; Live-Server sollten sie setzen.

## Warum

Die Standardwerte in `site.ini [FileSettings]` sind offen: `StorageDirPermissions=0777`,
`StorageFilePermissions=0666`, `LogFilePermissions=0666`, `TemporaryPermissions=0777`. Das Verzeichnis des INI-Caches
wurde unabhängig davon mit 0777 angelegt, und mehrere Klassen setzen vor dem Schreiben `umask( 0 )`, sodass auch die
umask des Servers nichts einengte. Der INI-Cache und die Autoload-Arrays sind PHP, das bei jeder Anfrage eingebunden
wird: In einem Verzeichnis, in das jedes Konto schreiben darf, kann ein anderes Konto sie ersetzen, und das heißt,
Code als die Website auszuführen. Die vier Einstellungen zu senken deckte die Stellen nicht ab, die einen festen oder
gar keinen Modus verwenden.

## Die Konstanten

```php
// config.php
define( 'EZP_DIR_MODE_MAX', 0750 );   // directories: owner rwx, group r-x, others nothing
define( 'EZP_FILE_MODE_MAX', 0640 );  // files: owner rw, group r, others nothing
```

Von der Konsole aus, ohne Editor (jeder Befehl zeigt die Änderung zuerst und schreibt erst mit `--apply`, nach einer
Sicherung nach `var/backup/config/<Zeit>/` und einer Prüfung mit `php -l`):

```bash
./console exp:setup:config --apply                     # once, when there is no config.php yet
./console exp:config:set EZP_DIR_MODE_MAX 0750 --apply
./console exp:config:set EZP_FILE_MODE_MAX 0640 --apply
./console exp:config:get EZP_FILE_MODE_MAX             # 0640
```

`exp:config:set` schreibt den Wert als oktalen Modus (`750` und `0o750` werden angenommen und als `0750` geschrieben),
und zwar in die Zeile, die `config.php-RECOMMENDED` dokumentiert. Siehe
[.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md).

- Schreiben Sie sie als Oktalzahlen mit führender 0 (`0750`) oder als Zeichenketten (`'0750'`, `'750'`). Eine Ganzzahl
  über 0777 ohne die 0 (`750`) wird ebenfalls als 0750 gelesen, eine kleinere lässt sich aber nicht von einer Oktalzahl
  unterscheiden: `440` ist 0670. Ein Wert, der kein Modus ist (`'rwxr-x---'`, 0x1ff0, eine Gleitkommazahl, ein Array),
  oder eine Grenze, die dem Besitzer Rechte nähme (alles ohne 0600 für Dateien oder 0700 für Verzeichnisse: `0` würde
  jede neue Datei zu 0000 machen), wird ignoriert: Die Website verhält sich wie ohne diese Konstante, und das
  PHP-Fehlerprotokoll meldet es einmal pro Prozess. Prüfen Sie nach dem Setzen das Fehlerprotokoll.
- Eine Konstante genügt: `EZP_FILE_MODE_MAX=0640` allein gibt Verzeichnissen 0750 (das Such-Bit überall, wo Lesen
  erlaubt ist), `EZP_DIR_MODE_MAX=0750` allein gibt Dateien 0640.
- Setzen Sie sie als Paar, dessen Dateigrenze die Verzeichnisgrenze ohne die Such-Bits ist (0750/0640, 0770/0660,
  0700/0600). Bei einem anderen Paar lässt die umask nur durch, was beide erlauben: 0750/0600 gibt Dateien 0600 und
  Verzeichnissen 0710 (Suchen, kein Auflisten für die Gruppe), auch dort, wo der Code 0777 verlangt.
- Es sind Konstanten und keine Einstellungen, weil sie gelten müssen, bevor die INI-Dateien gelesen werden (der
  INI-Cache, die Autoload-Arrays, frühe Log-Zeilen), und weil eine Sicherheitsgrenze zur Konfiguration des Servers
  gehört, nicht zu einer Einstellung, die ein Administrator in der Administrationsoberfläche ändern kann. Definieren
  Sie sie weit oben in `config.php`, vor dem frühen Ausstieg des HTTP-Caches, den sie am Ende einbindet und der
  schreibt, bevor der Autoloader läuft.
- Wo der Webserver und die Kommandozeilenskripte (Cronjobs, `bin/php/*`) unter verschiedenen Benutzern laufen,
  brauchen sie eine gemeinsame Gruppe mit Schreibzugriff: Verwenden Sie `0770` / `0660` und lassen Sie die
  Verzeichnisse von `var/` dieser Gruppe gehören, mit dem Set-Group-ID-Bit
  (`chgrp -R <group> var; find var -type d -exec chmod g+s {} +`), sodass alles, was einer der Benutzer anlegt, diese
  Gruppe erhält. Die Benutzer in die Gruppe aufzunehmen genügt nicht: Eine Datei erhält die primäre Gruppe des
  Benutzers, der sie anlegt, und die von root (Velocity als root gestartet, ein Cronjob als root) ist `root`. Ohne das
  Set-Group-ID-Bit ist, was root unter `0770` / `0660` anlegt, `root:root`, und der Webserver kann es weder lesen noch
  hineinschreiben. Darin angelegte Verzeichnisse behalten das Bit (`mkdir()` erbt es). Die umask jedes Prozesses engt
  weiterhin Dateien ein, die ohne eigenen Modus geschrieben werden (`eZFile::create()`, ein einfaches
  `file_put_contents()`): Unter dem üblichen `0022` erhalten sie `0640`, sodass der andere Benutzer sie lesen, aber
  nicht ergänzen kann, genau wie ohne die Grenzen (`0644`); starten Sie beide mit `umask 0007`, wenn sie das müssen.
  Mit einem Benutzer für beides ist `0700` / `0600` die engste Wahl. Einige Dateien werden von beiden geschrieben, wer
  zuerst kommt, legt sie an: die Sperre und das Laufprotokoll des Benachrichtigungsdienstes, das SQL-Profil der
  Cache-Verwaltung, der Wartungszustand, der Speicher der Inhaltsjobs, der Abfrage-Cache. Sie wurden deshalb früher für
  alle beschreibbar angelegt (0666); mit `0750` / `0640` und zwei Benutzern kann der zweite sie nicht mehr schreiben
  (ein Lauf der Benachrichtigungen findet zum Beispiel die Sperre belegt). Betreiben Sie beides als einen Benutzer oder
  als eine Gruppe mit `0770` / `0660`.
- Die Berechtigungen aus `site.ini [FileSettings]` gelten unterhalb der Grenzen weiter: `StorageFilePermissions=0666`
  mit `EZP_FILE_MODE_MAX=0640` ergibt 0640.

Vorhandene Dateien und Verzeichnisse behalten ihren Modus. Leeren Sie nach dem Setzen der Konstanten die Caches (sie
werden neu geschrieben) und engen Sie den Rest einmal ein:

```bash
find var -type d -exec chmod 0750 {} +
find var -type f -exec chmod 0640 {} +
```

## Wie es funktioniert

| Hilfsfunktion | Tut |
|---|---|
| `eZFile::fileMode( $mode )` | `$mode`, begrenzt auf `EZP_FILE_MODE_MAX` |
| `eZDir::dirMode( $mode )` | `$mode`, begrenzt auf `EZP_DIR_MODE_MAX` |
| `eZFile::executableMode( $mode )` | `$mode` einer Datei, die ausführbar bleiben muss (eine heruntergeladene Binärdatei), begrenzt durch `EZP_DIR_MODE_MAX`, das die erlaubten Ausführungs-Bits behält (0755 mit 0750 ergibt 0750) |
| `eZFile::creationUmask()` | Die umask, mit der statt `umask( 0 )` geschrieben wird: 0 ohne Grenzen (wie bisher), sonst jedes Bit, das die Grenzen nicht beide erlauben (0027 für 0750/0640, 0067 für 0750/0600) |
| `eZFile::applyCreationUmask()` | Wird von `autoload.php` nach `config.php` aufgerufen, wenn eine Grenze gesetzt ist: fügt diese Bits zur umask des Prozesses hinzu, sodass auch `fopen()`, `file_put_contents()` und `touch()` innerhalb der Grenzen bleiben. Unter Velocity gilt das für den ganzen Worker |

`eZDir::mkdir()` (und die Elternverzeichnisse eines rekursiven Aufrufs), `eZDir::directoryPermission()`,
`eZFile::create()`, `eZLog`, `eZDebug`, `eZINI` (Cache-Verzeichnis, Cache-Dateien, gespeicherte Einstellungsdateien),
`eZPHPCreator`, `eZFileHandler` und der Autoload-Generator verwenden sie.

Uploads (`eZHTTPFile`), Bildvarianten (`eZImageHandler`, `image.ini ImagePermissions`, leer: `site.ini
StorageFilePermissions`), die Cluster-Handler für
Dateisystem und DFS und der statische Cache setzen die Modi aus `site.ini [FileSettings]` und `image.ini` ebenfalls über
`eZFile::fileMode()`. Sind beide Bildmodi leer oder ist ein Wert kein oktaler Modus, bekommt eine Variante das
ausgelieferte 0666 (begrenzt wie jeder andere Modus), nie den Modus 0.

Der Autoload-Generator legt `var/autoload` mit `EZP_INI_FILE_PERMISSION` an, sofern definiert, einem Dateimodus
(0644), der das Verzeichnis ohne Such-Bit lässt, und seine Dateien mit 0777. Ohne die Grenzen bleibt das genau wie
bisher. Mit ihnen erhält das Verzeichnis einen Verzeichnismodus (0777 unter der umask des Servers, innerhalb von
`EZP_DIR_MODE_MAX`) und die Dateien `EZP_INI_FILE_PERMISSION` oder 0777 innerhalb von `EZP_FILE_MODE_MAX`, was die
Ausführungs-Bits entfernt.

Die umask gehört zum Prozess. Ein Server, der Anfragen in Threads eines Prozesses ausführt (FrankenPHP in seinem
Thread-Modus), teilt sie zwischen ihnen; die Stellen, die sie kurz ändern, stellen sie wieder her, und was sie setzen,
erlaubt nie mehr als die Grenzen. Unter Velocity wird die umask einmal im Serverprozess gesetzt, wenn er
`autoload.php` lädt, und jeder Worker erbt sie; der Kernel läuft dort aus `dist/engine.phar`, bauen Sie es daher nach
einem Update neu.

Unter Windows entscheiden die Modi nur, ob eine Datei schreibgeschützt ist; eine Grenze lässt dem Besitzer immer den
Schreibzugriff, daher ändert sich dort nichts.

## Für Code von Erweiterungen und Kernel

Geben Sie einer Datei oder einem Verzeichnis den Modus über die Hilfsfunktionen:

```php
eZDir::mkdir( $dir, 0770, true );                       // limited inside eZDir
chmod( $file, eZFile::fileMode( 0660 ) );
mkdir( $dir, eZDir::dirMode( 0770 ), true );
$old = umask( eZFile::creationUmask() );                 // not umask( 0 )
...
umask( $old );
```

Der Sicherheitstest `expFileModeLimitsTest` prüft das für den Kernel, die Bibliotheken, die Skripte und die
Erweiterungen des Repositorys: Ein natives `chmod()`, `mkdir()` oder `umask()` (auch als `\chmod()` geschrieben) zählt
nur, wenn sein Modus-Argument ein Aufruf einer Hilfsfunktion ist und nichts anderes (`eZFile::fileMode( 0640 ) | 0777`
ist es nicht), und `umask( $old... )` nur, wenn es eine zuvor gesicherte umask wiederherstellt. Ein Aufruf über einen
variablen Funktionsnamen oder `call_user_func()` wird nicht erkannt; schreiben Sie keinen.

Alle anderen Stellen verwenden sie ebenfalls: das Audit-Protokoll (`expAuditWriter::ownLikeParent()` begrenzt ein
Verzeichnis als Verzeichnis und eine Datei als Datei), Inhaltsjobs, Velocity und FrankenPHP (die Binärdatei über
`executableMode()`), der INI-Editor und -Verschieber, die E-Mail-Einstellungen, der Setup-Assistent und das
Installationsprogramm, der HTTP-Cache, die Debug-Leiste, der Zustand von Wartung und Benachrichtigungen, Pakete, in der
Administration geschriebene Templates, Shop-Quittungen, der Abfrage-Cache und das Verzeichnis der SQLite-Datenbank.
Enge umasks, die sie absichtlich setzen (`umask( 0077 )` um ein Geheimnis herum), bleiben mindestens so eng:
`umask( eZFile::creationUmask( 0077 ) )`.

## Tests

`expFileModeLimitsTest` (Sicherheitssuite, ohne Datenbank): das Lesen einer Grenze; ohne Grenzen ändert sich nichts;
mit 0750/0640 bleibt jedes angelegte Verzeichnis, jede Datei, jedes Log, jede INI-Cache-Datei und jede PHP-Cache-Datei
innerhalb von ihnen; eine Konstante ergibt die andere; eine vertippte oder eine, die dem Besitzer Rechte nähme, wird
ignoriert und gemeldet; ohne Grenzen erhalten die Autoload-Arrays und Bildvarianten genau die Modi wie bisher; kein
Code umgeht die Hilfsfunktionen.

Der Aufbau mit gemischten Benutzern (Velocity als root, PHP-FPM als Benutzer der Website) wurde in einer Sandbox
geprüft: Jeder Benutzer legte Verzeichnisse, Dateien und Logs über `eZDir::mkdir()`, `eZFile::create()`,
`eZLog::write()` und ein einfaches `file_put_contents()` an, und der andere las sie, ergänzte sie, schrieb hinein und
ersetzte sie: Mit `0770` / `0660` und Set-Group-ID-Verzeichnissen ist das Ergebnis dasselbe wie ohne Grenzen; ohne das
Set-Group-ID-Bit oder mit `0750` / `0640` kann der Benutzer der Website nicht mehr in das schreiben, was root angelegt
hat.

## Verwandte Seiten

- [Anleitung zu Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)
- [Über die Administration gespeicherte Einstellungsdateien](dateirechte-beim-speichern-von-ini-dateien.md)
- [.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md): `exp:config:set` für diese Konstanten
