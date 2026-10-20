---
title: Intranet planen
audience: businesses, project-manager, admin
category: business-solutions
order: 10
summary: Was eine Organisation entscheidet, bevor sie ein Intranet auf Exponential baut - für wen es ist, wie es strukturiert ist, wer welchen Teil verantwortet, wie sich Mitarbeiter anmelden und welche Fragen über Kosten und Aufwand entscheiden.
keywords: Intranet planen, Mitarbeiterportal, Anforderungen, Struktur, Verantwortliche, Abteilungen, Anmeldung, Governance, Umfang, Budget, RequireUserLogin, Siteaccess, intranet planning, staff portal, requirements, structure, owners, departments, sign-in, governance, scope, budget, siteaccess
type: help
---
# Intranet planen

Diese Seite ist für Führungskräfte und Projektleitungen, die ein Intranet beginnen. Sie listet die Entscheidungen auf,
die das Projekt prägen, erklärt, welche Funktion von Exponential jede beantwortet, und verlinkt die Seiten, auf denen
die Arbeit beschrieben ist. Lesen Sie sie zusammen mit den Personen, die das Intranet verantworten werden, sobald es
live ist.

## Inhalt

1. [Mit dem Zweck beginnen](#mit-dem-zweck-beginnen)
2. [Die Entscheidungen, die das Projekt prägen](#die-entscheidungen-die-das-projekt-prägen)
3. [Wie ein Intranet in Exponential gebaut wird](#wie-ein-intranet-in-exponential-gebaut-wird)
4. [Verantwortliche und Governance](#verantwortliche-und-governance)
5. [Fragen, die über den Aufwand entscheiden](#fragen-die-über-den-aufwand-entscheiden)
6. [Ein Arbeitsblatt zur Planung](#ein-arbeitsblatt-zur-planung)
7. [Verwandte Seiten](#verwandte-seiten)

## Mit dem Zweck beginnen

Ein Intranet, das niemand verantwortet, wird zum Lager veralteter Dateien. Bevor Sie Funktionen wählen, schreiben Sie in
einem oder zwei Sätzen auf, wofür das Intranet da ist. Typische Zwecke sind:

- ein Ort für Firmenneuigkeiten und Ankündigungen;
- die aktuelle Version von Richtlinien, Handbüchern und Formularen;
- Abteilungsseiten, die jede Abteilung selbst aktuell hält;
- eine Dokumentenbibliothek, die Mailanhänge und gemeinsame Laufwerke ersetzt;
- ein Ausgangspunkt für interne Werkzeuge und Links.

Jeder Zweck führt zu einer Seite dieses Bereichs. Halten Sie die Liste für das erste Release kurz; Exponential lässt
Sie später Sektionen, Klassen und Rollen hinzufügen, ohne die Website neu zu bauen
([Ihr Inhaltsmodell entwerfen](../../../../eng-us/help/creators/website-creators/designing-your-content-model.md)).

## Die Entscheidungen, die das Projekt prägen

| Entscheidung | Optionen | Wo es erklärt ist |
|---|---|---|
| Wer das Intranet öffnen darf | alle Mitarbeiter; Mitarbeiter und einige externe Kräfte | [Abteilungen, Rollen und Sektionen](abteilungen-rollen-und-sektionen.md) |
| Wie sich Mitarbeiter anmelden | lokale Konten; Firmenverzeichnis (LDAP); zusätzlich zweistufige Anmeldung | [Zwei-Faktor und Anmeldung](zwei-faktor-und-anmeldung.md), [Verzeichnisanmeldung und LDAP](verzeichnisanmeldung-und-ldap.md) |
| Wer veröffentlichen darf | ein zentrales Team; jede Abteilung für ihren eigenen Bereich | [Abteilungen, Rollen und Sektionen](abteilungen-rollen-und-sektionen.md) |
| Ob Texte zuerst freigegeben werden | sofort veröffentlichen; Freigabe vor dem Veröffentlichen; Prüfstadien | [Freigabe-Workflows](freigabe-workflows.md) |
| Was mit Dokumenten geschieht | Dateien als Inhaltsobjekte mit Downloads mit Berechtigungsprüfung | [Dokumente und Dateibibliotheken](dokumente-und-dateibibliotheken.md) |
| Wie Mitarbeiter Dinge finden | eingebaute Suche; Solr für große Bestände | [Suche im Intranet](suche-im-intranet.md) |
| Wie Mitarbeiter von Änderungen erfahren | Benachrichtigungen, Digests, ein Newsletter | [Benachrichtigungen für Mitarbeiter](benachrichtigungen-fuer-mitarbeiter.md) |
| Wo es erreichbar ist | nur aus dem Büronetz; von überall mit einer Anmeldung | [Die Administration einschränken: Network restrictions](../../hosting/sicherheit/die-administration-einschraenken.md#einschränkungen-im-netzwerk) |
| Sprachen | eine; mehrere | [Mehrsprachig](../../../../eng-us/help/businesses/multilingual/README.md) |

## Wie ein Intranet in Exponential gebaut wird

Ein Intranet ist ein Siteaccess, der Begriff von Exponential für eine Website mit eigenen Einstellungen und eigener
Adresse ([10.2 Siteaccesses und Website-Adressen](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
Drei Dinge machen es privat:

1. **Jede Anfrage braucht eine Anmeldung.** `site.ini [SiteAccessSettings] RequireUserLogin=true` schickt jeden
   Besucher, der nicht angemeldet ist, auf die Anmeldeseite. Nur die Ansichten in `AnonymousAccessList[]` bleiben
   offen, etwa die Seite für vergessene Passwörter
   ([Was Besucher sehen dürfen](../../../../eng-us/help/creators/website-creators/visitor-permissions.md#siteaccess-settings)).
2. **Nur Mitarbeiter dürfen sich dort anmelden.** Die Richtlinie `user/login` lässt sich nach Siteaccess einschränken
   (`kernel/user/module.php` definiert die Einschränkung `SiteAccess` für `login`); eine Rolle für Mitarbeiter kann
   also die Anmeldung im Intranet erlauben, eine öffentliche Mitgliederrolle nicht.
3. **Inhalte werden nach Berechtigung gruppiert.** Sektionen und Rollen entscheiden, welche Gruppe was liest und
   bearbeitet ([Sektionen](../../../anleitungen/sektionen.md)).

Ein Intranet kann in einer eigenen Installation liegen oder neben einer öffentlichen Website in derselben Installation.
Eine Installation mit mehreren Siteaccesses teilt sich den Inhaltsbaum, die Benutzer und die Betriebsarbeit; getrennte
Installationen halten alles auseinander. Beide Formen sind unter
[Mehrere Websites in einer Installation einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md) und im Bereich
[Multisite](../../../../eng-us/help/businesses/multisite/README.md) beschrieben.

> **Tipp**
> Wenn Intranet und öffentliche Website eine Installation teilen, stellen Sie sicher, dass die Rolle Anonymous des
> öffentlichen Siteaccess die Sektionen des Intranets nicht lesen kann. Die Standardrolle Anonymous liest nur die
> Sektion Standard ([Sektionen, Teil 2](../../../anleitungen/sektionen.md#2-die-sektionen-einer-neuen-installation)); legen Sie
> Intranetinhalte in eigene Sektionen.

## Verantwortliche und Governance

Planen Sie die Menschen vor den Seiten:

| Rolle in der Organisation | Was sie im Intranet tut | Typische Rolle in Exponential |
|---|---|---|
| Intranetverantwortliche Person | legt die Struktur fest, gibt neue Bereiche frei, prüft Zugriffe | Administrator oder eine Redakteursrolle mit weiten Grenzen |
| Redakteur einer Abteilung | hält die Seiten und Dokumente der Abteilung aktuell | Redakteur, mit einer Sektions-Einschränkung zugeordnet |
| Freigebende Person | prüft Texte, bevor sie live gehen | Mitglied der Gruppe der Freigebenden eines Ereignisses Approve |
| Alle Mitarbeiter | lesen, suchen, folgen Einträgen | eine Rolle Mitarbeiter mit `content/read` für die Mitarbeitersektionen |
| IT oder der Hoster | Backups, Aktualisierungen, Mail, Anbindung der Anmeldung | Serverzugriff; keine Inhaltsrolle nötig |

Die Seiten [Projektvorlagen](../../fuehrung/projektvorlagen.md) und
[Zusammenarbeiten](../../fuehrung/zusammenarbeiten.md) im Bereich Führung helfen, das schriftlich festzuhalten.

## Fragen, die über den Aufwand entscheiden

Diese Fragen verändern die Größe des Projekts. Beantworten Sie sie früh und geben Sie die Antworten an die Person, die
die Arbeit schätzt ([Schätzen und Abgrenzen mit Erweiterungen](../../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md)).

- Wie viele Abteilungen gibt es, und braucht jede einen eigenen Bereich?
- Müssen Konten aus einem vorhandenen Verzeichnis kommen, oder können Mitarbeiter lokale Konten bekommen?
- Muss jeder Text freigegeben werden oder nur manche Bereiche?
- Wie viele Dokumente gibt es heute, in welchen Formaten, und wer verantwortet sie?
- Brauchen Dokumente eine Volltextsuche in den Dateien?
- Muss das Intranet von außerhalb des Büronetzes erreichbar sein?
- Welche Sprachen werden gebraucht?
- Welche vorhandenen Werkzeuge müssen verlinkt oder eingebettet werden?

## Ein Arbeitsblatt zur Planung

Kopieren Sie diese Liste in die Projektbeschreibung
([Vorlage für Projektbeschreibungen](../vorlagen/vorlage-fuer-projektbeschreibungen.md)).

- [ ] Zweck des Intranets in zwei Sätzen.
- [ ] Liste der Benutzergruppen und ihrer Größe.
- [ ] Karte der Bereiche (Sektionen) und welche Gruppen jeden lesen und bearbeiten.
- [ ] Entscheidung zur Anmeldung: lokal, Verzeichnis, zweistufig.
- [ ] Entscheidung zum Veröffentlichen: direkt, Freigabe, Prüfzustände.
- [ ] Bestandsaufnahme der Dokumente und Migrationsplan.
- [ ] Entscheidung zur Suche: eingebaut oder Solr.
- [ ] Entscheidung zu Benachrichtigungen: welchen Einträgen Mitarbeiter standardmäßig folgen.
- [ ] Hosting und Backups entschieden ([Zuverlässigkeit](../../../../eng-us/help/businesses/reliability/README.md)).
- [ ] Benannte verantwortliche Person und ein Termin für die erste Zugriffsprüfung
      ([Zugriffsprüfungen](../../../../eng-us/help/businesses/security/access-reviews.md)).

## Verwandte Seiten

- [Intranets](README.md)
- [Checkliste für die Intranet-Einführung](checkliste-fuer-die-intranet-einfuehrung.md)
- [Ein Exponential-Projekt planen](../../fuehrung/ein-exponential-projekt-planen.md)
- [Workshops zur Inhaltsmodellierung](../../fuehrung/workshops-zur-inhaltsmodellierung.md)
- [Durchgerechnetes Beispiel: ein Intranet](../kosten/durchgerechnetes-beispiel-intranet.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
