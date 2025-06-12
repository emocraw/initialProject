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
                <h4 class="text-center text-primary" for="">รายงานการ Check in รายวัน</h4>
            </div>
            <div class="col-md-6 mb-3">
                <label for="">วันที่</label>
                <input id='dateInput' type="date" class="form-control" aria-describedby="button-addon2">
            </div>
            <div class="col-md-3 mb-3 d-flex align-items-end">
                <select id='mcname' class="vendor-select form-select" aria-label="Default select example">
                    <option selected>เลือกเครื่องหน้างาน</option>
                    <option value="All">All</option>
                    <option value="DPL">DPL</option>
                    <option value="P01">P01</option>
                    <option value="P02">P02</option>
                    <option value="P03">P03</option>
                    <option value="P04">P04</option>
                    <option value="P05">P05</option>
                    <option value="P06">P09</option>
                    <option value="P07">P07</option>
                    <option value="P08">P08</option>
                    <option value="P09">P09</option>
                    <option value="P10">P10</option>
                    <option value="P11">P11</option>
                    <option value="PL1">PL1</option>
                    <option value="PL3">PL3</option>
                    <option value="PL4">PL4</option>
                    <option value="PL5">PL5</option>

                </select>
            </div>
            <div class="col-md-3 mb-3 d-flex align-items-end justify-content-end">
                <button id='searchBtn' class="btn btn-warning">search</button>
            </div>

            <div class="col-12">
                <div class="d-flex flex-column">
                    <label for=""><small class="text-danger">***ดึงข้อมูลจากวันที่ Check in -1 day 19:00 ถึง check out +1 day 01:00***</small></label>
                    <label for="">จำนวนคนที่มาทำงานวันนี้ <span class="text-primary" id="reqQty">คน</label>
                </div>
            </div>
        </div>
        <div class="row mt-3 px-2 py-2">
            <div style="overflow-x: auto; max-height: 400px;" class="border border-2">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">แถว</th>
                            <th scope="col">ประเภทงาน</th>
                            <th scope="col">ชื่อ-นามสกุล</th>
                            <th scope="col">บริษัท</th>
                            <th scope="col">ทำงานที่</th>
                            <th scope="col">Check in</th>
                            <th scope="col">Check out</th>
                        </tr>
                    </thead>
                    <tbody id="bodyLoginLog">
                        <!-- <tr>
                            <td class="text-center justify-content-center">1</td>
                            <td><select class="form-select" aria-label="Default select example">
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
        </div>
    </div>
    <footer>

    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="../controller/reportCheckInV4.js"></script>
    <script src="../controller/public.js"></script>
</body>

</html>