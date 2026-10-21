---
title: Kunden- und Partnerbereiche
audience: businesses, admin, partner
category: business-solutions
order: 30
summary: Wie ein Exponential-Extranet jedem Partner, Kunden oder jeder Stufe einen eigenen Bereich gibt - Benutzergruppen pro Organisation, Sektionen pro Stufe, Teilbaum-Einschränkungen pro Organisation, Objektzustände für wechselnde Zielgruppen, Einstiegsseiten nach der Anmeldung, und wie Sie testen, dass niemand die Daten eines anderen sieht.
keywords: Partnerbereiche, Kundenbereiche, Bereich pro Partner, Stufen, Sektionen, Teilbaum-Einschränkung, Objektzustände, StateGroup, Benutzergruppen, mit Einschränkungen zuordnen, LoginRedirectionUriAttribute, Datentrennung, Struktur des Extranets, partner areas, customer areas, per-partner area, tiers, sections, subtree limitation, object states, user groups, assign with limitation, data separation, extranet structure
type: help
---
# Kunden- und Partnerbereiche

Diese Seite ist für Portalverantwortliche und Administratoren. Das Versprechen eines Partnerportals ist, dass jeder
Partner sieht, was für ihn gedacht ist, und nichts, was einem anderen gehört. Diese Seite zeigt, wie Exponential dieses
Versprechen mit Benutzergruppen, Sektionen, Teilbaum-Einschränkungen und Objektzuständen hält und wie Sie es belegen,
bevor Partner eingeladen werden.

## Inhalt

1. [Die Bausteine](#die-bausteine)
2. [Stufen mit Sektionen](#stufen-mit-sektionen)
3. [Ein Bereich pro Organisation mit Teilbäumen](#ein-bereich-pro-organisation-mit-teilbäumen)
4. [Wechselnde Zielgruppen mit Objektzuständen](#wechselnde-zielgruppen-mit-objektzuständen)
5. [Die Modelle kombinieren](#die-modelle-kombinieren)
6. [Partner nach der Anmeldung in ihren Bereich schicken](#partner-nach-der-anmeldung-in-ihren-bereich-schicken)
7. [Die Trennung testen](#die-trennung-testen)
8. [Checkliste](#checkliste)
9. [Verwandte Seiten](#verwandte-seiten)

## Die Bausteine

| Baustein | Rolle in einem Portal | Anleitung |
|---|---|---|
| Benutzergruppe pro Organisation | die Personen von Partner A, Kunde B | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) |
| Sektion | eine Stufe oder Art von Inhalten (Partnermaterial, Gold-Stufe) | [Sektionen](../../../anleitungen/sektionen.md) |
| Teilbaum | der Ordner einer Organisation | [Rollen und Richtlinien, 5](../../../anleitungen/rollen-und-richtlinien.md#5-die-rolle-zuweisen) |
| Objektzustand | eine Kennzeichnung der Zielgruppe, die sich ändern kann (Intern, Partner, Öffentlich) | [Objektzustände](../../../anleitungen/objektzustaende.md) |
| Rolle | was eine Gruppe tun darf, eingeschränkt durch das oben Genannte | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) |

Jedes Objekt gehört zu genau einer Sektion; eine Rolle kann einer Gruppe mit einer Einschränkung auf eine Sektion oder
einen Teilbaum zugeordnet werden, und eine Richtlinie kann nach Zuständen eingeschränkt werden. Mehrere Einschränkungen
einer Richtlinie müssen alle erfüllt sein; Richtlinien mehrerer Rollen addieren sich
([Rollen und Richtlinien, 3](../../../anleitungen/rollen-und-richtlinien.md#3-eine-rolle-mit-eingeschränkten-richtlinien-anlegen)).

## Stufen mit Sektionen

Wenn jeder Partner einer Stufe dasselbe Material bekommt:

1. Legen Sie eine Sektion pro Stufe an, zum Beispiel *Partnermaterial* (`partner_material`) und *Gold-Stufe*
   (`gold_tier`) ([Sektionen, 5](../../../anleitungen/sektionen.md#5-eine-sektion-anlegen)).
2. Weisen Sie den Ordner jeder Stufe mit **Inhalt zuweisen** seiner Sektion zu.
3. Legen Sie eine Rolle *Partner-Leser* mit `content/read` für Section( Partnermaterial ) und `user/login` für den
   Siteaccess des Portals an; ordnen Sie sie der Gruppe aller Partner zu.
4. Legen Sie eine Rolle *Gold-Leser* mit `content/read` für Section( Gold-Stufe ) an; ordnen Sie sie der Gruppe der
   Gold-Partner zu.

Einen Partner eine Stufe höher zu setzen ist eine einzige Änderung: Fügen Sie die Gruppe der Organisation der
Gold-Gruppe hinzu, oder ordnen Sie ihr die Gold-Rolle zu. Die Seite der Sektion listet dann jede Rolle und Gruppe auf,
die sie verwendet, was bei Prüfungen hilft ([Sektionen, 4](../../../anleitungen/sektionen.md#4-die-seite-einer-sektion)).

## Ein Bereich pro Organisation mit Teilbäumen

Wenn jede Organisation eigene Dokumente hat (Verträge, Rechnungen, Projektdateien):

1. Legen Sie einen Ordner pro Organisation an, zum Beispiel *Partner > Partner A*, in einer Sektion *Partner privat*,
   die keine allgemeine Rolle liest.
2. Legen Sie eine Rolle *Organisationsbereich* mit `content/read` (und, wenn Partner hochladen, `content/create` für
   die Klasse File) ohne Teilbaum in der Richtlinie an.
3. Ordnen Sie sie der Gruppe von Partner A **mit einer Teilbaum-Einschränkung** auf den Ordner von Partner A zu;
   wiederholen Sie das pro Organisation
   ([Rollen und Richtlinien, 5](../../../anleitungen/rollen-und-richtlinien.md#5-die-rolle-zuweisen)).

Eine Rolle bedient jede Organisation; jede Zuordnung trägt den Teilbaum der Organisation. Einen Partner zu entfernen
entfernt eine Zuordnung, und die Seite der Rolle listet jede Zuordnung mit ihrer Einschränkung auf.

> **Warnung**
> Eine Teilbaum-Einschränkung folgt dem Platz im Baum. Inhalte, die aus dem Ordner von Partner A herausgeschoben werden,
> kann Partner A nicht mehr lesen, und Inhalte, die hineingeschoben werden, werden lesbar. Schulen Sie Redakteure,
> Partnerinhalte mit Sorgfalt zu verschieben.

## Wechselnde Zielgruppen mit Objektzuständen

Manche Inhalte wechseln im Laufe ihres Lebens die Zielgruppe: Ein Produktblatt ist intern, solange es entworfen wird,
wird vor dem Marktstart mit Partnern geteilt und ist nach dem Start öffentlich. Eine Zustandsgruppe `audience` mit
*Intern*, *Partner* und *Öffentlich* drückt das aus:

- Die Rolle Partner-Leser bekommt `content/read` mit `StateGroup_audience = Partners, Public`.
- Die öffentliche Rolle Anonymous bekommt `content/read` mit `StateGroup_audience = Public`.
- Produktverantwortliche bekommen `state/assign` mit `NewState`, beschränkt auf die Zustände, die sie setzen dürfen.

Die Änderung eines Zustands verschiebt einen Eintrag zwischen Zielgruppen, ohne ihn im Baum zu verschieben
([Objektzustände, 8](../../../anleitungen/objektzustaende.md#8-zustände-in-rollen-und-richtlinien)). Denken Sie daran, dass der
erste Zustand einer neuen Gruppe jedem vorhandenen Objekt gegeben wird; setzen Sie vorhandene öffentliche Inhalte also
gleich nach dem Anlegen der Gruppe auf *Öffentlich*
([Objektzustände, 9](../../../anleitungen/objektzustaende.md#9-ein-durchgearbeitetes-beispiel-ein-prüf-workflow) zeigt dieselbe Sorgfalt
für eine Prüfgruppe).

## Die Modelle kombinieren

| Inhalt | Modell |
|---|---|
| Material für alle Partner | Sektion *Partnermaterial* |
| Material für eine Stufe | Sektion pro Stufe |
| Dokumente einer Organisation | Teilbaum pro Organisation in der Sektion *Partner privat* |
| Einträge, die von intern zu Partnern zu öffentlich wandern | Zustandsgruppe *audience* |

## Partner nach der Anmeldung in ihren Bereich schicken

`site.ini [UserSettings] LoginRedirectionUriAttribute[group]` nennt ein Textattribut der Klasse der Benutzergruppe,
das die Adresse enthält, zu der Benutzer dieser Gruppe nach der Anmeldung geschickt werden; `[user]` tut dasselbe pro
Benutzer (auskommentierte Beispiele in `settings/site.ini`, gelesen von `kernel/private/classes/views/user/login.php`).
Damit landen die Personen von Partner A auf der Seite von Partner A. Das Attribut muss zuerst der Klasse hinzugefügt
werden; bitten Sie Ihren Partner, es einzurichten.

## Die Trennung testen

Bevor Sie Partner einladen, testen Sie als jede Art von Benutzer, in einem privaten Browserfenster
([Als Besucher testen](../../../../eng-us/help/creators/website-creators/visitor-permissions.md#testing-as-a-visitor)):

| Angemeldet als | Muss sehen | Darf nicht sehen |
|---|---|---|
| Benutzer von Partner A | Partnermaterial, Ordner von Partner A | Ordner von Partner B, Gold-Stufe (wenn nicht Gold) |
| Benutzer eines Gold-Partners | Gold-Stufe | Ordner anderer Organisationen |
| Besucher | öffentliche Seiten | irgendetwas aus dem Portal |

Prüfen Sie für jeden auch Suche, Menüs und Datei-Downloads, denn alle folgen denselben Berechtigungen. Der Befehl
`./console exp:access:check` fragt die Berechtigungsprüfungen für einen anderen Benutzer auf der Kommandozeile ab
([Einschränkungen von Inhaltsrichtlinien, In short](../../../../eng-us/features/6.0/content-limitation-handlers.md#in-short)).

## Checkliste

- [ ] Eine Benutzergruppe pro Partnerorganisation.
- [ ] Sektionen für gemeinsames Material und Material der Stufen.
- [ ] Eine Rolle für Organisationsbereiche, pro Organisation mit einer Teilbaum-Einschränkung zugeordnet.
- [ ] Zustandsgruppe nur angelegt, wenn Zielgruppen sich wirklich ändern; vorhandene Inhalte auf den richtigen Zustand
      gesetzt.
- [ ] Trennung als mindestens zwei Partnerbenutzer und ein Besucher getestet, einschließlich Suche und Downloads.
- [ ] Ein Verfahren, um die Zuordnung einer Organisation zu entfernen, wenn ein Vertrag endet.

## Verwandte Seiten

- [Partnerportal planen](partnerportal-planen.md)
- [Abteilungen, Rollen und Sektionen](../intranets/abteilungen-rollen-und-sektionen.md)
- [Sektionen und Objektzustände für Website-Ersteller](../../../../eng-us/help/creators/website-creators/sections-and-object-states.md)
- [Berechtigungen im Extranet prüfen](berechtigungen-im-extranet-pruefen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
