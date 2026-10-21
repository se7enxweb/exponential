---
title: "Projektvorlagen: Projektauftrag, RACI, Checklisten, Livegang und Übergabe"
audience: project-manager
category: leadership
order: 140
summary: Kopierbare Vorlagen zum Ausfüllen für ein Exponential-Projekt - Projektauftrag, RACI-Matrix, Anforderungs-Checkliste, Livegang-Checkliste und Übergabedokument, jeweils verknüpft mit den tatsächlichen Funktionen und Seiten des Produkts.
keywords: Vorlage, Projektauftrag, Projektcharta, RACI, Verantwortungsmatrix, Anforderungen, Checkliste, Livegang, Go-live, Übergabe, Dokumentation, Formular, Projektmanagement, template, charter, project charter, responsibility matrix, requirements, checklist, launch, handover, hand-off, documentation, fill-in, form, project management
type: help
---
# Projektvorlagen: Projektauftrag, RACI, Checklisten, Livegang und Übergabe

Diese Seite richtet sich an Projektleiter, die ein Exponential-Projekt mit guten Unterlagen beginnen möchten statt mit
einem leeren Blatt. Sie enthält fünf Vorlagen zum Ausfüllen. Kopieren Sie einen Block in Ihr eigenes Dokument oder
Ticketsystem, ersetzen Sie jeden `<placeholder>` und löschen Sie, was nicht zutrifft.

Die Vorlagen sind allgemein, wo sie es sein müssen (Ihre Ziele, Ihre Leute, Ihre Termine), und konkret, wo das Produkt
Ihnen etwas zum Prüfen gibt: Jede Zeile, die eine Funktion nennt, verweist auf die Seite, die sie beschreibt, damit
die Person, die die Vorlage ausfüllt, weiß, wo sie nachsehen kann.

## Inhalt

1. [So verwenden Sie die Vorlagen](#so-verwenden-sie-die-vorlagen)
2. [Projektauftrag](#projektauftrag)
3. [RACI-Matrix](#raci-matrix)
4. [Anforderungs-Checkliste](#anforderungs-checkliste)
5. [Livegang-Checkliste](#livegang-checkliste)
6. [Übergabedokument](#übergabedokument)

## So verwenden Sie die Vorlagen

- **Kopieren** Sie den Textblock; er ist reiner Text, damit er sich überall einfügen lässt.
- **Füllen Sie** jeden `<placeholder>` **aus**. Lassen Sie in einem unterschriebenen Dokument keinen Platzhalter
  stehen; schreiben Sie stattdessen „nicht zutreffend“.
- **Nennen Sie Rollen, nicht nur Personen.** Personen wechseln während eines Projekts, Rollen bleiben. Verwenden Sie
  die Rollenbezeichnungen Ihrer Organisation und führen Sie eine separate Liste, wer welche Rolle innehat.
- **Bewahren Sie Versionen auf.** Legen Sie jede ausgefüllte Vorlage neben dem Code des Projekts unter
  Versionsverwaltung oder mit Datum in Ihrem Dokumentensystem ab.
- **Fügen Sie keine Geheimnisse ein.** Passwörter, Schlüssel und Zugangsdaten zu Servern gehören nie in diese
  Dokumente. Schreiben Sie, wo sie aufbewahrt werden, nicht, wie sie lauten.

## Projektauftrag

Der Projektauftrag ist die Vereinbarung zu Beginn: warum es das Projekt gibt, was es liefert und woran alle erkennen,
dass es abgeschlossen ist. Füllen Sie ihn während der Analyse aus ([Ein Exponential-Projekt planen](ein-exponential-projekt-planen.md))
und lassen Sie ihn unterzeichnen, bevor die Umsetzung beginnt.

```text
PROJECT CHARTER
Project name:            <project name>
Version / date:          <version>, <date>
Sponsor:                 <role of the person who funds and decides>
Project manager:         <role>
Product owner:           <role of the person who accepts the result>

1. Purpose
   Why this project exists, in two or three sentences:
   <purpose>

2. Goals and measures of success
   | Goal                         | Measure                          | Target   | Where it is measured          |
   |------------------------------|----------------------------------|----------|-------------------------------|
   | <goal>                       | <measure>                        | <target> | <page, log or report>         |
   (For measures the product can report itself, see "Measuring a project: KPIs from the
    product's own logs and audit trail".)

3. Scope
   In scope:
   - Sites (siteaccesses):            <list>
   - Languages:                       <list of locales, e.g. eng-US, ger-DE, ara-SA>
   - Content classes (first model):   <list or "see content model document">
   - Features:                        <multisite, layouts editor, shop, newsletter, REST API, ...>
   - Extensions:                      <list, own and third-party>
   - Content migration:               <source, volume, method>
   Out of scope:
   - <item>

4. Platform decisions
   Exponential version:     <release tag, e.g. v6.0.14, or main with reason>
   PHP version:             <version>
   Database:                <MySQL/MariaDB | PostgreSQL | SQLite | MongoDB | Oracle>
   Operating system:        <OS>
   Web server / engine:     <Exponential Velocity | Apache with PHP-FPM | FrankenPHP | nginx with PHP-FPM>
   Hosting:                 <self-hosted / provider>
   Code in Git:             <repositories, site branch name>
   Configuration from version control (ManagedByDeployment): <yes/no per siteaccess>

5. Deliverables
   - <deliverable>          Owner: <role>   Due: <date>

6. Milestones and gates
   | Gate                     | Date    | Checks                                  | Owner  |
   |--------------------------|---------|-----------------------------------------|--------|
   | G1 Change ready          | ongoing | <definition of done>                    | <role> |
   | G2 Staging ready         | <date>  | <checks>                                | <role> |
   | G3 Acceptance            | <date>  | <acceptance checklist signed>           | <role> |
   | G4 Live                  | <date>  | <launch checklist done>                 | <role> |

7. Quality requirements
   Accessibility:           <standard and level, e.g. WCAG 2.x AA; how it is checked>
   Security:                <hardening checklist, CSP mode, two-step sign-in for administrators>
   Performance:             <how measured, e.g. exp:benchmark baseline>
   Privacy:                 <questions answered with the organization's data protection contact>

8. Assumptions and constraints
   - <assumption>

9. Risks (top five; full register separate)
   - <risk>: <mitigation>

10. Communication
    Weekly status: <day, audience>   Steering: <rhythm>   Channel: <tool>

11. Contribution to the open source project
    Policy: <link to the team contribution policy>   Time reserved: <hours per sprint>

Signatures:  Sponsor <name/role, date>   Product owner <name/role, date>   Project manager <name/role, date>
```

## RACI-Matrix

R = Responsible (führt die Arbeit aus), A = Accountable (eine Person je Zeile, entscheidet), C = Consulted (wird
konsultiert), I = Informed (wird informiert).

Die Zeilen nennen die tatsächlichen Bereiche der Administration und die tatsächlichen Aufgaben eines
Exponential-Projekts. Wer eine Aufgabe im Produkt ausführen darf, legen **Rollen und Richtlinien** fest
(**Benutzer > Rollen und Richtlinien**, [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)); diese Matrix ist
die organisatorische Seite. Halten Sie beides im Einklang: Wer für eine Aufgabe „R“ ist, braucht eine Rolle, deren
Richtlinien sie erlauben, und niemand sollte die Rolle Administrator nur aus Bequemlichkeit innehaben.

```text
RACI MATRIX: <project name>          Version: <date>

Columns:  PM = project manager   PO = product owner   ED = editors   AD = administrator
          DEV = developers       DES = designer       SM = sales and marketing   HOST = hosting provider

| Task                                                        | PM | PO | ED | AD | DEV | DES | SM | HOST |
|-------------------------------------------------------------|----|----|----|----|-----|-----|----|------|
| Project charter and plan                                    | A  | C  | I  | C  | C   | I   | C  | I    |
| Requirements and scenarios                                  | R  | A  | C  | C  | C   | C   | C  |      |
| Content model (classes, attributes)                         | R  | A  | C  |    | R   | C   |    |      |
| Sections, object states and workflows                       | C  | A  | C  | R  | R   |     |    |      |
| Roles and policies                                          | C  | A  | C  | R  | C   |     |    |      |
| Siteaccesses, languages and URLs                            | C  | A  |    | R  | R   |     | C  |      |
| Design and templates                                        | C  | A  | C  |    | R   | R   | C  |      |
| Layouts (layouts editor)                                    | I  | A  | R  |    | C   | C   | C  |      |
| Extensions: choice and installation                         | C  | A  |    | R  | R   |     |    |      |
| Own extensions: build, test, release                        | I  | I  |    | C  | R/A |     |    |      |
| Content migration                                           | A  | C  | R  | C  | R   |     |    |      |
| Server, PHP, database, web server                           | I  | I  |    | A  | C   |     |    | R    |
| Deployments (staging, production)                           | I  | I  |    | R/A| C   |     |    | C    |
| Cronjobs                                                    | I  |    |    | R/A| C   |     |    |      |
| Backups and restore tests                                   | I  | I  |    | R/A|     |     |    | C    |
| Security hardening and CSP                                  | I  | C  |    | R/A| R   |     |    | C    |
| Audit trail: alerts, recipients, review                     | I  | C  |    | R/A|     |     |    |      |
| Accessibility checks                                        | C  | A  | C  |    | R   | R   |    |      |
| Acceptance                                                  | R  | A  | R  | C  | C   | C   | C  |      |
| Launch go/no-go                                             | R  | A  | I  | C  | C   | I   | I  | C    |
| Launch announcement                                         | C  | A  | I  |    |     |     | R  |      |
| Editor training                                             | A  | C  | I  | C  | R   |     |    |      |
| Upgrades                                                    | A  | I  | I  | R  | R   |     |    | C    |
| Bug reports to the Exponential project                      | I  |    | C  | R  | R   |     |    |      |
| Handover document                                           | R/A| C  | I  | R  | R   |     |    |      |
```

Seiten zu den Zeilen: [Inhaltsmodell und Bearbeiten](../../anleitungen/inhaltsmodell-und-bearbeitung.md),
[Sektionen](../../anleitungen/sektionen.md), [Objektzustände](../../anleitungen/objektzustaende.md),
[Workflows](../../anleitungen/workflows.md), [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md),
[Templates und Design](../../anleitungen/templates-und-design.md), [Der Layout-Editor](../layout-editor.md),
[Erweiterungen](../../anleitungen/extensions.md), [Deployment](../../anleitungen/bereitstellung.md), [Cronjobs](../../anleitungen/cronjobs.md),
[Eine Site betreiben](../../anleitungen/betrieb-einer-website.md), [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md),
[Upgrade](../../anleitungen/aktualisieren.md), [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md).

## Anforderungs-Checkliste

Gehen Sie sie in der Analysephase mit dem Product Owner durch. Jede Zeile ist eine Frage; die letzte Spalte nennt die
Seite, auf der das Team findet, wie Exponential sie beantwortet. Markieren Sie jede Zeile mit **Notwendig**,
**Wünschenswert** oder **Nein** (in der Vorlage N, H und -).

```text
REQUIREMENTS CHECKLIST: <project name>          Date: <date>     With: <roles>

Mark: N = need, H = nice to have, - = no

Content
[ ] _ Structured content types of our own (classes with fields)           -> guides/content-model-and-editing.md
[ ] _ Versions, drafts, preview, going back to an older version            -> help/publishing-and-versions.md
[ ] _ Approval before publishing / scheduled publishing                    -> guides/workflows.md
[ ] _ Editors build pages from blocks themselves                           -> help/layouts-editor.md
[ ] _ Rich text editing                                                    -> help/editing-content.md
[ ] _ Images and files, uploads in bulk                                    -> help/media-and-uploads.md
[ ] _ Tags / keywords                                                      -> features/6.0/extensions/eztags.md
[ ] _ Search                                                               -> help/searching.md
[ ] _ RSS feeds / podcasts                                                 -> guides/rss-feeds.md
[ ] _ PDF export                                                           -> guides/pdf-exports.md
[ ] _ Help writing and translating content (Exponential Intelligence)      -> features/6.0/extensions/expai.md

Sites and languages
[ ] _ Several sites from one installation (siteaccesses)                   -> leadership/multisite-and-multilingual-planning.md
[ ] _ Content in several languages: <list>                                 -> guides/content-languages.md, languages.md
[ ] _ Right-to-left languages                                              -> features/6.0/right-to-left.md
[ ] _ Administration in a language other than English: <language>          -> languages.md

Users and access
[ ] _ Editor roles with limited rights                                     -> guides/roles-and-policies.md
[ ] _ Visitor registration                                                 -> guides/roles-and-policies.md
[ ] _ Two-step sign-in                                                     -> guides/two-factor-login.md
[ ] _ Record of who did what (audit trail)                                 -> features/6.0/audit-trail.md

Commerce and communication
[ ] _ Shop: products, basket, orders                                       -> features/6.0/store-dashboard.md
[ ] _ Newsletters                                                          -> features/6.0/extensions/cjw_newsletter.md
[ ] _ Notifications by e-mail                                              -> guides/notifications-administrator.md
[ ] _ E-mail preferences and consent                                       -> features/6.0/mail-preferences.md

Integration
[ ] _ REST API for apps or other systems                                   -> guides/rest-api-v1-and-v2.md
[ ] _ API keys                                                             -> guides/api-keys.md
[ ] _ Remote services                                                      -> guides/remote-services-and-apps.md

Operations
[ ] _ Database: <engine>                                                   -> help/database-support.md
[ ] _ Operating system: <OS>                                               -> help/os-support.md
[ ] _ Web server / Velocity                                                -> guides/deploying.md
[ ] _ Configuration only from version control                              -> features/6.0/managed-by-deployment.md
[ ] _ Maintenance page during work                                         -> features/6.0/maintenance-mode.md
[ ] _ Backups and restore                                                  -> guides/operating-a-site.md
[ ] _ Monitoring and health checks                                         -> guides/system-information.md

Quality
[ ] _ Accessibility level: <level>                                         -> leadership/accessibility-security-and-compliance-checklists.md
[ ] _ Content Security Policy enforced                                     -> features/6.0/content-security-policy.md
[ ] _ Security hardening checklist                                         -> help/security-hardening.md
[ ] _ Performance measured                                                 -> guides/benchmarking.md

Open questions (answer with the organization's own experts)
[ ] Privacy: which personal data is stored, for how long, who may see it?
[ ] Legal: accessibility obligations, imprint, consent requirements?
[ ] Hosting: where must data be stored?
```

Die Pfade sind relativ zu `doc/` im Repository; in dieser Hilfe ist jeder davon eine Seite, die Sie über die Suche
öffnen können. Die Links: [Inhaltsmodell und Bearbeiten](../../anleitungen/inhaltsmodell-und-bearbeitung.md),
[Veröffentlichen und Versionen](../veroeffentlichen-und-versionen.md), [Workflows](../../anleitungen/workflows.md),
[Layout-Editor](../layout-editor.md), [Inhalte bearbeiten](../inhalte-bearbeiten.md),
[Medien und Uploads](../medien-und-uploads.md), [eztags](../../funktionen/6.0/erweiterungen/eztags.md),
[Suchen](../suchen.md), [RSS-Feeds](../../anleitungen/rss-feeds.md), [PDF-Exporte](../../anleitungen/pdf-exporte.md),
[expai](../../funktionen/6.0/erweiterungen/expai.md), [Multisite- und mehrsprachige Projekte planen](multisite-und-mehrsprachigkeit-planen.md),
[Inhaltssprachen](../../anleitungen/inhaltssprachen.md), [Sprachen](../../sprachen.md),
[Von rechts nach links](../../funktionen/6.0/rechts-nach-links.md), [Zweistufige Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md),
[Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md), [Shop-Dashboard](../../funktionen/6.0/shop-dashboard.md),
[cjw_newsletter](../../funktionen/6.0/erweiterungen/cjw_newsletter.md),
[Benachrichtigungen](../../anleitungen/benachrichtigungen-administrator.md), [E-Mail-Einstellungen](../../funktionen/6.0/e-mail-einstellungen.md),
[REST API](../../anleitungen/rest-api-v1-und-v2.md), [API-Schlüssel](../../anleitungen/api-schluessel.md),
[Remote-Dienste](../../anleitungen/entfernte-dienste-und-apps.md), [Datenbankunterstützung](../datenbankunterstuetzung.md),
[Betriebssystemunterstützung](../betriebssystemunterstuetzung.md), [Deployment](../../anleitungen/bereitstellung.md),
[ManagedByDeployment](../../funktionen/6.0/durch-deployment-verwaltet.md), [Wartungsmodus](../../funktionen/6.0/wartungsmodus.md),
[Eine Site betreiben](../../anleitungen/betrieb-einer-website.md), [Systeminformationen](../../anleitungen/systeminformationen.md),
[Compliance-Checklisten](checklisten-barrierefreiheit-sicherheit-und-compliance.md),
[Content Security Policy](../../funktionen/6.0/content-security-policy.md), [Sicherheitshärtung](../sicherheitshaertung.md),
[Benchmarking](../../anleitungen/benchmarking.md).

## Livegang-Checkliste

Die Checkliste fasst die Go-live-Checkliste des Installationshandbuchs, die Checkliste für die erste Woche und die
Sicherheits-Checkliste der Hilfe zusammen. Wo eine Zeile von einer Seite stammt, steht die Seite hinter dem Pfeil.

```text
LAUNCH CHECKLIST: <project name>          Launch date: <date>     Go/no-go: <role>

Two weeks before
[ ] Acceptance signed                                                    -> Quality gates and acceptance
[ ] Launch window and rollback decision owner agreed
[ ] Editors trained; each has a personal account with the right role     -> Roles and policies
[ ] Content migration finished; content checks show nothing unexplained  -> Content checks
[ ] Security hardening checklist worked through                          -> Security hardening, Checklist
[ ] Go-live checklist of the installation book worked through            -> 13.16 Go-live checklist
[ ] CSP reports reviewed; mode decided (report-only / enforce)           -> Content Security Policy
[ ] Performance baseline saved (var/benchmark/)                          -> Benchmarking; 10.15
[ ] Announcement prepared                                                -> Sales and marketing

One day before
[ ] Backup of database and files taken and restored on another machine   -> Operating a site, Back up and restore
[ ] Upgrade check (files and database) run and saved                     -> The upgrade check
[ ] System information: no failure; warnings explained                   -> System information
[ ] Crontab ready: frequent, the default part, infrequent, cache_cleanup -> 10.15, Cronjobs

Launch
[ ] Maintenance mode on if the switch needs it                           -> Maintenance mode
[ ] Final deploy done; every step PASS (exp:velocity deploy)             -> Deploying, section 7
[ ] Administrator password changed; var/log/initial-admin-password gone  -> 10.15
[ ] Debug output off, display_errors=Off                                 -> 13.4
[ ] HTTPS everywhere; secure cookies                                     -> Security hardening, Checklist
[ ] AdminEmail and mail transport set; a test mail arrived in a real inbox -> 10.4, 10.15
[ ] SiteURL set per siteaccess                                           -> System information, Health checks
[ ] Search index complete                                                -> 10.8
[ ] Maintenance mode off
[ ] Smoke test: front page, every siteaccess, every language, sign-in, publish a test item

First week
[ ] Cron logs grow; System information shows recent cronjob runs        -> Cronjobs
[ ] Audit: frequent group runs; alert recipients are real people; chains intact -> Audit trail
[ ] Error digest read daily; security.log looked at                      -> Log viewer, Request logs
[ ] Logs rotate                                                          -> 10.15
[ ] Lessons learned recorded; handover document updated
```

Seiten: [Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md), [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md),
[Inhaltsprüfungen](../../funktionen/6.0/inhaltspruefungen.md), [Sicherheitshärtung](../sicherheitshaertung.md#checkliste),
[13.16 Go-live-Checkliste](../../installation/13-sicherheitshaertung.md#1316-checkliste-für-den-livegang),
[Content Security Policy](../../funktionen/6.0/content-security-policy.md), [Benchmarking](../../anleitungen/benchmarking.md),
[10.15 Eine Checkliste für die erste Woche](../../installation/10-nach-der-installation.md#1015-eine-checkliste-für-die-erste-woche),
[Vertrieb und Marketing: hier beginnen](../geschaeft/vertrieb-und-marketing-hier-beginnen.md),
[Eine Site betreiben](../../anleitungen/betrieb-einer-website.md#6-sichern-und-wiederherstellen), [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md),
[Systeminformationen](../../anleitungen/systeminformationen.md#3-zustandsprüfungen), [Cronjobs](../../anleitungen/cronjobs.md),
[Wartungsmodus](../../funktionen/6.0/wartungsmodus.md), [Deployment](../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern),
[13.4](../../installation/13-sicherheitshaertung.md#134-debug-ausgabe-und-fehleranzeige),
[10.4](../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand),
[10.8](../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung),
[Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md), [Log-Viewer](../../funktionen/6.0/log-betrachter.md),
[Request-Logs](../../funktionen/6.0/anfrageprotokolle.md).

## Übergabedokument

Mit dem Übergabedokument kann ein neues Team oder das eigene Personal des Kunden die Site betreiben und verändern, ohne
die Personen fragen zu müssen, die sie gebaut haben. Beginnen Sie es in der ersten Woche und halten Sie es aktuell;
eine Übergabe, die erst in der letzten Woche geschrieben wird, ist eine Gedächtnisübung.

```text
HANDOVER DOCUMENT: <site name>          Version: <date>     Prepared by: <role>     Received by: <role>

1. Overview
   Purpose of the site:          <one paragraph>
   Sites (siteaccesses):         <name: address, language(s), design>
   Administration address:       <address of the admin siteaccess>
   Owners:                       Product owner <role>, technical owner <role>

2. Versions (check: Setup > System information; Setup > Extensions)
   Exponential:                  <version as System information shows it>
   PHP:                          <version>        Database: <engine and version>
   Web server / engine:          <Velocity / Apache with PHP-FPM / ...>
   Extensions:                   <name: version, own or third-party, repository>

3. Code and repositories
   Kernel branch:                <branch name>, based on <tag or main>
   Own repositories:             <list with purpose>
   Local patches still carried:  <list with reason and upstream issue, or "none">
   Release owner per repository: <role>
   How to deploy:                <command and order, e.g. exp:velocity deploy>

4. Configuration
   Settings in version control:  <where>     ManagedByDeployment: <per siteaccess>
   Where secrets are kept:       <place, never the values>
   Audit keys:                   settings/override/audit.ini.append.php backed up to <place>

5. Content model
   Classes and their purpose:    <list or link>
   Sections, object states:      <list>
   Workflows:                    <list>
   Languages:                    <list, main language>
   Layouts:                      <shared layouts, rules>

6. Users and access
   Roles and what they are for:  <role: purpose, assigned to which groups>
   Administrators:               <roles of people, personal accounts only>
   Two-step sign-in:             <who must use it>
   Break-glass procedure:        <where it is written>

7. Operations
   Cronjobs:                     <crontab lines or groups, schedule>
   Backups:                      <what, when, where, retention>; last restore test: <date>
   Logs:                         <rotation, who reads the error digest, how often>
   Audit:                        <alert recipients (roles), sinks, verify schedule>
   Monitoring:                   <checks and who receives alerts>
   Mail:                         <transport, sender>

8. Quality
   Tests:                        <suites or test files that cover own code>
   Performance baseline:         <file and date>
   CSP:                          <mode per siteaccess>
   Accessibility:                <what was checked, when, result>

9. Known issues and decisions
   | Issue / decision | Status | Upstream link | Owner |

10. Upgrades
   Next planned upgrade:         <target version, date>
   Notes to read:                <changelogs and behavior change notes since the running version>

11. Where to get help
   This help (Help in the administration), the forums, the issue trackers   -> When something goes wrong
   Security problems: privately, as SECURITY.md describes

Sign-off:  Handed over by <role, date>   Received by <role, date>
```

Seiten zu den Abschnitten: [Systeminformationen](../../anleitungen/systeminformationen.md),
[Die Seite Extensions](../../anleitungen/extensions-seite.md), [Ein Team mit Git führen](ein-team-mit-git-fuehren.md),
[ManagedByDeployment](../../funktionen/6.0/durch-deployment-verwaltet.md), [Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md),
[Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md), [Cronjobs](../../anleitungen/cronjobs.md),
[Eine Site betreiben](../../anleitungen/betrieb-einer-website.md), [Release- und Upgrade-Planung](release-und-upgrade-planung.md),
[Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md).

## Verwandte Seiten

- [Ein Exponential-Projekt planen](ein-exponential-projekt-planen.md), [Projektleiter: hier beginnen](projektleiter-hier-beginnen.md)
- [Risikomanagement für Exponential-Projekte](risikomanagement.md): ein Register zum Kopieren
- [Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md): Definition of Done und Abnahme-Checkliste
- [Zusammenarbeiten](zusammenarbeiten.md): Übergaben zwischen Rollen
- [Ein Projekt messen: Kennzahlen aus den eigenen Logs und dem Audit-Protokoll des Produkts](kennzahlen-aus-logs-und-audit.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
