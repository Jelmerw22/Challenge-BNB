<?php
// Je hebt een database nodig om dit bestand te gebruiken....
include "database.php";
if (!isset($db_conn)) { //deze if-statement checked of er een database-object aanwezig is. Kun je laten staan.
    return;
}

$database_gegevens = null;
$poolIsChecked = false;
$bathIsChecked = false;

$sql = "SELECT * FROM homes"; //Selecteer alle huisjes uit de database

if (isset($_GET['filter_submit'])) {

    if ($_GET['faciliteiten'] == "ligbad") { // Als ligbad is geselecteerd filter dan de zoekresultaten
        $bathIsChecked = true;

        $sql = "SELECT * FROM homes WHERE bath_present=1"; // query die zoekt of er een BAD aanwezig is.
    }

    if ($_GET['faciliteiten'] == "zwembad") {
        $poolIsChecked = true;

        $sql = "SELECT * FROM homes WHERE pool_present=1"; // query die zoekt of er een ZWEMBAD aanwezig is.
    }
}


if (is_object($db_conn->query($sql))) { //deze if-statement controleert of een sql-query correct geschreven is en dus data ophaalt uit de DB
    $database_gegevens = $db_conn->query($sql)->fetchAll(PDO::FETCH_ASSOC); //deze code laten staan
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bed & Breakfast.</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <link href="css/index.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <h1>Quattro Cottage Rental</h1>
    </header>
    <main>
        <div class="left">
            <div class="book">
                <form action="" method="POST">
                <h3>Reservering maken</h3>
                <div class="form-control">
                    <label for="aantal_personen">Vakantiehuis</label>
                    <select name="gekozen_huis" id="gekozen_huis">
                        <option value="1">IJmuiden Cottage</option>
                        <option value="2">Assen Bungalow</option>
                        <option value="3">Espelo Entree</option>
                        <option value="4">Weustenrade Woning</option>
                    </select>
                </div>
                <div class="form-control">
                    <label for="aantal_personen">Aantal personen</label>
                    <input type="number" name="aantal_personen" id="aantal_personen">
                </div>
                <div class="form-control">
                    <label for="aantal_dagen">Aantal dagen</label>
                    <input type="number" name="aantal_dagen" id="aantal_dagen">
                </div>
                <div class="form-control">
                    <h5>Beddengoed</h5>
                    <label for="beddengoed_ja">Ja</label>
                    <input type="radio" id="beddengoed_ja" name="beddengoed" value="ja">
                    <label for="beddengoed_nee">Nee</label>
                    <input type="radio" id="beddengoed_nee" name="beddengoed" value="nee">
                </div>
                <input class="submit" type="submit" value="Reserveer Huis" name="submit"></input>
                <?php if (isset($database_gegevens) && $database_gegevens != null) : ?>
                <?php foreach ($database_gegevens as $huisje) : ?>
                <?php
                    $gekozen = [1 => 55.00, 2 => 155.00, 3 => 300.00, 4 => 75.00];
                    $beddenprijs = [1 => 10.00, 2 => 0.00, 3 => 0.00, 4 => 0.00];
                    if(isset($_POST['aantal_dagen']) && $_POST['aantal_personen'] != null) {
                        $aantal_dagen = $_POST['aantal_dagen'];
                        $aantal_personen = $_POST['aantal_personen'];
                        $gekozenhuis = $_POST['gekozen_huis'];
                        $nummerhuis = $gekozen[$gekozenhuis];
                        $bedden = $beddenprijs[$gekozenhuis];
                    }
                ?>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="currentBooking">
                <div class="bookedHome"></div>
                <div class="totalPriceBlock"><div class="mL">Totale prijs &euro;<span class="totalPrice"><?php if(isset($_POST['submit'])){
                if(isset($_POST['beddengoed']) && $_POST['beddengoed'] == "ja"){
                    echo $all = ($nummerhuis * $aantal_dagen) * $aantal_personen + $bedden;
                } elseif (isset($_POST['beddengoed']) && $_POST['beddengoed'] == "nee") {
                    echo $totaal = ($nummerhuis * $aantal_dagen) * $aantal_personen;
                } else {
                    echo "";
                }
            } ?></div></span></div>
             
            </div>
            <div id="mapid"></div>
        </form>
        </div>
        <div class="right">
            <div class="filter-box">
                <form class="filter-form">
                    <div class="form-control">
                        <a href="index.php">Reset Filters</a>
                    </div>
                    <div class="form-control">
                        <label for="ligbad">Ligbad</label>
                        <input type="radio" id="ligbad" name="faciliteiten" value="ligbad" <?php if ($bathIsChecked) echo 'checked' ?>>
                    </div>
                    <div class="form-control">
                        <label for="zwembad">Zwembad</label>
                        <input type="radio" id="zwembad" name="faciliteiten" value="zwembad" <?php if ($poolIsChecked) echo 'checked' ?>>
                    </div>
                    <button type="submit" name="filter_submit">Filter</button>
                </form>
                <div class="homes-box">
                    <?php if (isset($database_gegevens) && $database_gegevens != null) : ?>
                        <?php foreach ($database_gegevens as $huisje) : ?>
                            <h4>
                            <div class="name+descr"><h4><?php echo $huisje['name']; ?></h4>
                            </h4>
                            <p>
                                <?php echo $huisje['description'] ?>
                                <div class="image"><?php echo $huisje['image']; ?></div>
                            </p>
                            
                            <div class="kenmerken">
                                <h6>Kenmerken</h6>
                                <ul>
                                    <?php
                                    if ($huisje['bath_present'] ==  1) {
                                        echo "<li>Er is ligbad!</li>";
                                    }
                                    ?>
                                    <?php
                                    if ($huisje['pool_present'] ==  1) {
                                        echo "<li>Er is zwembad!</li>";
                                    }
                                    ?>
                                </ul>

                            </div>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>
    <footer>
        <div></div>
        <div>copyright Quattro Rentals BV.</div>
        <div></div>

    </footer>
    <script src="js/map_init.js"></script>
    <script>
        // De verschillende markers moeten geplaatst worden. Vul de longitudes en latitudes uit de database hierin
        var coordinates = [
            [52.44902, 4.61001],
            [52.99864, 6.64928],
            [52.30340, 6.36800],
            [50.89720, 5.90979]
        ];

        var bubbleTexts = [
            'IJmuiden Cottage',
            'Assen Bungalow',
            'Espelo Entree',
            'Weustenrade Woning'

        ];
    </script>
    <script src="js/place_markers.js"></script>
</body>

</html>