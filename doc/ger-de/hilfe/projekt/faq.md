---
title: "Exponential: Häufige Fragen"
audience: user, editor, admin, developer, designer, partner, supporter
category: project
order: 10
summary: Die Antworten der FAQ auf exponential.earth, in eigenen Worten wiedergegeben und mit der Software abgeglichen - was Exponential ist und wie es mit eZ Publish zusammenhängt, warum Sie es wählen sollten, Module, Designs und Distributionen, das Ändern von Kerndateien, Codestandards, Releases und Versionen, PHP-Versionen, der Umzug von WordPress, das Hosting Ihrer ersten Site, die Demo und wo Sie Hilfe bekommen oder selbst helfen können.
keywords: FAQ, Häufige Fragen, Framework, fertiges Produkt, Webserver, Velocity, was ist Exponential, warum Exponential, Namensänderung, Modul, Erweiterung, Design, Distribution, Kerndateien, Überschreibung, Codestandards, Release, Version, semantische Versionierung, Migration, Hosting, Demo, Hilfe, beitragen, frequently asked questions, what is Exponential, why Exponential, eZ Publish, name change, Ibexa, module, extension, design, distribution, core files, override, coding standards, release, version, semantic versioning, PHP, WordPress, migration, hosting, demo, help, contribute, framework, complete product, web server
featured: true
type: help
---
# Exponential: Häufige Fragen

Diese Antworten folgen den FAQ-Seiten der Projektwebsite, [exponential.earth/faq](https://exponential.earth/faq),
gelesen im Oktober 2026, in den eigenen Worten dieser Hilfe wiedergegeben und mit der Software in diesem Repository
abgeglichen. Wo das Repository etwas anderes sagt, weist die Antwort darauf hin und folgt dem Repository.

![Die Seite eines Objekts in der Administration](../../../eng-us/help/images/help/pj-item.webp)

Die FAQ der Website verweist außerdem auf eine ältere FAQ in der archivierten Dokumentation,
[doc.ezpublishlegacy.se7enx.com/FAQ.html](https://doc.ezpublishlegacy.se7enx.com/FAQ.html), die sie als „schon etwas
älter“ beschreibt. Ihre Antworten zu Konzepten gelten weiterhin, ihre Versionsnummern nicht.

## Über Exponential

### Was ist Exponential?

Ein Open-Source-Content-Management-System (CMS) und Entwicklungsframework, geschrieben in PHP. Seine größte Stärke
ist ein Inhaltsmodell, das Sie selbst festlegen: Inhaltsklassen beschreiben, welche Arten von Inhalten es gibt (einen
Artikel, ein Produkt, eine Veranstaltung), Inhaltsobjekte enthalten die Inhalte, und der Inhaltsbaum ordnet sie an.
Dazu kommen Versionen, Übersetzungen, Berechtigungen, Workflows, Suche, Multisite-Fähigkeit, ein Shop und ein
Template-System. Die Seite [About Exponential](https://exponential.earth/about) der Website nennt Web-Publishing,
Medienportale, Intranets, E-Commerce und Extranets als typische Einsatzgebiete. Die vollständige Liste der Funktionen
steht in der [README](https://github.com/se7enxweb/exponential/blob/main/README.md).

### Ist Exponential dasselbe wie eZ Publish?

Exponential ist die Fortführung des Codes von eZ Publish, einem Produkt, das sein Hersteller nach fast achtzehn
Jahren eingestellt hat. Das Projekt führte ihn bis August 2025 unter dem Namen eZ Publish weiter: Die FAQ der Website
hält fest, dass der Name am 11. August 2025 auf Wunsch des Inhabers des alten Namens zu „Exponential“ (oder
„Exponential (CMS)“) geändert wurde und dass das Projekt nicht mit dem Entwickler der ursprünglichen Software
verbunden ist. Der Code, die Template-Sprache und die Erweiterungs-API blieben kompatibel: Die Dokumentationsseite der
Website merkt an, dass für eZ Publish geschriebene Beispiele mit Exponential funktionieren. Die Geschichte erzählen
[die Chronik](../../geschichte/README.md) und [Wer ist 7x?](../wer-ist-7x.md).

### Warum Exponential wählen?

Der Aufsatz [Why choose Exponential?](https://exponential.earth/faq/why-choose-exponential) der Website nennt diese
Punkte, die diese Hilfe anhand der Software bestätigen kann:

- **Langlebigkeit.** Inhalte, die vor Jahren modelliert wurden, funktionieren weiterhin: Inhaltsmodell, Templates und
  Erweiterungen sind über die Releases hinweg kompatibel geblieben, und Upgrades sind Schritt für Schritt dokumentiert
  ([Upgrades](../../installation/11-aktualisieren.md)).
- **Strukturierte, wiederverwendbare Inhalte.** Inhalte werden als Objekte mit Attributen gespeichert, nicht als
  fertige Seiten; so kann ein Inhalt an vielen Stellen und über die REST-API erscheinen.
- **Erweiterbar, ohne den Kern anzufassen.** Erweiterungen fügen Module, Datentypen, Template-Operatoren,
  Workflow-Ereignisse und mehr hinzu ([Erweiterungen](../../anleitungen/extensions.md)).
- **Trennung der Rollen.** Redakteure arbeiten an Inhalten, Designer an Templates und Überschreibungen, Entwickler an
  Erweiterungen.
- **Freie Software.** Unter der GPL dürfen Sie sie ausführen, studieren, ändern und weitergeben; niemand kann sie
  Ihnen wegnehmen. Siehe [Fragen zur Lizenz](lizenz-faq.md).

### Ist Exponential ein Framework oder ein fertiges Produkt?

Ein fertiges Produkt. Exponential ist eine vollständige, funktionierende Einheit, nicht nur ein Framework, das Sie
erst fertigstellen müssen, bevor es etwas tut: Ein einziges `composer install` liefert Ihnen einen Stack für die
schnelle Anwendungsentwicklung (RAD) von Internetanwendungen, der zugleich ein Content-Management-System, eine
Digital Experience Platform (der Seitenbaukasten Exponential Layouts, Multisite und mehrsprachige Sites, der
Schreibassistent Exponential Intelligence) und ein E-Commerce-System ist (Warenkorb, Bestellungen, Zahlungsgateways,
Bestellbelege und das Shop-Dashboard). Seit 6.0.15 gehört dazu auch ein eigener Internet- und
Webanwendungsserver, Exponential Velocity. Weil jede Schicht aus einer Quelle kommt, mit einem Installer, einer
Konsole und einer Dokumentation, kommt der Einsatz von Exponential überall auf der Welt ohne die übliche
Integrationsarbeit aus. Siehe [Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md).

### Enthält Exponential einen Webserver?

Ja, seit 6.0.15. Der Velocity Internet / Web Application Server ist Teil von Exponential: Die `composer.json` im
Wurzelverzeichnis verlangt `se7enxweb/exponential-velocity ~0.0.4.55`, sodass jede Installation ihn hat, auf PHP 8.0
und neuer. Das Projekt nennt das einen Wendepunkt, einen Grundstein und einen Schlussstein: eine neue Stufe der
Integration von Anwendung und System, die noch mehr Funktionen von Haus aus und standardmäßig ins Kernprodukt bringt.
Velocity ist die empfohlene Engine für jede Stufe, und FrankenPHP ist eine produktionsreife Alternative; Apache und
nginx mit PHP-FPM funktionieren weiter wie bisher. Die Befehle lauten `./console exp:velocity deploy`, `start`,
`restart` und `cache clear`. Siehe [Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md)
und [Die Website ausliefern](../../installation/08-die-website-ausliefern.md).

### Wer entwickelt es?

7x entwickelt und pflegt es, und die Exponential Foundation, eine gemeinnützige Organisation, betreibt das Projekt
(Fußzeile von exponential.earth). Siehe [Wer ist 7x?](../wer-ist-7x.md) und
[Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md).

### Warum hat Exponential manche Funktionen und andere nicht?

Die [About Exponential FAQ](https://exponential.earth/faq/about-exponential) der Website antwortet, dass das Projekt
neue Funktionen gegen Stabilität abwägt: Jede Änderung am Kern wird sorgfältig geprüft, und der Kern beschränkt sich
auf wesentliche Funktionen, während Erweiterungen den Rest hinzufügen. In diesem Repository sehen Sie dieselbe Regel
am Werk: Neue Funktionen kommen meist als Erweiterungen oder als standardmäßig ausgeschaltete Einstellungen, und jede
Änderung ist im [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md) festgehalten.

## Mit Exponential arbeiten

Diese Antworten folgen [Working with Exponential](https://exponential.earth/faq/working-with-exponential).

### Was ist ein Modul und was eine Erweiterung?

Die FAQ der Website nennt die Ergänzungen, die Exponential erweitern, „Module“. In der Sprache von Exponential selbst
ist die Einheit, die Sie installieren, eine **Erweiterung**: ein Ordner unter `extension/`, der Module (Gruppen von
Seiten wie `/content/view`), Datentypen, Template-Operatoren, Workflow-Ereignisse, Designs, Einstellungen und
Übersetzungen enthalten kann. Sie können Erweiterungen installieren, die andere geteilt haben, oder eigene schreiben
([Was eine Erweiterung ist](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md),
[Erweiterungen](../../anleitungen/extensions.md)).

### Was ist ein Design?

Ein Design gibt einer Site ihr Aussehen: Templates, Stylesheets, Bilder und Schriften in einem Ordner
`design/<name>/`, meist innerhalb einer Erweiterung. Ein Siteaccess wählt, welche Designs er verwendet, und das
Überschreibungssystem lässt ein Design jedes Template ersetzen, ohne das Original zu bearbeiten. Sie können ein
Design installieren, das jemand anderes gemacht hat, oder Ihr eigenes bauen.

### Was ist eine Distribution?

Ein Paket, das den Kernel mit einer ausgewählten Menge von Erweiterungen, Designs und Einstellungen verbindet,
fertig für eine bestimmte Art von Site. Für Exponential bedeutet das zweierlei: die **Site-Pakete**, die der
Setup-Assistent installiert (zum Beispiel das Design 7x Simple mit seinen Demo-Inhalten), und die **Distributionen**
der größeren Familie, etwa Exponential Platform Legacy und Exponential Platform Nexus
([Mit Exponential bauen](mit-exponential-bauen.md#die-produktfamilie)). Eine Distribution zu bauen ist Arbeit für
erfahrene Mitwirkende; eine zu installieren nicht.

### Darf ich die Kerndateien von Exponential für meine Site ändern?

Sie dürfen, die Lizenz erlaubt es, aber das Projekt rät dringend davon ab, und die FAQ der Website ebenso. Eine
geänderte Kerndatei wird beim nächsten Update überschrieben, auch bei Sicherheitsupdates, oder sie hindert Sie
überhaupt am Aktualisieren. Legen Sie Ihre Änderungen dorthin, wo Updates sie nicht berühren:

- **Templates**: eine Design-Überschreibung in Ihrer eigenen Erweiterung;
- **Einstellungen**: `settings/override/` und `settings/siteaccess/<name>/` oder das `settings/` einer Erweiterung;
- **Verhalten**: eine Erweiterung, ein Workflow-Ereignis oder eine in den Einstellungen registrierte
  Klassenüberschreibung.

Hat der Kern selbst einen Fehler, beheben Sie ihn im Projekt, nicht in Ihrer Kopie:
[Zum Projekt beitragen](zum-projekt-beitragen.md).

### Hat Exponential Codestandards?

Ja. Die Website verweist auf die [Exponential-Codestandards](https://share.exponential.earth/learn/exponential-code-standards)
auf Share Exponential und auf den Code der [Repositories von se7enxweb](https://github.com/se7enxweb) als Vorbild.
Für den Kernel ist `CONTRIBUTING.md` im Hauptverzeichnis der Installation der verbindliche Leitfaden: Sie erklärt den
Commit-Hook, die Prüfung des Codestils und das Dateimanifest, das jeder Pull-Request aktuell hält.

### Kann ich eine WordPress-Site zu Exponential umziehen?

Ja. Die FAQ der Website sagt, dass es Werkzeuge dafür gibt; diese Dokumentation hat einen vollständigen Leitfaden:
[WordPress und Exponential](../../installation/migration/wordpress.md) erklärt den WordPress-Export, wie Beiträge, Seiten,
Medien, Kategorien, Schlagwörter und Autoren auf Inhalte in Exponential abgebildet werden, die Adressen und
Weiterleitungen und den Weg zurück. Leitfäden für andere Systeme finden Sie in der
[Migrationsübersicht](../../installation/migration/README.md).

### Wo bekomme ich Hilfe?

[Unterstützung erhalten](unterstuetzung-erhalten.md) listet die kostenlosen Community-Kanäle und die bezahlten Möglichkeiten
auf. Bei einem Fehler in der Software siehe [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md).

## Versionen, Releases und Anforderungen

### Wann und wie werden neue Versionen veröffentlicht?

Die FAQ der Website sagt, dass Releases ab Exponential 6 semantische Versionierung verwenden. Im Repository wird die
Linie 6.0 als Tags `v6.0.0`, `v6.0.1` und folgende veröffentlicht (das neueste Release im Oktober 2026 ist
`v6.0.14`; 6.0.15 ist in Entwicklung). Einen festen Kalender gibt es nicht. Eine veröffentlichte Version wird nie
geändert: Eine Korrektur kommt immer als nächstes Release
([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern)). Wie
Sie Releases oder jeder Änderung beim Entstehen folgen, zeigt [Mit Git aktualisieren](../mit-git-aktualisieren.md).

### Welche PHP-Versionen werden unterstützt?

Exponential 6.0.15 läuft unter PHP 8.0 bis 8.5, und ab seinem Release 0.0.4.54 auch der Anwendungsserver Velocity
([Anforderungen](../../installation/02-voraussetzungen.md)). Die Community-Seite der Website nennt für das Release 6.0.14
„PHP 8.1 bis PHP 8.5“; maßgeblich sind die Anforderungen des Repositorys, die auch PHP 8.0 abdecken.

### Wie hoste ich meine erste Site?

Der Artikel
[Getting started: hosting your first Exponential website](https://exponential.earth/faq/getting-started-hosting-your-first-exponential-website)
der Website empfiehlt Hosting-Anbieter, die Exponential bereits installiert anbieten, damit Sie mit Inhalten statt
mit der Servereinrichtung beginnen können. Installieren Sie es selbst, deckt das [Installationsbuch](../../installation/README.md)
jeden Schritt ab, und [Erste Schritte](../erste-schritte.md) zeigt die Administration. Hosting-Unternehmen haben
einen eigenen Bereich in dieser Hilfe ([Hosting-Unternehmen: Hier beginnen](../hosting/hosting-anbieter-hier-beginnen.md)).

### Kann ich es vor der Installation ausprobieren?

Ja. Die Website nennt eine öffentliche Demo unter [demo.exponential.earth](https://demo.exponential.earth/) mit einer
Administration unter [edit.demo.exponential.earth](https://edit.demo.exponential.earth/); die Anmeldedaten stehen auf
der [Community-Seite](https://exponential.earth/about/community) der Website. Die Demo wird jeden Tag zurückgesetzt,
Sie können also alles ändern.

## Dem Projekt helfen

### Wie kann ich helfen?

Die FAQ der Website nennt Spenden und Mitgliedschaften und außerdem die vielen Wege, die kein Geld kosten: Code,
Übersetzungen, Dokumentation, Beratung zur Barrierefreiheit und Tests. Siehe
[Zum Projekt beitragen](zum-projekt-beitragen.md), [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md)
und [Geben und spenden](geben-und-spenden.md).

### Muss ich etwas unterschreiben, um Code beizutragen, und was gebe ich dabei ab?

Für den Kern-Kernel ja. 7x und die Exponential Foundation verlangen für jeden Code-Beitrag zum Kernel eine
unterschriebene Contributor Licensing Agreement (CLA), bevor er übernommen werden kann, gleich ob er als Pull Request,
Patch, Issue oder per E-Mail eingereicht wird. **Mit Unterschrift und Einreichung übertragen Sie das Urheberrecht an
Ihrem Beitrag an 7x, das sein Eigentümer wird, und geben die Kontrolle darüber auf:** 7x darf ihn auf jede Weise
verwenden, ändern, neu lizenzieren und verbreiten, auch unter anderen Lizenzen und in kommerziellen Produkten, ohne
Sie zu fragen oder zu bezahlen, und Sie können ihn weder zurückziehen noch an Bedingungen knüpfen. Sie unterschreiben
einmal für alle künftigen Beiträge. Ihre eigenen Extensions, Seitenpakete und Themes sind nicht betroffen. Siehe
[Urheberrechtsübertragung (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla) (auf Englisch).

### Ist es wirklich kostenlos?

Ja. Exponential ist freie Software unter der GNU GPL Version 2 oder später: Sie dürfen es für jeden Zweck nutzen,
auch für kommerzielle Arbeit, es ändern und weitergeben. Die Bedingungen der Lizenz gelten, wenn Sie es
weitergeben; siehe [Fragen zur Lizenz](lizenz-faq.md).

## Verwandte Seiten

- [Das Exponential-Projekt: Hier beginnen](README.md)
- [Fragen zur Lizenz](lizenz-faq.md)
- [Erste Schritte](../erste-schritte.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt
  und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
