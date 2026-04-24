<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İşlem Sonucu | AltınMakas</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #1a1a1a; 
            font-family: 'Montserrat', sans-serif; 
            color: white; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            padding: 20px;
            box-sizing: border-box;
        }
        .container { 
            background: #262626; 
            padding: 40px; 
            border-radius: 15px; 
            text-align: center; 
            border-top: 5px solid #d4af37; 
            max-width: 500px; 
            width: 100%; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.7);
        }
        h1 { color: #d4af37; font-family: 'Oswald', sans-serif; text-transform: uppercase; letter-spacing: 1px;}
        p { color: #e0e0e0; font-size: 1.1em; line-height: 1.5; }
        
        a.btn { 
            display: inline-block; 
            margin-top: 25px; 
            background-color: #d4af37; 
            color: #1a1a1a; 
            padding: 15px 30px; 
            text-decoration: none; 
            font-weight: bold; 
            border-radius: 8px; 
            text-transform: uppercase; 
            transition: 0.3s; 
        }
        a.btn:hover { background-color: white; transform: scale(1.05); }
    </style>
</head>
<body>

    <div class="container">
        <?php
        $baglanti = mysqli_connect("localhost", "root", "", "berber_db");
        
        if (!$baglanti) { 
            die("<h1>Sistem Hatası!</h1><p>Şu an veritabanına bağlanılamıyor.</p>"); 
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $ad = $_POST['ad'];
            $tarih = $_POST['tarih'];
            $hizmet = $_POST['hizmet'];

            // Çakışma kontrolü
            $kontrol_sql = "SELECT COUNT(*) as sayi FROM randevular WHERE tarih = '$tarih'";
            $kontrol_sonuc = mysqli_query($baglanti, $kontrol_sql);
            $satir = mysqli_fetch_assoc($kontrol_sonuc);

            if ($satir['sayi'] > 0) {
                // Saat doluysa
                echo "<h1>Saat Dolu!</h1>";
                echo "<p>Üzgünüz, seçtiğiniz tarih ve saatte randevumuz doludur. Lütfen takvimden boş bir saat seçiniz.</p>";
                echo "<a href='index.php' class='btn'>Takvime Geri Dön</a>";
            } else {
                // Saat boşsa kaydet
                $sql = "INSERT INTO randevular (ad_soyad, tarih, hizmet) VALUES ('$ad', '$tarih', '$hizmet')";
                
                if (mysqli_query($baglanti, $sql)) {
                    echo "<h1>Randevu Başarılı!</h1>";
                    echo "<p>Sayın <strong>$ad</strong>, randevunuz sistemimize kaydedildi. Sizi dükkanımızda bekliyoruz!</p>";
                    echo "<a href='index.php' class='btn'>Takvime Geri Dön</a>";
                } else {
                    echo "<h1>Hata Oluştu!</h1>";
                    echo "<p>Kayıt sırasında bir hata oluştu. Hata kodu: " . mysqli_error($baglanti) . "</p>";
                    echo "<a href='index.php' class='btn'>Tekrar Dene</a>";
                }
            }
        } else {
            echo "<h1>Geçersiz İşlem!</h1>";
            echo "<p>Bu sayfaya doğrudan erişim kapalıdır. Lütfen formu kullanın.</p>";
            echo "<a href='index.php' class='btn'>Ana Sayfaya Dön</a>";
        }

        mysqli_close($baglanti);
        ?>
    </div>

</body>
</html>
