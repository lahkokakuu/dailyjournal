<?php
include './koneksi.php';
include "upload_foto.php";

if (!isset($_SESSION['username'])) {
    header(header: "location:login.php");
}

$sql = "SELECT * FROM USER WHERE username = '" . $_SESSION['username'] . "'";
$hasil = $conn->query(query: $sql);

while ($row =$hasil->fetch_assoc()){

?>
<form method="post" action="" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $row["id"] ?>">
    <div class="mb-3">
        <label for="formgroupexampleinput" class="form-label">Ganti Password</label>
        <input type="text" class="from-control" name="gantipassword" placeholder="Tuliskan Password Baru">
    </div>
    <div class=""mb-3>
        <label for="formgroupwxampleinput" class="form-label">Gambar</label>
        <input type="file" class="form-control" name="gambar">
    </div>
    <div class="mb-3">
        <p>Profile Saat Ini</p>
        <?php
        if ($row['foto'] !='') {
            if (file_exists(filename:'img/' . $row['foto'] .'')) {
                ?>
                <img src="img/<?= $row["foto"] ?>" width="100">
                <?php
            }
        }
        ?>
        <input type="hidden" name="gambar_lama" value="<?= $row["foto"] ?>">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dimiss="modal">Close</button>
        <input type="submit" value="simpan" name="simpan" class="btn btn-primary">
    </div>
</form>
<?php
}

if (isset($_POST['simpan'])) {
    $gantipassword = $_POST['gantipassword'];
    $passwordResult = md5($gantipassword);
    $gambar = '';
    $nama_gambar = $_FILES['gambar']['name'];

    if ($nama_gambar != '') {
        $cek_upload = upload_foto($_FILES["gambar"]);

        if ($cek_upload['status']) {
            $gambar = $cek_upload['message'];
        } else {
            echo "<script>
                alert('" . $cek_upload['message'] . "');
                document.location='admin.php?page=gallery';
            </script>";
            die;
        }
    }

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        if ($nama_gambar == '') {
            $gambar = $_POST['gambar_lama'];
        } else {
            unlink("../assets/images/" . $_POST['gambar_lama']);
        }

        $sql = "UPDATE user SET password = ?, foto = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $passwordResult, $gambar, $id);
        $simpan = $stmt->execute();

    }

    if ($simpan) {
        echo "<script>
            alert('Simpan data sukses');
            document.location='admin.php?page=profile';
        </script>";
    } else {
        echo "<script>
            alert('Simpan data gagal');
            document.location='admin.php?page=profile';
        </script>";
    }


    $stmt->close();
    $conn->close();
}

?>
