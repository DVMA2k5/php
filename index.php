<?php
session_start();
include('database.php');
include('toast.php');
if (isset($_SESSION['success'])) {
    echo '<script>alert("' . $_SESSION['success'] . '");</script>';
    unset($_SESSION['success']); // Xóa thông báo sau khi hiển thị
}

// Xử lý đăng xuất
if (isset($_GET['logout'])) {

    session_unset();
    session_destroy();

    header("Location: index.php");
    exit;
}
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : null;
$customer_id = isset($_SESSION["customer_id"]) ? $_SESSION["customer_id"] : null;
$total_price=0;
$role = null;


if ($username && !$customer_id) {
    $stmt = $conn->prepare("SELECT id, role FROM customer WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($customer_id, $role);
    if ($stmt->fetch()) {
        $_SESSION["customer_id"] = $customer_id;
        $_SESSION["role"] = $role;
    }
    $stmt->close();
} else {
    $role = isset($_SESSION["role"]) ? $_SESSION["role"] : null;
}

?>
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
    <style>
    
        .display {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
    </style>
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
    <!--tìm kiếm-->
                
                    <form action="timkiem.php" method="GET" class="form-search">
                        <span class="search-btn">
                            <button type="submit">
                                <i class="fa-light fa-magnifying-glass"></i>
                            </button>
                        </span>
                        <input type="text" name="tukhoa" class="form-search-input" id="searchBox" placeholder="Tìm kiếm xe... "
                            onkeyup="searchProducts()">
                        <button class="filter-btn">
                            <i class="fa-light fa-filter-list"></i>
                            <span style="font-size: 14px;">Lọc</span>
                        </button>
                    </form>
                </div>
                <div class="header-middle-right">
                    <ul class="header-middle-right-list">
                        <li class="header-middle-right-item open">
                            <i class="fa-light fa-user" style="color:#139b3a"></i>

                            <div class="auth-container">
                                <div class="user-info">
                                    <div class="dropdownb">
                                        <h1 class="welcome">
                                            <div class="drip" style="display:flex;flex-direction:column">

                                                <!-- Dòng trên -->
                                                <span style="font-size:12px">
                                                    <?php
                                                    if (isset($_SESSION['customer_id'])) {
                                                        echo "Tài khoản";
                                                    } else {
                                                        echo "Đăng nhập/ Đăng ký";
                                                    }
                                                    ?>
                                                </span>

                                                <!-- Dòng dưới -->

                                                <span class="dropdownb-toggle" style="color:green;font-size:17px">


                                                    <?php
                                                    if (isset($_SESSION['customer_id'])) {
                                                        // Lấy tên khách hàng
                                                        $stmt = $conn->prepare("SELECT name FROM customer WHERE id = ?");
                                                        $stmt->bind_param("i", $_SESSION['customer_id']);
                                                        $stmt->execute();
                                                        $stmt->bind_result($name);
                                                        if ($stmt->fetch()) {
                                                            echo htmlspecialchars($name);
                                                        } else {
                                                            echo "Tài khoản";
                                                        }
                                                        $stmt->close();
                                                    } else {
                                                        echo "Tài khoản";
                                                    }
                                                    ?>
                                                    <i class="fa-sharp fa-solid fa-caret-down" style="font-size:12px;"></i>
                                                </span>
                                            </div>
                                        </h1>

                                        <!-- Dropdown -->
                                        <div class="dropdownb-menu">
                                            <?php if (isset($_SESSION['customer_id'])): ?>
                                                <a href="thongtintk.php">
                                                    <div class="hd"><i class="fa-light fa-circle-user" style="font-size:20px"></i> Tài khoản của tôi</div>
                                                </a>
                                                <a href="hoadon.php">
                                                    <div class="hd"><i class="fa-regular fa-bags-shopping" style="font-size:20px"></i> Đơn hàng đã mua</div>
                                                </a>
                                                <a href="index.php?logout=1" onclick="return confirm('Bạn có muốn đăng xuất?')">
                                                    <div class="hd"><i class="fa-light fa-right-from-bracket" style="font-size:20px"></i> Đăng xuất</div>
                                                </a>
                                                <?php if ($role === 'admin'): ?>
                                                    <a href="dnurl.php">
                                                        <div class="hd"><i class="fa-light fa-gear" style="font-size:20px"></i> Quản lý</div>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="dn.php">
                                                    <div class="hd"><i class="fa-light fa-right-to-bracket" style="font-size:20px;color:green"></i> Đăng nhập</div>
                                                </a>
                                                <a href="dk.php">
                                                    <div class="hd"><i class="fa-light fa-user-plus" style="font-size:20px;color:green"></i> Đăng ký</div>
                                                </a>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>

                                <div class="hoadon">
                                    <span class="ravao">
                                        <i class="fa-light fa-basket-shopping"></i> Giỏ hàng
                                    </span>
                                </div>

                            </div>
                        </li>
                    </ul>
                </div>


            </div>
        </div>
    </header>
    <div class="advanced-search">
        <div class="container">
            <div class="advanced-search-category">
                <span>Phân loại </span>
                <select name="" id="advanced-search-category-select" onchange="searchProducts()">
                    <option>Tất cả</option>
                    <option>Dòng Ninja</option>
                    <option>Dòng Z</option>
                    <option>Dòng KLX</option>
                </select>
            </div>
            <div class="advanced-search-price">
                <span>Giá từ</span>
                <input type="number" placeholder="tối thiểu" id="min-price" onchange="searchProducts()">
                <span>đến</span>
                <input type="number" placeholder="tối đa" id="max-price" onchange="searchProducts()">
                <button id="advanced-search-price-btn"><a href=""><i class="fa-light fa-magnifying-glass-dollar"></i></a></button>
            </div>
            <div class="advanced-search-control">
                <button id="sort-ascending" onclick="searchProducts(1)"><i
                        class="fa-regular fa-arrow-up-short-wide"></i></button>
                <button id="sort-descending" onclick="searchProducts(2)"><i
                        class="fa-regular fa-arrow-down-wide-short"></i></button>
                <button id="reset-search" onclick="searchProducts(0)"><i
                        class="fa-light fa-arrow-rotate-right"></i></button>
                <button onclick="closeSearchAdvanced()"><i class="fa-light fa-xmark"></i></button>
            </div>
        </div>
    </div>
    <div class="green-line-header"></div>

    <nav class="header-bottom">
        <div class="container">
            <ul class="menu-list">
                <li class="menu-list-item"><a href="index.php" class="menu-link">Trang chủ</a></li>

                <div class="dropdown">
                    <span>Sản phẩm</span>
                    <div class="dropdown-content">
                        <li class="menu-list-item"><a href="NINJA.php" class="menu-link">Dòng Ninja</a></li>
                        <li class="menu-list-item"><a href="Z.php" class="menu-link">Dòng Z</a></li>
                        <li class="menu-list-item"><a href="KLX.php" class="menu-link">Dòng KLX</a></li>
                    </div>
                </div>
                <li class="menu-list-item"><a href="index.php" class="menu-link">Tin tức </a></li>
                <li class="menu-list-item"><a href="index.php" class="menu-link">Điều khoản</a></li>

            </ul>
        </div>
    </nav>



    <main class="main-wrapper">
        <div class="slideshow-container">
            <div class="mySlides fade">

                <img src="image/web1.jpg">

            </div>
            <div class="mySlides fade">

                <img src="image/web2.jpg">

            </div>
            <div class="mySlides fade">

                <img src="image/web3.png">

            </div>
            <div class="mySlides fade">

                <img src="image/web4.jpg">

            </div>
            <div class="dot-wrapper">

                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>

            </div>
            <a class="prev" onclick="prevSlide()">&#10094;</a>
            <a class="next" onclick="showSlides()">&#10095;</a>
        </div>
        <div class="container" id="trangchu">


            <div class="home-service" id="home-service">
                <div class="home-service-item">
                    <div class="home-service-item-icon">
                        <i class="fa-light fa-person-carry-box"></i>
                    </div>
                    <div class="home-service-item-content">
                        <h4 class="home-service-item-content-h">GIAO HÀNG NHANH</h4>
                        <p class="home-service-item-content-desc">Cho tất cả đơn hàng</p>
                    </div>
                </div>
                <div class="home-service-item">
                    <div class="home-service-item-icon">
                        <i class="fa-light fa-shield-heart"></i>
                    </div>
                    <div class="home-service-item-content">
                        <h4 class="home-service-item-content-h">SẢN PHẨM AN TOÀN</h4>
                        <p class="home-service-item-content-desc">Cam kết chất lượng</p>
                    </div>
                </div>
                <div class="home-service-item">
                    <div class="home-service-item-icon">
                        <i class="fa-light fa-headset"></i>
                    </div>
                    <div class="home-service-item-content">
                        <h4 class="home-service-item-content-h">HỖ TRỢ 24/7</h4>
                        <p class="home-service-item-content-desc">Tất cả ngày trong tuần</p>
                    </div>
                </div>
                <div class="home-service-item">
                    <div class="home-service-item-icon">
                        <i class="fa-light fa-circle-dollar"></i>
                    </div>
                    <div class="home-service-item-content">
                        <h4 class="home-service-item-content-h">HOÀN LẠI TIỀN</h4>
                        <p class="home-service-item-content-desc">Nếu không hài lòng</p>
                    </div>
                </div>
            </div>
            <div class="home-title-block" id="home-title">
                <h2 class="home-title">MẪU XE ĐẶC TRƯNG</h2>
                <div class="border-line"></div>
            </div>


            <div class="page-nav">
                <ul class="page-nav-list">
                    <div class="grid-container" id="product-list">

                        <?php
                        $sql = "SELECT id, tensp, giaban, hinhanh FROM products";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '
                                <form method="POST" action="">
                                    <div class="card page-1" id="invoiceModal">
                                        <a href="thongtinsp.php?id=' . $row["id"] . '">
                                            <img src="sanpham/' . $row["hinhanh"] . '" alt="' . $row["tensp"] . '">
                                        </a>
                                        <h3>' . $row["tensp"] . '</h3>
                                        <div class="greenSpacer"></div>
                                        <div class="price">' . number_format($row["giaban"], 0, ',', '.') . 'đ</div>

                                        <input type="hidden" name="product_id" value="' . $row["id"] . '">
                                        <input type="hidden" name="product_name" value="' . htmlspecialchars($row["tensp"]) . '">
                                        <input type="hidden" name="product_price" value="' . $row["giaban"] . '">
                                        <input type="hidden" name="product_img" value="' . $row["hinhanh"] . '">
                                        <div class=display style="display:flex;">
                                        <button type="submit" class="mua" name="add_to_cart">Thêm vào giỏ hàng</button>
                                        <a href="thongtinsp.php?id=' . $row['id'] . '" class="mua" style="text-decoration: none; text-align: center; display: inline-block;color:white;">Xem chi tiết</a>
                                        </div>
                                    
                                    </div>
                                </form>';
                            }
                        }
                        ?>

                    </div>
                    
                    <div class="pagination">
                        <button id="prevBtn" onclick="changePage(-1)" disabled>&#10094;</button>
                        <div id="pageNumbers" class="page-numbers"></div>
                        <button id="nextBtn" onclick="changePage(1)">&#10095;</button>
                    </div>
                </ul>
            </div>
        </div>

    </main>

    <section class="cart">
        <button class="dong"><i class="fa-regular fa-xmark"></i></button>
        <!-- <div>Đóng</div> -->
        <div style="margin-top: 45px;margin-bottom: 20px;">Danh sách mua hàng</div>
        <form action="" method="POST">
            <?php
            if (isset($_POST['add_to_cart'])) {
                if (!$customer_id) {
                    die("Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng!");
                }

                $product_id = $_POST['product_id'];
                $product_price = $_POST['product_price'];
                $product_img = $_POST['product_img'];
                $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

                // Kiểm tra sản phẩm đã tồn tại chưa
                $stmt = $conn->prepare("SELECT soluong FROM giohang WHERE product_id = ? AND customer_id = ?");
                $stmt->bind_param("ii", $product_id, $customer_id);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    // Sản phẩm đã tồn tại, cập nhật số lượng
                    $stmt->bind_result($current_quantity);
                    $stmt->fetch();
                    $new_quantity = $current_quantity + $quantity;
                    $stmt->close();

                    $update_stmt = $conn->prepare("UPDATE giohang SET soluong = ? WHERE product_id = ? AND customer_id = ?");
                    $update_stmt->bind_param("iii", $new_quantity, $product_id, $customer_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                } else {
                    // Sản phẩm chưa có, thêm mới
                    $stmt->close();
                    $stmt = $conn->prepare("INSERT INTO giohang (customer_id, product_id, soluong, price, img) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("iiiss", $customer_id, $product_id, $quantity, $product_price, $product_img);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            ?>

            <table>
                <thead>
                    <tr>
                        <th>
                            Sản phẩm
                        </th>
                        <th>Tên</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Chọn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Kiểm tra xem người dùng đã đăng nhập chưa
                    if (!isset($_SESSION['customer_id'])) {
                        echo "<tr><td colspan='4'>Bạn cần đăng nhập để xem giỏ hàng!</td></tr>";
                    } else {
                        $customer_id = $_SESSION['customer_id'];

                        // Truy vấn lấy sản phẩm từ bảng giỏ hàng
                        $sql = "SELECT g.id, g.product_id, g.soluong, g.price, g.img, p.tensp 
                                FROM giohang g
                                JOIN products p ON g.product_id = p.id
                                WHERE g.customer_id = ?";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $customer_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $total_price = 0;

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $subtotal = $row["soluong"] * $row["price"];
                                $total_price += $subtotal;
                                echo '
                                <tr>
                                    <td style="display: flex; align-items: center;">
                                        <img style="width: 90px;" src="sanpham/' . $row["img"] . '" alt="' . htmlspecialchars($row["tensp"]) . '">
                                    </td>
                                    <td><span>' . htmlspecialchars($row["tensp"]) . '</span></td>
                                    <td>
                                        <p><span>' . number_format($row["price"], 0, ',', '.') . '</span><sup>đ</sup></p>
                                    </td>
                                    <td>
                                        <input style="width: 40px; outline: none;" type="number" value="' . $row["soluong"] . '" min="1" class="cart-quantity" data-cart-id="' . $row["id"] . '">
                                    </td>
                                    <td style="cursor: pointer;" class="delete-item" data-cart-id="' . $row["id"] . '">Xóa</td>
                                </tr>';
                            }
                        } else {
                            echo "<tr><td colspan='4'>Giỏ hàng của bạn đang trống!</td></tr>";
                        }
                    }
                    ?>
                </tbody>

            </table>
            <div style="text-align: center;" class="price-total">
                <p style="font-weight: bold; margin-top: 10px; margin-bottom: 20px;">
                    Tổng tiền: <span><?php echo number_format($total_price, 0, ',', '.'); ?></span>đ
                </p>
            </div>
            <a class="thanhtoan" href="thanhtoan.php">Thanh toán</a>
        </form>
    </section>

    <div class="green-line-header"></div>
    <?php include 'footer.php' ?>
    <!-- <script src="js/hoadon.js"></script> -->
    <script src="js/giohang.js"></script>
    <script src="js/phantrang.js"></script>
    <script src="js/ssbutton.js"></script>




</body>
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<script>if(window.history.replaceState){window.history.replaceState(null, null, window.location.href);}</script>";
}
?>
<?php $conn->close(); ?>

</html>