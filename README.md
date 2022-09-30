# Data Provinsi, Kota/Kabupaten, Kecamatan, dan Kelurahan/Desa di Indonesia Beserta Kode Pos
Data diambil dari GIS BPS (Badan Pusat Statistik) **terakhir di fetch tanggal 30 September 2022**

Menggunakan framework CodeIgniter, dengan database mysql

- CodeIgniter 3.1.9 with PHP Minimum 7.4

Cara menggunakan:
1. Import database, bisa didapat pada folder /database
2. jalankan local server
3. buka browser, akses http://localhost/get-wilayah-indonesia-php **NOTE : INI AKAN MELAKUKAN FETCH DENGAN KODE POS, AKAN TETAPI DATANYA TIDAK LENGKAP, JIKA ANDA TIDAK MEMBUTUHKAN KODE POS, BISA LANGSUNG SKIP KE POIN 5**
4. tunggu hingga selesai
5. buka /application/controllers/Index_controller.php
6. comment line 88 and uncomment line 91 ($api_url = "https://sig.bps.go.id/rest-bridging/getwilayah?level=".$level;)
7. buka kembali browser, akses http://localhost/get-wilayah-indonesia-php
8. tunggu hingga selesai untuk melengkapi data

=====================================================================================================
# Data of Province, City/Regency, District, and Sub-District/Village in Indonesia along with Postal Code
Data taken from GIS BPS (Badan Pusat Statistik) **last taken on 30 September 2022**

Using CodeIgniter framework, with mysql database

- CodeIgniter 3.1.9 with Minimum PHP 7.4

How to use:
1. Import the database, can be found in the /database folder
2. run local server
3. open a browser, access http://localhost/get-region-indonesia-php **NOTE : THIS WILL GET REGION WITH POSTAL CODE, BUT IT'S NOT COMPLETE, IF YOU DOESN'T NEED POSTAL CODE, YOU CAN SKIP TO POINT 5**
4. wait for it to finish
5. open /application/controllers/Index_controller.php
6. comment line 88 and uncomment line 91 ($api_url = "https://sig.bps.go.id/rest-bridging/getwilayah?level=".$level;)
7. open the browser again, access http://localhost/get-wilayah-indonesia-php
8. wait for it to finish