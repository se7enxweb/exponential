# Quechua (que-PE): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Southern Quechua (Runasimi) of Peru, Cusco-Collao and Ayacucho-Chanka in the pan-dialectal standard written form of the
Peruvian Ministry of Education, in the neutral, plain register of the Quechua translations of the Peruvian state,
bilingual school books and the Quechua localisations of Google, Mozilla and Wikipedia. No other locale falls back to
`que-PE`. Spanish (`esl-ES`) gives the loan words where Quechua has none, as everyday Quechua does.

- Quechua words are used where they exist: waqaychay, chinkachiy, hurquy, hukchay, maskay, akllay, yapay,
  paqarichiy, qhaway, apaykachaq, pakasqa simi, rikchay, willañiqi, q'upa, khipu, simi, t'ikray.
- A loan word without a Quechua one is a Spanish loan in Quechua spelling: sirwidur, mudulu, versiyun, kuwinta,
  karpeta, iskript, motur, aparatu, plantilla. Never an English word, and never a fresh Spanish word in Spanish
  spelling where the glossary has a Quechua one.
- Plural **-kuna**, object **-ta**, genitive **-pa/-p**, locative **-pi**, possessive "your" **-yki**:
  willañiqikuna, apaykachaqkuna, pakasqa simiyki.

## 2. Form of address

The interface addresses the user with **qam** (you, singular) and **-yki** (your), as Quechua software does.

- **Buttons, menu items and links**: the imperative singular in **-y**: Waqaychay, Qhisachay, Chinkachiy, Hukchay,
  Lluqsichiy, Lluqsichinapaq kachay, Akllasqakunata hurquy, Q'upaman apay.
- **Titles, column headings and tabs**: nouns or noun phrases: Allichaykuna, T'ikraykuna, Hatun tiyana.
- **Instructions**: the polite imperative in **-riy** or "Ama hina kaspa," where a request needs it: "Huk objetuta
  akllay hinaspa «OK» nisqata ñit'iy.", "Ama hina kaspa, suyay ...". A reference ("see ...") is "Qhaway ...".
- **Confirmations and questions**: with **-chu**: "Chiqaptachu kay t'aqata chinkachiyta munanki?", "...-ta
  munankichu?".
- **What the user can or may do**: "...-ta atinki", "Mana ...-ta atinkichu", or impersonal: "Hatun simitaqa mana
  hukchayta atikunchu ...". "You do not have permission" is "Mana saqillayniyki kanchu ...".
- Not allowed: slash forms.

## 3. Script and typography

- **Latin**, the official alphabet of Quechua in Peru (Ministry of Education, three vowels a, i, u), the script of the
  shipped locale (`share/locale/que-PE.ini`, LanguageName Runasimi). The letters: a, ch, chh, ch', h, i, k, kh, k',
  l, ll, m, n, ñ, p, ph, p', q, qh, q', r, s, sh, t, th, t', u, w, y. The glottalised consonants take the straight
  apostrophe ' (U+0027), as the official texts write: ch'uya, q'upa, t'ikray. The vowels e and o appear only in Spanish
  loans and names. Inside an attribute value in quotes the source's apostrophe rules hold.
- **Latin text inside Quechua**: product names, codes and acronyms stay as they are: "PHP willañiqi", "URL", "HTML
  kamachiy".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free. Quechua glues its
  suffixes to the word; never glue a suffix to a placeholder directly ("%nameta" is a different placeholder). Join it
  with a hyphen ("%name-ta", "%count-manta") or rephrase so that the placeholder stands alone: "Objetu: %name".
- **Numbers**: the number comes before the noun, which stays in the singular: "%count objetu", "3 willañiqi". Digits
  0-9.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly. The Spanish opening ¿ and ¡ are not used.
- **Punctuation**: as in the source. A label keeps its colon.

## 4. Glossary

Binding for the kernel and every extension.

| English | Quechua | Note |
|---|---|---|
| content | ukhun | |
| content object | ukhun objetu | |
| object | objetu | |
| node | khipu | main node: hatun khipu; parent node: tayta khipu; child: wawa khipu |
| location | tiyana | main location: hatun tiyana |
| item (list entry) | kaq | |
| class | t'aqa | the content class; class group: t'aqakunap huñun; a PHP class: PHP t'aqa |
| content class | ukhun t'aqa | |
| attribute | kaqnin | |
| datatype | willay laya | |
| version | versiyun | version history: versiyunkunap willakuynin |
| draft | ñawpa qillqasqa | store draft: Ñawpa qillqasqata waqaychay |
| publish | lluqsichiy | published: lluqsichisqa |
| send for publishing | Lluqsichinapaq kachay | |
| discard | wikch'uy | discard draft: Ñawpa qillqasqata wikch'uy |
| archived | ñawpa waqaychanapi | |
| pending | suyachkan | |
| rejected | mana chaskisqa | |
| siteaccess | web yaykuna | the configuration unit |
| site, website | web | |
| section | raki | the Exponential section |
| role | ruwana | |
| policy | kamachikuy | |
| limitation | saywa | |
| permission | saqillay | access rights: yaykunapaq saqillay |
| module | mudulu | |
| function | ruray | |
| user | apaykachaq | |
| user group | apaykachaqkunap huñun | |
| group | huñu | |
| anonymous | mana sutiyuq | anonymous user: mana sutiyuq apaykachaq |
| administrator | kamachiq | |
| administration (interface) | kamachiy panqa | |
| username | apaykachaqpa sutin | the column "Login" too |
| password | pakasqa simi | |
| log in / sign in | yaykuy | button: Yaykuy |
| log out / sign out | lluqsiy | button: Lluqsiy |
| e-mail | antañiqi | e-mail address: antañiqi riqsichiq |
| cache | utqay waqaycha | clear cache: Utqay waqaychata pichay |
| template | plantilla | |
| override | rantin | override template: rantin plantilla |
| fallback | yanapakuq | fallback language: yanapakuq simi |
| design | siq'iy | |
| workflow | llamk'ay puriy | |
| trigger | qallarichiq | |
| event | pasakuy | |
| trash | q'upa | empty trash: Q'upata ch'usaqyachiy |
| bookmark | yuyarina | |
| settings | allichaykuna | |
| setup | wakichiy | the Setup tab; installation: churay |
| layout | churanakuy | Exponential Layouts |
| block | bluki | |
| zone | k'iti | |
| extension | yapakuq | |
| package | q'ipi | |
| search | maskay | button: Maskay |
| log | pasakuy qillqa | log file: pasakuy qillqa willañiqi |
| queue | suyana sinru | |
| job / task | llamk'ay | |
| cron | cron | cron job: cron llamk'ay |
| script | iskript | |
| worker | llamk'aq | kept apart from apaykachaq (user) |
| kernel, core | sunqu | |
| server | sirwidur | |
| database | willakuna waqaychana | |
| folder | karpeta | |
| file | willañiqi | |
| image | rikchay | |
| preview | ñawpaq qhaway | |
| upload | huqariy | button: Huqariy |
| download | uraqay | button: Uraqay |
| export / import | apachiy / apamuy | |
| remove | hurquy | take out of a list or a place |
| delete | chinkachiy | destroy |
| edit | hukchay | |
| editor | hukchaq | |
| save / store | waqaychay | |
| Store (shop tab) | Qhatu | the word the shop menu uses for "Shop" |
| cancel | qhisachay | |
| OK | OK | |
| apply | hunt'achiy | |
| back | kutiy | |
| next / previous | qatiq / ñawpaq | |
| enable / disable | llamk'achiy / samachiy | a setting or feature |
| enabled / disabled | llamk'achkan / samachkan | |
| active / inactive | llamk'achkan / samachkan | |
| run | purichiy | a script or job |
| start / stop | qallariy / sayachiy | |
| session | tinkuy | |
| hidden / visible | pakasqa / rikukuq | hide / reveal: pakay / rikuchiy |
| archive | ñawpa waqaychana | a file archive (.zip): willañiqi q'ipi |
| order (sort) | ñiqi | sort: ñiqinchay |
| order (shop) | mañakuy | |
| shop | qhatu | |
| basket | isanka | |
| product | ranqhana | |
| customer | rantiq | |
| discount | pisiyachiy | |
| VAT | IGV | the abbreviation used in Peru |
| price | chani | |
| currency | qullqi | |
| tag | unancha | keyword tag; an HTML tag: HTML unancha |
| translation | t'ikray | |
| language | simi | main language: hatun simi |
| locale | suyu simi | |
| state | imaynakay | the object state; state group: imaynakaykunap huñun |
| status | kaynin | |
| view (button) | qhaway | a module view: qhawana |
| format | rikch'ay | |
| sort | ñiqinchay | |
| filter | suysuy | |
| select | akllay | selected: akllasqa |
| browse | purimuy | |
| copy | kikinchay | |
| move | kuyuchiy | |
| add | yapay | |
| create | paqarichiy | |
| new | musuq | |
| restore | kutichiy | |
| install / uninstall | churay / churasqata hurquy | |
| sub items | ukhupi kaqkuna | |
| subtree | k'allma | |
| related objects | t'inkisqa objetukuna | relation: t'inki |
| URL alias | URL huk suti | |
| redirect | ñan t'ikray | |
| dashboard | llamk'ana panqa | |
| content structure | ukhun wakichisqa | |
| media library | media waqaychana | |
| identifier / ID | riqsichiq / ID | |
| priority | ñawpaqchay | |
| owner | kapuq | |
| modified / created | hukchasqa / paqarichisqa | |
| date / time | p'unchaw / pacha | |
| error / warning | pantay / yuyarichiy | |
| help | yanapay | |
| notification | willakuy | |
| wizard | pusaq | |
| feed (RSS) | willakuy hamuq | RSS feed: RSS willakuy hamuq |
| tab | raphicha | |
| audit, audit trail | chiqaqchay, chiqaqchay qillqa | |
| repository | waqaychana | |
| checksum | chiqaqchay yupay | |
| account | kuwinta | |
| default | ñawpaqmanta | default value: ñawpaqmanta chani |
| toolbar | llamk'ana sinru | |
| container | churana | |
| AI | IA | the Spanish abbreviation used in Peru |
| pattern (name pattern) | rikch'ana | kept apart from plantilla (template) |
| wildcard | kumudin | |
| remote ID | karu ID | |
| placeholder | suyaq | |
| collection | huñukuy | information collector: willay huñuq |
| approve, approval | chaskiy | |
| software | kamachiy | |
| creator / modifier | paqarichiq / hukchaq | |
| request (HTTP) | mañay | |
| query | tapukuy | search query: maskay tapukuy |
| handler / provider / listener | ruraq / quq / uyariq | |
| engine | motur | |
| release / upgrade | lluqsichisqa / musuqchay | update: musuqchay |
| backup | waqaychay kikin | |
| maintenance | qhawapay | |
| directory | karpeta | |
| header (HTTP) | uma | |
| page layout (pagelayout template) | raphi plantilla | kept apart from churanakuy (Exponential Layouts) |
| passkey | yaykuna kichana | |
| two-step sign-in | iskay purinapi yaykuy | |
| device / browser | aparatu / web puriq | |
| link | t'inkiy | |
| registration, register | qillqakuy | |
| payment gateway | qullqi quna punku | |
| form | hunt'ana | |
| comment | rimariy | |
| newsletter / subscription | willakuy raphi / suyakuy | |
| field | qillqana | |
| value | chani | |
| list | sinru | |
| page | raphi | |
| title / name | uma qillqa / suti | the column "Name": Suti |
| description | sut'ichay | |
| type | laya | |
| path | ñan | |
| width / height | anchu / sayay | |
| size | tupu | |
| none | manan imapas | |
| yes / no | arí / mana | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Qhatu, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is Waqaychay.
- "Delete" is Chinkachiy (destroy) and "remove" is Hurquy (take out), so that the two buttons are never confused.
- "Settings" is Allichaykuna and "edit" is Hukchay, so that the two are never confused.
