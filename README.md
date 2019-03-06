# Overview

Ini proyek yang gw bikin buat kebutuhan Uji Kompetensi RPL Tahun 2019. _At least part of it_ secara sekolah gw yang 4 tahun studinya minta 2 _platform_ dan ini bagian web + API-nya

# Cara Pake

Proyek ini dibikin pake framework PHP laravel jadi perlu diperhatikan hal-hal di bawah ini

## Yang Mesti Diinstal

1. PHP versi 7.2 atau lebih baru (lengkapnya bisa diliat [disini](https://laravel.com/docs/5.8/installation))
2. Database SQL (MySQL, MariaBD, Postgre, etc);
3. Kalo gamau males intall 2 benda di atas boleh install paket AMP(Apache + MySQL/MariaDB + PHP). Buat Windows boleh install WAMP (ga _recommend_), macOS direkomendasikan install MAMP, lainnya boleh install XAMPP (berlaku untuk Windows, Linux, dan macOS)
4. Composer (bisa dicari [disini](getcomposer.org))
5. Git (Gawajib tapi sangat disunnahkan untuk menginstall karena kalo gw update gaperlu download ulang proyeknya. Download [disini](https://git-scm.com/downloads))

## Cara biar proyek ini ada di komputer

TL:DR Gimanapun caranya yang penting ada di komputer kalian

### Download ZIP

Paling praktis tapi ngga _recommended_ karena kalo ada update seluruh proyeknya mesti didownload ulang

### Pake Git (**_recommended_**)

Ini cara yang sangat disarankan karena kalo ada update cuma bagian yang diupdate yang mesti didownload dan kalian bisa ikut kolaborasi buat ngembangin proyek ini sampe jadi atau take it to the next level.

#### Caranya

Install git kalo belum ngistall. Buka terminal/Command Prompt/PowerShell terus ketik perintah di bawah abis itu pencet `enter`

    git clone https://github.com/bondanrsantoso/ujikom-rpl-inventaris.git

#### Kalo ada update

Kalo gw kebetulan update atau kalian _notice_ ada update di _repository_ masukin perintah di bawah terus pencet `enter`

    git pull

#### _Side note_

Catatan buat perintah di atas itu mesti dimasukin dengan proyek ini sebagai **direktori aktif di terminal/command prompt/powershell**. Contohnya proyek kalian disimpen di direktori `~/proyek/ujikom-rpl-inventaris` atau `C:\proyek\ujikom-rpl-inventaris` buat kalian yang pake Windows.

Kalo di Command Prompt/Powershell windows jadinya begini

    C:\> cd "\proyek\ujikom-rpl-inventaris"
    C:\proyek\ujikom-rpl-inventaris> git pull

Kalo di terminal `sh` (Linux/macOS)

    #Kalo di linux tampilannya kaya begini kira-kira:
    user@localhost:~$ cd ~/proyek/ujikom-rpl-inventaris
    user@localhost:~/proyek/ujikom-rpl-inventaris$ git pull

    #Kalo di macOS tampilan standarnya begini
    Users-MacBook-Pro:~ User$ cd ~/proyek/ujikom-rpl-inventaris
    Users-MacBook-Pro:ujikom-rpl-inventaris User$ git pull

## _Set-Up_ proyek laravel

Abis proyek ini ada di komputer kalian, boleh (baca: harus) disetup dulu laravel-nya biar bisa _running_

1.  Install PHP+Database (atau pake paket AMP) kalo belum
2.  Install [Composer](getcomposer.org) kalo belum
3.  Masuk ke direktori tempat proyek ini disimpen (liat instriksi di atas yang judulnya **_Side note_** kalo gapaham)
4.  Di Terminal/Command Prompt/Powershell masukin perintah di bawah

        composer install

5.  _Copy_ file `.env.example` abis itu rename hasil _copy_-annya jadi `.env`
    -   Catatan: file yang di depannya dikasih tanda titik itu otomatis tersembunyi di UNIX, Linux dan macOS
    -   Catatan buat pengguna Windows: nge-rename file jadi `.env` itu gadibolehin sama Explorer jadi mending nge-rename via text editor atau IDE favorit kalian (Sublime Text, VS Code, PHPStorm, Atom, etc)
    -   Catatan buat pengguna macOS: file tersembunyi ngga bisa ditampilin di Finder. kalupun bisa itu _tricky_ dan ga _worth the hassle_ jadi mending pake metode ala Windows
6.  Abis itu generate _security key_ buat kebutuhan _session_ (ini wajib, soalnya kalo nggak app laravel-nya gamau _running_)
7.  Nyalain Database kalian
8.  Bikin databse baru buat nampung data dari aplikasi ini (bisa pake GUI bar ga ribet ala PHPMyAdmin, Sequel Pro, Navicat, etc), kasih nama `ujikom` misalnya
9.  Edit file `.env` buat konfigurasi. Terutama konfigurasi database contohnya di bawah

        DB_CONNECTION=mysql # Ganti kalo pake DB lain selain MySQL/MariaDB
        DB_HOST=127.0.0.1 # bisa juga localhost
        DB_PORT=3306 # ganti kalo perlu
        DB_DATABASE=ujikom
        DB_USERNAME=usernameDatabase
        DB_PASSWORD=PasswordSuperRahasia

10. Karena udah ada file migrasi database laravel, masukin perintah di bawah ini di terminal/command prompt kalian

        php artisan migrate
        # kalo perintah di atas gagal masukin yang bawah
        php artisan migrate:fresh

11. Kalo udah siap running masukin perintah `php artisan serve` di terminal/command prompt
12. _Enjoy_

## Kolaborasi

Biar makin cepet beres boleh dibantu pengembangannya buat yang pake git dengan nge-_push_ hasil kerja kalian ke repository ini dengan perintah di bawah (masukinnya sebaris sebaris). Tapi jangan di _branch_ `master`. _Branch_ `master` cuma buat kode yang udah verified.

Maka dari itu ada baiknya bikin _branch_ baru (namanya bebas, tapi buat contoh disini namanya `nama-branch`) dengan perintah di bawah ini (masukin sebaris-sebaris):

    git branch nama-branch
    git checkout nama-branch

Catatan: Perintah di atas bakalan gagal kalo kalian udah terlanjur coding. cara mengatasinya adalah dengan perintah berikut:

    git branch nama-branch
    git stash
    git checkout nama-branch
    git stash pop

Kemudian kalo udah siap dibagi sama yang lain masukin perintah di bawah

    git add .
    git commit -m "pesan commit. contohnya: nambah fungsi blabla. maks 72 karakter"
    git push origin nama-branch

Kalo misalnya ada update di _branch_ `master` dan kalian mau lanjut develop dari situ, _push_ hasil kerja kalian terlebih dahulu (lihat perintah di atas) abis itu masukin perintah di bawah ini sebaris-sebaris:

    git checkout master
    git pull
    git branch -C master nama-branch

# Taking to the next level

Kalo menurut kalian aplikasi ini bisa _taken to the next level_ ada baiknya kalian _fork_ _repository_ ini dan lanjut develop disana

# Akhir Kata

Ini adalah proyek yang saya buat untuk uji kompetensi RPL jaman tahun 2019. Kalo kalian kebetulan perlu proyek ini buat belajar silahkan pakai proyek ini sesuka kalian dengan mematuhi _guideline_ yang sudah ditetapkan di atas. Kalo kebetulan kalian nge-fork dan _publish_ aplikasi ini di bawah nama kalian, _it's good_ tapi jangan lupa untuk mencantumkan keterangan bahwa projek kalian itu di-fork dari _repository ini_. Happy Coding!
