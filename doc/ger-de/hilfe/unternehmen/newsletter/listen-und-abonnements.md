---
title: Listen und Abonnements
audience: businesses, sales-marketing, editor
category: business-solutions
order: 30
summary: Wie Newsletter-Systeme, Listen und Abonnenten in cjw_newsletter zusammenhängen, was jeder Abonnementstatus für ein Unternehmen bedeutet, wie Sie Abonnenten per CSV übernehmen und herausgeben und wer Abonnentendaten sehen darf.
keywords: Newsletter-Liste, Abonnenten, Abonnementstatus, Newsletter-System, CSV-Import, CSV-Export, Interessen, virtuelle Liste, Abonnentendaten, Berechtigungen, newsletter list, subscribers, subscription status, newsletter system, CSV import, CSV export, interests, virtual list, subscriber data, permissions
type: help
---
# Listen und Abonnements

Diese Seite richtet sich an die Menschen, denen eine Newsletter-Zielgruppe gehört: Mitarbeiter im Marketing,
Mitgliederbeauftragte, Führungskräfte. Sie erklärt, wie die Erweiterung `cjw_newsletter` Abonnenten organisiert, was die
Status in der Praxis bedeuten, wie Sie Abonnenten übernehmen und herausgeben und wer Zugriff haben sollte. Die
Einzelheiten für Redakteure stehen in
[Newsletter-Listen und Abonnements](../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md).

## Inhalt

1. [Systeme, Listen und Abonnenten](#systeme-listen-und-abonnenten)
2. [Was die Status bedeuten](#was-die-status-bedeuten)
3. [Wo Sie Abonnenten verwalten](#wo-sie-abonnenten-verwalten)
4. [Importieren und exportieren](#importieren-und-exportieren)
5. [Interessen und Sprachen](#interessen-und-sprachen)
6. [Wer Abonnentendaten sehen darf](#wer-abonnentendaten-sehen-darf)
7. [Eine Struktur, die einfach bleibt](#eine-struktur-die-einfach-bleibt)

## Systeme, Listen und Abonnenten

| Ebene | Was sie ist | Geschäftliches Beispiel |
|---|---|---|
| Newsletter-System | Inhalt (Klasse `cjw_newsletter_system`), der Listen gruppiert | „Kunden-Newsletter“ |
| Liste | Inhalt (Klasse `cjw_newsletter_list`), den Menschen abonnieren | „Monatliche Angebote“, „Fachhandelskunden“ |
| Newsletter-Benutzer | Eine Person (eine E-Mail-Adresse) über alle Listen hinweg | Der Abonnent |
| Abonnement | Eine Person auf einer Liste, mit einem Status | „Abonniert Monatliche Angebote, bestätigt“ |

Systeme und Listen liegen im Inhaltsbaum unterhalb des Knotens, der in `[NewsletterSettings] RootFolderNodeId` gesetzt
ist ([cjw_newsletter, Die Teile](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-parts)). Weil sie Inhalte sind,
können Sie sie wie alle anderen Inhalte platzieren, umbenennen und mit Berechtigungen versehen.

## Was die Status bedeuten

| Status | Bedeutung | Erhält Ausgaben? |
|---|---|---|
| Wartend | Abonniert, Bestätigungsmail gesendet, noch nicht bestätigt | Nein |
| Bestätigt | Hat auf den Bestätigungslink geklickt | Ja |
| Freigegeben | Bestätigt und, wo die Liste es verlangt, von einem Administrator freigegeben | Ja |
| Vom Benutzer oder von einem Administrator entfernt | Hat die Liste verlassen | Nein |
| Soft oder Hard Bounce | Die Mail konnte nicht zugestellt werden; nach `BounceThresholdValue` Bounces (Standard 3) erhält der Benutzer nichts mehr | Nein |
| Auf der Blacklist | Die Adresse bekommt nie Mails | Nein |

„Nur bestätigte oder freigegebene Abonnenten erhalten Ausgaben“
([Newsletter-Listen und Abonnements](../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#statuses)).
Für ein Unternehmen bedeutet das: Die entscheidende Zahl ist „bestätigt“ plus „freigegeben“, nicht die Summe aller
Zeilen.

> **Tipp**
> Eine große Zahl wartender Abonnements bedeutet meist, dass die Bestätigungsmail nicht ankommt. Prüfen Sie den
> Transport und den Spam-Ordner einer Testadresse, bevor Sie dem Formular die Schuld geben
> ([Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md)).

## Wo Sie Abonnenten verwalten

| Aufgabe | Ansicht in der Administration |
|---|---|
| Die Abonnements einer Liste sehen | `newsletter/subscription_list` |
| Eine Person über alle Listen finden, bearbeiten oder anlegen | `newsletter/user_list`, `user_view`, `user_edit`, `user_create` |
| Eine Adresse endgültig sperren | `newsletter/blacklist_item_list`, `blacklist_item_add` |
| Bounce-Postfächer und ihre Nachrichten | `newsletter/mailbox_list`, `mailbox_item_list` |

Seit 4.1.17 blättert, sortiert und filtert die Benutzerliste (vorher zeigte sie nur die ersten zehn Benutzer); die
Blacklist, die Importe und die Bounces haben dieselben Filter, Sortierungen und Seiten
([cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#dashboard-commands-and-background-runs-4117)).

## Importieren und exportieren

**Import.** Release 4.2.0 importiert CSV-Dateien mit einer Spaltenzuordnung, einer Vorschau und einem Probelauf. Ein
echter Import verlangt eine **Einwilligungsquelle**, einen Satz, der sagt, wo und wie die Menschen zugestimmt haben,
zum Beispiel ein Anmeldeformular auf einer Veranstaltung. Die Double-Opt-in-Mail wird an importierte Personen nicht
gesendet, weil die Einwilligung dort gegeben wurde, wo die Adressen gesammelt wurden, und Sie dafür verantwortlich sind,
diese Einwilligung nachweisen zu können. Jedes importierte Abonnement wird mit der Quelle `import` in das
Einwilligungsprotokoll der Website geschrieben, und der abgeschlossene Import schreibt das Audit-Ereignis
`data.import.csv`, ohne die Adressen selbst
([importexport.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/importexport.md)).

**Export.** `newsletter/subscriber_export/<list content object id>` exportiert Abonnenten mit Filtern nach Status und
Datum. Zellen, die mit `=`, `+`, `-` oder `@` beginnen, bekommen ein vorangestelltes Apostroph, damit eine
Tabellenkalkulation sie nicht als Formeln ausführt, und jeder Download wird als Audit-Ereignis `data.export.csv`
festgehalten (derselbe Leitfaden).

> **Warnung**
> Ein Export enthält personenbezogene Daten. Bewahren Sie ihn nur so lange auf, wie die Aufgabe dauert, für die er
> gebraucht wurde, und speichern Sie ihn wie alle anderen personenbezogenen Daten. Siehe
> [Sicherheit und Datenschutz](../sicherheit/README.md).

**Umstieg von der alten Erweiterung eznewsletter.** Der Befehl `ext:cjw_newsletter:import-eznewsletter` übernimmt
Listen und Abonnenten von dort (derselbe Leitfaden, Abschnitt „Migration from eznewsletter“).

## Interessen und Sprachen

Eine Liste kann Abonnenten **Interessen** wählen lassen, und eine Ausgabe kann in mehreren Sprachen versendet werden,
wobei jeder Abonnent seine eigene Sprache erhält. Beides wählen Abonnenten auf ihrer Einstellungsseite
([rendering.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/rendering.md)). Für eine kleine Organisation
sind Interessen oft die bessere Wahl als viele getrennte Listen: eine Liste, eine Ausgabe, ein Block, der sich je nach
Leser unterscheidet.

Für Websites in mehreren Sprachen siehe auch [Mehrsprachige Websites](../mehrsprachig/README.md).

## Wer Abonnentendaten sehen darf

Das Newsletter-Modul hat eigene Richtlinienfunktionen in `modules/newsletter/module.php` der Erweiterung, zum Beispiel
`subscribe`, `configure` und `unsubscribe` für die öffentlichen Formulare, `subscription_list` und `user_list` für die
Administration und `import_export` für Import und Export von 4.2.0. Der Leitfaden der Erweiterung sagt,
`newsletter/import_export` „nur den Rollen zu geben, die Abonnentendaten sehen und ändern dürfen“
([importexport.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/importexport.md)).

- [ ] Besucher bekommen nur die öffentlichen Funktionen (abonnieren, einstellen, abmelden).
- [ ] Autoren bekommen, was sie zum Schreiben und Testen von Ausgaben brauchen, nicht die Abonnentenlisten.
- [ ] Eine oder zwei namentlich benannte Personen bekommen die Abonnentenlisten, den Import und den Export.
- [ ] Prüfen Sie diese Rollen, wenn Menschen die Stelle wechseln ([Zugriffsprüfungen](../sicherheit/zugriffspruefungen.md)).

Wie Rollen und Richtlinien allgemein funktionieren: [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md).

## Eine Struktur, die einfach bleibt

- Beginnen Sie mit einem System und einer Liste pro Versprechen an die Abonnenten.
- Verwenden Sie Interessen statt vieler fast gleicher Listen.
- Benennen Sie Listen so, wie Abonnenten sie beschreiben würden; der Name erscheint auf den Seiten zum Abonnieren und
  zu den Einstellungen.
- Archivieren Sie eine Liste, die nicht mehr versendet wird, statt sie für einen anderen Zweck wiederzuverwenden; die
  Abonnenten haben dem alten Zweck zugestimmt.

## Verwandte Seiten

- [Newsletter für Unternehmen](README.md)
- [Opt-in und Einwilligung](opt-in-und-einwilligung.md)
- [Bounces und Listenpflege](bounces-und-listenpflege.md)
- [Newsletter-Listen und Abonnements (Website-Ersteller)](../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
