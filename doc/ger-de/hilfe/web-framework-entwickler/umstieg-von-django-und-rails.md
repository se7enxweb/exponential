---
title: Umstieg von Django oder Rails
audience: web-framework-developer, developer
category: web-frameworks
order: 460
summary: Ein Leitfaden für Django- und Ruby-on-Rails-Entwickler, die zu einem PHP-Framework mit eingebautem CMS wechseln - URLconf und Routen, Views und Controller, ORM und Migrationen, der Django-Admin, Templates, Signals und Callbacks, Management-Befehle und Rake-Tasks, Celery und Active Job, jeweils mit ihrer Entsprechung in Exponential, dazu die PHP-Gewohnheiten, die Sie sich aneignen sollten.
keywords: Umstieg, Django, Rails, Ruby on Rails, Python, Ruby, Routen, Migrationen, Signale, coming from, URLconf, routes, ORM, ActiveRecord, migrations, Django admin, signals, callbacks, management commands, rake, Celery, Active Job, Wagtail, Web-Framework-Entwickler, web framework developer
type: help
---
# Umstieg von Django oder Rails

Diese Seite richtet sich an Entwickler, die Django oder Rails kennen und neu in PHP oder in Exponential sind. Beide
Frameworks teilen den Gedanken von Exponential, „alles Nötige mitzubringen“: Django liefert einen Admin mit, Rails
Generatoren und Konventionen. Exponential liefert eine redaktionelle Administrationsoberfläche, ein Inhaltsmodell mit
Versionen und Übersetzungen, Berechtigungen und eine Template-Sprache mit.

## Inhalt

1. [Gegenüberstellung](#gegenüberstellung)
2. [Der Admin, den Sie schon kennen, und mehr](#der-admin-den-sie-schon-kennen-und-mehr)
3. [PHP für Python- und Ruby-Entwickler](#php-für-python--und-ruby-entwickler)
4. [Was Ihnen fehlen wird](#was-ihnen-fehlen-wird)

## Gegenüberstellung

| Django | Rails | Exponential 6 | Seite |
|---|---|---|---|
| `urls.py` | `config/routes.rb` | `module.php`, URL-Aliase | [Routing](routing-mit-modulen-und-views.md) |
| Views | Controller | Modul-Views | [Controller](controller-als-modul-views.md) |
| Templates (DTL, Jinja) | ERB | die Template-Sprache | [Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md) |
| Modelle, ORM | ActiveRecord | Inhaltsklassen; `eZPersistentObject` | [Das Inhaltsmodell](das-inhaltsmodell-fuer-orm-nutzer.md) |
| Migrationen | Migrationen | `.dba`-Dateien und Upgrade-Skripte | [Migrationen](migrationen-und-dba-schemadateien.md) |
| `settings.py` | `config/environments/*.rb` | INI-Dateien, `EXP_ENV` | [Konfiguration](konfiguration-ini-dateien.md) |
| Middleware | Rack-Middleware, Filter | Request-Regeln, Listener | [Middleware](middleware-und-request-regeln.md) |
| Signals | Callbacks | `ezpEvent`, Workflows | [Ereignisse](ereignisse-und-listener.md) |
| `manage.py`-Befehle | `bin/rails`, Rake | `bin/php/console` | [Kommandozeile](cli-und-konsolenbefehle.md) |
| Celery | Active Job, Sidekiq | `expqueue` | [Warteschlangen](warteschlangen-und-hintergrundjobs.md) |
| `contrib.auth`, Berechtigungen | Devise, Pundit | Benutzer als Inhalte, Rollen und Richtlinien | [Authentifizierung](authentifizierung-und-sitzungen.md), [Autorisierung](autorisierung-rollen-und-richtlinien.md) |
| `sites`-Framework | mehrere Apps oder Engines | Siteaccesses | [Siteaccesses](siteaccesses-hosts-und-mandanten.md) |
| gettext | I18n | `.ts`-Kataloge, übersetzte Inhalte | [Übersetzungen](i18n-l10n-und-rtl.md) |
| Gunicorn, Uvicorn | Puma | PHP-FPM, Velocity, FrankenPHP | [Server und Deployment](server-und-deployment.md) |

## Der Admin, den Sie schon kennen, und mehr

Der Django-Admin erzeugt Formulare für Modelle; die Administrationsoberfläche von Exponential ist für Redakteure
gebaut, nicht für Entwickler: ein Inhaltsbaum, Entwürfe und Versionen, Übersetzungen nebeneinander, ein
Rich-Text-Editor, Bildvarianten, Freigabe-Workflows, ein Seiten-Baukasten, ein Papierkorb, ein Audit-Protokoll und
Rollen. Es gibt auch inhaltsorientierte CMS in Python und Ruby; wenn Sie eines davon kennen, werden Ihnen die
Inhaltsklassen von Exponential vertraut vorkommen. Beginnen Sie mit dem
[Rundgang durch die Administration](../rundgang-durch-die-administration.md), bevor Sie Code schreiben.

## PHP für Python- und Ruby-Entwickler

- Jeder Request beginnt bei `index.php`; unter PHP-FPM überlebt nichts zwischen Requests, unter Velocity bleiben die
  Worker bestehen, wie es ein Gunicorn- oder Puma-Worker täte ([Der Request-Lebenszyklus](der-request-lebenszyklus.md)).
- Composer ist der Paketmanager (wie pip oder Bundler) und Packagist der Paketindex.
- Arrays sind zugleich Listen und Dictionaries; ein großer Teil der Konfiguration in `module.php` und `definition()`
  besteht aus Arrays.
- Benennung: Kernel-Klassen verwenden das Präfix `eZ`, neuer Code des Projekts verwendet `exp`, und Ihre eigenen
  Klassen erhalten ein Präfix Ihrer Organisation oder Erweiterung
  ([Autoloads und Klassenbenennung](../ersteller/anwendungsentwickler/autoloads-und-klassenbenennung.md)).
- Die Seiten für PHP-Entwickler behandeln die Sprachseite ([PHP-Entwickler: hier beginnen](../php-entwickler/php-entwickler-hier-beginnen.md)).

## Was Ihnen fehlen wird

- Modellgetriebene Migrationen, die aus Codeänderungen erzeugt werden.
- Eine Schicht vom Modell zum Formular wie Django-Forms oder `form_with`.
- Ein Test-Client, der im selben Prozess läuft.
- Generatoren für jedes Teil; die RAD-Werkzeuge decken die häufigsten ab ([RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md)).

## Verwandte Seiten

- [Begriffswörterbuch](begriffswoerterbuch.md)
- [Umstieg von Express oder Spring](umstieg-von-express-und-spring.md)
- [Aufgabe: ein kleines CRUD-Modul bauen](ihr-erstes-modul-schritt-fuer-schritt.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
