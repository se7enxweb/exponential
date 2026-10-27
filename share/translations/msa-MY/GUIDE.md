# Malay (msa-MY): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Malay of Malaysia (Bahasa Melayu, Bahasa Malaysia) in Rumi, the Latin script, in the spelling and the
computing terminology of Dewan Bahasa dan Pustaka (DBP, Pusat Rujukan Persuratan Melayu) and of current Malaysian
software. The neutral, technical register of a Malaysian user interface: short, plain, without literary or
colloquial words.

`msa-SG` (Singapore) and `msa-BN` (Brunei) have no catalogue of their own: their
`FallbackLanguages[]` lines in `settings/i18n.ini` give them this one. Standard Malay of Malaysia, Singapore and Brunei is the same written language (the joint spelling of
MABBIM), so nothing in this catalogue may be specific to Malaysia apart from the default examples of the locale.

### Malay is not Indonesian

Indonesian (`ind-ID`) shares the grammar and much of the vocabulary, but its catalogue is no model for words. Where
Malaysian usage and Indonesian differ, the Malaysian word is used. The differences that occur most in this
interface:

| English | Malay (use) | Indonesian (do not use) |
|---|---|---|
| delete | padam | hapus |
| settings | tetapan | pengaturan, setelan |
| file | fail | berkas, file |
| e-mail | e-mel | surel, email |
| password | kata laluan | kata sandi |
| log in / log out | log masuk / log keluar | masuk / keluar |
| upload / download | muat naik / muat turun | unggah / unduh |
| server | pelayan | server |
| database | pangkalan data | basis data |
| browser | pelayar | peramban |
| device | peranti | perangkat |
| software | perisian | perangkat lunak |
| link | pautan | tautan |
| preview | pratonton | pratinjau |
| create | cipta | buat |
| new | baharu | baru |
| information | maklumat | informasi |
| message | mesej | pesan |
| image | imej | gambar |
| error / warning | ralat / amaran | galat / peringatan |
| list | senarai | daftar |
| table / column | jadual / lajur | tabel / kolom |
| field | medan | bidang |
| title | tajuk | judul |
| date / time | tarikh / masa | tanggal / waktu |
| number | nombor | nomor |
| minute / second | minit / saat | menit / detik |
| account | akaun | akun |
| package | pakej | paket |
| archive | arkib | arsip |
| default | lalai | bawaan, default |
| enable / disable | dayakan / nyahdayakan | aktifkan / nonaktifkan |
| install / uninstall | pasang / nyahpasang | pasang / copot |
| successful | berjaya | berhasil |
| invalid | tidak sah | tidak valid |
| free (of charge) | percuma | gratis |
| government | kerajaan | pemerintah |
| car | kereta | mobil |
| December, August, March | Disember, Ogos, Mac | Desember, Agustus, Maret |
| Sunday, Monday, Thursday, Friday | Ahad, Isnin, Khamis, Jumaat | Minggu, Senin, Kamis, Jumat |
| please (instruction) | sila | silakan |
| have / own | mempunyai | memiliki |
| size | saiz | ukuran |
| support | sokongan | dukungan |
| security | keselamatan | keamanan |
| performance | prestasi | kinerja |
| percent | peratus | persen |
| automatic | automatik | otomatis |
| quality | kualiti | kualitas |
| priority | keutamaan | prioritas |
| update | kemas kini | perbarui, pembaruan |

The spelling follows the Malaysian forms where the two differ: *-isasi* (organisasi, konfigurasi), *kualiti*,
*aktiviti*, *universiti*, *teknikal*, *praktikal*, *sistem*, *struktur*, *versi*, *fungsi*, *eksport*,
*kategori*, *statistik*.

## 2. Form of address

The interface addresses the user with the polite **anda** (your: **anda** after the noun, *akaun anda*). It is
written in lower case inside a sentence and with a capital only at the start of a sentence (*Anda*). Never *kamu*,
*awak*, *engkau*, and never *Saudara* or *tuan/puan*.

- **Buttons, menu items and links**: the bare verb, imperative: *Simpan*, *Batal*, *Padam*, *Edit*, *Hantar untuk
  diterbitkan*, *Alih keluar yang dipilih*, *Alih ke tong sampah*, *Cipta di sini*, *Pilih semua*.
- **Titles, column headings and tabs**: nouns or noun phrases: *Tetapan*, *Terjemahan*, *Lokasi utama*, *Sejarah
  versi*.
- **Instructions**: *Sila* plus the verb, or the bare verb: *Sila pilih item dan klik "OK".*, *Masukkan nama.*
  A reference ("see ...") is *Lihat ...*.
- **Confirmations and questions**: *Adakah anda pasti mahu ...?*, *Adakah anda mahu ...?*: *Adakah anda pasti mahu
  memadam kelas ini?*
- **What the user can or may do**: *Anda boleh ...*, *Anda tidak boleh ...*; impersonal: *... tidak boleh diubah*.
  "You do not have permission" is *Anda tidak mempunyai kebenaran untuk ...*.
- **Passive voice** is natural Malay and is preferred over a subject-less active sentence: *Fail telah dimuat
  naik.*, *Draf telah disimpan.*, *Objek tidak dijumpai.*
- **Capitalisation**: sentence case; only the first word of a button, title or heading has a capital (*Simpan
  draf*, *Sejarah versi*), as current Malaysian software does. Proper names and acronyms keep their form.

## 3. Script and typography

- **Rumi (Latin script)**, left to right. No Jawi.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Malay puts the modifier after the noun, so a placeholder often
  moves: "Edit %name" is *Edit %name*, "%count objects" is *%count objek*, "%name's settings" is *Tetapan %name*.
  Never a prefix or suffix glued to a placeholder: *%name-nya* is fine only with the hyphen, and better avoided.
- **Plural**: Malay nouns do not change for the plural: *%count objek*, *semua pengguna*. Reduplication
  (*objek-objek*) is not used in labels; *beberapa*, *semua*, *para* where the sense needs it.
- **Affixes on loanwords and acronyms** take a hyphen: *di-cache*, *URL-nya*; prefer a wording without them:
  *disimpan dalam cache*.
- **Quotation marks**: straight double quotes "..." as in the source; where the source has `&quot;` or a straight "
  that is code or an entity, keep it exactly.
- **Punctuation**: as in the source; a colon after a label, an ellipsis "..." where the source has one.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Malay | Note |
|---|---|---|
| content | kandungan | |
| content object | objek kandungan | |
| object | objek | |
| node | nod | main node: nod utama; parent node: nod induk |
| location | lokasi | main location: lokasi utama |
| item (list entry) | item | |
| class | kelas | content class; class group: kumpulan kelas; a PHP class: kelas PHP |
| content class | kelas kandungan | |
| attribute | atribut | |
| datatype | jenis data | |
| version | versi | version history: sejarah versi |
| draft | draf | store draft: simpan draf |
| publish | terbitkan | published: diterbitkan; publishing: penerbitan |
| send for publishing | hantar untuk diterbitkan | |
| discard | buang | discard draft: buang draf |
| archived | diarkibkan | |
| pending | belum selesai | waiting: menunggu |
| rejected | ditolak | |
| siteaccess | akses laman | plural the same |
| site, website | laman, laman web | |
| page | halaman | |
| section | seksyen | the Exponential section; an INI section: bahagian |
| role | peranan | |
| policy | dasar | one rule of a role |
| limitation | had | plural the same |
| permission | kebenaran | access denied: akses ditolak |
| module | modul | |
| function | fungsi | |
| view (a module view, noun) | paparan | the button "View": Lihat |
| user | pengguna | |
| user group | kumpulan pengguna | |
| group | kumpulan | |
| anonymous | tanpa nama | |
| administrator | pentadbir | |
| administration (interface) | pentadbiran | |
| username | nama pengguna | login (the name): nama pengguna |
| password | kata laluan | |
| log in / sign in | log masuk | |
| log out / sign out | log keluar | |
| e-mail | e-mel | e-mail address: alamat e-mel |
| cache | cache | clear cache: kosongkan cache |
| template | templat | |
| override | ganti | override template: templat ganti |
| fallback | gantian | fallback language: bahasa gantian |
| backup | sandaran | |
| design | reka bentuk | |
| workflow | aliran kerja | |
| trigger | pencetus | |
| event | peristiwa | |
| trash | tong sampah | empty trash: kosongkan tong sampah; move to trash: alih ke tong sampah |
| bookmark | penanda buku | |
| settings | tetapan | configuration: konfigurasi |
| setup | persediaan | the Setup tab |
| layout | susun atur | Exponential Layouts |
| page layout (pagelayout template) | tataletak halaman | kept apart from susun atur (Exponential Layouts) |
| block | blok | |
| zone | zon | |
| extension | sambungan | |
| package | pakej | |
| search | cari | search (noun): carian; search index: indeks carian; indexing: pengindeksan |
| log | log | a log file; never "log in" |
| queue | baris gilir | job queue: baris gilir tugas |
| job / task | tugas | |
| cron | cron | a cron job: tugas cron |
| script | skrip | |
| worker | proses pekerja | |
| kernel, core | teras | |
| server | pelayan | |
| database | pangkalan data | |
| folder | folder | |
| directory | direktori | |
| file | fail | |
| path | laluan | |
| image | imej | thumbnail: lakaran kecil |
| preview | pratonton | |
| upload | muat naik | uploaded: dimuat naik |
| download | muat turun | |
| export / import | eksport / import | |
| remove | alih keluar | take out of a list or place |
| delete | padam | destroy; deleted: dipadam |
| edit | edit | editing: pengeditan |
| editor | editor | a person, and the editing software |
| save / store | simpan | the verb "Store" of the forms |
| Store (shop tab) | Kedai | the shop, as "Shop" |
| cancel | batal | |
| OK | OK | |
| apply | guna | |
| back | kembali | |
| next / previous | seterusnya / sebelumnya | |
| enable / disable | dayakan / nyahdayakan | |
| enabled / disabled | didayakan / dinyahdayakan | |
| active / inactive | aktif / tidak aktif | |
| run | jalankan | a script, a job |
| start / stop | mula / henti | |
| session | sesi | |
| hidden / visible | tersembunyi / kelihatan | hide / reveal: sembunyikan / tunjukkan |
| archive | arkib | a file archive too |
| order (sort) | tertib | sort order: tertib isihan |
| order (shop) | pesanan | |
| shop | kedai | |
| basket | bakul | |
| product | produk | |
| customer | pelanggan | |
| discount | diskaun | |
| VAT | VAT | cukai nilai tambah where a sentence names it |
| price | harga | |
| currency | mata wang | |
| tag | tag | an HTML tag: tag HTML |
| translation | terjemahan | |
| language | bahasa | main language: bahasa utama; interface language: bahasa antara muka |
| locale | lokal | |
| state | keadaan | the object state |
| status | status | |
| format | format | |
| sort | isih | |
| filter | penapis | to filter: tapis |
| select | pilih | select all: pilih semua; selected: dipilih |
| browse | semak imbas | |
| copy | salin | |
| move | alih | |
| add | tambah | |
| create | cipta | |
| new | baharu | |
| restore | pulihkan | |
| install / uninstall | pasang / nyahpasang | |
| sub items | subitem | |
| subtree | subpepohon | |
| related objects | objek berkaitan | relation: hubungan |
| URL alias | alias URL | |
| redirect | ubah hala | |
| dashboard | papan pemuka | |
| content structure | struktur kandungan | |
| media library | pustaka media | |
| identifier | pengecam | ID: ID |
| priority | keutamaan | |
| owner | pemilik | |
| modified / created | diubah suai / dicipta | |
| date / time | tarikh / masa | |
| error / warning | ralat / amaran | |
| help | bantuan | |
| notification | pemberitahuan | |
| wizard | wizard | setup wizard: wizard persediaan |
| feed (RSS) | suapan | feed reader: pembaca suapan |
| tab | tab | |
| audit, audit trail | audit, jejak audit | |
| repository | repositori | |
| checksum | hasil tambah semak | |
| account | akaun | |
| default | lalai | default value: nilai lalai |
| toolbar | bar alat | |
| container | bekas | |
| AI | AI | kecerdasan buatan where a sentence explains it |
| pattern (name pattern) | corak | |
| wildcard | kad bebas | |
| remote ID | ID jauh | |
| placeholder | pemegang tempat | |
| collection | koleksi | information collector: pengumpul maklumat |
| information | maklumat | |
| approve, approval | luluskan, kelulusan | |
| software | perisian | |
| creator / modifier | pencipta / pengubah suai | |
| request (HTTP) | permintaan | |
| query | pertanyaan | |
| handler / provider / listener | pengendali / pembekal / pendengar | |
| engine | enjin | |
| release / upgrade | keluaran / naik taraf | |
| update | kemas kini | |
| maintenance | penyelenggaraan | |
| header (HTTP) | pengepala | |
| passkey | kunci laluan | |
| two-step sign-in | log masuk dua langkah | |
| device / browser | peranti / pelayar | |
| link | pautan | |
| registration, register | pendaftaran, daftar | |
| payment gateway | get laluan pembayaran | |
| field | medan | |
| value | nilai | |
| property | sifat | |
| message | mesej | |
| title | tajuk | |
| description | penerangan | |
| summary | ringkasan | |
| details | butiran | |
| list | senarai | |
| table / column / row | jadual / lajur / baris | |
| keyword | kata kunci | |
| comment | komen | |
| newsletter | surat berita | |
| subscription | langganan | |
| sender / recipient | penghantar / penerima | |
| attachment | lampiran | |
| security | keselamatan | |
| certificate | sijil | |
| confirm, confirmation | sahkan, pengesahan | |
| successful / failed | berjaya / gagal | |
| invalid | tidak sah | |
| required / optional | wajib / pilihan | |
| size | saiz | |
| statistics / report | statistik / laporan | |
| online | dalam talian | |
| interface | antara muka | user interface: antara muka pengguna |
| type | jenis | |
| level | tahap | |
| none | tiada | "no ...": tiada ... |
| yes / no | ya / tidak | |
| free (of charge) | percuma | |
| government | kerajaan | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is *Kedai*, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is *simpan*.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) and words spelled the
  same in Malay (*status*, *format*, *data*, *editor*, *media*, *audit*) are listed with their reason in
  `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
