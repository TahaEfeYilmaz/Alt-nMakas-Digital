<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AltınMakas | Randevu Sistemi</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #1a1a1a; color: white; font-family: 'Montserrat', sans-serif; text-align: center; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: #262626; padding: 30px; border-radius: 15px; border-top: 5px solid #d4af37; box-shadow: 0 10px 30px rgba(0,0,0,0.8); }
        h1 { font-family: 'Oswald', sans-serif; color: #d4af37; font-size: 2.5em; margin-bottom: 5px; letter-spacing: 2px; }
        h2 { color: #ffffff; font-size: 1.2em; margin-bottom: 30px; font-weight: 400;}
        
        /* Takvim Seçici */
        .takvim-secici { margin-bottom: 30px; background: #333; padding: 15px; border-radius: 10px; display: inline-block; border: 1px solid #d4af37; }
        .takvim-secici input { padding: 10px; font-size: 16px; border-radius: 5px; background: #1a1a1a; color: white; border: 1px solid #555; cursor: pointer;}
        
        /* Saat Tablosu */
        .saat-tablosu { display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; margin-bottom: 40px; }
        .saat-kutusu { 
            width: 40%; 
            padding: 15px; 
            border-radius: 8px; 
            font-weight: bold; 
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .bos { background-color: white; color: #1a1a1a; border: 2px solid #ccc; }
        .dolu { background-color: #e74c3c; color: white; border: 2px solid #c0392b; }

        /* Form Css */
        form.randevu-formu { display: flex; flex-direction: column; gap: 15px; max-width: 400px; margin: auto; }
        .randevu-formu input, .randevu-formu select, .randevu-formu button { padding: 12px; font-size: 16px; border-radius: 8px; border: none; width: 100%; box-sizing: border-box;}
        .randevu-formu input, .randevu-formu select { background-color: #1a1a1a; color: white; border: 1px solid #444; }
        .randevu-formu button { background-color: #d4af37; color: #1a1a1a; font-weight: bold; cursor: pointer; text-transform: uppercase; transition: 0.3s; padding: 15px;}
        .randevu-formu button:hover { background-color: white; }
        hr { border: 0; border-top: 1px solid rgba(212, 175, 55, 0.3); margin: 30px 0; }
        
        @media (max-width: 600px) {
            .saat-kutusu { width: 100%; flex-direction: column; gap: 5px; text-align: center;}
        }
    </style>
</head>
<body>

<div class="container">
    <h1>ALTINMAKAS</h1>
    <h2>Günlük Çalışma Programı</h2>
    
    <?php
    // Veritabanı Bağlantısı
    $baglanti = mysqli_connect("localhost", "root", "", "berber_db");
    if (!$baglanti) { die("Veritabanı bağlantı hatası!"); }

    // Takvimden Tarih Çekme
    $secilen_tarih = isset($_GET['tarih']) ? $_GET['tarih'] : date('Y-m-d');
    
    echo "<div class='takvim-secici'>";
    echo "<form method='GET' action='index.php' style='margin:0;'>";
    echo "<strong style='margin-right:10px;'>Tarih Seçin: </strong>";
    echo "<input type='date' name='tarih' value='$secilen_tarih' onchange='this.form.submit()'>";
    echo "</form>";
    echo "</div>";

    // O günün randevularını çek
    $sql = "SELECT * FROM randevular WHERE DATE(tarih) = '$secilen_tarih'";
    $sonuc = mysqli_query($baglanti, $sql);
    
    $dolu_saatler = [];
    $isimler = [];
    
    while($satir = mysqli_fetch_assoc($sonuc)) {
        $saat = date('H:i', strtotime($satir['tarih']));
        $dolu_saatler[] = $saat;
        $isimler[$saat] = $satir['ad_soyad'];
    }

    // 09:00 - 22:00 Arası Saatleri Yazdır
    $mesai_baslangic = strtotime("09:00");
    $mesai_bitis = strtotime("22:00");
    
    echo "<div class='saat-tablosu'>";
    for ($i = $mesai_baslangic; $i <= $mesai_bitis; $i += 1800) { 
        $suanki_saat = date('H:i', $i);
        
        if (in_array($suanki_saat, $dolu_saatler)) {
            $isim = $isimler[$suanki_saat];
            echo "<div class='saat-kutusu dolu'><span>$suanki_saat</span> <span>$isim (DOLU)</span></div>";
        } else {
            echo "<div class='saat-kutusu bos'><span>$suanki_saat</span> <span>BOŞ</span></div>";
        }
    }
    echo "</div>";
    ?>

    <hr>
    
    <h1 style="font-size: 2em;">YENİ RANDEVU AL</h1>
    <form class="randevu-formu" action="randevu_kaydet.php" method="POST">
        <input type="text" name="ad" placeholder="Adınız Soyadınız" required>
        <input type="datetime-local" name="tarih" required>
        <select name="hizmet">
            <option value="Saç Kesimi">Saç Kesimi</option>
            <option value="Sakal Tıraşı">Sakal Tıraşı</option>
            <option value="Saç & Sakal">Saç & Sakal</option>
            <option value="Cilt Bakımı">Cilt Bakımı</option>
        </select>
        <button type="submit">Sisteme Kaydet</button>
    </form>
</div>

</body>
</html>
