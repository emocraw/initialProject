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
    <div class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Modal body text goes here.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="header">
        <?php include('../include/navbar.php'); ?>
    </div>
    <div class="container require_approve">
        <div class="row mt-3">
            <div class="col-12">
                <h4 class="text-center text-primary" for="">รายงานใบแจ้งขอพนักงาน</h4>
            </div>
        </div>
        <div class="row mt-3 px-2 py-2">
            <div style="overflow-x: auto; max-height: 400px;">
                <table class="table border border-2">
                    <thead>
                        <tr class="text-center">
                            <th scope="col" style="white-space: nowrap;">ID</th>
                            <th scope="col" style="white-space: nowrap;">เลขที่เอกสาร</th>
                            <th scope="col" style="white-space: nowrap;">เครื่อง</th>
                            <th scope="col" style="white-space: nowrap;">ประเภทงาน</th>
                            <th scope="col" style="white-space: nowrap;">จำนวนที่ต้องการ</th>
                            <th scope="col" style="white-space: nowrap;">เริ่ม</th>
                            <th scope="col" style="white-space: nowrap;">ถึง</th>
                            <th scope="col" style="white-space: nowrap;">ชื่อผรม</th>
                        </tr>
                    </thead>
                    <tbody id="bodyReport">
                        <!-- js -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row px-4">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitModal">
                บันทึกข้อมูล
            </button>
        </div>


        <div class="row mt-3">
            <div class="col-12">
                <h4 class="text-center text-primary" for="">รายงานผรมที่จะเข้าทำงานในแต่ละพื้นที่</h4>
            </div>
        </div>
        <div class="row mt-3 px-2 py-2">
            <div style="overflow-x: auto; max-height: 400px;">
                <table class="table border border-2">
                    <thead>
                        <tr class="text-center">
                            <th scope="col" style="white-space: nowrap;">ID</th>
                            <th scope="col" style="white-space: nowrap;">เลขที่เอกสาร</th>
                            <th scope="col" style="white-space: nowrap;">เครื่อง</th>
                            <th scope="col" style="white-space: nowrap;">ประเภทงาน</th>
                            <th scope="col" style="white-space: nowrap;">จำนวนที่ต้องการ</th>
                            <th scope="col" style="white-space: nowrap;">วันที่</th>
                            <th scope="col" style="white-space: nowrap;">ชื่อผรม</th>
                            <th scope="col" style="white-space: nowrap;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bodyScrap">
                        <tr class="text-center">

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>




    </div>
    <footer>

    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- เชื่อมโยง Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="../controller/vendorManagement.js"></script>
    <script src="../controller/public.js"></script>
</body>


</html>