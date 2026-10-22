---
title: Arbeitsblatt Rollen und Berechtigungen
audience: non-profit, businesses, admin, project-manager
category: non-profits
order: 40
summary: Ein Arbeitsblatt, mit dem eine Non-Profit-Organisation festlegt, wer auf ihrer Exponential-Site was tun darf - die Aufgaben (Autor, Redakteur, Freigebender, Layout-Redakteur, Formularbearbeiter, Kontoverwalter, Übersetzer, Administrator), die mitgelieferten Rollen Anonymous, Member, Editor und Administrator, Einschränkungen nach Bereich, Teilbaum und Sprache, Zwei-Faktor-Anmeldung und eine jährliche Überprüfung - mit einer Zeile pro Person oder Gruppe.
keywords: Rollen, Berechtigungen, Arbeitsblatt, Bereichseinschränkung, Teilbaum, Sprache, Zwei-Faktor-Anmeldung, Freigabe-Workflow, Ehrenamtliche, Mitarbeiter, roles, permissions, worksheet, Editor, Administrator, Member, Anonymous, section limitation, subtree, language, infocollector/read, user/hygiene, two-factor login, approval workflow, volunteers, staff
type: help
---
# Arbeitsblatt Rollen und Berechtigungen

Füllen Sie dieses Arbeitsblatt aus, bevor Sie Konten anlegen, und überprüfen Sie es jedes Jahr. Es wendet die
Ratschläge aus [Rollen für Ihr Team](../../kunden/alltag/rollen-fuer-ihr-team.md) und
[Ehrenamtliche als Redakteure](../ehrenamtliche/ehrenamtliche-als-redakteure.md) auf eine Non-Profit-Organisation an, in der viele
Redakteure Ehrenamtliche sind, die kommen und gehen.

## Inhalt

1. [Die Rollen, mit denen Sie beginnen](#die-rollen-mit-denen-sie-beginnen)
2. [Schritt 1: die Aufgaben auflisten](#schritt-1-die-aufgaben-auflisten)
3. [Schritt 2: eine Zeile pro Gruppe](#schritt-2-eine-zeile-pro-gruppe)
4. [Schritt 3: die sensiblen Berechtigungen](#schritt-3-die-sensiblen-berechtigungen)
5. [Schritt 4: überprüfen](#schritt-4-überprüfen)

## Die Rollen, mit denen Sie beginnen

Eine Standardinstallation arbeitet mit diesen Rollen ([Rollen für Ihr Team](../../kunden/alltag/rollen-fuer-ihr-team.md#die-rollen-mit-denen-sie-beginnen)):

| Rolle | Wer sie hat | Kurz gesagt |
|---|---|---|
| Anonymous | Jeder Besucher, der nicht angemeldet ist | Lesen der öffentlichen Site |
| Member | Registrierte Besucher | Anmelden, Lesen von Inhalten nur für Mitglieder |
| Editor | Das Redaktionsteam | Inhalte in der Administrationsoberfläche anlegen und bearbeiten |
| Administrator | Sehr wenige Personen | Alles |

Weisen Sie Rollen **Gruppen** zu, nicht einzelnen Benutzern, und schränken Sie sie nach Möglichkeit auf einen Bereich
oder einen Teilbaum ein.

## Schritt 1: die Aufgaben auflisten

Haken Sie die Aufgaben ab, die es in Ihrer Organisation gibt, und nennen Sie, wer sie erledigt:

| Aufgabe | Braucht | Gibt es bei uns? | Erledigt von |
|---|---|---|---|
| Autor | Entwürfe schreiben, nicht veröffentlichen (Rolle Editor hinter einem Freigabe-Workflow) | | |
| Redakteur | In seinem Bereich schreiben und veröffentlichen | | |
| Freigebender | Wartende Versionen freigeben ([Freigaben und Prüfung](../../kunden/alltag/freigabe-und-pruefung.md)) | | |
| Layout-Redakteur | Kampagnen- und Landingpages ändern (Richtlinien von `explayouts`) | | |
| Formularbearbeiter | Eingänge lesen und exportieren (`infocollector/read`) | | |
| Newsletter-Redakteur | Newsletter-Ausgaben schreiben und versenden | | |
| Kontoverwalter | Konten prüfen und deaktivieren (`user/hygiene`) | | |
| Übersetzer | In eine Sprache übersetzen (eingeschränkt nach Sprache) | | |
| Shop-Mitarbeiter | Spendenbestellungen bearbeiten, falls Sie den Shop nutzen | | |
| Administrator | Alles; höchstens zwei Personen | | |

## Schritt 2: eine Zeile pro Gruppe

| Gruppe | Rolle(n) | Eingeschränkt auf (Bereich, Teilbaum, Sprache) | Zwei-Faktor-Anmeldung | Mitglieder |
|---|---|---|---|---|
| Administratoren | Administrator | keine Einschränkung | Pflicht | |
| Redakteure unter den Mitarbeitern | Editor | | Pflicht | |
| Ehrenamtliche Autoren | Editor + Freigabe-Workflow | z. B. Bereich „News“ | Empfohlen | |
| Koordination der Ehrenamtlichen | `infocollector/read` | | Pflicht | |
| Übersetzer | Editor, übersetzen | Sprache: | Empfohlen | |
| Vorstandsmitglieder (lesen Vorstandsunterlagen) | Member + Vorstandsbereich | Bereich „Board“ | Empfohlen | |
| _Ihre Gruppe_ | | | | |

Zwei-Faktor-Anmeldung: [Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md). Bereiche für Einschränkungen:
[Bereiche und Objektzustände](../../ersteller/website-ersteller/sektionen-und-objektzustaende.md).

## Schritt 3: die sensiblen Berechtigungen

Tragen Sie die Namen der wenigen Personen ein, die jede dieser Berechtigungen haben:

| Berechtigung | Warum sie sensibel ist | Inhaber |
|---|---|---|
| Administrator (Vollzugriff) | Kann alles | |
| Eine Rolle, die Rollen ändern darf | Inhaber können sich jeden Zugriff selbst geben | |
| `infocollector/read` | Liest personenbezogene Daten von Ehrenamtlichen | |
| `audit/read` | Liest das Audit-Protokoll | |
| Shop-Bestellungen | Sieht Namen und Adressen von Spendern | |
| Newsletter-Abonnenten | Sieht die Mailadressen von Unterstützern | |

## Schritt 4: überprüfen

| Wann | Was | Lesen |
|---|---|---|
| Eine ehrenamtliche Person geht | Das Konto in derselben Woche deaktivieren und aus der Gruppe verschieben | [Wenn Ehrenamtliche gehen](../betrieb/wenn-ehrenamtliche-gehen.md) |
| Jedes Quartal | **Benutzerkonten prüfen** für lange nicht genutzte Konten ausführen | [Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md) |
| Jedes Jahr | Dieses Arbeitsblatt Zeile für Zeile durchgehen | [Zugriffsüberprüfungen](../../unternehmen/sicherheit/zugriffspruefungen.md) |

```text
Reviewed by ............................ date ________
Changes made ........................... ______________________
```

## Verwandte Seiten

- [Vorlagen und Arbeitsblätter](README.md)
- [Arbeitsblatt Rollen im Team](../../kunden/alltag/arbeitsblatt-teamrollen.md)
- [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
