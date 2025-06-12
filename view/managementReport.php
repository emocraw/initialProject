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
    <div class="container">
        <div class="row mt-3">
            <div class="col-12">
                <h4 class="text-center text-primary" for="">ประวัติรายงานเรียกผู้รับเหมา</h4>
            </div>
        </div>
        <!-- ช่องค้นหาด้านบน -->
        <div class="row mt-3 px-2">
            <div class="col-12">
                <input type="text" id="searchInput" class="form-control" placeholder="ค้นหาข้อมูล..." onkeyup="searchTable()">
            </div>
        </div>
        <div class="row mt-3 px-2 pb-2">
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
                            <th scope="col">ผรม</th>
                            <th scope="col">Status</th>
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
    <script src="../controller/managementReport.js"></script>
    <script src="../controller/public.js"></script>
</body>

</html>