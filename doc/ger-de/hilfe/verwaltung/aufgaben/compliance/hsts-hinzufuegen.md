---
title: HSTS hinzufügen
audience: government, admin, hosting
category: public-sector
order: 110
summary: Schritt für Schritt - HTTP Strict Transport Security für eine Site einschalten, die vollständig HTTPS ist, mit der Einstellung SecurityHeaders von Exponential (Apache, nginx) oder HSTSMaxAge von Velocity, kurz beginnen und den Wert stufenweise anheben.
keywords: HSTS, Strict-Transport-Security, max-age, includeSubDomains, preload, HSTSMaxAge, SecurityHeaders, HTTPS, TLS, stufenweise Einführung, öffentlicher Sektor, staged rollout, public sector
type: help
---
# HSTS hinzufügen

Für Administratoren und Hosting-Teams. HSTS ist in Exponential standardmäßig leer und muss vom Betreiber gesetzt
werden, sobald die ganze Site nur noch über HTTPS läuft ([HTTPS und HSTS](../../../../../eng-us/help/government/security/https-and-hsts.md)). Ein
Browser merkt sich HSTS für den ganzen Zeitraum, auch wenn Sie den Wert später senken; gehen Sie in Stufen vor.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Jeder Name der Site wird über HTTPS mit einem gültigen Zertifikat ausgeliefert; HTTP leitet auf HTTPS weiter.
- Jede Seite und jede Ressource lädt über HTTPS (keine gemischten Inhalte).
- Hinter einem Proxy: `TrustedProxies[]` ist konfiguriert, damit der Kernel weiß, dass die Anfrage über HTTPS kam.

## Schritte

**Apache oder nginx mit PHP-FPM** (der Kernel sendet ihn nur über HTTPS):

```bash
./console exp:ini set 'site.ini/HTTPHeaderSettings/SecurityHeaders[Strict-Transport-Security]' 'max-age=300' global --dry-run --allow-root-user
./console exp:ini set 'site.ini/HTTPHeaderSettings/SecurityHeaders[Strict-Transport-Security]' 'max-age=300' global --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

dann laden Sie PHP-FPM neu.

**Velocity**: `settings/override/velocity.ini.append.php`, `[HTTPSSettings] HSTSMaxAge=300` (300 ist der
mitgelieferte Wert; außerdem `HSTSIncludeSubDomains`, `HSTSPreload`), dann
`./console exp:velocity deploy --allow-root-user`.

Heben Sie den Wert in Stufen an: 300 Sekunden, ein Tag (86400), 30 Tage (2592000), ein Jahr (31536000), jeweils nach
einer Zeit ohne Probleme.

## Prüfen, ob es funktioniert hat

```bash
curl -sI https://www.example.gov/ | grep -i strict-transport-security
```

zeigt den Wert, den Sie gesetzt haben. Über einfaches HTTP wird der Header nicht gesendet (richtig so).

## Wenn etwas schiefgeht

- Ein Name, der nicht über HTTPS läuft, ist für Browser, die den Header gesehen haben, bis zum Ende des Zeitraums
  unerreichbar: Richten Sie HTTPS für diesen Namen ein; das Senken des Werts hilft Browsern nicht, die ihn schon
  gespeichert haben.
- Fügen Sie `includeSubDomains` erst hinzu, wenn jede Subdomain nur noch über HTTPS läuft.

## Verwandte Seiten

- [HTTPS und HSTS](../../../../../eng-us/help/government/security/https-and-hsts.md)
- [13.9 Sicherheitsheader und HTTPS](../../../../installation/13-sicherheitshaertung.md#139-sicherheits-header-und-https)
- [Die Sicherheitsheader prüfen](die-sicherheitsheader-pruefen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
