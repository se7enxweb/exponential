---
title: Alles sichern, was Sie für den Ausstieg brauchen
audience: government, project-manager, admin, hosting
category: public-sector
order: 50
summary: Was eine vollständige Sicherung einer Exponential-Website enthält, damit eine öffentliche Stelle ihre eigene Kopie halten und einen Lieferanten jederzeit verlassen kann - Datenbank und Dateien zusammen, Einstellungen, generierte Schlüssel, Code, Audit-Einträge - und wie Sie prüfen, ob Ihre Kopie vollständig ist.
keywords: Sicherung, eigene Kopie, Hinterlegung, Datenhoheit, Datenbank und Dateien, Einstellungen, Schlüssel, Audit-Protokoll, Wiederherstellungstest, Ausstieg, Kontinuität, backup, own copy, escrow, data custody, database and files, settings, keys, audit trail, restore test, exit, continuity
type: help
---
# Alles sichern, was Sie für den Ausstieg brauchen

Diese Seite richtet sich an die Projektleitung, die möchte, dass die öffentliche Stelle ihre eigene vollständige Kopie
ihrer Website hält, und an den Administrator, der sie prüft. Eine Sicherung, die nur der Lieferant aufbewahrt, hilft
nicht, wenn der Lieferant das Problem ist. Die Inhalte stammen aus [Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md) und
[10.9 Sicherung und Wiederherstellung](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung).

## Inhalt
1. [Eine Sicherung hat zwei Hälften](#eine-sicherung-hat-zwei-hälften)
2. [Die vollständige Liste](#die-vollständige-liste)
3. [Schlüssel, die sich nicht neu erzeugen lassen](#schlüssel-die-sich-nicht-neu-erzeugen-lassen)
4. [Eine konsistente Kopie erstellen](#eine-konsistente-kopie-erstellen)
5. [Eine eigene Kopie halten](#eine-eigene-kopie-halten)
6. [Die Kopie prüfen](#die-kopie-prüfen)

## Eine Sicherung hat zwei Hälften

„Eine Sicherung besteht aus der Datenbank und den Dateien, im selben Moment erstellt.“ Die Datenbank enthält Inhalte,
Benutzer, Rollen und Versionen; die Dateien enthalten Uploads, Einstellungen und Code. Das eine ohne das andere kann die
Website nicht wieder aufbauen.

## Die vollständige Liste

| Element | Pfad oder Quelle | Warum |
|---|---|---|
| Datenbank | das Dump-Werkzeug der Engine | Inhalte, Benutzer, Rollen, Versionen |
| Hochgeladene Dateien | `var/storage/`, `var/<site>/storage/` | Bilder, Dokumente, Medien |
| SQLite-Datenbankdatei (bei SQLite) | `var/storage/sqlite3/` über die Online-Sicherung von SQLite | die Datenbank selbst |
| Einstellungen der Installation | `settings/override/` | Zugangsdaten und generierte Schlüssel |
| Siteaccess-Einstellungen | `settings/siteaccess/` | die Einstellungen jeder Website |
| Eigene Erweiterungen und Designs | ihre Git-Repositorys | Ihr Code |
| Konfiguration auf PHP-Ebene | `config.php`, `config.cluster.php`, `config.env.php`, sofern vorhanden | Umgebung |
| Audit-Protokoll | `var/<site>/log/audit/` mit Archiven und signierten Manifesten | Nachweis, wer was getan hat |
| Composer-Dateien | `composer.json`, `composer.lock` | `vendor/` in denselben Versionen neu aufbauen |
| Konfiguration von Velocity (bei Velocity) | `/etc/vc` oder `var/vc/qbix/etc` | Einstellungen der Engine und Zertifikatsschlüssel |

Lassen Sie Caches (`var/cache/`), Klassenzuordnungen (`var/autoload/`), `vendor/` und temporäre Dateien weg: Sie werden
neu aufgebaut.

## Schlüssel, die sich nicht neu erzeugen lassen

| Datei | Enthält | Bei Verlust |
|---|---|---|
| `settings/override/audit.ini.append.php` | Signatur- und Pseudonymschlüssel des Audits | bestehende Audit-Einträge lassen sich nicht mehr verifizieren |
| `settings/override/mailpreferences.ini.append.php` | `[SecretSettings] TokenSecret` | jeder bereits versandte Abmeldelink funktioniert nicht mehr |
| Verzeichnis `ssl/` von Velocity | private Schlüssel der Zertifikate | HTTPS auf Velocity muss neu eingerichtet werden |

Wie Sie sie sicher übertragen, steht in [Übergabe von Schlüsseln und Geheimnissen](uebergabe-von-schluesseln-und-geheimnissen.md).

## Eine konsistente Kopie erstellen

Der Wartungsmodus hält Datenbank und Dateien im Gleichschritt:

```bash
php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
# database: the engine's dump tool
tar czf files-<date>.tar.gz var/storage settings/override settings/siteaccess
php bin/php/maintenance.php off --allow-root-user
```

Unter SQLite und unter MySQL mit InnoDB ist der Datenbank-Dump allein auch ohne Wartungsmodus konsistent; der
Wartungsmodus sorgt dafür, dass Datenbank und Dateien übereinstimmen.

## Eine eigene Kopie halten

- Vereinbaren Sie im Vertrag, dass der Lieferant Ihnen in festem Abstand und auf Anforderung eine vollständige Sicherung
  liefert.
- Bewahren Sie sie an einem Ort auf, den der Lieferant weder erreichen noch löschen kann, verschlüsselt und mit Zugriff
  nur für benannte Rollen: Sie enthält Zugangsdaten und personenbezogene Daten.
- Bewahren Sie sie nach Ihren Aufbewahrungsregeln auf; löschen Sie alte Kopien, wie diese Regeln es vorsehen.

## Die Kopie prüfen

- [ ] Jedes Element der Liste oben ist vorhanden.
- [ ] Der Dump lässt sich auf einem Testrechner wiederherstellen, und die Website startet ([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)).
- [ ] Bilder auf der wiederhergestellten Website werden angezeigt (Dateien und Datenbank stimmen überein).
- [ ] `./console exp:audit verify` meldet auf der wiederhergestellten Kopie eine intakte Kette.
- [ ] Das Datum der letzten erfolgreichen Prüfung steht im Ausstiegsplan.

## Verwandte Seiten
- [Ausstiegspläne: Übersicht](README.md)
- [Formate für den Datenexport](formate-fuer-den-datenexport.md)
- [Sicherungen und Wiederherstellungen für Website-Ersteller](../../../../../eng-us/help/creators/website-creators/backups-and-restores.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
