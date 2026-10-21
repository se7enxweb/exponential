---
title: Ein Passwort auf der Kommandozeile zurücksetzen
audience: government, admin, hosting
category: public-sector
order: 90
summary: Schritt für Schritt - mit bin/php/resetuserpassword.php ein neues Passwort für jedes Konto setzen, auch ein verlorenes Administratorpasswort, und dabei ein zufälliges Passwort erzeugen, damit nichts in der Shell-Historie landet.
keywords: resetuserpassword, Passwort zurücksetzen, Kommandozeile, verlorenes Administratorpasswort, Administrator, Passwort erzeugen, Shell, root, Notfallzugang, reset password, command line, lost admin password, generate password, shell, break glass
type: help
---
# Ein Passwort auf der Kommandozeile zurücksetzen

Diese Aufgabe ist für Systemadministratoren mit Shell-Zugang, typischerweise wenn das Administratorpasswort verloren ist
oder eine Installation übernommen wird. `bin/php/resetuserpassword.php` setzt ein neues Passwort für jeden Benutzer; es
ist unter [Ein Benutzerpasswort auf der Kommandozeile zurücksetzen](../../../../../eng-us/features/6.0/reset-user-password.md)
beschrieben.

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Mit einem erzeugten Passwort zurücksetzen](#mit-einem-erzeugten-passwort-zurücksetzen)
3. [Stattdessen als Site-Administrator autorisieren](#stattdessen-als-site-administrator-autorisieren)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang im Wurzelverzeichnis der Installation. Entweder sind Sie der Betriebssystembenutzer `root` (dann
  verwenden Sie `--allow-root-user`), oder Sie kennen Login und Passwort eines Site-Administrators.
- Ein genehmigter Antrag: Ein Zurücksetzen in der Shell umgeht den Service Desk, also halten Sie fest, warum es getan
  wurde.

## Mit einem erzeugten Passwort zurücksetzen

Erzeugen Sie als `root` ein neues Passwort mit 20 Zeichen für das Konto `admin`:

```bash
php bin/php/resetuserpassword.php --allow-root-user -u admin -g -l 20
```

Das Passwort wird einmal ausgegeben. Tragen Sie es in Ihren Passwort-Safe ein, melden Sie sich an und ändern Sie es in
der Administration, wenn Ihre Richtlinie es verlangt. Für ein anderes Konto geben Sie dessen Login mit `-u <login>` an;
für einen Siteaccess mit eigener Datenbank fügen Sie `-s <siteaccess>` hinzu.

## Stattdessen als Site-Administrator autorisieren

Ohne Root des Betriebssystems wird das Zurücksetzen von einem Konto autorisiert, das die Rolle `Administrator` hat:

```bash
php bin/php/resetuserpassword.php -a <admin login> -ap '<admin password>' -u <login> -g
```

## Prüfen, ob es funktioniert hat

- Melden Sie sich mit dem neuen Passwort an.
- Die anderen Sitzungen des Kontos werden bei ihrer nächsten Anfrage abgemeldet, und eine E-Mail „Ihr Passwort wurde
  geändert“ geht an die Adresse des Kontos
  ([Das eigene Passwort ändern](../../../../../eng-us/features/6.0/modern-password-change.md#in-short)).
- Die Änderung steht im Audit-Protokoll; das Passwort wird nie aufgezeichnet.

## Fallstricke

| Meldung | Ursache |
|---|---|
| `Admin authentication required` | weder `-a`/`-ap` noch `--allow-root-user` wurde angegeben |
| `... does not have the Administrator role.` | der autorisierende Benutzer ist kein Administrator |
| `Target user not found: <login>` | der Login existiert nicht |
| `Target password does not validate ...` | kürzer als `MinPasswordLength` |

Ein mit `-p` eingetipptes Passwort (und das Passwort des Administrators nach `-ap`) ist in der Prozessliste und in der
Shell-Historie sichtbar. Bevorzugen Sie `-g`, und leeren Sie danach die Historie, wenn Sie eines eintippen mussten.

## Verwandte Seiten
- [Das Passwort eines Mitarbeiters zurücksetzen](das-passwort-eines-mitarbeiters-zuruecksetzen.md)
- [Standard-Administratorpasswort (Hilfe)](../../../standard-administratorpasswort.md)
- [Erste Anmeldung und Standardpasswörter (Hosting)](../../../hosting/sicherheit/erste-anmeldung-und-standardpasswoerter.md)
- [Vermutete Kompromittierung eines Kontos](../../../../../eng-us/help/government/operations/incidents/suspected-account-compromise.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
