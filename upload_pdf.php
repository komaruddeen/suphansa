<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <title>Basic Upload pdf file PHP PDO by devbanban.com 2021</title>
    <!-- sweet alert  -->
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10"> <br>
                <h3>อัพโหลดเอกสารสมัครงาน</h3>
                <form action="" method="post" enctype="multipart/form-data">
                    <font color="red">*ตัวอย่าง --> Printer </font>
                    <input type="text" name="doc_position" required class="form-control" placeholder="ตำแหน่ง"> <br>

                    <font color="red">*ตัวอย่าง --> Mr.Somchai Wandee </font>
                    <input type="text" name="doc_name" required class="form-control" placeholder="ชื่อ-สกุล"> <br>

                    <font color="red">*ตัวอย่าง --> 086xxxxxxx </font>
                    <input type="text" name="doc_tel" required class="form-control" placeholder="เบอร์โทร"> <br>

                    <font color="red">*อัพโหลดได้เฉพาะ .pdf เท่านั้น </font>
                    <input type="file" name="doc_file" required class="form-control" accept="application/pdf"> <br>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
                <h3>รายการเอกสาร </h3>
                <table class="table table-striped  table-hover table-responsive table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">ลำดับ</th>
                            <th width="30%">ตำแหน่ง</th>
                            <th width="30%">ชื่อเอกสาร</th>
                            <th width="25%">เบอร์โทร</th>
                            <th width="10%">เปิดดู</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        //คิวรี่ข้อมูลมาแสดงในตาราง
                        require_once 'connect.php';
                        $stmt = $conn->prepare("SELECT* FROM tbl_pdf");
                        $stmt->execute();
                        $result = $stmt->fetchAll();
                        foreach ($result as $row) {
                        ?>
                            <tr>
                                <td><?= $row['no']; ?></td>
                                <td><?= $row['doc_position']; ?></td>
                                <td><?= $row['doc_name']; ?></td>
                                <td><?= $row['doc_tel']; ?></td>
                                <td><a href="docs/<?php echo $row['doc_file']; ?>" target="_blank" class="btn btn-info btn-sm"> เปิดดู </a></td>
                            <?php } ?>
                    </tbody>
                </table>
                <br>

            </div>
        </div>
    </div>
</body>

</html>

<?php

if (isset($_POST['doc_name'])) {
    require_once 'connect.php';
    //สร้างตัวแปรวันที่เพื่อเอาไปตั้งชื่อไฟล์ใหม่
    $date1 = date("Ymd_His");
    //สร้างตัวแปรสุ่มตัวเลขเพื่อเอาไปตั้งชื่อไฟล์ที่อัพโหลดไม่ให้ชื่อไฟล์ซ้ำกัน
    $numrand = (mt_rand());
    $doc_file = (isset($_POST['doc_file']) ? $_POST['doc_file'] : '');
    $upload = $_FILES['doc_file']['name'];

    //มีการอัพโหลดไฟล์
    if ($upload != '') {
        //ตัดขื่อเอาเฉพาะนามสกุล
        $typefile = strrchr($_FILES['doc_file']['name'], ".");

        //สร้างเงื่อนไขตรวจสอบนามสกุลของไฟล์ที่อัพโหลดเข้ามา
        if ($typefile == '.pdf') {

            //โฟลเดอร์ที่เก็บไฟล์ **สร้างไฟล์ index.php หรือ index.html (ไม่ต้องมี code) ไว้ในโฟลเดอร์ด้วยนะครับจะได้ป้องกันการเข้าถึงทุกไฟล์ในโฟลเดอร์
            $path = "docs/";
            //ตั้งชื่อไฟล์ใหม่เป็น สุ่มตัวเลข+วันที่
            $newname = 'doc_' . $numrand . $date1 . $typefile;
            $path_copy = $path . $newname;
            //คัดลอกไฟล์ไปยังโฟลเดอร์
            move_uploaded_file($_FILES['doc_file']['tmp_name'], $path_copy);

            //ประกาศตัวแปรรับค่าจากฟอร์ม
            $doc_name = $_POST['doc_name'];
            $doc_position = $_POST['doc_position'];
            $doc_tel = $_POST['doc_tel'];

            //sql insert
            $stmt = $conn->prepare("INSERT INTO tbl_pdf (doc_name, doc_file , doc_position , doc_tel)
    VALUES (:doc_name, '$newname',:doc_position ,:doc_tel)");
            $stmt->bindParam(':doc_name', $doc_name, PDO::PARAM_STR);
            $stmt->bindParam(':doc_position', $doc_position, PDO::PARAM_STR);
            $stmt->bindParam(':doc_tel', $doc_tel, PDO::PARAM_STR);
            $result = $stmt->execute();
            $conn = null; //close connect db
            //เงื่อนไขตรวจสอบการเพิ่มข้อมูล
            if ($result) {
                echo '<script>
                     setTimeout(function() {
                      swal({
                          title: "อัพโหลดไฟล์เอกสารสำเร็จ",
                          type: "success"
                      }, function() {
                          window.location = "upload_pdf.php"; //หน้าที่ต้องการให้กระโดดไป
                      });
                    }, 1000);
                </script>';
            } else {
                echo '<script>
                     setTimeout(function() {
                      swal({
                          title: "เกิดข้อผิดพลาด",
                          type: "error"
                      }, function() {
                          window.location = "upload_pdf.php"; //หน้าที่ต้องการให้กระโดดไป
                      });
                    }, 1000);
                </script>';
            } //else ของ if result


        } else { //ถ้าไฟล์ที่อัพโหลดไม่ตรงตามที่กำหนด
            echo '<script>
                         setTimeout(function() {
                          swal({
                              title: "คุณอัพโหลดไฟล์ไม่ถูกต้อง",
                              type: "error"
                          }, function() {
                              window.location = "upload_pdf.php"; //หน้าที่ต้องการให้กระโดดไป
                          });
                        }, 1000);
                    </script>';
        } //else ของเช็คนามสกุลไฟล์

    } // if($upload !='') {

} //isset
?>