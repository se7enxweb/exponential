---
title: Fehlerhafte Bereitstellung zurücknehmen
audience: government, admin, developer, hosting
category: public-sector
order: 90
summary: Runbook für eine Website, die direkt nach einer Bereitstellung ausfällt - bestätigen, dass die Änderung die Ursache ist, Code und Einstellungen zurücknehmen, entscheiden, ob die Datenbank wiederhergestellt werden muss, in der richtigen Reihenfolge erneut bereitstellen und das Ganze für den Änderungsbeirat dokumentieren.
keywords: fehlerhafte Bereitstellung, Rücknahme, gescheiterte Bereitstellung, Regression, erneut bereitstellen, vorherige Version, Autoloads, Cache leeren, exp:velocity deploy, PHP-FPM neu laden, Änderungsbeirat, Vorfall, broken deploy, rollback, failed deployment, redeploy, previous version, autoloads, cache clear, PHP-FPM reload, change board, incident
type: help
---
# Fehlerhafte Bereitstellung zurücknehmen

Dieses Runbook richtet sich an die Administration und die Entwicklung, wenn direkt nach einer Bereitstellung oder einer
Aktualisierung Fehler auftreten. Die Ursache liegt fast immer in dieser Änderung. Hintergrund zur Planung:
[Planung der Rücknahme](../aktualisierungen-und-aenderungen/planung-der-ruecknahme.md).

## Inhalt
1. [Bestätigen](#bestätigen)
2. [Schnelle Prüfungen vor der Rücknahme](#schnelle-prüfungen-vor-der-rücknahme)
3. [Code und Einstellungen zurücknehmen](#code-und-einstellungen-zurücknehmen)
4. [Wenn sich die Datenbank geändert hat](#wenn-sich-die-datenbank-geändert-hat)
5. [Nach der Rücknahme](#nach-der-rücknahme)
6. [Verwandte Seiten](#verwandte-seiten)

## Bestätigen

```bash
./console exp:logs --tail=var/log/error.log --lines=50
./console exp:audit search --channel=system --from='<time of the deploy>' --allow-root-user
```

Fehler, die zum Zeitpunkt der Bereitstellung begannen, deuten auf sie hin. Der Audit-Kanal `system` listet die
Bereitstellung, geschriebene Einstellungen und das Leeren von Caches in diesem Zeitfenster auf.

## Schnelle Prüfungen vor der Rücknahme

Viele „fehlerhafte Bereitstellungen“ sind unvollständige Bereitstellungen, die in einer Minute behoben sind:

| Symptom | Behebung |
|---|---|
| `Class ... not found` | `php bin/php/ezpgenerateautoloads.php -e`, dann die Caches leeren |
| Altes Verhalten noch sichtbar | PHP-FPM oder Velocity führen noch den alten Code aus: `./console exp:velocity deploy --allow-root-user` (lädt das richtige PHP-FPM neu und startet Velocity in der richtigen Reihenfolge neu) |
| Seiten sehen nach einer Template-Änderung alt aus | `./console exp:cache clear --id=template --allow-root-user` und `./console exp:velocity cache clear --allow-root-user` |
| Eine neue Einstellung wirkt nicht | `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, dann neu laden |

## Code und Einstellungen zurücknehmen

1. Wartungsmodus, wenn Besucher betroffen sind:
   `php bin/php/maintenance.php on --message="This service is temporarily unavailable." --allow-admin --allow-root-user`.
2. Stellen Sie den vorherigen Commit (oder das vorherige Release-Verzeichnis) mit seiner `composer.lock` und seinen
   Einstellungen bereit.
3. Erzeugen Sie neu und leeren Sie in der richtigen Reihenfolge, die Caches mit fertigen Seiten zuletzt:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   ./console exp:velocity deploy --dry-run --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

   Ohne Velocity: Caches leeren, das PHP-FPM neu laden, das die Website bedient, dann den Inhaltscache leeren.
4. Prüfen Sie von einer zugelassenen Adresse aus und schalten Sie dann den Wartungsmodus aus.

## Wenn sich die Datenbank geändert hat

Wenn die Bereitstellung eine Datenbankaktualisierung oder ein Migrationsskript ausgeführt hat, funktioniert der
vorherige Code mit dem neuen Schema möglicherweise nicht. Stellen Sie die direkt vor der Änderung erstellte
Datensicherung wieder her (Datenbank und Dateien), wie es der Leitfaden zur Aktualisierung für eine gescheiterte
SQL-Datei vorsieht; reparieren Sie nie von Hand und führen Sie nicht erneut aus
([Aktualisieren](../../../../anleitungen/aktualisieren.md#wenn-etwas-schiefgeht)). Inhalte, die seit der Datensicherung
bearbeitet wurden, gehen verloren: Sagen Sie der Redaktion, was sie neu machen muss.

## Nach der Rücknahme

- Leeren Sie den Inhalts- und den Antwortcache, nachdem PHP und Velocity den wiederhergestellten Code ausführen.
- Dokumentieren Sie: was ausgefallen ist, wann, dass die Rücknahme erfolgt ist, die Auswirkungen auf die Nutzenden. Der
  Änderungsbeirat braucht das, bevor die Änderung erneut versucht wird.
- Reproduzieren Sie den Fehler vor dem nächsten Versuch auf dem Staging-System
  ([Staging und Freigabe](../aktualisierungen-und-aenderungen/staging-und-freigabe.md)).

## Verwandte Seiten
- [Eine PHP-Änderung bereitstellen](../../../../../eng-us/help/government/tasks/operations/deploy-a-php-change.md)
- [Website nicht erreichbar](website-nicht-erreichbar.md)
- [Änderungen bereitstellen (Hosting)](../../../hosting/betrieb/aenderungen-ausrollen.md)
- [Leitfaden zur Bereitstellung, Abschnitt 7](../../../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
