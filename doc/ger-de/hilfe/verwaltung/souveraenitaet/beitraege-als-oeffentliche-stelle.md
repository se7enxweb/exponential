---
title: Beiträge als öffentliche Stelle
audience: government, project-manager, developer
category: public-sector
order: 170
summary: Warum und wie eine öffentliche Stelle Code, Übersetzungen, Dokumentation und Fehlermeldungen an das Projekt Exponential zurückgibt - was sich zu teilen lohnt, die Schritte zum Veröffentlichen einer Erweiterung, Korrekturen am Kernel und die internen Freigaben, die Sie einholen sollten.
keywords: beitragen, zurückgeben, öffentlicher Code, Open-Source-Strategie, Erweiterungen, Pull Request, Übersetzungen, Dokumentation, Fehlermeldungen, gemeinsame Entwicklung, Public Money Public Code, contribute, give back, public code, open source policy, extensions, translations, documentation, bug reports, shared development, public money public code
type: help
---
# Beiträge als öffentliche Stelle

Diese Seite richtet sich an Führungskräfte und Entwickler in öffentlichen Stellen, die auf Exponential aufbauen. Wenn
Sie teilen, was Sie bauen, können andere Stellen es nutzen und gemeinsam mit Ihnen pflegen; außerdem bleibt Ihr
eigener Code mit neuen Releases im Gleichschritt. Die Anleitung des Projekts zum Veröffentlichen von Erweiterungen ist
[Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md).

## Inhalt

1. [Was sich zu teilen lohnt](#was-sich-zu-teilen-lohnt)
2. [Interne Freigaben](#interne-freigaben)
3. [Eine Erweiterung veröffentlichen](#eine-erweiterung-veröffentlichen)
4. [Korrekturen am Kernel](#korrekturen-am-kernel)
5. [Übersetzungen und Dokumentation](#übersetzungen-und-dokumentation)
6. [Fehlermeldungen](#fehlermeldungen)
7. [Es von Dienstleistern verlangen](#es-von-dienstleistern-verlangen)

## Was sich zu teilen lohnt

| Teilen | Für sich behalten |
|---|---|
| Allgemeine Erweiterungen: Datentypen, Workflow-Ereignisse, Importwerkzeuge, Korrekturen zur Barrierefreiheit | Einstellungen mit Zugangsdaten, Schlüssel, Hostnamen |
| Übersetzungen der Oberfläche in Ihre Amtssprachen | personenbezogene Daten jeder Art |
| Korrekturen für Fehler, die Sie gefunden haben | Code, der Sicherheitsmaßnahmen speziell für Ihr Netz abbildet |
| Dokumentation und Schulungsmaterial, das Sie geschrieben haben | Verträge und interne Verfahren |
| Inhaltsklassen für gängige Inhalte des öffentlichen Sektors (als Pakete) | Ihre Inhalte selbst, sofern sie keine offenen Daten sind |

## Interne Freigaben

Legen Sie einmal schriftlich fest: wer im Namen der Stelle Code veröffentlichen darf, unter welcher Lizenz (das
Projekt empfiehlt für Erweiterungen GPL 2 oder später), unter welchem Konto (ein Organisationskonto, kein
persönliches) und den Prüfschritt, der vor der Veröffentlichung nach Geheimnissen und personenbezogenen Daten sucht.

## Eine Erweiterung veröffentlichen

Die Schritte in [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md): aufräumen; die Lizenzdatei
hinzufügen; `ezinfo.php` und `extension.xml` mit Version, Lizenz und Website schreiben; `composer.json` schreiben;
eine README schreiben; auf GitHub stellen; eine Version veröffentlichen und bei Packagist registrieren; sie der
Community anbieten; sie lebendig halten.

## Korrekturen am Kernel

Korrekturen am Kernel gehen als Pull Requests an das Projekt; der Ablauf vom Fork bis zum angenommenen Pull Request,
die Regeln für Commit-Nachrichten und die CI-Prüfungen stehen in
[Code zurückgeben](../../partner/code-zurueckgeben.md). Beiträge zum Kernel werden unter derselben Lizenz
veröffentlicht, GPL v2 oder später.

**Übertragung des Urheberrechts (CLA).** Bevor ein Beitrag zum Kernel angenommen werden kann, verlangen 7x und die
Exponential Foundation von dessen Rechteinhaber ein unterzeichnetes Contributor Licensing Agreement. **Mit der
Unterzeichnung und Einreichung überträgt die Stelle das Urheberrecht an dem Beitrag auf die Exponential Foundation und 7x, die dessen Inhaber werden,
und gibt die Kontrolle darüber auf:** Die Exponential Foundation und 7x dürfen ihn auf jede Weise nutzen, ändern, neu lizenzieren und verbreiten, auch
unter anderen Lizenzen und in kommerziellen Produkten, ohne die Stelle zu fragen oder zu bezahlen; die Stelle kann ihn
weder zurückziehen noch Bedingungen daran knüpfen. Ihre internen Freigaben müssen das akzeptieren und regeln, wer
unterzeichnen darf: Wenn die Stelle die Rechte an der Arbeit ihrer Mitarbeitenden hat, unterzeichnet die Stelle oder
erteilt ihre Erlaubnis (CLA, Abschnitt 6). Erweiterungen, die die Stelle baut und für ihre eigenen Websites behält,
sind nicht betroffen. Siehe
[Übertragung des Urheberrechts (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla).

## Übersetzungen und Dokumentation

Übersetzungen der Oberfläche sind `.ts`-Dateien ([Sprachen in Exponential](../../../sprachen.md)); mehrere
mitgelieferte Übersetzungen sind maschinell unterstützt, und das Projekt freut sich über eine Durchsicht durch
Muttersprachler. Die Dokumentation ist freie Dokumentation unter der GFDL
([Dokumentation und Übersetzungen beitragen](../../partner/dokumentation-und-uebersetzungen-beitragen.md)). Eine öffentliche
Stelle mit eigenen Übersetzerinnen und Übersetzern ist gut aufgestellt, um ihre eigenen Sprachen durchzusehen.

## Fehlermeldungen

Melden Sie Fehler möglichst anhand einer sauberen Installation, mit Schritten zum Nachvollziehen und ohne Ihre
Inhalte, personenbezogene Daten oder Geheimnisse ([Fehler als Partner melden](../../partner/fehler-gut-melden.md);
dieselben Regeln passen für alle). Sicherheitsprobleme gehen vertraulich an die Adresse in der SECURITY.md des
Projekts, nicht in ein öffentliches Issue.

Bedenken Sie dabei, dass manches Verhalten beabsichtigt oder Auslegungssache ist und andere Stellen und Sites sich
darauf verlassen. Eine Änderung daran braucht eine klare Begründung und kann mehrere Runden der Prüfung brauchen, bevor
sie angenommen wird; eine Option ist dann besser als ein neuer Standard
([Negative Funktionen](../../projekt/unterstuetzung-erhalten.md#negative-funktionen)).

## Es von Dienstleistern verlangen

Schreiben Sie in Verträge, dass allgemeine Verbesserungen, die ein Dienstleister für Sie entwickelt, mit Ihrer
Zustimmung an das Projekt zurückgegeben werden und dass der Dienstleister die Arbeit der Veröffentlichung übernimmt
(siehe „Open-Source-Beschaffung“).

## Verwandte Seiten

- [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md)
- [Eine Erweiterung veröffentlichen](../../partner/eine-erweiterung-veroeffentlichen.md)
- [Mit der Community zusammenarbeiten](../../fuehrung/mit-der-community-arbeiten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
