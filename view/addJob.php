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
                <h3 class="text-center text-success" for="">เพิ่มงาน</h3>
            </div>
        </div>
        <div class="row px-2 py-2">
            <div class="col-md-6">
                <label class="text-success" for="">ชื่องาน</label>
                <input id="jobName" class="form-control border border-success" placeholder="Job name" type="text">
            </div>
            <div class="col-md-6">
                <label class="text-success" for="">ราคาต่อวัน</label>
                <input id='price' class="form-control border border-success text-success" placeholder="Job name" type="text">
            </div>
            <div class="col-md-6">
                <label class="text-success" for="">กลุ่มงาน</label>
                <select id='workGroup' class="vendor-select border border-success form-select" aria-label="Default select example">
                </select>
            </div>
            <div class="col-md-12 mt-3">
                <div id="submitBtn" class="btn btn-outline-success">บันทึก</div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="../controller/addJob.js"></script>
    <script src="../controller/publicPurchaserPages.js"></script>
</body>

</html>