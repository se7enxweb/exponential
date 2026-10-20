---
title: Auf Share und Projects veröffentlichen
audience: advocate
category: advocacy
order: 56
summary: Wie Fürsprecher auf den eigenen Websites der Community veröffentlichen - Share (share.exponential.earth) für Foren, Blogs, Artikel und Tutorials und Projects (projects.exponential.earth) für Erweiterungen und Community-Projekte - wofür jede da ist, Konten und eine Checkliste für einen guten Eintrag.
keywords: Share, share.exponential.earth, Projects, projects.exponential.earth, Community-Portal, Blogs, Artikel, Tutorials, Erweiterungen, Projekteintrag, RSS, veröffentlichen, Konten, community portal, blogs, articles, tutorials, extensions, project listing, publishing, accounts
type: help
---
# Auf Share und Projects veröffentlichen

Diese Seite ist für Fürsprecher, die möchten, dass ihre Beiträge, Tutorials und Erweiterungen dort gefunden werden, wo
die Community sucht. Die zwei Community-Websites sind **Share** und **Projects**. Beide werden nach ihren öffentlichen
Seiten beschrieben, gelesen am 18. Oktober 2026; prüfen Sie auf den Websites ihre aktuellen Regeln.

## Inhalt

1. [Share und Projects auf einen Blick](#share-und-projects-auf-einen-blick)
2. [Konten](#konten)
3. [Auf Share veröffentlichen](#auf-share-veröffentlichen)
4. [Ein Projekt auf Projects eintragen](#ein-projekt-auf-projects-eintragen)
5. [Ein guter Eintrag: Checkliste](#ein-guter-eintrag-checkliste)
6. [Neue Arbeit verfolgen](#neue-arbeit-verfolgen)

## Share und Projects auf einen Blick

| | Share | Projects |
|---|---|---|
| Adresse | [share.exponential.earth](https://share.exponential.earth/) | [projects.exponential.earth](https://projects.exponential.earth/) |
| Wie sie sich beschreibt | „Share ist das Tor zur Exponential-Community ...“ (Beschreibung der Website) | „Laden Sie Exponential kostenlos herunter und treten Sie unserer Community bei! Veröffentlichen Sie Ihre eigenen angepassten Exponential-Erweiterungen“ (Beschreibung der Website) |
| Was Sie finden | Foren, Blogs, Artikel, Neuigkeiten von 7x | Neue und aktualisierte Projekte, Stöbern und Suchen, RSS-Feeds |
| Nutzen Sie sie für | Fragen, Antworten, Beiträge, Tutorials | Erweiterungen und Community-Projekte |
| Dokumentiert in | [Orte der Community](../../../en/us/help/who-is-the-exponential-foundation.md#community-places) | der Website selbst |

## Konten

- Share hat eine Registrierungsseite ([share.exponential.earth/user/register](https://share.exponential.earth/user/register)).
- Projects hat angekündigt, dass es „jetzt Anmeldungen und die Nutzung mit Konten der Website Share Exponential
  unterstützt“ (Neuigkeit auf der Website Projects); ein Konto dient also für beide.
- Verwenden Sie einen Namen, mit dem Sie öffentlich leben können. Stellen Sie keine privaten Daten in Ihr Profil.

## Auf Share veröffentlichen

| Sie haben | Gehört in |
|---|---|
| Eine Frage | Die [Foren](https://share.exponential.earth/forums) (es gibt auch ein Forum für Exponential Platform) |
| Eine Antwort auf eine häufige Frage | Einen Forenbeitrag, dann verbessern Sie die Hilfeseite ([Die Hilfe verbessern](die-hilfe-verbessern.md)) |
| Einen Blogbeitrag oder ein Tutorial | Die Blogs und Artikel auf Share oder Ihren eigenen Blog mit einem Forenbeitrag, der darauf verlinkt |
| Neuigkeiten zu Ihrer Erweiterung | Einen Forenbeitrag, der auf ihre Seite auf Projects verlinkt |

Lesen Sie vor dem Veröffentlichen die eigenen Regeln der Website und das Thema des Forums. Folgen Sie dann
[Einen Blogbeitrag schreiben](einen-blogbeitrag-schreiben.md) oder [Ein Tutorial schreiben, das funktioniert](ein-tutorial-schreiben.md).

## Ein Projekt auf Projects eintragen

Projects hat einen Bereich „Add your project!“ zum Eintragen eigener Projekte. Ein Projekteintrag sollte auf die
maßgebliche Quelle verweisen: das Git-Repository und, bei Composer-Paketen, den Paketnamen.

Bereiten Sie vor:

1. **Ein öffentliches Repository** mit README, einer Datei LICENSE und einem klaren Namen ([Erweiterungen](../../../en/us/guides/extensions.md)).
2. **Versionsangaben in den eigenen Dateien der Erweiterung**: `ezinfo.php` und `extension.xml` tragen die Release-Nummer
   ([Erweiterungen, „Release it“](../../../en/us/guides/extensions.md#7-release-it)).
3. **Installationsanleitung**: `composer require` und die Einstellungen zum Aktivieren.
4. **Kompatibilität**: Mit welchen Exponential- und PHP-Versionen Sie getestet haben.
5. **Screenshots** mit Alternativtext und ohne personenbezogene Daten.

## Ein guter Eintrag: Checkliste

```text
[ ] Name: clear, uses "exp" or your own prefix for new work; never misuses the product name
[ ] One-sentence summary: what it does for whom
[ ] License stated (and the same as in the repository)
[ ] Link to the repository and, if any, the Composer package
[ ] Version, release date, tested Exponential and PHP versions
[ ] Install and enable steps
[ ] Where to report issues (the repository's tracker)
[ ] Screenshots with alt text; no secrets, no real personal data
[ ] Credits to the original author if it is a fork or port
```

Hinweise zur Benennung neuer Dinge stehen unter [Marken- und Namensregeln, Namen für Neues](../../../en/us/help/business/brand-and-naming-rules.md#names-for-new-things).

## Neue Arbeit verfolgen

Projects bietet RSS-Feeds (Projekte, Projektneuigkeiten, Neuigkeiten, Foren, Git-Aktivität, wie auf der Startseite
verlinkt). Wer sie verfolgt, findet leicht neue Erweiterungen zum Ausprobieren, Prüfen oder Darüberschreiben
([Einen Blogbeitrag schreiben](einen-blogbeitrag-schreiben.md)).

## Verwandte Seiten

- [Die Community-Kanäle](community-kanaele.md)
- [Zu eZpedia beitragen](zu-ezpedia-beitragen.md)
- [Erweiterungen](../../../en/us/guides/extensions.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
