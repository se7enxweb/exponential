---
title: Die CSP auf report-only zurücksetzen
audience: government, admin
category: public-sector
order: 90
summary: Schritt für Schritt - wenn eine durchgesetzte Content Security Policy die Arbeit blockiert, den Siteaccess in Minuten auf report-only zurücksetzen, jeden Prozess den neuen Header senden lassen und weiter Berichte sammeln, um die Ursache zu beheben.
keywords: CSP zurücksetzen, report-only, Mode, enforce, Notfalländerung, ezcache clear-tag ini, PHP-FPM neu laden, Velocity neu starten, Content Security Policy, öffentlicher Sektor, CSP rollback, emergency change, PHP-FPM reload, Velocity restart, public sector
type: help
---
# Die CSP auf report-only zurücksetzen

Für diensthabende Administratoren, wenn eine durchgesetzte Content Security Policy eine Seite zerstört, die Redakteure
brauchen. Das Zurücksetzen ändert eine Einstellung; sonst ändert sich nichts
([Content Security Policy: Zurücksetzen](../../../../../eng-us/features/6.0/content-security-policy.md#enforcing-nonce-more-sources-a-stricter-companion-rollback)).

## Inhalt

1. [Schritte](#schritte)
2. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
3. [Danach](#danach)
4. [Verwandte Seiten](#verwandte-seiten)

## Schritte

1. In `site.ini.append.php` des Siteaccess (wo `Mode=enforce` gesetzt wurde):

   ```ini
   [ContentSecurityPolicy]
   Mode=report-only
   ```

2. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
3. Laden Sie PHP-FPM neu und starten Sie Velocity neu: `./console exp:velocity deploy --allow-root-user`.

Wenn die Einstellungen aus der Versionsverwaltung kommen (`ManagedByDeployment`), nehmen Sie die Änderung dort vor und
rollen Sie sie als Notfalländerung aus.

## Prüfen, ob es funktioniert hat

```bash
curl -sI https://admin.example.gov/user/login | grep -i 'content-security-policy'
```

zeigt `Content-Security-Policy-Report-Only:` für die Richtlinie (der eigene Header `frame-ancestors 'self'` aus
`SecurityHeaders[]` bleibt). Bitten Sie den Redakteur, die Seite mit einem harten Neuladen neu zu laden.

## Danach

- Behalten Sie `ReportEndpoint=enabled`; dieselbe Richtlinie wird jetzt berichtet, nicht durchgesetzt, sodass der
  Verstoß unter „Berichte der Content Security Policy“ erscheint.
- Beheben Sie die Ursache (siehe „CSP-Berichte prüfen“) und planen Sie den nächsten Versuch.
- Halten Sie den Vorfall und die Zeiten fest.

## Verwandte Seiten

- [Von report-only zu einer durchgesetzten CSP](../../../../../eng-us/help/government/security/csp-from-report-only-to-enforce.md)
- [CSP-Berichte prüfen](csp-berichte-pruefen.md)
- [Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
