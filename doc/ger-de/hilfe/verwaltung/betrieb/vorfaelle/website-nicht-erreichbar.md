---
title: Website nicht erreichbar
audience: government, admin, hosting
category: public-sector
order: 30
summary: Runbook für eine öffentliche Website, die keine Seiten ausliefert - bestätigen, ablesen, welche Fehlerseite Besucher sehen, die Ursache finden (Datenbank, vergessener Wartungsmodus, fehlende Bibliotheken, ein nicht abgefangener Fehler, der Webserver oder Velocity), beheben und die Öffentlichkeit informieren.
keywords: Website nicht erreichbar, Ausfall, 500, 503, leere Seite, weiße Seite, Fehlerreferenz, Velocity-Status, PHP-FPM, Runbook, Vorfall, site down, outage, blank page, white page, We'll be right back, error reference, Velocity status, incident
type: help
---
# Website nicht erreichbar

Dieses Runbook richtet sich an die Administration in Rufbereitschaft, wenn eine öffentliche Website keine Seiten
ausliefert. Es folgt [Website nicht erreichbar (Hosting-Runbook)](../../../hosting/runbooks/website-nicht-erreichbar.md) und den
Fehlerseiten aus [Kapitel 12.9](../../../../installation/12-fehlerbehebung.md#129-seiten-und-fehler-zur-laufzeit) und ergänzt
die Schritte für den öffentlichen Sektor: Vorfallsrollen, Kommunikation, Dokumentation. Beginnen Sie mit den
[Grundlagen der Vorfallsbehandlung](grundlagen-der-vorfallsbehandlung.md).

## Inhalt
1. [Bestätigen](#bestätigen)
2. [Welche Seite sieht der Besucher?](#welche-seite-sieht-der-besucher)
3. [Überhaupt nichts antwortet](#überhaupt-nichts-antwortet)
4. [Die Öffentlichkeit informieren](#die-öffentlichkeit-informieren)
5. [Nach der Behebung](#nach-der-behebung)
6. [Verwandte Seiten](#verwandte-seiten)

## Bestätigen

```bash
curl -sI https://www.example.gov/ | head -5
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.gov/admin/user/login
./console exp:logs --tail=var/log/error.log --lines=50
df -h
```

Eine volle Festplatte verursacht die meisten Symptome; siehe [Festplatte voll](festplatte-voll.md).

## Welche Seite sieht der Besucher?

| Der Besucher sieht | Ursache | Vorgehen |
|---|---|---|
| „We'll be right back“ (503) | die Datenbank war nicht erreichbar | [Datenbank nicht erreichbar](datenbank-nicht-erreichbar.md) |
| „Down for maintenance“ (503) | der Wartungsmodus ist eingeschaltet | `php bin/php/maintenance.php status --allow-root-user`; wenn die Arbeiten abgeschlossen sind, `php bin/php/maintenance.php off --allow-root-user` |
| „The site is missing the software libraries it needs“ (503) | `vendor/` fehlt | stellen Sie es wieder her, oder reparieren Sie: `php bin/php/exprepair.php --create-key` und den Schlüssel auf der Seite eingeben ([Reparatur aus dem Browser](../../../../funktionen/6.0/reparatur-im-browser.md)) |
| „Something went wrong on our side“ (500) mit `ERR-...` | ein nicht abgefangener Fehler | `./console exp:logs --reference=ERR-...` |
| Eine leere Seite, oder 500 ohne Eintrag in `error.log` | PHP ist gescheitert, bevor Exponential protokollieren konnte | die Fehlerlogs des Webservers und von PHP-FPM; `php -l` für eine kürzlich geänderte Datei |
| 404 für jede Seite unter Apache | `.htaccess` wird nicht gelesen oder `mod_rewrite` ist aus | `AllowOverride` und Module des virtuellen Hosts |

Fehler, die direkt nach einer Bereitstellung beginnen, liegen fast immer in dieser Änderung:
[Fehlerhafte Bereitstellung zurücknehmen](fehlerhafte-bereitstellung-zuruecknehmen.md).

## Überhaupt nichts antwortet

- Velocity: `./console exp:velocity status --allow-root-user`; wenn es nicht läuft,
  [Velocity startet nicht (Hosting-Runbook)](../../../hosting/runbooks/velocity-startet-nicht.md).
- Apache mit PHP-FPM: Läuft der Webserver, und läuft der PHP-FPM-Dienst, der diese Website bedient (auf Servern mit
  mehreren PHP-Versionen heißt er nicht immer `php-fpm`)?
- Netzwerk, DNS und Zertifikat: [Ablauf von Zertifikaten](ablauf-von-zertifikaten.md).

## Die Öffentlichkeit informieren

Wenn der Ausfall länger als ein paar Minuten dauert, veröffentlicht die Kommunikation einen kurzen Hinweis auf Ihren
anderen Kanälen (wo der Dienst in der Zwischenzeit erreichbar ist). Wenn die Website überhaupt antwortet, ist der
Wartungsmodus mit einer klaren Meldung besser als eine Fehlerseite.

## Nach der Behebung

1. Leeren Sie die Caches, die die Behebung erfordert; unter Velocity dessen Antwortcache **nach** jedem Neustart:
   `./console exp:velocity cache clear --apply --allow-root-user`.
2. Bestätigen Sie das Ergebnis von außen; beobachten Sie die Fehlerübersicht eine Stunde lang.
3. Dokumentieren Sie Ursache, Zeitablauf und Behebung; halten Sie bei Schweregrad 1 eine
   [Nachbetrachtung des Vorfalls](nachbetrachtung-eines-vorfalls.md) ab.

## Verwandte Seiten
- [Grundlagen der Vorfallsbehandlung](grundlagen-der-vorfallsbehandlung.md)
- [Externe Verfügbarkeitsüberwachung](../ueberwachung/externe-verfuegbarkeitsueberwachung.md)
- [Wenn etwas schiefgeht (Hilfe)](../../../wenn-etwas-schiefgeht.md)
- [Langsame Website (Hosting-Runbook)](../../../hosting/runbooks/langsame-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
