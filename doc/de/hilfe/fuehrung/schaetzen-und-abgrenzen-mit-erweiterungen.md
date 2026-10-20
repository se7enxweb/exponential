---
title: Schätzen und Abgrenzen mit Erweiterungen
audience: project-manager, developer
category: leadership
order: 50
summary: Den Umfang eines Exponential-Projekts abgrenzen, indem Sie prüfen, was der Kernel und seine Erweiterungen bereits leisten: eine Inventur nach Bereichen, Aufwandstreiber als Fragen und ein Arbeitsblatt zur Abgrenzung. Keine Stunden, keine Preise.
keywords: Schätzung, schätzen, Abgrenzung, Umfang, Erweiterungen, Inventur, Aufwand, Arbeitsblatt, selbst bauen oder wiederverwenden, eigene Erweiterung, Pakete, Projektleiter, Entwickler, estimate, estimation, scoping, scope, extensions, inventory, effort, worksheet, build or reuse, RAD, custom extension, composer, packages, se7enxweb, project manager, developer
type: help
---
# Schätzen und Abgrenzen mit Erweiterungen

Diese Seite ist für Projektleiter und Entwickler, die gemeinsam den Umfang eines Exponential-Projekts abgrenzen und es
schätzen. Sie beantwortet: was bereits als Teil des Kernels oder als Erweiterung vorhanden ist, welche Fragen den Aufwand
eines Projekts bestimmen und wie Sie den Umfang festhalten, sodass sich eine Schätzung erstellen und später überprüfen
lässt. Sie nennt bewusst **keine Stundenzahlen und keine Preise**: Die Quellen enthalten keine, und jedes Projekt ist
anders. Was sie Ihnen gibt, sind die Inventur, die Fragen und ein Arbeitsblatt.

## Inhalt

1. [Zuerst wiederverwenden: wie Erweiterungen funktionieren](#zuerst-wiederverwenden-wie-erweiterungen-funktionieren)
2. [Die Inventur: was mit Exponential ausgeliefert wird](#die-inventur-was-mit-exponential-ausgeliefert-wird)
3. [Herausfinden, was auf Ihrer Installation vorhanden ist](#herausfinden-was-auf-ihrer-installation-vorhanden-ist)
4. [Aufwandstreiber als Fragen](#aufwandstreiber-als-fragen)
5. [Eine eigene Erweiterung bauen](#eine-eigene-erweiterung-bauen)
6. [Das Arbeitsblatt zur Abgrenzung](#das-arbeitsblatt-zur-abgrenzung)
7. [Mit dem Team schätzen](#mit-dem-team-schätzen)
8. [Den Umfang unter Kontrolle halten](#den-umfang-unter-kontrolle-halten)

## Zuerst wiederverwenden: wie Erweiterungen funktionieren

Eine Erweiterung „ist ein Ordner unter `extension/`, der Exponential Einstellungen, Templates, Module, Inhaltstypen,
Befehle oder Cronjobs hinzufügt, ohne den Kernel anzufassen“ ([Erweiterungen](../../anleitungen/extensions.md)). Fakten, die
für die Abgrenzung zählen:

| Fakt | Folge für den Plan | Quelle |
|---|---|---|
| Eine Erweiterung tut nichts, bis sie eingeschaltet ist (`ActiveExtensions[]`) | Viele Erweiterungen liegen bereits auf der Festplatte; eine einzuschalten ist Konfiguration, keine Entwicklung | [Erweiterungen, Abschnitt 1](../../anleitungen/extensions.md) |
| `composer.json` verlangt 66 Pakete `se7enxweb/*`, sodass jede Installation sie mitbringt; viele sind inaktiv, bis sie eingeschaltet werden | Prüfen Sie die Inventur, bevor Sie planen, etwas zu bauen | [Wer ist 7x?](../wer-ist-7x.md#das-ökosystem-der-erweiterungen), [Die Standardauswahl an Erweiterungen](../../../en/us/features/6.0/default-extension-distribution.md) |
| Die Reihenfolge von `ActiveExtensions[]` ist die Rangfolge für Einstellungen und Templates | Wenn mehrere Erweiterungen dasselbe ändern, muss über die Reihenfolge entschieden werden | [Ladereihenfolge](../../../en/us/features/6.0/extension-loading-order.md) |
| Jede Erweiterung hat ihr eigenes Repository, eigene Releases und einen eigenen Changelog | Planen Sie Updates pro Erweiterung, nicht nur pro Kernel-Release | [Wer ist 7x?](../wer-ist-7x.md#das-ökosystem-der-erweiterungen) |
| Jede ausgelieferte Erweiterung hat eine Seite, die sagt, wofür sie gedacht ist, wie sie eingerichtet wird und welche Einstellungen sie hat | Lesen Sie die Seite, bevor Sie schätzen | [Erweiterungen, Themes und Pakete](../../../en/us/features/6.0/extensions/README.md) |

## Die Inventur: was mit Exponential ausgeliefert wird

Der Erweiterungskatalog gruppiert die Erweiterungen, die eine eigene Seite haben
([Erweiterungen, Themes und Pakete](../../../en/us/features/6.0/extensions/README.md)). Markieren Sie mit dieser Tabelle für jede
Anforderung, ob eine Erweiterung sie abdeckt.

| Bereich | Erweiterungen mit eigener Seite | Was sie abdecken |
|---|---|---|
| Inhalte und Datentypen | `birthday`, `enhancedezbinaryfile`, `enhancedselection2`, `exp_enhanced_link`, `ezgmaplocation`, `ezstarrating`, `eztags`, `hcaptcha`, `ngclasslist`, `recaptcha`, `xrowmetadata` | Zusätzliche Feldtypen, Tags, Bewertungen, Kartenstandorte, Spamschutz, Metadaten und Sitemaps |
| Bearbeiten und Seitenbau | `expai`, die Suite `explayouts`, `ezautosave`, `ezflow`, `ezie`, `ezmultiupload`, `ezodf`, `ezoe`, `ezwt` | Schreibassistent, Layouts, automatisches Speichern, Seitenabläufe, Bildeditor, Mehrfach-Upload, Import und Export von OpenDocument, der Online-Editor, Website-Toolbar |
| Designs und Themes | `ezdemo`, `ezwebin`, `ezwebin-ezpackage`, `sevenx_themes_admin_classic`, `sevenx_themes_simple` | Demo- und Website-Designs, das Theme simple |
| Shop, Newsletter und Besucher | `bccie`, `bcgooglesitemaps`, `bcwebsitestatistics`, `cjw_newsletter`, `ezpaypal`, `ezpm` | Export gesammelter Informationen, Sitemaps, Statistiken, Newsletter, PayPal, private Nachrichten |
| Datenaustausch und APIs | `ezprestapi`, `ezxmlexport`, `nxc_powercontent`, `syndication`, `xrowextract` | REST-API v1 und v2, XML-Export, Inhalte aus Code, Syndizierung, Export und Import |
| Administration, Sicherheit und Betrieb | `AdminAid`, `ezjscore`, `ezmbpaex`, `expqueue`, `ezupdate`, `git_manager`, `sevenx_authentication_2fa`, `sevenx_dse`, `sevenx_valkey_cache` | Hilfen für die Administration, Ablauf von Passwörtern, Job-Warteschlange, Updates, Git-Dashboard und Backups, Zwei-Faktor-Anmeldung, Editor für Datenbankquellen, Cache mit Redis und Valkey |
| Template-Helfer | `owsimpleoperator`, `swark` | Zusätzliche Template-Operatoren und Workflow-Ereignisse |

Daneben deckt der Kernel selbst vieles ab, wofür andere Systeme Erweiterungen brauchen: Versionen, Workflows, Multisite,
Sprachen, den Shop, das Audit-Protokoll, Inhaltsjobs und die Hilfe
([Was Exponential ist](was-exponential-ist.md#was-der-kernel-leistet)). Prüfen Sie zuerst den Kernel, dann die
Erweiterungen, und bauen Sie erst dann.

> **Warnung**
> Zwei Erweiterungen für dieselbe Aufgabe sind nicht immer gleichwertig. Für den Seitenbau führt der Katalog sowohl
> `ezflow` (Seiten, Zonen und Blöcke) als auch Exponential Layouts. Unter MongoDB „ist `eztags` nicht nutzbar“, und die
> Planung von `ezflow` ist ungetestet ([Grenzen von MongoDB](../datenbankunterstuetzung.md#grenzen-von-mongodb)). Unter einer
> erzwungenen Content Security Policy tun Menüpunkte von Erweiterungen mit Inline-Skripten nichts, bis sie umgebaut
> sind ([Content Security Policy](../../../en/us/features/6.0/content-security-policy.md)). Jeder dieser Punkte ist eine Frage
> der Abgrenzung.

## Herausfinden, was auf Ihrer Installation vorhanden ist

- **In der Administration**: **Setup > Extensions** (`/setup/extensions`) listet jede Erweiterung auf der Festplatte
  auf, die aktiven zuerst, mit Beschreibung, Lizenz, Projektseite und Abhängigkeiten; **Details** ergänzt Autor, Designs
  und Downloads ([Die Seite Extensions](../../anleitungen/extensions-seite.md),
  [Erweiterungsliste](../../../en/us/features/6.0/extension-list-and-downloads.md)).
- **In einem Terminal**: `./console exp:ini list site.ini/ExtensionSettings --allow-root-user` zeigt die aktiven in
  ihrer Ladereihenfolge ([Erweiterungen, Abschnitt 1](../../anleitungen/extensions.md)).
- **In `composer.json`**: `grep -n '"se7enxweb/' composer.json` listet `suggest` und `require` auf
  ([Die Standardauswahl an Erweiterungen](../../../en/us/features/6.0/default-extension-distribution.md)).
- **Über Ihre Festplatte hinaus**: die Composer-Pakete `se7enxweb/<name>` auf Packagist; der Anbieter se7enxweb hat dort
  219 Pakete ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#das-ökosystem-der-erweiterungen)).

Bitten Sie einen Entwickler, diese Liste in der ersten Woche zu erstellen; sie ist die Grundlage Ihres Umfangs.

## Aufwandstreiber als Fragen

Der Aufwand in einem Exponential-Projekt hängt von den Antworten auf die folgenden Fragen ab. Stellen Sie sie in der
Analysephase und halten Sie die Antworten im Arbeitsblatt fest. Ein „Ja“ bedeutet nicht „teuer“; es bedeutet „diese
Zeile gesondert schätzen“.

### Produkt und Plattform

- Ist das eine neue Site oder ein Upgrade bzw. eine Migration von einer älteren Installation? Von wie weit zurück
  ([Upgrade](../../anleitungen/aktualisieren.md), Kapitel 14 bis 17 des Installationshandbuchs)?
- Braucht das Projekt Symfony neben dem Kernel (Platform Legacy oder Nexus) oder nur den Kernel?
- Welche Datenbank? Sind alle geplanten Erweiterungen darauf nutzbar ([Datenbankunterstützung](../datenbankunterstuetzung.md))?
- Welcher Webserver und welches Hosting-Modell ([Hosting, Datenbank und Betriebssystem wählen](hosting-datenbank-und-betriebssystem-waehlen.md))?
- Wie viele Umgebungen (Entwicklung, Test, Staging, Produktion)?

### Inhalte

- Wie viele Inhaltsklassen, und wie viele davon gibt es bereits in dem Site-Paket, von dem Sie ausgehen
  ([4.3 Site-Pakete](../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird))?
- Welche Feldtypen werden über die 35 Datentypen der ausgelieferten Liste hinaus gebraucht
  ([Inhaltsmodell, Abschnitt 1](../../anleitungen/inhaltsmodell-und-bearbeitung.md))?
- Wie viele Inhalte werden migriert, und woher? Gibt es ein Werkzeug für diese Quelle (Pakete, `xrowextract`, REST)?
- Wie viele Inhalte werden von Hand eingegeben, und von wem?
- Wie viele Sprachen, und wer übersetzt?

### Sites und Design

- Wie viele Sites (Siteaccesses) und Designs?
- Gibt es ein bestehendes Designsystem oder Theme als Ausgangspunkt (`sevenx_themes_simple`, das Media-Theme, `ezwebin`)?
- Welche Seitentypen ordnen Redakteure selbst mit Layouts an, und welche sind feste Templates?
- Müssen die Designs von rechts nach links funktionieren?

### Workflow und Berechtigungen

- Wie viele Rollen über Administrator, Editor und Anonymous hinaus?
- Gibt es einen Freigabeschritt, und für welche Inhalte?
- Werden Objektzustände gebraucht (Prüfung, rechtliche Freigabe, Sperrfrist)?
- Gibt es einen Mitgliederbereich oder ein Intranet?

### Integration

- Welche Systeme tauschen Daten mit der Site aus, in welcher Richtung, wie oft?
- Genügt die REST-API, oder wird eine eigene Erweiterung gebraucht?
- Single Sign-on, Zahlungsanbieter, Newsletter, Webanalyse?

### Qualität und Compliance

- Welche Norm für Barrierefreiheit gilt für die öffentlichen Sites
  ([Checklisten](checklisten-barrierefreiheit-sicherheit-und-compliance.md))?
- Muss die Content Security Policy erzwungen werden, in der Administration, auf den Sites oder in beiden?
- Welche Datenschutzregeln gelten, und wer beantwortet Anfragen?
- Welche Qualitätsschranken müssen vor der Abnahme bestanden sein ([Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md))?

### Betrieb

- Wer betreibt die Site nach dem Start, und wer spielt Updates ein?
- Wie lange müssen Logs und das Audit-Protokoll aufbewahrt werden? (Die ausgelieferte Aufbewahrung des Audit-Protokolls
  beträgt für die Hauptkanäle 90 Tage live und 730 Tage im Archiv: [Audit-Protokoll](../../../en/us/features/6.0/audit-trail.md).)
- Gibt es einen Supportvertrag, und mit wem?

## Eine eigene Erweiterung bauen

Wenn nichts passt, ist die Arbeit eine eigene Erweiterung. Was die Hilfe dokumentiert, damit Entwickler es schätzen
können:

- Eine kleinste funktionierende Erweiterung von Hand, und generierte: **Setup > RAD** (`/setup/rad`) listet die
  Erweiterungspunkte auf, bietet Assistenten an, die eine funktionierende Erweiterung schreiben, und „listet jede Datei
  auf, die er unter `extension/<name>` schreiben würde, bevor er auch nur eine davon schreibt“
  ([RAD-Werkzeuge](../../../en/us/features/6.0/rad-tools.md), [Erweiterungen, Abschnitt 6](../../anleitungen/extensions.md)).
- Eigene Einstellungen, Inhalte und Cronjobs ([Erweiterungen, Abschnitt 6.4](../../anleitungen/extensions.md)).
- Die Veröffentlichung mit einer Versionsnummer in `ezinfo.php` und `extension.xml` und einem Tag, der nie verschoben
  wird ([Erweiterungen, „Release it“](../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).
- Templates der Administration folgen den Gestaltungsregeln: kein `<style>`-Block und kein `style=""`-Attribut
  (`CONTRIBUTING.md`, [Die Administration gestalten](../../../en/us/bc/6.0/admin4-styling.md)).

Posten, die Sie für jede eigene Erweiterung aufnehmen sollten:

- [ ] Entwurf und Umsetzung
- [ ] Einstellungen und ihre Dokumentation
- [ ] Richtlinien (wer sie verwenden darf) und ihr Platz im Rollenplan
- [ ] Übersetzungstexte
- [ ] Tests
- [ ] Release- und Update-Routine
- [ ] Dokumentation für Redakteure und Administratoren (sie kann in der Hilfe stehen: [Das Hilfesystem](../../../en/us/features/6.0/help-system.md), „Help from extensions“)

## Das Arbeitsblatt zur Abgrenzung

Kopieren Sie es in eine Tabellenkalkulation oder Ihr Projektnotizbuch. Eine Zeile pro Anforderung.

```text
Scoping worksheet for <project name>                        version <n>, <date>

| # | Requirement (need)        | Covered by           | Kind                     | Questions open           | Owner   | Estimate  |
|---|---------------------------|----------------------|--------------------------|--------------------------|---------|-----------|
| 1 | <need>                    | kernel / <extension> / build | configure / template / extension / content / migration | <questions> | <name> | <by team> |
| 2 | Editors approve news      | kernel: workflows    | configure                | Who approves? Cronjob?   | <name>  | <by team> |
| 3 | German site               | kernel: languages    | configure + content      | Who translates?          | <name>  | <by team> |
| 4 | Tag pages                 | eztags               | configure + template     | Database is not MongoDB? | <name>  | <by team> |
| 5 | CRM export every night    | build: extension     | extension                | Format? Direction?       | <name>  | <by team> |

Kinds:
  configure  = settings and roles only
  template   = design and template work
  extension  = new code in an extension of our own
  content    = classes, content entry, translation
  migration  = moving data from another system
```

Fügen Sie unter der Tabelle drei zusammenfassende Zeilen hinzu:

- **Annahmen**: alles, was die Schätzung als gegeben voraussetzt (zum Beispiel „die Datenbank ist PostgreSQL“).
- **Ausschlüsse**: was nicht zum Umfang gehört, namentlich.
- **Offene Fragen**: mit dem Datum, bis zu dem sie beantwortet sein müssen.

## Mit dem Team schätzen

1. **Gehen Sie das Arbeitsblatt mit einem Entwickler und dem leitenden Redakteur durch**, Zeile für Zeile. Der
   Projektleiter liest die Anforderung vor; der Entwickler nennt die Art; der Redakteur sagt, was „fertig“ dafür bedeutet.
2. **Lassen Sie die Personen schätzen, die die Arbeit machen.** Der Projektleiter protokolliert, hinterfragt Annahmen und
   führt die Liste der Ausschlüsse.
3. **Schätzen Sie Unbekanntes als Spike.** Eine Zeile mit offenen Fragen bekommt zuerst eine kleine, zeitlich begrenzte
   Untersuchung; die eigentliche Schätzung folgt aus deren Ergebnis.
4. **Weisen Sie die riskantesten Zeilen früh auf der Testinstallation nach**: eine Erweiterung auf der gewählten
   Datenbank, einen Import echter Inhalte, eine Seite unter der angestrebten Content Security Policy.
5. **Halten Sie die Version des Arbeitsblatts fest**, auf der die Schätzung beruht. Wenn sich der Umfang ändert, ändert
   sich die Schätzung mit.

## Den Umfang unter Kontrolle halten

- **Eine Liste.** Das Arbeitsblatt ist der Umfang. Ein Änderungswunsch ist eine neue Zeile mit eigener Schätzung.
- **Wiederverwendung ist eine Entscheidung, kein Zufall.** Wenn ein Entwickler vorschlägt, etwas zu bauen, fragen Sie,
  welche Erweiterung zuerst geprüft wurde und warum sie nicht passte.
- **Konfiguration ist nicht kostenlos.** Eine Erweiterung einzuschalten erfordert Einstellungen, Rollen, Übersetzungen,
  Tests und Dokumentation wie jede andere Änderung.
- **Geben Sie Korrekturen an das Projekt zurück.** Eine Korrektur an einer Erweiterung, die als Pull-Request
  zurückgeschickt wurde, muss nicht durch jedes spätere Update mitgeschleppt werden
  ([Mit der Community arbeiten](mit-der-community-arbeiten.md)).

## Verwandte Seiten

- [Erweiterungen: finden, installieren, konfigurieren, bauen und veröffentlichen](../../anleitungen/extensions.md)
- [Erweiterungen, Themes und Pakete](../../../en/us/features/6.0/extensions/README.md)
- [Die Standardauswahl an Erweiterungen](../../../en/us/features/6.0/default-extension-distribution.md)
- [Ein Exponential-Projekt planen](ein-exponential-projekt-planen.md)
- [Risikomanagement für Exponential-Projekte](risikomanagement.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
