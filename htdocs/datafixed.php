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
            <h2>FIXED INJECTION</h2>
        </div>
        <form id="myForm" method="POST">
            <div class="form-group">
                <label for="input1">TRY OS INJECT: </label>
                <input type="text" id="input1" name="input1" placeholder="Ping atmak istediğiniz IP'yi giriniz.">
            </div>
            <div class="form-group">
                <label for="input2">TRY SQL INJECT: </label>
                <input type="text" id="input2" name="input2" placeholder="Personel adını giriniz.">
            </div>
            <div class="form-group">
                <label for="input3">TRY XSS INJECT: </label>
                <input type="text" id="input3" name="input3" placeholder="Çıktı olarak istediğiniz düzyazıyı giriniz.">
            </div>
            <button type="submit">GO!</button>
        </form>
        <div>
            <label id="label1">

                <?php //Inputların kontrolünü sağlayan yer.
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input1 = isset($_POST['input1']) ? htmlspecialchars($_POST['input1']) : '';
                    $input2 = isset($_POST['input2']) ? htmlspecialchars($_POST['input2']) : '';
                    $input3 = isset($_POST['input3']) ? htmlspecialchars($_POST['input3']) : '';
                    //Input3'te de htmlspecialchars kullanılarak xss attacaktan kaçınılmıştır.

                    if ($input1) {    // Ping işleminin yapıldığı yer. 
                         $pingResult = shell_exec("ping -c 4 " . escapeshellarg($input1));
                         //escapeshellarg komutu kullanılarak kullanıcıdan terminal komutu gelmesi engellendi.
                         if ($pingResult == null){
                            echo "Lütfen geçerli bir IP adresi giriniz!";
                         }else {
                            echo "OS INJECT (Ping Sonuçları): <br><pre>" . ($pingResult) . "</pre>";
                         }
                         
                    }
                    elseif ($input2) { //SQL sorgusu ve SQL injection yapıldığı yer.
                        $stmt = $conn->prepare("SELECT * FROM Personel WHERE ad = ?");
                        $stmt->bind_param("s", $input2);
                        $stmt->execute();
                        $result = $stmt->get_result();

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

                        $stmt->close();

                    } 
                    elseif ($input3) {    //Input direkt html karakterlerini decode ettiği için burada ekstra bir güvenlik önlemi almadım.
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
$conn->close(); //SQL bağlantısının kapanışı 
?> 