<nav>
    <nav class="navbar navbar-expand-lg bg-body-tertiary mb-3">
        <div class="container-fluid px-3">
            <a class="navbar-brand" href="#">
                <img src="../assets/SHERA_Shopfloor.png" alt="Logo" width="160vh" height="auto" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- เมนูฝั่งซ้าย -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- Roll Requester -->
                    <li class="nav-item requester">
                        <a id='index' class="nav-link" href="index.php">ใบแจ้งขอพนักงาน</a>
                    </li>
                    <li class="nav-item requester">
                        <a id='empManagement' class="nav-link" href="empManagement.php">จัดการพนักงาน</a>
                    </li>
                    <!-- <li class="nav-item requester">
                        <a id='createWorking' class="nav-link" href="http://localhost/working_allocate/createWorking.php">ส่งพนักงานไปทำงาน</a>
                    </li> -->
                    <li class="nav-item requester">
                        <a id='reportCheckIn' class="nav-link" href="reportCheckIn.php">รายงานการมาทำงานของพนักงานรายวัน</a>
                    </li>
                    <!-- Roll VendorManagement -->
                    <li class="nav-item safety">
                        <a id='vendorManagement' class="nav-link" href="vendorManagement.php">จัดการผู้รับเหมา</a>
                    </li>
                    <li class="nav-item safety">
                        <a id='managementReport' class="nav-link" href="managementReport.php">ประวัติการเรียกเข้า</a>
                    </li>
                    <!-- Roll workerManagement -->
                    <li class="nav-item vendor">
                        <a id='workerManagement' class="nav-link" href="workerManagement.php">จัดการพนักงาน</a>
                    </li>
                </ul>

                <!-- เมนูฝั่งขวา -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <div class="btn btn-danger">Log out</div>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </nav>
</nav>