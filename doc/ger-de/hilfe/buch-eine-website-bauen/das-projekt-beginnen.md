---
title: "Das Projekt beginnen: der Fall, ein Siteaccess und ein Design"
audience: builder, designer, developer
category: books
order: 100
summary: Der Schachclub-Fall des Tutorials Building a Site und die Einrichtung, mit der es beginnt (ein Siteaccess, ein Design, eine Einstellungserweiterung und eine Designerweiterung), so umgesetzt, wie es in Exponential 6 üblich ist, statt die Ordner der Plain-Site umzubenennen.
keywords: Tutorial, Website bauen, Fall, Siteaccess, Design, Erweiterung, Override, Debug, tutorial, building a site, case, siteaccess, design, extension, site.ini, SiteDesign, VarDir, override, debug, Building book
type: help
---
# Das Projekt beginnen: der Fall, ein Siteaccess und ein Design

Das Tutorial *Building an eZ publish site* lehrt, indem es eine kleine Website aus dem Nichts baut. Diese Hilfe behält
seinen Fall und seine Reihenfolge bei und führt jeden Schritt so aus, wie er in Exponential 6 gemacht wird. Sie folgt
demselben Bogen wie [Eine Website bauen, ohne Code zu schreiben](../erweiterungsentwicklung/teil-2-builder/eine-website-ohne-code-bauen.md),
der kompakten Fassung; lesen Sie diese für den kürzesten Weg und diese Reihe für den längeren Gang mit den Seiten des
Buches (Startseite, Neuigkeiten, Mitglieder, Gästebuch mit Freigabe, Links).

## Der Fall

Ein örtlicher Schachclub, *The Scandinavian Check Mates* (TSCM), mit etwa vierzig Mitgliedern, die Schach lieben und mit
einem Computer umgehen können. Die alte Website des Clubs hat ein Mitglied gebaut, das weggezogen ist. Die Wunschliste:

- sie soll gut aussehen, aber nicht überladen sein,
- eine Seite mit **Neuigkeiten**,
- eine **Mitgliederliste**,
- ein **Gästebuch**,
- sie soll leicht zu pflegen und zu erweitern sein.

Die Lehre des Tutorials steckt im nächsten Satz: Eine solche Liste ist nur der Anfang; der Club wird mehr wollen, also bauen
Sie flexibel. Mit einem Inhaltsmodell und Templates sind das Gästebuch und die Mitgliederliste Klassen und Ansichten, kein
Code.

## Was das Buch tat und was Sie tun

Das Buch installierte die Site *Plain*, benannte ihre beiden Siteaccess-Ordner um (`plain` in `tscm`, `plain_admin` in
`tscm_admin`), bearbeitete deren `site.ini.append`, ersetzte jedes `plain` im globalen Override, entfernte `var/plain` und
legte `design/tscm` an. Benennen Sie die Ordner eines Pakets nicht um. Legen Sie eigene an, in Erweiterungen, damit ein
Update von Exponential oder eines Pakets Ihre Arbeit nie berührt.

1. **Installieren** Sie Exponential ([Erste Schritte](../../anleitungen/erste-schritte.md)). Jedes Seitenpaket genügt; die hier
   verwendeten Inhaltsklassen (`folder`, `article`) sind in allen enthalten.
2. **Zwei Erweiterungen**: eine für die Einstellungen (den Siteaccess) und eine für das Design. Eine
   Einstellungserweiterung hält den Siteaccess aus dem `settings/` des Kernels heraus:

   ```bash
   mkdir -p extension/exp_tscm_settings/settings/siteaccess/tscm
   mkdir -p extension/exp_tscm_design/settings
   mkdir -p extension/exp_tscm_design/design/tscm/{images,stylesheets,templates,override/templates}
   ```

   Geben Sie beiden eine `ezinfo.php` und eine `extension.xml` ([ezinfo.php](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ezinfo-php.md)).
3. **Registrieren Sie das Design** in `extension/exp_tscm_design/settings/design.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [ExtensionSettings]
   DesignExtensions[]=exp_tscm_design

   */ ?>
   ```
4. **Der Siteaccess** in `extension/exp_tscm_settings/settings/siteaccess/tscm/site.ini.append.php`: Die Einstellungen des
   Buches (`SiteName`, `SiteDesign`, `VarDir`) sind noch immer dieselben:

   ```ini
   [SiteSettings]
   SiteName=The Scandinavian Check Mates

   [DesignSettings]
   SiteDesign=tscm
   AdditionalSiteDesignList[]
   AdditionalSiteDesignList[]=base

   [FileSettings]
   VarDir=var/tscm
   ```

   Fügen Sie den Siteaccess der Liste im globalen Override hinzu (`[SiteAccessSettings] AvailableSiteAccessList[]=tscm`) und
   wählen Sie, wie er erreicht wird (URL, Port oder Hostname), wie im [Setup-Assistenten](../buch-lernen/der-setup-assistent-damals-und-heute.md).
   Die Adresse `[...]/index.php/tscm` des Buches ist die alte Form; heute kommen der Hostname oder der Pfad vom Webserver
   und von `MatchOrder` ([Siteaccess-Einstellungen](../erweiterungsentwicklung/teil-2-builder/siteaccess-einstellungen.md)).
5. **Aktivieren** Sie beide Erweiterungen, die Einstellungserweiterung zuerst ([Ladereihenfolge der Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ladereihenfolge-der-erweiterungen.md)):
   `ActiveExtensions[]=exp_tscm_settings` und `ActiveExtensions[]=exp_tscm_design`, danach
   `php bin/php/ezpgenerateautoloads.php -e` und die Caches leeren.
6. **Entwicklungseinstellungen** nur für Ihre eigene Kopie (das Buch schaltet die Caches aus und Debug ein und sagt das
   aus demselben Grund): in der Siteaccess-Datei `[DebugSettings] DebugOutput=enabled` und `[TemplateSettings] Debug=enabled`.
   Nie auf einer Live-Website ([Debug-Ausgabe und Logs für Entwickler](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md)).
7. **Testen.** Öffnen Sie die Website. Sie zeigt die Standard-Templates: Ihr leeres Design fällt auf das Standarddesign
   zurück, wie das Buch sagt („wenn das System etwas nicht findet, fällt es auf das Standarddesign zurück“). Melden Sie
   sich mit Ihrem eigenen Administratorkonto in der Administration an, nicht mit `admin` / `publish` aus dem Buch; die
   Passwörter einer neuen Installation legt der Installer fest ([In der Administration anmelden](../anmelden-in-der-administration.md)).

Caches leeren nach einer **neuen** Template-Datei: `php bin/php/ezcache.php --clear-id=template-override,design_base --allow-root-user`
([Welchen Cache leere ich?](../handbuch/welchen-cache-leere-ich.md)). Das Buch sagt dasselbe („tun Sie das jedes Mal, wenn
Sie ein Template hinzufügen“).

Weiter: [das Hauptlayout](das-hauptlayout.md).

## Quelle und Lizenz

Bearbeitet nach dem Abschnitt über das Anlegen einer neuen Website und das Hauptlayout des Tutorials *Building an eZ publish site*
(geschrieben für eZ publish 3.2, 2003), ursprünglich von ez.no, 2024 von 7x erneut unter der GNU Free Documentation License
veröffentlicht, [Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x und Graham Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren
Version, ohne unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: für Exponential 6 neu geschrieben: Erweiterungen statt umbenannter Ordner, aktuelle Einstellungen und Operatoren, keine Inline-Stile und ein moderner Seitenrahmen.
