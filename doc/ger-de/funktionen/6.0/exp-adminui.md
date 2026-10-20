# Exponential Admin UI: das Design und der Siteaccess adminui

Diese Seite ist für Administratoren, die ein anderes Aussehen der Administrationsoberfläche möchten, und für
Website-Entwickler, die Admin-Designs betreuen. Exponential Admin UI (die Extension `exp_adminui`) ist das Aussehen und
Layout von Netgen Admin UI für die Administrationsoberfläche von Exponential: eine dunkle Seitenleiste mit den
Hauptbereichen und dem Benutzermenü, ein Suchkopf, eine helle linke Spalte mit dem Menü oder Inhaltsbaum des Bereichs und
die Tabellen, Reiter und Schaltflächen von Admin UI. Sie läuft als eigener Siteaccess, `adminui`, neben der
Standard-Administrationsoberfläche, die ihr Design admin4 / admin4l behält.

## Was Sie bekommen

- Den Seitenrahmen von Admin UI, aus dem Twig-Layout von Netgen Admin UI in Exponential-Templates übertragen. Symfony wird
  nicht gebraucht.
- Jede Admin-Ansicht im Aussehen von Admin UI. Die eigenen Ansichts-Templates von Admin UI sind mit allem
  zusammengeführt, was Exponential seitdem hinzugefügt hat (Widgets von Exponential UI statt YUI, jQuery 4, Korrekturen
  der Maskierung, neue Menüeinträge und Ansichten). Ansichten, die Admin UI nie hatte (Audit, Cronjobs, Wartung,
  Exponential Layouts, Newsletter ...), kommen aus admin4l und admin4 und erhalten den Rahmen und die Farben von Admin UI.
- Das Logo und die Namen von Exponential. Die Akzentfarbe ist das Blau von Admin UI (admin4 behält sein Orange).

## Eine neue Installation hat ihn bereits

Seit dem 06.10.2026 legt die Installation den Siteaccess `adminui` standardmäßig an, wann immer `extension/exp_adminui`
in der Installation liegt: der Einrichtungsassistent, der Kickstarter und `exp:install` (`CreateSites`,
`createAdminUISiteAccess()` in `kernel/setup/steps/ezstep_create_sites.php`) sowie der Multisite-Paketinstaller von
`sevenx_multisite` und `sevenx_multisite_clean`, der dieselbe Funktion als letzten seiner eigenen Schritte nach der
Installation aufruft. Was geschrieben wird:

- `settings/siteaccess/adminui/`: eine Kopie der `*.ini.append.php`-Dateien des Admin-Siteaccess, wie die Installation
  sie geschrieben hat (Datenbank, Sprachen, `VarDir`, `RequireUserLogin=true`, `LoginPage=custom`,
  `ShowHiddenNodes=true`, die Einstellungen für Überschreibungen, Menü, Werkzeugleiste, Inhaltsstrukturmenü, Inhalte und
  Ansichts-Cache), mit `SiteName=Admin UI`, `SiteURL` als Adresse des Admins mit dem Pfad `/adminui`,
  `SiteDesign=adminui`, der Design-Kette `admin4l`, `admin4`, `admin3`, `admin2`, `admin` und
  `ActiveAccessExtensions[]=exp_adminui`. Ihre `icon.ini.append.php` und `ezoe.ini.append.php` enthalten keine
  Einstellungen, sodass der Icon-Satz und die Editor-Einstellungen der Extension gewinnen.
- `adminui` in `AvailableSiteAccessList[]`, `RelatedSiteAccessList[]` und `SiteList[]` von
  `settings/override/site.ini.append.php`, zugeordnet **nur über die URI**: keine Zeile `HostMatchMapItems[]`, kein Port.
  `uri` wird vor `MatchOrder` gesetzt, wenn der Zugangstyp Host oder Port ist, sodass `https://<admin host>/adminui`
  funktioniert, gleich, wonach die anderen Siteaccesses zugeordnet werden.
- Nicht in `ActiveExtensions[]`: `exp_adminui` ist eine Access-Extension und bleibt nur für `adminui` eingeschaltet. Der
  Multisite-Installer lässt `adminui` außerdem aus den öffentlichen Siteaccesses heraus (HTTP-Cache, anonyme Anmeldung).

Ohne die Extension wird nichts geschrieben, und `var/log/setup.log` sagt, warum („no adminui siteaccess, the
exp_adminui extension is not in extension“). Eine Installation, die vorher entstand oder die Extension später erhält,
wird wie unten von Hand eingerichtet.

## Von Hand einschalten

1. Installieren Sie die Extension in `extension/exp_adminui` (git clone von
   [se7enxweb/exp_adminui](https://github.com/se7enxweb/exp_adminui) oder Composer, sobald das Paket veröffentlicht ist).
2. Legen Sie `settings/siteaccess/adminui/` als Kopie der Einstellungen des Admin-Siteaccess an und setzen Sie:

   ```ini
   [ExtensionSettings]
   ActiveAccessExtensions[]=exp_adminui

   [DesignSettings]
   SiteDesign=adminui
   AdditionalSiteDesignList[]
   AdditionalSiteDesignList[]=admin4l
   AdditionalSiteDesignList[]=admin4
   AdditionalSiteDesignList[]=admin3
   AdditionalSiteDesignList[]=admin2
   AdditionalSiteDesignList[]=admin
   ```

   Lassen Sie die `icon.ini.append.php` und `ezoe.ini.append.php` der Kopie weg (oder leeren Sie sie): Die Extension
   bringt ihre eigenen mit.
3. Fügen Sie `AvailableSiteAccessList[]=adminui` in `settings/override/site.ini.append.php` hinzu.
4. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, dann
   `--clear-id=template,template-override,template-block,content,translation`; unter Velocity außerdem
   `./console exp:velocity cache clear --allow-root-user`.

Öffnen Sie `https://<host>/adminui` und melden Sie sich wie bei der Administrationsoberfläche an.

## Einstellungen

`extension/exp_adminui/settings/exp_adminui.ini`: der Titel, der Logo-Typ, die Einträge der Seitenleiste
(`[MenuPlugins]`), die Navigationsteile ohne Pfadleiste, der Standardreiter der Knotenansicht und die Felder des
Code-Editors. Die Anleitung der Extension (`extension/exp_adminui/doc/`) beschreibt jede davon, die Architektur, jede
Ansicht und ihr Template, das Zusammenführen mit den Änderungen von Exponential, das Erweitern des Designs, bekannte
Lücken und die Fehlersuche.

## Gut zu wissen

- Unter 640 CSS-Pixeln Breite zeigt Admin UI „The current window is too small“, wie Netgen Admin UI.
- Die Extension ist ein Design mit Einstellungen: keine PHP-Klassen, keine Datenbanktabellen. Eine Änderung an Template
  oder Stylesheet braucht das Leeren der Caches, keinen Neustart des Servers.

## Verwandte Seiten

- [Das Design admin4](../../../eng-us/features/6.0/admin4-design.md) und [admin4l](../../../eng-us/features/6.0/admin4l.md)
- [Templates und Design](../../anleitungen/templates-und-design.md) (die Anleitung)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
