# Einen Fehler anhand seiner Referenz finden

Diese Anleitung zeigt, wie Sie von dem, was ein Besucher meldet („auf der Seite stand *Etwas ist schiefgegangen*,
Referenz ERR-0123456789“), zu den Logzeilen dieser Anfrage gelangen, zu dem, was die Anfrage sonst noch protokolliert
hat, und dazu, wer in diesem Moment was getan hat. Sie richtet sich an Administratoren und Entwickler, die solche
Meldungen beantworten.

[Anleitungen](README.md) · Siehe auch: [Der Log-Viewer](../funktionen/6.0/log-betrachter.md),
[Sicherheit und Audit](sicherheit-und-audit.md), [Systeminformationen](systeminformationen.md)

## Kurz gesagt

- Eine Fehlerseite zeigt eine Referenz wie `ERR-0123456789`; dieselbe Referenz wird neben den Einzelheiten des
  Fehlers in `error.log` geschrieben.
- **Setup > Logdateien**, Feld **Fehlerreferenz**: Fügen Sie die Referenz ein und klicken Sie auf **Suchen**. Die
  Seite zeigt den Eintrag und jede Zeile derselben Anfrage (gleiche Adresse und gleicher Client, innerhalb von zwei
  Sekunden) aus allen Kernel-Logs.
- Oder geben Sie die Adresse direkt ein: `/setup/logs/view/error/(reference)/ERR-0123456789/(scope)/kernel/(rotations)/1`
  (im Siteaccess der Administration, z. B. `/admin/setup/logs/view/error/...`).
- Von dort aus: **Das Audit dieses Moments** dafür, wer was getan hat, und die Links auf den Knoten, das Objekt oder
  das Template, die die Zeilen nennen.
- Dasselbe in einer Shell: `./console exp:logs --reference=ERR-0123456789`.
- Personenbezogene Daten bleiben maskiert (E-Mail- und IP-Adressen, Tokens); nur ein Benutzer mit `setup/unmasklogs`
  kann das für sich selbst abschalten.

## 1. Die Referenz beschaffen

Fragen Sie nach der Referenz, die die Fehlerseite gezeigt hat, und ungefähr danach, wann es passiert ist. Eine
Referenz besteht aus `ERR-` und zehn Ziffern oder Buchstaben von A bis F. Gibt es keine, notieren Sie die Uhrzeit und
die Adresse der Seite.

## 2. Sie finden

1. Melden Sie sich in der Administration an und öffnen Sie **Setup > Logdateien** (in der Gruppe **System** des
   Setup-Menüs).
2. Geben Sie im Suchformular die Referenz in **Fehlerreferenz** ein und klicken Sie auf **Suchen**. Gesucht wird in
   jedem Kernel-Log und seinen Rotationen (`error.log`, `error.log.1` ... und den komprimierten `.gz`-Dateien).
3. Der Eintrag wird mit Uhrzeit, Stufe, Siteaccess, maskierter Client-Adresse und Anfrage angezeigt, darunter
   **Dieselbe Anfrage**: die Warnungen, Hinweise und Debug-Zeilen, die die Anfrage geschrieben hat, in zeitlicher
   Reihenfolge.

Nicht gefunden? Das Log ist möglicherweise wegrotiert (sehen Sie sich die ältesten Einträge unter **Rotierte Archive** im Navigator
und `site.ini [FileSettings] LogFileRotateCount` der Site an), oder der Fehler ist auf einem anderen Server
aufgetreten: Jeder Server hat sein eigenes `var/log`.

Ohne Referenz suchen Sie stattdessen nach Zeit und Adresse: **Von** und **Bis** einige Minuten um die Meldung herum,
die Adresse unter **Anfrage**, die Stufen **Fehler** und **Warnung**, Bereich **Alle Kernel-Logs**. Als Permalink:
`/setup/logs/view/error/(level)/error,warning/(from)/2026-10-17T01:00/(to)/2026-10-17T01:10/(uri)/~2Fshop/(scope)/kernel`
(ein `/` wird in einem Wert zu `~2F`, siehe [Permalinks](../funktionen/6.0/log-betrachter.md#permalinks); ein
früher geschriebener Link mit `~3A` für `:` wird genauso gelesen).

Sind die Webserver-Logs des Servers konfiguriert ([Webserver-Logs](../funktionen/6.0/log-betrachter.md#webserver-logs)),
zeigt `/setup/logs/view/webserver-access` mit demselben Zeitraum die Anfrage so, wie der Webserver sie gesehen hat:
ihren Status, ihre Größe und den User-Agent.

## 3. Die Anfrage lesen

- Die **Quelle** jedes Eintrags (`eZTemplate`, `eZMySQLiDB`, `kernel/content/view.php` ...) sagt, welcher Teil des
  Codes ihn geschrieben hat.
- Knoten- und Objekt-IDs, Klassenbezeichner und Template-Pfade in den Meldungen sind Links: Öffnen Sie den Inhalt
  oder das Template, um das es bei dem Fehler ging.
- **Das Audit dieses Moments** öffnet die Audit-Konsole zur selben Zeit: Eine Bearbeitung, eine Veröffentlichung oder
  eine Änderung der Einstellungen unmittelbar vor dem Fehler ist oft die Ursache.
- **Zugehörige Zeilen** bei einem beliebigen Eintrag findet den Rest seiner Anfrage auf dieselbe Weise.

## 4. In einer Shell

```
./console exp:logs --reference=ERR-0123456789
./console exp:logs --search=ERR-0123456789 --scope=kernel --rotations --json
./console exp:logs --level=error,warning --from='2026-10-17 01:00' --to='2026-10-17 01:10' --uri=/shop --scope=kernel
```

Der erste Befehl gibt den Eintrag, die Zeilen seiner Anfrage und das Audit-Zeitfenster aus; Exit-Code 1 bedeutet,
dass er nicht gefunden wurde.

## 5. Weitergeben, was Sie gefunden haben

Schicken Sie einem Kollegen die Ansicht selbst: **Permalink kopieren** liefert eine Adresse wie
`/setup/logs/view/error/(reference)/ERR-0123456789/(scope)/kernel/(rotations)/1`, die für jeden mit `setup/logs`
dieselbe Suche öffnet, immer maskiert (ein Permalink trägt nie den Schalter zum Aufheben der Maskierung). Kopieren
oder laden Sie die Zeilen mit eingeschalteter Maskierung herunter (die Voreinstellung): Der Download über **Bereich
herunterladen** ist wie die Seite maskiert und komprimiert. Fügen Sie nie unmaskierte Zeilen in ein Ticket ein: Sie
können E-Mail-Adressen und IP-Adressen von Kunden enthalten.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
