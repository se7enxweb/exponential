---
title: Struktur, Sektionen und Rollen für die nationale Regierung
audience: government, admin, website-creator, project-manager
category: public-sector
order: 40
summary: Wie Sie den Inhaltsbaum der Website eines Ministeriums anordnen, welche Sektionen Sie für Einheiten und für Staging anlegen und welche Rollen Sie Redaktion, Freigebenden, Veröffentlichenden und dem zentralen Webteam geben, mit echten Richtlinien von Exponential.
keywords: Inhaltsbaum, Sektionen, Rollen, Richtlinien, Einheiten eines Ministeriums, Freigebende, Veröffentlichende, Webteam, Einschränkung nach Sektion, Einschränkung nach Teilbaum, Freigabe-Workflow, Staging, content tree, sections, roles, policies, ministry units, approver, publisher, web team, section limitation, subtree limitation, approval workflow, staging
type: help
---
# Struktur, Sektionen und Rollen für die nationale Regierung

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Website-Ersteller, die den Inhaltsbaum, die
Sektionen und die Rollen der Website eines Ministeriums oder zentralen Ressorts einrichten. Sie verwendet die
Mechanismen, die unter [Sektionen](../../../../anleitungen/sektionen.md),
[Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md) und [Workflows](../../../../anleitungen/workflows.md)
beschrieben sind; lesen Sie diese für jeden Bildschirm und jedes Feld.

## Inhalt

1. [Der Inhaltsbaum](#der-inhaltsbaum)
2. [Sektionen](#sektionen)
3. [Rollen](#rollen)
4. [Freigabe](#freigabe)
5. [Benutzergruppen](#benutzergruppen)
6. [Prüfungen](#prüfungen)

## Der Inhaltsbaum

Besucher navigieren nach Thema; Beschäftigte denken in Einheiten. Der Baum muss zuerst den Besuchern dienen, weil die
Menüs aus ihm entstehen ([Menüs und Navigation](../../../ersteller/website-ersteller/menues-und-navigation.md)). Eine
Struktur, die für viele nationale Websites funktioniert:

```text
Home (frontpage or landing page)
├── Topics                 one folder per policy area, the visitor's entry
├── Services               how to apply, eligibility, links to transactions
├── News                   press releases, statements, speeches
├── Publications           reports, strategies, guidance (Publication class)
├── Consultations          open, closed, outcomes
├── About us               structure, leadership, budget, contact, jobs
└── Transparency           disclosure logs, spending, contracts
```

Einheiten erhalten keine eigenen obersten Ordner. Ihre Inhalte liegen in den gemeinsamen Ordnern und gehören ihnen über
Sektionen und den Eigentümer des Objekts. Eine Seite, die an zwei Stellen gehört, erhält einen zweiten Ort, keine Kopie.

## Sektionen

Sektionen gruppieren Inhalte für Berechtigungen ([Sektionen](../../../../anleitungen/sektionen.md)). Eine neue Installation
hat Standard, Users, Media, Setup und Design. Fügen Sie hinzu:

| Sektion | Bezeichner | Enthält | Warum |
|---|---|---|---|
| Eine je veröffentlichender Einheit | zum Beispiel `unit_policy_a` | die Seiten, die diese Einheit pflegt | die Redaktion der Einheit bearbeitet nur diese |
| Pressestelle | `press_office` | Nachrichten und Erklärungen | ein eigenes Veröffentlichungsteam und eine eigene Freigaberegel |
| Staging | `staging` | vor einem Start vorbereitete Seiten | Besucher können sie nicht lesen, bis sie Standard zugewiesen werden ([13.3](../../../../anleitungen/sektionen.md#133-inhalte-vorbereiten-bevor-sie-öffentlich-werden)) |

Anonyme Besucher dürfen nur die Sektionen lesen, die ihre Rolle auflistet; die mitgelieferte Rolle Anonymous liest
Standard (und im Standardpaket die Sektion Media). Eine Sektion einer Einheit muss daher der Richtlinie `content/read`
der Rolle Anonymous hinzugefügt werden, bevor ihre Seiten öffentlich sind, sonst ergeben die Seiten „Zugriff
verweigert“. Planen Sie das ein, wenn Sie die Sektion anlegen.

## Rollen

Beginnen Sie mit den mitgelieferten Rollen (**Benutzer > Rollen und Richtlinien**): Administrator, Anonymous, Editor
und Member gibt es in einer Standardinstallation
([Rollen und Richtlinien von sevenx_multisite](../../../seitenpakete/sevenx-multisite/rollen-und-richtlinien-von-sevenx-multisite.md)).
Kopieren Sie Editor, statt die Rolle zu ändern, damit ein Upgrade oder ein Paketimport Sie nicht überrascht.

| Rolle | Richtlinien (Modul / Funktion, Einschränkung) | Zugewiesen an |
|---|---|---|
| Redaktion der Einheit | `content/read`; `content/create`, `content/edit`, `content/hide`, `content/versionread`, `content/translate` eingeschränkt nach Sektion (der Sektion der Einheit); `content/remove` ebenso eingeschränkt, wenn Einheiten entfernen dürfen | die Redaktionsgruppe der Einheit, die Rolle mit einer Einschränkung nach Sektion zugewiesen |
| Pressestelle | die Richtlinien der Redaktion der Einheit für die Sektion Pressestelle, dazu `content/publish_without_notification`, wenn Korrekturen keine E-Mails an Abonnenten auslösen sollen ([Ohne Benachrichtigung veröffentlichen](../../../../funktionen/6.0/veroeffentlichen-ohne-benachrichtigung.md)) | die Gruppe der Pressestelle |
| Freigabe | die Richtlinien der Redaktion der Einheit plus `content/versionread` für ausstehende Versionen | die Freigebenden einer Einheit |
| Webteam | `content/*`, `section/assign`, `state/assign`, `rss/edit`, `infocollector/read` | das zentrale Webteam |
| Administration | `*/*` | eine kleine Gruppe von Systemadministratorinnen und -administratoren |

Eine Rolle, die mehrmals mit unterschiedlichen Einschränkungen nach Sektion zugewiesen wird, erspart Ihnen eine Rolle je
Einheit: Wählen Sie auf der Seite der Rolle **Section** in der Liste der Einschränkungen, **Mit Einschränkungen
zuordnen**, dann die Sektion und die Gruppe
([13.2](../../../../anleitungen/sektionen.md#132-ein-intranet-mit-abteilungen)).

## Freigabe

Ein Ereignis *Freigeben* in einem Workflow auf **Vor dem Veröffentlichen von Inhalten** hält Versionen zurück, bis eine
freigebende Person entscheidet ([Workflows](../../../../anleitungen/workflows.md)). Seine Felder passen gut zu nationalen
Websites:

| Feld | Vorgeschlagene Verwendung |
|---|---|
| Betroffene Sektionen | die Sektionen der Einheiten, nicht Staging |
| Betroffene Versionen | *Alle Versionen* für Politikseiten; erwägen Sie *Neues Objekt publizieren* nur für Ordner mit geringem Risiko |
| Gruppen, die Content freigeben | die Freigebenden der Einheit |
| Gruppen ausschließen | das Webteam und die Pressestelle für dringende Erklärungen |

Je Trigger läuft nur ein Workflow. Für mehrere Einheiten mit unterschiedlichen Freigebenden setzen Sie je Einheit ein
Ereignis *Freigeben* in den Workflow (jedes auf seine Sektionen eingeschränkt) oder verwenden ein Ereignis
*Multiplexer*. Der Workflow-Cronjob muss laufen, sonst werden freigegebene Inhalte nie veröffentlicht.

## Benutzergruppen

Legen Sie die Gruppen unter **Benutzer** an: eine je Einheit für die Redaktion, eine je Einheit für die Freigebenden,
Webteam, Pressestelle. Setzen Sie Beschäftigte nie in die Gruppe Members, die öffentliche Registrierungen verwenden
(`[UserSettings] DefaultUserPlacement`). Halten Sie die Konten mit **Benutzer > Benutzerkonten prüfen** sauber
([Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md)).

## Prüfungen

- [ ] Melden Sie sich als Redaktion einer Einheit an: Sie können die Seiten Ihrer Einheit bearbeiten und nicht die
  einer anderen.
- [ ] Melden Sie sich ab: Seiten der Einheit, die öffentlich sein sollen, sind lesbar; Staging-Seiten nicht.
- [ ] Veröffentlichen Sie als Redaktion: Die Version ist ausstehend; **Setup > Workflow-Prozesse** zeigt sie wartend.
- [ ] Geben Sie als freigebende Person frei und führen Sie den häufigen Cronjob-Teil aus: Die Seite wird veröffentlicht.
- [ ] **Benutzer > Rollen und Richtlinien**: Jede Rolle listet die Gruppen, denen sie zugewiesen ist, mit ihren
  Einschränkungen.

## Verwandte Seiten

- [Nationale Regierung](README.md)
- [Schwerpunkte der Anforderungen für die nationale Regierung](schwerpunkte-der-anforderungen.md)
- [Objektzustände](../../../../anleitungen/objektzustaende.md)
- [Kurs für Website-Administratoren](../../../trainer/website-administratoren/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
