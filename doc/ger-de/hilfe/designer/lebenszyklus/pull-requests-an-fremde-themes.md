---
title: "Pull-Requests an fremde Themes"
audience: designer, developer
category: theme-lifecycle
order: 350
summary: Einen Pull-Request vorbereiten, den ein Betreuer schnell zusammenführen kann - klein und fokussiert, erklärt, getestet, mit Screenshots bei optischen Änderungen und ohne fremde oder private Inhalte - und wie Sie auf ein Review antworten.
keywords: Pull-Request, PR, Umgangsformen, Beitrag, Review, Screenshots, fokussierte Änderung, Beschreibung, pull request, etiquette, contribution, review, screenshots, focused change, description
type: help
---
# Pull-Requests an fremde Themes

Diese Seite ist für Designer, die eine Änderung an das Theme eines anderen Betreuers oder an ein Repository von
Exponential schicken.

## Schritt für Schritt

1. **Prüfen Sie zuerst**: Gibt es ein Issue? Kommentieren Sie, dass Sie daran arbeiten. Lesen Sie die Hinweise des
   Repositorys zum Beitragen.
2. **Ein Thema pro Pull-Request.** Eine Korrektur und ein Refactoring sind zwei Pull-Requests.
3. **Verzweigen Sie vom aktuellen main**: `git switch -c fix-map-popup origin/main`.
4. **Nehmen Sie die Änderung vor** und folgen Sie dem Stil des Themes. Kein Umformatieren unberührter Zeilen.
5. **Testen** Sie auf einer aktuellen Installation; bei optischen Änderungen hell und dunkel, Telefon und Zoom.
6. **Committen** Sie mit einem Präfix und einem Grund ([Changelog-Einträge](../veroeffentlichung/changelog-praefixe.md)).
7. **Öffnen Sie den Pull-Request** mit:
   - was er ändert und warum;
   - wie Sie ihn getestet haben;
   - Screenshots vorher und nachher bei optischen Änderungen;
   - dem Issue, das er schließt.
8. **Antworten Sie auf das Review** zügig und freundlich, mit Änderungen auf demselben Branch. Rechnen Sie beim Kernel von
   Exponential mit vielen Runden: 7x prüft einen Commit oder Pull-Request in weit mehr Runden als üblich, manchmal in
   Hunderten von Überarbeitungen; seien Sie also bereit, Ihre Änderung zu überarbeiten, bis sie einfach anzunehmen ist.
   7x verlangt außerdem, dass Sie den Pull-Request zusammenfassen (squash) und auf `main` rebasen, solange er offen ist
   (`git fetch origin`, `git rebase -i origin/main`, `git push --force-with-lease` nur auf Ihren Pull-Request-Branch);
   nach dem Zusammenführen wird `main` nie umgeschrieben ([Code-Reviews](../../../../../CONTRIBUTING.md#code-reviews)).
9. **Für den Kernel von Exponential** (`se7enxweb/exponential`) muss Ihr unterzeichnetes Contributor Licensing Agreement
   vorliegen: 7x und die Exponential Foundation führen einen Beitrag zum Kernel erst zusammen, nachdem es eingegangen ist.
   **Mit der Unterzeichnung und Einreichung übertragen Sie das Urheberrecht an Ihrem Beitrag auf 7x, das dessen
   Eigentümer wird, und geben die Kontrolle darüber ab:** 7x darf ihn auf jede Weise nutzen, ändern, neu lizenzieren und
   verbreiten, auch unter anderen Lizenzen und in kommerziellen Produkten, ohne Sie zu fragen oder zu bezahlen, und Sie
   können ihn nicht zurückziehen oder an Bedingungen knüpfen
   ([Übertragung des Urheberrechts (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla)). Themes anderer
   Betreuer und Ihre eigenen Themes sind davon nicht betroffen.

## Nie aufnehmen

- Zugangsdaten, Kundennamen oder -daten, Serverpfade.
- Änderungen an Versionsfeldern oder Tags (der Betreuer veröffentlicht).
- Erzeugte Dateien, die das Repository nicht versioniert.

## Verwandte Seiten

- [An Upstream zurückgeben](an-upstream-zurueckgeben.md)
- [Einen Fork synchron halten](einen-fork-synchron-halten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
