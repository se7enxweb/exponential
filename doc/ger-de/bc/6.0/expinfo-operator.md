# Die Klasse expInfo und der Operator expinfo

Lesen Sie diese Seite, wenn Sie Metadaten der Installation oder von Erweiterungen aus PHP oder aus einem Template
lesen möchten oder wenn Sie ein Override der Ansicht Setup > Extensions pflegen. 6.0.15 fügt `expInfo` hinzu, eine
nur lesende PHP-Klasse, und den passenden Template-Operator `expinfo`. Am Verhalten von Vorhandenem ändert sich
nichts; die Ansicht Setup > Extensions verwendet die neue Klasse jetzt intern.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neue Klasse `expInfo` (`lib/ezutils/classes/expinfo.php`) und Template-Operator `expinfo`. |
| Wer betroffen ist | Autoren von Templates und Erweiterungen, die die Daten möchten; Overrides von Setup > Extensions. |
| Wie Sie es prüfen | Setzen Sie `{expinfo( 'no_such_ext' )\|attribute( show )}` in ein Test-Template; es zeigt `false`. |
| Wie Sie es beheben | Nichts zu beheben. Erzeugen Sie nach dem Bereitstellen die Autoloads neu, leeren Sie die Caches und laden Sie PHP-FPM neu (siehe „Bereitstellen“). |

## PHP-API

`lib/ezutils/classes/expinfo.php` deklariert `expInfo` mit diesen statischen Methoden:

| Methode | Gibt zurück |
|---|---|
| `expInfo::activeExtensions()` | Metadaten aller aktiven Erweiterungen, mit dem Verzeichnisnamen der Erweiterung als Schlüssel. |
| `expInfo::availableExtensions()` | Metadaten jeder auf der Festplatte gefundenen Erweiterung, jeweils mit einem booleschen Wert `active`. |
| `expInfo::extensionInfo( $name, $activeOnly = false )` | Metadaten einer Erweiterung, oder `false`, wenn sie nicht gefunden wird (oder, mit `$activeOnly`, nicht aktiv ist). |
| `expInfo::hasActiveExtension( $name )` | `true` oder `false`. |
| `expInfo::activeNames()` | Die Liste der Namen aktiver Erweiterungen. |
| `expInfo::kernelInfo( $section = false )` | Ein großes, nur lesbares Array, das die Installation beschreibt. Mit einem Abschnittsnamen (siehe die Tabelle unten) nur diesen Abschnitt. Einmal pro Anfrage gebaut und im Speicher gehalten. |

Das Metadaten-Array einer Erweiterung enthält die Schlüssel `Name`, `Version`, `Copyright`, `License`, `info_url`,
`description`, `author`, `summary`, `meta`, `mtime` und `active` und weitere: Die genaue Liste ist das, was die
`ezinfo.php` oder `extension.xml` der Erweiterung liefert, normalisiert.

## Template-Operator

`lib/eztemplate/classes/eztemplateexpinfooperator.php` registriert `expinfo`:

```tpl
{expinfo()}                     {* active extensions array *}
{expinfo('all')}                {* all available extensions array *}
{expinfo('bcwebshop')}          {* single extension info array *}
{expinfo('kernel')}             {* the whole kernel info array; a single section is a PHP call, see above *}
```

## Abschnitte der Kernel-Informationen

`expinfo('kernel')` und `expInfo::kernelInfo()` geben diese dreizehn Abschnitte der obersten Ebene zurück:

| Abschnitt | Inhalt |
|---|---|
| `version` | Version, Zustand, Alias und Edition von `ExponentialSDK` (`lib/version.php`) |
| `php` | PHP-Version, SAPI, geladene Erweiterungen, INI-Limits |
| `memory` | Aktueller und höchster Speicherverbrauch |
| `server` | Hostname, Pfade von kernel/var/cache/www, Serversoftware |
| `database` | Datenbanktyp, Version, Name, Server, Benutzer (Passwort geschwärzt) |
| `ini` | Name der Website, Siteaccess, Designs, Sprachen, Datenbankeinstellungen |
| `user` | ID und Anmeldename des aktuellen Benutzers, sofern verfügbar |
| `extensions` | Anzahl und Namenslisten aktiver und verfügbarer Erweiterungen |
| `cache` | Größe des Cache-Verzeichnisses und Zahl der kompilierten Templates |
| `filesystem` | Größen des Wurzel-, des var- und des Erweiterungsverzeichnisses; freier und gesamter Plattenplatz |
| `timestamps` | mtime von `lib/version.php`, `index.php`, `composer.lock` und anderen |
| `git` | Branch, Hash und Datum des letzten Commits, Zahl geänderter Dateien |
| `composer` | Zahl und Versionen der installierten Composer-Pakete |

## Sicherheit

- Datenbank- und INI-Passwörter werden immer als `***` zurückgegeben.
- Es werden keine Geheimnisse, privaten Schlüssel oder Zugangsdaten preisgegeben.

## Selbst prüfen

- In einem Template: Setzen Sie `{expinfo( 'no_such_ext' )|attribute( show )}` in ein Test-Template und rufen Sie es
  auf. Das Ergebnis ist `false`.
- Aus PHP: Rufen Sie die Klasse aus einem Skript auf, das mit
  `php bin/php/ezexec.php <script> --allow-root-user` ausgeführt wird. `expInfo::kernelInfo()` gibt genau die
  dreizehn Abschnitte oben zurück, und `expInfo::extensionInfo( 'no_such_ext' )` gibt `false` zurück.

## Setup > Extensions

Die Ansicht Setup > Extensions (Einstiegspunkt `kernel/setup/extensions.php`, Code in
`kernel/private/classes/views/setup/extensions.php`) sammelt die Metadaten der Erweiterungen jetzt mit
`expInfo::availableExtensions()` statt mit einer eingebetteten Hilfsfunktion. Ein Override dieser Ansicht kann
dieselbe Methode aufrufen.

## Bereitstellen

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

Laden Sie dann das PHP-FPM neu, das die Website ausliefert, damit die Web-Laufzeit die neue Klasse übernimmt.

## Verwandte Seiten

- [Spezifikation der Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md)
- [Liste der Erweiterungen und Downloads](../../../eng-us/features/6.0/extension-list-and-downloads.md)
- [RAD-Werkzeuge](../../../eng-us/features/6.0/rad-tools.md)
- [ExponentialSDK und exponential.cron](exponentialsdk-und-exponential-cron.md)
- [September 2026, erste Hälfte](../../../eng-us/history/2026/2026-09a.md#1-to-2-september-knowing-what-is-installed)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
