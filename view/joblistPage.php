<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require "../include/head.php";
    ?>
    <link rel="stylesheet" href="index.css">
</head>


<body>
    <?php include('spinner.php'); ?>
    <?php include('../include/modals.php'); ?>
    <div class="header border-bottom  border-2 border-success mb-3">
        <?php include('../include/purchaseNav.php'); ?>
    </div>
    <div class="container require_approve">
        <div class="row">
            <div class="col-12">
                <h4 class="text-center text-success" for="">Job ทั้งหมด</h4>
            </div>
        </div>
        <div class="row mt-3 px-2 py-2">
            <div class="border border-success border-2" style="overflow-x: auto; max-height: 400px;">
                <table class="table table-hover">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                        <tr class="fs-5 fw-bold">
                            <td class="text-success">Job ID</td>
                            <td class="text-success">Job description</td>
                            <td class="text-success">Prices/Manpower</td>
                            <td class="text-success">Group_machine</td>
                            <td class="text-success">Create_date</td>
                            <td class="text-success">UpdateTime</td>
                            <td class="text-success">Update</td>
                        </tr>
                    </thead>
                    <tbody id="bodyJoblists">

                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="../controller/joblistPage.js"></script>
    <script src="../controller/publicPurchaserPages.js"></script>
</body>

</html>