---
title: Ein Exponential-Projekt planen
audience: project-manager
category: leadership
order: 30
summary: Die Phasen eines Exponential-Projekts von der Analyse bis zum Betrieb, die Projektrollen abgebildet auf die tatsächlichen Rollen und Richtlinien und eine Anforderungstabelle, die jedem Bedarf die Funktion zuordnet, die ihn erfüllt.
keywords: Projektplan, Phasen, Analyse, Inhaltsmodell, Umsetzung, Migration, Inhaltserfassung, Qualitätssicherung, Livegang, Betrieb, Rollen, Richtlinien, Anforderungen, RACI, Planung, Projektleiter, project plan, phases, discovery, content model, build, migration, content entry, QA, launch, operate, roles, policies, requirements, planning, project manager
type: help
---
# Ein Exponential-Projekt planen

Diese Seite richtet sich an Projektleiter, die ein Website- oder Anwendungsprojekt mit Exponential 6 planen. Sie
beantwortet folgende Fragen: Welche Phasen durchläuft ein Projekt, was liefert jede Phase und woran erkennen Sie, dass
sie abgeschlossen ist? Wie werden die Personen Ihres Projekts auf die Rollen und Richtlinien der Administration
abgebildet, und wie machen Sie aus Anforderungen die Funktionen und Hilfeseiten, die sie erfüllen? Stunden und Preise
finden Sie hier nicht; diese schätzt Ihr Team mit
[Schätzen und Abgrenzen mit Erweiterungen](schaetzen-und-abgrenzen-mit-erweiterungen.md).

## Inhalt

1. [Die Phasen im Überblick](#die-phasen-im-überblick)
2. [Phase 1: Analyse](#phase-1-analyse)
3. [Phase 2: Inhaltsmodell](#phase-2-inhaltsmodell)
4. [Phase 3: Umsetzung](#phase-3-umsetzung)
5. [Phase 4: Inhaltsmigration und -erfassung](#phase-4-inhaltsmigration-und--erfassung)
6. [Phase 5: Qualitätssicherung](#phase-5-qualitätssicherung)
7. [Phase 6: Livegang](#phase-6-livegang)
8. [Phase 7: Betrieb](#phase-7-betrieb)
9. [Personen und die Rollen der Administration](#personen-und-die-rollen-der-administration)
10. [Anforderungen erheben: Bedarf, Funktion, Seite](#anforderungen-erheben-bedarf-funktion-seite)
11. [Ein durchgespieltes Beispiel](#ein-durchgespieltes-beispiel)

## Die Phasen im Überblick

| Phase | Leitfrage | Ergebnis | Verantwortlich | Abschlussprüfung |
|---|---|---|---|---|
| 1. Analyse | Was bauen wir, für wen und worauf? | Projektauftrag, Anforderungsliste, Plattformentscheidung | Projektleiter | Der Auftraggeber unterzeichnet den Projektauftrag |
| 2. Inhaltsmodell | Welche Inhaltstypen gibt es, und wer darf was damit tun? | Klassenliste, Sektions- und Zustandsplan, Rollenplan | Projektleiter mit Redakteuren und Entwicklern | Workshop-Ergebnisse freigegeben |
| 3. Umsetzung | Tut die Site, was vereinbart wurde? | Klassen, Templates, Erweiterungen, Einstellungen in Git | Entwickler | Qualitätsschranken bestanden |
| 4. Inhaltsmigration und -erfassung | Sind die Inhalte vollständig und korrekt eingepflegt? | Migrierte und erfasste Inhalte | Redakteure, Entwickler für Importe | Inhaltsprüfungen ohne Befund |
| 5. Qualitätssicherung | Ist die Site barrierefrei, sicher und schnell genug? | Testberichte, unterzeichnete Abnahmeliste | Projektleiter mit Testern | Abnahme unterzeichnet |
| 6. Livegang | Können wir sicher umschalten? | Go-live-Checkliste abgearbeitet, Site live | Administrator | Smoke-Test bestanden |
| 7. Betrieb | Bleibt die Site gesund und aktuell? | Betriebsroutine, Update-Plan | Administrator | Übergabe angenommen |

In der Praxis überlappen sich die Phasen: Die Inhaltserfassung beginnt oft, während die Umsetzung noch läuft, und eine
erste Prüfung der Barrierefreiheit gehört in die Umsetzung, nicht erst in die Qualitätssicherung. Behalten Sie die
Abschlussprüfungen trotzdem bei; nach ihnen fragt ein Lenkungsausschuss.

## Phase 1: Analyse

**Ziel.** Ein Projektauftrag, der Produkt, Umfang, Beteiligte, Plattform und Risiken benennt.

Schritte:

1. **Wählen Sie das Produkt der Familie.** Exponential 6, eine Platform-Distribution oder Exponential Basic
   ([Was Exponential ist](was-exponential-ist.md#ein-produkt-für-ein-projekt-wählen)).
2. **Neue Site oder Upgrade?** Eine Site auf einer älteren Produktlinie wird aktualisiert oder migriert; die Hilfe hat
   für jeden Ausgangspunkt ein Kapitel ([Upgrade](../../anleitungen/aktualisieren.md), Kapitel
   [14](../../installation/14-migration-von-4x.md), [15](../../installation/15-migration-von-5x-legacy.md) und
   [16](../../installation/16-migration-von-ez-platform-und-ibexa.md) des Installationshandbuchs).
3. **Zählen Sie Sites und Sprachen.** Eine Installation kann mehrere Sites und Sprachen bedienen
   ([Multisite- und mehrsprachige Projekte planen](multisite-und-mehrsprachigkeit-planen.md)).
4. **Entscheiden Sie über die Plattform** zusammen mit Ihrem Administrator
   ([Hosting, Datenbank und Betriebssystem wählen](hosting-datenbank-und-betriebssystem-waehlen.md)).
5. **Sammeln Sie die Anforderungen** in der Tabelle im [Anforderungsabschnitt dieser Seite](#anforderungen-erheben-bedarf-funktion-seite).
6. **Listen Sie die Erweiterungen auf**, die Sie verwenden, und die, die Sie selbst entwickeln
   ([Schätzen und Abgrenzen mit Erweiterungen](schaetzen-und-abgrenzen-mit-erweiterungen.md)).
7. **Legen Sie das Risikoregister an** ([Risikomanagement](risikomanagement.md)).
8. **Schreiben Sie den Projektauftrag** anhand der [Projektvorlagen](projektvorlagen.md).

Checkliste:

- [ ] Es gibt eine Demonstrationsinstallation, die sich das Team ansehen kann (`./console exp:install`,
      [Exponential mit einem Befehl installieren](../../../en/us/features/6.0/install-in-one-command.md)).
- [ ] Der Auftraggeber hat die Administration gesehen, nicht nur Folien.
- [ ] Produkt, Datenbank, Webserver und Installationsmethode stehen im Projektauftrag.
- [ ] Die Herkunft der Inhalte (alte Site, Tabellen, neu geschriebene Texte) ist bekannt.
- [ ] Anforderungen an Barrierefreiheit, Sicherheit und Datenschutz sind aufgelistet
      ([Checklisten](checklisten-barrierefreiheit-sicherheit-und-compliance.md)).

## Phase 2: Inhaltsmodell

**Ziel.** Eine abgestimmte Liste der Inhaltsklassen und ihrer Attribute, die Festlegung, wo Inhalte im Baum liegen,
welche Sektionen und Zustände es gibt und wer was tun darf.

Die Methode beschreibt [Workshops zur Inhaltsmodellierung durchführen](workshops-zur-inhaltsmodellierung.md). Die Begriffe
stammen aus [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md):

| Zu entscheiden | Begriff im Produkt | Lesen |
|---|---|---|
| Die Inhaltstypen | Inhaltsklassen und ihre Attribute (Datentypen) | [Inhaltsmodell, Abschnitt 1](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| Wo Inhalte liegen | Der Inhaltsbaum; ein Objekt kann mehrere Orte haben | ebenda |
| Welche Inhaltsgruppen unterschiedliche Rechte haben | Sektionen | [Sektionen](../../anleitungen/sektionen.md) |
| Welche Stufen Inhalte durchlaufen | Objektzustände | [Objektzustände](../../anleitungen/objektzustaende.md) |
| Wer vor der Veröffentlichung freigibt | Workflows mit einem Ereignis „Freigeben“ (Approve) | [Workflows](../../anleitungen/workflows.md) |
| Welche Sprachen | Inhaltssprachen je Siteaccess | [Inhaltssprachen](../../anleitungen/inhaltssprachen.md) |
| Was Redakteure selbst anordnen | Layouts, Zonen und Blöcke | [Der Layout-Editor](../layout-editor.md) |

Abschlussprüfung: Klassenliste, Sektionsplan und Rollenplan sind schriftlich festgehalten und von der leitenden
Redaktion und dem leitenden Entwickler freigegeben.

## Phase 3: Umsetzung

**Ziel.** Eine funktionierende Site unter Versionsverwaltung, gebaut mit den Qualitätsschranken des Teams.

Was Entwickler bauen und wo die Hilfe es erklärt:

| Arbeit | Lesen |
|---|---|
| Kernel und Erweiterungen als Git-Klone installieren | [Mit Git entwickeln](../mit-git-entwickeln.md) |
| Klassen, Attribute und Objekte | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| Templates und das Design | [Templates und Design](../../anleitungen/templates-und-design.md) |
| Eigene Erweiterungen, generiert oder von Hand | [Erweiterungen](../../anleitungen/extensions.md), [RAD-Werkzeuge](../../../en/us/features/6.0/rad-tools.md) |
| Integrationen | [REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md) |
| Ausliefern und Deployment | [Deployment](../../anleitungen/bereitstellung.md), [Kapitel 8](../../installation/08-die-website-ausliefern.md) |

Checkliste für den Projektleiter:

- [ ] Jede Änderung läuft über Git, auf einem Branch, mit einem Pull-Request ([Ein Team mit Git führen](ein-team-mit-git-fuehren.md)).
- [ ] Einstellungen, die nicht in der Administration geändert werden dürfen, sind versioniert; `ManagedByDeployment`
      kann verhindern, dass die Administration sie schreibt ([Vom Deployment verwaltete Konfiguration](../../../en/us/features/6.0/managed-by-deployment.md)).
- [ ] Geheimnisse in `settings/override` bleiben außerhalb der Versionsverwaltung ([Kapitel 13](../../installation/13-sicherheitshaertung.md)).
- [ ] Die Qualitätsschranken sind vereinbart und werden ausgeführt ([Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md)).
- [ ] Neben der Entwicklungsumgebung gibt es eine Test- oder Staging-Installation.

## Phase 4: Inhaltsmigration und -erfassung

**Ziel.** Alle Inhalte sind eingepflegt, in den richtigen Klassen, Sprachen, Sektionen und Zuständen.

| Herkunft der Inhalte | Werkzeuge, die die Hilfe beschreibt |
|---|---|
| Von Redakteuren eingegeben | [Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md), [Bilder, Medien und Uploads](../medien-und-uploads.md) (viele Dateien auf einmal) |
| Tabellen oder Dateien | `xrowextract`: Inhalte als Dateien exportieren, importieren und verschieben ([xrowextract](../../../en/us/features/6.0/extensions/xrowextract.md)) |
| Eine andere Installation | Pakete: Klassen, Inhalte und Erweiterungen als `.ezpkg`-Dateien übertragen ([Pakete](../../anleitungen/pakete.md)) |
| Eine ältere Exponential-Site | Die Kapitel zu Upgrade und Migration ([Upgrade](../../anleitungen/aktualisieren.md)) |
| Ein externes System | REST API ([REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md)) |
| Hilfe beim Schreiben | Exponential Intelligence schreibt Entwürfe, die Redakteure prüfen ([expai](../../../en/us/features/6.0/extensions/expai.md)) |

Umfangreiches Entfernen, Kopieren und Verschieben läuft als Inhaltsjob im Hintergrund, in Stapeln, die nach einer
Unterbrechung weiterlaufen ([Inhaltsjobs](../../../en/us/features/6.0/content-jobs.md)).

Checkliste:

- [ ] Eine Inhaltsinventur mit einem Verantwortlichen je Bereich.
- [ ] Ein Testimport auf der Testinstallation vor dem echten Import.
- [ ] Nach dem Import laufen die Inhaltsprüfungen: nicht referenzierte Objekte, defekte Relationen, Speicherdateien
      ([Inhaltsprüfungen](../../../en/us/features/6.0/content-checks.md)).
- [ ] Alternativtexte für Bilder: Bildfelder können sie verpflichtend machen
      ([Inhaltsmodell, Abschnitt 9](../../anleitungen/inhaltsmodell-und-bearbeitung.md)).
- [ ] Übersetzungen vollständig für jede Sprache, die ein Siteaccess anzeigt.

## Phase 5: Qualitätssicherung

Verwenden Sie [Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md) für die Schranken und
[Checklisten zu Barrierefreiheit, Sicherheit und Compliance](checklisten-barrierefreiheit-sicherheit-und-compliance.md) für
die Prüfungen. Ergänzen Sie eine Leistungsprüfung: Der Befehl `exp:benchmark` misst die Antwortzeiten von Seiten
([Benchmark](../../../en/us/features/6.0/benchmark.md)), und Abschnitt 2.10 des Kapitels zu den Systemanforderungen erklärt,
warum die Dokumentation nur wenige feste Zahlen nennt ([Arbeitsspeicher, CPU und Festplatte](../../installation/02-voraussetzungen.md#210-arbeitsspeicher-cpu-und-festplatte)).

## Phase 6: Livegang

Der Administrator arbeitet die Go-live-Checkliste des Installationshandbuchs ab
([13.16 Go-live-Checkliste](../../installation/13-sicherheitshaertung.md#1316-checkliste-für-den-livegang)) sowie die
Härtungs-Checkliste dieser Hilfe ([Sicherheitshärtung](../sicherheitshaertung.md#checkliste)). Als Projektleiter prüfen
Sie, dass jede Zeile einen Namen und ein Datum trägt, und Sie bereiten vor:

- [ ] Ein Zeitfenster für den Livegang und einen Rückfallplan (Backup erstellt **und** einmal wiederhergestellt).
- [ ] Den Wartungsmodus für die Umschaltung, falls Inhalte eingefroren werden müssen ([Wartungsmodus](../../../en/us/features/6.0/maintenance-mode.md)).
- [ ] Die Personen, die am Tag des Livegangs die Logs beobachten ([Log-Viewer](../../../en/us/features/6.0/log-viewer.md)).
- [ ] Die Ankündigung an die Redakteure: Was sich für sie ändert und wo sie Hilfe finden.

## Phase 7: Betrieb

Eine Site ist nie fertig. Übergeben Sie mit:

- [ ] Der wöchentlichen Checkliste aus [Eine Site betreiben](../../anleitungen/betrieb-einer-website.md) und der Checkliste für
      die erste Woche aus [Kapitel 10](../../installation/10-nach-der-installation.md#1015-eine-checkliste-für-die-erste-woche).
- [ ] Laufenden Cronjobs, einschließlich der Gruppe `frequent`, die das Audit-Protokoll und die Workflows benötigen
      ([Cronjobs](../../anleitungen/cronjobs.md), [Audit-Protokoll](../../../en/us/features/6.0/audit-trail.md)).
- [ ] Einer Update-Routine: Wer verfolgt die Releases, und wie werden Updates getestet
      ([Release- und Upgrade-Planung](release-und-upgrade-planung.md))?
- [ ] Der Übergabevorlage aus den [Projektvorlagen](projektvorlagen.md).

## Personen und die Rollen der Administration

Eine **Rolle** ist eine Menge von **Richtlinien**; eine Richtlinie erlaubt ein Modul oder eine Funktion, unter
Umständen nur in bestimmten Sektionen, Klassen, Teilbäumen, Sprachen oder Siteaccesses. Eine Rolle bewirkt nichts,
solange sie keinen Benutzern oder Benutzergruppen zugewiesen ist
([Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md)).

Was eine neue Installation laut Quellcode mitbringt:

| Rolle | Was sie laut Quellcode hat |
|---|---|
| **Administrator** | Jede Richtlinie („Vollzugriff“); in einer neuen Installation die einzige Rolle mit `audit/read` und `audit/manage` ([Audit-Protokoll](../../../en/us/features/6.0/audit-trail.md)) |
| **Editor** | Bearbeitungsrechte, `exphelp/read` zum Lesen der Hilfe ([Das Hilfesystem](../../../en/us/features/6.0/help-system.md)), `ezjscore/call` für `ezjscnode` und `oauth/authorize` bei neuen Installationen ([Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)) |
| **Anonymous** | Was nicht angemeldete Besucher tun dürfen; `content/read` und `content/pdf`, beschränkt auf die Sektion Standard ([Sektionen](../../anleitungen/sektionen.md)) |

Ordnen Sie die Personen Ihres Projekts Rollen zu. Bevorzugen Sie Benutzergruppen gegenüber einzelnen Benutzern: Jedes
Mitglied einer Gruppe erhält die Rolle, jetzt und künftig ([Rollen und Richtlinien, Abschnitt 5](../../anleitungen/rollen-und-richtlinien.md)).

| Projektrolle | Typische Rolle in der Administration | Zu besprechende Richtlinien | Hinweise |
|---|---|---|---|
| Auftraggeber | keine oder eine Nur-Lese-Rolle | `content/read` | Braucht oft nur die Site und Berichte |
| Projektleiter | Editor oder eine eigene Rolle | `content/read`, `exphelp/read` | Zum Lesen des Audit-Protokolls ist `audit/read` nötig, das standardmäßig nur Administrator hat; beantragen Sie es bewusst |
| Leitende Redaktion | Editor plus Freigaberechte | `content/*` in den vereinbarten Sektionen, Freigabe im Workflow | In der Regel die freigebende Person des Ereignisses „Freigeben“ |
| Redakteur | Editor | `content/create`, `content/edit`, beschränkt nach Sektion oder Klasse | Beschränken Sie nach Sektion oder Teilbaum, wo Teams Bereiche verantworten |
| Übersetzer | Eine eigene Rolle | `content/translate` oder `content/edit` je Sprache | Die Sprachbeschränkung steht in der Richtlinie ([Inhaltssprachen](../../anleitungen/inhaltssprachen.md)) |
| Administrator | Administrator | Vollzugriff | Ein persönliches Konto pro Person; `admin` wird als Anmeldung stillgelegt ([Sicherheitshärtung](../sicherheitshaertung.md)) |
| Entwickler | Administrator auf Entwicklung und Test; auf Produktion oft keine | | Änderungen gelangen über Git in die Produktion, nicht über die Administration |
| Tester | Eine Kopie der zu testenden Rolle | die zu testende Rolle | Testen Sie mit den echten Rollen, nicht mit Administrator |

> **Warnung**
> Eine Richtlinie über alle Module gewährt Vollzugriff, und eine Richtlinie über das Modul `role` erlaubt ihren
> Benutzern, Rollen zu ändern und sich so beliebige Rechte zu geben. Die Rollenliste markiert den ersten Fall rot und
> den zweiten gelb. Mit `PreventPrivilegeEscalation=enabled` (der Standard) kann niemand mehr Rechte vergeben, als er
> selbst besitzt ([Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)).

## Anforderungen erheben: Bedarf, Funktion, Seite

Formulieren Sie jede Anforderung als Bedarf und suchen Sie dann die Funktion, die ihn erfüllt. Erfüllt keine Funktion
den Bedarf, ist es Entwicklungsarbeit oder eine Erweiterung ([Schätzen und Abgrenzen mit Erweiterungen](schaetzen-und-abgrenzen-mit-erweiterungen.md)).

| Bedarf | Die Funktion, die ihn erfüllt | Lesen |
|---|---|---|
| Redakteure schreiben, eine leitende Redaktion gibt frei, bevor etwas live geht | Workflow mit einem Ereignis „Freigeben“, verbunden über einen Trigger | [Workflows, Abschnitt 3](../../anleitungen/workflows.md) |
| Zu einem früheren Text zurückkehren | Versionen eines Objekts | [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md), [Inhaltshistorie](../../anleitungen/inhaltsverlauf.md) |
| Verschiedene Teams verantworten verschiedene Bereiche | Sektionen sowie Rollen, beschränkt nach Sektion oder Teilbaum | [Sektionen](../../anleitungen/sektionen.md) |
| Inhalte durchlaufen Stufen (Entwurf, Prüfung, rechtliche Freigabe) | Objektzustände mit nach Zustand beschränkten Richtlinien | [Objektzustände](../../anleitungen/objektzustaende.md) |
| Ein Bereich nur für Mitglieder | Eine Sektion, eine Rolle für Mitglieder | [Sektionen, 13.1 Ein Mitgliederbereich](../../anleitungen/sektionen.md) |
| Redakteure gestalten Landingpages selbst | Exponential Layouts | [Der Layout-Editor](../layout-editor.md) |
| Mehrere Sites aus einer Installation | Siteaccesses, Multisite-Einstellungen | [Multisite- und mehrsprachige Projekte planen](multisite-und-mehrsprachigkeit-planen.md) |
| Inhalte in mehreren Sprachen | Inhaltssprachen, `SiteLanguageList` je Siteaccess | [Inhaltssprachen](../../anleitungen/inhaltssprachen.md) |
| Sprachen mit Schreibrichtung von rechts nach links | `dir="rtl"` in Inhalten und Administration | [Von rechts nach links](../../../en/us/features/6.0/right-to-left.md) |
| Hilfe beim Schreiben von Entwürfen und Übersetzungen | Exponential Intelligence | [expai](../../../en/us/features/6.0/extensions/expai.md) |
| Inhalte mit Tags klassifizieren | `eztags` | [eztags](../../../en/us/features/6.0/extensions/eztags.md) |
| Ein Newsletter | `cjw_newsletter` | [cjw_newsletter](../../../en/us/features/6.0/extensions/cjw_newsletter.md) |
| Produkte verkaufen | Der eingebaute Shop, PayPal über `ezpaypal` | [ezpaypal](../../../en/us/features/6.0/extensions/ezpaypal.md), [Bestellbestätigungen](../../../en/us/features/6.0/order-receipts.md) |
| Abonnenten über neue Inhalte benachrichtigen | Benachrichtigungen | [Benachrichtigungen für Administratoren](../../anleitungen/benachrichtigungen-administrator.md) |
| Respektieren, was Personen per E-Mail erhalten möchten | Die Mail-Schranke, das Einwilligungsprotokoll und die Sperrliste | [13.13 E-Mail: Einwilligung und Sperrliste](../../installation/13-sicherheitshaertung.md#1313-mail-einwilligung-und-sperrliste) |
| Zweistufige Anmeldung für Mitarbeitende | `sevenx_authentication_2fa` | [Zweistufige Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md) |
| Wissen, wer was geändert hat | Das Audit-Protokoll | [Audit-Protokoll](../../../en/us/features/6.0/audit-trail.md) |
| Andere Systeme lesen oder schreiben Inhalte | REST API v1 und v2 | [REST API](../../anleitungen/rest-api-v1-und-v2.md) |
| PDF-Fassungen von Inhalten | PDF-Export | [PDF-Export](../../../en/us/features/6.0/pdf-export.md) |
| RSS-Feeds | RSS-Feeds | [RSS-Feeds](../../anleitungen/rss-feeds.md) |
| Suche auf der Site | Die eingebaute Suche | [Suchen](../suchen.md) |
| Spamschutz für Formulare | `recaptcha`, `hcaptcha` | [Erweiterungen](../../../en/us/features/6.0/extensions/README.md) |
| SEO-Metadaten und Sitemaps | `xrowmetadata`, `bcgooglesitemaps` | [xrowmetadata](../../../en/us/features/6.0/extensions/xrowmetadata.md) |
| Ungenutzte Bilder und defekte Links bereinigen | Inhaltsprüfungen | [Inhaltsprüfungen](../../../en/us/features/6.0/content-checks.md) |

## Ein durchgespieltes Beispiel

Eine regionale Organisation möchte eine öffentliche Site auf Englisch und Deutsch, einen Mitgliederbereich und einen
Freigabeschritt für Nachrichten. Die Planung Schritt für Schritt:

1. **Produkt.** Exponential 6; Symfony wird nicht benötigt.
2. **Sites und Sprachen.** Ein öffentlicher Siteaccess je Sprache (`site_en`, `site_de`) und die Administration, wie
   Kapitel 10 es beschreibt ([10.14 Mehrsprachige Sites](../../installation/10-nach-der-installation.md#1014-mehrsprachige-sites)).
3. **Plattform.** Für das erwartete Redaktionsteam genügt ein einzelner Rechner, daher wird SQLite in Betracht gezogen;
   der Administrator prüft vor der Entscheidung die Grenzen, die unter [Datenbankunterstützung](../datenbankunterstuetzung.md)
   aufgeführt sind.
4. **Inhaltsmodell.** Workshop-Ergebnisse: die Klassen Article, News, Event, Folder; eine Sektion „Mitgliederbereich“;
   eine Zustandsgruppe „Prüfung“ ist nicht nötig, weil der Freigabe-Workflow sie abdeckt.
5. **Rollen.** Redakteure (Rolle Editor, beschränkt auf die Sektion Standard), Mitglieder (eine neue Rolle,
   `content/read` auf die Sektion Mitgliederbereich), Freigebende (die leitende Redaktion).
6. **Anforderungen.** Mitgliederbereich: [Sektionen 13.1](../../anleitungen/sektionen.md). Freigabe:
   [Workflows 3](../../anleitungen/workflows.md). Deutsch: [Inhaltssprachen 11](../../anleitungen/inhaltssprachen.md).
7. **Risiken.** Übersetzungskapazität; der Workflow-Cronjob muss laufen: „Ohne diesen Cronjob wird freigegebener
   Inhalt nie veröffentlicht“ ([Workflows](../../anleitungen/workflows.md)).

## Verwandte Seiten

- [Projektleiter: hier beginnen](projektleiter-hier-beginnen.md)
- [Projektvorlagen](projektvorlagen.md)
- [Workshops zur Inhaltsmodellierung durchführen](workshops-zur-inhaltsmodellierung.md)
- [Zusammenarbeiten](zusammenarbeiten.md)
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
