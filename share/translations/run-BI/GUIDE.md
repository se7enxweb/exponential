# Kirundi (run-BI): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Kirundi (Ikirundi) as used in Burundi, in the Latin alphabet, in the neutral, polite register of the
Government of Burundi portals, Burundian news (Iwacu, RTNB, Radio Isanganiro) and Kirundi Wikipedia. Kinyarwanda
(kin-RW) is a close relative with its own catalogue and may help with vocabulary, but write Kirundi forms and spelling
(Kirundi "kuraba" for view, "ivyo" not "ibyo", "vyose" not "byose", "ico" not
"icyo", "ubutumwa", "inyandiko"). No locale falls back to `run-BI`.

- Word order is subject - verb - object, with the noun class agreement of Kirundi (idosiye, amadosiye; umukoresha,
  abakoresha). Function words: na (and), canke (or), ariko (but), nimba (if), iki / iyi / uyu (this), vyose / zose /
  bose (all), muri (in), kuri (on, to), kuva (from), kugira ngo (so that), nta (no, none), si (not).
- Technical words: a Kirundi word where one is in use (ijambo ry'ibanga, umukoresha, kurondera, ingene vyateguwe);
  a French loan in Kirundi spelling where Burundian writing uses it (idosiye for file, serivere, porogaramu, imeyili,
  verisiyo, sekisiyo -> igice). French (fre-FR) is the second official language and may help with loans, but translate
  from the English source. Never leave the English word unless it is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is **mwebwe** (polite plural "you"), with the verb forms in mu- / mw- (murashobora, murashaka, mwahisemo).
- **Buttons, menu items and links**: the infinitive in ku- / gu-, as Kirundi and Kinyarwanda interfaces do: Kubika
  (save), Guhagarika (cancel), Gufuta (delete), Gukuraho (remove), Guhindura (edit), Kwongerako (add), Kurema
  (create), Kurondera (search), Guhitamwo (select), Kurungika (send), Kuraba (view), Gusohora (publish), Gushiraho
  (upload), Kumanura (download).
- **Titles, column headings and tabs**: nouns or noun phrases: Ingene vyateguwe, Ubuhinduzi, Ahantu nyamukuru, Izina.
- **Instructions**: the polite plural imperative in -e: "Muhitemwo ikintu, hanyuma mukande kuri \"OK\".". "Please" is
  "Turabasavye".
- **Confirmations and questions**: "Murizeye ko mushaka ...?": "Murizeye ko mushaka gufuta iki gice?".
- **What the user can or may do**: "Murashobora ...", "Ntimushobora ..."; "You do not have permission" is "Ntimufise
  uburenganzira bwo ...".
- **Messages about a result**: the passive past: "Umushinga wabitswe.", "Idosiye yashizweho neza.".

## 3. Script and typography

- **Latin** alphabet; the apostrophe ' (U+0027) of elisions (ry'ibanga, y'umukoresha, w'ibirimwo), never ’.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. A noun class prefix or an elision is never written onto a
  placeholder (n'%name, %namena): use a noun that carries the agreement ("idosiye %name", "ibintu %count"). The same
  for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Kirundi | Note |
|---|---|---|
| content | ibirimwo | content object: ikintu c'ibirimwo |
| object | ikintu | plural: ibintu |
| tree | igiti | tree menu: urutonde rw'igiti; content structure tree: igiti c'imiterere y'ibirimwo |
| node | ihuriro | main node: ihuriro nyamukuru; parent node: ihuriro mvyeyi |
| location | ahantu | main location: ahantu nyamukuru |
| item (list entry) | ikintu | |
| class | ubwoko | the content class; class group: umugwi w'ubwoko |
| attribute | ikiranga | plural: ibiranga |
| datatype | ubwoko bw'amakuru | |
| version | verisiyo | version history: kahise ka verisiyo |
| draft | umushinga | store draft: Kubika umushinga |
| publish | gusohora | published: vyasohowe |
| send for publishing | kurungika ngo bisohorwe | |
| discard | guta | discard draft: Guta umushinga |
| archived / pending / rejected | vyabitswe mu bubiko / birindiriye / vyanswe | |
| siteaccess | uburyo bwo gushika ku rubuga | siteaccess as a setting name stays in code |
| site, website | urubuga | |
| section | igice | the Exponential section; an INI section: igice ca INI |
| role | uruhara | |
| policy | politike | |
| limitation | imbibe | |
| permission | uburenganzira | access denied: kwinjira vyanswe |
| module | modile | |
| function | igikorwa | a PHP function: fonction -> funksiyo |
| user / user group | umukoresha / umugwi w'abakoresha | plural: abakoresha |
| group | umugwi | |
| anonymous | atazwi | anonymous user: umukoresha atazwi |
| administrator | umuyobozi | administration (interface): ubuyobozi |
| username | izina ry'umukoresha | login (the name): izina ryo kwinjira |
| password | ijambo ry'ibanga | |
| log in / log out | kwinjira / gusohoka | |
| e-mail | imeyili | e-mail address: aderese ya imeyili |
| cache | ububiko bw'igihe gito | clear cache: Gusukura ububiko bw'igihe gito |
| template | icitegererezo | override template: icitegererezo gisubirira |
| override | gusubirira | |
| fallback | ingoboka | fallback language: ururimi rw'ingoboka |
| design | igishushanyo | |
| workflow | urugendo rw'igikorwa | |
| trigger | imbarutso | |
| event | igikorwa cabaye | short: ikintu cabaye |
| trash | igiseke c'imyanda | empty trash: Gusukura igiseke; move to trash: Kurungika mu giseke |
| bookmark | akamenyetso | |
| settings | ingene vyateguwe | configuration: ingene vyateguwe |
| setup | gutunganya | the Setup tab |
| layout | ingene urupapuro rutondetse | Exponential Layouts stays |
| block / zone | igice / akarere | block: ibloki |
| extension | iyongerwako | |
| package | ipaki | |
| search | irondera | to search: kurondera; search index: urutonde rw'irondera |
| log | igitabo c'ivyakozwe | a log file: idosiye y'ivyakozwe; never "log in" |
| queue | umurongo | job queue: umurongo w'ibikorwa |
| job / task | igikorwa | |
| cron | cron | |
| script | sikiripiti | |
| kernel, core | intango | |
| server | serivere | |
| database | ububiko bw'amakuru | |
| folder / directory | ububiko | |
| file | idosiye | plural: amadosiye |
| image | ishusho | thumbnail: akashusho |
| preview | kubanza kuraba | |
| upload / download | gushiraho / kumanura | |
| export / import | gusohora hanze / kwinjiza | |
| remove | gukuraho | take out of a list or place |
| delete | gufuta | destroy; deleted: vyafuswe |
| edit | guhindura | |
| editor | umwanditsi / porogaramu yo guhindura | person / software |
| save / store | kubika | the verb "Store" of the forms |
| Store (shop tab) | iduka | the shop, as "Shop" |
| cancel | guhagarika | |
| OK | Ego | |
| apply | gushira mu ngiro | |
| back | gusubira inyuma | |
| next / previous | igikurikira / icabanje | |
| enable / disable | gukora / guhagarika | enabled / disabled: birakora / ntibikora |
| active / inactive | birakora / ntibikora | |
| run | gukoresha | |
| start / stop | gutangura / guhagarika | |
| session | igihe co kwinjira | |
| hidden / visible | binyegejwe / biboneka | hide / reveal: kunyegeza / kwerekana |
| archive | ububiko | |
| order (sort) | urutonde | sort order: uburyo bwo gutondeka |
| order (shop) | itegeko ryo kugura | short: itegeko |
| shop | iduka | |
| basket | igiseke | |
| product | igicuruzwa | |
| customer | umuguzi | |
| discount | igabanuka ry'igiciro | |
| VAT | TVA | |
| price / currency | igiciro / ifaranga | |
| tag | akarango | |
| translation | ubuhinduzi | |
| language | ururimi | main language: ururimi nyamukuru |
| locale | aho ururimi rukoreshwa | |
| state | ingene vimeze | the object state |
| status | uko bihagaze | |
| view (button) | kuraba | a module view (noun): ivyerekanwa |
| format | ingene vyanditswe | |
| sort | gutondeka | |
| filter | iyungurura | to filter: kuyungurura |
| select | guhitamwo | select all: Guhitamwo vyose; selected: vyatowe |
| copy / move | gukopora / kwimura | |
| add / create / new | kwongerako / kurema / gishasha | |
| restore | gusubiza | |
| install / uninstall | gushiramwo / gukuramwo | |
| related objects | ibintu bijanye | relation: isano |
| dashboard | ikibaho | |
| identifier / ID | ikiranga / ID | |
| priority | icihutirwa | |
| owner | nyene | |
| modified / created | vyahinduwe / vyakozwe | |
| date / time | itariki / isaha | |
| error / warning | ikosa / ukwihanizwa | |
| help | imfashanyo | |
| notification | imenyesha | |
| account | konti | |
| default | isanzwe | |
| link | ihuza | |
| none / all | nta na kimwe / vyose | |
| yes / no | ego / oya | |
| name / title / description | izina / umutwe / insiguro | |
| value / type / list | agaciro / ubwoko / urutonde | |
| page / path | urupapuro / inzira | |
| required / optional | birakenewe / si ngombwa | |
| options | amahitamwo | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is iduka, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  kubika.
- "Login" in the user, session and audit lists is the user's login name (izina ryo kwinjira); the Login button of the
  error page is the act (Kwinjira).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
