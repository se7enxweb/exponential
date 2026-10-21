---
title: "Rollenentwurf: externe Dienstleister und befristet Beschäftigte"
audience: government, admin, project-manager
category: public-sector
order: 100
summary: Wie Sie einer Agentur, einem externen Dienstleister oder befristet Beschäftigten genau den Zugang geben, den ein Vertrag abdeckt - ein Ordner, eine Website, ein fester Zeitraum - mit persönlichen Konten, einer eigenen Benutzergruppe, eingeschränkten Zuweisungen, zweistufiger Anmeldung und einem sauberen Vertragsende.
keywords: externer Dienstleister, Lieferant, Agentur, befristet Beschäftigte, externe Kräfte, Zugang für Anbieter, Zugang für Dritte, befristet, ein Ordner, Mit Einschränkungen zuordnen, Deaktivieren und verschieben, Ausscheiden, Vertragsende, contractor, supplier, agency, temporary staff, external staff, vendor access, third party access, fixed term, one folder, Assign with limitation, Deactivate and move, offboarding, contract end
type: help
---
# Rollenentwurf: externe Dienstleister und befristet Beschäftigte

Diese Seite richtet sich an Administratoren und Vertragsverantwortliche in öffentlichen Stellen, die eine externe Agentur, einen
Lieferanten oder befristet Beschäftigte an der Website arbeiten lassen. Ziel ist ein Zugang, der zum Vertrag passt und mit ihm endet.
Alles nutzt das Rollenmodell aus [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md) und die
Kontenwerkzeuge aus [Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md).

## Inhalt
1. [Grundsätze](#grundsätze)
2. [Eine Rolle für einen Dienstleister für Inhalte](#eine-rolle-für-einen-dienstleister-für-inhalte)
3. [Eine Rolle für einen Dienstleister für Entwicklung oder Gestaltung](#eine-rolle-für-einen-dienstleister-für-entwicklung-oder-gestaltung)
4. [Während des Vertrags](#während-des-vertrags)
5. [Am Ende des Vertrags](#am-ende-des-vertrags)
6. [Verwandte Seiten](#verwandte-seiten)

## Grundsätze

- **Nur persönliche Konten.** Ein Konto je Person, nie ein gemeinsamer Anmeldename für die „Agentur“: Das Prüfprotokoll erfasst Personen,
  und **Benutzerkonten prüfen** kann dann Konten finden, die niemand nutzt. Der Filter **Doppelte E-Mail-Adresse** findet Konten, die
  mit einer gemeinsamen Teamadresse angelegt wurden.
- **Eine Gruppe je Vertrag.** Legen Sie die Konten des Dienstleisters in eine eigene Benutzergruppe (zum Beispiel „Vertrag 2026-07 -
  Webagentur“), sodass eine Zuweisung Zugang gibt und ein Entfernen ihn wieder nimmt.
- **Nach Ort einschränken.** Weisen Sie die Rolle mit **Mit Einschränkungen zuordnen: Teilbaum** dem Ordner zu, den der Vertrag abdeckt.
- **Keine Administrationsrechte.** Keine Rechte für `role`, `setup/administrate`, `audit` oder Benutzerkonten; Dienstleister, die mehr als
  Inhaltsarbeit brauchen, brauchen in der Regel eine Testkopie, keinen Zugang zur Produktion.
- **Befristen Sie den Zugang.** Exponential hat kein Ablaufdatum für eine Rollenzuweisung oder ein Konto. Tragen Sie das Enddatum in Ihr
  Vertragsregister ein und handeln Sie danach (siehe unten).

## Eine Rolle für einen Dienstleister für Inhalte

Für eine Agentur, die Inhalte in einem Ordner schreibt oder migriert (das Muster „Fachbereichsredaktion“, enger gefasst):

| Modul | Funktion | Einschränkungen | Warum |
|---|---|---|---|
| user | login | SiteAccess: der Redaktions-Siteaccess (nicht die Administration) | dort anmelden, wo Inhalte bearbeitet werden |
| content | read | Sektion: Standard, Media | den Zusammenhang ihres Ordners sehen |
| content | create, edit | Klasse: die Klassen aus dem Auftrag | nur anlegen, was der Vertrag abdeckt |
| content | versionread, diff | keine | ihre eigenen Versionen verfolgen |
| content | dashboard | keine | **Meine Entwürfe**, **Meine ausstehenden Elemente** |
| user | password | keine | das eigene Passwort ändern |

Weisen Sie sie der Gruppe des Vertrags **mit Einschränkung: Teilbaum** = der Vertragsordner zu. Vergeben Sie `content/remove` nicht, es
sei denn, der Vertrag umfasst das Entfernen von Inhalten; nehmen Sie die Gruppe nicht vom Freigabe-Workflow aus, damit eine beschäftigte
Person alles freigibt, bevor es öffentlich ist ([Rollenentwurf: Freigebende](rollenentwurf-freigebende.md)).

## Eine Rolle für einen Dienstleister für Entwicklung oder Gestaltung

Arbeit an Templates und Code gehört in die Versionsverwaltung und auf eine Testkopie, nicht in die Produktion: Wenn
[Durch Bereitstellung verwaltete Konfiguration](../aktualisierungen-und-aenderungen/verwaltet-durch-bereitstellung.md) eingeschaltet ist, sind der
Template-Editor und die Einstellungsseiten auf der Live-Website ohnehin gesperrt. In der Produktion braucht ein solcher Dienstleister meist
höchstens:

| Modul | Funktion | Warum |
|---|---|---|
| user | login (SiteAccess: Administration) | anmelden |
| setup | logs | maskierte Logs lesen, um ein Problem zu diagnostizieren |
| setup | system_info | den Bericht der Systeminformationen lesen |

Debug-Ausgabe nur für die eigene Adresse ist mit einem Ablaufdatum in `DebugIPList[]` möglich
([Sicherheit und Audit, Schritt 4](../../../../anleitungen/sicherheit-und-audit.md#4-debug-ausgabe-nur-für-ihre-adresse-5-minuten)).

## Während des Vertrags

- Verlangen Sie die zweistufige Anmeldung für die Konten des Vertrags ([Zwei-Faktor für Beschäftigte](zwei-faktor-anmeldung-fuer-beschaeftigte.md)).
- Sehen Sie sich den Kanal `access` des Prüfprotokolls für ihre Anmeldungen an, wenn Ihr Vertrag eine Überwachung verlangt.
- Wenn sich der Ordner ändert, ändern Sie die eingeschränkte Zuweisung, nicht die Rolle.

## Am Ende des Vertrags

1. Markieren Sie auf der Rollenseite die Zuweisung der Vertragsgruppe und klicken Sie auf **Ausgewählte entfernen**: Die Konten verlieren
   die Rolle sofort.
2. **Benutzer > Benutzerkonten prüfen**: Suchen Sie die Konten, markieren Sie sie und klicken Sie auf **Deaktivieren**; ihre Sitzungen enden.
3. Wenn Ihre Website es nutzt, wenden Sie stattdessen **Deaktivieren und verschieben** auf die Konten an: Sie werden deaktiviert und in eine
   verborgene Gruppe ohne Rollen verschoben, wobei ihre Inhalte und der Nachweis dessen, was sie getan haben, erhalten bleiben
   ([Deaktivieren und verschieben](../../../../funktionen/6.0/benutzerpflege.md#deaktivieren-und-verschieben)).
4. Exportieren Sie die Liste als CSV für die Vertragsakte (**Liste als CSV exportieren**).
5. Entfernen Sie IP-Adressen des Dienstleisters aus allen Freigabelisten für Debug-Ausgabe oder Netzwerkzugang.

Das Entfernen eines Benutzerkontos ist endgültig (Benutzerobjekte kommen nie in den Papierkorb), deshalb wird Deaktivieren oder
Deaktivieren und verschieben bevorzugt: Das Prüfprotokoll und die Inhalte behalten einen gültigen Eigentümer.

## Verwandte Seiten
- [Einen Dienstleister nur einen Ordner bearbeiten lassen](../../aufgaben/personen-und-berechtigungen/einen-auftragnehmer-nur-einen-ordner-bearbeiten-lassen.md)
- [Eintritt, Wechsel und Austritt](eintritt-wechsel-austritt.md)
- [Checkliste für das Ausscheiden von Kunden (Hosting)](../../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)
- [Grundsätze der geringsten Rechte](prinzip-der-geringsten-rechte.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
