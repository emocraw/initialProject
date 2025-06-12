$(document).ready(function () {
    // อัพเดทสี Navbar ตาม ID 
    let url = window.location.href;
    let pageName = url.split('/').pop().split('.').shift();
    $(`#${pageName}`).addClass('active');
    let type = localStorage.getItem('type');
    if (type != 'purchaser') {
        window.location.href = 'login.php';
        return;
    }
    let isActive = true; // สถานะของผู้ใช้
    let secInterval = 60000;
    let inactivityLimit = 30; // 30 minutes
    let inactivityCounter = 0; // Counter for inactivity

    async function checkUserActivity() {
        console.log("checkUserActivity");
        if (isActive) {
            // Reset inactivity counter if user is active
            inactivityCounter = 0;
            isActive = false;
            return;
        }
        inactivityCounter++; // Increment inactivity counter
        if (inactivityCounter >= inactivityLimit) {
            window.location.href = 'login.php';
            return;
        }
    }
    checkUserActivity();
    // ตั้งเวลาให้เช็คทุก 1 นาที
    setInterval(checkUserActivity, secInterval);
    // ถ้ามีการโต้ตอบ ให้ตั้งค่า isActive เป็น true
    $(document).on("click", function () {
        isActive = true;
    });
});
