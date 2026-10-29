# Konsolenbefehle für .htaccess und config.php

Für Administratoren, Hoster und alle, die Installationen per Skript aufsetzen. Jede Installation bringt in ihrem
Wurzelverzeichnis drei Vorlagen mit: `.htaccess_root` und `.htaccess_root_static` (die Regeln für Apache und
LiteSpeed, die zweite mit der Auslieferung des statischen Caches) und `config.php-RECOMMENDED` (die beschriebenen
Einstellungen der `config.php`). Sieben Konsolenbefehle bringen sie an ihren Platz und lesen und ändern `config.php`,
so dass niemand sie von Hand kopieren oder PHP auf einem Server im Editor bearbeiten muss:

| Befehl | Wirkung |
|---|---|
| `./console exp:setup:status` | Sagt, von welcher Vorlage `.htaccess` eine Kopie ist und ob `config.php` eine einfache Kopie ist, Einstellungen setzt oder sich nicht kompilieren lässt |
| `./console exp:setup:htaccess [--static] [--force] --apply` | Kopiert `.htaccess_root` (mit `--static`: `.htaccess_root_static`) nach `.htaccess` |
| `./console exp:setup:config [--force] --apply` | Kopiert `config.php-RECOMMENDED` nach `config.php` |
| `./console exp:config:list [--only-set] [--format=json]` | Jede beschriebene Einstellung mit dem Wert in `config.php`, dem Vorschlag und ihrer Beschreibung |
| `./console exp:config:get <NAME>` | Ein Wert, schlicht, für ein Skript (Exit-Code 1, wenn er nicht gesetzt ist) |
| `./console exp:config:set <NAME> <wert> [--type=<t>] --apply` | Setzt einen Wert an Ort und Stelle, entfernt den Kommentar vor seiner beschriebenen Zeile oder fügt ihn hinzu |
| `./console exp:config:unset <NAME> --apply` | Kommentiert die Einstellung wieder aus |

## Was sie sicher macht

- **Erst der Probelauf.** Ohne `--apply` zeigt jeder Befehl, was er ändern würde; `--apply` zusammen mit `--dry-run`
  ist ein Bedienfehler (Exit-Code 2).
- **Kein stilles Überschreiben.** Eine vorhandene `.htaccess` oder `config.php`, die von der Vorlage abweicht, wird als
  Diff gezeigt und ohne `--force` behalten.
- **Eine Sicherung vor jedem Schreiben**, nach `var/backup/setup/<zeit>/` oder `var/backup/config/<zeit>/`, mit Modus
  und Eigentümer der Datei, in Verzeichnissen mit Modus `0700`.
- **Atomares Schreiben** über eine temporäre Datei im selben Verzeichnis, die an ihren Platz umbenannt wird.
- **Eigentümer und Modi wie bei `cp -a`.** Eine neue Datei bekommt den Modus der Vorlage und, als root, ihren
  Eigentümer (nie root); eine vorhandene behält ihre eigenen. `--mode` und `--owner=benutzer[:gruppe]` setzen sie;
  `-v` gibt jede Kopie aus wie `cp -v`.
- **`config.php` wird geparst, nicht durchsucht.** Sie wird als PHP-Tokens gelesen und nie ausgeführt; nur der Wert
  eines `define( 'NAME', wert )` ändert sich, Kommentare, Reihenfolge und Formatierung bleiben. Ein `define()` in einer
  Bedingung wird gemeldet und nicht angefasst.
- **Geprüft, bevor sie landet.** Eine neue `config.php` muss `php -l` bestehen und den verlangten Wert zurücklesen
  lassen. Eine `config.php`, die sich nicht kompilieren lässt, wird abgelehnt, und `./console` startet trotzdem, um
  sie zu reparieren.
- **Typisierte Werte.** `bool`, `int`, `octal` (Modi wie `0640`), `float`, `string`, `null`, aus `--type`, dem
  aktuellen oder dem beschriebenen Wert.
- **Geheimnisse maskiert.** Werte von Namen mit `PASS`, `SECRET`, `KEY`, `TOKEN`, `CREDENTIAL` oder `PWD` erscheinen
  in jeder Ausgabe und jedem Diff als `********`, außer mit `--show-secrets`; wer eines in eine für alle lesbare
  `config.php` schreibt, bekommt eine Warnung.
- **Nur bekannte Namen**, die `config.php-RECOMMENDED` beschreibt, außer mit `--allow-unknown`, so wird ein Tippfehler
  abgefangen statt geschrieben.

## Beispiel

```bash
./console exp:setup:htaccess --apply -v
./console exp:setup:config --apply
./console exp:config:set EZP_INI_FILEMTIME_CHECK false --apply
./console exp:config:set EZP_FILE_MODE_MAX 0640 --apply
./console exp:config:list --only-set
```

```text
'.htaccess_root' -> '.htaccess'
PASS: .htaccess_root copied to .htaccess (mode 0644, owner www-data:www-data)
...
config.php: 2 setting(s) set, 30 documented in config.php-RECOMMENDED

INI FILES OPTIMIZATIONS
  EZP_INI_FILEMTIME_CHECK              false (line 57)
      RECOMMENDED false - This new setting controls whether eZINI should check if the settings have been updated...

FILE PERMISSIONS
  EZP_FILE_MODE_MAX                    0640 (line 112)
      RECOMMENDED 0640 - Set EZP_DIR_MODE_MAX and EZP_FILE_MODE_MAX to the widest modes any directory and any fi...
```

Der ganze Durchgang, mit jeder Option, der Ausgabe jedes Falls, den Regeln für Sicherung und `--force`, Skripten,
Containern und Fehlerbehebung: **[.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)**.

## Für Entwickler

- Code: `kernel/classes/expsetupfilecopy.php` (Kopie, Sicherung, atomares Schreiben, `php -l`),
  `kernel/classes/expconfigphpeditor.php` (der tokenbasierte `define()`-Editor), `kernel/classes/expconfigphpcatalogue.php`
  (die in `config.php-RECOMMENDED` beschriebenen Einstellungen), die Befehle `kernel/private/classes/commands/setupfiles.php`
  und `configphp.php`, die Einstiegspunkte `bin/php/setupfiles.php` und `bin/php/configphp.php`.
- Eine `config.php`, die sich nicht kompilieren lässt, würde `autoload.php` anhalten. `bin/php/console`,
  `bin/php/setupfiles.php` und `bin/php/configphp.php` definieren vorher `EXP_CONFIG_PHP_TOLERATE`; nur für sie prüft
  `autoload.php` die Datei mit dem PHP-Parser und liest sie nicht, wenn sie sich nicht kompilieren lässt
  (`EXP_CONFIG_PHP_SKIP` enthält den Fehler). Jeder andere Einstiegspunkt liest `config.php` wie bisher.
- Eine neue Einstellung wird den Befehlen bekannt, indem sie in `config.php-RECOMMENDED` wie die anderen beschrieben
  wird: ein Kommentarblock (eine mit Strichen unterstrichene Überschrift beginnt einen Abschnitt), gefolgt von
  `//define( 'NAME', wert );`.
- Tests: `tests/tests/kernel/private/classes/commands/expSetupFilesAndConfigPhpCommandsTest.php` (ohne Datenbank).
- Erzeugte Referenz: [exp:setupfiles](../../beispiele/befehle/setupfiles.md), [exp:configphp](../../beispiele/befehle/configphp.md).

## Verwandte Seiten

- [Installation: den Code beschaffen](../../installation/03-den-code-beschaffen.md), [Die Website ausliefern](../../installation/08-die-website-ausliefern.md)
- [Einstellungen von der Befehlszeile ändern: exp:ini](exp-ini-befehl.md), für die INI-Dateien in `settings/`
- [Der Generator für den statischen Cache](generator-fuer-den-statischen-cache.md), den `.htaccess_root_static` ausliefert
- [Dateimodi](../../bc/6.0/dateimodi.md)
