---
title: Partnerportal planen
audience: businesses, project-manager, partner
category: business-solutions
order: 10
summary: Die Entscheidungen, die eine Organisation trifft, bevor sie ein Partner-, Kunden-, Händler- oder Lieferantenportal auf Exponential baut - Zielgruppen und Stufen, was geteilt wird, wie Konten freigegeben werden, wo das Portal liegt und wer es verantwortet.
keywords: Partnerportal, Extranet planen, Kundenportal, Händlerportal, Lieferantenportal, Stufen, Zielgruppen, B2B, Kontofreigabe, Datentrennung, Verantwortung, Anforderungen, partner portal, extranet planning, customer portal, dealer portal, supplier portal, tiers, audiences, account approval, data separation, ownership, requirements
type: help
---
# Partnerportal planen

Diese Seite ist für Führungskräfte, die Partner- oder Kundenbeziehungen verantworten und ein Portal dafür möchten. Sie
macht aus den geschäftlichen Fragen Entscheidungen und zeigt, welche Funktion von Exponential jede Entscheidung
beantwortet. Arbeiten Sie sie durch, bevor jemand eine Seite baut; ein Portal, dessen Struktur den Verträgen folgt, ist
viel leichter sicher zu halten.

## Inhalt

1. [Für wen das Portal ist](#für-wen-das-portal-ist)
2. [Was mit wem geteilt wird](#was-mit-wem-geteilt-wird)
3. [Entscheidungen und die Funktionen dahinter](#entscheidungen-und-die-funktionen-dahinter)
4. [Das Trennungsmodell wählen](#das-trennungsmodell-wählen)
5. [Verantwortung auf Ihrer Seite](#verantwortung-auf-ihrer-seite)
6. [Daten und Verträge](#daten-und-verträge)
7. [Arbeitsblatt zur Planung](#arbeitsblatt-zur-planung)
8. [Verwandte Seiten](#verwandte-seiten)

## Für wen das Portal ist

Listen Sie die externen Zielgruppen auf und wie viele Organisationen jede hat. Typische Zielgruppen:

| Zielgruppe | Braucht meist |
|---|---|
| Wiederverkäufer und Partner | Preislisten, Vertriebsmaterial, Produktneuigkeiten, Formulare zur Registrierung von Geschäften |
| Kunden | ihre eigenen Dokumente, Handbücher, Supportartikel, Bestellhistorie |
| Händler | Bestellen zu eigenen Preisen, Bestandsinformationen, Marketingmaterial |
| Lieferanten | Spezifikationen, Qualitätsdokumente, eine Möglichkeit, Zertifikate hochzuladen |

Innerhalb einer Zielgruppe gibt es oft **Stufen** (Gold- und Silber-Partner oder Regionen). Stufen bestimmen einen
großen Teil der Struktur.

## Was mit wem geteilt wird

Zeichnen Sie eine einfache Matrix, bevor Sie irgendetwas Technisches entscheiden:

| Inhalt | Alle Partner | Gold-Stufe | Nur ein Partner | Öffentlich |
|---|---|---|---|---|
| Produktneuigkeiten | ja | ja | | einige |
| Preisliste | | ja | | |
| Vertragsdokumente | | | ja | |
| Marketingmaterial | ja | ja | | |

Jede Spalte „Nur ein Partner“ bedeutet einen Bereich pro Partnerorganisation
([Kunden- und Partnerbereiche](kunden-und-partnerbereiche.md)). Jede Spalte „Öffentlich“ bedeutet, dass auch die
öffentliche Website den Inhalt zeigen darf; Exponential löst das, indem dasselbe Objekt beiden Siteaccesses gegeben
wird ([Getrennte Hosts und Siteaccesses](getrennte-hosts-und-siteaccesses.md)).

## Entscheidungen und die Funktionen dahinter

| Entscheidung | Optionen | Funktion | Seite |
|---|---|---|---|
| Wie Konten angelegt werden | Selbstregistrierung mit Freigabe; durch Ihre Mitarbeiter angelegt | `user/register`, Freigabelinks in der Mail, Freigabe-Workflow, Nicht aktivierte Benutzer | [Registrierung und Kontofreigabe](registrierung-und-kontofreigabe.md) |
| Wo das Portal liegt | eigener Hostname; ein Pfad der Hauptwebsite | Siteaccess-Zuordnung | [Getrennte Hosts und Siteaccesses](getrennte-hosts-und-siteaccesses.md) |
| Wie Partner getrennt werden | Sektionen pro Stufe; Teilbäume pro Partner; Objektzustände | Einschränkungen von Rollen | [Kunden- und Partnerbereiche](kunden-und-partnerbereiche.md) |
| Ob Partner hochladen | nur lesen; Upload in den eigenen Ordner | `content/create`, beschränkt nach Klasse und Teilbaum | [Dateien mit Partnern teilen](dateien-mit-partnern-teilen.md) |
| Ob Partner bestellen | nein; eingebauter Shop mit Gruppenermäßigungen | Shop, Ermäßigungsgruppen | [Händler- und Lieferantenportale](haendler-und-lieferantenportale.md) |
| Stärke der Anmeldung | Passwort; zweistufige Anmeldung | `sevenx_authentication_2fa` | [Zwei-Faktor und Anmeldung](../../../../eng-us/help/businesses/intranets/two-factor-and-sign-in.md) |
| Wie Zugriffe geprüft werden | vierteljährliche Prüfung | Rollenseiten, Benutzerkonten prüfen, Audit-Trail | [Berechtigungen im Extranet prüfen](berechtigungen-im-extranet-pruefen.md) |

## Das Trennungsmodell wählen

| Modell | Passt, wenn | Aufwand, wenn die Zahl der Partner wächst |
|---|---|---|
| Eine Sektion pro Stufe | die Inhalte für jeden Partner einer Stufe gleich sind | gering: neue Partner treten einer Gruppe bei |
| Ein Teilbaum pro Partnerorganisation | jeder Partner eigene Dokumente hat | ein Ordner und eine Rollenzuordnung pro Partner |
| Objektzustände (zum Beispiel Intern, Partner, Öffentlich) | dieselben Einträge im Laufe der Zeit die Zielgruppe wechseln | gering, sobald die Zustandsgruppe existiert |
| Eigene Installation pro Partner | eine strenge vertragliche Trennung verlangt wird | am höchsten: eine Website pro Partner zu betreiben |

Die meisten Portale kombinieren die ersten beiden: Sektionen der Stufen für gemeinsames Material und ein Teilbaum pro
Partner für private Dokumente.

## Verantwortung auf Ihrer Seite

- Ein **Portalverantwortlicher**, der die Struktur festlegt und neue Partner freigibt.
- **Verantwortliche für die Beziehungen**, die Konten für ihre Partner anfordern und bestätigen.
- **Redakteure** für jeden Inhaltsbereich.
- **Eine freigebende Person** für Registrierungen, mit Vertretung.
- **Die IT oder der Hoster** für Anmeldung, Backups und Aktualisierungen.

Halten Sie schriftlich fest, wer ein neues Konto freigibt und wer es schließt, wenn ein Vertrag endet. Konten
rechtzeitig zu schließen, ist die am häufigsten vergessene Aufgabe jedes Portals
([Berechtigungen im Extranet prüfen](berechtigungen-im-extranet-pruefen.md)).

## Daten und Verträge

Ein Portal enthält personenbezogene Daten von Menschen anderer Organisationen: Namen, E-Mail-Adressen,
Anmeldeprotokolle. Planen Sie mit Ihrer Ansprechperson für den Datenschutz, was erhoben und wie lange es aufbewahrt
wird ([DSGVO-Grundlagen](../../../../eng-us/help/businesses/security/gdpr-basics.md),
[Personenbezogene Daten in Exponential](../../../../eng-us/help/businesses/security/personal-data-in-exponential.md)). Diese Seite ist keine
Rechtsberatung.

## Arbeitsblatt zur Planung

- [ ] Zielgruppen und die Zahl der Organisationen in jeder.
- [ ] Stufen und was jede Stufe bekommt.
- [ ] Die Matrix, was geteilt wird.
- [ ] Trennungsmodell gewählt.
- [ ] Verfahren für Anlage und Freigabe von Konten, mit Namen.
- [ ] Hostname für das Portal.
- [ ] Bedarf an Uploads und Bestellungen.
- [ ] Anforderungen an die Anmeldung.
- [ ] Prüfrhythmus und verantwortliche Person.
- [ ] Aufbewahrung der Daten von Konten beendeter Partnerschaften.

## Verwandte Seiten

- [Extranets und Partnerportale](README.md)
- [Ein Exponential-Projekt planen](../../fuehrung/ein-exponential-projekt-planen.md)
- [Risikomanagement](../../fuehrung/risikomanagement.md)
- [Checkliste Anforderungen](../../../../eng-us/help/businesses/templates/requirements-checklist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
