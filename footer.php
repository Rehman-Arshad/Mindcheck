<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>MindCheck</h3>
            <p>Providing comprehensive mental health services and support for children and families.</p>
        </div>
        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="<?php echo $base_url; ?>about.php">About Us</a></li>
                <li><a href="<?php echo $base_url; ?>services.php">Services</a></li>
                <li><a href="<?php echo $base_url; ?>contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Contact Info</h3>
            <p><i class='bx bx-envelope'></i> info@mindcheck.com</p>
            <p><i class='bx bx-phone'></i> +1 234 567 8900</p>
            <p><i class='bx bx-map'></i> 123 Health Street, Medical District</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> MindCheck. All rights reserved.</p>
    </div>
</footer>

<style>
.footer {
    background: #f8f9fa;
    padding: 40px 0 20px;
    margin-top: 50px;
    border-top: 1px solid #eee;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    padding: 0 20px;
}

.footer-section {
    margin-bottom: 20px;
}

.footer-section h3 {
    color: #1977cc;
    margin-bottom: 15px;
    font-size: 1.2em;
}

.footer-section p {
    color: #2c4964;
    line-height: 1.6;
    margin-bottom: 10px;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 8px;
}

.footer-section ul li a {
    color: #2c4964;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-section ul li a:hover {
    color: #1977cc;
}

.footer-section i {
    margin-right: 10px;
    color: #1977cc;
}

.footer-bottom {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    color: #2c4964;
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .footer-section {
        margin-bottom: 30px;
    }
}
</style>
