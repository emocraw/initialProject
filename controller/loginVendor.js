$(document).ready(app);
async function app() {
    $('.overlay').hide();
    $('#pswDiv').hide();
    $('#newPsw').hide();
}
$('#logInBtn').on('click', login);
$('#findVendor').on('click', findVendor);
$('#confirmNewPassword').on('click', createNewPassword);
// Function to create new password
async function createNewPassword() {
    let newPassword = $('#newpassword').val();
    let newPassword2 = $('#newpassword2').val();
    let vendorCode = $('#username').val();
    // Check if passwords match
    if (newPassword === newPassword2) {
        // Do something with the new password (e.g., send to server or show success message)
        const url = '../model/createNewPassword.php';
        const headers = new Headers();
        headers.append('Content-Type', 'application/json');
        const requestData = {
            newPassword,
            vendorCode
        };
        const requestOptions = {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(requestData)
        };
        console.log(JSON.stringify(requestData));
        try {
            const response = await fetch(url, requestOptions);
            const status = response.status;
            const data = await response.json();
            console.log(data);
            if (status != 200) {
                $('#error-msg').text(data.message);
                $('#error-msg').show();
                $('.overlay').hide();
                return;
            }
            $('#error-msg').hide();
            $('#success-msg').text('สร้างรหัสใหม่เรียบร้อยแล้ว');
            $('#success-msg').show();
            $('#newPsw').hide();
            $('#pswDiv').show();
        } catch (error) {
            // Handle any errors
            console.log('Error:', error);
        }
    } else {
        // Show an error if passwords do not match
        $('#error-msg').text("รหัสผ่านไม่ตรงกัน");
        $('#error-msg').show();
    }
}
$(".toggle-password").click(function () {
    let target = $(this).data("target"); // ดึงค่า ID ของ input
    let input = $("#" + target);
    let icon = $(this).find("i");
    if (input.attr("type") === "password") {
        input.attr("type", "text");
        icon.removeClass("fa-eye").addClass("fa-eye-slash");
    } else {
        input.attr("type", "password");
        icon.removeClass("fa-eye-slash").addClass("fa-eye");
    }
});
async function findVendor() {
    $('#findVendor').hide();
    let vendorCode = $('#username').val();
    $('.overlay').show();
    const url = '../model/findVendor.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestData = {
        // Request body data
        vendorCode
    };
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(requestData)
    };
    console.log(JSON.stringify(requestData));
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        console.log(data);
        if (status != 200) {
            $('#error-msg').text(data.message);
            $('#error-msg').show();
            $('#findVendor').show();
            $('#pswDiv').hide();
            $('#vendorName').text("Vendor code");
            $('.overlay').hide();
            return;
        }

        if (data.password_status == "มี") {
            $('#pswDiv').show();
            $('#vendorName').text(data.VendorName);
            return;
        } else {
            $('#newPsw').show();
            $('#vendorName').text(data.VendorName);
        }

    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    }
}
async function login() {
    $('.overlay').show();
    localStorage.clear();
    const url = '../model/loginVendor.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    let username = $('#username').val();
    let password = $('#password').val();
    const requestData = {
        // Request body data
        username,
        password
    };
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(requestData)
    };
    console.log(JSON.stringify(requestData));
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        if (status != 200) {
            $('#error-msg').show();
            $('.overlay').hide();
            return;
        }
        localStorage.setItem('token', data.UserInfo.Token);
        localStorage.setItem('type', data.UserInfo.type)
        localStorage.setItem('user', data.UserInfo.ID)
        localStorage.setItem('vendorName', data.UserInfo.Name_Surname)
        window.location.href = 'workerManagement.php';
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    }
}