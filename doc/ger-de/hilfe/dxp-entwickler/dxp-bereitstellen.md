---
title: Ein DXP-Projekt bereitstellen
audience: dxp-developer
category: dxp-development
order: 38
summary: Die Bereitstellung eines DXP-Projekts in der Produktion der Reihe nach - Pull, Vendor-Pakete ohne Entwicklungspakete, Migrationen, öffentliche Assets, Builds für Administration und Website, Übersetzungen, Cache leeren und vorwärmen, Neuindexierung bei Bedarf - mit den Prüfungen davor und danach und dem, was nie auf einen Server gehört.
keywords: Bereitstellung, bereitstellen, Deployment, Produktion, composer install --no-dev, Migrationen, assets:install, yarn build, Cache vorwärmen, Neuindexierung, Release, Rückweg, Checkliste, deploy, production, migrations, cache warmup, reindex, release, rollback, checklist
type: help
---
# Ein DXP-Projekt bereitstellen

Diese Seite richtet sich an Entwickler und Betreiber, die eine neue Version eines DXP-Projekts live schalten. Die
Reihenfolge ist die Produktions-Checkliste der Anleitung des Skeletons (Abschnitt 15), mit den Prüfungen drumherum.

## Inhalt

1. [Bevor Sie bereitstellen](#bevor-sie-bereitstellen)
2. [Die Bereitstellung, Schritt für Schritt](#die-bereitstellung-schritt-für-schritt)
3. [Nach der Bereitstellung](#nach-der-bereitstellung)
4. [Anderswo bauen](#anderswo-bauen)
5. [Zurückrollen](#zurückrollen)
6. [Nie auf einem Server](#nie-auf-einem-server)

## Bevor Sie bereitstellen

- Die Änderung hat ihre Tests an einer Kopie mit produktionsähnlichen Daten bestanden.
- `composer.lock` und `yarn.lock` sind committet; der Server installiert genau diese.
- Ausstehende Migrationen wurden an einer Kopie mit `--dry-run` ausprobiert.
- Eine Datenbanksicherung existiert, und Sie wissen, wie Sie sie wiederherstellen.

## Die Bereitstellung, Schritt für Schritt

```bash
# 1. Code
git pull --rebase

# 2. Vendors
composer install --no-dev -o

# 3. Database
php bin/console doctrine:migration:migrate --allow-no-migration --env=prod

# 4. Public bundle assets
php bin/console assets:install --symlink --relative public --env=prod

# 5. Admin assets (when admin-ui or a bundle's assets changed)
source ~/.nvm/nvm.sh && nvm use 20 && yarn ibexa:build

# 6. Site assets (when the theme changed)
yarn build

# 7. Admin JavaScript translations
php bin/console bazinga:js-translation:dump public/assets --merge-domains --env=prod

# 8. Caches
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 9. Search (when the content model changed)
php bin/console exponential:reindex --env=prod
```

Laufen Nachrichten-Worker, senden Sie `php bin/console messenger:stop-workers`, damit sie mit dem neuen Code neu starten.
Haben Sie PHP-Einstellungen geändert, laden Sie PHP-FPM neu.

## Nach der Bereitstellung

```bash
curl -sI https://<your host>/ | head -1
curl -sI https://<your host>/adminui/ | head -1
tail -n 50 var/log/prod.log
```

Öffnen Sie eine Seite jedes wichtigen Inhaltstyps und melden Sie sich an `/adminui/` an.

## Anderswo bauen

Die Schritte 5 und 6 brauchen Node.js. Viele Teams bauen in der CI und stellen die gebauten `public/assets/` und
`public/bundles/` mit dem Code bereit, sodass Produktionsserver weder Node.js noch Build-Werkzeuge tragen. So oder so:
Bauen Sie mit den Versionen, die das Projekt festlegt
([Assets bauen](assets-mit-yarn-und-encore-bauen.md#für-die-produktion-bauen)).

## Zurückrollen

Code rollen Sie mit Git zurück; eine Datenbankmigration nicht von selbst. Bevorzugen Sie rückwärtskompatible
Migrationen (zuerst hinzufügen, in einem späteren Release entfernen), damit der vorherige Code noch auf dem neuen Schema
läuft. Ist das nicht möglich, stellen Sie die vor der Bereitstellung angelegte Sicherung wieder her.

## Nie auf einem Server

- `APP_ENV=dev` oder `APP_DEBUG=1` in der Produktion.
- Die Entwicklungsabhängigkeiten (`--no-dev` hält sie heraus).
- `.env.local` aus einer anderen Umgebung oder zwischen Umgebungen kopierte JWT-Schlüssel.
- Das Standard-Administratorpasswort des Installers.

## Verwandte Seiten

- [Aktualisieren](dxp-aktualisieren.md)
- [Doctrine-Migrationen](doctrine-migrationen.md)
- [Checkliste für DXP-Projekte](checkliste-fuer-dxp-projekte.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
