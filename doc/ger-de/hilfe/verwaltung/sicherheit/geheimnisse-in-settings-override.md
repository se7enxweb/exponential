---
title: Geheimnisse in settings/override
audience: government, admin, hosting, developer
category: public-sector
order: 320
summary: Für Administratorinnen, Administratoren und Hosting-Teams öffentlicher Websites - welche Dateien in settings/override Geheimnisse enthalten (Datenbankpasswort, Audit-Schlüssel, Geheimnis der E-Mail-Einstellungen, Velocity-Token, 2FA-Verschlüsselungsschlüssel), was geschieht, wenn sie verloren gehen oder abfließen, und wie Sie sie schützen, sichern und umziehen.
keywords: Geheimnisse, Datenbankpasswort, Audit-Schlüssel, Signaturschlüssel, Pseudonymschlüssel, Dateirechte, Sicherung, Versionsverwaltung, Geheimnisverwaltung, öffentlicher Sektor, secrets, settings/override, database password, audit keys, signing key, pseudonym key, mailpreferences TokenSecret, Velocity token, 2FA SecretKey, file permissions, backup, version control, secret management, public sector
type: help
---
# Geheimnisse in settings/override

Diese Seite richtet sich an Administratorinnen und Administratoren, Hosting-Teams sowie Entwicklerinnen und
Entwickler. Mehrere Dateien in `settings/override/` enthalten Geheimnisse, die das Installationsprogramm schreibt oder
der Code bei der ersten Verwendung erzeugt. Gehen sie verloren, funktioniert einiges nicht mehr; fließen sie ab, steht
die Website offen ([13.3 Geheimnisse in settings/override](../../../installation/13-sicherheitshaertung.md#133-geheimnisse-in-settingsoverride)).

## Inhalt

1. [Die Dateien](#die-dateien)
2. [Sie schützen](#sie-schützen)
3. [Eine Installation umziehen](#eine-installation-umziehen)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Dateien

| Datei | Geheimnis | Wenn verloren oder abgeflossen |
|---|---|---|
| `site.ini.append.php` | Datenbankbenutzer und -passwort | Zugriff auf die Datenbank |
| `audit.ini.append.php` | Audit-Signaturschlüssel und Pseudonymschlüssel | alte Archive lassen sich nicht mehr prüfen; Pseudonyme lassen sich umkehren |
| `mailpreferences.ini.append.php` | das Geheimnis der Website (`[SecretSettings] TokenSecret`) | Abmelde- und Einstellungslinks funktionieren nicht mehr, Sperreinträge passen nicht mehr, jeder Benutzer wird einmal abgemeldet |
| `velocity.ini.append.php` | `[DashboardSettings] Token`, sofern gesetzt | entfernter Zugriff auf Serverkennzahlen und `/Q/phpinfo` |
| `sevenxauthentication2fa.ini.append.php` | `[TOTPSettings] SecretKey` | alle müssen die Authenticator-App neu einrichten |
| `var/log/initial-admin-password` (nicht in settings) | das generierte erste Administratorpasswort | das Administratorkonto; löschen Sie die Datei nach der ersten Anmeldung |

## Sie schützen

1. **Checken Sie `settings/override/` nie** in die Versionsverwaltung ein und kopieren Sie es nie an einen öffentlichen
   Ort.
2. Modus `0640` oder strenger, Eigentümer der Benutzer der Website (oder root), Gruppe die Gruppe der Website. Seit
   6.0.15 behalten Speichervorgänge aus der Administration oder mit `exp:ini` den Modus der Datei bei und legen neue
   Dateien mit `0640` an. Prüfen Sie, dass nichts für alle beschreibbar ist:
   `find settings extension/*/settings -perm -o+w` darf nichts ausgeben.
3. **Sichern Sie sie** als Geheimnisse, verschlüsselt, zusammen mit der Datenbank.
4. Geben Sie `setup/setup` (Einstellungsansichten) nur Administratorinnen und Administratoren; die Administration und
   `exp:ini` maskieren geheime Werte.
5. Mit `ManagedByDeployment` kann die Administration überhaupt keine Einstellungen schreiben; generierte Schlüssel werden
   trotzdem geschrieben.
6. Schlüssel aus einem Secrets-Speicher: Das Audit kann seine Schlüssel aus Einstellungen übernehmen, die Sie schreiben
   (`[AuditKeySettings] GenerateKeys=disabled`) ([4.6 Schlüssel](../../../bc/6.0/audit.md#46-schlüssel)).

## Eine Installation umziehen

Kopieren Sie `settings/override/` bei jedem Umzug auf einen anderen Rechner mit. Eine Kopie, die ohne
`mailpreferences.ini.append.php` gestartet wird, erzeugt bei ihrer ersten E-Mail oder Anmeldung ein neues Geheimnis
der Website, und alte Links und Sperreinträge passen nicht mehr; die Datei nachträglich hineinzukopieren hilft für das
zwischenzeitlich Versandte nicht.

## Verwandte Seiten

- [13.3 Geheimnisse in settings/override](../../../installation/13-sicherheitshaertung.md#133-geheimnisse-in-settingsoverride)
- [Dateirechte und Eigentümer](dateirechte-und-eigentuemer.md)
- [Sicherung und Wiederherstellung](sicherung-und-wiederherstellung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
