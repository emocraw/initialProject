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
                <h4 class="text-center text-primary" for="">รายการของที่จะขออนุมัติขาย</h4>
            </div>
            <div class="col-12">
                <h5 class="text-secondary">Doc NO:<span id="docNo"></span></h5>
            </div>
        </div>
        <div class="row mt-3 px-2 py-2">
            <table class="table border border-2">
                <thead>
                    <tr>
                        <th scope="col">Code</th>
                        <th scope="col">Name</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Unit</th>
                        <th scope="col">Price</th>
                        <th scope="col">Vendor</th>
                        <th scope="col">Action</th>
                        <th scope="col">Location</th>
                        <th scope="col">Image</th>
                    </tr>
                </thead>
                <tbody id="bodyAlldoc">
                    <tr>
                        <td scope="row">1</td>
                        <td>Mark</td>
                        <td>Otto</td>
                        <td>b</td>
                        <td>d</td>
                        <td>c</td>
                        <td><input class="form-check-input" type="checkbox" value="" id="flexCheckChecked"></td>
                    </tr>
                    <tr>
                        <td scope="row">2</td>
                        <td>Jacob</td>
                        <td>Thornton</td>
                        <td>@fat</td>
                    </tr>
                    <tr>
                        <td scope="row">3</td>
                        <td colspan="2">Larry the Bird</td>
                        <td>@twitter</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitModal">
                บันทึกข้อมูล
            </button>


        </div>
    </div>
    <footer>

    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../controller/reportReq.js"></script>
</body>

</html>