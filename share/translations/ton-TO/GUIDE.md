# Tongan (ton-TO): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Tongan (lea fakatonga) of the Kingdom of Tonga, in the neutral, plain written register of the Tongan government,
Tongan newspapers and the Tongan Bible; the polite common register, never the chiefly or royal vocabulary. No other
locale falls back to `ton-TO`. English gives the loan words for computing that have no Tongan word, as everyday Tongan
does.

- Tongan words are used where they exist: tauhi, tamateʻi, toʻo, fakatonutonu, kumi, fili, tānaki, faʻu, vakai,
  lea fufū, tā, veve, lea, liliu lea, falekoloa.
- Computing words without a Tongan one are English loans in Tongan spelling: faile, folota, sēva, kēsi, tempileti,
  vēsiona, mōtiule, poloka, pēkeisi, sikilipi, ʻīmeili, ʻakauni, palausa, fōmeti, tisaini. A word that has no settled
  Tongan spelling keeps the glossary's form; never a fresh English word in English spelling inside a Tongan sentence.
- The plural is marked by **ngaahi** (or **kau** for people) before the noun: ngaahi faile, kau fakaʻaongaʻi. Nouns
  take the article **ʻa e** / **e** and the definitive accent where the sentence needs it.

## 2. Form of address

The interface addresses the user with **koe** (you, singular) and **hoʻo** / **ho** (your), as Tongan software does.

- **Buttons, menu items and links**: the bare verb: Tauhi, Kaniseli, Tamateʻi, Fakatonutonu, Pulusi, Fekau ke
  pulusi, Toʻo ʻa e ngaahi meʻa kuo fili, ʻAve ki he veve.
- **Titles, column headings and tabs**: nouns or noun phrases: Ngaahi Fokotuʻutuʻu, Ngaahi Liliu Lea, Feituʻu
  tefito.
- **Instructions**: "Kātaki ʻo ..." where a request needs it: "Fili ha ʻōpeki pea lomiʻi ʻa e «Sai».", "Kātaki ʻo
  tatali ...". A reference ("see ...") is "Vakai ki he ...".
- **Confirmations and questions**: "ʻOku ke fakapapauʻi ʻoku ke loto ke tamateʻi ʻa e kalasi ko ʻeni?", "ʻOku ke loto
  ke ...?".
- **What the user can or may do**: "ʻE lava ke ke ...", "ʻOku ʻikai lava ke ke ...", or impersonal: "ʻOku ʻikai
  lava ke liliu ʻa e lea tefito ...". "You do not have permission" is "ʻOku ʻikai haʻo ngofua ke ...".
- Not allowed: slash forms.

## 3. Script and typography

- **Latin**, the official Tongan alphabet, the script of the shipped locale (`share/locale/ton-TO.ini`, LanguageName
  Lea fakatonga). The glottal stop (fakauʻa) is written with the modifier letter turned comma ʻ (U+02BB), never with
  an apostrophe ' or a quotation mark ‘ (U+2018): ʻio, ʻikai, ʻaho, faʻu. Long vowels take the macron (toloi):
  ā, ē, ī, ō, ū (Ā, Ē, Ī, Ō, Ū). The definitive accent (fakamamafa) is written only where the standard spelling
  writes it.
- **Latin text inside Tongan**: product names, codes and acronyms stay as they are: "faile PHP", "URL", "kouti HTML".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Never a letter glued to a placeholder: "ʻōpeki ʻe %count" or
  "ʻŌpeki: %name".
- **Numbers**: the number comes after the noun with **ʻe**: "ʻōpeki ʻe %count", "faile ʻe 3". Digits 0-9.
- **Quotation marks**: the double quotes «...» where the source has its own quotation marks in text (a quotation
  mark next to a fakauʻa never takes its place); where the source has `&quot;` or a straight " that is code or an
  entity, keep it exactly.
- **Punctuation**: as in the source. A label keeps its colon.

## 4. Glossary

Binding for the kernel and every extension.

| English | Tongan | Note |
|---|---|---|
| content | meʻa ʻi loto | |
| content object | ʻōpeki meʻa ʻi loto | |
| object | ʻōpeki | |
| node | noti | main node: noti tefito; parent node: noti mātuʻa; child: noti fānau |
| location | feituʻu | main location: feituʻu tefito |
| item (list entry) | meʻa | |
| class | kalasi | the content class; class group: kulupu kalasi; a PHP class: kalasi PHP |
| content class | kalasi meʻa ʻi loto | |
| attribute | ʻulungāanga | |
| datatype | faʻahinga fakamatala | |
| version | vēsiona | version history: hisitōlia ʻo e ngaahi vēsiona |
| draft | tohi fakataimi | store draft: Tauhi ʻa e tohi fakataimi |
| publish | pulusi | published: kuo pulusi |
| send for publishing | Fekau ke pulusi | |
| discard | liʻaki | discard draft: Liʻaki ʻa e tohi fakataimi |
| archived | kuo ʻakaivi | |
| pending | ʻoku tatali | |
| rejected | kuo fakafisi | |
| siteaccess | hūʻanga uepisaiti | the configuration unit |
| site, website | uepisaiti | |
| section | konga | the Exponential section |
| role | fatongia | |
| policy | tuʻutuʻuni | |
| limitation | fakangatangata | |
| permission | ngofua | access rights: ngofua ke hū |
| module | mōtiule | |
| function | fanisoni | |
| user | tokotaha fakaʻaongaʻi | plural: kau fakaʻaongaʻi |
| user group | kulupu ʻo e kau fakaʻaongaʻi | |
| group | kulupu | |
| anonymous | taʻehingoa | anonymous user: tokotaha fakaʻaongaʻi taʻehingoa |
| administrator | tokotaha pule | |
| administration (interface) | pule | |
| username | hingoa fakaʻaongaʻi | the column "Login" too |
| password | lea fufū | |
| log in / sign in | hū | button: Hū |
| log out / sign out | ʻalu ki tuʻa | button: ʻAlu ki tuʻa |
| e-mail | ʻīmeili | e-mail address: tuʻasila ʻīmeili |
| cache | kēsi | clear cache: Fakamaʻa ʻa e kēsi |
| template | tempileti | |
| override | fetongi | override template: tempileti fetongi |
| fallback | poupou | fallback language: lea poupou |
| design | tisaini | |
| workflow | founga ngāue | |
| trigger | fakatupu | |
| event | meʻa hoko | |
| trash | veve | empty trash: Fakamaʻa ʻa e veve |
| bookmark | fakaʻilonga peesi | |
| settings | ngaahi fokotuʻutuʻu | |
| setup | fokotuʻu | the Setup tab; installation too |
| layout | fōtunga | Exponential Layouts |
| block | poloka | |
| zone | vahe | |
| extension | fakalahi | |
| package | pēkeisi | |
| search | kumi | button: Kumi |
| log | lēkooti | log file: faile lēkooti |
| queue | laine tatali | |
| job / task | ngāue | |
| cron | cron | cron job: ngāue cron |
| script | sikilipi | |
| worker | tokotaha ngāue | kept apart from tokotaha fakaʻaongaʻi (user) |
| kernel, core | uho | |
| server | sēva | |
| database | pangikē fakamatala | |
| folder | folota | |
| file | faile | |
| image | tā | |
| preview | vakai muʻa | |
| upload | fakaheke | button: Fakaheke |
| download | fakahifo | button: Fakahifo |
| export / import | ʻave atu / fakahū mai | |
| remove | toʻo | take out of a list or a place |
| delete | tamateʻi | destroy |
| edit | fakatonutonu | |
| editor | tokotaha fakatonutonu | |
| save / store | tauhi | |
| Store (shop tab) | Falekoloa | the word the shop menu uses for "Shop" |
| cancel | kaniseli | |
| OK | Sai | |
| apply | fakahoko | |
| back | foki | |
| next / previous | hoko / kimuʻa | |
| enable / disable | fakaʻaongaʻi / taʻofi | a setting or feature |
| enabled / disabled | ʻoku ngāue / ʻoku ʻikai ngāue | |
| active / inactive | ʻoku ngāue / ʻoku ʻikai ngāue | |
| run | fakalele | a script or job |
| start / stop | kamata / taʻofi | |
| session | sēsini | |
| hidden / visible | fufū / hā | hide / reveal: fufū / fakahā |
| archive | ʻakaivi | a file archive (.zip): ʻakaivi faile |
| order (sort) | hokohoko | sort: fokotuʻu hokohoko |
| order (shop) | ʻota | |
| shop | falekoloa | |
| basket | kato | |
| product | koloa | |
| customer | kasitomaa | |
| discount | holoki totongi | |
| VAT | tukuhau | |
| price | totongi | |
| currency | paʻanga | |
| tag | tāki | keyword tag; an HTML tag: tāki HTML |
| translation | liliu lea | |
| language | lea | main language: lea tefito |
| locale | lea fakafeituʻu | |
| state | tūkunga | the object state; state group: kulupu tūkunga |
| status | tuʻunga | |
| view (button) | vakai | a module view: vakaiʻanga |
| format | fōmeti | |
| sort | fokotuʻu hokohoko | |
| filter | sivi | |
| select | fili | selected: kuo fili |
| browse | vakavakai | |
| copy | kopi | |
| move | hiki | |
| add | tānaki | |
| create | faʻu | |
| new | foʻou | |
| restore | fakafoki | |
| install / uninstall | fokotuʻu / toʻo ʻa e fokotuʻu | |
| sub items | ngaahi meʻa ʻi lalo | |
| subtree | vaʻa | |
| related objects | ngaahi ʻōpeki fekauʻaki | relation: fekauʻaki |
| URL alias | hingoa kehe URL | |
| redirect | tafoki hala | |
| dashboard | tēpile ngāue | |
| content structure | langa ʻo e meʻa ʻi loto | |
| media library | laipeli mītia | |
| identifier / ID | fakaʻilonga / ID | |
| priority | muʻomuʻa | |
| owner | ʻonā | |
| modified / created | kuo fakatonutonu / kuo faʻu | |
| date / time | ʻaho / taimi | |
| error / warning | fehalaaki / fakatokanga | |
| help | tokoni | |
| notification | fanongonongo | |
| wizard | tataki | |
| feed (RSS) | fafanga | RSS feed: fafanga RSS |
| tab | tepi | |
| audit, audit trail | ʻaotita, lēkooti ʻaotita | |
| repository | tukuʻanga | |
| checksum | fika siviʻi | |
| account | ʻakauni | |
| default | tuʻumaʻu | default value: mahuʻinga tuʻumaʻu |
| toolbar | laine meʻangāue | |
| container | puha | |
| AI | AI | stays as the acronym |
| pattern (name pattern) | sīpinga | kept apart from tempileti (template) |
| wildcard | fakaʻilonga fakalūkufua | |
| remote ID | ID mamaʻo | |
| placeholder | fakaʻilonga fakataimi | |
| collection | tānaki | information collector: tānaki fakamatala |
| approve, approval | fakangofua | |
| software | polokalama | |
| creator / modifier | tokotaha faʻu / tokotaha fakatonutonu | |
| request (HTTP) | kole | |
| query | fehuʻi | search query: fehuʻi kumi |
| handler / provider / listener | tokotaha fakahoko / tokotaha ʻoatu / tokotaha fanongo | |
| engine | misini | |
| release / upgrade | tukuange / fakafoʻou | update: fakafoʻou |
| backup | kopi malu | |
| maintenance | fakaleleiʻi | |
| directory | folota | |
| header (HTTP) | ʻulu | |
| page layout (pagelayout template) | tempileti peesi | kept apart from fōtunga (Exponential Layouts) |
| passkey | kī hū | |
| two-step sign-in | hū ʻi he sitepu ʻe ua | |
| device / browser | meʻangāue / palausa | |
| link | hokotaki | |
| registration, register | lesisita | |
| payment gateway | matapā totongi | |
| form | foomu | |
| comment | fakakaukau | |
| newsletter / subscription | tohi fanongonongo / kau atu | |
| field | funga tohi | |
| value | mahuʻinga | |
| list | lisi | |
| page | peesi | |
| title / name | kaveinga / hingoa | the column "Name": Hingoa |
| description | fakamatala | |
| type | faʻahinga | |
| path | hala | |
| width / height | fālahi / māʻolunga | |
| size | lahi | |
| none | ʻikai ha meʻa | |
| yes / no | ʻio / ʻikai | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Falekoloa, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is Tauhi.
- "Delete" is Tamateʻi (destroy) and "remove" is Toʻo (take out), so that the two buttons are never confused.
- "OK" is Sai.
