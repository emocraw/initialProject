 <!-- Submit Modal -->
 <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">ยืนยันการบันทึก</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 ยืนยันการบันทึกข้อมูล ?
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 <button id="btnSubmit" type="button" class="btn btn-primary">ยืนยัน</button>
             </div>
         </div>
     </div>
 </div>
 <!-- Cancel Modal -->
 <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="examplecancelModal" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="examplecancelModal">ยืนยันการบันทึก</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body text-danger">
                 ยืนยันการยกเลิกข้อมูล ?
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 <button id="btnCancel" type="button" class="btn btn-primary">ยืนยัน</button>
             </div>
         </div>
     </div>
 </div>

 <!-- Emps Modal -->
 <div class="modal fade" id="empModal" tabindex="-1" aria-labelledby="exampleempModal" aria-hidden="true">
     <div class="modal-dialog modal-dialog-scrollable">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">เลือกพนักงาน</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div class="d-flex flex-column">
                     <label for="">ไปทำงานที่: <span class="primary" id='machineShera'></span></label>
                     <label for="">งาน: <span class="text-warning" id='workType'></span></label>
                     <label for="">จำนวน: <span class="text-warning" id='request_qty'></span></label>
                     <label for="">จำนวนที่เลือก: <span class="text-warning" id='qty_pick'></span></label>
                     <label for="">เลขที่เอกสาร: <span class="text-success" id='doc'></span></label>
                     <div id='divEmps'>

                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 <button id="confirmEmp" type="button" class="btn btn-primary">ยืนยัน</button>
             </div>
         </div>
     </div>
 </div>
 <!-- Edit job modal -->
 <div class="modal fade" id="editJobModal" tabindex="-1" aria-labelledby="exampleempModal" aria-hidden="true">
     <div class="modal-dialog modal-dialog-scrollable">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title text-success" id="exampleModalLabel">Edit Job Detail</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div class="d-flex flex-column">
                     <label for="">Job ID: <span class="text-primary" id="jobIdModal">XXX</span></label>
                     <label for="">Job desc: <input id="jobdescModal" class="form-control text-danger" type="text"></label>
                     <label for="">Price: <input id="priceModal" class="form-control text-danger" type="text"></span></label>
                     <label for="">กลุ่มงาน</label>
                     <select id="groupMachineModal" class="form-select">

                     </select>
                     <div id='divEmps'>

                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 <button id="editJobBtn" type="button" class="btn btn-primary">Update</button>
             </div>
         </div>
     </div>
 </div>
 <!-- End Edit job modal -->
 <!-- Change Emps Modal -->
 <div class="modal fade" id="changeempModal" tabindex="-1" aria-labelledby="exampleempModal" aria-hidden="true">
     <div class="modal-dialog modal-dialog-scrollable">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">เลือกพนักงาน</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div class="d-flex flex-column">
                     <div id='ChangedivEmps'>

                     </div>
                 </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                 <button id="confirmChangeEmp" type="button" class="btn btn-primary">ยืนยัน</button>
             </div>
         </div>
     </div>
 </div>