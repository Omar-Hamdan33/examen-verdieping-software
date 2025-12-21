<form action="/reservations/update" method="POST">
    <input type="hidden" name="id" value="<?= htmlspecialchars($reservation['id']) ?>">

    <label>Pakket ID:</label>
    <input type="number" name="package_id" value="<?= $reservation['package_id'] ?>" required>

    <label>Locatie ID:</label>
    <input type="number" name="location_id" value="<?= $reservation['location_id'] ?>" required>

    <label>Aantal personen:</label>
    <input type="number" name="aantal" value="<?= $reservation['aantal'] ?>" required>

    <label>Duo voornaam:</label>
    <input type="text" name="duo_voornaam" value="<?= $reservation['duo_voornaam'] ?>">

    <label>Duo tussenvoegsel:</label>
    <input type="text" name="duo_tussenvoegsel" value="<?= $reservation['duo_tussenvoegsel'] ?>">

    <label>Duo achternaam:</label>
    <input type="text" name="duo_achternaam" value="<?= $reservation['duo_achternaam'] ?>">

    <label>Duo geboortedatum:</label>
    <input type="date" name="duo_geboortedatum" value="<?= $reservation['duo_geboortedatum'] ?>">

    <label>Betaald:</label>
    <input type="checkbox" name="betaald" <?= $reservation['betaald'] ? 'checked' : '' ?>>

    <label>Definitief:</label>
    <input type="checkbox" name="definitief" <?= $reservation['definitief'] ? 'checked' : '' ?>>

    <button type="submit">Opslaan</button>
</form>
