---
title: Die Lernwebsite zurücksetzen und neu aufbauen
audience: student
category: learning
order: 15
summary: Wie Lernende eine Lernwebsite vor einer riskanten Übung sichern und danach neu anfangen - eine Sicherung der SQLite-Datenbank und der Dateien, eine Wiederherstellung, eine frische Installation mit exp:install --force, zuerst ein Probelauf, die eigene Arbeit in Git aufbewahren und was eine Neuinstallation entfernt.
keywords: zurücksetzen, neu aufbauen, neuinstallation, exp:install --force, dry-run, probelauf, sicherung, wiederherstellen, sqlite, schnappschuss, lernwebsite, neu anfangen, üben, reset, rebuild, reinstall, backup, restore, snapshot, study site, start over, practice
type: help
---
# Die Lernwebsite zurücksetzen und neu aufbauen

Eine Lernwebsite ist dazu da, kaputt gemacht zu werden. Diese Seite zeigt Ihnen, wie Sie sie vor einer riskanten Übung
sichern, wie Sie sie zurückbekommen und wie Sie in wenigen Minuten mit einer sauberen Installation neu beginnen. Alle
Befehle stammen aus den Installationsseiten und werden im Wurzelverzeichnis der Installation ausgeführt.

## Inhalt

1. [Drei Wege zurück](#drei-wege-zurück)
2. [Vor einer riskanten Übung sichern](#vor-einer-riskanten-übung-sichern)
3. [Einen gesicherten Zustand wiederherstellen](#einen-gesicherten-zustand-wiederherstellen)
4. [Mit einer frischen Installation neu anfangen](#mit-einer-frischen-installation-neu-anfangen)
5. [Die eigene Arbeit außerhalb der Installation aufbewahren](#die-eigene-arbeit-außerhalb-der-installation-aufbewahren)
6. [Was eine Neuinstallation entfernt](#was-eine-neuinstallation-entfernt)

## Drei Wege zurück

| Sie möchten | Verwenden Sie | Zeit |
|---|---|---|
| Eine Änderung an Inhalten rückgängig machen | Die Versionen des Elements oder den Papierkorb ([Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md)) | Sekunden |
| Zu einem gesicherten Zustand zurückkehren | Eine Sicherung der Datenbank und der Dateien | Minuten |
| Ganz von vorn beginnen | `exp:install --force` | Einige Minuten |

Versuchen Sie zuerst den kleinsten Weg. Versionen und Papierkorb gehören zum Redakteurpfad, und sie zu verwenden ist
bereits eine Übung.

## Vor einer riskanten Übung sichern

Eine vollständige Sicherung umfasst die Datenbank **und** die Dateien: `var/storage` (Uploads und Bildvarianten),
`settings/override` und `settings/siteaccess` ([Datenbankunterstützung](../datenbankunterstuetzung.md#backup-und-wiederherstellung)).

Für die Standard-SQLite-Datenbank von `exp:install` (`exponential.db` unter `var/storage/sqlite3/`) nennt die
Datenbankseite eine konsistente Sicherung im laufenden Betrieb:

```bash
mkdir -p ../study-backups/2026-10-20
sqlite3 var/storage/sqlite3/exponential.db ".backup '../study-backups/2026-10-20/exponential.db'"
cp -a var/storage settings/override settings/siteaccess ../study-backups/2026-10-20/
```

Die ersten beiden Zeilen folgen [Datenbankunterstützung](../datenbankunterstuetzung.md#backup-und-wiederherstellung); die Ordnernamen
sind Beispiele. Bewahren Sie die Sicherungen außerhalb der Installation auf, damit eine Neuinstallation sie nicht
entfernen kann. Wenn Sie mit dem Setup-Assistenten installiert haben, trägt Ihre SQLite-Datei den Namen, den Sie
vergeben haben (die Voreinstellung dort ist `sqlite.db`, [Exponential 6.0 installieren](../../INSTALL.md#die-schritte)).

Für eine Lernwebsite mit anderen Datenbanken nennt dieselbe Seite jeweils den Befehl für den Dump.

## Einen gesicherten Zustand wiederherstellen

Nach [Datenbankunterstützung](../datenbankunterstuetzung.md#backup-und-wiederherstellung):

1. Schalten Sie den Wartungsmodus ein: `php bin/php/maintenance.php on --allow-root-user` (lassen Sie
   `--allow-root-user` weg, wenn Sie nicht `root` sind).
2. Stellen Sie die Datenbank wieder her: `sqlite3 var/storage/sqlite3/exponential.db ".restore '../study-backups/2026-10-20/exponential.db'"`.
   Kopieren Sie niemals eine Datenbankdatei, die in Benutzung ist.
3. Kopieren Sie die gesicherten Ordner zurück.
4. Leeren Sie alle Caches: `php bin/php/ezcache.php --clear-all --allow-root-user`, und mit Velocity außerdem
   `php bin/php/console exp:velocity cache clear --apply --allow-root-user`.
5. Schalten Sie den Wartungsmodus wieder aus (siehe [Wartungsmodus](../../anleitungen/wartungsmodus.md)).

## Mit einer frischen Installation neu anfangen

`exp:install` verweigert die Ausführung über einer bestehenden Installation, sofern Sie nicht `--force` hinzufügen
([Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md#alle-optionen)). Prüfen Sie zuerst, was es
tun würde:

```bash
php bin/php/console exp:install --dry-run --random-password --url=http://localhost:8087
php bin/php/console exp:install --apply --force --random-password --url=http://localhost:8087
```

`--dry-run` prüft die Konfiguration und die Pakete und installiert nichts. Danach ersetzt der echte Lauf die
Lernwebsite und gibt ein neues Administratorpasswort aus. Fügen Sie `--email=` mit einer Adresse unter
`example.invalid` hinzu, damit der Administrator kein echtes Postfach hat; ohne diese Option verwendet der Installer
seine eigene Standardadresse.

Bei einer anderen Datenbank leert `--db-action=remove` zuerst die genannte Datenbank; verwenden Sie also eine
Datenbank, die nichts enthält, was Sie brauchen ([Erste Schritte](../../anleitungen/erste-schritte.md#32-andere-datenbanken)).

## Die eigene Arbeit außerhalb der Installation aufbewahren

Alles, was Sie über Neuinstallationen hinweg behalten möchten, gehört in Git, außerhalb der Ordner, die eine
Neuinstallation schreibt:

| Ihre Arbeit | Wo Sie sie aufbewahren |
|---|---|
| Ein Design oder ein Modul, das Sie geschrieben haben | Eine eigene Erweiterung in `extension/<name>`, in ihrem eigenen Git-Repository ([Projekt 8](projekt-08-ihre-erste-erweiterung.md)) |
| Notizen und das Tagebuch | Ein eigener Ordner und ein eigenes Repository ([Ein Lerntagebuch führen](ein-lerntagebuch-fuehren.md)) |
| Inhalte, die Sie erneut installieren möchten | Ein Paket, das Sie exportieren ([Pakete](../../anleitungen/pakete.md)) |
| Änderungen am Kernel selbst | Ein Branch Ihres Klons ([Aus Git-Klonen zum Lernen installieren](aus-git-klonen-zum-lernen-installieren.md)) |

## Was eine Neuinstallation entfernt

Wenn `exp:install` eine Installation findet, meldet es, dass es „deren Datenbank und Einstellungen ersetzen würde“,
und verlangt `--force` (`bin/php/install.php`). Nach einer erzwungenen Neuinstallation gilt also:

- die Inhalte und Benutzer der alten Datenbank sind weg;
- die Einstellungen, die der Installer schreibt (in `settings/override/` und `settings/siteaccess/`), werden neu
  geschrieben, sodass eine von Hand geänderte Einstellung verloren geht und eine eigene Erweiterung erneut
  eingeschaltet werden muss ([Erweiterungen](../../anleitungen/extensions.md#4-die-extension-einschalten)).

Ihre Git-Branches und Repositorys sind nicht Teil der Datenbank oder der Einstellungen. Behandeln Sie alles andere,
was Sie brauchen, als etwas, das Sie zuerst sichern.

## Verwandte Seiten

- [Eine Lernwebsite mit einem Befehl installieren](eine-lernwebsite-mit-einem-befehl-installieren.md)
- [Eine Lernwebsite mit einer Klasse teilen](eine-lernwebsite-mit-einer-klasse-teilen.md)
- [Datenbankunterstützung](../datenbankunterstuetzung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
