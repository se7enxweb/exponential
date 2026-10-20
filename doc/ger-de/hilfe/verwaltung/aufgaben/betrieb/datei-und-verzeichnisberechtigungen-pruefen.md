---
title: Datei- und Verzeichnisberechtigungen prüfen
audience: government, admin, hosting
category: public-sector
order: 370
summary: Schritt für Schritt - prüfen, dass nur var/ (und, wo nötig, settings/) für das PHP der Site beschreibbar ist, dass jeder Prozess, der var/ beschreibt, denselben Eigentümer verwendet und dass neue Dateien nicht für alle lesbar sind, mit den gehärteten Modi aus dem Sicherheitskapitel.
keywords: Dateiberechtigungen, Verzeichnisberechtigungen, Eigentümer, beschreibbare Verzeichnisse, var, settings, EZP_DIR_MODE_MAX, EZP_FILE_MODE_MAX, root, gemischte Eigentümer, Härtung, file permissions, directory permissions, ownership, writable directories, mixed owners, hardening
type: help
---
# Datei- und Verzeichnisberechtigungen prüfen

Diese Aufgabe ist für Administratoren bei einer Sicherheitsüberprüfung oder bei der Reparatur einer Site, nachdem ein
als root ausgeführter Befehl sie beschädigt hat. Am Ende kann das PHP der Site nur schreiben, was es muss, jeder
Schreibende ist sich über den Eigentümer einig, und neue Dateien sind für andere Konten nicht offen. Quellen:
[Beschreibbare Verzeichnisse und Berechtigungen (Hosting)](../../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)
und [Kapitel 13.11](../../../../installation/13-sicherheitshaertung.md#1311-dateiberechtigungen-und-eigentümer).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Gehärtete Modi](#gehärtete-modi)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang als root; kennen Sie den Benutzer der Site (den Benutzer des PHP-FPM-Pools oder den Benutzer, unter dem
  die Worker von Velocity laufen).

## Schritte

1. Was beschreibbar sein muss: `var/` und alles darunter; `settings/`, `settings/override` und `settings/siteaccess` nur,
   solange Administratoren Einstellungen im Browser ändern; `design/` und `extension/` nur, solange Pakete aus der
   Administration installiert werden. Alles andere (`kernel/`, `lib/`, `vendor/`, `index.php`) muss nur lesbar sein.
2. Suchen Sie nach Dateien in `var/`, die jemand anderem gehören:

   ```bash
   find var -not -user <site user> | head
   ```

   Cache- oder Log-Dateien, die nach einem als root ausgeführten Befehl root gehören, sind die übliche Ursache seltsamer
   Fehler; **Zustand der Logs** warnt außerdem vor Logs mit gemischten Eigentümern.
3. Suchen Sie nach PHP-Dateien, wo nur Daten hingehören:

   ```bash
   find var/storage -name '*.php'
   ```

   Jedes Ergebnis ist verdächtig ([Defacement](../../../../../eng-us/help/government/operations/incidents/defacement.md)).
4. Führen Sie Cronjobs und Konsolenbefehle von nun an als Benutzer der Site aus, nicht als root.

## Gehärtete Modi

Neue Dateien und Verzeichnisse werden so offen angelegt, wie `site.ini [FileSettings]` und der Code es verlangen
(standardmäßig 0777/0666). Begrenzen Sie sie auf einem Live-Server in `config.php`:

```php
define( 'EZP_DIR_MODE_MAX', 0750 );
define( 'EZP_FILE_MODE_MAX', 0640 );
```

und schränken Sie das Bestehende einmal ein ([Grenzen für Dateimodi](../../../../../eng-us/bc/6.0/file-modes.md)). Mit
eingeschaltetem [ManagedByDeployment](die-konfiguration-mit-managedbydeployment-sperren.md) müssen `settings/` und `design/`
für PHP überhaupt nicht beschreibbar sein.

## Prüfen, ob es funktioniert hat

- **Setup > Systeminformationen**: Die Prüfung *var-Verzeichnis* ist grün.
- **Zustand der Logs**: keine Logs als nicht beschreibbar gelistet.
- `find var/storage -name '*.php'` findet nichts.

## Verwandte Seiten
- [Den Host härten](../../../../../eng-us/help/government/operations/hosting/hardening-the-host.md)
- [Den Zustand der Logs prüfen](den-zustand-der-logs-pruefen.md)
- [Sicherheit und Audit, Schritt 1](../../../../anleitungen/sicherheit-und-audit.md#1-die-bereits-aktive-härtung-prüfen-5-minuten)
- [Härtung für Hoster](../../../hosting/sicherheit/haertung-fuer-hoster.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
