<?php require   APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-3">

    <div class="row">

        <div class="col-1"></div>
        <div class="col-10 text-center text-danger">        
            <h3><?= $data['title']; ?></h3>
        </div>
        <div class="col-1"></div>
    </div>
    <!-- begin tabel -->
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Nettowaarde</th>
                        <th>Land</th>
                        <th>Mobiel</th>
                        <th>Leeftijd</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['zangeressen'] as $zangeres) : ?>
                        <tr>
                            <td><?= $zangeres->Naam; ?></td>
                            <td><?= $zangeres->Nettowaarde; ?></td>
                            <td><?= $zangeres->Land; ?></td>
                            <td><?= $zangeres->Mobiel; ?></td>
                            <td><?= $zangeres->Leeftijd; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <a href="<?= URLROOT; ?>/homepages/index"><i class="bi bi-arrow-left"></i></a>
        </div>
        <div class="col-1"></div>
    </div>
    <!-- eind tabel -->

<?php require   APPROOT . '/views/includes/footer.php'; ?>