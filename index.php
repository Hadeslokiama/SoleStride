<?php
// index.php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

global $conn;

// Safely handle optional category filter via query string
$category = isset($_GET['category']) ? sanitize_input($_GET['category']) : '';

if ($category !== '') {
    // Parameterized filtering for active items under specific category
    $query = "SELECT id, name, description, price, image_path FROM products WHERE is_active = 1 AND category = ? ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Fallback default query showcasing all globally active inventory items
    $query = "SELECT id, name, description, price, image_path FROM products WHERE is_active = 1 ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
?>

<section class="storefront">
    <div class="storefront-hero">
        <div class="hero-title-block">
            <p class="kicker">New line / open form</p>
            <h1>
                Wear without permission.<a href="#collection" class="btn btn-primary hero-cta">Shop Now</a>
            </h1>
        </div>
    </div>

    <div class="kinetic-marquee" aria-label="Unbound store cues">
        <div class="kinetic-track">
            <span>Sharp essentials</span>
            <span>Direct movement</span>
            <span>No permission</span>
            <span>Clean silhouettes</span>
            <span aria-hidden="true">Sharp essentials</span>
            <span aria-hidden="true">Direct movement</span>
            <span aria-hidden="true">No permission</span>
            <span aria-hidden="true">Clean silhouettes</span>
        </div>
    </div>

    <section class="signal-grid" aria-label="Unbound principles">
        <article class="signal-card">
            <span class="signal-number" aria-hidden="true">01</span>
            <h2>Essential Fits</h2>
            <p>Browse sharp tops, bottoms, outerwear, and accessories made for everyday wear.</p>
        </article>
        <article class="signal-card">
            <span class="signal-number" aria-hidden="true">02</span>
            <h2>Cart Ready</h2>
            <p>Add pieces fast, review quantities, and keep your selected apparel organized.</p>
        </article>
        <article class="signal-card">
            <span class="signal-number" aria-hidden="true">03</span>
            <h2>Checkout Clean</h2>
            <p>Complete orders with clear totals, delivery details, and secure account access.</p>
        </article>
    </section>

    <div class="kinetic-marquee kinetic-marquee-muted" aria-label="Catalog categories">
        <div class="kinetic-track kinetic-track-reverse">
            <span>Tops</span>
            <span>Bottoms</span>
            <span>Outerwear</span>
            <span>Accessories</span>
            <span aria-hidden="true">Tops</span>
            <span aria-hidden="true">Bottoms</span>
            <span aria-hidden="true">Outerwear</span>
            <span aria-hidden="true">Accessories</span>
        </div>
    </div>

    <section id="collection" class="products-grid-container">
        <div class="section-heading">
            <h2>Our Collection<?php echo $category !== '' ? ' - ' . htmlspecialchars($category) : ''; ?></h2>
            <span>Built for daily motion</span>
        </div>
        <div class="products-grid">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($product = mysqli_fetch_assoc($result)): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars(app_url($product['image_path'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>

                            <div class="product-actions">
                                <a href="<?php echo app_url('product-details.php?id=' . (int)$product['id']); ?>" class="btn btn-secondary">View Details</a>

                                <form action="<?php echo app_url('cart.php'); ?>" method="POST">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-state">No apparel options are available at this moment.</p>
            <?php endif; ?>
        </div>
    </section>
</section>

<?php
if ($stmt) {
    mysqli_stmt_close($stmt);
}
require_once 'includes/footer.php';
?>
