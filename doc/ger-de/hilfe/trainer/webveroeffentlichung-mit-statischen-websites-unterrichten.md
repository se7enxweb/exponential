---
title: "Webveröffentlichung mit statischen Websites unterrichten"
audience: trainer
category: training
order: 9
summary: Ein Unterrichtsplan und eine Übung für einen halben Tag - Teilnehmende bearbeiten Inhalte in Exponential, bauen eine statische Kopie, prüfen die Änderungen, veröffentlichen sie kostenlos auf GitHub Pages und nehmen sie zurück.
keywords: Trainer, Lehrkraft, Unterrichtsplan, Übung, Kurs, Schulung, statische Website, kostenloses Hosting, Rücknahme, Statisches Veröffentlichen, trainer, lesson plan, exercise, course, static site, GitHub Pages, gh-pages, free hosting, free website, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing, git, rollback
type: help
---
# Webveröffentlichung mit statischen Websites unterrichten

Statisches Veröffentlichen macht den ganzen Weg vom Inhalt zur öffentlichen Website in einer Einheit sichtbar:
bearbeiten, bauen, prüfen, veröffentlichen, zurückgehen. Alle Teilnehmenden haben am Ende eine echte Website, und
niemand muss für Hosting zahlen.

## Was Sie am Ende haben

- Einen Unterrichtsplan für etwa vier Stunden.
- Eine Übung mit klarem Ergebnis: die Website jeder Person auf GitHub Pages, mit zwei Veröffentlichungen im Verlauf.

## Vor dem Kurs

- Eine Exponential-Installation pro Person oder Paar (ein Laborserver mit mehreren Sites geht auch), mit eingeschaltetem
  statischen Veröffentlichen.
- Alle haben ein GitHub-Konto und einen Fine-grained Token, oder Sie verteilen Deploy Keys, die mit `exp:static:keys`
  erzeugt wurden. Siehe [Mit GitHub verbinden](../statisches-veroeffentlichen/mit-github-verbinden-token-key-oder-app.md).
- Geben Sie jeder Person eine Rolle mit den Richtlinien `staticpublish/*`, beschränkt auf ihr eigenes Ziel. Siehe
  [Wer veröffentlichen darf](../statisches-veroeffentlichen/wer-veroeffentlichen-darf-rollen-und-richtlinien.md).

## Unterrichtsplan

| Zeit | Thema | Aktivität |
|---|---|---|
| 20 Min. | Was eine statische Website ist | [Was statisches Veröffentlichen ist](../statisches-veroeffentlichen/was-statisches-veroeffentlichen-ist.md) besprechen; die Demo-Website öffnen (in Kürze) |
| 30 Min. | Passt es? | Beispiel-Websites mit [Passt statisch zu meiner Website?](../statisches-veroeffentlichen/passt-statisch-zu-meiner-website.md) einordnen |
| 40 Min. | Einrichten | Der Einrichtungsassistent: Siteaccess, Repository, Zugangsdaten, Links |
| 40 Min. | Bauen und prüfen | Bauen, Diff und Bericht lesen, Vorschau öffnen |
| 30 Min. | Veröffentlichen | Veröffentlichen, die `github.io`-Adresse öffnen, Commit und Tag auf GitHub finden |
| 30 Min. | Ändern und zurücknehmen | Eine Seite ändern, erneut veröffentlichen, dann zu Veröffentlichung 1 zurückgehen |
| 20 Min. | Abschluss | Was sich für Formulare und Suche ändert; Fragen |

## Die Übung

1. Ändern Sie den Titel der Startseite Ihrer Site und legen Sie einen Artikel an.
2. Bauen Sie Ihr Ziel und notieren Sie, wie viele Seiten neu und geändert sind.
3. Veröffentlichen Sie. Stellen Sie Ihre öffentliche Adresse in den Kurs-Chat.
4. Ändern Sie den Artikel erneut und veröffentlichen Sie. Gehen Sie dann zu Veröffentlichung 1 zurück und prüfen Sie,
   dass die erste Fassung des Artikels wieder online ist.
5. Zusatz: Erklären Sie, warum `/<repository>/` in Ihrer Adresse steht. Siehe [Links und Basispfad](../statisches-veroeffentlichen/links-und-basispfad.md).

## Bewertung

- Die Website ist erreichbar und die Links funktionieren unter `/<repository>/`.
- Das Repository hat mindestens drei Tags `static/<ziel>/<n>`.
- Die Person kann sagen, welche Teile einer Website einen Server brauchen.

Wenn im Kurs etwas schiefgeht: [Wenn eine Veröffentlichung fehlschlägt](../statisches-veroeffentlichen/wenn-eine-veroeffentlichung-fehlschlaegt.md).

## Jetzt Ihre eigene erstellen

Machen Sie die Übung zuerst selbst: [In 15 Minuten eine kostenlose Website](../statisches-veroeffentlichen/in-15-minuten-eine-kostenlose-website.md).
