# Den Request Shield einrichten

Dieser Leitfaden installiert den Request Shield (`exp_request_shield`) auf einer bestehenden Exponential-Website,
gibt der Website eigene Regeln, prüft und testet sie, erklärt, wann Sie beobachten und wann Sie durchsetzen, wie Sie
den Shield pausieren, seine Bibliothek aktualisieren und ihn wieder entfernen. Er richtet sich an Administratoren und
Hosting-Personal mit Shell-Zugang zur Installation.

[Anleitungen](README.md) · Referenz der Funktion: [Der Request Shield](../funktionen/6.0/request-shield.md) ·
Hinweise zur Aktualisierung: [Aktualisierung auf 6.0.15: der Request Shield](../bc/6.0/request-shield.md) · In der
Hilfe: [Request Shield: hier beginnen](../hilfe/request-shield/README.md)

## Kurz gesagt

- Installation: `composer require se7enxweb/exp_request_shield`, `ActiveExtensions[]=exp_request_shield`, dann die
  Hook-Zeile in `config.php` oberhalb des frühen Ausstiegs des HTTP-Caches. Unter Velocity starten Sie diesen neu.
- Geben Sie der Website eine eigene Regeldatei in `settings/request-shield/`, mit Kopien der drei
  `exponential*.rules` daneben, ihren eigenen Hostnamen und `www.example.org`.
- Nach jeder Änderung: `bin/request-shield check` und `test`. Um zu sehen, warum eine Anfrage so entschieden wurde:
  `trace`.
- Der Shield setzt nach der Installation durch. Beobachten Sie in den ersten Tagen **Setup > Request Shield > Protokoll**;
  schalten Sie auf monitor, während Sie etwas untersuchen; fügen Sie Ausnahmen mit je einem `expect`-Beispiel hinzu.
- Sofort aus: `var/request-shield/PAUSED` oder **Pausieren** in der Administration.

## Inhalt

1. [Auf einer bestehenden Website installieren](#1-auf-einer-bestehenden-website-installieren)
2. [Der Website eigene Regeln geben](#2-der-website-eigene-regeln-geben)
3. [Prüfen, testen und verfolgen](#3-prüfen-testen-und-verfolgen)
4. [Beobachten oder durchsetzen](#4-beobachten-oder-durchsetzen)
5. [Eine Ausnahme hinzufügen](#5-eine-ausnahme-hinzufügen)
6. [Pausieren](#6-pausieren)
7. [Die Bibliothek aktualisieren](#7-die-bibliothek-aktualisieren)
8. [Deinstallieren](#8-deinstallieren)
9. [Verwandte Seiten](#9-verwandte-seiten)

## 1. Auf einer bestehenden Website installieren

Voraussetzungen: Exponential 6.0 und PHP 7.4 bis 8.5. Unter Exponential Velocity braucht die Website das `index.php`
der Linie 6.0.15, das die in `EXP_REQUEST_PREPEND_FILE` genannte Datei ausführt; mit einem älteren `index.php` schützt
der Shield nur die Anfragen über PHP-FPM. APCu ist optional.

1. Holen Sie das Paket, im Wurzelverzeichnis der Installation:

   ```bash
   composer require se7enxweb/exp_request_shield
   ```

   Der Legacy-Installer legt es in `extension/exp_request_shield/` ab. Auf einer Installation aus
   `se7enxweb/exponential` `main` ist es schon da, weil das `composer.json` im Wurzelverzeichnis es verlangt.

2. Aktivieren Sie die Extension in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=exp_request_shield
   ```

3. Erzeugen Sie die Autoloads neu und leeren Sie die Caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

   Auf einer Installation mit dem Befehl `exp:velocity deploy` erledigt `./console exp:velocity deploy --packer`
   beides, lädt PHP-FPM neu und startet Velocity neu.

4. Geben Sie der Website eigene Regeln ([Abschnitt 2](#2-der-website-eigene-regeln-geben)) und prüfen Sie sie
   ([Abschnitt 3](#3-prüfen-testen-und-verfolgen)), bevor Sie den nächsten Schritt tun: Ab dann entscheiden die
   Regeln jede Anfrage.

5. Fügen Sie den Hook in `config.php` (neben `index.php`) ein, **oberhalb** der Zeile, die den frühen Ausstieg des
   HTTP-Caches lädt (`ezphttpcacheearlyexit.php`), damit auch zwischengespeicherte Seiten geschützt sind:

   ```php
   if ( is_file( __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php' ) )
       require __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php';
   ```

   `config.php-RECOMMENDED` enthält diese Zeilen mit ihrem Kommentar im Abschnitt **REQUEST SHIELD**. Unter PHP-FPM
   wird die nächste Anfrage gefiltert; ein Neustart ist nicht nötig. Unter Velocity starten Sie diesen neu, weil
   seine Worker `config.php` nur einmal lesen:

   ```bash
   ./console exp:velocity restart
   ```

6. Sehen Sie nach, ob er arbeitet. Öffnen Sie **Setup > Request Shield**: Die Übersicht sagt, ob der Shield für diese
   Seite gelaufen ist, in welchem Modus, mit welcher Regeldatei und was er in den letzten 24 Stunden entschieden
   hat. Aus einer Shell:

   ```bash
   curl -sI https://www.example.org/settings/site.ini      # 404, vom Shield entschieden
   tail -f var/request-shield/log/shield.log
   ```

Extension und Hook funktionieren unabhängig voneinander: Ohne den Hook zeigt die Administration die Einrichtung,
aber nichts wird gefiltert; ohne aktivierte Extension filtert der Hook trotzdem, es gibt aber keine Admin-Seite.

## 2. Der Website eigene Regeln geben

Die eigene Datei `rules/request-shield.rules` der Extension wird verwendet, bis die Website eine eigene Datei hat, und
jede Aktualisierung der Extension ersetzt sie. Geben Sie der Website eine eigene:

- **Aus der Administration**: Setup > Request Shield, eine beliebige Modus-Schaltfläche (**Durchsetzen**, **Beobachten**
  oder **Aus**). Der erste Schalter legt `settings/request-shield/request-shield.rules` aus der Vorgabe an und
  kopiert die drei `exponential*.rules` daneben. Der Webserver muss `settings/request-shield/` beschreiben können.
- **Von Hand**, als der Benutzer, unter dem PHP für die Website läuft:

  ```bash
  mkdir -p settings/request-shield
  cp extension/exp_request_shield/rules/request-shield.rules \
     extension/exp_request_shield/rules/exponential*.rules settings/request-shield/
  ```

Die Kopien liegen neben der Hauptdatei, weil ein `include` das Verzeichnis seiner Datei nicht mit `..` verlassen darf;
die Bibliothek verweigert das. Bearbeiten Sie dann `settings/request-shield/request-shield.rules`:

1. **Wählen Sie die Datei für die Administration**: `include exponential-admin-uri.rules`, wenn die Administration
   der Siteaccess `/admin` ist, `include exponential-admin-host.rules`, wenn sie einen eigenen Hostnamen hat.
2. **Korrigieren Sie die Pfade**: Relative Pfade gelten relativ zur Regeldatei, und von `settings/request-shield/`
   aus liegt das Wurzelverzeichnis der Installation zwei Ebenen höher (die Vorgabe der Extension nennt drei):

   ```
   set store-dir ../../var/request-shield/store
   set log       ../../var/request-shield/log/shield.log
   ```

3. **Nennen Sie die Hostnamen der Website**, damit eine Anfrage an jeden anderen Namen abgewiesen wird, und behalten
   Sie `www.example.org`:

   ```
   host shop.example.com example.com admin.example.com
   host www.example.org                  # die Dokumentationsdomain, die die mitgelieferten Beispiele verwenden
   ```

   Die Exponential-Beispiele der Bibliothek senden `Origin: https://www.example.org`; ohne diese Host-Zeile lässt
   `bin/request-shield test` fünf davon scheitern (403 durch `EXP-ORIGIN`). Die Domain ist für Dokumentation
   reserviert; kein echter Besucher sendet sie.
4. **Nennen Sie den Proxy oder Load Balancer** davor, falls es einen gibt: `trust 10.0.0.0/8`. Ohne diese Zeile
   scheint jeder Besucher von der Adresse des Proxys zu kommen.
5. **Passen Sie die websitespezifischen Regeln an**, jede eine Zeile in `exponential.rules`, die Sie in Ihrer
   Hauptdatei mit `replace [ID] ...` ersetzen: `EXP-MODULES` (die URI-Siteaccesses, die vorangestellt sein dürfen,
   etwa `(/(ger|eng))?`), `EXP-POST` (die Module Ihrer Extensions, die Formulare annehmen), `EXP-SEARCHES` (das
   Budget der Suche).

Eine kürzere Website-Datei zum Vergleich:

```
ids SITE
version 2026-11-12.1

include exponential-admin-uri.rules

set mode enforce
host www.example.org example.org

set store-dir ../../var/request-shield/store
set log       ../../var/request-shield/log/shield.log
set log-level flag

[SITE-DASH] restrict /rs/** to 127.0.0.1 ::1
expect GET /rs/stats/overview from 198.51.100.27 403 by SITE-DASH
```

Um eine Datei an einem anderen Ort zu laden, definieren Sie `EXP_REQUEST_SHIELD_RULES` in `config.php` **vor** der
Hook-Zeile. Die anderen Konstanten (`EXP_REQUEST_SHIELD_ROOT`, `EXP_REQUEST_SHIELD_VAR`,
`EXP_REQUEST_SHIELD_DISABLED`) beschreibt
[doc/configuration.md](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/configuration.md) der Extension.
Jede Zeile der Regelsprache: die
[Referenz der Regeldateien](https://github.com/cjw-network/request-shield/blob/main/docs/reference/rule-files.md)
der Bibliothek (beides auf Englisch).

Der Webserver darf `settings/` und `var/request-shield/` nie ausliefern; ein standardmäßiger virtueller Host für
Exponential verweigert beide.

## 3. Prüfen, testen und verfolgen

Führen Sie die Kommandozeile als der Benutzer aus, unter dem PHP für die Website läuft (`sudo -u <Webbenutzer> ...`,
wenn Sie root sind), damit die Dateien, die sie schreibt, für die Website lesbar bleiben:

```bash
php extension/exp_request_shield/bin/request-shield rules
php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield test  settings/request-shield/request-shield.rules
```

- `rules` gibt die Hauptregeldatei aus, die die Website verwendet, und woher sie stammt.
- `check` liest alles, benennt jeden Fehler mit Datei und Zeile, warnt vor riskanten Einstellungen (zum Beispiel
  wenn die `/rs`-Seiten der Bibliothek für alle offen sind) und gibt die Stufe des Speichers aus.
- `test` entscheidet jedes `expect`-Beispiel, das neben den Regeln steht. Nach jeder Änderung muss er bestehen.

Um zu sehen, wie eine einzelne Anfrage Regel für Regel entschieden wird:

```bash
php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.org/the/path"
```

`check` und `test` übersetzen die Einstellungen neben der Regeldatei (`.request-shield/` daneben); die Web-Anfragen
verwenden `var/request-shield/cache/`. Setup > Request Shield > Regeln zeigt dasselbe Ergebnis in der Administration.

## 4. Beobachten oder durchsetzen

Die Regeln werden im Modus **enforce** ausgeliefert: Sobald der Hook in `config.php` steht, werden Scanner,
Anfragefluten, verirrte Formulare und System-URLs abgewiesen, und die Anmeldung und die Formulare der Administration
verlangen die Browserprüfung (für einen echten Browser eine Sekunde Arbeit, einmal pro Pass). In den Regeldateien
mit `monitor` markierte Regeln (`EXP-BAN`, `EXP-STRICT`) beobachten nur.

Im Modus **monitor** wird jede Entscheidung so protokolliert, wie sie fiele (`monitor-reject`, `monitor-challenge`
...), und niemand wird abgewiesen. Verwenden Sie ihn:

- auf einer Website mit vielen ungewöhnlichen Adressen (Seiten-Aliase, die mit einem Modulnamen beginnen, Formulare
  eigener Extensions) in den ersten Tagen, während die Redaktion arbeitet und die Suche genutzt wird;
- während Sie einer Beschwerde nachgehen, dass ein echter Besucher abgewiesen wurde.

Schalten Sie mit **Durchsetzen** und **Beobachten** auf der Übersicht um (Richtlinie `requestshield/manage`), oder schreiben
Sie `set mode monitor` oder `set mode enforce` in die Hauptdatei der Website. Jeder Server liest eine geänderte
Regeldatei innerhalb seiner `recheck` Sekunden neu; ein Neustart ist nicht nötig. Lesen Sie **Setup > Request
Shield > Protokoll**, Entscheidungsgruppe **Beobachtet (Monitor)**, und wechseln Sie zurück zu enforce, wenn das Log nur noch zeigt,
was Sie abweisen wollen.

## 5. Eine Ausnahme hinzufügen

Wenn ein echter Besucher oder Redakteur abgewiesen wurde (das Log nennt die Regel, etwa 404 durch `EXP-MODULES` oder
`EXP-SYSVIEW` bei einem Seiten-Alias, der mit einem Modulnamen beginnt):

1. Suchen Sie die Zeile in **Setup > Request Shield > Protokoll**, oder verfolgen Sie die Anfrage mit `trace`.
2. Fügen Sie in der Hauptdatei der Website eine Ausnahme dafür hinzu, mit einem `expect`-Beispiel der Adresse, die
   durchgelassen werden muss, und einem knapp daneben liegenden, das weiterhin abgewiesen werden muss. Der
   [Anwendungsfall Exponential](https://github.com/cjw-network/request-shield/blob/main/docs/use-cases/exponential.md)
   der Bibliothek zeigt die Muster (auf Englisch).
3. Führen Sie `check` und `test` aus.

Für eine Redakteurin oder einen Redakteur, die in der Administration immer wieder die Browserprüfung sehen: Erlauben
Sie JavaScript im Browser, oder nennen Sie eine feste Büroadresse mit `exempt <Adresse>`.

## 6. Pausieren

| Wie | Wirkung | Rückgängig |
|---|---|---|
| **Pausieren** auf der Übersicht | legt `var/request-shield/PAUSED` an: Der Shield tut nichts, auf jedem Server, ab der nächsten Anfrage | **Einschalten** |
| `touch var/request-shield/PAUSED` | dasselbe aus einer Shell, ohne etwas zu bearbeiten | die Datei entfernen |
| **Aus** oder `set mode off` in den Regeln | nichts wird entschieden; die Regeldatei wird geändert | **Durchsetzen** oder **Beobachten** |
| `define( 'EXP_REQUEST_SHIELD_DISABLED', true );` vor der Hook-Zeile | der Hook tut nichts | die Zeile entfernen; Velocity neu starten |

Jeder Schalter aus der Administration wird mit Login und ID des Benutzers und dem Ergebnis in
`var/log/requestshield.log` festgehalten.

## 7. Die Bibliothek aktualisieren

Die Bibliothek kommt mit der Extension; aktualisieren Sie die **Extension**, nie die mitgelieferten Dateien mit
`self-update` der Bibliothek, damit Dateien, `ezinfo.php` und Änderungsprotokoll zusammenbleiben:

```bash
composer update se7enxweb/exp_request_shield
```

Führen Sie danach `check` und `test` für die Regeln der Website aus und vergleichen Sie die drei Kopien in
`settings/request-shield/` mit `extension/exp_request_shield/rules/`: Übernehmen Sie die neuen Fassungen, wenn sie
sich geändert haben.

Unter Velocity darf ein Worker, der die alten Klassen schon geladen hat, nicht auf die neuen Dateien treffen: Legen
Sie `var/request-shield/PAUSED` an, aktualisieren Sie, führen Sie `./console exp:velocity restart` aus und entfernen
Sie dann `PAUSED`.

Wer als Maintainer eine neue Version der Extension aus einem neuen Tag der Bibliothek baut, verwendet
`bin/update-library.sh`; es baut die einzelnen Dateien und den Build für PHP 7.4 und prüft die Regeln mit jedem
genannten PHP: [doc/library.md](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/library.md) (auf
Englisch).

## 8. Deinstallieren

1. Entfernen Sie die Hook-Zeilen aus `config.php`; unter Velocity `./console exp:velocity restart`.
2. Entfernen Sie `ActiveExtensions[]=exp_request_shield`, dann `composer remove se7enxweb/exp_request_shield` (oder
   entfernen Sie das Verzeichnis). Auf einer Installation, deren `composer.json` im Wurzelverzeichnis das Paket
   verlangt, lassen Sie es installiert und nehmen nur den Hook und die Aktivierung heraus.
3. Erzeugen Sie die Autoloads neu und leeren Sie die Caches.
4. Entfernen Sie `var/request-shield/` und `settings/request-shield/`, wenn Sie Log, Statistiken und Regeln nicht
   mehr brauchen.

## 9. Verwandte Seiten

- [Der Request Shield](../funktionen/6.0/request-shield.md) (Referenz der Funktion)
- [Aktualisierung auf 6.0.15: der Request Shield](../bc/6.0/request-shield.md)
- [Request Shield: hier beginnen](../hilfe/request-shield/README.md) und seine
  [Fehlersuche](../hilfe/request-shield/fehlersuche.md)
- [Der HTTP-Cache mit vorgeschaltetem request-shield](../bc/6.0/httpcache.md#mit-vorgeschaltetem-request-shield)
- [Sicherheit und Audit](sicherheit-und-audit.md)
- [Bereitstellung](bereitstellung.md)
