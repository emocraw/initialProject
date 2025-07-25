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
        <div class="row">
            <div class="col-12">
                <h4 class="text-center text-primary" for="">ใบแจ้งขอพนักงาน</h4>
            </div>
            <div class="col-12 px-4">
                <select id='mcname' class="vendor-select" aria-label="Default select example">
                    <option selected value="">เลือกเครื่องหน้างาน</option>
                    <option value="P05">P05</option>
                    <option value="DPL">DPL</option>
                    <option value="M06">M06</option>
                    <option value="M08">M08</option>
                    <option value="PL3">PL3</option>
                    <option value="PL5">PL5</option>
                </select>
            </div>
        </div>
        <div class="row mt-3 px-2 py-2">
            <div style="overflow-x: auto; max-height: 400px;">
                <table class="table border border-2">
                    <thead>
                        <tr>
                            <th scope="col">แถว</th>
                            <th scope="col">ประเภทงาน</th>
                            <th scope="col">จำนวนที่ต้องการ</th>
                            <th class='text-center' scope="col">เริ่ม</th>
                            <th class='text-center' scope="col">ถึง</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bodyScrap">
                        <!-- <tr>
                            <td class="text-center justify-content-center">1</td>
                            <td>
                                <div class="row">
                                    <div class="col-6">

                                    </div>
                                    <div class="col-6">

                                    </div>
                                </div><select class="form-select" aria-label="Default select example">
                                    <option selected>เลือกทักษะของพนักงาน</option>
                                    <option value="1">งาน One</option>
                                    <option value="2">งาน Two</option>
                                    <option value="3">งาน Three</option>
                                </select>
                            </td>
                            <td> <input type="text" class="form-control" placeholder="ระบุตัวเลขจำนวนคน"
                                    aria-label="Recipient's username" aria-describedby="button-addon2"></td>
                            <td><input type="date" class="form-control"
                                    aria-describedby="button-addon2"></td>
                            <td><input type="date" class="form-control"
                                    aria-describedby="button-addon2"></td>
                            <td> <button class="btn btn-danger">ลบ</button> </td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-column">
                <div class="row mb-3">
                    <div class="col text-end">
                        <button id="addBtn" class="btn btn-success">+</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col text-end">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitModal">
                            บันทึกข้อมูล
                        </button>
                    </div>
                </div>


            </div>

        </div>


        <div class="row mt-3">
            <div class="col-12">
                <h4 class="text-center text-primary" for="">รายงานใบแจ้งขอพนักงาน</h4>
            </div>
        </div>
        <div class="row mt-3 px-2 py-2">
            <div style="overflow-x: auto; max-height: 400px;">
                <table class="table border border-2">
                    <thead>
                        <tr>
                            <th scope="col">แถว</th>
                            <th scope="col">เลขที่เอกสาร</th>
                            <th scope="col">ประเภทงาน</th>
                            <th scope="col">จำนวนที่ต้องการ</th>
                            <th scope="col">เริ่ม</th>
                            <th scope="col">ถึง</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bodyReport">
                        <!-- <tr>
                        <td class="text-center justify-content-center">1</td>
                        <td>xxxxx</td>
                        <td><select class="form-select" aria-label="Default select example">
                                <option selected>เลือกทักษะของพนักงาน</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                        </td>

                        <td> <input type="text" class="form-control" placeholder="ระบุตัวเลขจำนวนคน"
                                aria-label="Recipient's username" aria-describedby="button-addon2"></td>
                        <td><input type="date" class="form-control"
                                aria-describedby="button-addon2"></td>
                        <td><input type="date" class="form-control"
                                aria-describedby="button-addon2"></td>
                        <td> <button class="btn btn-danger">ลบ</button> </td>
                    </tr> -->


                    </tbody>
                </table>

            </div>

        </div>
    </div>
    <footer>

    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="../controller/index.js"></script>
    <script src="../controller/public.js"></script>
</body>

</html>