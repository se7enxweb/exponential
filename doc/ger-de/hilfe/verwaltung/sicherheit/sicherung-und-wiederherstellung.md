---
title: Sicherung und Wiederherstellung
audience: government, admin, hosting
category: public-sector
order: 390
summary: Für öffentliche Stellen, die ihre Website nach Ransomware, einem fehlgeschlagenen Update oder einem versehentlichen Löschen wiederherstellen können müssen - was eine Sicherung von Exponential enthält, wie Sie mit dem Wartungsmodus eine konsistente Sicherung erstellen, welche Geheimnisse sie enthält, Wiederherstellungstests und die Wiederherstellung einzelner Elemente ohne vollständige Wiederherstellung.
keywords: Sicherung, Backup, Wiederherstellung, Ransomware, Geschäftsfortführung, Notfallvorsorge, Wartungsmodus, Datenbank-Dump, Offline-Kopie, Wiederherstellungstest, Papierkorb, Versionen, Wiederherstellungszeit, öffentlicher Sektor, backup, restore, recovery, business continuity, maintenance mode, database dump, var/storage, settings/override, offline copy, restore test, trash, versions, recovery time, public sector
type: help
---
# Sicherung und Wiederherstellung

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams öffentlicher Websites und an
die Projektverantwortlichen, die Wiederherstellungsziele festlegen. Eine Sicherung besteht aus der Datenbank und den
Dateien, aufgenommen im selben Moment
([10.9 Sicherung und Wiederherstellung](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)).

## Inhalt

1. [Was gesichert werden muss](#was-gesichert-werden-muss)
2. [Eine konsistente Sicherung](#eine-konsistente-sicherung)
3. [Die Sicherungen schützen](#die-sicherungen-schützen)
4. [Wiederherstellungstests](#wiederherstellungstests)
5. [Kleinere Wiederherstellungen](#kleinere-wiederherstellungen)
6. [Verwandte Seiten](#verwandte-seiten)

## Was gesichert werden muss

| Pfad | Warum |
|---|---|
| die Datenbank | Inhalte, Benutzer, Rollen, in der Datenbank gespeicherte Einstellungen |
| `var/storage`, `var/<site>/storage` | hochgeladene Bilder und Dateien |
| `settings/override`, `settings/siteaccess` | die Einstellungen der Installation, **einschließlich Geheimnissen** (Datenbankpasswort, Audit-Schlüssel, Geheimnis für die E-Mail-Einstellungen) |
| Ihre eigenen Erweiterungen und Designs | falls sie nicht in einem Repository liegen |
| `var/log` (optional) | für spätere Untersuchungen; das Audit-Protokoll liegt unter `var/<site>/log/audit` |

Caches müssen nicht gesichert werden.

## Eine konsistente Sicherung

1. Wartungsmodus, damit zwischen der Sicherung der Datenbank und der Dateien nichts geschrieben wird:
   `php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user` (`--allow-admin` hält die
   Administration erreichbar).
2. Die Datenbank mit dem Werkzeug ihrer Datenbank-Engine (`mysqldump --single-transaction`, `pg_dump -Fc`, die
   Online-Sicherung von SQLite, ...).
3. Die Dateien: `tar czf /backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess`.
4. `php bin/php/maintenance.php off --allow-root-user`.

Unter SQLite und MySQL mit InnoDB ist die Datenbanksicherung auch ohne Wartungsmodus konsistent; der Wartungsmodus sorgt
dafür, dass Datenbank und Dateien zueinander passen.

## Die Sicherungen schützen

- **Verschlüsseln** Sie sie: Sie enthalten das Datenbankpasswort und die Audit-Schlüssel.
- Bewahren Sie mindestens eine Kopie **außerhalb des Rechners** und eine **offline** auf, damit Ransomware auf dem
  Server sie nicht erreichen kann
  ([Sicherheitshärtung: Sicherung und Wiederherstellung](../../sicherheitshaertung.md#sicherungen-und-wiederherstellung)).
- Bewahren Sie mehrere Generationen auf, so lange es Ihre Aufbewahrungs- und Datenschutzregeln erlauben (auch
  Sicherungen enthalten personenbezogene Daten).
- Legen Sie das Notfallverfahren für das Administratorkonto neben das Wiederherstellungsverfahren.

## Wiederherstellungstests

Stellen Sie regelmäßig (zum Beispiel jedes Quartal) auf einem Testrechner wieder her:

1. Wartungsmodus an; unter Velocity den Server anhalten;
2. die Datenbank wiederherstellen und die Dateien entpacken; Eigentümer und Modus korrigieren;
3. Caches leeren und starten;
4. prüfen: Startseite, Anmeldung, eine Inhaltsseite mit Bildern, `./console exp:audit verify --allow-root-user`.

Halten Sie Datum, verwendete Sicherung, benötigte Zeit und gefundene Probleme fest. Diese Zeit ist Ihre tatsächliche
Wiederherstellungszeit.

## Kleinere Wiederherstellungen

Nicht jeder Verlust erfordert eine vollständige Wiederherstellung:

| Verloren | Wiederherstellen mit |
|---|---|
| eine in den Papierkorb verschobene Seite | **Wiederherstellen** im Papierkorb (`content/trash`) ([Die Papierkorbansicht](../../../bc/6.0/papierkorb.md)) |
| ein früherer Text einer Seite | **Neuen Entwurf machen** aus der alten Version auf der Versionsseite ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)) |
| ein entferntes Benutzerkonto | nur eine Wiederherstellung: Benutzerobjekte kommen nie in den Papierkorb; nutzen Sie für ausscheidende Beschäftigte besser **Deaktivieren und verschieben** |
| ein archivierter Audit-Tag | `./console exp:audit restore --channel=<channel> --date=<YYYY-MM-DD> --allow-root-user` |

## Verwandte Seiten

- [10.9 Sicherung und Wiederherstellung](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)
- [Runbook: aus einer Sicherung wiederherstellen](../../hosting/runbooks/aus-einem-backup-wiederherstellen.md)
- [Sicherungen und Wiederherstellungen für Website-Ersteller](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
