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
                <h4 class="text-center text-primary" for="">จัดการส่งพนักงานไปทำงาน</h4>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <input id="dateInput" type="date" class="form-control" placeholder="วันที่" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <div class="col-md-6">
                <select id='mcname' class="vendor-select form-select" aria-label="Default select example">
                    <option selected value="">เลือกเครื่องหน้างาน</option>
                    <option value="PMN">Pack MN</option>
                    <option value="PAUTO">Pack Auto</option>
                    <option value="DPL">DPL</option>
                    <option value="PL1">PL1</option>
                    <option value="PL3">PL3</option>
                    <option value="PL5">PL5</option>
                    <option value="MTN1">MTN1</option>
                    <option value="MTN1">MTN1</option>
                    <option value="MNT3">MNT3</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <select id='workTypeEmp' class="vendor-select form-select" aria-label="Default select example">
                    <option selected value="">งานปกติ</option>
                    <option value="Special">Special</option>
                </select>
            </div>
            <div class="col">
                <button id="search" type="button" class="btn btn-warning">
                    Search
                </button>
            </div>
        </div>

        <div class="row mt-3 px-2">
            <label class="text-danger" for="">***เลือกงานแล้วติ๊กถูกที่ต้องการเพื่อกำหนดงานให้กับผรมทุกคนไม่งั้นโปรแกรมจะไม่นำไปคำนวนค่าแรง</label>
            <div class="col-md-6">
                <select id='shift' class="vendor-select form-select" aria-label="Default select example">

                </select>
            </div>
            <div class="col-md-6">
                <select id='workGroup' class="vendor-select form-select" aria-label="Default select example">
                </select>
            </div>

        </div>
        <div id="table_normal" class="row mt-3 px-2 py-2">
            <div>จำนวนคน: <span id="rowCount">0</span></div>
            <div style="overflow-x: auto; max-height: 400px;">
                <table class="table border border-2">
                    <thead>
                        <tr class="text-center">
                            <th scope="col" style="white-space: nowrap;">ID Card</th>
                            <th scope="col" style="white-space: nowrap;">ชื่อ-นามสกุล</th>
                            <th scope="col" style="white-space: nowrap;">สถาณที่ปฏิบัติงาน</th>
                            <th scope="col" style="white-space: nowrap;">งาน</th>
                            <th scope="col" style="white-space: nowrap;">เวลาเข้า</th>
                            <th scope="col" style="white-space: nowrap;">เวลาออก</th>
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

        <div id="table_special" class="row mt-3 px-2 py-2">
            <div>จำนวนคน: <span id="rowCount">0</span></div>
            <div style="overflow-x: auto; max-height: 400px;">
                <table class="table border border-2">
                    <thead>
                        <tr class="text-center">
                            <th scope="col" style="white-space: nowrap;">ID Card</th>
                            <th scope="col" style="white-space: nowrap;">ชื่อ-นามสกุล</th>
                            <th scope="col" style="white-space: nowrap;">สถาณที่ปฏิบัติงาน</th>
                            <th scope="col" style="white-space: nowrap;">งาน</th>
                            <th scope="col" style="white-space: nowrap;">ส่งไปที่</th>
                            <th scope="col" style="white-space: nowrap;">เวลาเข้า</th>
                            <th scope="col" style="white-space: nowrap;">เวลาออก</th>
                            <th scope="col" style="white-space: nowrap;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bodySpecial">
                        <tr class="text-center">

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="row px-2">
            <div class="col">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitModal">
                    บันทึกข้อมูล
                </button>
            </div>
            <!-- Button trigger modal -->
        </div>
    </div>
    <footer>

    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="../controller/empManagementV5.js?v=1.0"></script>
    <script src="../controller/public.js"></script>
</body>


</html>