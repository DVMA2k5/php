<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="./assets/font/font-awesome-pro-v6-6.2.0/css/all.min.css">
    <script src="https://fontawesome.com/v6/search" crossorigin="anonymous"></script>
    <title>BMT </title>
</head>

<body>
    <header>

        <div class="header-middle">
            <div class="container">
                <div class="header-middle-left">
                    <div class="header-logo">
                        <a href="" id="logo">
                            <img src="image/logo.png" alt="BMT">
                        </a>

                    </div>
                </div>
                <div class="header-middle-center">
                    <form action="" class="form-search">
                        <span class="search-btn">
                            <a href="">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </a>
                        </span>
                        <input type="text" class="form-search-input" id="searchBox" placeholder="Tìm kiếm xe... "
                            onkeyup="searchProducts()">

                    </form>
                </div>

                <div class="header-middle-right">
                    <ul class="header-middle-right-list">
                        <li class="header-middle-right-item dropdown open">

                            <div class="auth-container">

                                <div class="user-info">
                                    <h1 class="welcome"> <span id="userDisplayName"></span></h1>
                                    <a href="dk.php"><button class="logout-btn" onclick="logout()"
                                            style="font-size: 17px;">Đăng xuất</button></a>
                                </div>

                                <div class="hoadon">
                                    <span class="ravao">Giỏ hàng</span>
                                    <a href="hoadon.php">
                                        <div class="hd">Hóa đơn</div>
                                    </a>
                                    <a href="admin.php">
                                        <div class="hd">Quản lý</div>
                                    </a>

                                </div>
                            </div>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <div class="advanced-search">
        <div class="container">

        </div>
    </div>
    <div class="green-line-header"></div>

    <nav class="header-bottom">
        <div class="container">

        </div>
    </nav>


    <main class="main-wrapper">

        <div class="container" id="trangchu">


            <div class="home-service" id="home-service">

            </div>

            <?php

            // Kết nối CSDL
            $conn = new mysqli("localhost", "root", "", "admindoan");
            if ($conn->connect_error) {
                die("Kết nối thất bại: " . $conn->connect_error);
            }

            // Nhận dữ liệu từ GET
            $tukhoa = isset($_GET['tukhoa']) ? trim($_GET['tukhoa']) : '';
            $brandchecked = isset($_GET['brands']) ? $_GET['brands'] : [];
            $min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
            $max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (int)$_GET['max_price'] : PHP_INT_MAX;

            // Bắt đầu xây dựng câu truy vấn
            $conditions = [];
            if (!empty($tukhoa)) {
                $safe_keyword = $conn->real_escape_string($tukhoa);
                $conditions[] = "(tensp LIKE '%$safe_keyword%' OR dongsp LIKE '%$safe_keyword%' OR mauxe LIKE '%$safe_keyword%')";
            }

            if (!empty($brandchecked)) {
                $escaped_brands = array_map(function ($brand) use ($conn) {
                    return "'" . $conn->real_escape_string($brand) . "'";
                }, $brandchecked);
                $brand_list = implode(",", $escaped_brands);
                $conditions[] = "dongsp IN ($brand_list)";
            }

            // Điều kiện lọc theo giá
            $conditions[] = "giaban BETWEEN $min_price AND $max_price";

            // Gộp các điều kiện lại
            $where_sql = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

            // Câu truy vấn cuối cùng
            $sql = "SELECT * FROM products $where_sql ORDER BY tensp";

            // Thực thi truy vấn
            $result = $conn->query($sql);
            ?>
            <!-- Hiển thị tiêu đề kết quả -->
            <div class="home-title-block" id="home-title">
                <h2 class="home-title">CÓ <span><?= $result ? $result->num_rows : 0 ?></span> KẾT QUẢ TÌM KIẾM</h2>
                <div class="border-line"></div>
            </div>


            <form action="" class="form-search">
                <span class="search-btn">
                    <a href="">
                        <i class="fa-light fa-magnifying-glass"></i>
                    </a>
                </span>
                <input type="text" class="form-search-input" id="searchBox" placeholder="Tìm kiếm xe... "
                    onkeyup="searchProducts()">

            </form>


            <div class="page-nav">
                <ul class="page-nav-list">
                    <div class="filter-row">
                        <div class="filter">
                            <div class="filter-box">
                                <div class="container">
                                    <form action="" method="GET">
                                        <h3 class="advanced-title">Bộ lọc tìm kiếm</h3>
                                        <div class="advanced-search-container">
                                            <!--Lọc theo danh mục sản phẩm-->
                                            <legend class="advanced-search-header">Theo danh mục</legend>

                                            <div class="advanced-search-category">
                                                <?php
                                                // Kết nối CSDL
                                                $conn = mysqli_connect("localhost", "root", "", "admindoan");

                                                // Kiểm tra kết nối
                                                if (!$conn) {
                                                    die("Kết nối thất bại: " . mysqli_connect_error());
                                                }

                                                // Truy vấn danh mục sản phẩm
                                                $brand_query = "SELECT DISTINCT dongsp FROM products";
                                                $brand_query_run = mysqli_query($conn, $brand_query);

                                                // Lưu giá trị đã chọn (nếu có)
                                                $checked = [];
                                                if (isset($_GET['brands'])) {
                                                    $checked = $_GET['brands'];
                                                }

                                                if (mysqli_num_rows($brand_query_run) > 0) {
                                                    while ($brandlist = mysqli_fetch_assoc($brand_query_run)) {
                                                        $brandName = $brandlist['dongsp']; // ✅ sửa chỗ này
                                                        $isChecked = in_array($brandName, $checked) ? 'checked' : '';
                                                ?>
                                                        <div>
                                                            <input type="checkbox" name="brands[]" value="<?= htmlspecialchars($brandName) ?>" <?= $isChecked ?> />
                                                            <?= htmlspecialchars($brandName) ?>
                                                        </div>
                                                <?php
                                                    }
                                                } else {
                                                    echo "<p>Không tìm thấy danh mục sản phẩm.</p>";
                                                }

                                                mysqli_close($conn);
                                                ?>

                                            </div>

                                        </div>
                                        <div class="advanced-search-container">
                                            <!--Lọc theo khoảng giá-->
                                            <legend class="advanced-search-header">Khoảng giá</legend>
                                            <div class="advanced-search-price">

                                                <input type="number" placeholder="₫ TỪ" name="min_price" id="min-price" onchange="searchProducts()" min="0" max="10000000000"
                                                    value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>">

                                                <input type="number" placeholder="₫ ĐẾN" name="max_price" id="max-price" onchange="searchProducts()" min="0" max="10000000000"
                                                    value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">


                                            </div>

                                        </div>
                                        <button id="advanced-price-btn" style="" aria-label="">Áp dụng <i class="fa-light fa-magnifying-glass-dollar"></i></button>
                                        <div class="advanced-search-control" style="padding-top: 15px;">
                                            <button id="sort-ascending" onclick="searchProducts(1)"><i
                                                    class="fa-regular fa-arrow-up-short-wide"></i></button>
                                            <button id="sort-descending" onclick="searchProducts(2)"><i
                                                    class="fa-regular fa-arrow-down-wide-short"></i></button>
                                            <button id="reset-search" onclick="searchProducts(0)"><i
                                                    class="fa-light fa-arrow-rotate-right"></i></button>
                                            <button onclick="closeSearchAdvanced()"><i class="fa-light fa-xmark"></i></button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="product-right" id="product-list">
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '
                                    <div class="card page-1" id="invoiceModal">
                                        <a href="thongtinsp.php?id=' . $row["id"] . '">                                 
                                            <img src="sanpham/' . htmlspecialchars($row["hinhanh"]) . '" alt="' . htmlspecialchars($row["tensp"]) . '">
                                        </a>
                                        <h3>' . htmlspecialchars($row["tensp"]) . '</h3>
                                        <div class="greenSpacer"></div>
                                        <div class="price">' . number_format($row["giaban"], 0, ',', '.') . 'đ</div>
                                        <button type="button" class="mua" onclick="addToCart(\'' . addslashes($row["tensp"]) . '\', ' . $row["giaban"] . ', \'sanpham/' . addslashes($row["hinhanh"]) . '\')">
                                            Thêm vào giỏ hàng
                                        </button>
                                    </div>';
                                }
                            } else {
                                echo "<p>Không tìm thấy sản phẩm nào.</p>";
                            }


                            ?>
                        </div>
                        </tbody>

                    </div>
                </ul>
            </div>
        </div>

    </main>
    <section class="cart">
        <button class="dong"><i class="fa-regular fa-xmark"></i></button>
        <!-- <div>Đóng</div> -->
        <div style="margin-top: 45px;margin-bottom: 20px;">Danh sách mua hàng</div>
        <form action="">
            <table>
                <thead>
                    <tr>
                        <th>
                            Sản phẩm
                        </th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Chọn</th>
                    </tr>
                </thead>
                <tbody>

                    <!-- <td style="display: flex;align-items: center;"><img style ="width: 120px;"src="image/ninja-1000sx.png"alt ="">Ninja</td>
                        <td><p><span>1500</span><sup>đ</sup></p></td>
                         <td><input style="width: 40px; outline: none;" type="number"value ="1"min="1""max="2""></td>
                         <td style="cursor: pointer;">Xóa </td>
                     -->
                </tbody>
            </table>
            <div style="text-align: center;" class="price-total">
                <p style=" font-weight: bold;margin-top: 10px; margin-bottom: 20px;">Tổng tiền:<span>0</span><sup></sup>
                </p>
            </div>
            <a class="thanhtoan" href="thanhtoan.php">Thanh toán</a>
        </form>
    </section>

    <div class="green-line-header"></div>
    <?php include 'footer.php'; ?>


    <script>
        // Lấy tên người dùng từ localStorage
        const loggedInUser = localStorage.getItem('loggedInUser');

        // Kiểm tra nếu có người dùng đã đăng nhập, hiển thị tên
        if (loggedInUser) {
            document.getElementById('userDisplayName').textContent = loggedInUser;
        }

        // Hàm đăng xuất
        function logout() {
            // Xóa thông tin người dùng khỏi localStorage
            localStorage.removeItem('loggedInUser');

            // Chuyển hướng về trang đăng nhập
            window.location.href = 'index.php';
        }
    </script>
    <!-- <script src="js/hoadon.js"></script> -->
    <script src="js/giohang.js"></script>
    <script src="js/phantrang.js"></script>
    <script src="js/ssbutton.js"></script>
    <script src="js/main.js"></script>

</body>

</html>