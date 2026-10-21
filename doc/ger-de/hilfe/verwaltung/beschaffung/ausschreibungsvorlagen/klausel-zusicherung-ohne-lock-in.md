---
title: "Ausschreibungsvorlage: Klausel zur Zusicherung ohne Lock-in"
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 170
summary: Klauseltext, in dem ein Lieferant zusichert, dass die gelieferte Exponential-Website keine technische, vertragliche oder kommerzielle Bindung schafft - keine geschlossenen Komponenten, keine proprietären Formate, kein exklusiver Zugang, keine Ausstiegsgebühren - mit den Prüfungen, die jedes Versprechen testen.
keywords: Lock-in, Zusicherung, Gewährleistung, Anbieterabhängigkeit, Portabilität, Interoperabilität, geschlossene Komponenten, proprietäre Formate, Ausstiegsgebühren, exklusiver Zugang, offene Standards, Ausschreibungsvorlage, lock-in, warranty, vendor lock-in, portability, interoperability, closed components, proprietary formats, exit fees, exclusive access, open standards, tender template
type: help
---
# Ausschreibungsvorlage: Klausel zur Zusicherung ohne Lock-in

Diese Seite richtet sich an Beschaffungsstellen, die eine einzige Klausel wollen, die den Zweck des Einkaufs von Open
Source erfasst: die Freiheit, den Lieferanten zu wechseln. Lock-in entsteht selten durch die Lizenz der Kernsoftware; er
entsteht durch geschlossene Erweiterungen, undokumentierte Integrationen, fehlenden Quellcode, exklusive Konten und
Ausstiegsgebühren. Die Zusicherung unten führt diese einzeln auf, damit sich jede prüfen lässt.

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Woher Lock-in kommt](#woher-lock-in-kommt)
2. [Die Klausel](#die-klausel)
3. [Wie Sie jedes Versprechen prüfen](#wie-sie-jedes-versprechen-prüfen)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Woher Lock-in kommt

| Art | Beispiel | Begegnet durch |
|---|---|---|
| Technisch | eine geschlossene Erweiterung, von der die Website abhängt; Daten in einem undokumentierten Format | W.1, W.2 |
| Wissen | keine Dokumentation; Konfiguration nur dem Lieferanten bekannt | W.3 |
| Zugang | Domain, Zertifikate, Repository oder Hosting-Konten beim Lieferanten | W.4 |
| Kommerziell | Gebühren für Exporte, für den Zugang zu den eigenen Daten oder dafür, dass eine andere Partei das System pflegt | W.5 |
| Vertraglich | Exklusivität; Zustimmung nötig für Änderungen durch andere | W.6 |

## Die Klausel

```text
W. WARRANTY OF NO LOCK-IN

The Supplier warrants that, for the duration of the contract and after it:

W.1 Components. The delivered system contains no component that the Customer
    cannot use, change and have maintained by another party under an open source
    license, except those listed in <annex> with their license, purpose and a
    named replacement.

W.2 Formats and interfaces. All content and data can be exported by the Customer
    in open, documented formats, and all interfaces built under the contract are
    documented.

W.3 Knowledge. The documentation delivered under <documentation clause> is
    sufficient for a competent third party to operate, change and rebuild the
    system.

W.4 Accounts and assets. Domain names, certificates, source repositories,
    hosting and third-party service accounts used for the Customer's system are
    registered to the Customer, or transferred to it on request within <time>.

W.5 No fees for freedom. No fee is charged for exports, for the return package,
    for access to the Customer's own data, or for another party maintaining the
    system.

W.6 No exclusivity. Nothing in the contract or in the Supplier's licenses
    requires the Supplier's consent for the Customer or a third party to change,
    host or maintain the system.

W.7 Remedy. If a warranty in this clause is found to be untrue, the Supplier
    remedies it at its own cost within <time> [and <other remedy>].
```

## Wie Sie jedes Versprechen prüfen

| Versprechen | Prüfung mit Exponential |
|---|---|
| W.1 | Vergleichen Sie `ActiveExtensions[]` und `composer.json` mit der Komponentenliste; Lizenzen der Erweiterungen auf der About-Seite (`/ezinfo/about`) ([About- und Paketseiten](../../../../../eng-us/features/6.0/about-and-package-pages.md)). Der Kernel steht unter der GPL Version 2 oder neuer ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)) |
| W.2 | Führen Sie einen Export des Website-Archivs aus und öffnen Sie das Manifest ([Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md)); REST API für den laufenden Zugriff ([REST API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md)) |
| W.3 | Lassen Sie Ihr eigenes Personal oder einen Dritten der Dokumentation zum Deployment auf einem Testrechner folgen; `./console exp:install` gibt mit einem Befehl eine saubere Basis ([Exponential mit einem Befehl installieren](../../../../../eng-us/features/6.0/install-in-one-command.md)) |
| W.4 | Verlangen Sie die Liste der Konten und ihrer Inhaber |
| W.5, W.6 | Lesen Sie das Preisblatt und die Lizenzbedingungen |

Öffentliche Fakten, die helfen: Der Code ist öffentlich auf GitHub und Packagist, jedes Unternehmen darf ihn betreuen,
und das Projekt hat neben 7x auch Partner ([Support erhalten](../../../projekt/unterstuetzung-erhalten.md),
[Umsetzungspartner](../../../partner/implementierungspartner.md)).

## Hinweise für die bewertende Person

- Ein Bieter kann unter W.1 ehrlich eine geschlossene Komponente aufführen; bewerten Sie den Ersatzplan, statt das
  Angebot auszuschließen.
- Führen Sie W.2 während des Proof of Concept durch, nicht nach der Unterzeichnung.

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Klausel zu Ausstieg und Datenrückgabe](klausel-ausstieg-und-datenrueckgabe.md)
- [Klausel zu Open-Source-Lizenz und Verfügbarkeit des Quellcodes](klausel-open-source-lizenz-und-quellcode.md)
- [Anforderungen an Ausstieg und Portabilität](../anforderungen/anforderungen-an-ausstieg-und-portabilitaet.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
