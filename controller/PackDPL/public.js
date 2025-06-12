$(document).ready(function () {
    // อัพเดทสี Navbar ตาม ID 
    let url = window.location.href;
    let pageName = url.split('/').pop().split('.').shift();
    $(`#${pageName}`).addClass('active');
    let user = localStorage.getItem('user');
    let token = localStorage.getItem('token');
    let type = localStorage.getItem('type');
    switch (type) {
        case "requester":
            $('.requester').show();
            $('.safety').hide();
            $('.vendor').hide();
            break;
        case "safety":
            $('.requester').hide();
            $('.safety').show();
            $('.vendor').hide();
            break;
        case "vendor":
            $('.requester').hide();
            $('.safety').hide();
            $('.vendor').show();
            break;
    }
    let isActive = true; // สถานะของผู้ใช้
    let checkInterval = 10 * 60 * 100;
    async function checkUserActivity() {
        console.log("checkUser");
        if (isActive) {
            await checkUser(user, type, token);
            //ตั้งใหม่เป็น False ถ้า User คลิกภายใน 1 นาทีจะ Active เอง
            isActive = false;
            return;
        }
        isActive = true; // รีเซ็ตสถานะเพื่อตรวจสอบรอบถัดไป
    }
    checkUserActivity();
    // ตั้งเวลาให้เช็คทุก 1 นาที
    setInterval(checkUserActivity, checkInterval);
    // ถ้ามีการโต้ตอบ ให้ตั้งค่า isActive เป็น true
    $(document).on("click ", function () {
        isActive = true;
    });
});

async function checkUser(user, type, token) {
    const url = '../../model/PackDPL/checkUser.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestData = {
        // Request body data
        user, type, token
    };
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(requestData)
    };
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        if (status != 200) {
            window.location.href = '../login.php';
            return;
        }
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    }

}