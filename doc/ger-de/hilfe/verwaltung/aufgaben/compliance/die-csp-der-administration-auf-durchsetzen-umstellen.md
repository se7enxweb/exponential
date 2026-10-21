---
title: Die CSP der Administration auf Durchsetzen umstellen
audience: government, admin, developer
category: public-sector
order: 70
summary: Schritt für Schritt - nach der Prüfung der Templates und einer Zeit sauberer Berichte die Content Security Policy des Siteaccess der Administration von report-only auf enforce umstellen, sie in jeden Prozess ausrollen und den Header prüfen.
keywords: Content Security Policy, CSP, durchsetzen, enforce, report-only, Mode, Siteaccess der Administration, site.ini, ReportEndpoint, ezcache clear-tag ini, Velocity deploy, zurücksetzen, öffentlicher Sektor, administration siteaccess, rollback, public sector
type: help
---
# Die CSP der Administration auf Durchsetzen umstellen

Für Administratoren. Ab Werk sendet die Administration eine Richtlinie im Modus **report-only** (`Mode=auto`). Das
Durchsetzen lässt den Browser eingeschleuste Skripte blockieren. Tun Sie es nach den Schritten in
[Von report-only zu einer durchgesetzten CSP](../../../../../eng-us/help/government/security/csp-from-report-only-to-enforce.md): der Prüfung der
Templates und einer Zeit der Berichte.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Der Prüftest lief mit jeder Erweiterung:
  `CSP_ALL_EXTENSIONS=1 php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php`.
- Eine Woche Berichte ohne unerklärte Verstöße („CSP-Berichte prüfen“).
- Idealerweise wurde zuerst ein zweiter Siteaccess der Administration durchgesetzt.
- Ein Änderungsantrag mit dem Zurücksetzen („Die CSP auf report-only zurücksetzen“).

## Schritte

1. In `settings/siteaccess/<admin siteaccess>/site.ini.append.php`:

   ```ini
   [ContentSecurityPolicy]
   Mode=enforce
   ReportEndpoint=enabled
   ```

2. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
3. Laden Sie PHP-FPM neu und starten Sie Velocity neu: `./console exp:velocity deploy --allow-root-user` erledigt alles.

## Prüfen, ob es funktioniert hat

```bash
curl -sI https://admin.example.gov/user/login | grep -i '^content-security-policy'
```

zeigt `Content-Security-Policy:` (nicht `-Report-Only`) mit `script-src 'self' 'nonce-...'`. Verwenden Sie dann die
wichtigsten Aufgaben der Redakteure (Bearbeiten, Hochladen, Kontextmenüs, Dialoge) und beobachten Sie
**Setup > Logdateien** > „Berichte der Content Security Policy“.

## Wenn etwas schiefgeht

- Einträge im Kontextmenü, die nichts tun: eine Erweiterung mit Inline-Skripten; stellen Sie sie um oder erlauben Sie
  `'unsafe-inline'` für diesen Siteaccess.
- Benutzer des klassischen Designs behalten eine Richtlinie im Modus report-only (`ReportOnlyDesigns[]`).
- Alles, was die Arbeit blockiert: sofort zurücksetzen (`Mode=report-only` setzen, INI-Cache leeren, neu laden).

## Verwandte Seiten

- [Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md)
- [Content Security Policy für öffentliche Sites](../../../../../eng-us/help/government/security/content-security-policy.md)
- [CSP-Berichte prüfen](csp-berichte-pruefen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
