<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require "../include/head.php";
    ?>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg" style="width: 100%; max-width: 400px;">
            <h3 class="text-center mb-4">Login</h3>
            <img class="img-fluid mb-3" src="../assets/thumbnail_image001.png" alt="Logo">

            <div class="mb-3">
                <label id='vendorName' for="username" class="form-label">Vendor code</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <button id='findVendor' class="btn btn-warning w-100 mt-3">ค้นหา</button>
            </div>
            <div class="mb-3" id="pswDiv">
                <label for="password" class="form-label">รหัสผ่าน</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <button id='logInBtn' class="btn btn-primary w-100 mt-3">เข้าระบบ</button>
            </div>
            <div class="mb-3" id="newPsw">
                <label for="newpassword" class="form-label">กำหนดรหัสผ่านใหม่</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="newpassword" name="newpassword" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="newpassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <label for="newpassword2" class="form-label">ระบุอีกครั้ง</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="newpassword2" name="newpassword2" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="newpassword2">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <button id="confirmNewPassword" class="btn btn-primary w-100 mt-3">ยืนยัน</button>
            </div>

            <div class="mt-3 text-danger" id="error-msg" style="display: none;">Invalid credentials!</div>
            <div class="mt-3 text-success" id="success-msg" style="display: none;">valid credentials!</div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <script src="../controller/loginVendor.js"></script>
</body>

</html>