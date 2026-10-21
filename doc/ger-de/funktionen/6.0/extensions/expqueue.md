# expqueue: die Auftragswarteschlange

Lesen Sie diese Seite, wenn eine Extension, die Sie verwenden, Arbeit in eine Warteschlange stellt (Exponential
Intelligence tut das), oder wenn Sie eigene Hintergrundaufträge ausführen möchten. Die Extension ist das Composer-Paket
`se7enxweb/expqueue`, das `composer.json` ab 6.0.15 verlangt (`~0.1.0`); sie liegt also auf jeder Installation in
`extension/expqueue` und bleibt inaktiv, bis sie aktiviert wird. Ihre vollständige Dokumentation liegt bei ihr, im
[Repository von expqueue](https://github.com/se7enxweb/expqueue).

## Kurz gesagt

| | |
|---|---|
| Was sie tut | Eine allgemeine, datenbankgestützte Auftragswarteschlange. Erzeuger stellen einen Auftrag mit einem Aufruf ein; Worker führen ihn auf der Kommandozeile, aus cron oder als systemd-Dienst aus, nie innerhalb einer Webanfrage. Extensions registrieren Auftragstypen und ihre Handler in `expqueue.ini`. |
| Planung | Sieben Prioritätsklassen mit Gewichten, faire Planung mit Alterung und einem Mindestanteil pro Klasse, Kontingente pro Benutzer und Website, Begrenzungen der Nebenläufigkeit pro Ressource, die sich an Hinweise zur Ratenbegrenzung anpassen |
| Zuverlässigkeit | Leases mit einem Aufräumer, Wiederholungen mit Backoff, ein Dead Letter, Idempotenzschlüssel, Abhängigkeiten, Zeitpunkte „nicht vor“, Auftragsgruppen mit Fortschritt, Abbrechen, Pausieren und Fortsetzen, ein Ergebnis-Checkpoint für Handler |
| Admin | Setup > Job queue (Dashboard, Aufträge, Gruppen, Steuerung); Richtlinien `expqueue/view` und `expqueue/manage` |
| Engines | MySQL/MariaDB, PostgreSQL, SQLite, Oracle und MongoDB |
| Release | [0.1.0](https://github.com/se7enxweb/expqueue/releases/tag/v0.1.0) |

expqueue ist getrennt von den [Inhaltsaufträgen](../../../../eng-us/bc/6.0/content-jobs.md) des Kernels, die große
Inhaltsoperationen (Entfernen, Kopieren, Verschieben, Paketinstallationen, Audit-Exporte) als eigene Aufträge ausführen.

## Einschalten

1. Aktivieren Sie sie global, damit Erzeuger auf jedem Siteaccess und die Worker sie sehen, in
   `settings/override/site.ini.append.php`: `[ExtensionSettings] ActiveExtensions[]=expqueue`.
2. Legen Sie die Tabellen an (idempotent; zugleich die Aktualisierung):

   ```bash
   ./console ext:expqueue:install-schema
   ```

3. `php bin/php/ezpgenerateautoloads.php -e` und `./console exp:velocity deploy` (oder leeren Sie die INI-, Template-
   und Übersetzungs-Caches und laden Sie PHP-FPM von Hand neu).
4. Ein Worker, als Benutzer der Website, jede Minute:

   ```cron
   * * * * * cd /path/to/installation && php runcronjobs.php -q expqueue >> var/log/cronjob-expqueue.log 2>&1
   ```

   Wenn die Auftragstypen einer Extension nur für einige Siteaccesses aktiv sind (expai wird pro Admin-Siteaccess
   aktiviert), fügen Sie `-s <that siteaccess>` hinzu, sonst kennt der Worker sie nicht. Ein systemd-Dienst
   (`./console ext:expqueue:service install`) kann für kürzere Wartezeiten neben cron laufen.
5. Prüfen: `./console ext:expqueue:enqueue expqueue.noop`, `./console ext:expqueue:work --once`,
   `./console ext:expqueue:status` oder Setup > Job queue > „Send a test job“.

Die vollständigen Schritte: die [Installationsanleitung](https://github.com/se7enxweb/expqueue/blob/main/INSTALL.md);
die API für Erzeuger, Planung, Worker und Einstellungen: [die Dokumentation](https://github.com/se7enxweb/expqueue/blob/main/doc/index.md).

## Verwandte Seiten

- [expai](expai.md), Exponential Intelligence, das seine eingereihte Arbeit hier ausführt
- [Inhaltsaufträge](../../../../eng-us/bc/6.0/content-jobs.md)
- [Exponential installieren, Abschnitt 10](../../../INSTALL.md#10-nach-der-installation)
- [Verzeichnis der Extensions](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
