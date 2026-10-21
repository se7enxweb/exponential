---
title: Die Kontosperre einschalten
audience: government, admin, hosting
category: public-sector
order: 220
summary: Schritt für Schritt - Konten nach einer Zahl fehlgeschlagener Anmeldungen mit MaxNumberOfFailedLogin sperren, vertrauenswürdige Büronetze ausnehmen, entscheiden, was Benutzern gesagt wird, und planen, wie gesperrte Mitarbeiter entsperrt werden.
keywords: Kontosperre, fehlgeschlagene Anmeldungen, MaxNumberOfFailedLogin, TrustedIPList, ShowMessageIfExceeded, Brute Force, Erraten von Passwörtern, Sicherheitsrichtlinie, Schutz der Anmeldung, account lockout, failed logins, brute force, password guessing, security policy, sign-in protection
type: help
---
# Die Kontosperre einschalten

Diese Aufgabe ist für Administratoren, deren Sicherheitsrichtlinie verlangt, dass Konten nach wiederholt
fehlgeschlagenen Anmeldungen gesperrt werden. Die Sperre ist standardmäßig **aus**:
`settings/site.ini [UserSettings] MaxNumberOfFailedLogin=0`. Der Sicherheitsstandard führt das als bekannte Lücke: Brute
Force wird von den Audit-Warnungen erkannt, nicht vom Kernel gebremst
([Sicherheitsstandard, „Bekannte Lücken“](../../../../../eng-us/standards/security.md#known-gaps)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Einstellungen](#die-einstellungen)
3. [Schritte](#schritte)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang oder Ihr Deployment-Prozess, wenn Einstellungen vom Deployment verwaltet werden.
- Ein Verfahren des Service Desks, um Mitarbeiter zu entsperren ([Ein gesperrtes Konto entsperren](ein-gesperrtes-konto-entsperren.md)).
- Die Netze Ihrer Büros, wenn sie ausgenommen werden sollen.

## Die Einstellungen

Alle in `site.ini [UserSettings]`:

| Einstellung | Mitgeliefert | Bedeutung |
|---|---|---|
| `MaxNumberOfFailedLogin` | `0` | Zahl fehlgeschlagener Versuche, nach der die Anmeldung verweigert wird; `0` schaltet es aus |
| `TrustedIPList[]` | leer | Adressen oder Netze, die sich nach fehlgeschlagenen Versuchen immer anmelden dürfen |
| `ShowMessageIfExceeded` | `false` | `true`: Dem Benutzer wird gesagt, dass die Grenze überschritten wurde |

## Schritte

1. Fügen Sie `settings/override/site.ini.append.php` mit Ihren Werten hinzu:

   ```ini
   [UserSettings]
   MaxNumberOfFailedLogin=5
   TrustedIPList[]
   TrustedIPList[]=192.0.2.0/24
   ShowMessageIfExceeded=false
   ```

2. Leeren Sie den INI-Cache und rollen Sie auf Velocity aus:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:velocity deploy --dry-run --allow-root-user
   ```

3. Lesen Sie den wirksamen Wert:

   ```bash
   ./console exp:ini get 'site.ini/UserSettings/MaxNumberOfFailedLogin' --allow-root-user
   ```

## Prüfen, ob es funktioniert hat

- Lassen Sie mit einem Testkonto die Anmeldung von einer Adresse außerhalb von `TrustedIPList` öfter fehlschlagen als
  die Grenze: Das Konto erscheint unter **Gesperrt** in **Benutzer > Benutzerkonten prüfen**.
- **Entsperren** Sie es dort.

## Fallstricke

- Eine Sperre lässt jeden, der einen Login kennt, diese Person aussperren. Eine Liste vertrauenswürdiger Netze und ein
  schnelles Verfahren zum Entsperren begrenzen den Schaden; eine Ratenbegrenzung vor der Site ist eine weitere Maßnahme
  ([Sicherheitshärtung](../../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).
- Falsche Codes der zweistufigen Anmeldung zählen ebenfalls als fehlgeschlagene Anmeldungen.
- `ShowMessageIfExceeded=true` sagt einem Angreifer, dass das Konto existiert und gesperrt ist; die meisten Behörden
  lassen es aus.

## Verwandte Seiten
- [Ein gesperrtes Konto entsperren](ein-gesperrtes-konto-entsperren.md)
- [Pflege der Benutzerkonten](../../../../../eng-us/help/government/operations/people-and-permissions/user-account-hygiene.md)
- [Die Administration beschränken (Hosting)](../../../hosting/sicherheit/die-administration-einschraenken.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
