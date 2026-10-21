# Wartungsmodus

Diese Seite ist für Administratoren, die eine Website für eine Weile vom Netz nehmen müssen: eine Aktualisierung, ein
Datenimport, eine Neuinstallation. Der Wartungsmodus nimmt die ganze öffentliche Website vom Netz, zeigt Besuchern eine
freundliche Seite und bringt sie mit einem Befehl oder einer Schaltfläche zurück. Der Kickstarter und der
Einrichtungsassistent im Web verwenden denselben Mechanismus, sodass eine Website, die gerade installiert wird, nie eine
halb gebaute Seite ausliefert.

Für eine Anleitung Schritt für Schritt mit Bildschirmfotos, Arbeitsabläufen und Fehlersuche lesen Sie die
Benutzeranleitung [Wartungsmodus: die Website vom Netz nehmen und zurückbringen](../../anleitungen/wartungsmodus.md).

Schnellstart:

```bash
php bin/php/maintenance.php on --message="Back at 06:00" --until=2h --allow-root-user
curl -sI https://www.example.com/ | head -1      # HTTP/1.1 503 Service Unavailable
php bin/php/maintenance.php off --allow-root-user
```

## Was ein Besucher sieht

Solange die Wartung eingeschaltet ist, wird jede Anfrage an `index.php`, `index_rest.php` und `index_treemenu.php` mit
der Wartungsseite beantwortet, bevor die Einstellungen oder die Datenbank berührt werden:

- HTTP-Status `503 Service Unavailable` mit einem Header `Retry-After`
- `Cache-Control: no-store` und `noindex`, sodass kein Cache und keine Suchmaschine sie behält
- Bilder, Stile und Skripte werden weiterhin ausgeliefert, sodass die Seite ihr Aussehen behält

Die Seite ist `errors/maintenance.html` des aktiven Themes, wenn eine aktive Extension eine hat, sonst
`share/maintenance.html`. Sie ist eine eigenständige HTML-Datei mit `{placeholders}`. Eine Standardinstallation hat jetzt
eine `favicon.ico` im Document Root, und die Standardseite verlinkt sie.

## Ein- und ausschalten

### Auf der Kommandozeile

```bash
php bin/php/maintenance.php on --message="Back at 06:00" --until=2h
php bin/php/maintenance.php status
php bin/php/maintenance.php off
./console exp:maintenance on|off|status     # the same, through the console
```

Als `root` fügen Sie `php bin/php/maintenance.php` `--allow-root-user` hinzu (sonst verweigert das Skript).
`php bin/php/maintenance.php --help` führt die Optionen auf.

| Option | Bedeutung |
|---|---|
| `--message="..."` | Was die Seite sagt, statt des Standardtextes. |
| `--until=30m` / `2h` / `"2026-09-28 06:00"` | Erwartetes Ende, Besuchern gezeigt und als `Retry-After` gesendet. |
| `--allow-ip=1.2.3.4,5.6.7.8` | Adressen, die die Website weiterhin sehen. |
| `--allow-admin` | Die Administrationsoberfläche (`/admin`) erreichbar halten. |

Das Einschalten leert außerdem die Caches, die vor `index.php` antworten.

### Aus der Administrationsoberfläche

**Setup > Wartung** (`/setup/maintenance`) tut dasselbe. Seit dem 5. Oktober 2026 ist sie nach der Aufgabe gegliedert, im
Aussehen der Cronjob-Seite:

- **Ein Statusbanner** oben: grün, wenn die Website online ist; gelb in der Wartung, mit dem Grund (Zeitfenster,
  Installation, Einrichtungsassistent, unlesbare Markierung), wer sie eingeschaltet hat, seit wann und wie lange, dem
  erwarteten Ende und der verbleibenden Zeit, einer Warnung, sobald dieses Ende vorbei ist (die Wartung endet nie von
  selbst), und ob Sie selbst die Website oder die Wartungsseite sehen. Die eine Aktion, die der Zustand verlangt, sitzt im
  Banner: **Website wieder online bringen**, mit einer Warnung, wenn eine Installation die Wartung noch hält.
- **Online ein Plan in drei Schritten**: die Meldung (mit einem Zeichenzähler), die erwartete Dauer (ein Klick für 15 min,
  30 min, 1 h, 2 h oder 4 h und die lokale und UTC-Zeit, die das bedeutet) und wer weiter hineinkommt (die
  Administrationsoberfläche unter `/admin` immer; Ihre eigene Adresse mit einem Haken; andere Adressen, beim Tippen und
  erneut auf dem Server geprüft, der nennt, was er ausgelassen hat). Eine verpflichtende Bestätigung schützt **Website
  offline nehmen**.
- **Eine Vorschau** der Seite, die Besucher bekommen: die echte Wartungsseite, ausgefüllt, in einem abgeschotteten Rahmen;
  sie folgt der Meldung und der Dauer beim Tippen. Offline zeigt sie die ausgelieferte Seite, mit der Meldung und der
  Liste derer, die weiter hineinkommen.
- **Aus einer Shell**: die Befehle `on`, `off` und `status` mit Schaltflächen zum Kopieren.
- **Letzte Änderungen**: die neuesten acht Ereignisse `system.maintenance.change` aus dem Audit-Index, verlinkt mit der
  Audit-Konsole (braucht `audit / read` auf dem Kanal `system`).

Sie braucht das Recht `setup / administrate`, und das Formular trägt das Formular-Token der Administrationsoberfläche;
die Namen von Feldern und Schaltflächen sind die der früheren Seite. Sie funktioniert ohne JavaScript (die Bestätigung ist
ein verpflichtendes Kontrollkästchen, die Abschnitte sind `<details>`), hält ab 390 Pixeln und bei 200 % Zoom und hält
einen Textkontrast von 4,5:1 im hellen und dunklen Modus von admin4; dasselbe Template bedient das ältere Design
`admin`. Die Administrationsoberfläche bleibt erreichbar, während die Website offline ist, sonst ließe sich die Seite, die
sie ausschaltet, nicht öffnen. Die Adresse, von der aus Sie sie eingeschaltet haben, wird nur durchgelassen, wenn
**Let my own address still see the site** angehakt ist (vor dem 27. September wurde sie immer durchgelassen, sodass es für
den Administrator aussah, als sei nichts geschehen).

Die vollständige Benutzeranleitung, mit Bildschirmfotos, Arbeitsabläufen, einer Sicherheitscheckliste und Fehlersuche:
[Wartungsmodus](../../anleitungen/wartungsmodus.md).

## Wie es funktioniert

Der Zustand ist eine Datei, `var/maintenance.json`. Solange sie existiert, binden die Front-Controller zuerst
`kernel/classes/expmaintenance.php` ein (einfaches PHP, kein Autoloader, keine Datenbank) und antworten mit der Seite. Eine
Markierung, die sich nicht lesen lässt, bedeutet trotzdem Wartung: Eine Wartungsseite ist besser als eine halb geänderte
Website.

| Feld der Markierung | Bedeutung |
|---|---|
| `reason` | `unknown`, eine Installation, `setup` (Assistent im Web) oder ein manuelles Zeitfenster. |
| `message`, `until` | Auf der Seite gezeigt. `until` ist eine Unix-Zeit; `Retry-After` sind die verbleibenden Sekunden oder 120, wenn kein Ende gesetzt ist. |
| `allow_ips`, `allow_paths` | Adressen und Pfade, die weiterhin bedient werden (`--allow-admin` fügt `/admin` hinzu). |
| `lease` | Nur Assistent im Web: Die Markierung läuft 900 Sekunden nach dem letzten gesendeten Schritt des Assistenten ab. |
| `allow_token` | Nur Assistent im Web: SHA-256-Hash des Cookies, der den eigenen Browser des Assistenten durchlässt. |

## Installationen verwenden ihn automatisch

- **Kickstarter**: schaltet die Wartung ein, wenn ein Lauf beginnt, und aus, wenn die Website installiert und geprüft ist.
  Nach einem fehlgeschlagenen Schritt bleibt sie an und er sagt, wie sie beendet wird (`php bin/php/maintenance.php off`).
- **Einrichtungsassistent im Web**: Sein erster gesendeter Schritt (nie ein GET, nie eine gezeigte Seite) schaltet die
  Wartung mit dem Grund `setup` ein und gibt seinem Browser einen zufälligen Cookie (`exp_setup_wizard`); jeder andere
  Besucher erhält die Wartungsseite statt eines zweiten Assistenten, der den ersten Lauf abbrechen oder überschreiben
  würde. Jeder gesendete Schritt erneuert den Lease von 15 Minuten (`expMaintenance::WIZARD_LEASE` = 900 Sekunden) und
  damit den Cookie, sodass ein verlassener Assistent die Website innerhalb von 15 Minuten von selbst zurückgibt. Die
  letzte Seite schaltet die Wartung aus und leert den Cookie. Auf einer installierten Website läuft der Assistent gar
  nicht: siehe [den Einrichtungsassistenten](../../installation/05-setup-assistent.md#52-die-browser-sperre-und-der-wartungsmodus).

Vorher trafen Anfragen, die während einer Installation ankamen, auf halb gebaute Tabellen und protokollierten Fehler,
die nicht die der Installation waren („no such table: ezurlalias_ml“). Eine Neuinstallation, geprüft mit einer Anfrage
pro Sekunde, zeigte während des Laufs 93 Wartungsantworten und ein `error.log`, das nur die eigenen Einträge des Laufs
für Beginn und Ende enthielt.

## Velocity und andere Caches

Die erzeugte Website-Konfiguration von Velocity nennt die Markierung als ihre Pausendatei für den Cache, sodass unter
[Velocity](velocity-server-mit-dauerhaften-workern.md) auch die Antwort-Caches während der Wartung pausieren (Engine-Release
0.0.4.34 und neuer).

## Verwandte Seiten

- [Wartungsmodus: die Benutzeranleitung](../../anleitungen/wartungsmodus.md)
- [Installation mit einem Befehl](installation-mit-einem-befehl.md), [Kickstarter](kickstarter-cli.md), [Einrichtungsassistent](einrichtungsassistent-und-editor-siteaccess.md)
- [Velocity: Exponential in einem Webserver mit dauerhaften Workern betreiben](velocity-server-mit-dauerhaften-workern.md)
- [Installer-Protokolle und Startdaten](../../spezifikationen/6.0/installer-protokolle-und-startdaten.md)
- [Chronik, 16. bis 30. September 2026](../../geschichte/2026/2026-09b.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
