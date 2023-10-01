<form>
    <div>Name:</div>
    <input type="text" name="personName"/>
    <div>Age:</div>
    <input type="number" name="age"/>
    <div>Town:</div>
    <select name="townId">
        <option value="10">Sofia</option>
        <option value="20">Varna</option>
        <option value="30">Plodvid
    </select>
    <div>
        <input type="submit"/></div>
</form>
<?php
if (isset($_GET['personName'])) {
    $name = $_GET['personName'];
    $age = $_GET['age'];
    $townId = $_GET['townId'];
    $town = ($townId === '10') ? 'Sofia' : (($townId === '20') ? 'Varna' : (($townId === '30') ? 'Plovdiv' : 'Unknown'));


    echo 'Hello, my first name is ' . $name . ', my age is ' . $age . ', and my town is ' . $town;
}
?>
