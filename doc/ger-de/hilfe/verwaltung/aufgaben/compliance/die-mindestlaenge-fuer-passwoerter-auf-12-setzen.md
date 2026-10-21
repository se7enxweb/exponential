---
title: Die Mindestlänge für Passwörter auf 12 setzen
audience: government, admin
category: public-sector
order: 20
summary: Schritt für Schritt - die Mindestlänge für Passwörter in Exponential von den mitgelieferten 10 auf 12 (der Wert von OWASP ASVS 4.0.3 Stufe 1) oder mehr anheben, ohne jemanden auszusperren, und prüfen, dass neue Passwörter sie einhalten.
keywords: Mindestlänge für Passwörter, MinPasswordLength, Passwortrichtlinie, ASVS V2.1.1, 12 Zeichen, 15 Zeichen, NIST, settings override, exp:ini, öffentlicher Sektor, minimum password length, password policy, 12 characters, 15 characters, public sector
type: help
---
# Die Mindestlänge für Passwörter auf 12 setzen

Für Administratoren, deren Sicherheitsrichtlinie oder ein Audit mindestens 12 Zeichen verlangt. Exponential liefert
`[UserSettings] MinPasswordLength=10` aus, unter OWASP ASVS 4.0.3 V2.1.1, das 12 verlangt
([OWASP ASVS zuordnen](../../../../../eng-us/help/government/security/owasp-asvs-mapping.md)). Viele Organisationen wählen 15, da die
Installationsanleitung empfiehlt, NIST SP 800-63B-4 zu folgen.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang zur Installation, als Benutzer der Site oder mit `--allow-root-user`.
- Entscheiden Sie den Wert mit Ihrem Sicherheitsbeauftragten: 12 erfüllt ASVS Stufe 1; 15 erfüllt auch die Leitlinie
  von NIST.
- Informieren Sie die Mitarbeiter: Die Regel gilt, wenn das nächste Mal ein Passwort gesetzt wird; niemand wird
  ausgesperrt.

## Schritte

1. Sehen Sie sich die Änderung in der Vorschau an:

   ```bash
   ./console exp:ini set site.ini/UserSettings/MinPasswordLength 12 global --dry-run --allow-root-user
   ```

2. Schreiben Sie sie (der Bereich `global` ist `settings/override/site.ini.append.php`):

   ```bash
   ./console exp:ini set site.ini/UserSettings/MinPasswordLength 12 global --allow-root-user
   ```

3. Leeren Sie den INI-Cache und lassen Sie PHP die Änderung sehen: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`,
   dann laden Sie PHP-FPM neu, oder `./console exp:velocity deploy --allow-root-user` auf Velocity.

## Prüfen, ob es funktioniert hat

- `./console exp:ini get site.ini/UserSettings/MinPasswordLength --allow-root-user` gibt 12 aus.
- Öffnen Sie als Testbenutzer **Mein Konto** > **Passwort** und versuchen Sie ein Passwort mit 11 Zeichen: Es wird
  abgelehnt.
- Das Audit zeigt das Schreiben der Einstellung: `/audit/console/(name)/system.setting.write`.

## Wenn etwas schiefgeht

- Die Länge zählt **Bytes**: Passwörter in Schriften mit Mehrbyte-Zeichen erreichen sie mit weniger Zeichen.
- Das Schreiben von Einstellungen aus der Administration wird abgelehnt, wenn `ManagedByDeployment` eingeschaltet ist:
  Tragen Sie die Zeile stattdessen in Ihre versionierten Einstellungen ein.
- Um zurückzugehen, setzen Sie den alten Wert auf dieselbe Weise.

## Verwandte Seiten

- [Passwortrichtlinie](../../../../../eng-us/help/government/security/password-policy.md)
- [13.6 Passwörter und Anmeldung](../../../../installation/13-sicherheitshaertung.md#136-passwörter-und-anmeldung)
- [Eigene Wörter zur Liste schwacher Passwörter hinzufügen](eigene-woerter-zur-liste-schwacher-passwoerter-hinzufuegen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
